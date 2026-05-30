# Recipe: Access Permission Page Missing Menus for Some Profiles

## Metadata
- **Pattern ID**: PAT-ACCESS-001
- **Severity**: HIGH
- **Modules Affected**: Settings → Access / Permission (admin_settings, admin_settings_model)
- **Auto-fixable**: Partial — SQL data fix is scriptable; model fix requires code change
- **Last Updated**: 2026-04-30 (v2 — removed all menu exclusions from PermissionDB)

## Client Scope
- **Applies to**: ALL
- **Reason**: Core permission system is shared across all clients. Any client where new menus were added without running the access-linking logic, or new profiles were created after initial setup, will have this gap.

## Created By
- **Developer**: Antigravity AI
- **Client**: erp.sriammanjewellers.in
- **Date**: 2026-04-29
- **Source Bug ID**: N/A

## Symptom
On the `Settings → Access/Permission` page (`settings/access/view`):
- Some profiles show **fewer menus** than others
- Newly added menus do **not appear** for older profiles
- Newly created profiles show **no menus** at all, or a very limited subset
- Cannot grant/revoke permissions for menus that don't appear in the list
- Profiles like `Developer`, `Sales`, `BILLING` had 60–211 menu entries instead of the expected 458+
- Specific menus (e.g. `id_menu 17`, `id_menu 18`) missing for certain profiles even after other fixes applied
- Admin profile (id=3) cannot see the **Permission** menu (id_menu=18) to manage access

## Root Cause

Three compounding bugs:

### Bug 1 — Missing DB rows (data gap)
When a new menu is added via `Settings → Menu`, the controller (`admin_settings.php` → `menu()` → `Save`) inserts access rows for existing profiles. However:
- Profiles created **after** the menu was added are never back-filled
- Manual DB inserts of menus bypass this logic entirely
- Result: `access` table is missing `(id_profile, id_menu)` combinations

### Bug 2 — SQL anti-pattern in `PermissionDB()` (implicit INNER JOIN)
The `PermissionDB('get', $profile_id, ...)` query in `admin_settings_model.php` uses:
```sql
From menu m
Left Join access a On (m.id_menu = a.id_menu)
Left Join profile p On (a.id_profile = p.id_profile)
Where (m.active=1 And m.id_menu>1) And p.id_profile = X
```
The `WHERE p.id_profile = X` clause silently converts the LEFT JOIN into an INNER JOIN —  
when there is **no** `access` row for a menu+profile pair, `p.id_profile` is NULL and `NULL = X` is false, so the menu row is dropped entirely.

### Bug 3 — Hardcoded menu exclusions in `PermissionDB()` (both `'empty'` and `'get'` cases)
Both cases contained profile-ID-based conditions that excluded certain menus from the permission page:
```php
// Hid id_menu=17 for all profiles except super admin (id=1)
.($id!=1 ? " And m.id_menu<>17 " : "")
// Hid id_menu=18 (Permission menu) for all profiles except id=1 and id=2
.($id==1 || $id==2 ? "" : " And m.id_menu<>18 ")
```
This meant no matter how many profiles were added, they could never see or manage these menus from the permission page — making it impossible for Admin (id=3) and all other profiles to grant/revoke access to those features.

## Detection

```bash
# Check how many (profile, menu) combinations are missing from access table
# Run via phpMyAdmin or mysql CLI
SELECT COUNT(*) as missing_entries
FROM menu m
CROSS JOIN profile p
WHERE m.active = 1
  AND m.id_menu > 1
  AND NOT EXISTS (
    SELECT 1 FROM access a
    WHERE a.id_menu = m.id_menu
      AND a.id_profile = p.id_profile
  );
# If result > 0, this bug is present

# Check per-profile menu counts (should all be roughly equal)
SELECT p.id_profile, p.profile_name, COUNT(a.id_menu) as access_count
FROM profile p
LEFT JOIN access a ON p.id_profile = a.id_profile
GROUP BY p.id_profile, p.profile_name
ORDER BY access_count ASC;
# Profiles with significantly fewer rows than others are affected

# Check the buggy SQL pattern in the model
grep -n "Left Join profile p On" admin/application/models/admin_settings_model.php
# Look for WHERE clause that follows with p.id_profile = — that's the bug
```

## Files
- `admin/application/models/admin_settings_model.php` — `PermissionDB()` function, `case 'get':` block
- `amman` database — `access` table (data fix needed)

## Fix

### Fix 1 — SQL Data Fix (fill missing access rows)

Run this SQL once to back-fill all missing `(profile, menu)` entries with default `0,0,0,0` permissions:

```sql
INSERT INTO access (id_profile, id_menu, `view`, `add`, `edit`, `delete`)
SELECT p.id_profile, m.id_menu, 0, 0, 0, 0
FROM menu m
CROSS JOIN profile p
WHERE m.active = 1
  AND m.id_menu > 1
  AND NOT EXISTS (
    SELECT 1 FROM access a
    WHERE a.id_menu = m.id_menu
      AND a.id_profile = p.id_profile
  );
```

