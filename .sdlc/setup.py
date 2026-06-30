#!/usr/bin/env python3
"""
SDLC Pipeline Setup Script
============================
Copies the SDLC pipeline engine to a new project or updates an existing one.

Usage:
    python setup.py                          # Show help
    python setup.py init                     # Initialize .sdlc/ in current project
    python setup.py init --target /path/to   # Initialize in specific project
    python setup.py update                   # Update engine files (keeps state)
    python setup.py check                    # Verify setup is correct
"""

import argparse
import json
import os
import shutil
import sys
from pathlib import Path


# Files that are part of the engine (committed to repo)
ENGINE_FILES = [
    'pipeline_state.py',
    'validate_write.py',
    'roles.json',
    'pipeline.schema.json',
    'dashboard.html',
    'api.php',
    'setup.py',
    'sdlc.cmd',
    'CHANGELOG.md',
    'README.md',
    '.gitignore',
    'SKILL.md',
    'steps.json',
    'intake_questions.json',
]

# Directories that are part of the engine (copied recursively)
ENGINE_DIRS = [
    'engine',
    'prompts',
    'templates',
    'docs',
    'quality',
    'memory',
    'workflows',
]

# Patterns to skip during directory copy (local artifacts, caches)
SKIP_PATTERNS = [
    '__pycache__',
    '.pyc',
    'node_modules',
    '.DS_Store',
    'Thumbs.db',
]

# Files/dirs that are local state (gitignored)
STATE_FILES = [
    'pipeline.json',
    'handoff.md',
    'tasks/',
    'history/',
]

GITIGNORE_CONTENT = """# SDLC Pipeline — Local State (do NOT commit)
# These are developer-specific working files.
pipeline.json
handoff.md
tasks/
history/
"""

# Enforcement rules block — injected into project agent config
ENFORCEMENT_MARKER = 'SDLC PIPELINE ENFORCEMENT'
ENFORCEMENT_RULES = """
## ⚡ SDLC PIPELINE ENFORCEMENT (applies to ALL projects with .sdlc/roles.json):

At the START of every conversation, check if the project root has `.sdlc/roles.json`. If it exists:

1. **Read State**: Load `.sdlc/pipeline.json` for current phase. If file doesn't exist, phase is IDLE.
2. **Read Role**: Load `.sdlc/roles.json` → `roles[current_phase]` for the active role definition.
3. **Role Banner**: Start EVERY response with: `{icon} [{title}] Phase: {phase} | Task: {ticket}`. Skip only if phase is IDLE with no active task.
4. **File Permission Guard**: Before creating or modifying ANY file, check `roles.json → file_permissions[current_phase]`. If the file doesn't match `allowed_patterns`, REFUSE and state: "BLOCKED: {phase} role cannot modify {file_type}. Transition to {correct_phase} first."
5. **Role Boundary**: If you find work that belongs to another phase (e.g., found a bug during REVIEW), DO NOT fix it. Flag it and state which phase should handle it.
6. **Phase Transition**: Only transition when the current phase's `transition_condition` is met. State the condition and evidence.
7. **State Update**: After completing phase work, update `.sdlc/pipeline.json` via `python .sdlc/pipeline_state.py`.
8. **Override Protocol**: If user requests an action outside the current role, do NOT silently comply. State the conflict, ask "Override role lock? This will be logged.", and only proceed after confirmation.
9. **DISCUSS Phase**: This is the default for conversations. READ-ONLY — can read code, grep, search, query DBs, but CANNOT create or modify ANY source file.
"""

# Agent config files to check (in priority order)
AGENT_CONFIG_FILES = [
    '.agent/GEMINI.md',      # Antigravity / Gemini
    '.cursorrules',           # Cursor
    '.github/copilot-instructions.md',  # GitHub Copilot
    '.windsurfrules',         # Windsurf
]


def get_source_dir():
    """Get the directory where this script lives (the reference .sdlc/)."""
    return os.path.dirname(os.path.abspath(__file__))


def find_project_root(start_dir):
    """Walk up to find a project root (has .git/ or .agent/)."""
    current = os.path.abspath(start_dir)
    while current != os.path.dirname(current):
        if os.path.isdir(os.path.join(current, '.git')) or \
           os.path.isdir(os.path.join(current, '.agent')):
            return current
        current = os.path.dirname(current)
    return start_dir


