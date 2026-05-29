# Standard Operating Procedure (SOP)
## AI Agent-Led Business Logic Analysis and Defect Remediation

### 1. Purpose
The purpose of this document is to define a standardized framework for the AI Agent-driven analysis, validation, and correction of business logic within the scheme management and payment modules. This procedure ensures data integrity, regulatory compliance, and system stability through a structured, artifact-driven methodology.

### 2. Scope
This SOP applies to all AI processing workflows involving:
*   Semantic decoding of system configurations.
*   Business logic failure analysis and risk assessment.
*   Surgical code remediation for legacy systems.
*   Automated unit testing and regression verification.
*   Documentation of analysis and execution results.

### 3. System Overview
The system is a multi-tier enterprise platform managing complex financial schemes (e.g., gold plans, installment-based savings). It relies on a metadata-driven architecture where database configurations dictate operational behavior across UI, validation, and transaction layers. The AI Agent acts as a specialized auditor and developer, bridging the gap between raw database state and intended business rules.

### 4. Inputs & Dependencies
Operational execution requires the following artifacts:
*   **Metadata Dictionary (`metadata_dictionary.json`):** The definitive source for semantic mapping of database columns, types, and enum values.
*   **Scheme Configuration Snapshot:** Raw data from the `scheme_master` and related tables.
*   **Application Source Code:** Specifically controller logic (e.g., `SaveAll`) and model-level calculations (e.g., `payment_model.php`).
*   **Legacy Reports:** Previous analysis and fix reports to maintain context and avoid regression.

### 5. Operational Workflow
The AI Agent operates in a cyclic, four-phase lifecycle:
1.  **Phase I: Discovery & Decoding:** Read raw configuration and map it to semantic meanings using the Metadata Dictionary.
2.  **Phase II: Logic Analysis:** Cross-reference decoded rules against established business constraints and code-path traces to identify risks.
3.  **Phase III: Remediation:** Execute surgical code fixes targeting specific "Flow Logic Risks" without altering original system architecture.
4.  **Phase IV: Validation & Reporting:** Generate and run unit tests to confirm fixes, followed by the production of formal Execution Reports.

### 6. Analysis Procedure
Analysis must be conducted with three distinct levels of scrutiny:
*   **Configuration Validation:** Confirming if parameters are internally consistent (e.g., Maturity Installments matching Total Installments).
*   **Contradiction Detection:** Identifying mutually exclusive settings (e.g., an "Amount-to-Weight" scheme with "Weight Conversion" disabled).
*   **Flow Trace Analysis:** Simulating the code path from UI load (`ajax/account`) through Validation (`Pre-save`) to Execution (`SaveAll`) to find logical gaps.

### 7. Bug Management Procedure
Bugs identified during analysis are processed as follows:
*   **Identification:** Assigned a unique ID (e.g., `BUG-S31-001`) and a severity rating (CRITICAL, HIGH, MEDIUM, LOW).
*   **Categorization:** Classified into Configuration-level defects (requiring business decisions) or Code-level defects (requiring remediation).
*   **Remediation Priority:** Focus on "Stability > Elegance." Surgical insertions (guards, caps, fallbacks) are preferred over refactoring.
*   **Traceability:** Every code change must be linked back to a specific Bug ID in the final execution report.

### 8. Testing & Validation Procedure
Testing follows a "Scenario-Based" methodology:
*   **Failure Scenario Design:** Creating specific test cases based on "Realistic Failure Scenarios" identified during analysis (e.g., 13th installment attempt).
*   **Unit Test Suite:** Implementation of PHPUnit tests that mock the database state and environment variables to isolate logic.
*   **Assertion Requirements:** Tests must verify both the "Fixed" behavior and the "Handled" state for edge cases.
*   **Regression Verification:** Running the full suite of previous scheme tests to guarantee no side effects on cross-scheme logic.

### 9. Artifact & File Handling
Artifacts are the primary communication medium between agents and human stakeholders:
*   **Analysis Reports (`*_ANALYSIS_REPORT_*.md`):** Documents the "Current State," risks, and proposed test designs.
*   **Fix Reports (`*_FIX_EXECUTION_REPORT_*.md`):** Documents the "Changes Made," syntax verification, and test results.
*   **Naming Convention:** All reports must include the Scheme ID and Date for auditability.
*   **Storage:** Reports are maintained within the project root or a designated `docs/` directory for version control integration.

### 10. Exception Handling
*   **Ambiguous Configurations:** If a setting is "Unknown" or its intent is unclear (e.g., `null` in a required field), it must be flagged for "Business Validation" rather than corrected by the agent.
*   **Conflict Deferral:** If a fix for one scheme risk would likely break another scheme's logic, the agent must defer the fix and request human review.
*   **Notification Failures:** Third-party failures (e.g., SMS/Email timeouts) during transactions must be handled via `try/catch` to ensure the core DB transaction is not rolled back.

### 11. Operational Constraints
*   **Legacy Safety:** Never rename variables, alter DB schemas, or delete existing code blocks unless explicitly instructed.
*   **Tool Agnosticism:** Procedures should focus on logical outcomes, ensuring they can be performed via manual audit or AI automation.
*   **Non-Storytelling:** Reports and SOPs must adhere to a clinical, technical tone focused on data and results.

### 12. Best Practices
*   **Atomic Transactions:** Ensure multi-installment operations are wrapped in a single database transaction level.
*   **Boundary Checking:** Always use inclusive operators (`<=`, `>=`) for date and amount boundaries unless business rules specify otherwise.
*   **Surgical Precision:** When fixing code, use the smallest possible footprint to minimize the "blast radius" of the change.
*   **Documentation First:** No code changes should be made before the corresponding logic analysis is completed and documented.
