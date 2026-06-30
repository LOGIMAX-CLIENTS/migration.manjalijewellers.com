#!/usr/bin/env python3
"""SDLC Pipeline v3.0 — CLI Engine (flat 8-step pipeline, validated step-done)"""

VERSION = '4.3.0'  # agent-only time tracking (what-next stamps start, step-done computes agent vs wall time)
import argparse
import json
import os
import re
import shutil
import subprocess
import sys
import textwrap
from datetime import datetime

# Force UTF-8 on Windows to support emoji
if sys.stdout.encoding != 'utf-8':
    sys.stdout = open(sys.stdout.fileno(), mode='w', encoding='utf-8', buffering=1)
    sys.stderr = open(sys.stderr.fileno(), mode='w', encoding='utf-8', buffering=1)


SDLC_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
PIPELINE_FILE = os.path.join(SDLC_DIR, 'pipeline.json')
ACTIVE_DIR = os.path.join(SDLC_DIR, 'active')
DONE_DIR = os.path.join(SDLC_DIR, 'done')
TEMPLATES_DIR = os.path.join(SDLC_DIR, 'templates')
ROLES_DIR = os.path.join(SDLC_DIR, 'roles')
STEPS_FILE = os.path.join(SDLC_DIR, 'steps.json')
PROJECT_ROOT = os.path.dirname(SDLC_DIR)
MEMORY_DIR = os.path.join(SDLC_DIR, 'memory')
MEMORY_FILE = os.path.join(MEMORY_DIR, 'rejected_hypotheses.json')

# Task pipeline phases (DISCUSS is a pre-task mode, not a pipeline phase)
# Flow: REQUIREMENT → TEST_DESIGN (discovery) → PLANNING → CODING → TEST_VERIFY → REVIEW → COMMIT
PHASES = ['IDLE', 'REQUIREMENT', 'TEST_DESIGN', 'PLANNING',
          'CODING', 'TEST_VERIFY', 'REVIEW', 'COMMIT', 'DONE']

PHASE_ICONS = {
    'IDLE': '\u23f8\ufe0f', 'REQUIREMENT': '\U0001f4cb',
    'PLANNING': '\U0001f3d7\ufe0f', 'TEST_DESIGN': '\U0001f9ea',
    'CODING': '\U0001f4bb', 'TEST_VERIFY': '\u2705',
    'REVIEW': '\U0001f50d', 'COMMIT': '\U0001f4e6', 'DONE': '\u2714\ufe0f'
}

# Human checkpoints — auto-flow pauses here for user approval
HUMAN_CHECKPOINTS = {
    'TEST_DESIGN': 'Review requirement via workflows/requirement-phase.md Step 8 before discovery testing',
    'CODING': 'Review design via workflows/planning-phase.md Step 7 before coding begins',
}

# ── Perf Task Type ──
# Perf tasks reuse standard phases but with different semantics and gates
PERF_PHASE_NAMES = {
    'REQUIREMENT': 'PROFILE',
    'PLANNING': 'HYPOTHESIS',
    'CODING': 'OPTIMIZE',
    'REVIEW': 'VERIFY',
    'COMMIT': 'COMMIT',
}

PERF_PHASE_ICONS = {
    'REQUIREMENT': '\U0001f52c',  # 🔬 PROFILE
    'PLANNING': '\U0001f4ca',     # 📊 HYPOTHESIS
    'CODING': '\u26a1',           # ⚡ OPTIMIZE
    'REVIEW': '\U0001f50d',       # 🔍 VERIFY
    'COMMIT': '\U0001f4e6',       # 📦 COMMIT
}

# Perf tasks use different spec files and gates
PERF_GATES = {
    'PLANNING': ('baseline', 'baseline.md must exist with acceptance criteria'),
    'CODING': ('hypothesis', 'hypothesis.md must exist with optimization plan'),
    'REVIEW': ('tasks', 'All tasks.md checkboxes must be checked'),
    'COMMIT': ('verify', 'verify.md must have Verdict: PASS'),
}

PERF_SPECS = ['baseline', 'hypothesis', 'tasks', 'verify']


# ── State Management ──

def load_state():
    if os.path.exists(PIPELINE_FILE):
        with open(PIPELINE_FILE, 'r', encoding='utf-8') as f:
            return json.load(f)
    return {'active_task': None, 'backlog': [], 'history': []}


def save_state(state):
    with open(PIPELINE_FILE, 'w', encoding='utf-8') as f:
        json.dump(state, f, indent=2, ensure_ascii=False)


# ── Spec File Management ──

def task_dir(task_id):
    return os.path.join(ACTIVE_DIR, task_id)


def get_intake_questions(task):
    """v4.3.1: Load structured intake questions for the task type.

    Returns (questions_to_ask, questions_answered) where each is a list of dicts.
    Questions already answered by --flags are auto-skipped.
    Auto-answerable questions are flagged so the agent can look them up instead of asking.
    """
    iq_path = os.path.join(os.path.dirname(__file__), '..', 'intake_questions.json')
    if not os.path.exists(iq_path):
        return [], []

    with open(iq_path, 'r', encoding='utf-8') as f:
        iq_data = json.load(f)

    task_type = task.get('type', 'fix')
    # Map task types to question sets (hotfix uses fix questions + hotfix-specific)
    type_map = {
        'fix': 'fix', 'hotfix': 'hotfix', 'feature': 'feature',
        'cr': 'cr', 'refactor': 'refactor', 'perf': 'fix'
    }
    q_key = type_map.get(task_type, 'fix')
    all_questions = iq_data.get('questions', {}).get(q_key, [])

    # Field-to-flag mapping for auto-skip
    flag_fields = {
        'url': task.get('url', ''),
        'expected': task.get('expected', ''),
        'actual': task.get('actual', ''),
        'steps_to_reproduce': task.get('steps_to_reproduce', ''),
    }

    questions_to_ask = []
    questions_answered = []

    for q in all_questions:
        maps_to = q.get('maps_to', '')
        skip_flag = q.get('skip_if_flag')

        # Skip if the mapped field was already provided via --flag
        if maps_to in flag_fields and flag_fields[maps_to] and len(str(flag_fields[maps_to]).strip()) > 5:
            questions_answered.append(q)
            continue

        # Skip auto-answerable questions (agent will find these during discover)
        if q.get('auto_answerable', False):
            questions_answered.append(q)
            continue

        questions_to_ask.append(q)

    return questions_to_ask, questions_answered


def format_intake_checklist(questions_to_ask, questions_answered):
    """Format intake questions into a structured checklist for the agent.

    Returns a list of checklist strings for the what-next step display.
    """
    checklist = []

    # Must-ask questions
    must_qs = [q for q in questions_to_ask if q.get('priority') == 'must']
    should_qs = [q for q in questions_to_ask if q.get('priority') == 'should']
    nice_qs = [q for q in questions_to_ask if q.get('priority') == 'nice']

    if must_qs:
        checklist.append('Ask the user these REQUIRED questions (present as a numbered list):')
        for i, q in enumerate(must_qs, 1):
            hint = f' — {q["hint"]}' if q.get('hint') else ''
            checklist.append(f'   {q["id"]}. {q["question"]}{hint}')

    if should_qs:
        checklist.append('If time allows, also ask:')
        for q in should_qs:
            hint = f' — {q["hint"]}' if q.get('hint') else ''
            checklist.append(f'   {q["id"]}. {q["question"]}{hint}')

    if questions_answered:
        checklist.append(f'Already answered ({len(questions_answered)} questions auto-skipped from --flags)')

    if not must_qs and not should_qs:
        checklist.append('All required context is provided. Confirm with user and proceed.')

    checklist.append('Update requirement.md with all answers — fill every section')
    checklist.append('Write concrete acceptance criteria (no placeholders)')
    checklist.append('Run: `python .sdlc/engine/cli.py step-done`')

    return checklist


def _extract_module_context(module_name):
    """Extract key domain context from the module brain for intake hints.
    Returns a markdown string with tables, entry points, risks, and suggested questions.
    Returns empty string if no brain found.
    """
    brain_dir = os.path.join(PROJECT_ROOT, 'knowledge_brain')
    if not os.path.isdir(brain_dir):
        return ''

    # Try exact match first, then case-insensitive
    brain_path = None
    for d in os.listdir(brain_dir):
        if d.lower() == module_name.lower() or module_name.lower() in d.lower():
            candidate = os.path.join(brain_dir, d, 'MODULE_BRAIN.md')
            if os.path.exists(candidate):
                brain_path = candidate
                break

    if not brain_path:
        return ''

    try:
        with open(brain_path, 'r', encoding='utf-8') as f:
            brain_content = f.read()
    except IOError:
        return ''

    lines = []
    lines.append('## Module Context (auto-extracted from Knowledge Brain)')
    lines.append('')
    lines.append(f'> Module brain: `{os.path.relpath(brain_path, PROJECT_ROOT)}`')
    lines.append('')

    # Extract Key Tables section
    tables_section = _extract_brain_section(brain_content, '## 5. Key Tables', '## 6')
    if tables_section:
        # Get first 15 lines of tables
        table_lines = [l for l in tables_section.split('\n') if l.strip()][:15]
        lines.append('### Key Tables')
        lines.extend(table_lines)
        lines.append('')

    # Extract Known Risks section
    risks_section = _extract_brain_section(brain_content, '## 6. Known Risks', '## 7')
    if not risks_section:
        risks_section = _extract_brain_section(brain_content, '## 6. Known Risks', '## 8')
    if risks_section:
        risk_lines = [l for l in risks_section.split('\n') if l.strip()][:10]
        lines.append('### Known Risks')
        lines.extend(risk_lines)
        lines.append('')

    # Extract Entry Points (helps identify which URL maps to what)
    entry_section = _extract_brain_section(brain_content, '## 3. Entry Points', '## 4')
    if entry_section:
        entry_lines = [l for l in entry_section.split('\n') if l.strip()][:10]
        lines.append('### Entry Points')
        lines.extend(entry_lines)
        lines.append('')

    # Also check BUSINESS_RULES.md for domain questions
    rules_path = os.path.join(os.path.dirname(brain_path), 'BUSINESS_RULES.md')
    if os.path.exists(rules_path):
        try:
            with open(rules_path, 'r', encoding='utf-8') as f:
                rules_content = f.read()
            # Extract first 10 rule lines
            rule_lines = [l for l in rules_content.split('\n')
                          if l.strip() and (l.strip().startswith('-') or l.strip().startswith('*'))][:8]
            if rule_lines:
                lines.append('### Business Rules (ask about these during intake)')
                lines.extend(rule_lines)
                lines.append('')
        except IOError:
            pass

    # Generate module-specific intake questions based on what we found
    lines.append('### Suggested Intake Questions (module-specific)')
    lines.append('')
    if tables_section:
        # Extract table names
        import re
        table_names = re.findall(r'`(\w+)`', tables_section)[:5]
        if table_names:
            lines.append(f'- Which of these tables are involved: {", ".join(table_names)}?')
    if entry_section:
        lines.append('- Which specific report/screen from the entry points above?')
    lines.append('- Is this a data issue (wrong values) or a display issue (data exists but not shown)?')
    lines.append('- Does this work for some records but not others? (specific branch, date range, status)')
    lines.append('')

    return '\n'.join(lines) if len(lines) > 5 else ''


def _extract_brain_section(content, start_header, end_header):
    """Extract content between two markdown headers."""
    start_idx = content.find(start_header)
    if start_idx < 0:
        return ''
    start_idx += len(start_header)
    end_idx = content.find(end_header, start_idx)
    if end_idx < 0:
        end_idx = min(start_idx + 2000, len(content))  # Cap at 2000 chars
    return content[start_idx:end_idx].strip()


def create_spec(task_id, spec_type, task_info=None):
    """Create a spec file from template.
    For perf tasks, looks in templates/perf/ first.
    """
    info = task_info or {}
    task_type = info.get('type', '')

    # Perf tasks use templates/perf/ subdirectory
    if task_type == 'perf':
        template_path = os.path.join(TEMPLATES_DIR, 'perf', f'{spec_type}.md')
        if not os.path.exists(template_path):
            template_path = os.path.join(TEMPLATES_DIR, f'{spec_type}.md')
    else:
        template_path = os.path.join(TEMPLATES_DIR, f'{spec_type}.md')

    dest_dir = task_dir(task_id)
    dest_path = os.path.join(dest_dir, f'{spec_type}.md')

    os.makedirs(dest_dir, exist_ok=True)

    if os.path.exists(dest_path):
        return dest_path  # Don't overwrite

    if os.path.exists(template_path):
        with open(template_path, 'r', encoding='utf-8') as f:
            content = f.read()

        # Fill placeholders
        replacements = {
            '{task_id}': info.get('id', task_id),
            '{summary}': info.get('summary', ''),
            '{task_type}': info.get('type', '?'),
            '{module}': info.get('module', '?'),
            '{priority}': info.get('priority', 'Not set'),
            '{ticket}': info.get('ticket', task_id),
            '{created_at}': info.get('started_at', datetime.now().isoformat()),
            '{description}': info.get('description', '<!-- Describe the task -->'),
            '{method}': info.get('method', 'methodName'),
        }
        for key, val in replacements.items():
            content = content.replace(key, str(val))

        # v4.2: Inject module context from Knowledge Brain
        if spec_type == 'requirement':
            module_name = info.get('module', '')
            if module_name:
                module_ctx = _extract_module_context(module_name)
                if module_ctx:
                    content += '\n' + module_ctx

        with open(dest_path, 'w', encoding='utf-8') as f:
            f.write(content)

    return dest_path



def check_spec(task_id, spec_type):
    """Check if a spec file exists and has real content (not just template).

    Returns: (exists: bool, has_content: bool)
    spec_type can be: requirement, design, tasks, tasks_exist, review,
                      baseline, hypothesis, verify (perf types)
    'tasks_exist' reads tasks.md but only checks items exist (not all checked).
    """
    # Map spec_type to actual filename
    filename = 'tasks' if spec_type == 'tasks_exist' else spec_type
    path = os.path.join(task_dir(task_id), f'{filename}.md')
    if not os.path.exists(path):
        return False, False

    with open(path, 'r', encoding='utf-8') as f:
        content = f.read()

    if spec_type in ('requirement', 'baseline'):
        # Must have ≥1 checkbox with real text after it
        checkboxes = [l.strip() for l in content.split('\n')
                      if re.match(r'^- \[[ x]\] .+', l.strip())]
        return True, len(checkboxes) >= 1

    if spec_type in ('review', 'verify'):
        # Verdict line must say PASS (not PENDING)
        for line in content.split('\n'):
            if 'Verdict:' in line:
                verdict = line.split('Verdict:')[1].strip()
                return True, verdict in ('PASS', 'PASS_WITH_ADVISORIES')
        return True, False

    if spec_type == 'tasks':
        # Check if ALL checkboxes are checked
        unchecked = [l for l in content.split('\n')
                     if re.match(r'^- \[ \] .+', l.strip())]
        checked = [l for l in content.split('\n')
                   if re.match(r'^- \[x\] .+', l.strip())]
        return True, len(unchecked) == 0 and len(checked) > 0

    if spec_type == 'tasks_exist':
        # Just check that tasks.md has real task items (not empty template)
        all_checkboxes = [l for l in content.split('\n')
                          if re.match(r'^- \[[ x]\] .+', l.strip())]
        return True, len(all_checkboxes) > 0

    if spec_type == 'hypothesis':
        # Must have at least one bottleneck section header
        headers = [l for l in content.split('\n') if l.strip().startswith('### ')]
        return True, len(headers) >= 1

    if spec_type == 'discovery_results':
        # Must have at least one test result line (✅ or ❌)
        results = [l for l in content.split('\n')
                   if '✅' in l or '❌' in l or 'PASS' in l or 'FAIL' in l]
        return True, len(results) >= 1

    # design.md — just needs to exist with non-template content
    return True, True


def spec_status(task_id, task_type=None):
    """Get status of all spec files for a task."""
    specs = PERF_SPECS if task_type == 'perf' else ['requirement', 'discovery_results', 'design', 'tasks', 'review']
    result = {}
    for s in specs:
        exists, has_content = check_spec(task_id, s)
        if exists and has_content:
            result[s] = 'ready'
        elif exists:
            result[s] = 'template'
        else:
            result[s] = 'missing'
    return result


# ── Transition Gates ──

GATES = {
    'TEST_DESIGN': ('requirement', 'requirement.md must exist with acceptance criteria and impact areas'),
    'PLANNING': ('discovery_results', 'discovery_results.md must exist with test outcomes'),
    'CODING': ('tasks_exist', 'tasks.md must exist with real task items'),
    'REVIEW': ('tasks', 'All tasks.md checkboxes must be checked'),
    'COMMIT': ('review', 'review.md must have verdict PASS'),
}


def can_transition(task_id, target_phase, task_type=None):
    """Check if transition is allowed. Returns (ok, reason)."""
    gates = PERF_GATES if task_type == 'perf' else GATES
    if target_phase not in gates:
        return True, 'No gate for this phase'

    spec_type, message = gates[target_phase]
    exists, has_content = check_spec(task_id, spec_type)

    if not exists:
        return False, f'{message} \u2014 file not found'
    if not has_content:
        return False, f'{message} \u2014 file exists but needs real content'

    return True, 'OK'


# ── CLI Commands ──

def cmd_show(state):
    task = state.get('active_task')
    if not task:
        backlog = state.get('backlog', [])
        if backlog:
            print(f'\n  No active task. Backlog ({len(backlog)}):')
            prio_icons = {'critical': '\U0001f534', 'high': '\U0001f7e0',
                          'medium': '\U0001f7e1', 'low': '\U0001f7e2'}
            for i, b in enumerate(backlog, 1):
                icon = prio_icons.get(b.get('priority'), '  ')
                print(f'    {i}. {icon} [{b["id"]}] {b["type"]}({b["module"]}): {b["summary"]}')
            print(f'\n  Pick one: sdlc pick <ID>')
        else:
            print('\n  No active task. No backlog.')
            print('  Start: sdlc start -t fix -m billing -s "description"')
            print('  Queue: sdlc queue -t fix -m billing -s "description"\n')
        return

    phase = task.get('phase', 'IDLE')
    is_perf = task.get('type') == 'perf'
    if is_perf:
        icon = PERF_PHASE_ICONS.get(phase, PHASE_ICONS.get(phase, ''))
        phase_display = PERF_PHASE_NAMES.get(phase, phase)
    else:
        icon = PHASE_ICONS.get(phase, '')
        phase_display = phase
    auto_badge = ' \U0001f680AUTO' if task.get('auto_flow') else ''
    print(f'\n  {icon} [{task["id"]}] {task["type"]}({task["module"]}): {task["summary"]}{auto_badge}')

    # Show current phase duration
    timeline = task.get('timeline', [])
    phase_dur = ''
    if timeline:
        last = timeline[-1]
        entered = _parse_iso(last.get('entered_at'))
        if entered:
            mins = (datetime.now() - entered).total_seconds() / 60
            phase_dur = f' | In phase: {_format_duration(mins)}'
    print(f'  Phase: {phase_display} | Priority: {task.get("priority", "?")} | Since: {task.get("started_at", "?")[:10]}{phase_dur}')
    if task.get('branch'):
        print(f'  Branch: {task["branch"]}')

    # Auto-flow checkpoint indicator
    if task.get('auto_flow') and phase in HUMAN_CHECKPOINTS:
        print(f'  \u23f8\ufe0f  CHECKPOINT: {HUMAN_CHECKPOINTS[phase]}')

    # Spec status
    status = spec_status(task['id'], task.get('type'))
    icons_map = {'ready': '\u2705', 'template': '\U0001f7e1', 'missing': '\u2b1c'}
    print(f'\n  Specs:')
    for name, st in status.items():
        print(f'    {icons_map[st]} {name}.md \u2014 {st}')

    # Role file
    if is_perf:
        role_file = os.path.join(ROLES_DIR, 'PERF.md')
        print(f'\n  Role: .sdlc/roles/PERF.md (section: {phase_display})')
    else:
        role_file = os.path.join(ROLES_DIR, f'{phase}.md')
        if os.path.exists(role_file):
            print(f'\n  Role: .sdlc/roles/{phase}.md')

    # Backlog
    backlog = state.get('backlog', [])
    if backlog:
        print(f'\n  Backlog ({len(backlog)} pending)')
    print()


# ── Git Branch Management ──

BRANCH_PREFIX_MAP = {
    'fix': 'bugfix',
    'feature': 'feature',
    'hotfix': 'hotfix',
    'cr': 'feature',
    'refactor': 'feature',
    'perf': 'bugfix',
}


def _load_config():
    """Load config.json for this repo."""
    config_path = os.path.join(SDLC_DIR, 'config.json')
    if os.path.exists(config_path):
        with open(config_path, 'r', encoding='utf-8') as f:
            return json.load(f)
    return {}


_php_path_cache = None

def _resolve_php_path(configured_path):
    """Resolve php_path: use config value if it works, otherwise auto-detect.

    config.json is shared via git and may have another dev's path.
    This ensures each machine resolves to a working PHP binary.
    """
    global _php_path_cache
    if _php_path_cache is not None:
        return _php_path_cache

    # 1. Try the configured path
    if configured_path and os.path.exists(configured_path):
        _php_path_cache = configured_path
        return configured_path

    # 2. Try 'php' in PATH
    if configured_path == 'php':
        _php_path_cache = 'php'
        return 'php'

    import subprocess as sp
    try:
        r = sp.run(['php', '--version'], capture_output=True, text=True, timeout=5)
        if r.returncode == 0:
            _php_path_cache = 'php'
            return 'php'
    except (FileNotFoundError, sp.TimeoutExpired):
        pass

    # 3. Scan common XAMPP locations
    for p in [r'C:\xampp\php\php.exe', r'D:\xampp\php\php.exe', r'E:\xampp\php\php.exe',
              '/usr/bin/php', '/usr/local/bin/php']:
        if os.path.exists(p):
            _php_path_cache = p.replace('\\', '/')
            return _php_path_cache

    # 4. Fallback — return 'php' and hope for the best
    _php_path_cache = 'php'
    return 'php'

def _slugify(text, max_len=40):
    """Convert text to git-safe branch slug."""
    slug = re.sub(r'[^a-zA-Z0-9]+', '-', text.lower()).strip('-')
    return slug[:max_len].rstrip('-')


def _create_task_branch(task_type, task_id, summary):
    """Create a new branch from {remote}/{base_branch} for this task.

    Branches are always created from base_branch (PRODUCTION) — the single source of truth.
    dev_branch is only used as the PR target (first promotion stop).
    Returns: (branch_name, success) tuple.
    Non-fatal — prints warning on failure but doesn't block task creation.
    """
    import subprocess

    config = _load_config()
    git_config = config.get('git', {})
    base_branch = git_config.get('base_branch', 'PRODUCTION')
    remote = git_config.get('remote', 'origin')
    prefix = BRANCH_PREFIX_MAP.get(task_type, 'feature')
    slug = _slugify(summary)
    branch_name = f'{prefix}/{task_id}-{slug}'

    project_root = os.path.dirname(SDLC_DIR)

    try:
        # Check for uncommitted changes
        result = subprocess.run(
            ['git', 'status', '--porcelain'],
            capture_output=True, text=True, cwd=project_root
        )
        dirty_files = [l for l in result.stdout.strip().split('\n') if l.strip()]
        if dirty_files:
            print(f'  \u26a0\ufe0f  Working tree has {len(dirty_files)} uncommitted change(s).')
            print(f'  Stash or commit them before starting a new task branch.')
            print(f'  Branch NOT created \u2014 you are on the current branch.')
            return branch_name, False

        # Fetch latest from remote
        subprocess.run(
            ['git', 'fetch', remote, base_branch],
            capture_output=True, text=True, cwd=project_root
        )

        # Create and checkout new branch from {remote}/{base_branch}
        result = subprocess.run(
            ['git', 'checkout', '-b', branch_name, f'{remote}/{base_branch}'],
            capture_output=True, text=True, cwd=project_root
        )
        if result.returncode != 0:
            # Branch might already exist
            if 'already exists' in result.stderr:
                subprocess.run(
                    ['git', 'checkout', branch_name],
                    capture_output=True, text=True, cwd=project_root
                )
                print(f'  \U0001f500 Branch: {branch_name} (already exists \u2014 checked out)')
                return branch_name, True
            else:
                print(f'  \u26a0\ufe0f  Branch creation failed: {result.stderr.strip()}')
                return branch_name, False

        print(f'  \U0001f500 Branch: {branch_name} (from {remote}/{base_branch})')
        return branch_name, True

    except FileNotFoundError:
        print(f'  \u26a0\ufe0f  git not found \u2014 branch NOT created')
        return branch_name, False
    except Exception as e:
        print(f'  \u26a0\ufe0f  Branch error: {e}')
        return branch_name, False


