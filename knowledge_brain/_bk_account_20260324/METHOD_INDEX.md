# Account Module — Method Index
> **Round**: 1 | **Date**: 2026-03-06

---

## Controller Methods (`admin_manage.php`)

### Account CRUD
| # | Method | Line | Access | Purpose |
|---|---|---|---|---|
| 1 | `__construct()` | 16 | — | Loads 13 models, sets session vars |
| 2 | `index()` | 88 | Page | Redirects to `open_account()` |
| 3 | `open_account()` | 93 | Page | Account list (opening) |
| 4 | `account_form($type, $id)` | 145 | Page | Add/Edit form (switch: Add, Edit) |
| 5 | `account_post($type, $id)` | 415 | POST | Add/Edit/Delete handler |
| 6 | `account_status($status, $id)` | 2500 | GET | Activate/deactivate account |
| 7 | `open_account_list()` | 3161 | Page | Full account list page |

### Account Closing
| # | Method | Line | Access | Purpose |
|---|---|---|---|---|
| 8 | `close_account_list()` | 1154 | Page | Close list page |
| 9 | `close_account_form($type, $id)` | 1191 | Page/POST | Close/Save/Revert/Reject handler |
| 10 | `acc_close_otp($mobile, $id_cust, $name)` | 1050 | AJAX | Generate closing OTP |
| 11 | `acc_fetch_otp($id_scheme_account, $otp)` | 1129 | AJAX | Verify closing OTP |

### Account History & Reports
| # | Method | Line | Access | Purpose |
|---|---|---|---|---|
| 12 | `close_account_history_form($id)` | 2424 | Page | Payment history for closed account |
| 13 | `invoice_history_form($id)` | 2458 | Page | PDF invoice history |
| 14 | `invoice_his_custom($id)` | 2478 | Page | Custom receipt PDF |
| 15 | `chit_detail_report($id)` | 3842 | Page | Benefit/deduction report PDF |
| 16 | `benefit_report_print($view, $data)` | 3883 | Internal | PDF renderer helper |
| 17 | `accountRemarks()` | 3902 | Page | Pending collection report |
| 18 | `getRemarkPayments()` | 3907 | AJAX | JSON remark data |

### Passbook Print
| # | Method | Line | Access | Purpose |
|---|---|---|---|---|
| 19 | `passbook_print($page, $id, $id_payment)` | 3504 | Page | Multi-page passbook PDF |
| 20 | `receipt_account_ForBarCOde($id)` | 3705 | Page | Bar/QR code receipt |
| 21 | `receipt_account($id)` | 3775 | Page | QR PRN receipt |
| 22 | `passbook_reprint()` | 3810 | AJAX | Reset print flag |
| 23 | `get_scheme_receipt($id)` | 3484 | Page | Scheme receipt PDF |

### AJAX Data
| # | Method | Line | Access | Purpose |
|---|---|---|---|---|
| 24 | `ajax_get_account_list()` | — | AJAX | Account list JSON |
| 25 | `ajax_get_closed_account_list()` | — | AJAX | Closed list JSON |
| 26 | `ajax_get_scheme_account()` | — | AJAX | Scheme account dropdown |
| 27 | `get_all_scheme_account()` | 3170 | AJAX | All accounts by mobile |
| 28 | `ajax_close_account_list()` | 3185 | Page | Filtered closed list |
| 29 | `closed_acc_detail($id)` | — | AJAX | Detail view JSON |
| 30 | `get_branch_name()` | 2620 | AJAX | Branch dropdown JSON |
| 31 | `get_metal_name()` | 3498 | AJAX | Metal dropdown JSON |
| 32 | `get_groups()` | 3194 | AJAX | Group dropdown JSON |
| 33 | `getCustomersBySearch()` | 3333 | AJAX | Customer search |
| 34 | `getBranchDetails()` | 4353 | AJAX | Branch details |
| 35 | `getEmployeeByBranch()` | 4360 | AJAX | Employee dropdown |

### Scheme Groups
| # | Method | Line | Access | Purpose |
|---|---|---|---|---|
| 36 | `scheme_group()` | 2738 | Page | Group list page |
| 37 | `ajax_scheme_group_list($id)` | 2745 | AJAX | Group list JSON |
| 38 | `schemegroup_form($type, $id)` | 3062 | Page/POST | View/Edit/Save/Update/Delete |
| 39 | `check_group()` | 3150 | AJAX | Group code validation |

### Registration & Requests
| # | Method | Line | Access | Purpose |
|---|---|---|---|---|
| 40 | `registration_list()` | 1898 | Page | Registration list |
| 41 | `schemereg_list()` | 2767 | Page | Existing scheme reg list |
| 42 | `ajax_requests_list()` | 2772 | AJAX | Filtered request list |
| 43 | `update_request()` | 2797 | AJAX | Approve/reject/revert requests |

