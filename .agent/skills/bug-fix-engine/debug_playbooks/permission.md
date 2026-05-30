# Playbook #6: Permission Denied

> **Symptom**: "User can't access this page", "Menu not visible", "403 error"

## ⚡ Binary Search Integration
- **Start Layer**: 5 (Session + DB) — check user's profile ID in session, then check access table for that profile
- **Elimination**: Access row exists with `allowed=1` → controller has extra hardcoded auth check. Access row missing → add it. Menu invisible → access table. Menu visible but 403 → controller-level check.
- **Smell test**: "New feature not accessible" → 50% access table row missing for this profile+menu. "Was working before" → session expired silently (check session table).

## Collect
1. Which user (username/employee ID)?
2. What profile are they assigned?
3. Which page/feature is blocked?
4. Is the menu VISIBLE but clicking gives error, or INVISIBLE entirely?

## Trace
1. Find the user's profile ID:
   ```sql
   SELECT id_employee, employee_name, id_profile FROM employee WHERE username = '{username}';
   ```
2. Check access table for that profile + menu:
   ```sql
   SELECT a.*, m.menu_name, m.link 
   FROM access a 
   JOIN menu m ON a.id_menu = m.id_menu 
   WHERE a.id_profile = {id_profile} AND m.link LIKE '%{target_page}%';
   ```
3. If menu invisible → access row is `allowed=0` or missing
4. If menu visible but 403 → controller has a permission check that fails
5. Check controller for auth check:
   ```php
   // Look for this pattern at the top of the method:
   $access = $this->$model->get_access('{current_url}');
   if (!$access) { redirect('unauthorized'); }
   ```

## Common Root Causes
1. **50%**: Access table missing row for this profile+menu combo
2. **25%**: New menu item added without setting default permissions
3. **15%**: Profile has access but menu `link` column doesn't match controller URL
4. **10%**: Session expired silently → redirect to login (looks like permission denied)