def _should_skip(filepath):
    """Check if a file/dir should be skipped during copy."""
    name = os.path.basename(filepath)
    for pattern in SKIP_PATTERNS:
        if name == pattern or name.endswith(pattern):
            return True
    return False


def _copy_dir(src_dir, dst_dir):
    """Copy a directory recursively, skipping unwanted patterns.
    Returns (copied, skipped) counts."""
    copied = 0
    skipped = 0
    for root, dirs, files in os.walk(src_dir):
        # Filter out skippable dirs in-place
        dirs[:] = [d for d in dirs if not _should_skip(d)]

        rel_root = os.path.relpath(root, src_dir)
        dst_root = os.path.join(dst_dir, rel_root) if rel_root != '.' else dst_dir
        os.makedirs(dst_root, exist_ok=True)

        for fname in files:
            if _should_skip(fname):
                skipped += 1
                continue
            src_file = os.path.join(root, fname)
            dst_file = os.path.join(dst_root, fname)
            shutil.copy2(src_file, dst_file)
            copied += 1
    return copied, skipped


def _update_dir(src_dir, dst_dir):
    """Update directory: only copy files that changed.
    Returns (updated, unchanged) counts."""
    updated = 0
    unchanged = 0
    for root, dirs, files in os.walk(src_dir):
        dirs[:] = [d for d in dirs if not _should_skip(d)]

        rel_root = os.path.relpath(root, src_dir)
        dst_root = os.path.join(dst_dir, rel_root) if rel_root != '.' else dst_dir
        os.makedirs(dst_root, exist_ok=True)

        for fname in files:
            if _should_skip(fname):
                continue
            src_file = os.path.join(root, fname)
            dst_file = os.path.join(dst_root, fname)

            if os.path.exists(dst_file):
                with open(src_file, 'rb') as f1, open(dst_file, 'rb') as f2:
                    if f1.read() == f2.read():
                        unchanged += 1
                        continue

            shutil.copy2(src_file, dst_file)
            updated += 1
    return updated, unchanged


def detect_php_path():
    """Auto-detect PHP executable path."""
    import subprocess as sp

    # 1. Check if 'php' is in PATH
    try:
        result = sp.run(['php', '--version'], capture_output=True, text=True, timeout=5)
        if result.returncode == 0:
            return 'php'  # Available in PATH — simplest option
    except (FileNotFoundError, sp.TimeoutExpired):
        pass

    # 2. Check common Windows XAMPP locations
    common_paths = [
        r'C:\xampp\php\php.exe',
        r'D:\xampp\php\php.exe',
        r'E:\xampp\php\php.exe',
        r'C:\wamp64\bin\php\php8.2.0\php.exe',
        r'C:\wamp64\bin\php\php8.1.0\php.exe',
    ]
    for p in common_paths:
        if os.path.exists(p):
            return p.replace('\\', '/')

    # 3. Check common Linux paths
    for p in ['/usr/bin/php', '/usr/local/bin/php']:
        if os.path.exists(p):
            return p

    # 4. Fallback
    return 'php'


def generate_config(target_dir, project_name=None):
    """Generate config.json with auto-detected environment values."""
    if project_name is None:
        project_name = os.path.basename(os.path.abspath(target_dir))

    php_path = detect_php_path()
    base_url = f'http://localhost/{project_name}/admin/index.php'

    config = {
        "project_name": project_name,
        "framework": "codeigniter3",
        "paths": {
            "controllers": "admin/application/controllers/",
            "models": "admin/application/models/",
            "views": "admin/application/views/",
            "js": "admin/assets/js/",
            "css": "admin/assets/css/",
            "helpers": "admin/application/helpers/",
            "config": "admin/application/config/"
        },
        "environment": {
            "php_path": php_path,
            "pytest_cmd": "python -m pytest",
            "playwright_installed": True
        },
        "test_paths": {
            "e2e": "admin/tests/e2e/",
            "unit": "admin/tests/",
            "playwright_config": "admin/tests/e2e/playwright.config.js"
        },
        "base_url": base_url,
        "git": {
            "base_branch": "PRODUCTION",
            "dev_branch": "Retail_1.1.1.0001",
            "branch_prefix": "bugfix/",
            "remote": "origin"
        },
        "repo": {
            "owner": "Logimax-Technologies",
            "name": project_name,
            "recipes_owner": "LOGIMAX-CLIENTS",
            "recipes_repo": "bug-recipes"
        },
        "discovery_sources": {
            "lca": False,
            "rag": False,
            "recipes": True,
            "call_tree": True,
            "knowledge_brain": True
        }
    }
    return config


