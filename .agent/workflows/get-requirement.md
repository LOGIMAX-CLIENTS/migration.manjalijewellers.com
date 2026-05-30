---
description: Analyze a task description against the codebase knowledge brain and generate structured requirement output (Category, Task, User Story, Observation, Impacts, Acceptance Criteria)
version: 1.0
last_updated: 2026-04-12
audience: Support Team
---

# Get Requirement Workflow

## Purpose

Take a **raw task description** (paragraph, bullet points, or document) from the support team, analyze it against the `knowledge_brain/` and codebase, and produce a **structured requirement** that the support team can copy-paste section-by-section into their tracking system.

> **This workflow is READ-ONLY** — it never modifies code, database, or any files. It only reads the knowledge_brain and codebase to generate output.

## Safety Constraints

**This workflow is used by non-technical support team members. The following rules are mandatory:**

### Allowed Tools (built-in to IDE, zero installation required)
1. **`view_file`** — Read any file from disk. Use this to read brain files and source code.
2. **`grep_search`** — Search across files for keywords. This uses ripgrep which is bundled inside VS Code/Cursor. It does NOT require system-level grep installation.

### Blocked Tools
3. **`run_command` is STRICTLY FORBIDDEN.** Do not execute any terminal/shell commands. The support team does not have developer tools (grep, git CLI, npm, pip, python, node) installed on their machines. Any terminal command will either fail or confuse them.
4. **DO NOT install anything.** No packages, no dependencies, no extensions. If a tool is missing, skip that step silently.
5. **DO NOT ask the user to run commands.** Never instruct them to run anything in the terminal. They do not have that knowledge.
6. **DO NOT create, modify, or delete any files.** Output goes to the conversation only.
7. **If a brain file does not exist**, state it in the output and proceed with whatever brain files are available. Do not attempt alternative methods.
8. **If you encounter any error**, skip that step silently and continue to the next step. Never surface errors to the user.

## Prerequisites

- `knowledge_brain/` directory exists with at least one module brain built
- `knowledge_brain/_SYSTEM/` directory exists (for cross-module analysis)
- Task description provided by the user

## Input

- `{TASK_DESCRIPTION}` — Raw task description (paragraph, bullet points, pasted document, or verbal explanation)

## Output

Six copyable sections displayed directly in the conversation:
1. **Category** — Module and sub-feature area
2. **Task** — Clean one-line title
3. **User Story** — As a [role], I want to [action], so that [benefit]
4. **Observation** — Current system behavior from brain analysis
5. **Impacts** — Files, tables, and cross-module effects
6. **Acceptance Criteria** — Testable done conditions

---

## Steps

### Step 1: Read the Skill File [Antigravity]

// turbo
Before any analysis, read the supporting skill file for keyword mappings, role definitions, and reference examples:

```
Read: {PROJECT_ROOT}/.agent/skills/requirement-generator/SKILL.md
```

This gives you:
- Module keyword dictionary (maps words → module brain paths)
- Task type classification rules
- User role mappings for this ERP domain
- Worked examples showing expected output quality and depth

### Step 2: Parse the Task Description [Antigravity]

// turbo
Extract these elements from the raw `{TASK_DESCRIPTION}`:

| Extract | How | Example |
|---|---|---|
| **Action verbs** | What is being requested? | add, modify, remove, show, hide, calculate, report, fix, export |
| **Module keywords** | Which system area? | bill, tag, estimate, report, stock, customer, payment, scheme |
| **Entity references** | Which data entities? | Table names, field names, form names, report names |
| **UI references** | Which screens/forms? | "billing form", "print copy", "payment modal", "report page" |
| **Business context** | Why is this needed? | Compliance, client request, missing feature, incorrect behavior |

From these extractions, determine:
1. **Primary Module** — The module most central to this task
2. **Secondary Modules** — Other modules that may be affected
3. **Task Type** — One of: `New Feature` | `Enhancement` | `Bug Fix` | `Report Change` | `Configuration` | `Integration`

Use the keyword → module mapping from SKILL.md to classify. If ambiguous, list the top 2-3 candidates and pick the most specific one.

