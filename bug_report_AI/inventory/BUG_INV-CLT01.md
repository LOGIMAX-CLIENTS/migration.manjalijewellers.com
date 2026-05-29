## INV-CLT01 — Stock Issue Database Error & Missing Display

| Field         | Value                         |
| ------------- | -------------                 |
| Severity      | P1                            |
| Track         | A (System)                    |
| Category      | 3 (Database)                  |
| Sprint        | Sprint 1                      |
| Pattern Match | Novel                         |
| Module Brain  | ❌ Needs build                |
| Reporter      | Client (via Chat)             |
| Source        | CLT (Client)                  |

### Steps to Reproduce

1. Open Stock Issue List in `etail_development_src`.
2. Observe that Non-Tagged items do not show "Tag Code" or "Category Name".
3. Attempt to print a Stock Issue where an item has no `cat_id` (e.g., corrupted data or specific flow).
4. System returns "Database Error" due to invalid SQL.

### Expected Behavior

- Stock Issue List should show product/category details for both Tagged and Non-Tagged items.
- Printing should handle missing `cat_id` gracefully without SQL syntax errors.

### Actual Behavior

- SQL Syntax Error: `WHERE p.cat_id =` (missing value).
- List shows '-' or empty for Non-Tagged items because only `ret_taging` is joined in the subquery.

### Evidence
Reported in previous chat context.
Original Fix: [3654c4bf-3e2f-4ec7-b938-14c057ca7bba](file:///C:/Users/admin/.gemini/antigravity/brain/3654c4bf-3e2f-4ec7-b938-14c057ca7bba/walkthrough.md)
