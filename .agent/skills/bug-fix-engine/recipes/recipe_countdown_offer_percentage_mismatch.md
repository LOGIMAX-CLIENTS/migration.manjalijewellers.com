# Recipe - Countdown Offer Percentage Key Alignment

## Metadata
- **Pattern ID**: PAT-OFF-102
- **Severity**: LOW
- **Modules Affected**: Offers, Home Screen
- **Auto-fixable**: Yes (string replacement)

## Client Scope
- **Applies to**: ALL
- **Reason**: Common key naming mismatch in the home screen CountdownOfferView payload.

## Created By
- **Developer**: Antigravity
- **Client**: LOGIMAX-CLIENTS
- **Date**: 2026-06-08
- **Source Bug ID**: N/A

## Symptom
Mobile app shows a reward percentage mismatch or cannot render reward percentages correctly for either new or existing users due to inconsistent API response keys (`reward_percentage` returned in `new_offer` vs. `current_reward_percentage` returned in `existing_offer`).

## Root Cause
The `new_offer` dictionary key was named `"reward_percentage"` while the `existing_offer` dictionary key was named `"current_reward_percentage"`. This caused parsing failures in components expecting consistent key names.

## Detection
Check `domains/masters/views/master.py` to see if `new_offer` contains `"reward_percentage"` and `existing_offer` contains `"current_reward_percentage"` without exposing both fields.

## Files
- `backend/services/api_service/domains/masters/views/master.py`

## Fix

### Before
In `new_offer`:
```python
                new_offer = {
                    ...
                    "reward_percentage":        int(reward_pct) if reward_pct == int(reward_pct) else reward_pct,
                    "daily_penalty_percentage": 1,
                    ...
                }
```
In `existing_offer`:
```python
            existing_offer = {
                ...
                "current_reward_percentage": int(display_pct) if display_pct == int(display_pct) else display_pct,
                "offer_end_date":            end_label,
                ...
            }
```

### After
In `new_offer`:
```python
                new_offer = {
                    ...
                    "reward_percentage":        int(reward_pct) if reward_pct == int(reward_pct) else reward_pct,
                    "current_reward_percentage": int(reward_pct) if reward_pct == int(reward_pct) else reward_pct,
                    "daily_penalty_percentage": 1,
                    ...
                }
```
In `existing_offer`:
```python
            existing_offer = {
                ...
                "current_reward_percentage": int(display_pct) if display_pct == int(display_pct) else display_pct,
                "reward_percentage":         int(display_pct) if display_pct == int(display_pct) else display_pct,
                "offer_end_date":            end_label,
                ...
            }
```

## Verification
1. Call `POST /api/v1/home/countdown-offer` for a new user and confirm both `reward_percentage` and `current_reward_percentage` are returned in `new_offer`.
2. Call `POST /api/v1/home/countdown-offer` for an existing user and confirm both `reward_percentage` and `current_reward_percentage` are returned in `existing_offer`.