### Step 3: Deep-Dive into Primary Module Brain [Antigravity]

// turbo
Read the following brain files for the **primary module** (in this order):

1. **`knowledge_brain/{MODULE}/MODULE_BRAIN.md`**
   - Extract: Architecture overview, entry points, key tables, constructor dependencies
   - Focus on: Routes and methods relevant to the task keywords

2. **`knowledge_brain/{MODULE}/BUSINESS_RULES.md`**
   - Extract: Existing rules that relate to the task
   - Note: Rules that would be affected or need new additions

3. **`knowledge_brain/{MODULE}/DATA_FLOW.md`**
   - Extract: Current CRUD flows for the relevant feature
   - Note: Where in the flow the task would insert/modify behavior

4. **`knowledge_brain/{MODULE}/METHOD_INDEX.md`**
   - Search for: Methods that handle the relevant feature
   - Extract: Method names, line numbers, tables read/written

5. **`knowledge_brain/{MODULE}/SCHEMA_ANALYSIS.md`**
   - Extract: Table structures for tables that will be affected
   - Note: Existing columns, types, indexes — and what new columns might be needed

6. **`knowledge_brain/{MODULE}/FLOW_RISK_MATRIX.md`** (if exists)
   - Check: Does this task touch any handoff contracts or reversal flows?
   - Flag: Any QA scenarios that need updating

> **If the primary module brain does NOT exist**: State this clearly in the output — "⚠️ Module brain not built for {MODULE}. The requirement below is based on limited analysis. For a more detailed and accurate requirement, ask the dev team to run `/build-module-brain` first."
> Then proceed with whatever brain files ARE available for other modules. Use only `view_file` to read any source code files directly — do NOT run terminal commands.

### Step 4: Cross-Module Impact Analysis [Antigravity]

// turbo
Read system-level brain files to identify downstream impacts:

1. **`knowledge_brain/{MODULE}/CROSS_MODULE_MAP.md`**
   - Identify: Which external modules read from or write to the affected tables
   - Flag: Any cross-module AJAX calls that involve the affected feature

2. **`knowledge_brain/_SYSTEM/MODULE_DEPENDENCIES.md`**
   - Check: The dependency matrix row for the primary module
   - Identify: Which modules depend on the primary module (the "Depended On By" column)

3. **`knowledge_brain/_SYSTEM/SHARED_TABLES.md`**
   - Check: Are any of the affected tables shared across modules?
   - If YES: List all modules that read/write the shared table

4. **`knowledge_brain/_SYSTEM/DANGER_ZONES.md`** (if exists)
   - Check: Does this task involve any danger zone area?
   - If YES: Flag with ⛔ warning

5. **For each secondary module identified**: Read its `MODULE_BRAIN.md` overview section (just the entry points and key tables — NOT the full deep-dive)

### Step 5: Analyze for User Story and Acceptance Criteria [Antigravity]

// turbo
Based on everything gathered:

1. **Identify the user role** — Use the role mapping from SKILL.md:
   - Who will USE this feature? (Counter Staff, Branch Manager, Admin, Accountant, etc.)
   - Who will CONFIGURE it? (Admin, IT)
   - Who will SEE the output? (Management, Auditor, Customer)

2. **Formulate the user story**:
   - Action = what the user needs to do
   - Benefit = the business value (NOT "so that it works" — actual business benefit)

3. **Generate acceptance criteria**:
   - Each criterion must be **testable** — a human can verify it passed or failed
   - Include: happy path, edge cases, validation rules, print/display checks, report impacts
   - If the task touches financial data: include a calculation verification criterion
   - If the task touches cross-module flows: include a cross-module verification criterion

### Step 6: Output Structured Requirement [Antigravity]

Present the complete requirement in the following format. Each section must be clearly separated so the support team can copy each one independently.

**IMPORTANT OUTPUT RULES**:
- Use clear section headers with emoji markers for visual scanning
- Keep language **business-friendly** — avoid raw code references in Category, Task, User Story
- Include code/file references ONLY in Observation and Impacts (developers need them)
- Acceptance criteria must be written so a non-developer tester can verify them

