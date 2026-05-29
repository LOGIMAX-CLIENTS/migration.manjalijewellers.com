# Flow Checklist: Advance Booking

> **Last Updated:** 2026-03-27
> **Controller:** `admin_adv_booking.php`

---

## Overview

Advance Booking = customer books an item in advance (different from customer order).

## SAVE Checklist

| # | Table | Expected Action | Status |
|---|---|---|---|
| 1 | Booking record | INSERT (customer, item, date, advance amount) | ⬜ |
| 2 | Payment | Record advance payment | ⬜ |
| 3 | Item reservation | Tag or item reserved for customer? | ⬜ **VERIFY** |

## CANCEL Checklist

| # | Expected Reversal | Status |
|---|---|---|
| 1 | Advance refund | ⬜ |
| 2 | Item released | ⬜ |
| 3 | Journal reversal | ⬜ |
