________________________________***________________________________  LOCK YOU GOLD MODULE   ________________________________***________________________________
WORK PLAN:

    1. Plan Settings : Plan settings table structure in db. ->Abi
    2. Plan Settings : Listing UI design. -> santhosh
    3. Plan Settings : Add/Edit form UI design. -> santhosh
    4. Plan Settings : Insert operation for plan form. ->abi
    5. Plan Settings : Fetch default/specified plan's data from db for form. ->abi
    6. Plan Settings : Fetch Data for listing and mobile_api. ->abi
    7. Plan Settings : Update operation for plan form. ->abi

    8. Account : Account joining form UI design. ->santhosh
    9. Account : Account Insert operation (admin/app) ->abi
    10. Account : ct_advance_account table structure in db. ->abi
    11. Account : Fetch default/specified account's data from db for form. ->abi
    12. Account : Account update operation. ->abi
    13. Account : Booking amount / weight calculation  ->abi
    14. Account : Passbook print (discuss)

    13. Payment : Payment for UI design. ->abi
    14. Payment : - Insert payment operation (Admin/app). ->abi
                - Mode detail table insert ->abi
                - Account number and receipt number generation. ->abi
    15. Payment : Payment Listing UI design. ->santhosh
    16. Payment : Fetch payment for listing ->abi
    17. Payment : Auto/ Manual verification. ->abi
    18. Payment : Add payment form pre-requisties;  ->abi
                    - customer, plan, account data.
                    - allow pay
                    - min, max, payable restriction
    19. Payment : Edit payment (discuss)
    20. Payment : Receipt print (discuss)

    21. Report : Adv booking plans report UI design.  ->santhosh
    22. Report : Fetch data for report (admin/app). ->abi

    23. App : Dashboard menu based on settings -> krish
    24. App : Display Plans -> Krish
    25. App : Join plans -> krish
    26. App : Payment -> kirsh
    27. App : Payment history -> krish
    28. App : Pay ema -> Abi
    29. App : Display Plans joined -> krish

    30. Module documentation : - Overall Summary ->pavithra
                            - Files Used, Table Structure , Technical Summary -> abi

_____________________***______________________________***_____________________________***_________________________***__________________

PLAN LISTING COLUMNS:
    1. Plan ID -> id_plan
    2. Plan Name -> plan_name
    3. code -> plan_code
    4. Payable By -> payable_by
    5. Active -> is_active
    6. Status -> is_visible

PRE-BOOKING LISTING:
    1. Booking ID
    2. Customer Name   
    3. Mobile
    4. Branch
    5. Booked Date
    6. Booked Through
    7. Booked amount
    8. Booked weight
    9. Booked metal rate
    10. Status 
    11. Remarks
    12. Employee
    13. Maturity Date
    14. Total Paid amount
    15. Total Paid weight
    16. Booking code

PRE-BOOKING PAYMENTS:
    1. Booking ID
    2. Customer Name   
    3. Mobile
    4. Branch
    5. Payment Date
    6. Payment Amount
    7. Booked amount
    8. Booked weight
    9. Booked metal rate
    10. Status 
    11. Remarks
    12. Employee
    13. Payment Through
    14. Total Paid amount
    15. Total Paid weight
    16. Booking code
    17. Payment Mode
    18. Online Ref number
    19. Payment Type




_____________________***______________________________***_____________________________***_________________________***__________________

API DETAILS:

1. Plan Data:
    HTTP Method : GET
    module=book_gold => lock you gold menu click
    module=book_silver => lock your silver menu click
    URL : http://localhost/etail_v3/api/index.php/advance_booking/allActivePlans?module=book_gold
    RESPONSE : [{"id_plan":"1","plan_name":"Lock Your Gold","plan_code":"LYG","sync_plan_code":"LYG","maturity_type":"1","maturity_value":"11","accessible_branches":null,"id_metal":"1","purity":"1","payable_by":"0","minimum_val":null,"maximum_val":null,"denomination":"1","adv_limit_type":"0","adv_limit_value_online":"5000","plan_image":null,"plan_description":null,"is_active":"1","is_visible":"1","date_add":"2023-11-27 14:08:19.895689","last_edited_date":"2023-11-27 14:08:19.895689","total_adv_limit_value":"10000","is_adv_limit_available":"0"}]

2. Create Account:
    HTTP Method : POST
    URL : http://localhost/etail_v3/api/index.php/advance_booking/create_booking
    POSTDATA : 
    RESPONSE : 

3. Payment:
    HTTP Method : POST
    URL : http://localhost/etail_v3/api/index.php/advance_booking/book_metal
    POSTDATA : source_type=mobile
    RESPONSE : 

