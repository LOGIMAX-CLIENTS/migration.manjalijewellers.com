# Flow Checklist: Section Transfer

> **Last Updated:** 2026-03-27
> **Controller:** `admin_ret_section_transfer.php`

---

## Overview

Section Transfer = moving tags between sections/counters within same branch. No physical shipping, just logical assignment change.

## SAVE Checklist

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | `ret_section_transfer` | INSERT header (from_section, to_section, date, branch) | ⬜ |
| 2 | `ret_section_transfer_items` | INSERT tag items being transferred | ⬜ |
| 3 | `ret_taging.id_section` | UPDATE to new section | ⬜ |
| 4 | `ret_section_tag_status_log` | INSERT status log per tag | ⬜ |
| 5 | Tag status guard | Must be status=0 (available) to transfer | ⬜ **VERIFY** |

## CANCEL Checklist

| # | Table | Expected Reversal | Status |
|---|---|---|---|
| 1 | `ret_taging.id_section` | Revert to original section | ⬜ **VERIFY** |
| 2 | `ret_section_transfer.status` | Mark cancelled | ⬜ |
| 3 | `ret_section_tag_status_log` | Log revert | ⬜ |

## Known Risks

| Risk | Description | Severity |
|---|---|---|
| SECT-001 | No cancel mechanism verified — tags may be stuck in wrong section | 🟡 MED |