def init_sdlc(target_dir, source_dir=None, force=False):
    """Initialize .sdlc/ in a project."""
    if source_dir is None:
        source_dir = get_source_dir()

    sdlc_dir = os.path.join(target_dir, '.sdlc')

    if os.path.exists(sdlc_dir) and not force:
        print(f'  ⚠️  .sdlc/ already exists at {sdlc_dir}')
        print(f'      Use --force to overwrite engine files (state is preserved).')
        return False

    os.makedirs(sdlc_dir, exist_ok=True)

    # Copy engine files
    copied = 0
    for filename in ENGINE_FILES:
        src = os.path.join(source_dir, filename)
        dst = os.path.join(sdlc_dir, filename)

        if os.path.exists(src):
            shutil.copy2(src, dst)
            print(f'  ✅ {filename}')
            copied += 1
        else:
            print(f'  ⚠️  {filename} — not found in source, skipping')

    # Copy engine directories
    dir_files = 0
    for dirname in ENGINE_DIRS:
        src = os.path.join(source_dir, dirname)
        dst = os.path.join(sdlc_dir, dirname)
        if os.path.isdir(src):
            fc, sc = _copy_dir(src, dst)
            dir_files += fc
            print(f'  ✅ {dirname}/ ({fc} files)')
        else:
            print(f'  ⚠️  {dirname}/ — not found in source, skipping')

    # Generate config.json if not exists
    config_path = os.path.join(sdlc_dir, 'config.json')
    if not os.path.exists(config_path) or force:
        config = generate_config(target_dir)
        with open(config_path, 'w', encoding='utf-8') as f:
            json.dump(config, f, indent=2, ensure_ascii=False)
        php_path = config['environment']['php_path']
        print(f'  ✅ config.json (php_path: {php_path})')
    else:
        print(f'  ⬜ config.json — already exists (preserved)')

    # Create .gitignore if not exists
    gitignore_path = os.path.join(sdlc_dir, '.gitignore')
    if not os.path.exists(gitignore_path):
        with open(gitignore_path, 'w') as f:
            f.write(GITIGNORE_CONTENT)
        print(f'  ✅ .gitignore (created)')

    # Also ensure project-level .gitignore has .sdlc state entries
    project_gitignore = os.path.join(target_dir, '.gitignore')
    ensure_project_gitignore(project_gitignore)

    total = copied + dir_files
    print(f'\n  🎉 Done! {total} files copied to {sdlc_dir}')
    print(f'  📊 Dashboard: http://localhost/{{project}}/.sdlc/dashboard.html')
    print(f'  🔧 CLI: python .sdlc/engine/cli.py banner')
    return True


def ensure_project_gitignore(gitignore_path):
    """Make sure the project .gitignore has SDLC state entries."""
    sdlc_entries = [
        '.sdlc/pipeline.json',
        '.sdlc/tasks/',
        '.sdlc/history/',
    ]

    existing_content = ''
    if os.path.exists(gitignore_path):
        with open(gitignore_path, 'r', encoding='utf-8', errors='ignore') as f:
            existing_content = f.read()

    missing = [e for e in sdlc_entries if e not in existing_content]
    if not missing:
        return

    with open(gitignore_path, 'a', encoding='utf-8') as f:
        f.write('\n# SDLC Pipeline — Local State\n')
        for entry in missing:
            f.write(f'{entry}\n')
    print(f'  ✅ Updated project .gitignore with SDLC entries')