def cmd_start(state, args):
    task_id = args.ticket or f'{args.module[:3].upper()}-{datetime.now().strftime("%y%m%d%H%M")}'

    if state.get('active_task'):
        if getattr(args, 'force', False):
            # Move current task to backlog
            current = state['active_task']
            backlog_entry = {
                'id': current['id'],
                'type': current['type'],
                'module': current['module'],
                'summary': current['summary'],
                'priority': current.get('priority', 'medium'),
                'queued_at': datetime.now().isoformat(),
            }
            state.setdefault('backlog', []).append(backlog_entry)
            print(f'  Moved [{current["id"]}] to backlog.')
        else:
            print(f'  Active task exists: {state["active_task"]["id"]}. Complete it first or use --force.')
            return

    now = datetime.now().isoformat()
    auto = getattr(args, 'auto', False)

    # Create branch from origin/{dev_branch}
    branch_name, branch_ok = _create_task_branch(args.type, task_id, args.summary)

    is_perf = args.type == 'perf'
    task = {
        'id': task_id,
        'type': args.type,
        'module': args.module,
        'summary': args.summary,
        'priority': getattr(args, 'priority', None) or 'medium',
        'phase': 'REQUIREMENT',
        'started_at': now,
        'description': getattr(args, 'description', None) or '',
        'auto_flow': auto,
        'timeline': [{'phase': 'REQUIREMENT', 'entered_at': now}],
        'branch': branch_name,
        'track': getattr(args, 'track', None) or 'STANDARD',  # v3.0.2: default to STANDARD (was empty, caused what-next failure)
        'current_step': 1,  # v2.5: step-driven pipeline
        'progress': [],  # v2.5: step completion log
        # v3.0.6: Structured intake fields
        'url': getattr(args, 'url', None) or '',
        'expected': getattr(args, 'expected', None) or '',
        'actual': getattr(args, 'actual', None) or '',
        'steps_to_reproduce': getattr(args, 'steps', None) or '',
    }

    # v2.5: If track explicitly provided, validate it exists in steps.json
    track_arg = (getattr(args, 'track', None) or '').upper()
    if track_arg:
        steps = _get_track_steps(track_arg)
        if steps:
            task['track'] = track_arg
        else:
            print(f'  \u26a0\ufe0f  Track "{track_arg}" not found in steps.json. Falling back to STANDARD.')
            task['track'] = 'STANDARD'
    else:
        # v4.3.1: Auto-detect track from summary keywords
        detected, confidence, reason = _auto_detect_track(
            args.summary,
            getattr(args, 'actual', '') or '',
            args.type
        )
        task['track'] = detected
        # v4.4: Always show track selection details for user confirmation
        tracks_info = _load_steps_data().get('tracks', {})
        print()
        print(f'  \u2500\u2500 Track Selection \u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500\u2500')
        print(f'  Detected: {detected} (confidence: {confidence})')
        print(f'  Reason: {reason}')
        print()
        for t_name in ['MICRO', 'EXPRESS', 'STANDARD']:
            t_data = tracks_info.get(t_name, {})
            t_steps = t_data.get('steps', [])
            marker = ' \u2190 SELECTED' if t_name == detected else ''
            step_names = ' \u2192 '.join(s.get('name', '?') for s in t_steps)
            print(f'    {t_name:10s} ({len(t_steps)} steps): {step_names}{marker}')
        print()
        print(f'  \u26a0\ufe0f  AWAITING USER CONFIRMATION on track selection.')
        print(f'  To change: restart with --track micro|express|standard')

    state['active_task'] = task
    save_state(state)

    # Create spec folder with appropriate template
    if is_perf:
        create_spec(task_id, 'baseline', task)
        phase_label = 'PROFILE'
        spec_name = 'baseline.md'
    else:
        create_spec(task_id, 'requirement', task)
        phase_label = 'REQUIREMENT'
        spec_name = 'requirement.md'

    mode = ' \U0001f680AUTO' if auto else ''
    track_display = f' | Track: {task["track"]}' if task['track'] else ' | Track: auto (set by discover)'
    print(f'  Started: [{task_id}] {args.type}({args.module}): {args.summary}{mode}')
    print(f'  Phase: {phase_label}{track_display}')
    print(f'  Spec: .sdlc/active/{task_id}/{spec_name}')
    if is_perf:
        print(f'  Workflow: PROFILE \u2192 HYPOTHESIS \u2192 OPTIMIZE \u2192 VERIFY \u2192 COMMIT')
    if auto:
        print(f'  Auto-flow: AI will generate specs, pause at checkpoints for your approval')
    # v4.1: Nudge for URL if missing on fix/hotfix tasks (URL drives discovery)
    if args.type in ('fix', 'hotfix') and not task.get('url'):
        print()
        print(f'  \u26a0\ufe0f  No --url provided. Discovery works best with a page URL.')
        print(f'  Consider restarting with: --url "controller/method"')
        print(f'  Example: --url "admin_ret_reports/cash_book/list"')
    # v4.3.1: Auto-advance INTAKE when all structured context is provided
    intake_fields = [task.get('url'), task.get('expected'), task.get('actual'), task.get('steps_to_reproduce')]
    all_intake_filled = all(f and len(str(f).strip()) > 5 for f in intake_fields)
    if all_intake_filled and task.get('current_step', 1) == 1:
        print()
        print('  ⚡ INTAKE AUTO-ADVANCE: All context flags provided (url, expected, actual, steps).')
        print('  → Populating requirement.md and advancing to next step.')
        # Auto-populate requirement.md
        req_path = os.path.join(task_dir(task_id), 'requirement.md')
        if os.path.exists(req_path):
            with open(req_path, 'r', encoding='utf-8', errors='ignore') as f:
                req_content = f.read()
            # Fill placeholders if sections are empty
            replacements = {
                '## Description\r\n\r\n\r\n': f'## Description\r\n\r\n{task["summary"]}. URL: `{task["url"]}`. {task.get("description", "")}\r\n',
                '## Current Behavior\r\n\r\n<!-- What is happening now? Include evidence from code investigation. -->\r\n': f'## Current Behavior\r\n\r\n{task["actual"]}\r\n',
                '## Expected Behavior\r\n\r\n<!-- What should happen instead? -->\r\n': f'## Expected Behavior\r\n\r\n{task["expected"]}\r\n',
            }
            for old, new in replacements.items():
                if old in req_content:
                    req_content = req_content.replace(old, new)
            with open(req_path, 'w', encoding='utf-8') as f:
                f.write(req_content)
        # Advance step
        task['current_step'] = 2
        task['progress'].append({
            'step': 1, 'name': 'intake', 'completed_at': datetime.now().isoformat(),
            'duration_s': 0, 'note': 'auto-advanced (all context flags provided)'
        })
        task['_step_started_at'] = None
        save_state(state)
        print('  ✅ Step 1 (intake) auto-completed.')
        print('  Next: Step 2')
        state = load_state()
        cmd_what_next(state)
    else:
        # v3.0.2: Show first step (normal flow when context incomplete)
        print()
        state = load_state()
        cmd_what_next(state)

    # v4.2: Surface tuning hints from pipeline history
    _show_tuning_hint(args.module, args.type)


def cmd_queue(state, args):
    task_id = args.ticket or f'{args.module[:3].upper()}-{datetime.now().strftime("%y%m%d%H%M")}'
    entry = {
        'id': task_id,
        'type': args.type,
        'module': args.module,
        'summary': args.summary,
        'priority': getattr(args, 'priority', None) or 'medium',
        'queued_at': datetime.now().isoformat(),
    }
    state.setdefault('backlog', []).append(entry)
    # Sort: critical > high > medium > low
    prio_order = {'critical': 0, 'high': 1, 'medium': 2, 'low': 3}
    state['backlog'].sort(key=lambda x: prio_order.get(x.get('priority'), 9))
    save_state(state)
    print(f'  Queued: [{task_id}] {args.type}({args.module}): {args.summary}')
    print(f'  Activate: sdlc pick {task_id}')


def cmd_pick(state, args):
    backlog = state.get('backlog', [])
    match = next((b for b in backlog if b['id'] == args.task_id), None)
    if not match:
        print(f'  Task {args.task_id} not found in backlog.')
        return

    if state.get('active_task'):
        print(f'  Active task exists: {state["active_task"]["id"]}. Complete it first.')
        return

    backlog.remove(match)
    now = datetime.now().isoformat()

    # Create branch from origin/{dev_branch}
    branch_name, branch_ok = _create_task_branch(
        match.get('type', 'fix'), match['id'], match.get('summary', '')
    )

    task = {**match, 'phase': 'REQUIREMENT', 'started_at': now,
            'timeline': [{'phase': 'REQUIREMENT', 'entered_at': now}],
            'branch': branch_name}
    del task['queued_at']
    state['active_task'] = task
    save_state(state)

    is_perf = task.get('type') == 'perf'
    if is_perf:
        create_spec(task['id'], 'baseline', task)
        print(f'  Activated: [{task["id"]}] → PROFILE')
        print(f'  Spec: .sdlc/active/{task["id"]}/baseline.md')
        print(f'  Workflow: PROFILE → HYPOTHESIS → OPTIMIZE → VERIFY → COMMIT')
    else:
        create_spec(task['id'], 'requirement', task)
        print(f'  Activated: [{task["id"]}] → REQUIREMENT')
        print(f'  Spec: .sdlc/active/{task["id"]}/requirement.md')


def cmd_transition(state, args):
    task = state.get('active_task')
    if not task:
        print('  No active task.')
        return

    target = args.phase.upper()
    if target not in PHASES:
        print(f'  Invalid phase: {target}. Valid: {PHASES}')
        return

    ok, reason = can_transition(task['id'], target, task.get('type'))
    if not ok:
        print(f'  \u26a0\ufe0f BLOCKED: {reason}')
        return

    old = task['phase']
    now = datetime.now().isoformat()

    # Record timeline entry
    timeline = task.setdefault('timeline', [])
    # Close the current phase entry
    if timeline and 'exited_at' not in timeline[-1]:
        timeline[-1]['exited_at'] = now
    # Open new phase entry
    timeline.append({'phase': target, 'entered_at': now})

    task['phase'] = target
    save_state(state)

    # Show duration of completed phase
    duration = _phase_duration(timeline, old)
    dur_str = f' ({_format_duration(duration)})' if duration else ''
    is_perf = task.get('type') == 'perf'
    if is_perf:
        old_label = PERF_PHASE_NAMES.get(old, old)
        target_label = PERF_PHASE_NAMES.get(target, target)
        print(f'  {old_label}{dur_str} \u2192 {target_label}')
        print(f'  Role: .sdlc/roles/PERF.md (section: {target_label})')
    else:
        print(f'  {old}{dur_str} \u2192 {target}')
        print(f'  Role: .sdlc/roles/{target}.md')


def cmd_create_spec(state, args):
    task = state.get('active_task')
    if not task:
        print('  No active task.')
        return
    path = create_spec(task['id'], args.spec_type, task)
    print(f'  Created: {path}')


def _load_memory():
    """Load the anti-pattern store (rejected hypotheses)."""
    if not os.path.exists(MEMORY_FILE):
        return {'rejected': []}
    try:
        with open(MEMORY_FILE, 'r', encoding='utf-8') as f:
            return json.load(f)
    except (json.JSONDecodeError, IOError):
        return {'rejected': []}


def _save_memory(memory):
    """Save the anti-pattern store."""
    os.makedirs(MEMORY_DIR, exist_ok=True)
    with open(MEMORY_FILE, 'w', encoding='utf-8') as f:
        json.dump(memory, f, indent=2, ensure_ascii=False)


def _check_anti_patterns(summary, module):
    """Check if any rejected hypotheses match the current task.
    Returns list of matching anti-patterns with warnings."""
    memory = _load_memory()
    if not memory.get('rejected'):
        return []

    matches = []
    search_text = f'{summary} {module}'.lower()
    for entry in memory['rejected']:
        keywords = entry.get('keywords', [])
        hit_count = sum(1 for kw in keywords if kw.lower() in search_text)
        if hit_count >= 2 or entry.get('module', '').lower() == module.lower():
            matches.append(entry)
    return matches


def cmd_reject(state, args):
    """Capture a rejected hypothesis in the anti-pattern store.
    Usage: sdlc reject --reason "existing JS already handles opening balance"
    """
    task = state.get('active_task')
    if not task:
        print('  No active task.')
        return

    reason = args.reason if hasattr(args, 'reason') and args.reason else ''
    if not reason:
        print('  \u274c --reason is required. Explain WHY the plan was rejected.')
        return

    # Extract hypothesis from context.md or requirement_review.md
    tid = task['id']
    hypothesis = ''
    context_path = os.path.join(task_dir(tid), 'context.md')
    if os.path.exists(context_path):
        with open(context_path, 'r', encoding='utf-8') as f:
            content = f.read()
        for line in content.split('\n'):
            if line.startswith('The bug is caused by') or line.startswith('This feature will be implemented'):
                hypothesis = line.strip()
                break
            if '## Hypothesis' in line or '## Approach' in line:
                # Next non-empty line is the hypothesis
                idx = content.find(line)
                remaining = content[idx + len(line):].strip()
                hypothesis = remaining.split('\n')[0].strip()
                break

    if not hypothesis:
        # Fallback: use the summary as hypothesis
        hypothesis = task.get('summary', 'Unknown')

    # Extract keywords from hypothesis + reason
    import re
    words = re.findall(r'[a-zA-Z_]{4,}', f'{hypothesis} {reason} {task.get("summary", "")}')
    # Filter common words, keep domain-specific ones
    stopwords = {'this', 'that', 'with', 'from', 'have', 'been', 'does', 'will',
                 'should', 'would', 'could', 'because', 'already', 'there',
                 'which', 'where', 'when', 'what', 'line', 'file', 'code',
                 'function', 'method', 'return', 'value', 'data', 'true',
                 'false', 'null', 'none', 'some', 'also', 'each', 'other'}
    keywords = list(set(w.lower() for w in words if w.lower() not in stopwords))[:15]

    entry = {
        'task_id': tid,
        'module': task.get('module', 'unknown'),
        'type': task.get('type', 'fix'),
        'hypothesis': hypothesis,
        'rejection_reason': reason,
        'keywords': keywords,
        'date': datetime.now().strftime('%Y-%m-%d %H:%M')
    }

    memory = _load_memory()
    memory['rejected'].append(entry)
    _save_memory(memory)

    print(f'  \u26a0\ufe0f Anti-pattern recorded:')
    print(f'  Hypothesis: {hypothesis[:80]}')
    print(f'  Reason: {reason[:80]}')
    print(f'  Keywords: {", ".join(keywords[:8])}')
    print(f'  Total anti-patterns: {len(memory["rejected"])}')


def cmd_done(state, args=None):
    task = state.get('active_task')
    if not task:
        print('  No active task.')
        return

    force = getattr(args, 'force', False) if args else False
    src = task_dir(task['id'])

    # ── ENRICH HARD GATE — block if mandatory post-fix artifacts are missing ──
    # v4.3.1: Track-aware checks — MICRO requires nothing, EXPRESS skips review.md
    track = task.get('track', 'STANDARD').upper()
    if not force and track not in ('MICRO',):
        blockers = []

        # Check 1: Rollback plan exists in context.md (EXPRESS + STANDARD only)
        context_path = os.path.join(src, 'context.md')
        if os.path.exists(context_path):
            with open(context_path, 'r', encoding='utf-8', errors='ignore') as f:
                context_content = f.read()
            if '## Rollback' not in context_content and '## rollback' not in context_content.lower():
                blockers.append('Missing ## Rollback section in context.md (rollback plan is mandatory)')
        else:
            # context.md itself missing — only block if task went past discover
            if task.get('current_step', 1) > 3:
                blockers.append('Missing context.md (should exist by investigate step)')

        # Check 2: Review.md exists — STANDARD only (EXPRESS skips formal review)
        if track == 'STANDARD':
            review_path = os.path.join(src, 'review.md')
            if not os.path.exists(review_path):
                blockers.append('Missing review.md (code review is mandatory before commit)')

        # Check 3: Recipe check — if no recipe was found during discover, one should be created
        discovery_path = os.path.join(src, 'discovery.md')
        if os.path.exists(discovery_path):
            with open(discovery_path, 'r', encoding='utf-8', errors='ignore') as f:
                disc_content = f.read()
            # If discovery said "no recipe match" → user should have created one
            no_recipe = 'no local recipe' in disc_content.lower() or 'no recipe match' in disc_content.lower()
            recipes_dir = os.path.join(os.path.dirname(PROJECT_ROOT), 'bug-recipes', 'recipes')
            if no_recipe and os.path.exists(recipes_dir):
                # Check if recipe was created during this task
                task_id_lower = task['id'].lower().replace('-', '')
                recipe_found = False
                for root, dirs, files in os.walk(recipes_dir):
                    for fname in files:
                        if task_id_lower in fname.lower().replace('-', ''):
                            recipe_found = True
                            break
                    if recipe_found:
                        break
                if not recipe_found:
                    blockers.append(f'No recipe created for novel fix (run: sdlc enrich, or push recipe manually)')

        if blockers:
            print(f'  ❌ BLOCKED — {len(blockers)} mandatory artifact(s) missing:\n')
            for i, b in enumerate(blockers, 1):
                print(f'     {i}. {b}')
            print(f'\n  Fix the above, then run `sdlc done` again.')
            print(f'  Or force close: `python .sdlc/engine/cli.py done --force`')
            return

    dst = os.path.join(DONE_DIR, task['id'])

    if os.path.exists(src):
        os.makedirs(DONE_DIR, exist_ok=True)
        shutil.move(src, dst)

    now = datetime.now().isoformat()
    task['completed_at'] = now

    # Close last timeline entry
    timeline = task.get('timeline', [])
    if timeline and 'exited_at' not in timeline[-1]:
        timeline[-1]['exited_at'] = now

    # Compute cycle time
    cycle = _compute_cycle_time(task)
    task['cycle_time_minutes'] = cycle

    # Compute per-phase durations
    task['phase_durations'] = _compute_phase_durations(timeline)

    state.setdefault('history', []).append(task)
    state['active_task'] = None
    save_state(state)

    # v4.2: Capture pipeline metrics for self-tuning
    _capture_task_metrics(task, dst)

    print(f'  Completed: [{task["id"]}] Specs moved to done/{task["id"]}/')
    if cycle:
        print(f'  Cycle time: {_format_duration(cycle)}')
        durations = task.get('phase_durations', {})
        if durations:
            print(f'  Phase breakdown:')
            for phase, mins in durations.items():
                print(f'    {phase}: {_format_duration(mins)}')


def _capture_task_metrics(task, task_done_dir):
    """Extract quality signals from completed task artifacts and store for tuning.
    Called by cmd_done after task is moved to done/.
    """
    import re

    metrics = {
        'task_id': task.get('id', ''),
        'module': task.get('module', ''),
        'type': task.get('type', ''),
        'track': task.get('track', ''),
        'cycle_time_minutes': task.get('cycle_time_minutes', 0),
        'phase_durations': task.get('phase_durations', {}),
        'completed_at': task.get('completed_at', ''),
    }

    def _read(name):
        p = os.path.join(task_done_dir, name)
        if os.path.exists(p):
            with open(p, 'r', encoding='utf-8', errors='ignore') as f:
                return f.read()
        return ''

    # ── Discovery quality ──
    discovery = _read('discovery.md')
    if discovery:
        # Extract quality score
        import re
        score_match = re.search(r'Score:\s*(\d+)/(\d+)\s*\((\d+)%\)', discovery)
        if score_match:
            metrics['discovery_score'] = int(score_match.group(1))
            metrics['discovery_max'] = int(score_match.group(2))
            metrics['discovery_pct'] = int(score_match.group(3))

        # Which sources contributed?
        metrics['rag_hit'] = 'STRONG' in discovery or 'good' in discovery
        metrics['recipe_hit'] = 'recipe match' in discovery.lower() and 'no recipe' not in discovery.lower()
        metrics['call_tree_used'] = 'Call Tree' in discovery

    # ── Review verdict ──
    review = _read('review.md')
    if review:
        if 'Verdict: PASS' in review:
            metrics['review_verdict'] = 'PASS'
        elif 'Verdict: CONCERNS' in review:
            metrics['review_verdict'] = 'CONCERNS'
        elif 'Verdict: REJECT' in review:
            metrics['review_verdict'] = 'REJECT'
        else:
            metrics['review_verdict'] = 'UNKNOWN'

    # ── Verification result ──
    context = _read('context.md')
    if context and '## Verification' in context:
        if 'Overall Verdict: PASS' in context:
            metrics['verify_result'] = 'PASS'
        elif 'Overall Verdict: FAIL' in context:
            metrics['verify_result'] = 'FAIL'
        else:
            metrics['verify_result'] = 'UNKNOWN'

    # ── Files changed count ──
    recipe_json_path = os.path.join(task_done_dir, 'recipe')
    if os.path.exists(recipe_json_path):
        for f in os.listdir(recipe_json_path):
            if f.endswith('.json'):
                try:
                    with open(os.path.join(recipe_json_path, f), 'r') as jf:
                        rdata = json.load(jf)
                    metrics['files_changed'] = len(rdata.get('files_changed', []))
                    metrics['keywords'] = rdata.get('keywords', [])
                except Exception:
                    pass

    # ── Store metrics ──
    memory_dir = os.path.join(SDLC_DIR, 'memory')
    os.makedirs(memory_dir, exist_ok=True)
    metrics_path = os.path.join(memory_dir, 'pipeline_metrics.json')

    all_metrics = []
    if os.path.exists(metrics_path):
        try:
            with open(metrics_path, 'r', encoding='utf-8') as f:
                all_metrics = json.load(f)
        except Exception:
            all_metrics = []

    all_metrics.append(metrics)

    with open(metrics_path, 'w', encoding='utf-8') as f:
        json.dump(all_metrics, f, indent=2, ensure_ascii=False)

    # ── Generate tuning insights ──
    if len(all_metrics) >= 3:
        insights = _generate_tuning_insights(all_metrics)
        if insights:
            insights_path = os.path.join(memory_dir, 'tuning_insights.md')
            with open(insights_path, 'w', encoding='utf-8') as f:
                f.write('# Pipeline Tuning Insights\n\n')
                f.write(f'> Auto-generated from {len(all_metrics)} completed tasks.\n\n')
                for insight in insights:
                    f.write(f'- {insight}\n')
            print(f'  \U0001f9e0 Tuning: {len(insights)} insight(s) updated in .sdlc/memory/tuning_insights.md')


def _generate_tuning_insights(all_metrics):
    """Analyze accumulated metrics and generate actionable tuning insights."""
    insights = []
    n = len(all_metrics)

    # Average cycle time
    cycle_times = [m['cycle_time_minutes'] for m in all_metrics if m.get('cycle_time_minutes')]
    if cycle_times:
        avg_cycle = sum(cycle_times) / len(cycle_times)
        insights.append(f'Average cycle time: {avg_cycle:.0f} min across {len(cycle_times)} tasks')

    # Phase bottlenecks
    phase_totals = {}
    phase_counts = {}
    for m in all_metrics:
        for phase, mins in m.get('phase_durations', {}).items():
            phase_totals[phase] = phase_totals.get(phase, 0) + mins
            phase_counts[phase] = phase_counts.get(phase, 0) + 1
    if phase_totals:
        phase_avgs = {p: phase_totals[p] / phase_counts[p] for p in phase_totals}
        slowest = max(phase_avgs, key=phase_avgs.get)
        insights.append(f'Slowest phase: **{slowest}** (avg {phase_avgs[slowest]:.0f} min)')

    # Discovery source effectiveness
    rag_hits = sum(1 for m in all_metrics if m.get('rag_hit'))
    recipe_hits = sum(1 for m in all_metrics if m.get('recipe_hit'))
    tree_used = sum(1 for m in all_metrics if m.get('call_tree_used'))
    if n >= 3:
        insights.append(f'Source hit rates: RAG={rag_hits}/{n}, Recipe={recipe_hits}/{n}, CallTree={tree_used}/{n}')
        if rag_hits == 0:
            insights.append('\u26a0\ufe0f RAG has 0 hits — check ChromaDB index freshness')
        if recipe_hits > n * 0.5:
            insights.append('\u2705 Recipe hit rate >50% — recipe library is paying off')

    # Review verdict distribution
    verdicts = [m.get('review_verdict') for m in all_metrics if m.get('review_verdict')]
    if verdicts:
        pass_rate = sum(1 for v in verdicts if v == 'PASS') / len(verdicts)
        reject_count = sum(1 for v in verdicts if v == 'REJECT')
        insights.append(f'Review pass rate: {pass_rate*100:.0f}% ({reject_count} rejects)')
        if reject_count > n * 0.3:
            insights.append('\u26a0\ufe0f High reject rate — investigator may need better prompting')

    # Track accuracy
    tracks = [m.get('track') for m in all_metrics if m.get('track')]
    if tracks:
        express_count = sum(1 for t in tracks if t == 'EXPRESS')
        standard_count = sum(1 for t in tracks if t == 'STANDARD')
        insights.append(f'Track split: EXPRESS={express_count}, STANDARD={standard_count}')

    # Module frequency (which modules have the most bugs)
    from collections import Counter
    module_counts = Counter(m.get('module', '') for m in all_metrics if m.get('module'))
    if module_counts:
        top_modules = module_counts.most_common(3)
        mod_str = ', '.join(f'{m}({c})' for m, c in top_modules)
        insights.append(f'Top bug modules: {mod_str}')

    # Verify success rate
    verify_results = [m.get('verify_result') for m in all_metrics if m.get('verify_result')]
    if verify_results:
        verify_pass = sum(1 for v in verify_results if v == 'PASS') / len(verify_results)
        insights.append(f'Verification pass rate: {verify_pass*100:.0f}%')
        if verify_pass < 0.7:
            insights.append('\u26a0\ufe0f Verification pass rate <70% — fixes may need more testing before commit')

    return insights


def _show_tuning_hint(module, task_type):
    """Surface pipeline history insights for this module at task start."""
    metrics_path = os.path.join(SDLC_DIR, 'memory', 'pipeline_metrics.json')
    if not os.path.exists(metrics_path):
        return

    try:
        with open(metrics_path, 'r', encoding='utf-8') as f:
            all_metrics = json.load(f)
    except Exception:
        return

    if not all_metrics:
        return

    # Module-specific history
    module_lower = module.lower()
    module_tasks = [m for m in all_metrics if m.get('module', '').lower() == module_lower]

    hints = []

    if module_tasks:
        # Average cycle time for this module
        cycles = [m['cycle_time_minutes'] for m in module_tasks if m.get('cycle_time_minutes')]
        if cycles:
            avg = sum(cycles) / len(cycles)
            hints.append(f'{len(module_tasks)} prior task(s) on {module} | avg cycle: {avg:.0f} min')

        # Most common fix files for this module
        all_kw = []
        for m in module_tasks:
            all_kw.extend(m.get('keywords', []))
        if all_kw:
            from collections import Counter
            top_kw = Counter(all_kw).most_common(3)
            kw_str = ', '.join(f'{k}' for k, c in top_kw)
            hints.append(f'Common keywords in past {module} fixes: {kw_str}')

        # Recipe hit rate for this module
        recipe_hits = sum(1 for m in module_tasks if m.get('recipe_hit'))
        if recipe_hits:
            hints.append(f'Recipe hit rate for {module}: {recipe_hits}/{len(module_tasks)}')

    # Global insights
    if len(all_metrics) >= 5:
        insights_path = os.path.join(SDLC_DIR, 'memory', 'tuning_insights.md')
        if os.path.exists(insights_path):
            hints.append(f'See .sdlc/memory/tuning_insights.md for {len(all_metrics)}-task pipeline analysis')

    if hints:
        print()
        print(f'  \U0001f9e0 Pipeline Intelligence:')
        for h in hints[:3]:
            print(f'     {h}')