> **Safe to re-run** — the `NOT EXISTS` guard ensures no duplicates are created.

---

### Fix 2 — Model Code Fix: True LEFT JOIN (prevent INNER JOIN anti-pattern)

**File:** `admin/application/models/admin_settings_model.php`  
**Function:** `PermissionDB()` → `case 'get':` → `if($id!=NULL)` block

#### Before
```php
$sql="Select 
        p.id_profile,
        m.id_menu,
        m.label,
        m.link,
        m.parent,
        (select count(id_menu) from menu where parent=m.id_menu) as submenus,
        m.icon,
        m.sort,
        a.`view`,
        a.`add`,
        a.`edit`,
        a.`delete`
     From menu m
     Left Join access a On (m.id_menu=a.id_menu)
     Left Join profile p On(a.id_profile=p.id_profile)
     Where  (m.active =1 And m.id_menu>1) And p.id_profile=".$id.($id_menu!=''? " And m.parent=".$id_menu:"").($id!=1?" And m.id_menu<>17 ":"").($id==1 || $id==2 ?" ":" And m.id_menu<>18 ").
         " Order By m.parent,m.sort,m.id_menu";
```

#### After
```php
$sql="Select 
        COALESCE(a.id_profile, ".$id.") as id_profile,
        m.id_menu,
        m.label,
        m.link,
        m.parent,
        (select count(id_menu) from menu where parent=m.id_menu) as submenus,
        m.icon,
        m.sort,
        COALESCE(a.`view`, 0) as `view`,
        COALESCE(a.`add`, 0) as `add`,
        COALESCE(a.`edit`, 0) as `edit`,
        COALESCE(a.`delete`, 0) as `delete`
     From menu m
     Left Join access a On (m.id_menu=a.id_menu And a.id_profile=".$id.")
     Where  (m.active =1 And m.id_menu>1)
     ".($id_menu!='' ? " And m.parent=".$id_menu : "")."
         Order By m.parent,m.sort,m.id_menu";
```

**Key changes:**
1. Moved `a.id_profile = X` from `WHERE` into the `LEFT JOIN ON` clause — preserves true LEFT JOIN behaviour
2. Removed `Left Join profile p` (no longer needed)
3. Added `COALESCE(a.view, 0)` etc. so NULL values from missing rows return `0` instead of NULL
4. **Removed all `id_menu<>17` and `id_menu<>18` exclusions** — all menus must be visible to all profiles on the permission page

---

### Fix 3 — Remove Menu Exclusions from `'empty'` case

**Function:** `PermissionDB()` → `case 'empty':` block

#### Before
```php
$sql="SELECT id_menu,label,...  FROM menu m
      WHERE m.active=1 and m.id_menu>1
      ".($id!=1?" And m.id_menu<>17 ":"").
      .($id_menu!=''? " And parent=".$id_menu:"").
      .($id==1 || $id==2 ?"":"  And m.id_menu<>18 ").
      " Order By parent,sort,id_menu ";
```

#### After
```php
$sql="SELECT id_menu,label,...  FROM menu m
      WHERE m.active=1 and m.id_menu>1
      ".($id_menu!=''? " And parent=".$id_menu:"").
      " Order By parent,sort,id_menu ";
```

**Key change:** Removed the profile-based `id_menu<>17` and `id_menu<>18` exclusion conditions — the empty/fallback menu list must also show all menus regardless of profile.

## Verification

1. Run the detection query — `missing_entries` should return **0** after the SQL fix
2. Open `Settings → Access/Permission` (`settings/access/view`)
3. Click through **every** profile tab — all profiles should show the **same number of menu rows**, including `id_menu 17` and `id_menu 18`
4. Newly added menus should appear under all profiles (even without existing `access` rows)
5. Toggle a permission checkbox and click Save — verify the value persists in the `access` table
6. Confirm the header navigation still respects permissions correctly for each profile
7. Verify the Admin profile (id=3) can now see and toggle the **Permission** menu (id_menu=18)

## Notes
- The SQL data fix is **idempotent** — safe to run in any environment, any number of times
- The model fix future-proofs against new menus/profiles being added out-of-order
- **Do NOT add `id_menu<>X` exclusions** to `PermissionDB()` — the permission page is the admin interface to manage access; every menu must be configurable from there. The actual runtime access enforcement happens in `get_access()` / `menu_generation()`, not here
- The same LEFT JOIN anti-pattern may exist in `DashboardPermissionDB()` — audit that function too if dashboard menu permissions also show gaps
- When adding a **new menu**, the controller already inserts access rows for existing profiles (`menu()` → `Save` case, lines 88–95 in `admin_settings.php`) — but new **profiles** added later are not back-filled; this SQL fix or a scheduled job should cover that
- **Related**: `menuPermission()` in `admin_settings_model.php` (used in header rendering) uses a proper INNER JOIN which is correct for header rendering — do not change that one