### Referral & OTP
| # | Method | Line | Access | Purpose |
|---|---|---|---|---|
| 44 | `checkreferalcode()` | 2604 | AJAX | Referral availability check |
| 45 | `referralcode_check()` | 2725 | AJAX | Full referral validation |
| 46 | `sendotp_scheme_join()` | 3999 | AJAX | Scheme join OTP |
| 47 | `verifyotp_scheme_join()` | 4047 | AJAX | Verify scheme join OTP |
| 48 | `rateFixing_otp()` | 3340 | AJAX | Rate fixing OTP |
| 49 | `submit_ratefix()` | 3362 | AJAX | Submit rate fix |
| 50 | `getBearerToken()` | 3454 | Internal | ERP auth token |

### Login & SMS
| # | Method | Line | Access | Purpose |
|---|---|---|---|---|
| 51 | `send_login()` | 1905 | Page | Send login page |
| 52 | `send_login_detail()` | 1912 | POST | Send login SMS |
| 53 | `send_sms($mobile, $msg, $dlt)` | 1941 | Internal | Gateway router |
| 54 | `login_sms($data)` | 1956 | Internal | Build login SMS |
| 55 | `account_join_message($id, $data)` | 2513 | Internal | Join SMS/Email/WA |
| 56 | `account_close_message($id, $acc)` | 2543 | Internal | Close SMS/Email/WA |
| 57 | `account_revert_message($data)` | 2574 | Internal | Revert SMS/Email/WA |
| 58 | `send_notification($mobile)` | 2981 | Internal | OneSignal push |
| 59 | `send_singlealert_notification($details)` | 3014 | Internal | OneSignal API call |

### Gift Management
| # | Method | Line | Access | Purpose |
|---|---|---|---|---|
| 60 | `generate_giftotp()` | 3202 | AJAX | Gift OTP generation |
| 61 | `gift_verify_otp()` | 3260 | AJAX | Gift OTP verify |
| 62 | `resend_giftotp()` | 3270 | AJAX | Resend gift OTP |
| 63 | `gift_issue()` | 3303 | AJAX | Simple gift issue |
| 64 | `get_gift_issued_list()` | 3316 | AJAX | Gift list by account |
| 65 | `get_gift_issued_Dropdown()` | 3324 | AJAX | Gift dropdown |
| 66 | `loadGiftData()` | 3821 | AJAX | Gift master data |
| 67 | `update_gift_status()` | 3834 | AJAX | Update gift status |

### Gift (Inventory)
| # | Method | Line | Access | Purpose |
|---|---|---|---|---|
| 68 | `sendotp_gift()` | 4138 | AJAX | Gift OTP (inventory) |
| 69 | `verifyotp_gift()` | 4177 | AJAX | Verify gift OTP (inv) |
| 70 | `get_gift_bystock()` | 4199 | AJAX | Gift by stock |
| 71 | `get_gift_account()` | 4206 | AJAX | Gift account mapping |
| 72 | `save_giftissued()` | 4212 | AJAX | Save gift (inv) |
| 73 | `get_gift_issued_byaccount()` | 4275 | AJAX | Gift by account |
| 74 | `get_gift_validation()` | 4283 | AJAX | Gift validation rules |
| 75 | `get_gifts_from_inv()` | 4290 | AJAX | Inventory gifts |
| 76 | `gift_issue_form()` | 4296 | Page | Gift issue form page |
| 77 | `cancel_giftissued()` | 4303 | AJAX | Cancel issued gift |
| 78 | `getGiftByRef()` | 4346 | AJAX | Gift by ref number |

### Sync Operations
| # | Method | Line | Access | Purpose |
|---|---|---|---|---|
| 79 | `getSchemeAccID($id_payment)` | 1966 | Internal | Get acc ID by payment |
| 80 | `update_client_jil()` | 2050 | POST | JIL sync |
| 81 | `syncInterData()` | 2117 | POST | SKTM group sync |
| 82 | `update_client()` | 2272 | POST | Main data sync |

### Account Helpers
| # | Method | Line | Access | Purpose |
|---|---|---|---|---|
| 83 | `manual_schemeaccount()` | 2630 | AJAX | Manual acc no generate |
| 84 | `digi_wallet_screen()` | 3894 | AJAX | DigiGold wallet data |
| 85 | `base64ToFile($imgBase64)` | 3914 | Internal | Webcam image converter |
| 86 | `set_remarks_byid()` | 4103 | AJAX | Account remarks |
| 87 | `blk_payment_byid()` | 4114 | AJAX | Block/unblock payment |
| 88 | `update_sche_acc_sts()` | 4129 | AJAX | Bulk status update |
| 89 | `calculateAllowBenefit($acc, $date)` | 4368 | Internal | Benefit eligibility |
| 90 | `isPreCloseEligible($acc, $date)` | 4388 | Internal | Pre-close check |
| 91 | `checkPreCloseThreshold($acc, $date)` | 4394 | Internal | Pre-close threshold |
| 92 | `insertEmployeeIncentive($ref, $id, $idp)` | 4407 | Internal | Employee wallet credit |
| 93 | `customerIncentive($ref, $id, $idp)` | 4440 | Internal | Customer wallet credit |