def cmd_history(state):
    history = state.get('history', [])
    if not history:
        # Also check done/ folder
        if os.path.exists(DONE_DIR):
            folders = os.listdir(DONE_DIR)
            if folders:
                print(f'\n  Completed tasks ({len(folders)}):')
                for f in folders:
                    print(f'    \u2705 {f}/')
                print()
                return
        print('  No completed tasks.')
        return

    print(f'\n  Completed ({len(history)}):')
    for h in history[-10:]:
        cycle = h.get('cycle_time_minutes')
        cycle_str = f' [{_format_duration(cycle)}]' if cycle else ''
        print(f'    \u2705 [{h["id"]}] {h["type"]}({h["module"]}): {h["summary"]}{cycle_str}')
    print()


# ── Metrics Helpers ──

def _parse_iso(s):
    """Parse ISO datetime string to datetime object."""
    try:
        return datetime.fromisoformat(s)
    except (ValueError, TypeError):
        return None


def _phase_duration(timeline, phase_name):
    """Get duration in minutes for a specific phase from timeline."""
    for entry in timeline:
        if entry.get('phase') == phase_name and 'entered_at' in entry and 'exited_at' in entry:
            start = _parse_iso(entry['entered_at'])
            end = _parse_iso(entry['exited_at'])
            if start and end:
                return (end - start).total_seconds() / 60
    return None


def _compute_cycle_time(task):
    """Compute total cycle time in minutes."""
    start = _parse_iso(task.get('started_at'))
    end = _parse_iso(task.get('completed_at'))
    if start and end:
        return (end - start).total_seconds() / 60
    return None


def _compute_phase_durations(timeline):
    """Compute duration per phase from timeline entries."""
    durations = {}
    for entry in timeline:
        phase = entry.get('phase')
        start = _parse_iso(entry.get('entered_at'))
        end = _parse_iso(entry.get('exited_at'))
        if phase and start and end:
            mins = (end - start).total_seconds() / 60
            durations[phase] = durations.get(phase, 0) + mins
    return durations


