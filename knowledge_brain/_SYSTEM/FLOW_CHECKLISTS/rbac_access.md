# Flow Checklist: RBAC / Access Control

> **Last Updated:** 2026-03-27
> **Controller:** `admin_settings.php` → `menu()`, `profile()`
> **Tables:** `admin_menu`, `admin_profiles`, `admin_access`

---

## MENU CREATE

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | `admin_menu` | INSERT (name, url, parent, icon) | ⬜ |
| 2 | Access records | Auto-create for all profiles? | ❌ **Manual** |

## PROFILE CREATE

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | `admin_profiles` | INSERT (name, description) | ⬜ |
| 2 | Default access | Auto-assign basic permissions? | ⬜ |

## ACCESS SET

| # | Item | Status |
|---|---|---|
| 1 | Per-profile CRUD permissions | Set via access grid | ⬜ |
| 2 | `get_access()` function | Returns CRUD bits for menu | ⬜ |

## DELETE REVERSAL

| # | Item | Status |
|---|---|---|
| 1 | Profile delete → cleanup access records | ❌ Orphan access rows |
| 2 | Menu delete → cleanup access records | ❌ Orphan access rows |

## Known Bugs

| Bug ID | Description | Severity |
|---|---|---|
| MST-BUG-017 | `get_access()` returns NULL for unconfigured menus | 🟡 MED |
| RBAC-001 | No auto-provisioning of access for new menus | 🟡 MED |
| RBAC-002 | Orphan access rows on profile/menu delete | 🟡 MED |