---

## Model Methods (`account_model.php`)

| # | Method | Lines | Purpose |
|---|---|---|---|
| 1 | `insertData($data, $table)` | 15-60 | Generic insert |
| 2 | `updateData($data, $field, $val, $table)` | 62-90 | Generic update |
| 3 | `account_empty_record()` | 92-137 | Default account array |
| 4 | `get_maturity_days($id)` | 139-143 | Scheme maturity days |
| 5 | `getActiveAccounts($id)` | 145-169 | Active accounts query |
| 6 | `getAmountSchemeAccounts($id)` | 198-240 | Amount scheme accounts |
| 7 | `set_registration_record(...)` | 242-256 | Registration default |
| 8 | `account_number_generator($sch, $br, $grp)` | 270-285 | Acc number gen |
| 9 | `get_schAccount_no($sch, $br, $grp)` | 287-410 | MAX acc number query |
| 10 | `get_settings()` | — | Chit settings |
| 11 | `insert_account($data)` | — | Account insert |
| 12 | `update_account($data, $id)` | — | Account update |
| 13 | `delete_account($id)` | — | Account delete |
| 14 | `get_account_detail($id)` | — | Full account detail |
| 15 | `get_account_open($id)` | — | Open account detail |
| 16 | `get_close_account($id)` | — | Close account detail |
| 17 | `get_closed_account_by_id($id)` | — | Closed account by ID |
| 18 | `get_all_account()` | — | All accounts |
| 19 | `insert_kyc($data)` | — | KYC insert |
| 20 | `check_referrals($code)` | — | Referral check |
| 21 | `check_refcode($code, $ref_by)` | — | Ref code validation |
| 22 | `getEmpBenefit($id)` | — | Employee benefit query |
| 23 | `getAgentBenefit($id)` | — | Agent benefit query |
| 24 | `getAccBenefitDeduction($data)` | — | Benefit/deduction calc |
| 25 | `getBonusInsAmt($data)` | — | Bonus installment amt |
| 26 | `checkSchemeCloseBeiefits($id)` | — | Close benefit check |
| 27 | `getMetalRates()` | — | Current metal rates |
| 28 | `isDigiAcc($id)` | — | DigiGold account check |
| 29 | `get_ClosedBenefitsDetails($id)` | — | Closed benefits wallet |
| 30 | `getCustomerByCode($code)` | — | Customer by ref code |
| 31 | `get_closing_request()` | — | Closing request list |
| 32 | `get_registration_details()` | — | Registration list |
| 33 | `clientid_exists($id)` | — | Client ID exists check |
| 34 | `insert_sync($data)` | — | Sync log insert |
| 35 | `code_available($code)` | — | Group code check |
| 36 | `get_schemegroup($branch)` | — | Scheme group list |
| 37 | `group_empty()` | — | Default group data |
| 38 | `get_groupaccount_details($id)` | — | Group detail |
| 39 | `insert_groupaccount($data)` | — | Group insert |
| 40 | `update_groupaccount($data, $id)` | — | Group update |
| 41 | `delete_group($data, $id)` | — | Group delete |
| 42 | `add_gift($data)` | — | Simple gift insert |
| 43 | `get_gift_issued($id)` | — | Gift list by account |
| 44 | `insert_gift_issued($data)` | — | Gift issued insert |
| 45 | `otp_insert($data)` | — | OTP insert |
| 46 | `otp_update_payment($data, $id)` | — | OTP update |
| 47 | `select_otp($otp)` | — | OTP lookup |
| 48 | `company_details()` | — | Company info |
| 49 | `branchname_list()` | — | Branch dropdown |
| 50 | `get_metal_name()` | — | Metal list |
| 51 | `chit_detail_report($data)` | — | Chit detail report |
| 52 | `get_chit_data($id)` | — | Chit scheme data |
| 53 | `get_chit_int($data)` | — | DigiGold interest |
| 54 | `get_remark_data($from, $to)` | — | Remark payments |
| 55 | `set_remarks_byid($id, $data)` | — | Set remarks |
| 56 | `blk_payment_byid($id, $stop, $reason)` | — | Block payment |
| 57 | `update_schacc_status($data)` | — | Bulk status update |
| 58 | `get_financialYear()` | — | Financial year data |
| 59 | `get_wallet_acc_number()` | — | Wallet acc number gen |
| 60 | `getDiscountByjoin($id, $sch, $date)` | — | Join discount |
| 61 | `getAvailableCustomers($search)` | — | Customer search |
| 62 | `is_agent_exist($code)` | — | Agent exists check |
| 63 | `getPurityName($id)` | — | Purity lookup |
| 64 | `getMetalName($id)` | — | Metal name |
| 65 | `paymentModeName($mode)` | — | Payment mode name |