def _format_duration(minutes):
    """Format minutes into human-readable string."""
    if minutes is None:
        return '?'
    if minutes < 1:
        return f'{minutes * 60:.0f}s'
    if minutes < 60:
        return f'{minutes:.0f}m'
    hours = int(minutes // 60)
    mins = int(minutes % 60)
    if hours < 24:
        return f'{hours}h {mins}m'
    days = hours // 24
    hours = hours % 24
    return f'{days}d {hours}h'


def cmd_metrics(state):
    """Analyze completed tasks for metrics."""
    history = state.get('history', [])
    if not history:
        print('  No completed tasks. Metrics need history data.')
        return

    # Overall stats
    total = len(history)
    with_cycle = [h for h in history if h.get('cycle_time_minutes')]
    avg_cycle = sum(h['cycle_time_minutes'] for h in with_cycle) / len(with_cycle) if with_cycle else None

    print(f'\n  \U0001f4ca SDLC Metrics ({total} completed tasks)')
    print(f'  {"=" * 45}')

    if avg_cycle:
        print(f'\n  Average Cycle Time: {_format_duration(avg_cycle)}')

    # Per-module breakdown
    modules = {}
    for h in history:
        mod = h.get('module', 'unknown')
        modules.setdefault(mod, []).append(h)

    print(f'\n  Module Hotspots:')
    sorted_mods = sorted(modules.items(), key=lambda x: len(x[1]), reverse=True)
    for mod, tasks in sorted_mods:
        mod_cycles = [t.get('cycle_time_minutes') for t in tasks if t.get('cycle_time_minutes')]
        avg = sum(mod_cycles) / len(mod_cycles) if mod_cycles else None
        avg_str = f' (avg: {_format_duration(avg)})' if avg else ''
        print(f'    {mod}: {len(tasks)} tasks{avg_str}')

    # Per-type breakdown
    types = {}
    for h in history:
        t = h.get('type', 'unknown')
        types.setdefault(t, []).append(h)

    print(f'\n  Task Types:')
    for t, tasks in sorted(types.items(), key=lambda x: len(x[1]), reverse=True):
        print(f'    {t}: {len(tasks)}')

    # Phase bottleneck analysis
    all_durations = {}
    for h in history:
        for phase, mins in h.get('phase_durations', {}).items():
            all_durations.setdefault(phase, []).append(mins)

    if all_durations:
        print(f'\n  Phase Bottlenecks (avg time per phase):')
        for phase in PHASES:
            if phase in all_durations and phase not in ('IDLE', 'DONE'):
                vals = all_durations[phase]
                avg = sum(vals) / len(vals)
                bar = '\u2588' * min(30, max(1, int(avg / 2)))  # Simple bar chart
                print(f'    {phase:15s} {_format_duration(avg):>8s} {bar}')

    # Priority breakdown
    priorities = {}
    for h in history:
        p = h.get('priority', 'medium')
        priorities.setdefault(p, []).append(h)

    if len(priorities) > 1:
        prio_order = ['critical', 'high', 'medium', 'low']
        print(f'\n  By Priority:')
        for p in prio_order:
            if p in priorities:
                count = len(priorities[p])
                print(f'    {p}: {count}')

    # Step-level bottleneck analysis (v4.2 — agent time vs wall time)
    step_agent_durations = {}
    step_wall_durations = {}
    for h in history:
        for p_entry in h.get('progress', []):
            step_name = p_entry.get('name', '')
            agent_dur = p_entry.get('duration_minutes', 0)
            wall_dur = p_entry.get('wall_minutes', agent_dur)  # fallback to agent if no wall
            if step_name and agent_dur > 0 and step_name not in ('escalated', 'auto_escalated'):
                step_agent_durations.setdefault(step_name, []).append(agent_dur)
                step_wall_durations.setdefault(step_name, []).append(wall_dur)

    if step_agent_durations:
        print(f'\n  Step Bottlenecks (agent time | wall time):')
        sorted_steps = sorted(step_agent_durations.items(), key=lambda x: sum(x[1])/len(x[1]), reverse=True)
        for step_name, durations in sorted_steps:
            avg_agent = sum(durations) / len(durations)
            wall_durs = step_wall_durations.get(step_name, durations)
            avg_wall = sum(wall_durs) / len(wall_durs)
            count = len(durations)
            bar = '\u2588' * min(30, max(1, int(avg_agent / 2)))
            idle = avg_wall - avg_agent
            idle_str = f' (+{_format_duration(idle)} idle)' if idle > 1 else ''
            print(f'    {step_name:15s} {_format_duration(avg_agent):>8s} (n={count}) {bar}{idle_str}')

    print()


def cmd_banner(state):
    task = state.get('active_task')
    if not task:
        print('IDLE — no active task')
        return

    phase = task.get('phase', 'IDLE')
    is_perf = task.get('type') == 'perf'
    if is_perf:
        icon = PERF_PHASE_ICONS.get(phase, PHASE_ICONS.get(phase, ''))
        phase_display = PERF_PHASE_NAMES.get(phase, phase)
    else:
        icon = PHASE_ICONS.get(phase, '')
        phase_display = phase
    auto = ' \U0001f680AUTO' if task.get('auto_flow') else ''

    # Track + step info (v2.5)
    track = task.get('track', '')
    current_step = task.get('current_step', 0)
    track_info = ''
    if track:
        steps_data = _load_steps_data()
        track_def = steps_data.get('tracks', {}).get(track, {})
        total = len(track_def.get('steps', []))
        track_info = f' | Track: {track} (Step {current_step}/{total})'

    print(f'{icon} [{task["id"]}] Phase: {phase_display}{track_info} | Task: {task["summary"]}{auto}')



# ── v2.5: Step-Driven Pipeline ──

def _load_steps_data():
    """Load steps.json definitions."""
    if os.path.exists(STEPS_FILE):
        with open(STEPS_FILE, 'r', encoding='utf-8') as f:
            return json.load(f)
    return {'tracks': {}}


def _auto_detect_track(summary, actual='', task_type='fix'):
    """v4.3.1: Auto-detect pipeline track from bug description keywords.

    Returns (track_name, confidence, reason).
    Confidence: 'HIGH' (auto-apply), 'MEDIUM' (suggest), 'LOW' (default STANDARD).
    """
    text = f'{summary} {actual}'.lower()

    # ── MICRO signals: trivial, obvious fix ──
    micro_keywords = ['typo', 'label', 'spelling', 'wrong column', 'column name',
                       'missing where', 'hardcoded', 'wrong text', 'wrong label',
                       'display name', 'alignment', 'css', 'padding', 'margin']
    micro_hits = sum(1 for k in micro_keywords if k in text)
    if micro_hits >= 1 and task_type in ('fix', 'hotfix'):
        return ('MICRO', 'HIGH', f'Trivial fix signal: matched {micro_hits} keyword(s)')

    # ── STANDARD signals: complex, multi-module ──
    standard_keywords = ['cross-module', 'cross module', 'multiple module', 'gst',
                          'financial', 'calculation chain', 'data flow', 'multi-table',
                          'transaction', 'billing.*estimation', 'estimation.*billing']
    standard_hits = sum(1 for k in standard_keywords if k in text)
    multi_module_signals = text.count('module') > 1 or ' and ' in text
    if standard_hits >= 1 or multi_module_signals:
        return ('STANDARD', 'MEDIUM', f'Complex fix signal: matched {standard_hits} keyword(s)')

    # ── EXPRESS signals: single module, specific screen ──
    express_keywords = ['report', 'missing', 'not showing', 'not reflecting',
                         'wrong total', 'wrong value', 'filter', 'opening balance',
                         'single module', 'one screen', 'not displayed']
    express_hits = sum(1 for k in express_keywords if k in text)
    if express_hits >= 1:
        return ('EXPRESS', 'HIGH', f'Simple bug signal: matched {express_hits} keyword(s)')

    # Default
    if task_type in ('fix', 'hotfix'):
        return ('EXPRESS', 'LOW', 'Default for fix/hotfix tasks')
    return ('STANDARD', 'LOW', 'Default for feature/CR tasks')


def _get_track_steps(track_name):
    """Get step list for a given track."""
    data = _load_steps_data()
    track = data.get('tracks', {}).get(track_name, {})
    return track.get('steps', [])


def _get_current_step(task):
    """Get the current step definition for a task."""
    track = task.get('track', 'STANDARD')
    steps = _get_track_steps(track)
    current = task.get('current_step', 1)
    if 1 <= current <= len(steps):
        return steps[current - 1]
    return None


def _fill_placeholders(text, task):
    """Replace {TASK_ID}, {summary}, {module} in instruction text."""
    return (text
            .replace('{TASK_ID}', task.get('id', '?'))
            .replace('{task_id}', task.get('id', '?'))
            .replace('{summary}', task.get('summary', '?'))
            .replace('{module}', task.get('module', '?')))


def _append_living_context(task, step, note=''):
    """Append step completion to the living context file."""
    ctx_path = os.path.join(task_dir(task['id']), 'context.md')
    now = datetime.now().strftime('%H:%M')

    entry = f'\n## Step {step["id"]}: {step["name"].upper()} (completed {now})\n'
    if note:
        entry += f'{note}\n'
    entry += '\n'

    # Create or append
    mode = 'a' if os.path.exists(ctx_path) else 'w'
    if mode == 'w':
        header = (f'# Living Context: {task["id"]}\n\n'
                  f'> Task: {task["summary"]}\n'
                  f'> Track: {task.get("track", "?")}\n'
                  f'> Started: {task.get("started_at", "?")[:16]}\n\n')
        entry = header + entry

    with open(ctx_path, mode, encoding='utf-8') as f:
        f.write(entry)


def _auto_classify_track(task_type, rag_count=0, lca_modules=1, recipe_found=False, code_snippets=False):
    """Auto-classify track based on task type and discovery results."""
    if task_type in ('refactor', 'cr'):
        return 'STANDARD'  # No COMPLEX — only EXPRESS or STANDARD
    # EXPRESS: recipe found, or simple single-module bug with code context
    if recipe_found:
        return 'EXPRESS'
    # RAG <= 5 because results include docs/migrations/brain files alongside code
    if rag_count <= 5 and lca_modules <= 1 and code_snippets:
        return 'EXPRESS'
    return 'STANDARD'


def cmd_what_next(state):
    """Output the current step checklist + required deliverable.

    Phase 7 (v3.9): Renders structured checklists with [ ]/[x] tracking.
    Falls back to old prose 'instruction' field if 'checklist' not present.
    """
    task = state.get('active_task')
    if not task:
        print('\n  \u26a0\ufe0f  NO ACTIVE TASK \u2014 you MUST start one before doing anything.')
        print('  Run NOW: python .sdlc/engine/cli.py start -t fix -m {module} -s "{description}"')
        print('  \u26d4 Do NOT investigate, grep, read brain docs, or search recipes until a task is started.')
        print('  Everything happens inside the pipeline. Start the task FIRST.')
        return

    track = task.get('track', 'STANDARD')
    steps = _get_track_steps(track)
    current = task.get('current_step', 1)

    if not steps:
        print(f'  \u26a0\ufe0f  No steps defined for track: {track}')
        print(f'  Check .sdlc/steps.json')
        return

    if current > len(steps):
        print(f'  \u2705 All {len(steps)} steps complete!')
        print(f'  Run: python .sdlc/engine/cli.py done')
        return

    step = steps[current - 1]
    total = len(steps)

    # ── Stamp step_started_at when agent starts working on this step ──
    # This is the real "agent start" time (excludes user idle between steps)
    if not task.get('_step_started_at'):
        task['_step_started_at'] = datetime.now().isoformat()
        save_state(state)

    # Load checklist progress for this step
    step_key = f'step_{current}_checked'
    checked_items = set(task.get(step_key, []))

    # ── Header ──
    w = 62
    print(f'\n  \u2554{"="*w}\u2557')
    title = f'Step {current} of {total}: {step["name"].upper()}'
    pad = w - 2 - len(title)
    print(f'  \u2551  {title}{" "*pad}\u2551')
    print(f'  \u2560{"="*w}\u2563')
    print(f'  \u2551{" "*w}\u2551')

    # ── Checklist items ──
    checklist = step.get('checklist', [])
    # v4.3.1: For intake steps, replace static checklist with dynamic questions
    if step.get('name') == 'intake' and track not in ('MICRO',):
        to_ask, answered = get_intake_questions(task)
        dynamic = format_intake_checklist(to_ask, answered)
        if dynamic:
            checklist = dynamic
    if checklist:
        for i, item in enumerate(checklist):
            item_text = _fill_placeholders(item, task)
            mark = '\u2705' if i in checked_items else f'{i+1}.'
            prefix = f'  {mark} '

            # Word-wrap long items
            available = w - 4 - len(prefix)
            if len(item_text) <= available:
                line = f'{prefix}{item_text}'
                pad = w - 2 - len(line)
                print(f'  \u2551{line}{" "*max(0, pad)}\u2551')
            else:
                # First line with prefix
                wrapped = textwrap.wrap(item_text, available)
                first = f'{prefix}{wrapped[0]}'
                pad = w - 2 - len(first)
                print(f'  \u2551{first}{" "*max(0, pad)}\u2551')
                # Continuation lines indented to align
                indent = ' ' * len(prefix)
                for wl in wrapped[1:]:
                    cont = f'  {indent}{wl}'
                    pad = w - 2 - len(cont)
                    print(f'  \u2551{cont}{" "*max(0, pad)}\u2551')

        done_items = len(checked_items)
        total_items = len(checklist)
        print(f'  \u2551{" "*w}\u2551')
        checklist_status = f'Checklist: {done_items}/{total_items} items'
        pad = w - 2 - len(checklist_status)
        print(f'  \u2551  {checklist_status}{" "*pad}\u2551')
    else:
        # Fallback: old prose instruction
        instruction = _fill_placeholders(step.get('instruction', ''), task)
        for line in instruction.split('\n'):
            line = line.strip()
            if not line:
                print(f'  \u2551{" "*w}\u2551')
                continue
            for wrapped in textwrap.wrap(line, w - 4):
                pad = w - 2 - len(wrapped)
                if pad < 0:
                    wrapped = wrapped[:w-5] + '...'
                    pad = 0
                print(f'  \u2551  {wrapped}{" "*pad}\u2551')

    # ── Forbidden actions ──
    forbidden = step.get('forbidden', [])
    if forbidden:
        print(f'  \u2551{" "*w}\u2551')
        hdr = '\u26d4 FORBIDDEN:'
        pad = w - 2 - len(hdr)
        print(f'  \u2551  {hdr}{" "*pad}\u2551')
        for fb in forbidden:
            fb_text = f'  - {fb}'
            for wrapped in textwrap.wrap(fb_text, w - 4):
                pad = w - 2 - len(wrapped)
                print(f'  \u2551  {wrapped}{" "*max(0, pad)}\u2551')

    # ── On failure ──
    on_failure = step.get('on_failure', '')
    if on_failure:
        print(f'  \u2551{" "*w}\u2551')
        fail_hdr = '\U0001f6a8 IF IT FAILS:'
        pad = w - 2 - len(fail_hdr)
        print(f'  \u2551  {fail_hdr}{" "*pad}\u2551')
        for wrapped in textwrap.wrap(f'  {on_failure}', w - 4):
            pad = w - 2 - len(wrapped)
            print(f'  \u2551  {wrapped}{" "*max(0, pad)}\u2551')

    # ── Fallback ──
    fallback = step.get('fallback', '')
    if fallback:
        print(f'  \u2551{" "*w}\u2551')
        fb_hdr = '\u26a0\ufe0f  FALLBACK:'
        pad = w - 2 - len(fb_hdr)
        print(f'  \u2551  {fb_hdr}{" "*pad}\u2551')
        for wrapped in textwrap.wrap(f'  {fallback}', w - 4):
            pad = w - 2 - len(wrapped)
            print(f'  \u2551  {wrapped}{" "*max(0, pad)}\u2551')

    print(f'  \u2551{" "*w}\u2551')

    # Human checkpoint indicator
    if step.get('human_checkpoint'):
        tag = '\u23f8\ufe0f  HUMAN CHECKPOINT \u2014 wait for approval'
        pad = w - 2 - len(tag) + 2
        print(f'  \u2551  {tag}{" "*pad}\u2551')

    # ── Required deliverable ──
    deliverable = step.get('deliverable', {})
    target_file = deliverable.get('file', '')
    check_type = deliverable.get('check', '')

    if target_file or check_type:
        label = ''
        if check_type == 'file_exists' and target_file:
            label = f'\U0001f4ce Required: {target_file}'
        elif check_type == 'contains_section' and target_file:
            section = deliverable.get('section', '')
            label = f'\U0001f4ce Required: {target_file} \u2192 {section}'
        elif check_type == 'artifact_exists' and target_file:
            label = f'\U0001f4ce Required: artifact {target_file}'
        elif check_type == 'git_has_changes':
            label = '\U0001f4ce Required: modified files in git'
        elif check_type == 'git_committed':
            label = '\U0001f4ce Required: git commit with task ID'

        if label:
            print(f'  \u2551{" "*w}\u2551')
            pad = w - 2 - len(label)
            if pad < 0:
                label = label[:w-5] + '...'
                pad = 0
            print(f'  \u2551  {label}{" "*pad}\u2551')

    print(f'  \u2551{" "*w}\u2551')
    print(f'  \u2560{"="*w}\u2563')

    # Progress bar
    done_count = current - 1
    bar_len = 20
    filled = int(bar_len * done_count / total)
    bar = '\u2588' * filled + '\u2591' * (bar_len - filled)
    pct = int(100 * done_count / total)
    progress_line = f'Progress: [{bar}] {pct}% ({done_count}/{total})'
    pad = w - 2 - len(progress_line)
    print(f'  \u2551  {progress_line}{" "*pad}\u2551')

    next_line = 'When done: python .sdlc/engine/cli.py step-done'
    pad = w - 2 - len(next_line)
    print(f'  \u2551  {next_line}{" "*pad}\u2551')
    print(f'  \u255a{"="*w}\u255d\n')


def _validate_deliverable(task, step):
    """Check if a step's required deliverable exists. Returns (ok, message)."""
    deliverable = step.get('deliverable', {})
    if not deliverable:
        return True, ''

    check = deliverable.get('check', '')
    target = deliverable.get('file', '')
    tdir = task_dir(task['id'])

    if check == 'file_exists' and target:
        path = os.path.join(tdir, target)
        if not os.path.exists(path):
            return False, f'{target} not found in {tdir}'
        return True, ''

    elif check == 'contains_section' and target:
        path = os.path.join(tdir, target)
        section = deliverable.get('section', '')
        if not os.path.exists(path):
            return False, f'{target} does not exist'
        with open(path, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()
        if section and section not in content:
            return False, f'{target} missing section: {section}'
        return True, ''

    elif check == 'artifact_exists' and target:
        # Check in task dir first, then artifact conventions
        path = os.path.join(tdir, target)
        if os.path.exists(path):
            return True, ''
        # Also OK if it was written as an artifact (we can't verify artifact dir easily)
        # So just check task dir
        return False, f'{target} not found in {tdir}'

    elif check == 'git_has_changes':
        result = subprocess.run(['git', 'diff', '--name-only'],
                                capture_output=True, text=True, cwd=PROJECT_ROOT)
        staged = subprocess.run(['git', 'diff', '--cached', '--name-only'],
                                capture_output=True, text=True, cwd=PROJECT_ROOT)
        if not result.stdout.strip() and not staged.stdout.strip():
            return False, 'No modified files detected (git diff empty)'
        return True, ''

    elif check == 'git_committed':
        result = subprocess.run(['git', 'log', '-1', '--oneline'],
                                capture_output=True, text=True, cwd=PROJECT_ROOT)
        if task['id'] not in result.stdout:
            return False, f'No commit found containing [{task["id"]}]'
        return True, ''

    return True, ''


def cmd_step_done(state, args=None):
    """Validate deliverable, then mark step done and advance."""
    task = state.get('active_task')
    if not task:
        print('  No active task.')
        return

    track = task.get('track', 'STANDARD')
    steps = _get_track_steps(track)
    current = task.get('current_step', 1)

    if not steps or current > len(steps):
        print('  No current step to complete.')
        return

    step = steps[current - 1]

    # ── NEW: Validate deliverable before advancing ──
    ok, reason = _validate_deliverable(task, step)
    if not ok:
        print(f'  \u274c BLOCKED: Step {current} ({step["name"]}) deliverable missing.')
        print(f'  Reason: {reason}')
        print(f'  Complete the deliverable, then call step-done again.')
        return  # DON'T advance

    # ── AUTO-ESCALATION GUARD (EXPRESS → STANDARD) ──
    # After discover step on EXPRESS, check discovery.md for cross-module signals
    if track == 'EXPRESS' and step['name'] == 'discover':
        discovery_path = os.path.join(task_dir(task['id']), 'discovery.md')
        if os.path.exists(discovery_path):
            try:
                with open(discovery_path, 'r', encoding='utf-8') as f:
                    disc_content = f.read().lower()
                # Count unique file paths (admin/application/...)
                import re
                file_refs = set(re.findall(r'admin/application/(?:controllers|models|views)/\S+\.php', disc_content))
                # Count module references from LCA impact
                module_refs = set(re.findall(r'module:\s*(\w+)', disc_content))
                if len(file_refs) > 3 or len(module_refs) > 2:
                    print(f'  \u26a1 AUTO-ESCALATION TRIGGERED')
                    print(f'    Files referenced: {len(file_refs)} (threshold: >3)')
                    print(f'    Modules referenced: {len(module_refs)} (threshold: >2)')
                    print(f'    Switching EXPRESS → STANDARD (step 2: investigate)')
                    task['track'] = 'STANDARD'
                    task['current_step'] = 3  # Skip to investigate (discover already done, intake already done)
                    task['escalated_at'] = datetime.now().isoformat()
                    task.setdefault('progress', []).append({
                        'step': 0,
                        'name': 'auto_escalated',
                        'completed_at': datetime.now().isoformat(),
                        'note': f'Auto-escalated: {len(file_refs)} files, {len(module_refs)} modules detected in discovery.md',
                    })
                    save_state(state)
                    print(f'  Run: python .sdlc/engine/cli.py what-next')
                    return
            except Exception:
                pass  # Non-critical — continue normal flow

    # Record progress with step timing
    progress = task.setdefault('progress', [])
    note = getattr(args, 'note', '') if args else ''
    now = datetime.now()

    # Use _step_started_at (set by what-next) for agent-only time
    # Falls back to previous step completed_at for wall-clock time
    agent_started = task.pop('_step_started_at', None)
    wall_started = task.get('started_at', now.isoformat())
    if progress:
        wall_started = progress[-1].get('completed_at', wall_started)

    # Compute durations
    try:
        wall_start_dt = datetime.fromisoformat(wall_started)
        wall_minutes = (now - wall_start_dt).total_seconds() / 60
    except Exception:
        wall_minutes = 0

    agent_minutes = wall_minutes  # default: same as wall
    if agent_started:
        try:
            agent_start_dt = datetime.fromisoformat(agent_started)
            agent_minutes = (now - agent_start_dt).total_seconds() / 60
        except Exception:
            pass

    progress.append({
        'step': current,
        'name': step['name'],
        'started_at': agent_started or wall_started,
        'completed_at': now.isoformat(),
        'duration_minutes': round(agent_minutes, 1),
        'wall_minutes': round(wall_minutes, 1),
        'note': note,
    })

    # Append to living context
    _append_living_context(task, step, note)

    # Advance step
    next_step_num = current + 1
    task['current_step'] = next_step_num
    save_state(state)

    duration_str = f' ({agent_minutes:.1f}m)' if agent_minutes > 0 else ''
    if next_step_num > len(steps):
        print(f'  \u2705 Step {current} ({step["name"]}) done.{duration_str}')
        print(f'  \U0001f389 All {len(steps)} steps complete!')
        print(f'  Run: python .sdlc/engine/cli.py done')
    else:
        next_s = steps[next_step_num - 1]
        print(f'  \u2705 Step {current} ({step["name"]}) done.{duration_str}')
        print(f'  Next: Step {next_step_num} ({next_s["name"]})')
        print(f'  Run: python .sdlc/engine/cli.py what-next')

    # Clear checklist state for completed step
    step_key = f'step_{current}_checked'
    if step_key in task:
        del task[step_key]
        save_state(state)


def cmd_check(state, args):
    """Mark a checklist item as done for the current step.

    Usage: sdlc check 1        — mark item 1 done
           sdlc check 1 2 3    — mark items 1, 2, 3 done
           sdlc check all      — mark all items done
           sdlc check reset    — reset all items to unchecked
    """
    task = state.get('active_task')
    if not task:
        print('  No active task.')
        return

    track = task.get('track', 'STANDARD')
    steps = _get_track_steps(track)
    current = task.get('current_step', 1)

    if not steps or current > len(steps):
        print('  No current step.')
        return

    step = steps[current - 1]
    checklist = step.get('checklist', [])
    if not checklist:
        print('  Current step has no checklist.')
        return

    step_key = f'step_{current}_checked'
    checked = set(task.get(step_key, []))

    items_arg = args.items if hasattr(args, 'items') else []

    if not items_arg:
        print('  Usage: sdlc check 1 2 3  (or: check all | check reset)')
        return

    if 'reset' in items_arg:
        task[step_key] = []
        save_state(state)
        print(f'  \u21ba Reset all checklist items for step {current}.')
        return

    if 'all' in items_arg:
        task[step_key] = list(range(len(checklist)))
        save_state(state)
        print(f'  \u2705 All {len(checklist)} items checked for step {current}.')
        return

    for item_str in items_arg:
        try:
            idx = int(item_str) - 1  # 1-indexed input → 0-indexed storage
            if 0 <= idx < len(checklist):
                checked.add(idx)
                print(f'  \u2705 Item {idx+1}: {checklist[idx][:50]}...' if len(checklist[idx]) > 50 else f'  \u2705 Item {idx+1}: {checklist[idx]}')
            else:
                print(f'  \u26a0\ufe0f  Item {item_str} out of range (1-{len(checklist)})')
        except ValueError:
            print(f'  \u26a0\ufe0f  Invalid item: {item_str}')

    task[step_key] = list(checked)
    save_state(state)
    print(f'  Checklist: {len(checked)}/{len(checklist)} items done')


def _url_to_targets(url_str):
    """Map a CodeIgniter 3 URL to controller file, method, and likely view/JS paths.
    
    Input examples:
        'admin_ret_reports/cash_book_details'
        'http://localhost/mpj/admin/index.php/admin_ret_reports/cash_book_details'
        '/admin/index.php/admin_ret_billing/add_billing/123'
    
    Returns: dict with controller, method, controller_file, view_hints, js_hints
    """
    # Strip protocol, host, path prefix
    path = url_str.strip()
    # Remove http://host/project/admin/index.php/ prefix
    for prefix in ['http://', 'https://']:
        if path.startswith(prefix):
            path = path.split('/', 3)[-1] if path.count('/') >= 3 else path
            break
    # Remove /admin/index.php/ or admin/index.php/
    for marker in ['/admin/index.php/', 'admin/index.php/']:
        idx = path.find(marker)
        if idx >= 0:
            path = path[idx + len(marker):]
            break
    # Remove leading/trailing slashes
    path = path.strip('/')
    # Remove query string
    if '?' in path:
        path = path.split('?')[0]
    
    parts = path.split('/')
    controller = parts[0] if parts else ''
    method = parts[1] if len(parts) > 1 else 'index'
    params = parts[2:] if len(parts) > 2 else []
    
    if not controller:
        return None
    
    result = {
        'controller': controller,
        'method': method,
        'params': params,
        'controller_file': f'admin/application/controllers/{controller}.php',
        'view_hints': [],
        'js_hints': [],
        'model_hints': [],
    }
    
    # Derive likely view paths (CI3 convention: views/{controller_without_admin_prefix}/)
    view_prefix = controller.replace('admin_', '').replace('ret_', 'ret/')
    result['view_hints'] = [
        f'admin/application/views/{view_prefix}/',
        f'admin/application/views/ret/{method}*',
    ]
    
    # Derive likely JS paths
    js_prefix = controller.replace('admin_', '')
    result['js_hints'] = [
        f'admin/assets/js/{js_prefix}*.js',
    ]
    
    # Derive likely model names from controller name
    model_name = controller.replace('admin_', '') + '_model'
    result['model_hints'] = [
        f'admin/application/models/{model_name}.php',
    ]
    
    return result


def _urls_to_targets(url_str):
    """Parse one or more URLs from a string.

    Splits on: ' and ', ',', newlines, or recognizes multiple http:// URLs.
    Returns: list of target dicts from _url_to_targets().
    """
    if not url_str or not url_str.strip():
        return []

    raw = url_str.strip()

    # Split strategies (ordered by priority)
    parts = []
    if ' and ' in raw.lower():
        # "admin_opening_master/cash_opening/list and admin_ret_reports/cash_book/list"
        parts = [p.strip() for p in raw.split(' and ')]
    elif ',' in raw:
        # "url1, url2"
        parts = [p.strip() for p in raw.split(',')]
    elif '\n' in raw:
        # Multi-line
        parts = [p.strip() for p in raw.split('\n')]
    elif raw.count('http') > 1:
        # Multiple full URLs: "http://...url1 http://...url2"
        import re
        parts = re.split(r'\s+(?=https?://)', raw)
    else:
        parts = [raw]

    targets = []
    seen_controllers = set()
    for part in parts:
        part = part.strip()
        if not part:
            continue
        t = _url_to_targets(part)
        if t:
            # Deduplicate by controller+method
            key = f"{t['controller']}::{t['method']}"
            if key not in seen_controllers:
                seen_controllers.add(key)
                targets.append(t)

    return targets


# ── Call Tree Builder (v4.0) ──────────────────────────────────────────────

# PHP builtins and framework methods to skip in call trees
_SKIP_FUNCTIONS = {
    'date', 'strtotime', 'userdata', 'result_array', 'intval', 'floatval',
    'empty', 'count', 'implode', 'explode', 'array_merge', 'in_array',
    'round', 'defined', 'get_instance', 'model', 'row_array', 'num_rows',
    'row', 'query', 'where', 'select', 'join', 'usort', 'array_push',
    'isset', 'str_replace', 'array_values', 'array_keys', 'strtolower',
    'trim', 'array_unique', 'json_encode', 'json_decode', 'array_filter',
    'array_map', 'array_column', 'array_sum', 'is_array', 'strlen',
    'substr', 'sprintf', 'number_format', 'ceil', 'floor', 'abs', 'max',
    'min', 'log_message', 'print_r', 'var_dump',
}

# Utility models that add noise (not domain logic)
_SKIP_MODELS = {
    'get_profile_settings', 'get_access', 'check_access', 'get_instance',
    'get_admin_settings', 'get_company_details',
}


def _build_call_tree(method_name, controller_class=None, max_depth=4):
    """Build a call tree from LCA index data using BFS traversal.

    Returns dict with: tree, all_tables, all_methods, reverse_callers,
    formulas, magic_numbers.  Returns None if LCA data is unavailable.
    """
    import json as json_mod

    lca_dir = os.path.join(PROJECT_ROOT, '.lca')
    if not os.path.isdir(lca_dir):
        return None

    # Load ALL LCA indexes
    all_calls = []
    all_funcs = []
    all_l2 = {}
    all_formulas = []
    all_magics = []

    for f in os.listdir(lca_dir):
        if f.endswith('.json'):
            try:
                with open(os.path.join(lca_dir, f), 'r', encoding='utf-8') as fh:
                    idx = json_mod.load(fh)
                all_calls.extend(idx.get('calls', []))
                all_funcs.extend(idx.get('functions', []))
                all_l2.update(idx.get('level2', {}))
                all_formulas.extend(idx.get('formulas', []))
                all_magics.extend(idx.get('magic_numbers', []))
            except Exception:
                pass

    if not all_calls:
        return None

    # Build adjacency list
    graph = {}
    for c in all_calls:
        src = c.get('source', '')
        tgt = c.get('target', '')
        if src and tgt:
            graph.setdefault(src, set()).add(tgt)

    # Build function registry
    func_registry = {}
    func_full_names = set()
    for f in all_funcs:
        name = f.get('name', '')
        ffile = f.get('file', '')
        func_full_names.add(name)
        short = name.split('::')[-1] if '::' in name else name
        if short not in func_registry:
            func_registry[short] = {'full': name, 'file': ffile}

    # Find root node
    root = None
    search_name = method_name.lower()
    if controller_class:
        full_try = f'{controller_class}::{method_name}'
        for fn in func_full_names:
            if fn.lower() == full_try.lower():
                root = fn
                break

    if not root:
        for fn in func_full_names:
            if fn.lower().endswith(f'::{search_name}'):
                root = fn
                break

    if not root:
        for fn in func_full_names:
            short = fn.split('::')[-1].lower()
            if search_name in short or short in search_name:
                root = fn
                break

    if not root:
        return None

    # BFS traversal
    tree = {}
    queue = [(root, 0)]
    visited = set()
    method_order = []

    while queue:
        node, depth = queue.pop(0)
        if depth > max_depth or node in visited:
            continue
        visited.add(node)

        short_name = node.split('::')[-1] if '::' in node else node
        class_name = node.split('::')[0] if '::' in node else ''

        children_raw = graph.get(node, set())
        project_children = []
        tables = []

        for child in children_raw:
            if child.startswith('TABLE:'):
                tables.append(child.replace('TABLE:', ''))
            elif (child.lower() not in _SKIP_FUNCTIONS and
                  child.lower() not in _SKIP_MODELS and
                  (child in func_full_names or
                   any(fn.endswith(f'::{child}') for fn in func_full_names))):
                project_children.append(child)

        node_file = ''
        reg = func_registry.get(short_name, {})
        if reg:
            node_file = reg.get('file', '')

        tree[node] = {
            'depth': depth,
            'calls': sorted(set(project_children)),
            'tables': sorted(set(tables)),
            'file': node_file,
            'class': class_name,
            'short': short_name,
        }

        method_order.append({
            'method': short_name,
            'class': class_name,
            'file': node_file,
            'full': node,
            'depth': depth,
        })

        for child in sorted(set(project_children)):
            if child not in visited:
                full_child = child
                for fn in func_full_names:
                    if fn.endswith(f'::{child}'):
                        full_child = fn
                        break
                queue.append((full_child, depth + 1))

    # Collect tables
    all_tables = set()
    for info in tree.values():
        all_tables.update(info['tables'])

    # Reverse callers from level2
    reverse_callers = {}
    tree_methods = {info['short'] for info in tree.values()}
    for func_key, func_data in all_l2.items():
        short = func_key.split('::')[-1]
        if short in tree_methods:
            callers = func_data.get('called_by', [])
            if callers:
                external = [c for c in callers if c.split('::')[-1] not in tree_methods]
                if external:
                    reverse_callers[short] = external

    # Formulas matching tree methods
    matched_formulas = []
    seen_f = set()
    for formula in all_formulas:
        func_name = formula.get('function', '').lower()
        for tm in tree_methods:
            if tm.lower() in func_name:
                key = f'{formula.get("variable", "")}={formula.get("expression", "")[:40]}'
                if key not in seen_f:
                    seen_f.add(key)
                    matched_formulas.append(formula)
                break

    # Magic numbers matching tree methods
    matched_magics = []
    seen_m = set()
    for magic in all_magics:
        func_name = magic.get('function', '').lower()
        for tm in tree_methods:
            if tm.lower() in func_name:
                key = f'{magic.get("value", "")}_{magic.get("line", "")}'
                if key not in seen_m:
                    seen_m.add(key)
                    matched_magics.append(magic)
                break

    return {
        'root': root,
        'tree': tree,
        'all_tables': all_tables,
        'all_methods': method_order,
        'reverse_callers': reverse_callers,
        'formulas': matched_formulas[:10],
        'magic_numbers': matched_magics[:10],
    }


# ── Recipe Smart Matching (v4.2) ─────────────────────────────────────────

def _index_recipe(filepath):
    """Extract metadata from a single recipe file for matching.
    Returns dict with title, modules, keywords, pattern_id.
    """
    import re
    try:
        with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()
    except IOError:
        return None

    result = {
        'file': os.path.basename(filepath),
        'title': '',
        'modules': '',
        'pattern_id': '',
        'severity': '',
        'keywords': set(),
    }

    file_lines = content.split('\n')

    # Extract title (first # heading)
    for line in file_lines[:5]:
        if line.startswith('# '):
            result['title'] = line[2:].strip()
            break

    # Extract modules from Metadata section
    for line in file_lines[:30]:
        lower = line.lower()
        if 'modules affected' in lower or 'module' in lower.split('|')[0] if '|' in lower else False:
            # Extract everything after the last pipe or colon
            parts = line.split('|')
            if len(parts) >= 3:
                result['modules'] = parts[-2].strip().strip('*').strip()
            elif ':' in line:
                result['modules'] = line.split(':', 1)[1].strip().strip('*').strip()
        if 'pattern id' in lower:
            parts = line.split('|')
            if len(parts) >= 3:
                result['pattern_id'] = parts[-2].strip().strip('*').strip()
            elif ':' in line:
                result['pattern_id'] = line.split(':', 1)[1].strip().strip('*').strip()
        if 'severity' in lower:
            parts = line.split('|')
            if len(parts) >= 3:
                result['severity'] = parts[-2].strip().strip('*').strip()

    # Extract keywords from content
    # Remove code blocks
    clean = re.sub(r'```[\s\S]*?```', '', content)
    # Extract significant words
    stopwords = {
        'this', 'that', 'with', 'from', 'have', 'been', 'does', 'will',
        'should', 'would', 'could', 'because', 'already', 'there',
        'which', 'where', 'when', 'what', 'line', 'file', 'code',
        'function', 'method', 'return', 'value', 'data', 'recipe',
        'true', 'false', 'null', 'none', 'field', 'table', 'used',
        'changes', 'before', 'after', 'need', 'make', 'added', 'removed',
        'example', 'check', 'step', 'note', 'metadata', 'scope',
        'client', 'created', 'developer', 'date', 'pattern',
    }
    words = re.findall(r'[a-zA-Z_]{4,}', clean.lower())
    result['keywords'] = set(w for w in words if w not in stopwords)

    return result


def _match_recipes(recipes_dir, query, module, tables):
    """Score all recipes against the current bug query.
    Returns sorted list of matches with score >= 2.
    """
    import re

    # Build query keywords
    stopwords = {
        'this', 'that', 'with', 'from', 'have', 'been', 'does', 'will',
        'should', 'would', 'could', 'because', 'already', 'there',
        'which', 'where', 'when', 'what', 'line', 'file', 'code',
        'show', 'list', 'report', 'admin',
    }
    query_words = set(w.lower() for w in re.findall(r'[a-zA-Z_]{4,}', query)
                      if w.lower() not in stopwords)
    module_lower = module.lower() if module else ''
    table_names = set(t.lower() for t in (tables or set()))

    results = []

    for fname in os.listdir(recipes_dir):
        if not fname.endswith('.md') or fname.startswith('_'):
            continue

        filepath = os.path.join(recipes_dir, fname)
        idx = _index_recipe(filepath)
        if not idx:
            continue

        score = 0
        matched_keywords = []

        # Score 1: Module match (+3)
        if module_lower and module_lower in idx.get('modules', '').lower():
            score += 3
            matched_keywords.append(f'module:{module}')

        # Score 2: Module in filename (+2)
        if module_lower and module_lower in fname.lower():
            score += 2
            matched_keywords.append(f'filename:{module}')

        # Score 3: Keyword overlap (+1 per match, max 6)
        keyword_hits = query_words.intersection(idx['keywords'])
        kw_score = min(6, len(keyword_hits))
        score += kw_score
        matched_keywords.extend(list(keyword_hits)[:6])

        # Score 4: Table name match (+2 per table)
        if table_names:
            table_hits = table_names.intersection(idx['keywords'])
            score += min(4, len(table_hits) * 2)
            matched_keywords.extend([f'table:{t}' for t in list(table_hits)[:3]])

        # Score 5: Title keyword match (+2)
        title_lower = idx.get('title', '').lower()
        title_hits = sum(1 for w in query_words if w in title_lower)
        if title_hits:
            score += min(3, title_hits)
            matched_keywords.append(f'title_match:{title_hits}')

        if score >= 2:
            # Determine cluster
            if module_lower and module_lower in idx.get('modules', '').lower():
                cluster = module
            elif module_lower and module_lower in fname.lower():
                cluster = module
            else:
                # Try to extract module from filename
                parts = fname.replace('recipe_', '').split('_')
                cluster = parts[0] if parts else 'other'

            results.append({
                'file': fname,
                'title': idx['title'],
                'modules': idx.get('modules', ''),
                'score': score,
                'matched_keywords': matched_keywords,
                'cluster': cluster,
            })

    # Sort by score descending
    results.sort(key=lambda x: x['score'], reverse=True)
    return results[:15]  # Top 15


def _find_common_keywords(recipe_matches):
    """Find keywords that appear in most matched recipes."""
    from collections import Counter
    all_kw = Counter()
    for rm in recipe_matches:
        for kw in rm.get('matched_keywords', []):
            if not kw.startswith(('module:', 'filename:', 'table:', 'title_match:')):
                all_kw[kw] += 1
    # Return keywords appearing in 2+ recipes
    return [kw for kw, count in all_kw.most_common(10) if count >= 2]


def cmd_discover(state, args):
    """One-command discovery: RAG + LCA + recipe search + brain check.

    Outputs a structured discovery.md in the task directory.
    """
    task = state.get('active_task')
    if not task:
        print('  No active task.')
        return

    query = args.query if hasattr(args, 'query') and args.query else task.get('summary', '')
    module = task.get('module', 'unknown')
    task_id = task['id']
    dest = os.path.join(task_dir(task_id), 'discovery.md')
    os.makedirs(task_dir(task_id), exist_ok=True)

    # v3.0.6: Extract structured intake from task
    task_url = task.get('url', '')
    task_expected = task.get('expected', '')
    task_actual = task.get('actual', '')
    task_steps = task.get('steps_to_reproduce', '')

    now = datetime.now().strftime('%Y-%m-%d %H:%M')
    lines = [
        f'# Discovery: {task_id}',
        '',
        f'> **Query**: {query}',
        f'> **Module**: {module}',
        f'> **Generated**: {now}',
        '',
    ]

    # ── Anti-Pattern Check (rejected hypotheses from past tasks) ──
    anti_patterns = _check_anti_patterns(query, module)
    if anti_patterns:
        lines.append('## ⚠️ ANTI-PATTERNS — Previously Rejected Hypotheses')
        lines.append('')
        lines.append('> **WARNING:** The following approaches were tried on similar bugs and REJECTED by the user.')
        lines.append('> Do NOT propose these again without strong new evidence.')
        lines.append('')
        for ap in anti_patterns:
            lines.append(f'### ❌ Rejected: {ap["hypothesis"][:100]}')
            lines.append(f'- **Task:** {ap.get("task_id", "?")} ({ap.get("date", "?")})')
            lines.append(f'- **Module:** {ap.get("module", "?")}')
            lines.append(f'- **Why rejected:** {ap["rejection_reason"]}')
            lines.append(f'- **Keywords:** {", ".join(ap.get("keywords", [])[:8])}')
            lines.append('')
        print(f'  ⚠️  [MEMORY] {len(anti_patterns)} anti-pattern(s) found — injected into discovery.md')

    # ── Section 0: User Context (URL → file mapping) ──
    # v3.9.1: Multi-URL support — parse ALL URLs from task
    all_url_targets = []
    url_targets = None  # backward compat: first target
    if task_url:
        print(f'  \U0001f517 [URL] Resolving: {task_url}')
        all_url_targets = _urls_to_targets(task_url)
        url_targets = all_url_targets[0] if all_url_targets else None
        lines.append('## 0. User Context')
        lines.append('')
        if all_url_targets:
            lines.append(f'**URL**: `{task_url}`')
            if len(all_url_targets) > 1:
                lines.append(f'**Detected {len(all_url_targets)} endpoints** (multi-URL analysis):')
            lines.append('')
            
            all_existing_hints = []
            for ti, ut in enumerate(all_url_targets):
                ctrl_file = ut['controller_file']
                ctrl_exists = os.path.exists(os.path.join(PROJECT_ROOT, ctrl_file))
                marker = f'**Endpoint {ti+1}**: ' if len(all_url_targets) > 1 else ''
                lines.append(f'{marker}**Controller**: `{ctrl_file}` {"✅ exists" if ctrl_exists else "❌ not found"}')
                lines.append(f'**Method**: `{ut["method"]}()`')
                if ut['params']:
                    lines.append(f'**Params**: `{"/".join(ut["params"])}`')
                
                # Check which derived files exist
                for hint in ut['model_hints'] + ut['js_hints']:
                    if '*' in hint:
                        import glob
                        matches = glob.glob(os.path.join(PROJECT_ROOT, hint))
                        for m in matches[:3]:
                            rel = os.path.relpath(m, PROJECT_ROOT).replace('\\', '/')
                            if rel not in all_existing_hints:
                                all_existing_hints.append(rel)
                    elif os.path.exists(os.path.join(PROJECT_ROOT, hint)):
                        if hint not in all_existing_hints:
                            all_existing_hints.append(hint)
                
                print(f'  \u2705 [URL {ti+1}/{len(all_url_targets)}] → {ut["controller"]}::{ut["method"]}()')
                if len(all_url_targets) > 1 and ti < len(all_url_targets) - 1:
                    lines.append('')
            
            if all_existing_hints:
                lines.append('')
                lines.append('**Derived files**:')
                for h in all_existing_hints:
                    lines.append(f'- `{h}`')
        else:
            lines.append(f'**URL**: `{task_url}` (could not parse)')
        
        if task_expected or task_actual:
            lines.append('')
            if task_expected:
                lines.append(f'**Expected**: {task_expected}')
            if task_actual:
                lines.append(f'**Actual**: {task_actual}')
        
        if task_steps:
            lines.append('')
            lines.append(f'**Steps to reproduce**: {task_steps}')
        
        lines.append('')

    # ── v4.2: Load discovery source config ──
    disc_config = _load_config().get('discovery_sources', {})
    lca_enabled = disc_config.get('lca', True)
    rag_enabled = disc_config.get('rag', True)
    recipes_enabled = disc_config.get('recipes', True)
    call_tree_enabled = disc_config.get('call_tree', True)
    brain_enabled = disc_config.get('knowledge_brain', True)

    disabled_sources = []
    if not lca_enabled: disabled_sources.append('LCA')
    if not rag_enabled: disabled_sources.append('RAG')
    if not recipes_enabled: disabled_sources.append('Recipes')
    if not call_tree_enabled: disabled_sources.append('Call Tree')
    if not brain_enabled: disabled_sources.append('Brain')
    if disabled_sources:
        print(f'  \u2699\ufe0f  Config: {", ".join(disabled_sources)} disabled in .sdlc/config.json')

    # Pre-initialize RAG results (LCA has fallback to RAG hits)
    rag_results = []
    rag_unique_files = set()
    rag_skipped = False

    # ── v3.9.4: Pre-load RAG in background while LCA runs ──
    # BGE-M3 cold start takes 30-60s. Starting it here means it loads
    # in parallel with LCA (5-15s), reducing effective wait time.
    import threading
    rag_container = {'results': [], 'error': None}
    rag_thread = None
    if rag_enabled and _rag_available():
        def _rag_preload_worker():
            try:
                rag_container['results'] = _rag_search(
                    query=f'{module} {query}',
                    n_results=10,
                )
            except BaseException as e:
                # BaseException catches SystemExit, KeyboardInterrupt, and
                # multiprocessing child process crashes that propagate up
                rag_container['error'] = str(e)

        rag_thread = threading.Thread(target=_rag_preload_worker, daemon=True)
        rag_thread.start()
        print('  \U0001f680 [RAG] Pre-loading embedding model in background...')
    elif not rag_enabled:
        rag_skipped = True

    # ── Section 1: LCA Impact Analysis ──
    # v4.1: Single-phase URL-driven analysis (replaces 3-step keyword approach)
    # Primary: URL → resolve controller::method() → lca impact --depth 2
    # Fallback: No URL → lca search *{module}* (basic landscape)
    lines.append('## 1. LCA Impact Analysis')
    lines.append('')

    lca_modules = set()
    lca_output = ''
    lca_index = os.path.join(PROJECT_ROOT, '.lca', 'index.json')
    lca_available = lca_enabled and os.path.exists(lca_index)
    if not lca_enabled:
        lines.append('> LCA disabled in config. Using grep + call tree instead.')
        lines.append('')
    elif not os.path.exists(lca_index):
        # Fallback: check legacy path
        lca_legacy = os.path.join(PROJECT_ROOT, 'logimax-devtools', 'backend', 'lca_core', 'lca_index.json')
        if os.path.exists(lca_legacy):
            lca_index = lca_legacy
            lca_available = True

    if not lca_available:
        lines.append(f'LCA index not found. Run: `lca index build admin/application -m all -o .lca/index.json`')
        print(f'  \u26a0\ufe0f  LCA index not found')
    else:
        try:
            # Resolve methods from URL targets (primary path)
            impact_methods = []
            if all_url_targets:
                impact_methods = [ut['method'] for ut in all_url_targets if ut.get('method') and ut['method'] != '?']
            elif url_targets and url_targets.get('method'):
                impact_methods = [url_targets['method']]
            if not impact_methods and rag_results:
                m = rag_results[0].get('metadata', {}).get('method_name', '')
                if m:
                    impact_methods = [m]

            if impact_methods:
                # URL-driven: run impact analysis on each resolved method
                for imp_idx, top_method in enumerate(impact_methods):
                    if not top_method:
                        continue
                    label = f'[LCA {imp_idx+1}/{len(impact_methods)}]' if len(impact_methods) > 1 else '[LCA]'
                    print(f'  \U0001f50e {label} Running impact analysis on {top_method}()...')
                    result2 = subprocess.run(
                        ['lca', 'impact', '-i', lca_index, top_method, '--depth', '2'],
                        capture_output=True, text=True, cwd=PROJECT_ROOT, timeout=30
                    )
                    impact_out = (result2.stdout or '').strip()
                    if impact_out:
                        lines.append('')
                        lines.append(f'### Impact: `{top_method}()`')
                        lines.append('```')
                        lines.append(impact_out[:2000])
                        lines.append('```')
                        lca_output += impact_out
                        print(f'  \u2705 {label} Impact analysis done')
                        for line in impact_out.split('\n'):
                            if 'module:' in line.lower():
                                mod_name = line.split(':')[-1].strip()
                                lca_modules.add(mod_name)
                    else:
                        print(f'  \u26a0\ufe0f  {label} No impact results')
            else:
                # Fallback: no URL provided — search by module name
                print(f'  \U0001f50e [LCA] No URL methods — falling back to module search *{module}*...')
                result = subprocess.run(
                    ['lca', 'search', '-i', lca_index, f'*{module}*', '--limit', '15'],
                    capture_output=True, text=True, cwd=PROJECT_ROOT, timeout=30
                )
                lca_output = (result.stdout or '').strip()
                if lca_output and 'No functions found' not in lca_output:
                    lines.append('### Module Search Results')
                    lines.append('```')
                    lines.append(lca_output[:2000])
                    lines.append('```')
                    print(f'  \u2705 [LCA] Module search found results')
                else:
                    lines.append('No LCA results found for this module.')
                    print(f'  \u26a0\ufe0f  [LCA] No results for *{module}*')

        except FileNotFoundError:
            lines.append('LCA not installed. Run: `pip install -e logimax-devtools/backend/lca_core`')
            print('  \u26a0\ufe0f  LCA not installed')
        except subprocess.TimeoutExpired:
            lines.append('LCA timed out (30s limit).')
            print(f'  \u26a0\ufe0f  LCA timed out')
        except Exception as e:
            lines.append(f'LCA error: {e}')
            print(f'  \u26a0\ufe0f  LCA error: {e}')

    if not lca_modules:
        lca_modules.add(module)  # At minimum, the task's own module

    lines.append('')

    # ── Section 2: RAG Semantic Search ──
    lines.append('## 2. RAG Semantic Search')
    lines.append('')

    if not rag_enabled:
        lines.append('> RAG disabled in config. Skipped.')
        rag_skipped = True
    else:
        try:
            if rag_thread is not None:
                # Wait for pre-loaded RAG thread (started before LCA section)
                elapsed_lca = '(model was loading during LCA)'
                print(f'  \U0001f50d [RAG] Waiting for pre-loaded results... {elapsed_lca}')
                rag_thread.join(timeout=90)  # Wait at most 90s for RAG
                if rag_thread.is_alive():
                    print('  \u26a0\ufe0f  [RAG] Timed out (>90s) — skipping. Run `python load_chroma.py` to pre-cache.')
                    lines.append('RAG search timed out (>90s). Run `python load_chroma.py` to pre-cache embeddings.')
                    rag_skipped = True
                elif rag_container['error']:
                    print(f'  \u26a0\ufe0f  RAG error: {rag_container["error"]}')
                    lines.append(f'RAG error: {rag_container["error"]}')
                else:
                    rag_results = rag_container['results']
                    # Filter to files that exist in this project
                    valid = []
                    for r in rag_results:
                        fpath = r.get('metadata', {}).get('file_path', '')
                        if os.path.exists(os.path.join(PROJECT_ROOT, fpath)):
                            valid.append(r)
                            rag_unique_files.add(fpath)
                    rag_results = valid[:10]

                    if rag_results:
                        print(f'  ✅ [RAG] {len(rag_results)} results found ({len(rag_unique_files)} unique files)')
                        lines.append(f'> {len(rag_results)} results from ChromaDB')
                        lines.append('')
                        code_snippets_shown = 0
                        for i, r in enumerate(rag_results, 1):
                            meta = r.get('metadata', {})
                            dist = r.get('distance', 0)
                            fpath = meta.get('file_path', '?')
                            method = meta.get('method_name', '')
                            mod = meta.get('module', '?')
                            summary = meta.get('summary', '')[:100]
                            doc_text = r.get('document', '')

                            if dist < 0.30:
                                rel = '\u2b50 STRONG'
                            elif dist < 0.40:
                                rel = '\u2705 good'
                            else:
                                rel = '\u26aa weak'

                            label = f'`{method}()`' if method and method != '?' else ''
                            lines.append(f'- **[{rel} {dist:.3f}]** `{fpath}` {label}')
                            if summary:
                                lines.append(f'  - {summary}')

                            # Include inline code for top 3 code-file results (skip .md, .sql)
                            is_code_file = fpath.endswith('.php') or fpath.endswith('.js')
                            if doc_text and dist < 0.40 and is_code_file and code_snippets_shown < 3:
                                # Extract just the [Code] section if present
                                code_section = doc_text
                                if '[Code]' in doc_text:
                                    code_section = doc_text.split('[Code]', 1)[1].strip()
                                elif 'search_document:' in doc_text:
                                    # Skip metadata lines
                                    raw_lines = doc_text.split('\n')
                                    code_start = 0
                                    for ci, cl in enumerate(raw_lines):
                                        if cl.strip().startswith(('public ', 'private ', 'protected ', 'function ', '/**')):
                                            code_start = ci
                                            break
                                    code_section = '\n'.join(raw_lines[code_start:])

                                doc_lines = code_section.strip().split('\n')[:20]
                                total_lines = len(code_section.strip().split('\n'))
                                ext = 'php' if fpath.endswith('.php') else 'javascript'
                                lines.append(f'  ```{ext}')
                                for dl in doc_lines:
                                    lines.append(f'  {dl}')
                                if total_lines > 20:
                                    lines.append(f'  // ... ({total_lines} lines total)')
                                lines.append(f'  ```')
                                code_snippets_shown += 1
                    else:
                        lines.append('No RAG results found.')
            elif _rag_available():
                rag_results = _rag_search(query=f'{module} {query}', n_results=10)
            else:
                lines.append('RAG store not available.')
                print('  \u26a0\ufe0f  RAG store not available')
        except BaseException as e:
            lines.append(f'RAG unavailable: {e}')
            print(f'  \u26a0\ufe0f  [RAG] Error: {e}')
            rag_skipped = True

    lines.append('')

    # ── Section 3: Recipe Check (v4.2 — Smart Matching) ──
    lines.append('## 3. Recipe Check')
    lines.append('')

    recipe_found = False
    if 'all_tables' not in dir():
        all_tables = set()  # Will be populated later by call tree analysis
    recipes_dir = os.path.join(os.path.dirname(PROJECT_ROOT), 'bug-recipes', 'recipes')
    if not recipes_enabled:
        lines.append('> Recipes disabled in config. Skipped.')
        lines.append('')
    elif os.path.exists(recipes_dir):
        print('  \U0001f4d6 Indexing recipes for smart match...')
        recipe_matches = _match_recipes(recipes_dir, query, module, all_tables)

        if recipe_matches:
            recipe_found = True
            # Group by cluster (same module or similar pattern)
            clusters = {}
            for rm in recipe_matches:
                cluster_key = rm.get('cluster', 'other')
                if cluster_key not in clusters:
                    clusters[cluster_key] = []
                clusters[cluster_key].append(rm)

            total_matches = len(recipe_matches)
            lines.append(f'**{total_matches} recipe match(es) found** ({len(clusters)} cluster(s)):')
            lines.append('')

            for cluster_name, cluster_items in clusters.items():
                if len(clusters) > 1:
                    lines.append(f'#### Cluster: {cluster_name} ({len(cluster_items)} recipes)')
                    lines.append('')

                for rm in cluster_items[:3]:  # Top 3 per cluster
                    score = rm['score']
                    if score >= 5:
                        conf = '\U0001f534 HIGH'
                    elif score >= 3:
                        conf = '\U0001f7e1 MEDIUM'
                    else:
                        conf = '\u26aa LOW'

                    lines.append(f'- **[{conf} score={score}]** `{rm["file"]}`')
                    lines.append(f'  - Title: {rm["title"]}')
                    if rm.get('modules'):
                        lines.append(f'  - Modules: {rm["modules"]}')
                    if rm.get('matched_keywords'):
                        lines.append(f'  - Matched keywords: {", ".join(rm["matched_keywords"][:6])}')

                    # Include recipe content for top HIGH matches
                    if score >= 5:
                        try:
                            full_path = os.path.join(recipes_dir, rm['file'])
                            with open(full_path, 'r', encoding='utf-8', errors='ignore') as rf:
                                content_lines = rf.readlines()[:50]
                            lines.append('  ```markdown')
                            lines.extend(['  ' + l.rstrip() for l in content_lines])
                            lines.append('  ```')
                        except Exception:
                            pass
                    lines.append('')

            # Pattern summary
            if len(recipe_matches) >= 3:
                common_words = _find_common_keywords(recipe_matches)
                if common_words:
                    lines.append(f'> **Common pattern across matches**: {", ".join(common_words[:5])}')
                    lines.append('')
        else:
            lines.append('No recipe match found (searched 190+ recipes).')
    else:
        lines.append(f'Local recipes not found at `{recipes_dir}`')

    lines.append('')

    # ── Section 4 (Brain Context) REMOVED in v3.9.2 ──
    # Brain docs are already indexed in ChromaDB RAG (7,430 chunks).
    # Brain results now surface through RAG search (Section 2).
    brain_context_found = True  # Always true — brain is in RAG

    # ── Section 4: Key Code Snippets (v4.0 — Call Tree First) ──
    lines.append('## 4. Key Code Snippets')
    lines.append('')

    def _read_method_code(filepath, method_name, context_lines=40, keywords=None):
        """Read a method's code from a file. For large functions (>100 lines),
        extracts smart keyword-targeted excerpts instead of dumping everything."""
        if not os.path.exists(filepath):
            return None
        try:
            with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
                file_lines = f.readlines()

            # Find method definition — try exact match first, then prefix
            search_names = [method_name]
            parts = method_name.split('_')
            for i in range(len(parts) - 1, 0, -1):
                prefix = '_'.join(parts[:i])
                if len(prefix) > 3:
                    search_names.append(prefix)

            for name in search_names:
                for i, line in enumerate(file_lines):
                    stripped = line.lstrip()
                    if stripped.startswith('//') or stripped.startswith('*') or stripped.startswith('/*'):
                        continue
                    if f'function {name}(' in line or f'function {name} (' in line:
                        func_start = i
                        brace_depth = 0
                        func_end = min(len(file_lines), i + context_lines)
                        started = False
                        for j in range(i, len(file_lines)):
                            for ch in file_lines[j]:
                                if ch == '{':
                                    brace_depth += 1
                                    started = True
                                elif ch == '}':
                                    brace_depth -= 1
                            if started and brace_depth <= 0:
                                func_end = j + 1
                                break

                        func_length = func_end - func_start
                        actual_name = name if name != method_name else method_name

                        # SHORT FUNCTION (<=100 lines): return full code
                        if func_length <= 100:
                            snippet = ''.join(file_lines[func_start:func_end])
                            return f'Lines {func_start+1}-{func_end}', snippet, actual_name

                        # LARGE FUNCTION (100+ lines): smart keyword excerpts
                        excerpt_lines = []
                        excerpt_lines.append(f'// \u26a0\ufe0f Large function ({func_length} lines) \u2014 showing key excerpts')
                        excerpt_lines.append('')

                        # 1. Signature + first 8 lines
                        sig_end = min(func_start + 8, func_end)
                        excerpt_lines.append('// --- Signature (L{}) ---'.format(func_start + 1))
                        for k in range(func_start, sig_end):
                            excerpt_lines.append(file_lines[k].rstrip())

                        # 2. Keyword matches with \u00b13 context
                        already_shown = set(range(func_start, sig_end))
                        if keywords:
                            kw_lower = [kw.lower() for kw in keywords]
                            matched_ranges = set()
                            for k in range(func_start, func_end):
                                line_lower = file_lines[k].lower()
                                if any(kw in line_lower for kw in kw_lower):
                                    for ctx in range(max(func_start, k-3), min(func_end, k+4)):
                                        matched_ranges.add(ctx)

                            new_kw = matched_ranges - already_shown
                            if new_kw:
                                sorted_matches = sorted(new_kw)
                                groups = [[sorted_matches[0]]]
                                for m_idx in range(1, len(sorted_matches)):
                                    if sorted_matches[m_idx] - sorted_matches[m_idx-1] <= 1:
                                        groups[-1].append(sorted_matches[m_idx])
                                    else:
                                        groups.append([sorted_matches[m_idx]])
                                for group in groups[:5]:
                                    excerpt_lines.append('')
                                    excerpt_lines.append(f'// --- Keyword match (L{group[0]+1}-L{group[-1]+1}) ---')
                                    for k in group:
                                        excerpt_lines.append(file_lines[k].rstrip())
                                already_shown |= new_kw

                        # 3. SQL query lines
                        sql_keywords = ['->query(', 'SELECT ', 'INSERT ', 'UPDATE ', 'DELETE ',
                                       '->where(', '->select(', '->join(', '->from(', '->group_by(']
                        sql_ranges = set()
                        for k in range(func_start + 8, func_end):
                            line_upper = file_lines[k].upper().strip()
                            if any(sq.upper() in line_upper for sq in sql_keywords):
                                for ctx in range(max(func_start, k-2), min(func_end, k+3)):
                                    sql_ranges.add(ctx)
                        new_sql = sql_ranges - already_shown
                        if new_sql:
                            sorted_sql = sorted(new_sql)
                            groups = [[sorted_sql[0]]]
                            for s_idx in range(1, len(sorted_sql)):
                                if sorted_sql[s_idx] - sorted_sql[s_idx-1] <= 1:
                                    groups[-1].append(sorted_sql[s_idx])
                                else:
                                    groups.append([sorted_sql[s_idx]])
                            for group in groups[:4]:
                                excerpt_lines.append('')
                                excerpt_lines.append(f'// --- SQL query (L{group[0]+1}-L{group[-1]+1}) ---')
                                for k in group:
                                    excerpt_lines.append(file_lines[k].rstrip())

                        # 4. Return statement
                        for k in range(func_end - 1, max(func_start, func_end - 10), -1):
                            if 'return ' in file_lines[k]:
                                excerpt_lines.append('')
                                excerpt_lines.append(f'// --- Return (L{k+1}) ---')
                                for r in range(max(func_start, k - 1), min(func_end, k + 2)):
                                    excerpt_lines.append(file_lines[r].rstrip())
                                break

                        snippet = '\n'.join(excerpt_lines)
                        return f'Lines {func_start+1}-{func_end}', snippet, actual_name

            return None
        except Exception:
            return None

    code_snippets_added = False
    lca_call_chain = []  # for quality score
    level2_methods = set()  # for quality score
    level3_methods = set()  # for quality score
    all_tables = set()

    # Extract keywords from query for smart excerpting
    _stop_words = {'the', 'in', 'is', 'are', 'not', 'and', 'or', 'to', 'a', 'an',
                   'of', 'for', 'from', 'with', 'on', 'at', 'by', 'it', 'be',
                   'has', 'have', 'was', 'were', 'been', 'being', 'do', 'does',
                   'did', 'will', 'would', 'should', 'could', 'may', 'might',
                   'shall', 'can', 'this', 'that', 'these', 'those'}
    _query_keywords = [w.lower().strip('.,!?') for w in query.split()
                       if len(w) > 2 and w.lower() not in _stop_words]
    if module and module != 'unknown':
        _query_keywords.extend(module.lower().replace('_', ' ').split())
    _query_keywords = list(dict.fromkeys(_query_keywords))

    # ══════════════════════════════════════════════════════════════════════
    #  v4.0+: Call Tree Strategy — v3.9.1: build for ALL URL targets
    # ══════════════════════════════════════════════════════════════════════
    call_tree_used = False

    # v4.2: Skip call tree if disabled in config
    if not call_tree_enabled:
        lines.append('> Call tree disabled in config. Skipped.')
        lines.append('')
        tree_targets = []
    else:
        # Collect all URL targets to build trees for
        tree_targets = []
        if all_url_targets:
            tree_targets = [(ut['method'], ut.get('controller', '')) for ut in all_url_targets if ut.get('method')]
        elif url_targets and url_targets.get('method'):
            tree_targets = [(url_targets['method'], url_targets.get('controller', ''))]

    for tree_idx, (method_name, controller_raw) in enumerate(tree_targets):
        controller_class = controller_raw.split('/')[-1].replace('.php', '')
        tree_label = f'[TREE {tree_idx+1}/{len(tree_targets)}]' if len(tree_targets) > 1 else '[TREE]'

        print(f'  \U0001f332 {tree_label} Building call tree from {controller_class}::{method_name}()...')
        ct = _build_call_tree(method_name, controller_class=controller_class, max_depth=4)

        if ct and len(ct['tree']) > 1:
            call_tree_used = True
            tree = ct['tree']
            root = ct['root']
            tree_methods = ct['all_methods']
            all_tables = all_tables.union(ct['all_tables']) if all_tables else ct['all_tables']

            print(f'  \u2705 {tree_label} {len(tree)} nodes, {len(ct["all_tables"])} tables')

            # ── Call Tree Diagram ──
            lines.append('### Call Tree (v4.0)')
            lines.append('```')

            def _print_tree_md(tree_data, node, indent=0, visited_print=None):
                if visited_print is None:
                    visited_print = set()
                if node in visited_print:
                    return
                visited_print.add(node)
                info = tree_data.get(node, {})
                prefix = '  ' * indent + ('+-- ' if indent > 0 else '')
                tables_str = f'  [{", ".join(info.get("tables", []))}]' if info.get('tables') else ''
                lines.append(f'{prefix}{info.get("short", node)}{tables_str}')
                for child in info.get('calls', []):
                    # Resolve full name
                    full_child = child
                    for tn in tree_data:
                        if tn.endswith(f'::{child}') or tn == child:
                            full_child = tn
                            break
                    _print_tree_md(tree_data, full_child, indent + 1, visited_print)

            _print_tree_md(tree, root)
            lines.append('```')
            lines.append(f'> {len(tree)} nodes | {len(all_tables)} tables | Strategy: CALL_TREE')
            lines.append('')

            # ── Reverse Callers (regression scope) ──
            if ct['reverse_callers']:
                lines.append('### Reverse Callers (Regression Scope)')
                for method, callers in ct['reverse_callers'].items():
                    if len(callers) > 5:
                        shown = ', '.join(callers[:5])
                        lines.append(f'- `{method}()` \u2190 {shown} (+{len(callers)-5} more)')
                    else:
                        lines.append(f'- `{method}()` \u2190 {", ".join(callers)}')
                lines.append('')

            # ── Formulas ──
            if ct['formulas']:
                lines.append('### Formulas (from LCA)')
                for f in ct['formulas']:
                    prec = f' (precision={f["precision"]})' if f.get('precision') else ''
                    lines.append(f'  - `{f["variable"]}` {f["operator"]} `{f["expression"][:80]}`{prec} \u2014 L{f["line"]}')
                lines.append('')

            # ── Magic Numbers ──
            if ct['magic_numbers']:
                lines.append('### Magic Values (from LCA)')
                for m in ct['magic_numbers']:
                    lines.append(f'  - `{m["value"]}` \u2014 {m["context"][:80]} (L{m["line"]})')
                lines.append('')

            # ── Code Excerpts: read code for each tree node ──
            # Controller first (depth 0), then model methods in BFS order
            max_code_nodes = 7  # controller + 6 model methods
            code_count = 0

            for node_info in tree_methods[:max_code_nodes]:
                fpath = os.path.join(PROJECT_ROOT, node_info['file']) if node_info['file'] else ''
                method = node_info['method']
                depth = node_info['depth']

                if not fpath or not os.path.exists(fpath):
                    continue

                result = _read_method_code(fpath, method, context_lines=50, keywords=_query_keywords)
                if result:
                    line_range, code, actual_name = result
                    basename = os.path.basename(fpath)
                    depth_label = 'Controller' if depth == 0 else f'L{depth}'
                    lines.append(f'### {depth_label}: `{basename}::{actual_name}()` ({line_range})')
                    lines.append('```php')
                    lines.append(code.rstrip())
                    lines.append('```')
                    lines.append('')
                    code_snippets_added = True
                    code_count += 1

                    if depth == 0:
                        # Update call chain tracking for quality score
                        lca_call_chain = [m['method'] for m in tree_methods[1:6]]

            # Populate level2/3 for quality score
            for m in tree_methods:
                if m['depth'] == 2:
                    level2_methods.add(m['method'])
                elif m['depth'] >= 3:
                    level3_methods.add(m['method'])

            print(f'  \u2705 [TREE] {code_count} code excerpts extracted')

    # ══════════════════════════════════════════════════════════════════════
    #  Fallback: RAG-based strategy (when no URL or call tree empty)
    # ══════════════════════════════════════════════════════════════════════
    if not call_tree_used:
        import re
        print('  \U0001f50d [FALLBACK] Using RAG + regex strategy (no URL or empty tree)')
        lines.append('> Strategy: RAG_SEARCH (no URL-based call tree available)')
        lines.append('')

        controller_code = ''
        # Controller method
        if url_targets:
            ctrl_file = os.path.join(PROJECT_ROOT, url_targets.get('controller_file', ''))
            method_name = url_targets.get('method', '')
            if method_name and os.path.exists(ctrl_file):
                result = _read_method_code(ctrl_file, method_name, keywords=_query_keywords)
                if result:
                    line_range, code, actual_name = result
                    controller_code = code
                    lines.append(f'### Controller: `{url_targets["controller"]}::{actual_name}()` ({line_range})')
                    lines.append('```php')
                    lines.append(code.rstrip())
                    lines.append('```')
                    lines.append('')
                    code_snippets_added = True

        # Regex-based call chain from controller code
        if controller_code and url_targets:
            model_call_pattern = re.compile(r'\$this->[\$]?(\w+)->(\w+)\s*\(')
            skip_vars = {'load', 'input', 'session', 'uri', 'db', 'security', 'config', 'output', 'email'}
            skip_model_vars = {'admin_settings_model', 'common_model', 'auth_model', 'access_right_model'}
            model_methods_found = []
            seen_methods = set()
            for match in model_call_pattern.finditer(controller_code):
                model_var = match.group(1)
                method_call = match.group(2)
                if model_var not in skip_vars and method_call not in ('view', 'model', 'library', 'helper'):
                    if method_call not in seen_methods:
                        seen_methods.add(method_call)
                        model_methods_found.append((model_var, method_call))

            model_hints = url_targets.get('model_hints', [])
            for model_var, method_call in model_methods_found[:3]:
                if model_var in skip_model_vars:
                    continue
                files_to_try = [os.path.join(PROJECT_ROOT, h) for h in model_hints]
                if model_var != 'model' and not model_var.startswith('$'):
                    files_to_try.append(os.path.join(PROJECT_ROOT, f'admin/application/models/{model_var}.php'))
                for model_path in files_to_try:
                    if os.path.exists(model_path):
                        result = _read_method_code(model_path, method_call, context_lines=50, keywords=_query_keywords)
                        if result:
                            line_range, code, actual_name = result
                            basename = os.path.basename(model_path)
                            lines.append(f'### Model: `{basename}::{actual_name}()` ({line_range}) [regex]')
                            lines.append('```php')
                            lines.append(code.rstrip())
                            lines.append('```')
                            lines.append('')
                            code_snippets_added = True
                            break

    # ── JS AJAX stub (find the AJAX call — applies to both strategies) ──
    if url_targets and url_targets.get('method'):
        import glob as glob_mod
        js_hints = url_targets.get('js_hints', [])
        ctrl_method = url_targets.get('method', '')
        ctrl_name = url_targets.get('controller', '').split('/')[-1].replace('.php', '')
        js_search_terms = [ctrl_method]
        if not ctrl_method.startswith('get_'):
            js_search_terms.append(f'get_{ctrl_method}')
        if ctrl_name:
            method_parts = ctrl_method.split('_')
            if len(method_parts) > 1:
                js_search_terms.append(f'{ctrl_name}/{ctrl_method}')
                js_search_terms.append(f'function get_{"_".join(method_parts)}')
                js_search_terms.append(f'function {"_".join(method_parts)}')

        js_found = False
        resolved_js = []
        for js_hint in js_hints:
            full = os.path.join(PROJECT_ROOT, js_hint)
            if '*' in js_hint:
                resolved_js.extend(glob_mod.glob(full))
            elif os.path.exists(full):
                resolved_js.append(full)
        for js_path in resolved_js:
            if js_found:
                break
            try:
                with open(js_path, 'r', encoding='utf-8', errors='ignore') as jf:
                    js_lines = jf.readlines()
                for ji, jl in enumerate(js_lines):
                    for term in js_search_terms:
                        if term in jl:
                            search_end = min(len(js_lines), ji + 40)
                            ajax_line = ji
                            for ak in range(ji, search_end):
                                if '$.ajax' in js_lines[ak] or 'ajax(' in js_lines[ak].lower():
                                    ajax_line = ak
                                    break
                            js_start = max(0, ji - 2)
                            js_end = min(len(js_lines), ajax_line + 12)
                            js_code = ''.join(js_lines[js_start:js_end])
                            js_basename = os.path.basename(js_path)
                            lines.append(f'### JS AJAX: `{js_basename}` (Lines {js_start+1}-{js_end})')
                            lines.append('```javascript')
                            lines.append(js_code.rstrip())
                            lines.append('```')
                            lines.append('')
                            code_snippets_added = True
                            js_found = True
                            break
                    if js_found:
                        break
            except Exception:
                pass

    # ── Class constants from model files ──
    if url_targets and url_targets.get('model_hints'):
        for mh in url_targets['model_hints'][:1]:
            model_path = os.path.join(PROJECT_ROOT, mh)
            if os.path.exists(model_path):
                try:
                    with open(model_path, 'r', encoding='utf-8', errors='ignore') as mf:
                        model_lines = mf.readlines()
                    const_lines_out = []
                    for ml in model_lines[:200]:
                        stripped = ml.strip()
                        if (stripped.startswith('const ') or stripped.startswith('public $')
                            or stripped.startswith('private $') or stripped.startswith('protected $')
                            or 'define(' in stripped):
                            const_lines_out.append(stripped)
                    if const_lines_out:
                        lines.append(f'### Class Constants/Properties: `{os.path.basename(model_path)}`')
                        lines.append('```php')
                        for cl in const_lines_out[:15]:
                            lines.append(cl)
                        lines.append('```')
                        lines.append('')
                except Exception:
                    pass

    if not code_snippets_added:
        lines.append('No key code snippets extracted (URL or method not available).')
        lines.append('')

    # ── Section 4.5: Tables in Call Chain ──
    if all_tables:
        lines.append('## 4.5. Tables in Call Chain')
        lines.append('')
        lines.append(f'> {len(all_tables)} tables referenced | For column details: `DESCRIBE table_name` via MCP `mysql-local`')
        lines.append('')
        kw_tables = sorted([t for t in all_tables if any(kw in t.lower() for kw in _query_keywords)])
        other_tables = sorted(all_tables - set(kw_tables))
        for table in kw_tables:
            lines.append(f'- **`{table}`** \u2b50')
        for table in other_tables:
            lines.append(f'- `{table}`')
        lines.append('')


    # ── Section 5: Auto-Classification ──
    suggested_track = _auto_classify_track(
        task.get('type', 'fix'),
        rag_count=len(rag_unique_files),
        lca_modules=len(lca_modules),
        recipe_found=recipe_found,
        code_snippets=code_snippets_added,
    )

    lines.append('## 5. Track Classification')
    lines.append('')
    lines.append(f'- RAG unique files: {len(rag_unique_files)}')
    lines.append(f'- LCA modules: {len(lca_modules)} ({", ".join(lca_modules)})')
    lines.append(f'- Recipe found: {"Yes" if recipe_found else "No"}')
    lines.append(f'- Code snippets: {"Yes" if code_snippets_added else "No"}')
    lines.append(f'- **Suggested track: {suggested_track}**')
    lines.append('')

    # ── Section 6: Suggested Next Actions ──
    lines.append('## 6. Next Actions')
    lines.append('')
    if code_snippets_added:
        lines.append('1. Review code snippets above for root cause')
    elif rag_results:
        top = rag_results[0].get('metadata', {})
        top_file = top.get('file_path', '?')
        top_method = top.get('method_name', '')
        lines.append(f'1. Read top hit: `{top_file}`' + (f' -> `{top_method}()`' if top_method else ''))
    lines.append(f'2. {"Apply recipe variant" if recipe_found else "Investigate root cause"}')
    lines.append(f'3. Form hypothesis and create requirement artifact')
    lines.append('')

    # ── Section 7: Discovery Quality Score ──
    # Score is proportional to AVAILABLE signals — missing infrastructure (no RAG, no recipes)
    # doesn't penalize the score. Only scores what's accessible.
    score = 0
    max_score = 0
    quality_details = []

    # RAG results (max 20 points — only counted if RAG is available)
    rag_available = _rag_available()
    if rag_available:
        max_score += 20
        if rag_results:
            strong_hits = sum(1 for r in rag_results if r.get('distance', 1) < 0.30)
            good_hits = sum(1 for r in rag_results if 0.30 <= r.get('distance', 1) < 0.40)
            rag_score = min(20, strong_hits * 10 + good_hits * 5 + min(len(rag_results), 5) * 2)
            score += rag_score
            quality_details.append(f'RAG: {rag_score}/20 ({strong_hits} strong, {good_hits} good, {len(rag_results)} total)')
        else:
            quality_details.append('RAG: 0/20 (no results)')
    else:
        quality_details.append('RAG: n/a (not configured)')

    # LCA impact analysis (max 15 points)
    max_score += 15
    lca_score = min(15, len(lca_modules) * 5 + (10 if lca_call_chain else 0))
    score += lca_score
    quality_details.append(f'LCA: {lca_score}/15 ({len(lca_modules)} modules, call chain: {"yes" if lca_call_chain else "no"})')

    # Code snippets (max 20 points)
    max_score += 20
    if code_snippets_added:
        score += 20
        quality_details.append('Code snippets: 20/20 (inline)')
    else:
        quality_details.append('Code snippets: 0/20 (none)')

    # L2 methods (max 10 points)
    max_score += 10
    # PHP built-in functions that shouldn't count as "resolved L2 methods"
    php_builtins = {
        'date', 'time', 'strtotime', 'array_merge', 'array_push', 'array_map',
        'array_filter', 'array_values', 'array_keys', 'array_unique', 'array_column',
        'in_array', 'count', 'isset', 'empty', 'unset', 'is_array', 'is_numeric',
        'is_null', 'is_string', 'intval', 'floatval', 'strval', 'round', 'ceil',
        'floor', 'abs', 'max', 'min', 'number_format', 'str_replace', 'substr',
        'strlen', 'strtolower', 'strtoupper', 'trim', 'ltrim', 'rtrim', 'explode',
        'implode', 'json_encode', 'json_decode', 'var_dump', 'print_r', 'echo',
        'die', 'exit', 'header', 'redirect', 'log_message', 'show_error',
        'set_flashdata', 'flashdata', 'post', 'get', 'input',
    }
    l2_count = len([m for m in level2_methods if m not in php_builtins])
    l2_score = min(10, l2_count * 5)
    score += l2_score
    quality_details.append(f'L2 methods: {l2_score}/10 ({l2_count} resolved)')

    # L3 methods (max 10 points)
    max_score += 10
    l3_count = len(level3_methods)
    l3_score = min(10, l3_count * 5)
    score += l3_score
    quality_details.append(f'L3 depth: {l3_score}/10 ({l3_count} methods)')

    # Brain context (max 10 points — brain docs indexed in RAG since v3.9.2)
    max_score += 10
    brain_val = 10  # Brain docs are in ChromaDB RAG (7,430 chunks)
    score += brain_val
    quality_details.append(f'Brain context: {brain_val}/10 (via RAG)')

    # Tables listed (max 5 points)
    max_score += 5
    table_score = min(5, len(all_tables))
    score += table_score
    quality_details.append(f'Tables: {table_score}/5 ({len(all_tables)} found)')

    # Recipe (max 10 points — only counted if recipe repo is configured)
    recipe_repo_exists = os.path.exists(os.path.join(os.path.dirname(PROJECT_ROOT), 'bug-recipes'))
    if recipe_repo_exists or recipe_found:
        max_score += 10
        if recipe_found:
            score += 10
            quality_details.append('Recipe: 10/10 (found)')
        else:
            quality_details.append('Recipe: 0/10 (none)')
    else:
        quality_details.append('Recipe: n/a (no repo)')

    # Calculate percentage
    pct = round(score * 100 / max_score) if max_score > 0 else 0

    # ── Section 8: Investigator Starting Points (v4.2 — Confidence-Ranked) ──
    # Aggregates top results from ALL sources, ranked by confidence
    starting_points = []

    # From RAG: strong hits (distance < 0.30)
    if rag_results:
        for r in rag_results:
            dist = r.get('distance', 1)
            meta = r.get('metadata', {})
            fpath = meta.get('file_path', '?')
            method = meta.get('method_name', '')
            if dist < 0.30:
                starting_points.append({
                    'confidence': 'HIGH',
                    'source': 'RAG',
                    'file': fpath,
                    'method': method,
                    'reason': f'Strong semantic match (distance={dist:.3f})',
                    'sort_key': dist,
                })
            elif dist < 0.40:
                starting_points.append({
                    'confidence': 'MEDIUM',
                    'source': 'RAG',
                    'file': fpath,
                    'method': method,
                    'reason': f'Good semantic match (distance={dist:.3f})',
                    'sort_key': dist + 0.5,
                })

    # From Call Tree: depth 0-1 are HIGH, depth 2 is MEDIUM, depth 3+ is LOW
    if call_tree_used:
        for node_info in tree_methods[:10]:
            depth = node_info['depth']
            method = node_info['method']
            fpath = node_info.get('file', '')
            tables = node_info.get('tables', [])
            if depth <= 1:
                conf = 'HIGH'
                sort_key = 0.1 + depth * 0.05
            elif depth == 2:
                conf = 'MEDIUM'
                sort_key = 0.6
            else:
                conf = 'LOW'
                sort_key = 0.8
            starting_points.append({
                'confidence': conf,
                'source': f'Call Tree (depth {depth})',
                'file': fpath,
                'method': method,
                'reason': f'{"Entry point" if depth == 0 else f"Called by entry point (depth {depth})"}'
                         + (f', touches: {", ".join(tables[:3])}' if tables else ''),
                'sort_key': sort_key,
            })

    # From Recipe: any recipe match is HIGH
    if recipe_found:
        starting_points.append({
            'confidence': 'HIGH',
            'source': 'Recipe',
            'file': 'See Section 3',
            'method': '',
            'reason': 'Matching recipe found — check if this exact fix pattern applies',
            'sort_key': 0.0,
        })

    # Deduplicate by file+method
    seen_sp = set()
    unique_points = []
    for sp in sorted(starting_points, key=lambda x: x['sort_key']):
        key = f'{sp["file"]}::{sp["method"]}'
        if key not in seen_sp:
            seen_sp.add(key)
            unique_points.append(sp)

    # Output
    lines.append('## 8. Investigator Starting Points')
    lines.append('')
    lines.append('> **Read these files IN THIS ORDER.** Results ranked by confidence across all discovery sources.')
    lines.append('')

    if unique_points:
        conf_icons = {'HIGH': '\U0001f534', 'MEDIUM': '\U0001f7e1', 'LOW': '\u26aa'}

        for i, sp in enumerate(unique_points[:10], 1):
            icon = conf_icons.get(sp['confidence'], '\u26aa')
            method_str = f'::{sp["method"]}()' if sp['method'] else ''
            lines.append(f'{i}. {icon} **[{sp["confidence"]}]** `{sp["file"]}{method_str}` — {sp["reason"]}')
            lines.append(f'   _Source: {sp["source"]}_')

        # Summary counts
        high = sum(1 for s in unique_points if s['confidence'] == 'HIGH')
        med = sum(1 for s in unique_points if s['confidence'] == 'MEDIUM')
        low = sum(1 for s in unique_points if s['confidence'] == 'LOW')
        lines.append('')
        lines.append(f'> **{len(unique_points)} starting points**: {high} HIGH, {med} MEDIUM, {low} LOW')
    else:
        lines.append('No high-confidence starting points found. Investigate manually.')
    lines.append('')

    lines.append('## 7. Discovery Quality')
    lines.append('')
    if pct >= 80:
        grade = '\U0001f7e2 HIGH'
    elif pct >= 50:
        grade = '\U0001f7e1 MEDIUM'
    else:
        grade = '\U0001f534 LOW'
    lines.append(f'**Score: {score}/{max_score} ({pct}%) {grade}**')
    lines.append('')
    for d in quality_details:
        lines.append(f'- {d}')
    lines.append('')

    # Write discovery.md with explicit flush
    with open(dest, 'w', encoding='utf-8') as f:
        f.write('\n'.join(lines))
        f.flush()
        os.fsync(f.fileno())

    # Verify file was written (race condition guard)
    if not os.path.exists(dest):
        print(f'  \u274c ERROR: discovery.md write failed — file not found at {dest}')
        return
    fsize = os.path.getsize(dest)
    print(f'  \u2705 discovery.md verified ({fsize} bytes)')

    # Auto-set track if not already set
    if not task.get('track'):
        task['track'] = suggested_track
        task['current_step'] = 1
        save_state(state)
        print(f'  \U0001f3af Track auto-classified: {suggested_track}')

    rag_label = 'skipped (timeout)' if rag_skipped else f'{len(rag_results)} hits'
    print(f'  \U0001f4c4 Saved: .sdlc/active/{task_id}/discovery.md')
    print(f'  RAG: {rag_label} | LCA: {len(lca_modules)} module(s) | Recipe: {"found" if recipe_found else "none"}')
    print(f'  Track: {suggested_track} ({len(_get_track_steps(suggested_track))} steps)')
    if rag_skipped:
        print(f'  \u26a0\ufe0f  RAG was skipped. To fix: run `python load_chroma.py` once to pre-cache the embedding model.')


def cmd_get_prompt(state):
    """v4.1: Get the subagent prompt for the current delegated step.
    Reads the prompt file, fills placeholders, and prints it.
    The orchestrator copies this prompt to spawn a subagent."""
    task = state.get('active_task')
    if not task:
        print('  No active task.')
        return

    step = _get_current_step(task)
    if not step:
        print('  No current step found.')
        return

    if not step.get('delegate'):
        print(f'  Step {step["id"]} ({step["name"]}) is not delegated. Handle it directly.')
        return

    prompt_file = step.get('agent_prompt_file', '')
    if not prompt_file:
        print(f'  Step {step["id"]} has delegate=true but no agent_prompt_file.')
        return

    prompt_path = os.path.join(SDLC_DIR, prompt_file)
    if not os.path.exists(prompt_path):
        print(f'  Prompt file not found: {prompt_path}')
        return

    with open(prompt_path, 'r', encoding='utf-8') as f:
        prompt = f.read()

    # Fill placeholders
    task_id = task.get('id', '?')
    prompt = prompt.replace('{task_id}', task_id)
    prompt = prompt.replace('{module}', task.get('module', '?'))
    prompt = prompt.replace('{summary}', task.get('summary', '?'))
    prompt = prompt.replace('{type}', task.get('type', 'fix'))
    prompt = prompt.replace('{task_dir}', f'.sdlc/active/{task_id}')

    # Environment placeholders from config.json
    cfg = _load_config()
    env = cfg.get('environment', {})
    php_path = _resolve_php_path(env.get('php_path', 'php'))
    prompt = prompt.replace('{php_path}', php_path)
    prompt = prompt.replace('{base_url}', cfg.get('base_url', 'http://localhost/etail_v3/admin/index.php'))

    agent_type = step.get('agent_type', 'self')
    agent_role = step.get('agent_role', step['name'].title())

    print(f'  \U0001f916 Step {step["id"]} ({step["name"]}) — delegate to: {agent_type} subagent')
    print(f'  \U0001f3af Role: {agent_role}')
    print(f'  \U0001f4c4 Prompt file: {prompt_file}')
    print()
    print('--- SUBAGENT PROMPT START ---')
    print(prompt)
    print('--- SUBAGENT PROMPT END ---')


def cmd_escalate(state):
    """Escalate EXPRESS task to STANDARD track."""
    task = state.get('active_task')
    if not task:
        print('  No active task.')
        return

    current_track = task.get('track', '')
    if current_track != 'EXPRESS':
        print(f'  Current track is {current_track}. Only EXPRESS can be escalated.')
        return

    # Map EXPRESS progress to STANDARD
    old_step = task.get('current_step', 1)
    # EXPRESS steps 1-5 (intake, discover, code, verify, commit) map to STANDARD steps 1-9
    step_map = {1: 1, 2: 2, 3: 3, 4: 5, 5: 9, 6: 9}
    new_step = step_map.get(old_step, old_step)

    task['track'] = 'STANDARD'
    task['current_step'] = new_step
    task['escalated_at'] = datetime.now().isoformat()
    task.setdefault('progress', []).append({
        'step': 0,
        'name': 'escalated',
        'phase': task.get('phase', 'REQUIREMENT'),
        'completed_at': datetime.now().isoformat(),
        'note': f'Escalated from EXPRESS (step {old_step}) to STANDARD (step {new_step})',
    })
    save_state(state)

    total = len(_get_track_steps('STANDARD'))
    print(f'  \u26a1 Escalated: EXPRESS \u2192 STANDARD')
    print(f'  Step mapping: EXPRESS #{old_step} \u2192 STANDARD #{new_step} (of {total})')
    print(f'  Run: python .sdlc/engine/cli.py what-next')


def cmd_audit(state, args):
    """Audit a conversation transcript for SDLC pipeline compliance.

    Reads transcript.jsonl and checks:
    - Did the agent read SKILL.md?
    - Did it run `sdlc start` before investigating code?
    - Did it use the `discover` command?
    - Did it use LCA/MCP tools?
    - Did it read source files before starting the pipeline?
    """
    cid = args.conversation_id if hasattr(args, 'conversation_id') else None
    if not cid:
        print('\u274c Please provide --conversation-id')
        return

    brain_dir = os.path.join(
        os.path.expanduser('~'), '.gemini', 'antigravity', 'brain',
        cid, '.system_generated', 'logs'
    )
    transcript_file = os.path.join(brain_dir, 'transcript.jsonl')

    if not os.path.exists(transcript_file):
        print(f'\u274c Cannot find transcript for {cid}')
        print(f'  Expected: {transcript_file}')
        return

    print(f'\U0001f50d Auditing conversation {cid[:12]}...\n')

    has_skill = False
    has_start = False
    has_discover = False
    has_what_next = False
    source_reads_before_start = 0
    lca_calls = 0
    mcp_calls = 0
    total_tool_calls = 0

    with open(transcript_file, 'r', encoding='utf-8') as f:
        for line in f:
            try:
                step = json.loads(line)
                if step.get('type') != 'PLANNER_RESPONSE':
                    continue
                tools = step.get('tool_calls', [])
                for t in tools:
                    name = t.get('name', '') or t.get('function', {}).get('name', '')
                    args_raw = t.get('args', {}) or t.get('function', {}).get('arguments', '{}')
                    if isinstance(args_raw, str):
                        try:
                            args_dict = json.loads(args_raw)
                        except Exception:
                            args_dict = {}
                    else:
                        args_dict = args_raw if isinstance(args_raw, dict) else {}

                    def _strip(v):
                        if isinstance(v, str):
                            v = v.strip()
                            if v.startswith('"') and v.endswith('"'):
                                v = v[1:-1]
                        return v

                    total_tool_calls += 1

                    # Check SKILL.md read
                    if name == 'view_file' and 'SKILL.md' in _strip(args_dict.get('AbsolutePath', '')):
                        has_skill = True

                    # Check sdlc start
                    cmd_line = _strip(args_dict.get('CommandLine', ''))
                    if name == 'run_command':
                        if 'sdlc' in cmd_line and 'start' in cmd_line:
                            has_start = True
                        if 'sdlc' in cmd_line and 'discover' in cmd_line:
                            has_discover = True
                        if 'sdlc' in cmd_line and 'what-next' in cmd_line:
                            has_what_next = True

                    # Check premature source reads (before start)
                    if not has_start and name in ('grep_search', 'view_file'):
                        path = _strip(args_dict.get('SearchPath', '')) or _strip(args_dict.get('AbsolutePath', ''))
                        if path:
                            skip_patterns = ['.sdlc', '.ag', 'config', 'SKILL', 'GEMINI', 'knowledge_brain', '.agent']
                            if not any(sp in path for sp in skip_patterns):
                                if any(ext in path for ext in ['.php', '.js', '.css']):
                                    source_reads_before_start += 1

                    # Check LCA/MCP calls
                    if name == 'call_mcp_tool':
                        mcp_calls += 1
                        tool_name = _strip(args_dict.get('ToolName', '')).lower()
                        if 'lca' in tool_name:
                            lca_calls += 1
            except Exception:
                pass

    # Scoring
    score = 0
    print('### SDLC Pipeline Compliance\n')

    if has_what_next:
        print('\u2705 `what-next` was called (correct entry point)')
        score += 2
    else:
        print('\u274c `what-next` was never called (-2 points)')
        score -= 2

    if has_start:
        print('\u2705 `sdlc start` was called (task registered)')
        score += 2
    else:
        print('\u274c `sdlc start` was never called (-5 points)')
        score -= 5

    if has_discover:
        print('\u2705 `sdlc discover` was called (automated discovery)')
        score += 3
    elif has_start:
        print('\u274c `sdlc discover` was never called (-3 points)')
        score -= 3

    if source_reads_before_start > 0:
        penalty = min(source_reads_before_start, 5)
        print(f'\u274c {source_reads_before_start} source file(s) read before `sdlc start` (-{penalty} points)')
        score -= penalty
    else:
        print('\u2705 No source code investigated before pipeline start')
        score += 1

    if mcp_calls > 0:
        print(f'\u2705 MCP tools used {mcp_calls} times (LCA: {lca_calls})')
    elif has_start:
        print(f'\u26a0\ufe0f  No MCP/LCA tools used (not penalized but unusual)')

    print(f'\n  Total tool calls: {total_tool_calls}')

    # Grade
    grade = 'A'
    if score < 5:
        grade = 'B'
    if score < 2:
        grade = 'C'
    if score < -2:
        grade = 'D'
    if score <= -5:
        grade = 'F'

    print(f'\n**Final Grade: {grade}** (Score: {score})')
    if grade in ('D', 'F'):
        print('Recommendation: Significant drift detected. The agent did not follow the SDLC pipeline.')
    elif grade in ('A', 'B'):
        print('Pipeline was followed correctly.')


def cmd_health(state):
    """Self-diagnostic: check LCA, RAG, pipeline state, and subsystem health."""
    import time

    print('\n  🏥 SDLC Health Check\n')
    issues = 0

    # ── 1. Pipeline State ──
    task = state.get('active_task')
    history = state.get('history', [])
    backlog = state.get('backlog', [])
    print(f'  📋 Pipeline State')
    print(f'     Active task: {task["id"] if task else "None (IDLE)"}')
    print(f'     History: {len(history)} completed')
    print(f'     Backlog: {len(backlog)} queued')

    # Check for stale active task (>24h in same step)
    if task:
        progress = task.get('progress', [])
        if progress:
            last_progress = progress[-1].get('completed_at', '')
            if last_progress:
                try:
                    last_dt = datetime.fromisoformat(last_progress)
                    hours_since = (datetime.now() - last_dt).total_seconds() / 3600
                    if hours_since > 24:
                        print(f'     ⚠️  STALE: No progress in {hours_since:.0f} hours')
                        issues += 1
                    else:
                        print(f'     ✅ Last progress: {hours_since:.1f}h ago')
                except Exception:
                    pass

    # ── 2. LCA Index ──
    print(f'\n  🔍 LCA Index')
    lca_index = os.path.join(PROJECT_ROOT, '.lca', 'index.json')
    lca_legacy = os.path.join(PROJECT_ROOT, 'logimax-devtools', 'backend', 'lca_core', 'lca_index.json')
    lca_path = lca_index if os.path.exists(lca_index) else (lca_legacy if os.path.exists(lca_legacy) else None)

    if lca_path:
        stat = os.stat(lca_path)
        age_hours = (time.time() - stat.st_mtime) / 3600
        size_mb = stat.st_size / (1024 * 1024)
        if age_hours > 168:  # >7 days
            print(f'     ⚠️  STALE: {lca_path}')
            print(f'        Age: {age_hours:.0f}h ({age_hours/24:.0f} days) | Size: {size_mb:.1f}MB')
            print(f'        Run: lca index build admin/application -m all -o .lca/index.json')
            issues += 1
        else:
            print(f'     ✅ {lca_path}')
            print(f'        Age: {age_hours:.0f}h | Size: {size_mb:.1f}MB')

        # Verify LCA CLI works
        try:
            result = subprocess.run(
                ['lca', '--version'], capture_output=True, text=True, timeout=5
            )
            ver = (result.stdout or '').strip()
            print(f'     ✅ LCA CLI: {ver}')
        except FileNotFoundError:
            print(f'     ❌ LCA CLI not installed')
            issues += 1
        except subprocess.TimeoutExpired:
            print(f'     ⚠️  LCA CLI timed out')
            issues += 1
    else:
        print(f'     ❌ No LCA index found')
        print(f'        Run: lca index build admin/application -m all -o .lca/index.json')
        issues += 1

    # ── 3. RAG / ChromaDB ──
    print(f'\n  🧠 RAG Store (ChromaDB)')
    rag_db = os.path.join(RAG_STORE_DIR, 'chroma.sqlite3')
    if os.path.exists(rag_db):
        stat = os.stat(rag_db)
        age_hours = (time.time() - stat.st_mtime) / 3600
        size_mb = stat.st_size / (1024 * 1024)
        if age_hours > 168:
            print(f'     ⚠️  STALE: Last updated {age_hours:.0f}h ago ({age_hours/24:.0f} days)')
            print(f'        Run: python load_chroma.py')
            issues += 1
        else:
            print(f'     ✅ ChromaDB: {size_mb:.1f}MB | Updated {age_hours:.0f}h ago')

        # Try to count collections
        try:
            import sqlite3
            conn = sqlite3.connect(rag_db)
            cursor = conn.execute("SELECT COUNT(*) FROM collections")
            count = cursor.fetchone()[0]
            cursor2 = conn.execute("SELECT COUNT(*) FROM embeddings")
            embed_count = cursor2.fetchone()[0]
            conn.close()
            print(f'     ✅ Collections: {count} | Embeddings: {embed_count}')
        except Exception as e:
            print(f'     ⚠️  Could not query ChromaDB: {e}')
    else:
        print(f'     ❌ ChromaDB not found at {RAG_STORE_DIR}')
        print(f'        Run: python load_chroma.py')
        issues += 1

    # ── 4. Steps Configuration ──
    print(f'\n  ⚙️  Steps Configuration')
    try:
        steps_data = json.load(open(STEPS_FILE, encoding='utf-8'))
        for track_name, track_data in steps_data.get('tracks', {}).items():
            steps = track_data.get('steps', [])
            delegated = sum(1 for s in steps if s.get('delegate'))
            print(f'     \u2705 {track_name}: {len(steps)} steps ({delegated} delegated)')
    except Exception as e:
        print(f'     ❌ steps.json error: {e}')
        issues += 1

    # ── 5. Prompts (for orchestrator) ──
    print(f'\n  📝 Orchestrator Prompts')
    prompts_dir = os.path.join(SDLC_DIR, 'prompts')
    if os.path.exists(prompts_dir):
        prompt_files = [f for f in os.listdir(prompts_dir) if f.endswith('.md')]
        print(f'     ✅ {len(prompt_files)} prompt files: {", ".join(prompt_files)}')
    else:
        print(f'     ⚠️  No prompts/ directory — orchestrator pattern unavailable')
        issues += 1

    # ── 6. Knowledge Brain ──
    print(f'\n  📚 Knowledge Brain')
    brain_dir = os.path.join(PROJECT_ROOT, 'knowledge_brain')
    if os.path.exists(brain_dir):
        modules = [d for d in os.listdir(brain_dir) if os.path.isdir(os.path.join(brain_dir, d))]
        print(f'     ✅ {len(modules)} module brains: {", ".join(modules[:8])}{"..." if len(modules) > 8 else ""}')
    else:
        print(f'     ⚠️  No knowledge_brain/ directory')
        issues += 1

    # ── 7. Discovery Sources Config ──
    disc_config = _load_config().get('discovery_sources', {})
    print(f'\n  \u2699\ufe0f  Discovery Sources')
    for src_name in ['lca', 'rag', 'recipes', 'call_tree', 'knowledge_brain']:
        enabled = disc_config.get(src_name, True)
        icon = '\u2705' if enabled else '\u274c'
        print(f'     {icon} {src_name}: {"enabled" if enabled else "disabled"}')

    # ── Summary ──
    print(f'\n  {"=" * 45}')
    if issues == 0:
        print(f'  \u2705 All systems healthy')
    elif issues <= 2:
        print(f'  \u26a0\ufe0f  {issues} issue(s) found \u2014 pipeline functional but degraded')
    else:
        print(f'  \u274c {issues} issues found \u2014 fix before starting a task')
    print()


def cmd_enrich(state):
    """Auto-generate recipe from completed task artifacts.

    Reads: context.md, requirement_review.md, requirement.md, review.md, test_cases.md, git diff
    Outputs: recipe/{id}.md (local) + prints push instructions
    """
    task = state.get('active_task')
    if not task:
        print('  No active task.')
        return

    task_id = task['id']
    td = task_dir(task_id)
    module = task.get('module', 'unknown')
    task_type = task.get('type', 'fix')
    summary = task.get('summary', '')
    now = datetime.now().strftime('%Y-%m-%d')

    # ── Read all available artifacts ──
    def _read_artifact(name):
        path = os.path.join(td, name)
        if os.path.exists(path):
            with open(path, 'r', encoding='utf-8') as f:
                return f.read()
        return ''

    requirement = _read_artifact('requirement.md')
    context = _read_artifact('context.md')
    fix_plan = _read_artifact('requirement_review.md')
    review = _read_artifact('review.md')
    test_cases = _read_artifact('test_cases.md')

    # ── Extract structured data from context.md ──
    hypothesis = ''
    root_cause_code = ''
    existing_behavior = ''
    data_flow = ''

    if context:
        for section_raw in context.split('##'):
            section = section_raw.strip()
            lower = section.lower()
            if lower.startswith('hypothesis') or lower.startswith('approach'):
                # First non-empty line after header
                section_lines = [l.strip() for l in section.split('\n')[1:] if l.strip()]
                hypothesis = section_lines[0] if section_lines else ''
            elif 'root cause code' in lower or 'root cause' in lower:
                root_cause_code = section[:500]
            elif 'existing behavior' in lower:
                existing_behavior = section[:300]
            elif 'data flow' in lower:
                data_flow = section[:300]

    # ── Extract fix approach from requirement_review.md ──
    fix_approach = ''
    files_changed = []
    if fix_plan:
        for section_raw in fix_plan.split('##'):
            section = section_raw.strip()
            lower = section.lower()
            if lower.startswith('fix plan') or lower.startswith('proposed changes'):
                fix_approach = section[:800]
            elif lower.startswith('file') or 'file' in lower[:20]:
                # Try to extract file paths
                import re
                paths = re.findall(r'`(admin/application/\S+\.php)`', section)
                files_changed.extend(paths)

    # ── Get git diff for the fix ──
    diff_stat = ''
    diff_detail = ''
    try:
        result = subprocess.run(
            ['git', 'diff', '--stat', 'HEAD~1'],
            capture_output=True, text=True, cwd=PROJECT_ROOT, timeout=10
        )
        diff_stat = result.stdout.strip()
    except Exception:
        diff_stat = '(unable to get git diff)'

    try:
        result = subprocess.run(
            ['git', 'diff', '--no-color', 'HEAD~1'],
            capture_output=True, text=True, cwd=PROJECT_ROOT, timeout=10
        )
        # Truncate to first 2000 chars to keep recipe manageable
        diff_detail = result.stdout.strip()[:2000]
    except Exception:
        diff_detail = ''

    # ── Extract file list from diff if not found in plan ──
    if not files_changed and diff_stat:
        import re
        files_changed = re.findall(r'^\s*(\S+\.php)', diff_stat, re.MULTILINE)

    # ── Generate keywords for fingerprinting ──
    import re
    all_text = f'{summary} {hypothesis} {module}'
    words = re.findall(r'[a-zA-Z_]{4,}', all_text)
    stopwords = {'this', 'that', 'with', 'from', 'have', 'been', 'does', 'will',
                 'should', 'would', 'could', 'because', 'already', 'there',
                 'which', 'where', 'when', 'what', 'line', 'file', 'code',
                 'function', 'method', 'return', 'value', 'data', 'caused'}
    keywords = list(set(w.lower() for w in words if w.lower() not in stopwords))[:12]

    # ── Build the recipe ──
    recipe_id = f'{module}-{task_id}'.lower()
    recipe_lines = [
        f'# Recipe: {recipe_id}',
        '',
        f'**Module**: {module}',
        f'**Type**: {task_type}',
        f'**Symptom**: {summary}',
        f'**Created**: {now}',
        f'**Source Task**: {task_id}',
        '',
        '## Root Cause',
        '',
        hypothesis if hypothesis else f'<!-- Hypothesis not found in context.md -->',
        '',
    ]

    if root_cause_code:
        recipe_lines.extend([
            '### Code Evidence',
            '',
            root_cause_code,
            '',
        ])

    if data_flow:
        recipe_lines.extend([
            '### Data Flow',
            '',
            data_flow,
            '',
        ])

    recipe_lines.extend([
        '## Fix',
        '',
    ])

    if fix_approach:
        recipe_lines.extend([
            fix_approach,
            '',
        ])

    recipe_lines.extend([
        '### Files Changed',
        '',
    ])
    if files_changed:
        for f_path in files_changed:
            recipe_lines.append(f'- `{f_path}`')
    else:
        recipe_lines.append('<!-- No files detected -->')
    recipe_lines.append('')

    if diff_stat:
        recipe_lines.extend([
            '### Diff Summary',
            '```',
            diff_stat,
            '```',
            '',
        ])

    if diff_detail:
        recipe_lines.extend([
            '### Diff Detail',
            '```diff',
            diff_detail,
            '```',
            '',
        ])

    # ── Verification section ──
    recipe_lines.extend([
        '## Verification',
        '',
    ])
    if test_cases:
        # Extract test case summaries
        tc_lines = [l for l in test_cases.split('\n')
                    if l.strip().startswith('- ') or l.strip().startswith('TC-')][:10]
        recipe_lines.extend(tc_lines)
    else:
        recipe_lines.append('<!-- No test cases found -->')
    recipe_lines.append('')

    # ── Anti-pattern section (what NOT to do) ──
    if existing_behavior:
        recipe_lines.extend([
            '## Anti-Pattern (what NOT to do)',
            '',
            existing_behavior,
            '',
        ])

    if review:
        # Extract any issues found during review
        review_issues = [l for l in review.split('\n')
                         if l.strip().startswith('### CRITICAL') or
                            l.strip().startswith('### WARNING') or
                            l.strip().startswith('**Why this is')][:5]
        if review_issues:
            recipe_lines.extend([
                '## Review Notes',
                '',
            ])
            recipe_lines.extend(review_issues)
            recipe_lines.append('')

    # ── Fingerprint for discovery matching ──
    recipe_lines.extend([
        '## Fingerprint',
        '',
        f'- Module: `{module}`',
        f'- Type: `{task_type}`',
        f'- Keywords: `{", ".join(keywords)}`',
        f'- Files: `{", ".join(files_changed[:5])}`' if files_changed else '- Files: none',
        '',
        '## Cross-References',
        '',
        f'- Task: {task_id}',
        f'- Branch: {task.get("branch", "unknown")}',
        '',
    ])

    # ── Save recipe locally ──
    recipe_dir = os.path.join(td, 'recipe')
    os.makedirs(recipe_dir, exist_ok=True)
    recipe_path = os.path.join(recipe_dir, f'{recipe_id}.md')
    with open(recipe_path, 'w', encoding='utf-8') as f:
        f.write('\n'.join(recipe_lines))

    # ── Also save as JSON for programmatic matching ──
    recipe_json = {
        'id': recipe_id,
        'module': module,
        'type': task_type,
        'symptom': summary,
        'hypothesis': hypothesis,
        'keywords': keywords,
        'files_changed': files_changed,
        'task_id': task_id,
        'created': now,
    }
    json_path = os.path.join(recipe_dir, f'{recipe_id}.json')
    with open(json_path, 'w', encoding='utf-8') as f:
        json.dump(recipe_json, f, indent=2, ensure_ascii=False)

    artifact_count = sum(1 for x in [context, fix_plan, review, test_cases] if x)
    print(f'  \U0001f4dd Recipe generated from {artifact_count} artifacts:')
    print(f'     Markdown: .sdlc/active/{task_id}/recipe/{recipe_id}.md')
    print(f'     JSON:     .sdlc/active/{task_id}/recipe/{recipe_id}.json')
    print(f'     Keywords: {", ".join(keywords[:6])}')
    print(f'     Files:    {len(files_changed)} changed')
    print()
    print(f'  To push to central repo (orchestrator should do this automatically):')
    print(f'    call_mcp_tool github create_or_update_file')
    print(f'      owner: LOGIMAX-CLIENTS')
    print(f'      repo: bug-recipes')
    print(f'      path: recipes/{module}/{recipe_id}.md')

    # ── Regression Scan: find the same bug pattern elsewhere ──
    regression_results = _scan_regression(task, diff_detail, files_changed, hypothesis)
    if regression_results:
        # Append to recipe
        recipe_lines_extra = [
            '',
            '## Regression Scan',
            '',
            f'> {len(regression_results)} other location(s) may have the same bug pattern.',
            '',
        ]
        for rr in regression_results:
            recipe_lines_extra.append(f'- `{rr["file"]}` L{rr["line"]}: {rr["snippet"][:80]}')
        recipe_lines_extra.append('')

        # Re-write recipe with regression section
        with open(recipe_path, 'a', encoding='utf-8') as f:
            f.write('\n'.join(recipe_lines_extra))

        # Save regression report standalone
        reg_path = os.path.join(td, 'regression_scan.md')
        with open(reg_path, 'w', encoding='utf-8') as f:
            f.write(f'# Regression Scan: {task_id}\n\n')
            f.write(f'> Same bug pattern found in {len(regression_results)} other location(s).\n')
            f.write(f'> Fix these before they become separate bug reports.\n\n')
            for rr in regression_results:
                f.write(f'### `{rr["file"]}` Line {rr["line"]}\n')
                f.write(f'**Pattern**: {rr["pattern"]}\n')
                f.write(f'```php\n{rr["snippet"]}\n```\n\n')
            f.write(f'## Auto-Queue\n\n')
            f.write(f'These can be auto-queued as backlog tasks:\n')
            f.write(f'```\n')
            for rr in regression_results:
                basename = os.path.basename(rr['file']).replace('.php', '')
                f.write(f'sdlc queue -t fix -m {basename} -s "Same pattern as {task_id}: {rr["pattern"][:50]}"\n')
            f.write(f'```\n')

        print()
        print(f'  \U0001f50d Regression scan: {len(regression_results)} similar pattern(s) found')
        print(f'     Report: .sdlc/active/{task_id}/regression_scan.md')
        for rr in regression_results[:3]:
            print(f'     - {rr["file"]}:L{rr["line"]}')
        if len(regression_results) > 3:
            print(f'     ... and {len(regression_results) - 3} more')
    else:
        print()
        print(f'  \u2705 Regression scan: no similar patterns found in other files')


def _scan_regression(task, diff_detail, files_changed, hypothesis):
    """Scan codebase for the same bug pattern that was just fixed.
    Returns list of {file, line, snippet, pattern} dicts.
    """
    import re

    if not diff_detail:
        return []

    results = []
    seen = set()

    # Strategy 1: Extract buggy lines from diff (lines starting with -)
    # and search for them in other files
    buggy_lines = []
    for line in diff_detail.split('\n'):
        if line.startswith('-') and not line.startswith('---'):
            clean = line[1:].strip()
            # Skip trivial lines
            if len(clean) < 20 or clean.startswith('//') or clean.startswith('*'):
                continue
            buggy_lines.append(clean)

    # Strategy 2: Extract table names and missing WHERE patterns
    # e.g., if fix added ->where('is_deleted', 0), find queries on same table WITHOUT it
    added_wheres = []
    for line in diff_detail.split('\n'):
        if line.startswith('+') and not line.startswith('+++'):
            # Match ->where('field', value) additions
            where_match = re.findall(r"->where\(['\"](\w+)['\"]", line)
            for w in where_match:
                added_wheres.append(w)

    # Strategy 3: Extract function names that were fixed
    fixed_functions = []
    current_func = None
    for line in diff_detail.split('\n'):
        func_match = re.match(r'.*function\s+(\w+)\s*\(', line)
        if func_match:
            current_func = func_match.group(1)
        if current_func and (line.startswith('+') or line.startswith('-')):
            if current_func not in fixed_functions:
                fixed_functions.append(current_func)

    # Now search the codebase
    search_dir = os.path.join(PROJECT_ROOT, 'admin', 'application')
    if not os.path.isdir(search_dir):
        return []

    # Search for buggy patterns (top 3 most specific lines)
    # Sort by length (longer = more specific = fewer false positives)
    buggy_lines.sort(key=len, reverse=True)

    for pattern in buggy_lines[:3]:
        # Clean for grep: escape special chars, extract the core pattern
        # Skip if too generic
        if any(skip in pattern.lower() for skip in ['return', 'echo', 'print', '{', '}', 'if (']):
            continue

        # Extract a searchable substring (20-60 chars)
        search_term = pattern.strip()
        if len(search_term) > 60:
            search_term = search_term[:60]

        try:
            result = subprocess.run(
                ['grep', '-rnI', '--include=*.php', search_term, search_dir],
                capture_output=True, text=True, timeout=10, cwd=PROJECT_ROOT
            )
            for match_line in result.stdout.strip().split('\n'):
                if not match_line.strip():
                    continue
                parts = match_line.split(':', 2)
                if len(parts) < 3:
                    continue
                fpath = parts[0]
                line_no = parts[1]
                snippet = parts[2].strip()

                # Skip if it's in one of the files we just fixed
                rel_path = os.path.relpath(fpath, PROJECT_ROOT)
                if any(rel_path.endswith(fc) or fc.endswith(rel_path) for fc in files_changed):
                    continue

                key = f'{fpath}:{line_no}'
                if key not in seen:
                    seen.add(key)
                    results.append({
                        'file': rel_path,
                        'line': line_no,
                        'snippet': snippet[:200],
                        'pattern': f'Same code as fixed bug: {search_term[:50]}',
                    })
        except Exception:
            pass

    # Search for missing WHERE clauses on same tables
    for where_field in added_wheres[:3]:
        # Find queries that DON'T have this where clause
        # This is harder — we search for table references without the field
        pass  # TODO: implement table-specific WHERE gap detection

    return results[:10]  # Cap at 10 matches


# ── RAG Search Helper ──

RAG_STORE_DIR = os.path.join(os.path.expanduser('~'), '.logimax', 'rag_store')

def _rag_available():
    """Check if ChromaDB RAG store is available and usable.
    
    Returns True only if RAG can actually run without freezing:
    1. ChromaDB store exists on disk
    2. Either RAG server is running (no torch needed) OR
       system has enough free memory for torch (~2GB)
    """
    if not os.path.exists(os.path.join(RAG_STORE_DIR, 'chroma.sqlite3')):
        return False
    
    # Strategy 1: Check if RAG server is running (lightweight, no torch needed)
    try:
        import urllib.request
        req = urllib.request.Request('http://127.0.0.1:9876/health')
        resp = urllib.request.urlopen(req, timeout=2)
        if resp.status == 200:
            return True  # Server running — no torch needed
    except Exception:
        pass  # Server not running — need torch for local RAG
    
    # Strategy 2: Check if system has enough memory for torch (~2GB minimum)
    try:
        import ctypes
        kernel32 = ctypes.windll.kernel32
        
        class MEMORYSTATUSEX(ctypes.Structure):
            _fields_ = [
                ('dwLength', ctypes.c_ulong),
                ('dwMemoryLoad', ctypes.c_ulong),
                ('ullTotalPhys', ctypes.c_ulonglong),
                ('ullAvailPhys', ctypes.c_ulonglong),
                ('ullTotalPageFile', ctypes.c_ulonglong),
                ('ullAvailPageFile', ctypes.c_ulonglong),
                ('ullTotalVirtual', ctypes.c_ulonglong),
                ('ullAvailVirtual', ctypes.c_ulonglong),
                ('ullAvailExtendedVirtual', ctypes.c_ulonglong),
            ]
        
        mem = MEMORYSTATUSEX()
        mem.dwLength = ctypes.sizeof(MEMORYSTATUSEX)
        kernel32.GlobalMemoryStatusEx(ctypes.byref(mem))
        
        avail_mb = mem.ullAvailPageFile / (1024 * 1024)
        if avail_mb < 4096:  # Less than 4GB available (torch+transformers+BGE-M3 needs ~3-4GB)
            print(f'  ⚠️  [RAG] Skipping — insufficient memory ({avail_mb:.0f}MB available, 4096MB needed for torch)')
            return False
    except Exception:
        pass  # Non-Windows or ctypes error — proceed optimistically
    
    return True


def _rag_search(query, n_results=10, module_filter=None):
    """Search ChromaDB RAG store. Multi-backend with graceful fallback.
    
    Strategy chain:
      1. RAG server (warm model, ~200ms) — if running on port 9876
      2. LCA semantic.store (auto-detect backend: gemini > bge > local)
      3. `lca semantic` CLI subprocess (last resort)
    """
    if not _rag_available():
        return []

    # Strategy 1: Try RAG server (warm model, ~200ms)
    try:
        import urllib.request
        payload = json_mod.dumps({
            'query': query,
            'n_results': n_results,
            'where_filter': {'module': module_filter} if module_filter else None,
        }).encode()
        req = urllib.request.Request(
            'http://127.0.0.1:9876/query',
            data=payload,
            headers={'Content-Type': 'application/json'},
        )
        resp = urllib.request.urlopen(req, timeout=5)
        data = json_mod.loads(resp.read())
        if data.get('results'):
            return data['results']
    except Exception:
        pass  # Server not running — fall through

    # Strategy 2: LCA semantic.store (auto-detect: gemini > bge > local)
    try:
        project_root = os.path.dirname(SDLC_DIR)
        lca_path = os.path.join(project_root, 'logimax-devtools', 'backend', 'lca_core')
        if lca_path not in sys.path:
            sys.path.insert(0, lca_path)

        # Guard import — PyTorch/Transformers can throw OSError (WinError 1455)
        # or other fatal errors during DLL loading. Catch at import level.
        try:
            from lca.semantic.store import query_chunks
        except (OSError, ImportError, RuntimeError) as import_err:
            print(f'  ⚠️  RAG model import failed: {import_err}')
            return []

        where_filter = None
        if module_filter:
            where_filter = {'module': module_filter}

        # Auto-detect backend (gemini if API key available, else bge)
        # Empty string = auto-detect in store.py
        results = query_chunks(
            query=query,
            persist_dir=RAG_STORE_DIR,
            n_results=n_results,
            where_filter=where_filter,
            backend='',  # Auto-detect: gemini > bge > local
        )
        return results
    except (OSError, RuntimeError) as e:
        # OSError: WinError 1455 (paging file too small), DLL load failures
        # RuntimeError: CUDA/torch initialization errors
        print(f'  ⚠️  RAG runtime error (torch/transformers): {e}')
        return []
    except Exception as e:
        err = str(e)
        # If it's a model loading error (sentence_transformers/transformers compat),
        # fall through to CLI strategy
        if 'Unrecognized processing class' in err or 'SentenceTransformer' in err:
            print(f'  ⚠️  RAG model error — trying CLI fallback...')
        else:
            print(f'  RAG search error: {e}')
            return []

    # Strategy 3: CLI fallback — `lca semantic` subprocess
    try:
        import subprocess as sp
        project_root = os.path.dirname(SDLC_DIR)
        cmd = ['lca', 'semantic', query, '-n', str(n_results)]
        if module_filter:
            cmd.extend(['-m', module_filter])
        result = sp.run(cmd, capture_output=True, text=True, cwd=project_root, timeout=90,
                       env={**os.environ, 'PYTHONIOENCODING': 'utf-8'})
        if result.returncode == 0 and result.stdout.strip():
            # Parse CLI output into result dicts
            cli_results = []
            for line in result.stdout.strip().split('\n'):
                if line.strip() and not line.startswith(' '):
                    cli_results.append({
                        'metadata': {'file_path': line.strip(), 'method_name': '', 'module': ''},
                        'distance': 0.35,
                        'document': '',
                    })
            return cli_results[:n_results]
    except Exception as e:
        print(f'  RAG CLI fallback failed: {e}')

    return []



def cmd_search(state, args):
    """Tiered search: RAG (ChromaDB) → Brain docs → grep fallback."""
    import subprocess

    query = args.query
    n = getattr(args, 'n', 10)
    module = getattr(args, 'module', None)
    project_root = os.getcwd()

    print(f'\n  Searching: "{query}"')
    if module:
        print(f'  Filter: module={module}')

    # ── Tier 1: RAG (ChromaDB semantic search) ──
    if _rag_available():
        print(f'  \U0001f50d Tier 1: RAG (ChromaDB)')
        results = _rag_search(query, n_results=n * 2, module_filter=module)  # fetch extra to compensate for filtering
        # Filter: only keep results whose files exist in this project
        valid_results = []
        for r in results:
            fpath = r.get('metadata', {}).get('file_path', '')
            if os.path.exists(os.path.join(project_root, fpath)):
                valid_results.append(r)
        results = valid_results[:n]
        if results:
            print()
            for i, r in enumerate(results):
                meta = r.get('metadata', {})
                dist = r.get('distance', 0)
                fpath = meta.get('file_path', '?')
                chunk_type = meta.get('chunk_type', '?')
                method = meta.get('method_name', '')
                mod = meta.get('module', '?')
                summary = meta.get('summary', '')[:80]

                if dist < 0.30:
                    rel = '\u2b50'
                elif dist < 0.40:
                    rel = '\u2705'
                else:
                    rel = '\u26aa'

                label = f'{method}()' if method and method != '?' else chunk_type
                print(f'  {rel} {i+1}. [{dist:.3f}] {fpath}')
                print(f'       {mod} | {label}')
                if summary:
                    print(f'       {summary}')
                print()
            print(f'  {len(results)} results ({n} requested)')
            return

    # ── Tier 2: Knowledge Brain docs ──
    brain_dir = os.path.join(project_root, 'knowledge_brain')
    if os.path.exists(brain_dir):
        print(f'  \U0001f4da Tier 2: Knowledge Brain')
        search_dirs = []
        if module:
            mod_brain = os.path.join(brain_dir, module)
            if os.path.exists(mod_brain):
                search_dirs.append(mod_brain)
        # Always include _SYSTEM
        sys_brain = os.path.join(brain_dir, '_SYSTEM')
        if os.path.exists(sys_brain):
            search_dirs.append(sys_brain)
        # If no module filter, search all brain dirs
        if not search_dirs:
            search_dirs = [brain_dir]

        brain_results = []
        for sd in search_dirs:
            try:
                result = subprocess.run(
                    ['grep', '-rnI', '--include=*.md', '-l', query, sd],
                    capture_output=True, text=True, cwd=project_root
                )
                for line in result.stdout.strip().split('\n'):
                    if line.strip():
                        brain_results.append(line.strip())
            except Exception:
                pass

        if brain_results:
            print()
            for i, fpath in enumerate(brain_results[:n], 1):
                # Make path relative
                rel_path = os.path.relpath(fpath, project_root) if os.path.isabs(fpath) else fpath
                print(f'  \U0001f4d6 {i}. {rel_path}')
            print(f'\n  {len(brain_results)} brain docs matched')
            return

    # ── Tier 3: grep source code ──
    print(f'  \U0001f50e Tier 3: Source grep')
    try:
        # Read config for source paths
        config_path = os.path.join(project_root, '.sdlc', 'config.json')
        grep_dirs = []
        if os.path.exists(config_path):
            with open(config_path, 'r', encoding='utf-8') as f:
                config = json.load(f)
                paths = config.get('paths', {})
                for key in ['controllers', 'models', 'views', 'js', 'helpers']:
                    p = paths.get(key)
                    if p and os.path.exists(os.path.join(project_root, p)):
                        grep_dirs.append(p)
        if not grep_dirs:
            grep_dirs = ['.']

        grep_results = []
        for gd in grep_dirs:
            try:
                result = subprocess.run(
                    ['grep', '-rnI', '--include=*.php', '--include=*.js', '-l', query, gd],
                    capture_output=True, text=True, cwd=project_root
                )
                for line in result.stdout.strip().split('\n'):
                    if line.strip() and line.strip() not in grep_results:
                        grep_results.append(line.strip())
            except Exception:
                pass

        if grep_results:
            print()
            for i, fpath in enumerate(grep_results[:n], 1):
                print(f'  \U0001f4c4 {i}. {fpath}')
            total = len(grep_results)
            shown = min(total, n)
            print(f'\n  {shown}/{total} source files matched')
        else:
            print(f'\n  No results found for "{query}"')

    except Exception as e:
        print(f'  Grep error: {e}')


def cmd_context(state):
    """Generate context.md for the active task from available data sources."""
    task = state.get('active_task')
    if not task:
        print('  No active task.')
        return

    dest = os.path.join(task_dir(task['id']), 'context.md')
    module = task.get('module', 'unknown')
    project_root = os.path.dirname(SDLC_DIR)
    brain_dir = os.path.join(project_root, 'knowledge_brain')

    lines = [
        f'# Context: {task["id"]}',
        '',
        f'> Auto-generated for: {task["summary"]}',
        f'> Module: {module}',
        f'> Generated: {datetime.now().isoformat()[:19]}',
        '',
        '## Knowledge Brain',
        '',
    ]

    # Check for module brain
    module_brain = os.path.join(brain_dir, module, 'MODULE_BRAIN.md')
    if os.path.exists(module_brain):
        lines.append(f'- Module Brain: `knowledge_brain/{module}/MODULE_BRAIN.md` \u2705')
    else:
        lines.append(f'- Module Brain: not found for `{module}` \u26a0\ufe0f')

    # Check system brain docs
    system_dir = os.path.join(brain_dir, '_SYSTEM')
    system_docs = ['DANGER_ZONES.md', 'DIAGNOSTIC_PLAYBOOK.md',
                   'SHARED_TABLES.md', 'TAG_STATUS_MAP.md']
    lines.append('')
    lines.append('## System Brain')
    lines.append('')
    for doc in system_docs:
        path = os.path.join(system_dir, doc)
        status = '\u2705' if os.path.exists(path) else '\u274c'
        lines.append(f'- `_SYSTEM/{doc}` {status}')

    # ── RAG Semantic Search ──
    rag_results = []
    if _rag_available():
        print('  Querying RAG store...')
        # Search with task summary
        rag_results = _rag_search(
            query=f'{module} {task["summary"]}',
            n_results=10,
        )

    lines.append('')
    lines.append('## Semantic Search Results')
    lines.append('')

    if rag_results:
        lines.append(f'> {len(rag_results)} results from ChromaDB ({RAG_STORE_DIR})')
        lines.append('')

        # Group by type
        by_type = {}
        for r in rag_results:
            ct = r.get('metadata', {}).get('chunk_type', 'unknown')
            if ct not in by_type:
                by_type[ct] = []
            by_type[ct].append(r)

        for chunk_type, items in by_type.items():
            lines.append(f'### {chunk_type.replace("_", " ").title()}')
            lines.append('')
            for r in items:
                meta = r.get('metadata', {})
                dist = r.get('distance', 0)
                fpath = meta.get('file_path', '?')
                method = meta.get('method_name', '')
                summary = meta.get('summary', '')
                rel = 'STRONG' if dist < 0.30 else 'good' if dist < 0.40 else 'weak'
                label = f'`{method}()`' if method and method != '?' else ''
                lines.append(f'- **[{rel} {dist:.3f}]** `{fpath}` {label}')
                if summary:
                    lines.append(f'  - {summary[:120]}')
            lines.append('')
    else:
        if _rag_available():
            lines.append('No semantic matches found.')
        else:
            lines.append('RAG store not available. Run `python load_chroma.py` to build.')

    # LCA commands to run
    lines.extend([
        '',
        '## LCA Commands (run these)',
        '',
        '```bash',
        f'lca impact . {{function_name}}        # Dependency tree',
        f'lca sql-map . -t {{table_name}}       # DB dependencies',
        f'lca crossref .                       # JS<->PHP chains',
        f'lca search . "{{keyword}}"            # Find related functions',
        '```',
        '',
        '## Recipe Search',
        '',
        '- [ ] Searched `bug-recipes/recipes/` for matching fix',
        '- [ ] Searched `COMMON_BUG_PATTERNS.md` for known pattern',
        '',
    ])

    os.makedirs(task_dir(task['id']), exist_ok=True)
    with open(dest, 'w', encoding='utf-8') as f:
        f.write('\n'.join(lines))

    print(f'  Generated: .sdlc/active/{task["id"]}/context.md')
    print(f'  Brain: {module} module brain {"found" if os.path.exists(module_brain) else "NOT FOUND"}')
    if rag_results:
        print(f'  RAG: {len(rag_results)} semantic matches included')
    elif not _rag_available():
        print(f'  RAG: store not available (optional)')


def cmd_review(state):
    """Auto-generate review.md with git diff, changed files, and acceptance criteria."""
    task = state.get('active_task')
    if not task:
        print('  No active task.')
        return

    task_id = task['id']
    task_path = task_dir(task_id)
    now = datetime.now().strftime('%Y-%m-%d %H:%M')

    # 1. Get acceptance criteria from requirement.md
    criteria = []
    req_path = os.path.join(task_path, 'requirement.md')
    if os.path.exists(req_path):
        with open(req_path, 'r', encoding='utf-8') as f:
            for line in f:
                line = line.strip()
                if re.match(r'^- \[[ x]\] .+', line):
                    text = re.sub(r'^- \[[ x]\] ', '', line)
                    criteria.append(text)

    # 2. Get git diff stats (staged + unstaged changes)
    import subprocess
    changed_files = []
    try:
        # Tracked changes
        result = subprocess.run(
            ['git', 'diff', '--stat', 'HEAD'],
            capture_output=True, text=True, cwd=os.getcwd()
        )
        diff_stat = result.stdout.strip()

        # Also get changed file list with line counts
        result2 = subprocess.run(
            ['git', 'diff', '--numstat', 'HEAD'],
            capture_output=True, text=True, cwd=os.getcwd()
        )
        for line in result2.stdout.strip().split('\n'):
            if line.strip():
                parts = line.split('\t')
                if len(parts) == 3:
                    added, removed, filepath = parts
                    # Skip .sdlc/ files from review
                    if not filepath.startswith('.sdlc/'):
                        changed_files.append({
                            'file': filepath,
                            'added': added,
                            'removed': removed,
                        })
    except Exception:
        diff_stat = 'Unable to get git diff'

    # 3. Build review.md
    lines = [
        f'# Review: {task_id}',
        '',
        f'**Reviewer**: AI (SDLC REVIEW phase)',
        f'**Date**: {now}',
        f'**Task**: {task.get("type", "fix")}({task.get("module", "?")}): {task.get("summary", "")}',
        f'**Verdict**: PENDING',
        '',
        '## Acceptance Criteria Verification',
        '',
        '| # | Criterion | Status | Evidence |',
        '|---|---|---|---|',
    ]

    if criteria:
        for i, c in enumerate(criteria, 1):
            lines.append(f'| {i} | {c} | PENDING | <!-- verify --> |')
    else:
        lines.append('| 1 | <!-- no criteria found in requirement.md --> | PENDING | |')

    lines.extend([
        '',
        '## Files Changed',
        '',
        '| File | Added | Removed | Notes |',
        '|---|---|---|---|',
    ])

    if changed_files:
        for cf in changed_files:
            lines.append(f'| `{cf["file"]}` | +{cf["added"]} | -{cf["removed"]} | <!-- review --> |')
    else:
        lines.append('| <!-- no changes detected --> | | | |')

    lines.extend([
        '',
        '## Quality Checklist',
        '',
        '- [ ] No hardcoded magic values',
        '- [ ] No `SELECT *`',
        '- [ ] Error handling present',
        '- [ ] XSS safe',
        '- [ ] SQL injection safe',
        '- [ ] CSRF tokens intact',
        '- [ ] Financial calcs: parseFloat + toFixed + NaN guard',
        '- [ ] Business logic in models, not controllers',
        '- [ ] No N+1 queries',
        '- [ ] Consistent with module patterns',
        '',
        '## Findings',
        '',
        '### Critical (blocks merge)',
        'None',
        '',
        '### Warnings (should fix)',
        'None',
        '',
        '### Advisories (nice to fix)',
        'None',
        '',
        '## Verdict: PENDING',
        '',
        '<!-- Change to: PASS / PASS_WITH_ADVISORIES / FAIL -->',
        '<!-- If FAIL: list specific issues that must be fixed -->',
        '',
    ])

    review_path = os.path.join(task_path, 'review.md')
    with open(review_path, 'w', encoding='utf-8') as f:
        f.write('\n'.join(lines))

    print(f'  Generated: .sdlc/active/{task_id}/review.md')
    print(f'  Criteria: {len(criteria)} from requirement.md')
    print(f'  Files: {len(changed_files)} changed (excluding .sdlc/)')
    print(f'  Next: Review each criterion, update verdict, then transition COMMIT')

def _copy_framework(source, target):
    """Copy generic engine files from source to target.

    Returns number of items copied.
    Copies: engine/, roles/, templates/, prompts/, quality/, workflows/, docs/,
            SKILL.md, steps.json, requirements.txt (generic instructions + config)
    Skips: config.json, ARCHITECTURE.md, ECOSYSTEM_ANALYSIS.md (repo-specific)
    Never touches: pipeline.json, active/, done/
    """
    dirs_to_copy = ['engine', 'roles', 'templates', 'prompts', 'quality', 'workflows', 'docs']
    files_to_copy = ['SKILL.md', 'steps.json', 'requirements.txt', 'README.md', 'CHANGELOG.md']
    # NOT synced: ARCHITECTURE.md, ECOSYSTEM_ANALYSIS.md, config.json, pipeline.json (repo-specific)
    count = 0

    os.makedirs(target, exist_ok=True)

    for d in dirs_to_copy:
        src = os.path.join(source, d)
        dst = os.path.join(target, d)
        if os.path.exists(src):
            if os.path.exists(dst):
                shutil.rmtree(dst)
            shutil.copytree(src, dst)
            print(f'    \u2705 {d}/')
            count += 1

    for f in files_to_copy:
        src = os.path.join(source, f)
        dst = os.path.join(target, f)
        if os.path.exists(src):
            shutil.copy2(src, dst)
            print(f'    \u2705 {f}')
            count += 1

    return count


def cmd_init(args):
    """Initialize .sdlc/ in the current project (fresh state)."""
    target = os.path.join(os.getcwd(), '.sdlc')

    if os.path.exists(target):
        print(f'  .sdlc/ already exists at {target}')
        print(f'  Use --force to reinitialize (CLEARS all task data)')
        print(f'  Use `sdlc sync` to update framework without losing tasks')
        if not getattr(args, 'force', False):
            return
        print(f'  Force mode: full reinitialize...')

    # Source is the directory containing THIS cli.py
    source = SDLC_DIR
    if os.path.abspath(source) == os.path.abspath(target):
        print(f'  Already in the source .sdlc/ project. Nothing to do.')
        return

    # Copy framework files
    _copy_framework(source, target)

    # Always create fresh state — never inherit tasks from source repo
    state_file = os.path.join(target, 'pipeline.json')
    old_state = None
    if os.path.exists(state_file):
        with open(state_file, 'r', encoding='utf-8') as f:
            try:
                old_state = json.load(f)
            except Exception:
                pass

    # Warn if clearing existing tasks
    if old_state:
        active = old_state.get('active_task')
        backlog = old_state.get('backlog', [])
        history = old_state.get('history', [])
        if active or backlog or history:
            print(f'\n    \u26a0\ufe0f  Clearing inherited state:')
            if active:
                print(f'       Active: [{active["id"]}] {active.get("summary", "")}')
            if backlog:
                print(f'       Backlog: {len(backlog)} task(s)')
            if history:
                print(f'       History: {len(history)} completed task(s)')

    # Write clean state
    with open(state_file, 'w', encoding='utf-8') as f:
        json.dump({'active_task': None, 'backlog': [], 'history': []}, f, indent=2)
    print(f'    \u2705 pipeline.json (fresh \u2014 no inherited tasks)')

    # Clean active/ and done/ — these belong to the source repo
    for d in ['active', 'done']:
        d_path = os.path.join(target, d)
        if os.path.exists(d_path):
            entries = os.listdir(d_path)
            if entries:
                print(f'    \u26a0\ufe0f  Clearing {d}/ ({len(entries)} inherited task folder(s))')
                shutil.rmtree(d_path)
        os.makedirs(d_path, exist_ok=True)
    # Create config.json if not exists (repo-specific — never synced)
    config_file = os.path.join(target, 'config.json')
    if not os.path.exists(config_file):
        project_name = os.path.basename(os.getcwd())
        default_config = {
            'project_name': project_name,
            'framework': 'codeigniter3',
            'paths': {
                'controllers': 'admin/application/controllers/',
                'models': 'admin/application/models/',
                'views': 'admin/application/views/',
                'js': 'admin/assets/js/',
                'css': 'admin/assets/css/',
                'helpers': 'admin/application/helpers/',
                'config': 'admin/application/config/',
            },
            'test_paths': {
                'e2e': 'admin/tests/e2e/',
                'unit': 'admin/tests/',
                'playwright_config': 'admin/tests/e2e/playwright.config.js',
            },
            'file_permissions': {
                'TEST_DESIGN': [
                    'admin/tests/**/*.spec.js',
                    'admin/tests/**/*.js',
                    'admin/tests/e2e/helpers/*.js',
                ],
                'CODING': [
                    'admin/application/**/*.php',
                    'admin/assets/**/*.js',
                    'admin/assets/**/*.css',
                    'admin/application/views/**/*.php',
                ],
                'TEST_VERIFY': [
                    'admin/tests/**/*.spec.js',
                ],
            },
            'base_url': f'http://localhost/{project_name}/admin/index.php',
            'git': {
                'base_branch': 'PRODUCTION',
                'dev_branch': 'Retail_1.1.1.0001',
                'branch_prefix': 'bugfix/',
                'remote': 'origin',
            },
        }
        with open(config_file, 'w', encoding='utf-8') as f:
            json.dump(default_config, f, indent=2)
        print(f'    \u2705 config.json (new \u2014 customize paths for this repo)')
    else:
        print(f'    \u2705 config.json (preserved \u2014 repo-specific)')

    print(f'\n  Initialized .sdlc/ in {os.getcwd()}')

    # Auto-append pipeline rules to GEMINI.md
    gemini_md = os.path.join(os.getcwd(), 'GEMINI.md')
    rules_template = os.path.join(target, 'templates', 'gemini-rules.md')
    marker = '# SDLC Pipeline Rules'

    if os.path.exists(rules_template):
        existing = ''
        if os.path.exists(gemini_md):
            with open(gemini_md, 'r', encoding='utf-8') as f:
                existing = f.read()

        if marker not in existing:
            with open(rules_template, 'r', encoding='utf-8') as f:
                rules = f.read()
            with open(gemini_md, 'a', encoding='utf-8') as f:
                if existing and not existing.endswith('\n'):
                    f.write('\n')
                f.write('\n\n')
                f.write(rules)
            print(f'    \u2705 GEMINI.md (pipeline rules appended)')
        else:
            print(f'    \u2705 GEMINI.md (pipeline rules already present)')

    print(f'  Run: python .sdlc/engine/cli.py health')


def cmd_sync(args):
    """Update framework files from source repo WITHOUT touching task data.

    Copies: engine/, roles/, templates/, SKILL.md, ARCHITECTURE.md
    Preserves: pipeline.json, active/, done/
    """
    target = os.path.join(os.getcwd(), '.sdlc')

    if not os.path.exists(target):
        print(f'  No .sdlc/ found. Use `sdlc init` first.')
        return

    source = SDLC_DIR
    if os.path.abspath(source) == os.path.abspath(target):
        print(f'  Already in the source .sdlc/ project. Nothing to sync.')
        return

    # Show what we're preserving
    state_file = os.path.join(target, 'pipeline.json')
    if os.path.exists(state_file):
        with open(state_file, 'r', encoding='utf-8') as f:
            try:
                state = json.load(f)
                active = state.get('active_task')
                backlog = state.get('backlog', [])
                history = state.get('history', [])
                preserved = []
                if active:
                    preserved.append(f'active: [{active["id"]}]')
                if backlog:
                    preserved.append(f'backlog: {len(backlog)}')
                if history:
                    preserved.append(f'history: {len(history)}')
                if preserved:
                    print(f'  \U0001f6e1\ufe0f  Preserving task data: {", ".join(preserved)}')
            except Exception:
                pass

    # Detect version change
    old_cli = os.path.join(target, 'engine', 'cli.py')
    old_version = None
    if os.path.exists(old_cli):
        with open(old_cli, 'r', encoding='utf-8') as f:
            for line in f:
                if line.startswith('VERSION'):
                    old_version = line.split('=')[1].split('#')[0].strip().strip("'\"")
                    break

    # Copy only framework — NOT pipeline.json, active/, done/
    print(f'  Updating framework from {source}...')
    count = _copy_framework(source, target)

    # Show version change
    if old_version and old_version != VERSION:
        print(f'\n  \U0001f4e6 Version: {old_version} \u2192 {VERSION}')
    elif old_version == VERSION:
        print(f'\n  \U0001f4e6 Version: {VERSION} (no change)')
    else:
        print(f'\n  \U0001f4e6 Version: {VERSION}')

    print(f'  Synced {count} items. Task data untouched.')
    print(f'  Run: python .sdlc/engine/cli.py show')


# ── Main ──

def main():
    parser = argparse.ArgumentParser(prog='sdlc', description='SDLC Pipeline v2.5 — Step-Driven')
    sub = parser.add_subparsers(dest='command')

    sub.add_parser('show', help='Show current task + phase + spec status')

    p_start = sub.add_parser('start', help='Start a new task')
    p_start.add_argument('-t', '--type', required=True, choices=['fix', 'feature', 'hotfix', 'cr', 'refactor', 'perf'])
    p_start.add_argument('-m', '--module', required=True)
    p_start.add_argument('-s', '--summary', required=True)
    p_start.add_argument('--ticket', default=None)
    p_start.add_argument('-p', '--priority', default='medium', choices=['critical', 'high', 'medium', 'low'])
    p_start.add_argument('-d', '--description', default=None)
    p_start.add_argument('--url', default=None, help='Page URL where bug occurs (e.g., admin_ret_reports/cash_book_details)')
    p_start.add_argument('--expected', default=None, help='Expected behavior')
    p_start.add_argument('--actual', default=None, help='Actual (buggy) behavior')
    p_start.add_argument('--steps', default=None, help='Steps to reproduce')
    p_start.add_argument('--force', action='store_true', help='Move active task to backlog')
    p_start.add_argument('--auto', action='store_true', help='Auto-flow: AI generates all specs, pauses at checkpoints')
    p_start.add_argument('--track', default=None, choices=['micro', 'express', 'standard'],
                         type=str.lower, help='Pipeline track: micro (3 steps), express (5), standard (10)')

    p_queue = sub.add_parser('queue', help='Add task to backlog')
    p_queue.add_argument('-t', '--type', required=True, choices=['fix', 'feature', 'hotfix', 'cr', 'refactor', 'perf'])
    p_queue.add_argument('-m', '--module', required=True)
    p_queue.add_argument('-s', '--summary', required=True)
    p_queue.add_argument('--ticket', default=None)
    p_queue.add_argument('-p', '--priority', default='medium', choices=['critical', 'high', 'medium', 'low'])

    p_pick = sub.add_parser('pick', help='Activate a backlog task')
    p_pick.add_argument('task_id')

    sub.add_parser('phase', help='Show current phase')

    p_tr = sub.add_parser('transition', help='Move to next phase (with gate check)')
    p_tr.add_argument('phase')

    sub.add_parser('spec-status', help='Show spec file status')

    p_cs = sub.add_parser('create-spec', help='Create a spec file')
    p_cs.add_argument('spec_type', choices=['design', 'tasks', 'review'])

    p_done = sub.add_parser('done', help='Complete task, move specs to done/')
    p_done.add_argument('--force', action='store_true', help='Force close without artifact checks')

    sub.add_parser('history', help='Show completed tasks')

    sub.add_parser('banner', help='Output role banner line')

    sub.add_parser('context', help='Generate context.md for active task (includes RAG search)')

    sub.add_parser('review', help='Auto-generate review.md from git diff + requirement.md')

    p_search = sub.add_parser('search', help='Semantic search across codebase via ChromaDB')
    p_search.add_argument('query', help='Natural language search query')
    p_search.add_argument('-n', type=int, default=10, help='Number of results (default: 10)')
    p_search.add_argument('-m', '--module', default=None, help='Filter by module name')

    sub.add_parser('metrics', help='Show metrics from completed tasks')

    sub.add_parser('health', help='Self-diagnostic: check LCA, RAG, pipeline state')

    p_init = sub.add_parser('init', help='Initialize .sdlc/ in current project (fresh state)')
    p_init.add_argument('--force', action='store_true', help='Reinitialize — CLEARS all task data')

    sub.add_parser('sync', help='Update framework from source repo (preserves task data)')

    # v2.5: Step-driven commands
    sub.add_parser('what-next', help='Show current step instruction (core anti-drift command)')

    p_sd = sub.add_parser('step-done', help='Mark current step complete and advance')
    p_sd.add_argument('--note', default='', help='Optional note about what was accomplished')

    p_disc = sub.add_parser('discover', help='One-command discovery: RAG + LCA + recipe + brain')
    p_disc.add_argument('query', nargs='?', default=None, help='Search query (defaults to task summary)')

    sub.add_parser('escalate', help='Escalate EXPRESS task to STANDARD track')

    # v4.1: Orchestrator command
    sub.add_parser('get-prompt', help='Get subagent prompt for current delegated step')

    sub.add_parser('enrich', help='Auto-generate recipe from task artifacts')

    # v4.0: Audit command (backported from AG)
    p_audit = sub.add_parser('audit', help='Audit conversation transcript for pipeline compliance')
    p_audit.add_argument('--conversation-id', required=True, help='Conversation UUID to audit')

    # v3.9: Checklist commands
    p_check = sub.add_parser('check', help='Mark checklist items done (e.g., check 1 2 3, check all, check reset)')
    p_check.add_argument('items', nargs='*', help='Item numbers to check (1-indexed), or "all"/"reset"')

    # v4.2: Anti-pattern store
    p_reject = sub.add_parser('reject', help='Record a rejected hypothesis in the anti-pattern store')
    p_reject.add_argument('--reason', '-r', required=True, help='Why the plan was rejected')

    args = parser.parse_args()
    state = load_state()

    if args.command == 'show' or args.command is None:
        cmd_show(state)
    elif args.command == 'start':
        cmd_start(state, args)
    elif args.command == 'queue':
        cmd_queue(state, args)
    elif args.command == 'pick':
        cmd_pick(state, args)
    elif args.command == 'phase':
        task = state.get('active_task')
        print(task['phase'] if task else 'IDLE')
    elif args.command == 'transition':
        cmd_transition(state, args)
    elif args.command == 'spec-status':
        task = state.get('active_task')
        if not task:
            print('  No active task.')
        else:
            status = spec_status(task['id'])
            icons = {'ready': '\u2705', 'template': '\U0001f7e1', 'missing': '\u2b1c'}
            print(f'\n  Spec Status: {task["id"]}\n')
            for name, st in status.items():
                print(f'    {icons[st]} {name}.md — {st}')
            print()
    elif args.command == 'create-spec':
        cmd_create_spec(state, args)
    elif args.command == 'done':
        cmd_done(state, args)
    elif args.command == 'history':
        cmd_history(state)
    elif args.command == 'banner':
        cmd_banner(state)
    elif args.command == 'context':
        cmd_context(state)
    elif args.command == 'review':
        cmd_review(state)
    elif args.command == 'search':
        cmd_search(state, args)
    elif args.command == 'metrics':
        cmd_metrics(state)
    elif args.command == 'health':
        cmd_health(state)
    elif args.command == 'init':
        cmd_init(args)
    elif args.command == 'sync':
        cmd_sync(args)
    # v2.5: Step-driven commands
    elif args.command == 'what-next':
        cmd_what_next(state)
    elif args.command == 'step-done':
        cmd_step_done(state, args)
    elif args.command == 'discover':
        cmd_discover(state, args)
    elif args.command == 'escalate':
        cmd_escalate(state)
    elif args.command == 'get-prompt':
        cmd_get_prompt(state)
    elif args.command == 'audit':
        cmd_audit(state, args)
    elif args.command == 'enrich':
        cmd_enrich(state)
    elif args.command == 'check':
        cmd_check(state, args)
    elif args.command == 'reject':
        cmd_reject(state, args)
    else:
        parser.print_help()


if __name__ == '__main__':
    main()
