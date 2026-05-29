# Flow Checklist: Sales Transfer

> **Last Updated:** 2026-03-27
> **Controller:** `admin_ret_sales_transfer.php`

---

## Overview

Sales Transfer = transferring a sale between branches (customer buys at Branch A, item from Branch B stock).

## SAVE Checklist

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | `ret_sales_transfer` | INSERT header | ⬜ |
| 2 | Tag items | Transfer tag ownership between branches | ⬜ |
| 3 | Stock adjustment | Source branch: decrement, Dest branch: increment | ⬜ |

## CANCEL Checklist

| # | Expected Reversal | Status |
|---|---|---|
| 1 | Revert tag branch assignment | ⬜ |
| 2 | Stock reversal at both branches | ⬜ |