def update_sdlc(target_dir, source_dir=None):
    """Update engine files without touching state."""
    if source_dir is None:
        source_dir = get_source_dir()

    sdlc_dir = os.path.join(target_dir, '.sdlc')
    if not os.path.exists(sdlc_dir):
        print(f'  ❌ No .sdlc/ found at {target_dir}. Run init first.')
        return False

    updated = 0
    for filename in ENGINE_FILES:
        src = os.path.join(source_dir, filename)
        dst = os.path.join(sdlc_dir, filename)

        if not os.path.exists(src):
            continue

        # Check if file changed
        if os.path.exists(dst):
            with open(src, 'rb') as f1, open(dst, 'rb') as f2:
                if f1.read() == f2.read():
                    print(f'  ⬜ {filename} — unchanged')
                    continue

        shutil.copy2(src, dst)
        print(f'  🔄 {filename} — updated')
        updated += 1

    # Update engine directories
    dir_updated = 0
    for dirname in ENGINE_DIRS:
        src = os.path.join(source_dir, dirname)
        dst = os.path.join(sdlc_dir, dirname)
        if os.path.isdir(src):
            u, unch = _update_dir(src, dst)
            dir_updated += u
            if u > 0:
                print(f'  🔄 {dirname}/ — {u} files updated')
            else:
                print(f'  ⬜ {dirname}/ — unchanged')
        else:
            print(f'  ⚠️  {dirname}/ — not found in source')

    total = updated + dir_updated
    print(f'\n  {"🎉" if total else "✅"} {total} files updated.')
    return True


