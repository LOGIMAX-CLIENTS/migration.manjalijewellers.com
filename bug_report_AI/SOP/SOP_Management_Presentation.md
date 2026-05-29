# SOP: Building a Module Brain — A Structured Approach to ERP Mastery

**For:** Team Leads & Management  
**Date:** 18 February 2026  
**Purpose:** Reusable methodology — apply this to any module, any project

---

## What is a "Module Brain"?

A Module Brain is a structured knowledge system that gives your team **complete mastery** over a module — every calculation, every data flow, every rule, every dependency — documented and verified.

We built the first Module Brain for the **Billing Engine**. This SOP explains the **7 core components** of the brain and how your team can replicate this process for any module.

---

## The 7 Core Components

```
┌─────────────────────────────────────────────────┐
│              THE MODULE BRAIN                    │
│                                                  │
│  1. Project Skeleton ──── Structure              │
│  2. Engine Reverse Engineering ──── Flow          │
│  3. Canonical Financial Rules ──── Truth          │
│  4. Cross-Module Mapping ──── Dependencies        │
│  5. Invariant Matrix ──── Behaviour              │
│  6. DB Truth Protocol ──── Verification          │
│  7. Forensic Framework ──── Investigation        │
│                                                  │
└─────────────────────────────────────────────────┘
```

---

## Component 1: Project Skeleton

### What It Is

A structural X-ray of the module. Shows every file, its role, and how they connect — like the blueprint of a building before you start renovating.

### What It Answers

- Which files make up this module?
- What does each file do?
- Where does the Controller end and the Model begin?
- What's the entry point? What's the exit?

### How To Build It

1. List every file the module touches (Controller, Model, View, JS, Config)
2. For each file, write a one-line purpose
3. Draw the connection: Browser → JS → Controller → Model → DB → View
4. Note the file sizes — the largest file is usually the most complex and risky

### The Core Idea

> **You cannot fix what you cannot see.** The skeleton makes the invisible visible. Every team member — senior or junior — opens this document and immediately knows where everything lives.

---

## Component 2: Engine Reverse Engineering

### What It Is

A complete trace of how data flows through the module — from the first user click to the final output. Not what the code _should_ do, but what it _actually_ does.

### What It Answers

- When a user clicks "Save," what happens step by step?
- Which function calls which?
- What data is sent from browser to server?
- What gets written to the database, and in what order?
- What triggers the output (print, email, report)?

### How To Build It

1. Pick the primary action (e.g., "Save a Bill")
2. Start at the browser — what JS function fires?
3. Follow the AJAX call to the Controller — which function handles it?
4. Inside the Controller — trace every Model call, every DB insert/update
5. Follow the response back — what does the browser do with the result?
6. Document the complete chain with line numbers

### The Core Idea

> **Reverse engineering turns tribal knowledge into team knowledge.** The senior developer who built this 5 years ago may leave tomorrow. This document ensures the knowledge stays.

---

## Component 3: Canonical Financial Rules

### What It Is

A formal rulebook defining what "correct" means for this module. Not the code — the **business truth** that the code must follow.

### What It Answers

- What is the correct formula for the bill total?
- What tax rules apply and when?
- What are the limits and constraints? (credit limits, advance limits)
- What must happen when a transaction is cancelled?
- What constitutes a duplicate?

### How To Build It

1. Extract every calculation from the code
2. Write each calculation as a plain-English rule with a formula
3. Define the tolerance (e.g., ±₹1 for rounding)
4. Define the enforcement (reject? log? alert?)
5. Get the rules **validated by the business team** — not just developers

### The Core Idea

> **Rules exist whether you write them down or not.** The difference is: unwritten rules change with every developer. Written rules become the law. When a bug is reported, you check: "Which rule was violated?" — and the fix becomes obvious.

---

## Component 4: Cross-Module Mapping

### What It Is

A dependency map showing how this module interacts with every other module in the system. Billing doesn't exist in isolation — it touches Inventory, Accounts, Schemes, Advances, and more.

### What It Answers

- Which other modules send data into this module?
- Which modules receive data from this module?
- What breaks in Module B if Module A sends wrong data?
- Where are the handshake points?

### How To Build It

1. From the Engine Reverse Engineering, identify every external table or function called
2. Group them by module (Inventory, Accounts, Schemes, etc.)
3. For each module, document: What data comes in? What data goes out?
4. Flag the dangerous connections — where bad data in one module corrupts another

### The Core Idea

