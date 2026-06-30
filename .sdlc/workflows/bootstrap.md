# Workflow: Conversation Bootstrap

> Auto-invoked at the start of EVERY conversation when `.sdlc/` exists.

## Step 1: Get Current State

```bash
python .sdlc/engine/cli.py what-next
```

- If **IDLE** → run `sdlc start` with user's request details. It auto-shows Step 1 instruction.
- If **active task** → follow the instruction shown.

## Step 2: Follow the Instruction

- Execute whatever `what-next` says
- Produce the deliverable
- Call `step-done`
- Call `what-next` again
- Repeat until human checkpoint

## ⛔ What NOT To Do

- Do NOT read system brain docs before starting a task
- Do NOT search recipes before starting a task
- Do NOT grep source code before starting a task
- Do NOT investigate the bug before starting a task
- Everything happens INSIDE the pipeline. Start the task FIRST.
