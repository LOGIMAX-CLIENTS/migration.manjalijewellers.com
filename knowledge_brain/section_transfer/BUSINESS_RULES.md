# BUSINESS RULES — Section Transfer
> Built: 2026-03-14 | Round 1

---

## RULE-ST-001: Only Available Tags Can Be Transferred

```
Formula: tag_status == 0 → eligible for transfer
Implementation: Model getSectionTags WHERE clause L128, Controller save L139
Validation: Server-side (model filter), partially client-side (order reservation warning)
Edge cases: tag_status=14 means already in home counter — not available again
```

---

## RULE-ST-002: Transfer Date Uses Day-Closing Date

```
Formula: datetime = (day_closing.entry_date == today ? NOW() : entry_date)
Implementation: Controller save L123
    $datetime = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date'])
Validation: Server-side only
Edge cases: If day is closed, entry_date from ret_day_closing is used;
            if open, current timestamp used. All log entries use $datetime.
```

---

## RULE-ST-003: Home Counter Section Triggers Additional Stock Update

```
Formula: IF ret_section.is_home_bill_counter == 1 THEN update ret_home_section_item
Implementation: Controller save L181 — sectionData() check
Validation: Server-side only
Edge cases: Only home-counter sections aggregate stock;
            regular sections only update ret_taging.id_section.
```

---

## RULE-ST-004: Tag Status Set to 14 After Home-Counter Transfer

```
Formula: After transfer to home-counter section → ret_taging.tag_status = 14
Implementation: Model updatestatus() L385 — hardcoded 14
Validation: Server-side only
Edge cases: Status 14 = "At home billing counter".
            Other section: status unchanged (stays 0 or current).
```

---

## RULE-ST-005: Non-Tag Stock Uses Arithmetic Increment/Decrement

```
Formula: 
  Deduct from source: no_of_piece - qty, gross_wt - gwt, net_wt - nwt
  Add to destination: no_of_piece + qty, gross_wt + gwt, net_wt + nwt
  If destination record missing: INSERT new ret_nontag_item row
Implementation: Model updateNTData() L281, updatesecNTData() L365
Validation: Client-side partial (pcs/wt cannot exceed balance), server-side arithmetic
Edge cases: No atomic check between source deduct and destination add — partial commit possible
```

---

## RULE-ST-006: OTP Gate for Counter-Change

```
Formula: IF profile.counter_change_otp == 1 → require OTP before transfer
OTP: mt_rand(1001, 9999), stored in session, expires in 300 seconds (5 minutes)
Implementation: Controller send_counterchange_otp L577–580, view L175 (allow_order_item_cancel_otp)
Validation: Both client-side (modal shown) and server-side (session verify)
Edge cases: 
  - Multi-mobile: OTP strings concatenated without delimiter (L568–578) — comparison risk
  - OTP value returned in JSON response (L610) — security issue
  - Timer reset: Resend button has 60-second lockout
```

---

## RULE-ST-007: Order-Reserved Tags Blocked from Transfer

```
Formula: IF tag.id_orderdetails IS NOT NULL AND orderno IS NOT NULL → block
Implementation: JS getSectionTags() L591–603 (client-side warning + allow_submit=false)
              Model getSectionTags L230 (server-side filter: omit order tags when only branch selected)
Validation: Both client-side and server-side
Edge cases: User can still manually search by tag_code even if reserved; 
            server-side filter applies only when branch/section selected without specific tag search
```

---

## RULE-ST-008: NT Item Quantity Validation (Client-Side)

```
Formula: 
  pieces_entered <= balance_pieces
  gross_wt_entered <= balance_gross_wt
  net_wt_entered <= balance_net_wt
  gross_wt_entered >= net_wt_entered
Implementation: JS input/change handler L1293–1381
Validation: Client-side only (no server-side qty validation!)
Edge cases: No server-side validation means crafted POST can transfer more than available
```

---

## RULE-ST-009: Section Transfer Log Status Codes

```
Tagged transfer log (ret_section_tag_status_log): status = 0 (section transfer)
Home section item log (ret_home_section_item_log): status = 0
NT deduct log (ret_section_nontag_item_log): status = 4
NT add/transfer log (ret_section_nontag_item_log): status = 0
ret_taging_status_log: status = 16 (home counter transfer)
Implementation: Controller save, various insertData calls
```