> **Most "billing bugs" aren't billing bugs.** They're data handshake failures between modules. Without this map, developers search in the wrong module for days. With it, they trace the data to the exact handshake point in minutes.

---

## Component 5: Invariant Matrix

### What It Is

A behaviour grid showing how each variant (bill type, transaction type, user role) behaves differently across key dimensions. One module, many behaviours.

### What It Answers

- How does a Sales bill differ from a Purchase bill in calculation?
- Which bill types affect stock? Which don't?
- Which payment modes are valid for which scenarios?
- What happens on cancellation for each type?

### How To Build It

1. List every variant (e.g., 13 bill types)
2. Define the key dimensions (Stock Effect, Deductions, Payment Modes, Cancel Logic)
3. Fill the grid — one row per variant, one column per dimension
4. Highlight exceptions and edge cases

### The Core Idea

> **The matrix prevents cross-variant regression.** When you fix a bug in Sales bills, the matrix tells you: "Does this change also affect Purchase bills? Order Delivery bills?" If yes, you test those too. If no, you're safe. Without the matrix, every fix is a gamble.

---

## Component 6: DB Truth Protocol

### What It Is

A verification method that queries the database directly to prove what's actually stored — independent of what the screen shows or the print displays.

### What It Answers

- What does the database actually say for this transaction?
- Does the stored value match what the user sees?
- Does the print output match the database?
- Where exactly is the mismatch — in the query, the variable, or the template?

### How To Build It

1. Write a simple script that takes a transaction ID and pulls every related record
2. Display: Header values, Line item sum, Payment sum, Linked records (advance, chit, etc.)
3. Calculate the expected total using the Canonical Rules
4. Compare: DB Total vs Rule-Based Total vs Screen Display vs Print Output

### The Core Idea

> **The database is the only truth.** Screens can lie (JavaScript can miscalculate). Prints can lie (templates can re-sum incorrectly). But the database stores what was committed. This protocol settles every dispute in seconds: _"The database says ₹4,100. The print says ₹0. The bug is in the print layer."_

---

## Component 7: Forensic Framework

### What It Is

A structured investigation method for tracing any reported discrepancy back to its root cause — not by guessing, but by following the data.

### What It Answers

- Where did the data go wrong?
- Was it wrong at input (JS), processing (Controller), storage (Model), or display (View)?
- Is this a one-time issue or a systemic pattern?

### How To Use It

1. **Identify the Invariant Breach:** Which Canonical Rule was violated?
2. **Run DB Truth Protocol:** Get the actual stored values
3. **Layer-by-Layer Trace:**
   - Was the JS calculation correct? → Check browser console
   - Was the Controller logic correct? → Check the submitted vs processed data
   - Was the Model query correct? → Check the SQL output
   - Was the View correct? → Check the template variables
4. **Classify:** Is this a View bug, Controller bug, Model bug, or JS bug?
5. **Fix the exact layer** — never touch other layers

### The Core Idea

> **Forensics eliminates guessing.** Traditional debugging: "Let me try changing this variable and see if it works." Forensic debugging: "The data was correct at the Controller. The data was correct in the database. The View template reads variable X instead of variable Y. Fix: Change the View template." No guesswork. No regressions.

---

## How To Apply This to Any Module

| Step | Action                                                  | Output                        |
| :--- | :------------------------------------------------------ | :---------------------------- |
| 1    | Pick the target module                                  | Module name                   |
| 2    | Build the **Project Skeleton**                          | File map + connection diagram |
| 3    | Trace the primary flow → **Engine Reverse Engineering** | End-to-end data flow document |
| 4    | Extract every rule → **Canonical Rules**                | Formal rulebook               |
| 5    | Map external dependencies → **Cross-Module Map**        | Dependency diagram            |
| 6    | Grid all variants → **Invariant Matrix**                | Behaviour comparison table    |
| 7    | Create verification scripts → **DB Truth Protocol**     | Diagnostic tools              |
| 8    | Investigate real cases → **Forensic Framework**         | Root cause reports            |

**Estimated time per module:** 5–7 working days for a complex module (Billing-scale). 2–3 days for a simpler module.

---

## The Result

When a Module Brain is complete, your team can:

- **Onboard** a new developer in hours, not weeks
- **Diagnose** any reported bug in hours, not days
- **Fix** with surgical precision — exact file, exact line, exact variable
- **Prevent regressions** — the matrix and rules catch side effects before deployment
- **Scale** — apply the same process to Estimation, Reports, Inventory, or any module next

> **One sentence summary:** We don't just fix bugs. We build the brain that prevents them.
