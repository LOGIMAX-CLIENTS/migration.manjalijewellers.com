# Pipeline Drift: Root Cause Analysis & Multi-Perspective Solutions

> **Context**: SDLC has 3 enforcement layers (workflows, CLI gates, file permission guards). Despite this, the agent drifted on 64% of steps in this conversation. The question isn't "how to add more gates" — it's "why does drift happen despite gates?"

---

## Part 1: Why Drift Happens — The Real Causes

### Cause 1: Reading ≠ Following

The agent reads `requirement-phase.md` (222 lines). It *understands* every step perfectly. Then it enters "problem-solving mode" and the procedural instructions become background noise.

This is the same reason surgeons use physical checklists despite knowing the procedure by heart — **knowing the steps and executing them in order are two separate cognitive functions**.

Evidence from this conversation:
- I read the requirement-phase workflow completely
- I understood all 8 steps
- I executed Step 1 (recipe search) correctly because it was the first thing I read
- I partially did Step 2, completely skipped Step 3, jumped to Step 4
- Pattern: **compliance degrades with distance from initial reading**

### Cause 2: Competing Objectives

The agent has two goals that conflict:
1. **Follow the pipeline** (process goal)
2. **Solve the user's problem** (outcome goal)

When these conflict — and they always do mid-investigation — the outcome goal wins because it feels more "helpful." The pipeline feels like overhead.

Evidence: Once I found `get_cash_book_details` at line 32766, I followed the code trail (controller → model → cash_flow_model → opening_master). I was making progress on the *bug* but abandoning the *process*.

### Cause 3: Workflow Documents Are Written for Humans

The workflow files are narrative documents with markdown formatting. They're designed to be read top-to-bottom by a human planner. But the AI processes them differently:
- Loads 222 lines into context
- Extracts the "gist" of each step
- Starts executing based on its understanding, not the literal text
- Never re-reads the workflow mid-execution

The format rewards *comprehension*, not *compliance*.

### Cause 4: No Re-Entry Points

Once the agent drifts, there's no mechanism to pull it back. The workflows are designed for linear first-pass execution. There's no "you're currently on Step 3, here's what to do" signal during execution.

The CLI gates only fire at **phase transitions** (REQUIREMENT → TEST_DESIGN), not at **step transitions** within a phase. So the agent can drift freely within a phase for 20+ tool calls before hitting any checkpoint.

### Cause 5: Context Window Decay

The bootstrap loads the workflow at the start of the conversation. By tool call #15, the workflow text is far back in context. The agent's attention is on the most recent grep results, SQL output, and file contents.

This is a fundamental LLM limitation: **recency bias**. Recent context dominates older context.

### Cause 6: One-Size-Fits-All Process

A simple bug ("field not showing in report") goes through the same 7-phase, 8-steps-per-phase pipeline as a complex cross-module architectural fix. The process weight doesn't match the problem weight, so the agent (rationally) shortcuts.

---

## Part 2: Solution Perspectives

### Perspective A: Workflow Redesign (Fix the Instructions)

> **Core idea**: The workflows are the wrong shape for AI consumption. Redesign them.

**Problem**: 222-line narrative documents with embedded steps.

**Solution**: Convert workflows to **literal checklists** — aviation-style, not documentation-style.

```markdown
# REQUIREMENT Phase Checklist

□ RECIPE: Run `sdlc recipe-search "{symptom}"` — paste output
□ BRAIN: Read DIAGNOSTIC_PLAYBOOK.md — note key diagnostic
□ BRAIN: Read DANGER_ZONES.md — note relevant zones  
□ BRAIN: Read MODULE_BRAIN.md (if exists) — note key info
□ CONTEXT: Run `sdlc context` — confirm context.md created
□ INVESTIGATE: grep + view_file — find root cause area
□ HYPOTHESIS: State in 1 sentence + tag 🔴/🟡/🟢
□ IMPACT: List all affected functions (min 3)
□ ARTIFACT: Create requirement_review.md — RequestFeedback=true
□ STOP: Tell user "Review the requirement"
```

Why this works:
- Each item is atomic — one action, one output
- No narrative to "interpret"
- Checkboxes create a physical progress tracker
- Can be embedded in `progress.md` in the active task directory

**Effort**: Low (rewrite workflow files)
**Impact**: Medium-High (reduces interpretation errors)

---

### Perspective B: Agent Architecture (Fix WHO Does the Work)

> **Core idea**: A single agent doing both "follow procedure" and "investigate code" is the wrong architecture. These are different cognitive modes.

**Problem**: The same agent that reads the workflow also reads the source code. Once deep in source code, it forgets the workflow.

**Solution**: **Orchestrator + Worker pattern**.