def check_sdlc(target_dir):
    """Verify .sdlc/ setup is correct."""
    sdlc_dir = os.path.join(target_dir, '.sdlc')
    issues = []
    ok = []

    if not os.path.exists(sdlc_dir):
        print(f'  ❌ No .sdlc/ directory found. Run: python setup.py init')
        return False

    # Check engine files
    for filename in ENGINE_FILES:
        path = os.path.join(sdlc_dir, filename)
        if os.path.exists(path):
            ok.append(filename)
        else:
            issues.append(f'Missing: {filename}')

    # Check engine directories
    for dirname in ENGINE_DIRS:
        path = os.path.join(sdlc_dir, dirname)
        if os.path.isdir(path):
            file_count = sum(1 for _, _, files in os.walk(path) for _ in files)
            ok.append(f'{dirname}/ ({file_count} files)')
        else:
            issues.append(f'Missing directory: {dirname}/')

    # Check config.json
    config_path = os.path.join(sdlc_dir, 'config.json')
    if os.path.exists(config_path):
        try:
            with open(config_path, 'r', encoding='utf-8') as f:
                cfg = json.load(f)
            env = cfg.get('environment', {})
            php = env.get('php_path', '')
            if php:
                # Verify PHP path actually works
                import subprocess as sp
                try:
                    r = sp.run([php, '--version'], capture_output=True, text=True, timeout=5)
                    if r.returncode == 0:
                        # PHP may output warnings before the version line
                        import re
                        combined = (r.stdout + r.stderr).strip()
                        ver = 'detected'
                        for line in combined.split('\n'):
                            if re.match(r'PHP \d+\.\d+', line.strip()):
                                ver = line.strip()
                                break
                        ok.append(f'config.json (php: {ver})')
                    else:
                        issues.append(f'php_path "{php}" exists but returned error')
                except FileNotFoundError:
                    issues.append(f'php_path "{php}" not found — update .sdlc/config.json')
                except sp.TimeoutExpired:
                    issues.append(f'php_path "{php}" timed out')
            else:
                issues.append('config.json missing environment.php_path')
        except json.JSONDecodeError:
            issues.append('config.json is invalid JSON')
    else:
        issues.append('Missing: config.json — run: python .sdlc/setup.py init --force')

    # Check gitignore
    project_gitignore = os.path.join(target_dir, '.gitignore')
    if os.path.exists(project_gitignore):
        with open(project_gitignore, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()
            if '.sdlc/pipeline.json' not in content:
                issues.append('.gitignore missing .sdlc/pipeline.json entry')
            else:
                ok.append('.gitignore entries')
    else:
        issues.append('No project .gitignore found')

    # Check roles.json validity
    roles_path = os.path.join(sdlc_dir, 'roles.json')
    if os.path.exists(roles_path):
        try:
            with open(roles_path, 'r', encoding='utf-8') as f:
                roles = json.load(f)
            if 'phase_order' in roles and 'roles' in roles:
                ok.append('roles.json valid')
            else:
                issues.append('roles.json missing phase_order or roles')
        except json.JSONDecodeError:
            issues.append('roles.json is invalid JSON')

    # Check enforcement rules in agent config
    enforcement_found = False
    for config_file in AGENT_CONFIG_FILES:
        config_path = os.path.join(target_dir, config_file)
        if os.path.exists(config_path):
            with open(config_path, 'r', encoding='utf-8', errors='ignore') as f:
                if ENFORCEMENT_MARKER in f.read():
                    ok.append(f'Enforcement rules in {config_file}')
                    enforcement_found = True
                    break
    if not enforcement_found:
        issues.append('No enforcement rules in agent config — run: python .sdlc/setup.py onboard')

    # Check global skill
    home = os.path.expanduser('~')
    skill_path = os.path.join(home, '.gemini', 'config', 'skills', 'sdlc-pipeline', 'SKILL.md')
    if os.path.exists(skill_path):
        ok.append('Global skill installed')
    else:
        issues.append('Global skill not installed — run: python .sdlc/setup.py onboard')

    # Print results
    print(f'\n  SDLC Pipeline Health Check: {sdlc_dir}\n')
    for item in ok:
        print(f'  ✅ {item}')
    for issue in issues:
        print(f'  ❌ {issue}')

    print(f'\n  Result: {"✅ All good!" if not issues else f"⚠️ {len(issues)} issue(s) found"}')
    return len(issues) == 0


def inject_enforcement_rules(project_dir):
    """Inject SDLC enforcement rules into the project's agent config.

    Checks for existing agent config files (.agent/GEMINI.md, .cursorrules, etc.).
    If found, appends enforcement rules if not already present.
    If none found, creates .agent/GEMINI.md with the rules.
    """
    # Check if enforcement rules already exist in any config
    for config_file in AGENT_CONFIG_FILES:
        config_path = os.path.join(project_dir, config_file)
        if os.path.exists(config_path):
            with open(config_path, 'r', encoding='utf-8', errors='ignore') as f:
                content = f.read()
            if ENFORCEMENT_MARKER in content:
                print(f'  ✅ Enforcement rules already in {config_file}')
                return config_file

    # Find existing config to append to, or create new one
    target_config = None
    for config_file in AGENT_CONFIG_FILES:
        config_path = os.path.join(project_dir, config_file)
        if os.path.exists(config_path):
            target_config = config_file
            break

    if target_config is None:
        # No agent config exists — create .agent/GEMINI.md
        target_config = AGENT_CONFIG_FILES[0]  # .agent/GEMINI.md
        config_path = os.path.join(project_dir, target_config)
        os.makedirs(os.path.dirname(config_path), exist_ok=True)
        with open(config_path, 'w', encoding='utf-8') as f:
            f.write(f'# Project Rules\n{ENFORCEMENT_RULES}')
        print(f'  ✅ Created {target_config} with enforcement rules')
    else:
        # Append to existing config
        config_path = os.path.join(project_dir, target_config)
        with open(config_path, 'a', encoding='utf-8') as f:
            f.write(f'\n{ENFORCEMENT_RULES}')
        print(f'  ✅ Appended enforcement rules to {target_config}')

    return target_config


def onboard_developer(source_dir=None):
    """Install the global SDLC skill + inject enforcement rules.

    Two things happen:
    1. SKILL.md → ~/.gemini/config/skills/sdlc-pipeline/ (global, one-time)
    2. Enforcement rules → project's .agent/GEMINI.md (per-project)

    After this, any new conversation in this project will auto-activate the pipeline.
    """
    if source_dir is None:
        source_dir = get_source_dir()

    # Find project root
    project_dir = find_project_root(os.path.dirname(source_dir))

    # --- Step 1: Install global skill ---
    home = os.path.expanduser('~')
    skill_dir = os.path.join(home, '.gemini', 'config', 'skills', 'sdlc-pipeline')

    print(f'\n  👤 SDLC Pipeline — Developer Onboarding')
    print(f'  Project: {project_dir}\n')

    print(f'  Step 1: Global Skill')
    os.makedirs(skill_dir, exist_ok=True)
    skill_content = generate_skill_md()
    skill_path = os.path.join(skill_dir, 'SKILL.md')
    with open(skill_path, 'w', encoding='utf-8') as f:
        f.write(skill_content)
    print(f'  ✅ SKILL.md → {skill_dir}')

    # --- Step 2: Inject enforcement rules ---
    print(f'\n  Step 2: Enforcement Rules')
    config_file = inject_enforcement_rules(project_dir)

    # --- Summary ---
    print(f'\n  🎉 Onboarding complete!')
    print(f'  ✅ Global skill installed (any project with .sdlc/ detected)')
    print(f'  ✅ Enforcement rules in {config_file} (pipeline activation guaranteed)')
    print(f'\n  Every new conversation will now auto-activate the SDLC pipeline.')
    print(f'\n  Quick test:')
    print(f'    python .sdlc/pipeline_state.py --version')
    print(f'    python .sdlc/pipeline_state.py show')
    return True


def generate_skill_md():
    """Generate the global SKILL.md with current version."""
    from pipeline_state import VERSION
    return f"""---
name: sdlc-pipeline
description: "SDLC Pipeline Engine v{VERSION} — role-locked phase management, TDD workflow, sub-tasks, file permission guards, role prompt extraction, decision tracking, pause/resume handoff, and dashboard. Auto-detects .sdlc/ in any project."
---

# SDLC Pipeline Engine

## Auto-Detection
At the START of every conversation, check if the project root has `.sdlc/roles.json`. If it exists:

1. Run `python .sdlc/pipeline_state.py get-prompt` to get your active role
2. Follow ALL instructions, constraints, and file permissions in the output
3. Show the role banner at the start of every response

## Rules

### RULE 1: Role Banner
Start EVERY response with: `{{icon}} [{{title}}] Phase: {{phase}} | Task: {{ticket}}`

### RULE 2: File Permission Guard
Before creating or modifying ANY file, run:
```bash
python .sdlc/validate_write.py --phase {{current_phase}} --file {{target_file}}
```
If it returns exit code 1, REFUSE the write.

### RULE 3: Override Protocol
If user requests an action outside the current role, state the conflict and ask: "Override role lock? This will be logged."

### RULE 4: Phase Boundaries
Do NOT fix bugs found during REVIEW. Flag them for CODING phase.

### RULE 5: Context Health
Run `python .sdlc/pipeline_state.py context` periodically. Warn at 60%, critical at 80%.

### RULE 6: VIBE Mode
In VIBE phase, auto-select the best sub-role for each user message. Use `get-prompt --sub-role <name>` to load sub-role instructions.

### RULE 7: Skill Activation
When `get-prompt` output includes a SKILL ACTIVATION section, read those files immediately.

### RULE 8: Decision Tracking
During DISCUSS and REQUIREMENT phases, record every resolved decision:
```bash
python .sdlc/pipeline_state.py add-decision <key> <value> --reason <why>
```

### RULE 9: Pause/Resume Handoff
Before ending a long conversation:
```bash
python .sdlc/pipeline_state.py pause --reason "reason"
```

## State Manager CLI
```bash
python .sdlc/pipeline_state.py show
python .sdlc/pipeline_state.py get-prompt
python .sdlc/pipeline_state.py suggest-next
python .sdlc/pipeline_state.py add-decision <key> <value> --reason <why>
python .sdlc/pipeline_state.py pause --reason "text"
python .sdlc/pipeline_state.py resume
python .sdlc/pipeline_state.py reset
```

## Dashboard
```
http://localhost/{{project}}/.sdlc/dashboard.html
```
"""



def main():
    parser = argparse.ArgumentParser(
        description='SDLC Pipeline Setup',
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
Examples:
  python .sdlc/setup.py init                         # Init in current project
  python .sdlc/setup.py init --target /path/to/proj  # Init in another project
  python .sdlc/setup.py update                       # Update engine files
  python .sdlc/setup.py check                        # Verify setup
        """
    )

    sub = parser.add_subparsers(dest='command')

    p_init = sub.add_parser('init', help='Initialize .sdlc/ in a project')
    p_init.add_argument('--target', default=None, help='Target project directory')
    p_init.add_argument('--source', default=None, help='Source .sdlc/ directory')
    p_init.add_argument('--force', action='store_true', help='Overwrite existing files')

    p_update = sub.add_parser('update', help='Update engine files')
    p_update.add_argument('--target', default=None)
    p_update.add_argument('--source', default=None)

    p_check = sub.add_parser('check', help='Verify setup')
    p_check.add_argument('--target', default=None)

    sub.add_parser('onboard', help='Install global skill for this developer (one-time)')

    p_profile = sub.add_parser('init-profile', help='Generate roles.json from stack profile(s)')
    p_profile.add_argument('--stacks', required=False, help='Comma-separated stacks: nextjs,django')
    p_profile.add_argument('--list', action='store_true', help='List available stacks')
    p_profile.add_argument('--target', default=None, help='Target project directory')

    args = parser.parse_args()

    if not args.command:
        parser.print_help()
        return

    # Resolve target directory
    target = args.target if hasattr(args, 'target') and args.target else os.getcwd()
    target = find_project_root(target)

    source = args.source if hasattr(args, 'source') and args.source else None

    print(f'\n  ⚡ SDLC Pipeline Setup')
    print(f'  Project: {target}\n')

    if args.command == 'init':
        init_sdlc(target, source, getattr(args, 'force', False))
    elif args.command == 'update':
        update_sdlc(target, source)
    elif args.command == 'check':
        check_sdlc(target)
    elif args.command == 'onboard':
        onboard_developer()
    elif args.command == 'init-profile':
        if getattr(args, 'list', False):
            list_profiles()
        elif getattr(args, 'stacks', None):
            init_profile(target, args.stacks)
        else:
            print('  Usage: python .sdlc/setup.py init-profile --stacks nextjs,django')
            print('         python .sdlc/setup.py init-profile --list')


# ── Stack Profiles ──────────────────────────────────────────────────────────

STACK_PROFILES = {
    "codeigniter3": {
        "label": "PHP / CodeIgniter 3",
        "source": [
            "application/controllers/**", "application/models/**", "application/views/**",
            "application/libraries/**", "application/helpers/**",
            "assets/js/**", "assets/css/**"
        ],
        "tests": ["application/tests/**"],
        "docs": ["knowledge_brain/**", "**/*.md"]
    },
    "laravel": {
        "label": "PHP / Laravel",
        "source": [
            "app/**", "resources/views/**", "resources/js/**", "resources/css/**",
            "routes/**", "database/migrations/**", "database/seeders/**"
        ],
        "tests": ["tests/**"],
        "docs": ["docs/**", "**/*.md"]
    },
    "nextjs": {
        "label": "TypeScript / Next.js",
        "source": [
            "src/app/**", "src/components/**", "src/lib/**", "src/hooks/**",
            "src/utils/**", "src/styles/**", "public/**", "styles/**"
        ],
        "tests": ["__tests__/**", "src/**/*.test.ts", "src/**/*.test.tsx", "src/**/*.spec.ts"],
        "docs": ["docs/**", "**/*.md"]
    },
    "react-vite": {
        "label": "TypeScript / React + Vite",
        "source": ["src/**", "public/**"],
        "tests": ["src/**/*.test.*", "src/**/*.spec.*", "__tests__/**", "vitest/**"],
        "docs": ["docs/**", "**/*.md"]
    },
    "django": {
        "label": "Python / Django",
        "source": [
            "apps/**", "templates/**", "static/**",
            "*/views.py", "*/models.py", "*/forms.py", "*/serializers.py",
            "*/urls.py", "*/admin.py", "*/signals.py"
        ],
        "tests": ["tests/**", "**/test_*.py", "**/*_test.py"],
        "docs": ["docs/**", "**/*.md"]
    },
    "express": {
        "label": "JavaScript / Express.js (Node)",
        "source": [
            "src/**", "routes/**", "middleware/**", "controllers/**",
            "models/**", "services/**", "views/**", "public/**"
        ],
        "tests": ["test/**", "__tests__/**", "*.test.js", "*.spec.js", "*.test.ts", "*.spec.ts"],
        "docs": ["docs/**", "**/*.md"]
    },
    "python": {
        "label": "Python package / library",
        "source": ["src/**", "lib/**"],
        "tests": ["tests/**", "test_*.py"],
        "docs": ["docs/**", "**/*.md"]
    },
    "go": {
        "label": "Go",
        "source": ["cmd/**", "internal/**", "pkg/**", "api/**"],
        "tests": ["**/*_test.go", "testdata/**"],
        "docs": ["docs/**", "**/*.md"]
    },
    "flutter": {
        "label": "Dart / Flutter",
        "source": ["lib/**", "assets/**", "android/**", "ios/**", "web/**"],
        "tests": ["test/**", "integration_test/**"],
        "docs": ["docs/**", "**/*.md"]
    },
    "spring": {
        "label": "Java / Spring Boot",
        "source": ["src/main/**"],
        "tests": ["src/test/**"],
        "docs": ["docs/**", "**/*.md"]
    }
}


def list_profiles():
    """List available stack profiles."""
    print('  Available stack profiles:\n')
    for key, profile in STACK_PROFILES.items():
        print(f'    {key:20s}  {profile["label"]}')
    print(f'\n  Usage:')
    print(f'    python .sdlc/setup.py init-profile --stacks nextjs')
    print(f'    python .sdlc/setup.py init-profile --stacks nextjs,django')
    print(f'    python .sdlc/setup.py init-profile --stacks react-vite,express')


def init_profile(target_dir, stacks_csv):
    """Generate roles.json by combining one or more stack profiles."""
    import copy

    stacks = [s.strip().lower() for s in stacks_csv.split(',')]

    # Validate stacks
    invalid = [s for s in stacks if s not in STACK_PROFILES]
    if invalid:
        print(f'  ❌ Unknown stack(s): {", ".join(invalid)}')
        print(f'     Run --list to see available stacks.')
        return False

    # Load generic base roles.json
    sdlc_dir = os.path.join(get_source_dir())
    base_path = os.path.join(sdlc_dir, 'roles.json')
    if not os.path.exists(base_path):
        print(f'  ❌ No roles.json found at {base_path}')
        return False

    with open(base_path, 'r', encoding='utf-8') as f:
        base = json.load(f)

    # Merge patterns from all selected stacks
    merged_source = []
    merged_tests = []
    merged_docs = []
    stack_labels = []

    for stack in stacks:
        profile = STACK_PROFILES[stack]
        stack_labels.append(profile['label'])
        for p in profile['source']:
            if p not in merged_source:
                merged_source.append(p)
        for p in profile['tests']:
            if p not in merged_tests:
                merged_tests.append(p)
        for p in profile['docs']:
            if p not in merged_docs:
                merged_docs.append(p)

    # Apply merged patterns to roles.json
    src_patterns = ['.sdlc/pipeline.json'] + merged_source + ['artifacts only']
    test_patterns = ['.sdlc/pipeline.json'] + merged_tests + ['artifacts only']
    doc_patterns = ['.sdlc/pipeline.json'] + merged_docs

    base['file_permissions']['TEST_DESIGN']['allowed_patterns'] = test_patterns
    base['file_permissions']['CODING']['allowed_patterns'] = src_patterns

    vibe = base['file_permissions']['VIBE']['sub_role_permissions']
    vibe['bug_fixer']['allowed_patterns'] = ['.sdlc/pipeline.json'] + merged_source
    vibe['builder']['allowed_patterns'] = ['.sdlc/pipeline.json'] + merged_source
    vibe['documenter']['allowed_patterns'] = doc_patterns
    vibe['tester']['allowed_patterns'] = ['.sdlc/pipeline.json'] + merged_tests

    # Add metadata
    base['_profile'] = f'Combined: {" + ".join(stacks)}'
    base['_profile_stacks'] = stacks

    # Write to target
    target_sdlc = os.path.join(target_dir, '.sdlc')
    os.makedirs(target_sdlc, exist_ok=True)
    output_path = os.path.join(target_sdlc, 'roles.json')

    with open(output_path, 'w', encoding='utf-8') as f:
        json.dump(base, f, indent=2, ensure_ascii=False)

    print(f'  ✅ Generated roles.json for: {" + ".join(stack_labels)}')
    print(f'     Stacks: {", ".join(stacks)}')
    print(f'     Source patterns: {len(merged_source)}')
    print(f'     Test patterns: {len(merged_tests)}')
    print(f'     Output: {output_path}')
    return True


if __name__ == '__main__':
    # Fix Windows encoding: emojis crash on cp1252
    if sys.platform == 'win32' and hasattr(sys.stdout, 'reconfigure'):
        try:
            sys.stdout.reconfigure(encoding='utf-8', errors='replace')
            sys.stderr.reconfigure(encoding='utf-8', errors='replace')
        except Exception:
            pass
    main()