---

#### Output Template:

```
📋 CATEGORY
═══════════════════════════════════════════
{Module Name} — {Sub-module / Feature Area}
Type: {New Feature | Enhancement | Bug Fix | Report Change | Configuration | Integration}


📋 TASK
═══════════════════════════════════════════
{Clean, specific, one-line task title — what needs to be done}


📋 USER STORY
═══════════════════════════════════════════
As a **{role}**, I want to **{specific action}**, so that **{business benefit}**.

{If multiple user stories are needed for different roles, list up to 3}


📋 OBSERVATION
═══════════════════════════════════════════
### Current System Behavior
{What exists today — describe how the system currently handles the relevant area.
Include: which screen, which flow, what data is captured/displayed currently.}

### Relevant Existing Features
{Features that already exist and relate to this task.
Reference specific business rules from BUSINESS_RULES.md if applicable.}

### Technical Context (for dev team)
- **Controller**: `{file}` — {relevant methods and line ranges}
- **Model**: `{file}` — {relevant methods}
- **JS**: `{file}` — {relevant functions}
- **Views**: `{file(s)}`
- **Tables**: `{table(s)}` — {relevant columns}

### Current Limitations / Gaps
{What the current system does NOT do that this task addresses.
Why does this task need to be done?}


📋 IMPACTS
═══════════════════════════════════════════
### Primary Module: {Module Name}
| Component | File | What Changes |
|---|---|---|
| Controller | `{filename}` | {what needs to be added/modified} |
| Model | `{filename}` | {what needs to be added/modified} |
| JS | `{filename}` | {what needs to be added/modified} |
| View(s) | `{filename(s)}` | {what needs to be added/modified} |
| DB Table(s) | `{table(s)}` | {new columns / altered queries} |

### Cross-Module Impacts
| Affected Module | Why | What Changes | Risk Level |
|---|---|---|---|
| {module} | {reason} | {what changes} | {🟢 Low / 🟡 Medium / 🔴 High} |

### Settings / Configuration
{Any new ret_settings entries needed, or existing settings that control this feature}

### Print / Report Impacts
{Any bill print formats, reports, or exports affected}

{If DANGER_ZONES.md flagged anything:}
### ⛔ Danger Zone Warning
{Description of the risk area and why extra care is needed}


📋 ACCEPTANCE CRITERIA
═══════════════════════════════════════════
- [ ] AC-1: {Testable condition — happy path}
- [ ] AC-2: {Testable condition — edge case}
- [ ] AC-3: {Testable condition — validation/error handling}
- [ ] AC-4: {Testable condition — display/print verification}
- [ ] AC-5: {Testable condition — report impact verification}
{Add more as needed — aim for 5-10 criteria per task}
```

---

## Handling Edge Cases

### If no module brain exists for the identified module:
```
⚠️ No Module Brain found for "{MODULE_NAME}".
Falling back to direct codebase analysis.
For deeper, more accurate requirement generation, build the brain first:
→ Run: /build-module-brain {MODULE_NAME}
```
Then use `view_file` to read the relevant controller/model/JS files directly. Do NOT run any terminal commands.

### If the task spans 3+ modules:
List all modules in Category, create one combined Observation section, but separate the Impacts table into per-module subsections.

### If the task description is too vague:
Ask the reporter for clarification BEFORE generating output:
```
❓ CLARIFICATION NEEDED
The task description mentions "{vague_term}" which could mean:
1. {interpretation_1}
2. {interpretation_2}
3. {interpretation_3}
Which interpretation is correct? (Or provide more detail)
```

### If the task is a pure configuration change (no code):
Skip the Impacts → Primary Module table. Only show the Settings/Configuration section.

---

## Completion Report

After outputting all 6 sections:

```
✅ Requirement generated for: {Task Title}
   Module: {Primary Module}
   Type: {Task Type}
   Impact: {N} modules affected
   Criteria: {N} acceptance criteria
   Brain sources: {list of brain files consulted}
   
   Each section above can be copied independently.
   For deeper analysis, ensure module brain coverage ≥ 80%.
```