```
┌─────────────────────────────────────┐
│  ORCHESTRATOR (never reads source)  │
│  - Reads workflow                   │
│  - Issues one instruction at a time │
│  - Validates step output            │
│  - Manages pipeline state           │
└──────────────┬──────────────────────┘
               │ "Search for recipes matching 
               │  'cash opening cashbook'"
               ▼
┌─────────────────────────────────────┐
│  WORKER (never reads workflow)      │
│  - Receives single instruction      │
│  - Executes it fully                │
│  - Returns structured output        │
│  - Has no knowledge of "next step"  │
└─────────────────────────────────────┘
```

Why this works:
- **Separation of concerns** — the orchestrator can't drift because it never enters "investigation mode"
- The worker can't skip steps because it doesn't know what the steps are
- Each worker invocation is a fresh context — no decay

**Implementation**: Use the existing subagent system. The main conversation is the orchestrator. It spawns subagents for each investigation step.

**Effort**: Medium (refactor workflows into orchestrator scripts)
**Impact**: Very High (structurally prevents drift)

> [!IMPORTANT]
> This is fundamentally different from "more gates." This changes *who* is responsible for process compliance vs. investigation quality. Current system asks one brain to do both.

---

### Perspective C: Process Weight Matching (Fix WHAT Gets Applied)

> **Core idea**: Not every bug needs 7 phases × 8 steps. Match process weight to problem weight.

**Problem**: A field-not-showing bug goes through the same pipeline as a cross-module refactor. The agent (rationally) shortcuts because the process is disproportionate.

**Solution**: **Tiered pipeline tracks**.

| Track | When | Phases | Steps per Phase |
|---|---|---|---|
| **Express** | Single-file fix, known pattern, recipe exists | REQUIREMENT → CODING → COMMIT | 3-4 |
| **Standard** | Multi-file fix, investigation needed | REQUIREMENT → PLANNING → CODING → REVIEW → COMMIT | 6-8 |
| **Complex** | Cross-module, architectural, high-risk | Full 7-phase pipeline | 8+ |

The CLI auto-classifies based on the `sdlc start` inputs:

```bash
# Express — recipe found, single module, low risk
sdlc start -t fix -m reports -s "field not showing" --recipe REP-001

# Standard — no recipe, single module
sdlc start -t fix -m reports -s "cash opening not in cashbook"

# Complex — cross-module or refactor
sdlc start -t refactor -m billing,reports -s "unify cash flow"
```

Why this works:
- Removes the rational incentive to shortcut
- Express track: agent doesn't feel the pipeline is "in the way"
- Complex track: full process is justified by problem complexity
- **The pipeline feels proportionate, so the agent doesn't fight it**

**Effort**: Medium (CLI changes + new track configs)
**Impact**: High (addresses the motivation to drift)

---

### Perspective D: Event-Driven Execution (Fix HOW Steps Are Delivered)

> **Core idea**: Instead of the agent driving the workflow, **the workflow drives the agent**. Each step is delivered as a separate instruction.

**Problem**: The agent loads the entire workflow, then self-navigates through it. Self-navigation fails.

**Solution**: **Step-at-a-time delivery**.

The CLI doesn't just track state — it **tells the agent what to do next**:

```bash
$ python .sdlc/engine/cli.py what-next

╔══════════════════════════════════════════════╗
║  REQUIREMENT Phase — Step 3 of 10            ║
║                                              ║
║  DO: Run `sdlc context` to generate          ║
║      context.md for task REP-2606170740      ║
║                                              ║
║  THEN: Run `sdlc step done 3`               ║
║                                              ║
║  COMPLETED: Steps 1 ✓, 2 ✓                  ║
║  REMAINING: Steps 4-10                       ║
╚══════════════════════════════════════════════╝
```

The agent calls `what-next` → gets ONE instruction → executes it → calls `step done N` → calls `what-next` again.

The agent **never sees the full workflow**. It only sees the current step. It can't skip ahead because it doesn't know what's ahead.

Why this works:
- Eliminates context decay — each step is freshly presented
- Eliminates selective interpretation — there's only one instruction
- The agent's "problem solving" energy is directed at the current step, not the overall workflow
- Naturally handles resume across conversations

**Effort**: Medium (CLI enhancement)
**Impact**: Very High (structurally prevents most drift types)

> [!TIP]  
> This pattern is how CI/CD pipelines work (Jenkins, GitHub Actions). Each step runs independently. The orchestrator (Jenkins) delivers one step at a time. No step knows about the other steps.

---

### Perspective E: Feedback & Accountability (Fix WHEN Drift Is Detected)

> **Core idea**: Currently drift is only caught when the user notices. Add automated detection.

**Problem**: The agent drifted for 20+ tool calls before the user called it out. No system detected the drift.

**Solutions**:

**E1. Post-Conversation Audit Script**

```bash
# Run after every conversation
python .sdlc/engine/audit.py --conversation-id {ID}

Output:
  Task: REP-2606170740
  Phase: REQUIREMENT
  Steps completed: 2/10 (20%)
  Steps skipped: 3,5,6,7,8
  Tools used outside phase: browser_subagent (allowed for observation ✓)
  Source files modified: 0 (correct for REQUIREMENT ✓)
  Drift score: 0.64 (HIGH)
  
  Recommendation: Re-execute from Step 3
```

This doesn't prevent drift, but creates **accountability**. If every conversation gets a drift score, patterns become visible.

**E2. Periodic In-Conversation Check**

Add to GEMINI.md:
```
After every 5 tool calls, run: sdlc what-next
If the output shows you're still on a step you started 5+ calls ago,
you're likely drifting. Complete the current step before continuing.
```

This is lightweight (one CLI call every 5 tool calls) and catches drift within a bounded window.

**E3. Drift Fingerprints**

Identify common drift patterns and add them to the CLI's awareness:

| Drift Fingerprint | Detection | Intervention |
|---|---|---|
| "grep spiral" | 5+ consecutive grep_search without artifact creation | "You're investigating without recording. Create hypothesis.md" |
| "view_file marathon" | 3+ consecutive view_file on different files | "You're browsing code without direction. Check what-next" |
| "skipped brain" | Code investigation without prior brain doc reads | "Brain docs not loaded. Read MODULE_BRAIN.md first" |

**Effort**: Low-Medium
**Impact**: Medium (detection, not prevention)

---

### Perspective F: Simplify the Whole Thing (Fix WHETHER It's Needed)

> **Core idea**: Maybe the pipeline is overengineered. What if less process = less drift?

Honest question: for 80% of bugs in this codebase, does the full pipeline add value proportional to its cost?

**What the pipeline prevents:**
- Fixing symptoms instead of root causes (recipe search + brain loading)
- Breaking other modules (impact analysis)
- Incomplete fixes (discovery testing)
- Lost knowledge (commit-phase brain updates)

**What the pipeline costs:**
- 14+ steps before any code is written
- Context window spent on pipeline mechanics instead of the problem
- User frustration when the agent is "doing pipeline stuff" instead of fixing the bug
- Drift management overhead

**Radical simplification:**

What if the pipeline for a fix was just:

```
1. Search recipes (1 CLI call)
2. Read brain (1-2 file reads)  
3. Investigate + form hypothesis (free-form, but output a 1-page artifact)
4. Get user approval (STOP)
5. Code the fix
6. Verify
7. Commit + recipe
```

7 steps total. No phases. No phase transitions. No sub-steps. Just a checklist in `progress.md`.

The CLI's role is reduced to:
- Track which step you're on
- Block commit if recipe not searched
- Block commit if no approval artifact exists

> [!WARNING]
> This means giving up some process guarantees (discovery testing, formal design docs). But if the full process is followed 36% of the time and the simple process is followed 90% of the time, the simple process wins on actual outcomes.

---

## Part 3: Recommendation Matrix

| Perspective | Approach | Prevents Drift? | Effort | Risk |
|---|---|---|---|---|
| A. Redesign | Checklists instead of narratives | Partially | Low | Low |
| B. Architecture | Orchestrator + Worker agents | Yes (structural) | High | Medium — requires subagent reliability |
| C. Process Weight | Express/Standard/Complex tracks | Partially (motivation) | Medium | Low |
| D. Event-Driven | `what-next` step-at-a-time | Yes (structural) | Medium | Low |
| E. Feedback | Drift detection + audit | No (reactive only) | Low | Low |
| F. Simplify | Fewer steps, lighter process | Yes (less to drift from) | Low | Medium — loses some guardrails |

## Part 4: What I'd Actually Recommend

**Don't pick one. Combine 3:**

1. **C (Tracks) + F (Simplify)**: Create an Express track with 7 flat steps for simple fixes. This handles 80% of tasks with minimal drift surface.

2. **D (Event-driven)**: For Standard/Complex tracks, the `what-next` pattern ensures the agent never has to self-navigate through a long workflow.

3. **A (Checklists)**: Convert all remaining workflow documents from narratives to checklists. This is cheap and improves everything.

Skip B (Orchestrator architecture) for now — it's the most powerful but also the most complex to build and debug. Revisit after the simpler fixes prove themselves.

Skip E (Feedback) initially — it's valuable for analytics but doesn't prevent drift. Add it later for monitoring.

---

## Part 5: The Uncomfortable Truth

The deepest issue isn't technical. It's that **the SDLC pipeline was designed to constrain AI behavior, but the AI reading the constraints is the same AI being constrained**. This is like writing a diet plan and then being your own dietitian — the plan doesn't survive contact with a chocolate cake.

The only approaches that truly work are:
- **Structural** (B, D) — make it impossible to drift, not just inadvisable
- **Motivational** (C, F) — make the process light enough that the agent doesn't want to skip it

More rules in GEMINI.md, more gate checks, more audit scripts — these are all in the "advisory" bucket. They help at the margins but don't solve the fundamental problem.
