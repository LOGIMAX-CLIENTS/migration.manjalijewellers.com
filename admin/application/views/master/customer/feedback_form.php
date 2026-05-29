<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Feedback</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 30%, #0f3460 60%, #533483 100%);
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 20px 10px;
        }

        .customer_form {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            width: 100%;
            max-width: 480px;
            padding: 32px 28px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(255,255,255,0.1);
            margin: 10px auto;
            position: relative;
            overflow: hidden;
        }


        .form-header {
            text-align: center;
            margin-bottom: 24px;
        }

        .form-header .logo-wrap {
            width: 80px;
            height: 80px;
            margin: 0 auto 12px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(83, 52, 131, 0.2);
            border: 2px solid rgba(83, 52, 131, 0.1);
        }

        .form-header .logo-wrap img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .form-header h5 {
            font-size: 15px;
            font-weight: 600;
            color: #1a1a2e;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .form-header p {
            font-size: 12px;
            color: #888;
            letter-spacing: 0.5px;
        }

        /* Section Dividers */
        .section-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #533483;
            margin: 20px 0 12px;
            padding-bottom: 6px;
            border-bottom: 1px solid rgba(83, 52, 131, 0.1);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .section-label i {
            font-size: 12px;
            opacity: 0.7;
        }

        /* Form Controls */
        .form-group {
            margin-bottom: 14px;
        }

        .form-group label {
            font-size: 12px;
            font-weight: 500;
            color: #444;
            margin-bottom: 4px;
            display: block;
        }

        .form-group label .text-danger {
            color: #e94560 !important;
        }

        .form-control {
            border: 1.5px solid #e0e0e0;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 13px;
            font-family: 'Inter', sans-serif;
            color: #333;
            transition: all 0.25s ease;
            background: #fafafa;
        }

        .form-control:focus {
            border-color: #533483;
            box-shadow: 0 0 0 3px rgba(83, 52, 131, 0.1);
            background: #fff;
            outline: none;
        }

        .form-control::placeholder {
            color: #bbb;
            font-size: 12px;
        }

        textarea.form-control {
            min-height: 60px;
            resize: vertical;
        }

        /* Mobile + Country Code Row */
        .mobile-row {
            display: flex;
            gap: 8px;
        }

        .mobile-row .country-col {
            flex: 0 0 100px;
            max-width: 100px;
        }

        .mobile-row .country-col .select2-container {
            width: 100px !important;
        }

        .mobile-row .country-col .select2-selection__rendered {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 70px;
            font-size: 12px;
        }

        .mobile-row .mobile-col {
            flex: 1;
        }

        /* Two-column fields */
        .row-2col {
            display: flex;
            gap: 10px;
            overflow: hidden;
        }

        .row-2col .col-half {
            flex: 1;
            min-width: 0;
        }

        .row-2col .col-pin {
            flex: 0 0 110px;
            max-width: 110px;
        }

        /* Select2 Overrides */
        .select2-container--default .select2-selection--single {
            height: 40px !important;
            padding: 6px 12px;
            border: 1.5px solid #e0e0e0;
            border-radius: 10px;
            background: #fafafa;
            font-family: 'Inter', sans-serif;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px;
            font-size: 13px;
            color: #333;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px;
            top: 1px;
        }

        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #533483;
            box-shadow: 0 0 0 3px rgba(83, 52, 131, 0.1);
        }

        /* Radio Cards */
        .source-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .source-option {
            position: relative;
        }

        .source-option input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .source-option label {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 12px;
            border: 1.5px solid #e0e0e0;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 12px;
            font-weight: 400;
            color: #666;
            background: #fafafa;
            margin: 0;
        }

        .source-option label i {
            font-size: 14px;
            opacity: 0.5;
            transition: all 0.2s ease;
        }

        .source-option input[type="radio"]:checked + label {
            border-color: #533483;
            background: rgba(83, 52, 131, 0.06);
            color: #533483;
            font-weight: 500;
        }

        .source-option input[type="radio"]:checked + label i {
            opacity: 1;
            color: #533483;
        }

        /* Star Ratings */
        .rating-group {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            background: #fafafa;
            border: 1.5px solid #e0e0e0;
            border-radius: 10px;
            margin-bottom: 8px;
            transition: all 0.2s ease;
        }

        .rating-group:hover {
            border-color: #d0d0d0;
            background: #f5f5f5;
        }

        .rating-group .rating-label {
            font-size: 13px;
            font-weight: 500;
            color: #444;
        }

        .star-rating {
            display: flex;
            gap: 4px;
        }

        .star-rating i {
            font-size: 20px;
            color: #d4d4d4;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .star-rating i:hover {
            transform: scale(1.2);
        }

        .star-rating i.hovered {
            color: #f5a623;
        }

        .star-rating i.selected {
            color: #f5a623;
        }

        /* Dynamic Feedback Textarea */
        .dynamic-feedback .form-group {
            margin-bottom: 10px;
        }

        .dynamic-feedback label {
            font-size: 12px;
            font-weight: 500;
            color: #555;
        }

        /* Submit Button */
        .btn-submit {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 30%, #0f3460 60%, #533483 100%);
            color: #fff;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(83, 52, 131, 0.35);
            margin-top: 8px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(83, 52, 131, 0.5);
            background: linear-gradient(135deg, #16213e 0%, #0f3460 40%, #533483 100%);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Footer */
        .form-footer {
            text-align: center;
            margin-top: 16px;
            font-size: 11px;
            color: #aaa;
        }

        /* ── Page Wrapper (shared flex shell used on tablet+desktop) ─── */
        .page-wrapper {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        /* ── Brand Panel (hidden on mobile, shown tablet+ in split mode) */
        .brand-panel {
            display: none;
        }

        /* ── Wide 2-column row (hidden / stacked on mobile) ─────────── */
        .row-wide-2col {
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        /* ═══════════════════════════════════════════════════════════════
           Responsive — Mobile  (≤ 576px)
        ═══════════════════════════════════════════════════════════════ */
        @media (max-width: 576px) {
            body {
                padding: 6px 4px;
            }

            .customer_form {
                max-width: 100%;
                margin: 10px auto;
                padding: 24px 20px;
                border-radius: 14px;
            }

            .source-options {
                grid-template-columns: 1fr;
            }

            .mobile-row .country-col {
                flex: 0 0 80px;
            }
        }

        /* ═══════════════════════════════════════════════════════════════
           Responsive — Tablet  (577px – 1024px)
           Strategy: single wide card, 2-col field grid for efficiency
        ═══════════════════════════════════════════════════════════════ */
        @media (min-width: 577px) and (max-width: 1024px) {
            body {
                padding: 24px 16px;
                align-items: flex-start;
            }

            .page-wrapper {
                max-width: 780px;
                margin: 0 auto;
                width: 100%;
            }

            .customer_form {
                max-width: 780px;
                width: 100%;
                padding: 36px 40px;
                border-radius: 22px;
                margin: 0;
                box-shadow: 0 24px 70px rgba(0, 0, 0, 0.28), 0 0 0 1px rgba(255,255,255,0.12);
            }

            /* Header — centered, slightly bigger */
            .form-header {
                margin-bottom: 28px;
            }

            .form-header .logo-wrap {
                width: 90px;
                height: 90px;
                border-radius: 18px;
                margin-bottom: 12px;
            }

            .form-header h5 {
                font-size: 16px;
                letter-spacing: 2.5px;
            }

            .form-header p {
                font-size: 13px;
            }

            /* Section labels */
            .section-label {
                font-size: 11.5px;
                letter-spacing: 2px;
                margin: 22px 0 14px;
                padding-bottom: 8px;
            }

            /* Form controls */
            .form-group {
                margin-bottom: 14px;
            }

            .form-group label {
                font-size: 13px;
                margin-bottom: 5px;
            }

            .form-control {
                padding: 11px 15px;
                font-size: 14px;
                border-radius: 10px;
            }

            .form-control::placeholder {
                font-size: 13px;
            }

            textarea.form-control {
                min-height: 68px;
            }

            /* Wide 2-col row — side-by-side on tablet */
            .row-wide-2col {
                flex-direction: row;
                gap: 14px;
            }

            .row-wide-2col > .form-group {
                flex: 1;
                min-width: 0;
            }

            /* Country code col */
            .mobile-row .country-col {
                flex: 0 0 110px;
                max-width: 110px;
            }

            .mobile-row .country-col .select2-container {
                width: 110px !important;
            }

            /* Existing 2col rows — wider pin */
            .row-2col { gap: 14px; }
            .row-2col .col-pin { flex: 0 0 128px; max-width: 128px; }

            /* Source options — 4 cards in one row on tablet */
            .source-options {
                grid-template-columns: repeat(4, 1fr);
                gap: 8px;
            }

            .source-option label {
                flex-direction: column;
                justify-content: center;
                text-align: center;
                padding: 12px 8px;
                font-size: 11.5px;
                border-radius: 10px;
                gap: 6px;
            }

            .source-option label i {
                font-size: 18px;
                opacity: 0.6;
            }

            /* Rating rows */
            .rating-group {
                padding: 12px 18px;
                border-radius: 10px;
                margin-bottom: 8px;
            }

            .rating-group .rating-label { font-size: 14px; }
            .star-rating i { font-size: 22px; }

            /* Select2 */
            .select2-container--default .select2-selection--single {
                height: 43px !important;
                padding: 8px 13px;
                border-radius: 10px;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 27px;
                font-size: 14px;
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 41px;
            }

            /* Submit */
            .btn-submit {
                padding: 14px;
                font-size: 15px;
                border-radius: 12px;
                margin-top: 10px;
            }
        }

        /* ═══════════════════════════════════════════════════════════════
           Responsive — Desktop  (> 1024px)
           Strategy: split-panel — brand panel left, scrollable form right
        ═══════════════════════════════════════════════════════════════ */
        @media (min-width: 1025px) {
            body {
                padding: 0;
                min-height: 100vh;
                align-items: stretch;
            }

            .responsive_form {
                width: 100%;
                min-height: 100vh;
                display: flex;
            }

            .page-wrapper {
                width: 100%;
                min-height: 100vh;
                display: flex;
                align-items: stretch;
                justify-content: flex-start;
            }

            /* ── Left brand panel ────────────────────────── */
            .brand-panel {
                display: flex;
                flex: 0 0 40%;
                max-width: 480px;
                min-height: 100vh;
                background: linear-gradient(160deg, #1a1a2e 0%, #16213e 35%, #0f3460 65%, #533483 100%);
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 60px 44px;
                position: sticky;
                top: 0;
                height: 100vh;
                overflow: hidden;
            }

            /* decorative circles */
            .brand-panel::before {
                content: '';
                position: absolute;
                width: 320px;
                height: 320px;
                border-radius: 50%;
                background: rgba(255,255,255,0.03);
                top: -80px;
                right: -80px;
            }

            .brand-panel::after {
                content: '';
                position: absolute;
                width: 200px;
                height: 200px;
                border-radius: 50%;
                background: rgba(255,255,255,0.03);
                bottom: -50px;
                left: -50px;
            }

            .brand-panel-inner {
                position: relative;
                z-index: 1;
                text-align: center;
                width: 100%;
            }

            .bp-logo-wrap {
                width: 110px;
                height: 110px;
                border-radius: 24px;
                overflow: hidden;
                margin: 0 auto 20px;
                border: 2px solid rgba(255,255,255,0.15);
                box-shadow: 0 8px 32px rgba(0,0,0,0.3);
                background: rgba(255,255,255,0.08);
            }

            .bp-logo-wrap img {
                width: 100%;
                height: 100%;
                object-fit: contain;
            }

            .bp-title {
                font-size: 26px;
                font-weight: 700;
                color: #fff;
                letter-spacing: 4px;
                text-transform: uppercase;
                margin-bottom: 4px;
            }

            .bp-subtitle {
                font-size: 11px;
                color: rgba(255,255,255,0.45);
                letter-spacing: 3px;
                text-transform: uppercase;
                margin-bottom: 28px;
            }

            .bp-divider {
                width: 48px;
                height: 2px;
                background: linear-gradient(90deg, transparent, rgba(245,166,35,0.8), transparent);
                margin: 0 auto 24px;
                border-radius: 2px;
            }

            .bp-quote {
                font-size: 13.5px;
                color: rgba(255,255,255,0.6);
                line-height: 1.75;
                font-style: italic;
                margin-bottom: 36px;
                padding: 0 8px;
            }

            .bp-features {
                display: flex;
                flex-direction: column;
                gap: 14px;
                width: 100%;
            }

            .bp-feat {
                display: flex;
                align-items: center;
                gap: 14px;
                padding: 13px 18px;
                background: rgba(255,255,255,0.06);
                border-radius: 12px;
                border: 1px solid rgba(255,255,255,0.08);
            }

            .bp-feat i {
                font-size: 16px;
                color: #f5a623;
                width: 20px;
                text-align: center;
                flex-shrink: 0;
            }

            .bp-feat span {
                font-size: 13px;
                color: rgba(255,255,255,0.75);
                font-weight: 400;
            }

            /* ── Right form panel ────────────────────────── */
            .customer_form {
                flex: 1;
                max-width: none;
                margin: 0;
                border-radius: 0;
                padding: 48px 52px;
                overflow-y: auto;
                min-height: 100vh;
                background: #fff;
                box-shadow: none;
            }

            /* Hide mobile form header — brand panel takes its place */
            .form-header {
                display: none;
            }

            /* Wide 2-col row — active on desktop */
            .row-wide-2col {
                flex-direction: row;
                gap: 16px;
            }

            .row-wide-2col > .form-group {
                flex: 1;
                min-width: 0;
            }

            /* Section labels */
            .section-label {
                font-size: 11px;
                letter-spacing: 2px;
                margin: 24px 0 14px;
            }

            /* Form controls */
            .form-group {
                margin-bottom: 16px;
            }

            .form-group label {
                font-size: 13px;
                margin-bottom: 5px;
            }

            .form-control {
                padding: 11px 15px;
                font-size: 14px;
                border-radius: 10px;
            }

            .form-control::placeholder {
                font-size: 13px;
            }

            textarea.form-control {
                min-height: 70px;
            }

            /* Source options — 4-col row */
            .source-options {
                grid-template-columns: repeat(4, 1fr);
                gap: 10px;
            }

            .source-option label {
                flex-direction: column;
                justify-content: center;
                text-align: center;
                padding: 14px 10px;
                font-size: 12px;
                border-radius: 10px;
                gap: 6px;
            }

            .source-option label i {
                font-size: 18px;
                opacity: 0.6;
            }

            /* Country code col */
            .mobile-row .country-col {
                flex: 0 0 120px;
                max-width: 120px;
            }

            .mobile-row .country-col .select2-container {
                width: 120px !important;
            }

            /* Existing 2col — pin col wider */
            .row-2col { gap: 14px; }
            .row-2col .col-pin { flex: 0 0 130px; max-width: 130px; }

            /* Rating groups */
            .rating-group {
                padding: 13px 18px;
                border-radius: 10px;
                margin-bottom: 10px;
            }

            .rating-group .rating-label { font-size: 14px; }
            .star-rating i { font-size: 22px; }

            /* Select2 */
            .select2-container--default .select2-selection--single {
                height: 43px !important;
                padding: 8px 13px;
                border-radius: 10px;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 27px;
                font-size: 14px;
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 41px;
            }

            /* Submit */
            .btn-submit {
                padding: 14px;
                font-size: 15px;
                border-radius: 12px;
                margin-top: 12px;
            }
        }

        /* Force Select2 to fill container width */
        .select2-container {
            width: 100% !important;
        }
    </style>
</head>

<body>
    <form class="responsive_form" id="feedback_form">
        <input type="hidden" id="branch_id" name="branch_id" value="">
        <div class="page-wrapper">

            <!-- Brand Panel (desktop only — hidden on mobile/tablet) -->
            <div class="brand-panel">
                <div class="brand-panel-inner">
                    <div class="bp-logo-wrap">
                        <img src="<?= base_url(); ?>assets/img/logo.png" alt="Navratna Jewellery Logo">
                    </div>
                    <h2 class="bp-title">LOGIMAX</h2>
                    <p class="bp-subtitle">TECHNOLOGIES</p>
                    <div class="bp-divider"></div>
                    <p class="bp-quote">"Your feedback helps us craft a more delightful experience for every customer who walks through our doors."</p>
                    <div class="bp-features">
                        <div class="bp-feat"><i class="fas fa-gem"></i><span>Exquisite Collections</span></div>
                        <div class="bp-feat"><i class="fas fa-award"></i><span>Certified Quality</span></div>
                        <div class="bp-feat"><i class="fas fa-heart"></i><span>Customer First</span></div>
                    </div>
                </div>
            </div>

            <!-- Form Panel -->
            <div class="customer_form">

            <!-- Header -->
            <div class="form-header">
                <div class="logo-wrap">
                    <img src="<?= base_url(); ?>assets/img/logo.png" alt="Logo">
                </div>
                <h5>Let's know more about you!</h5>
                <p>We Value Your Feedback</p>
            </div>

            <!-- Personal Info Section -->
            <div class="section-label"><i class="fas fa-user"></i> Personal Details</div>

            <div class="form-group">
                <label for="cus_mobile">Mobile No <span class="text-danger">*</span></label>
                <div class="mobile-row">
                    <div class="country-col">
                        <select id="country_code" name="country_code" class="form-control"></select>
                    </div>
                    <div class="mobile-col">
                        <input type="number" id="cus_mobile" name="cus_mobile" class="form-control" placeholder="Mobile number" required>
                    </div>
                </div>
                <input type="hidden" id="mobile" name="mobile">
                <input type="hidden" id="cus_id" name="cus_id">
                <input type="hidden" id="is_existing_customer" value="0" name="is_existing_customer">
            </div>

            <div class="row-wide-2col">
                <div class="form-group">
                    <label for="cus_name">Name <span class="text-danger">*</span></label>
                    <input type="text" id="cus_name" name="cus_name" class="form-control" placeholder="Your full name" required>
                </div>

                <div class="form-group">
                    <label for="cus_mail">Email <span class="text-danger">*</span></label>
                    <input type="email" id="cus_mail" name="cus_mail" class="form-control" placeholder="email@example.com" required>
                </div>
            </div>

            <div class="row-2col">
                <div class="col-half">
                    <div class="form-group">
                        <label for="cus_dob">Date of Birth</label>
                        <input type="date" id="cus_dob" name="cus_dob" class="form-control">
                    </div>
                </div>
                <div class="col-half">
                    <div class="form-group">
                        <label for="marital_status">Married?</label>
                        <select id="marital_status" name="marital_status" class="form-control">
                            <option value="">-- Select --</option>
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group" id="anniversary_wrap" style="display:none;">
                <label for="cus_doa">Anniversary Date</label>
                <input type="date" id="cus_doa" name="cus_doa" class="form-control">
            </div>

            <!-- Address Section -->
            <div class="section-label"><i class="fas fa-map-marker-alt"></i> Address</div>

            <div class="form-group">
                <label for="address">Address <span class="text-danger">*</span></label>
                <textarea id="address" name="address" class="form-control" placeholder="Street address, locality..."></textarea>
            </div>

            <div class="row-2col">
                <div class="col-pin">
                    <div class="form-group">
                        <label for="cus_pincode">Pincode <span class="text-danger">*</span></label>
                        <input type="text" id="cus_pincode" name="cus_pincode" class="form-control" placeholder="Pincode" required>
                    </div>
                </div>
                <div class="col-half">
                    <div class="form-group">
                        <label for="cus_state">State <span class="text-danger">*</span></label>
                        <select id="cus_state" name="cus_state" class="form-control">
                            <option value=""></option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="cus_city">City <span class="text-danger">*</span></label>
                <select id="cus_city" name="cus_city" class="form-control"></select>
                <input type="hidden" id="ed_city">
            </div>

            <!-- Source Section -->
            <div class="section-label"><i class="fas fa-bullhorn"></i> How did you hear about us?</div>

            <div class="source-options">
                <div class="source-option">
                    <input type="radio" id="src_social" name="source" value="1" checked>
                    <label for="src_social"><i class="fas fa-share-alt"></i> Social Media</label>
                </div>
                <div class="source-option">
                    <input type="radio" id="src_billboard" name="source" value="2">
                    <label for="src_billboard"><i class="fas fa-ad"></i> Bill Board</label>
                </div>
                <div class="source-option">
                    <input type="radio" id="src_reference" name="source" value="3">
                    <label for="src_reference"><i class="fas fa-user-friends"></i> Reference</label>
                </div>
                <div class="source-option">
                    <input type="radio" id="src_telecalling" name="source" value="4">
                    <label for="src_telecalling"><i class="fas fa-phone-alt"></i> Tele Calling</label>
                </div>
            </div>

            <!-- Purchase Occasion -->
            <div class="section-label" style="margin-top:20px;"><i class="fas fa-shopping-bag"></i> Purchase Occasion</div>
            <div class="row-wide-2col">
                <div class="form-group">
                    <label for="purchase_occasion">What is the occasion for your purchase?</label>
                    <select id="purchase_occasion" name="purchase_occasion" class="form-control">
                        <option value="">-- Select Occasion --</option>
                        <option value="Festival">Festival</option>
                        <option value="Wedding">Wedding</option>
                        <option value="Engagement">Engagement</option>
                        <option value="Anniversary">Anniversary</option>
                        <option value="Birthday">Birthday</option>
                        <option value="Gifting">Gifting</option>
                        <option value="Self-Use">Self-Use</option>
                    </select>
                </div>

                <!-- Reason for Not Purchasing -->
                <div class="form-group">
                    <label for="reason_not_purchase">Reason for Not Purchasing <span style="font-size:11px;color:#aaa;">(if applicable)</span></label>
                    <textarea id="reason_not_purchase" name="reason_not_purchase" class="form-control" placeholder="Please share your reason..."></textarea>
                </div>
            </div>

            <!-- Dynamic Feedback Questions -->
            <?php if(!empty($header)){ ?>
            <div class="section-label" style="margin-top:20px;"><i class="fas fa-comment-dots"></i> Your Thoughts</div>
            <div class="dynamic-feedback">
                <?php foreach($header as $h){ ?>
                <div class="form-group">
                    <label><?= $h['feedback_question']; ?></label>
                    <textarea name="feedback[<?= $h['feedback_header_id']; ?>]" class="form-control" placeholder="Share your thoughts..."></textarea>
                </div>
                <?php } ?>
            </div>
            <?php } ?>

            <!-- Rating Section -->
            <div class="section-label" style="margin-top:20px;"><i class="fas fa-star"></i> Rate Your Experience</div>

            <!-- Staff Code + Rating -->
            <div class="form-group">
                <label for="staff_id">Staff Code <span style="font-size:11px;color:#aaa;">(Select the staff who assisted you)</span></label>
                <select id="staff_id" name="staff_id" class="form-control">
                    <option value="">-- Select Staff --</option>
                </select>
            </div>
            <div class="rating-group" id="staff_rating_wrap" style="display:none;">
                <span class="rating-label">Rate Staff</span>
                <div class="star-rating" data-target="#rating_staff">
                    <i class="fas fa-star" data-rating="1"></i>
                    <i class="fas fa-star" data-rating="2"></i>
                    <i class="fas fa-star" data-rating="3"></i>
                    <i class="fas fa-star" data-rating="4"></i>
                    <i class="fas fa-star" data-rating="5"></i>
                </div>
                <input type="hidden" id="rating_staff" name="rating_staff" value="0">
            </div>

            <div class="rating-group">
                <span class="rating-label">Ambiance</span>
                <div class="star-rating" data-target="#rating_ambiance">
                    <i class="fas fa-star" data-rating="1"></i>
                    <i class="fas fa-star" data-rating="2"></i>
                    <i class="fas fa-star" data-rating="3"></i>
                    <i class="fas fa-star" data-rating="4"></i>
                    <i class="fas fa-star" data-rating="5"></i>
                </div>
                <input type="hidden" id="rating_ambiance" name="rating_ambiance" value="0">
            </div>

            <div class="rating-group">
                <span class="rating-label">Collection</span>
                <div class="star-rating" data-target="#rating_collection">
                    <i class="fas fa-star" data-rating="1"></i>
                    <i class="fas fa-star" data-rating="2"></i>
                    <i class="fas fa-star" data-rating="3"></i>
                    <i class="fas fa-star" data-rating="4"></i>
                    <i class="fas fa-star" data-rating="5"></i>
                </div>
                <input type="hidden" id="rating_collection" name="rating_collection" value="0">
            </div>

            <!-- Suggestions -->
            <div class="section-label" style="margin-top:20px;"><i class="fas fa-lightbulb"></i> Suggestions</div>
            <div class="form-group">
                <label for="suggestions">Any suggestions to better your experience?</label>
                <textarea id="suggestions" name="suggestions" class="form-control" rows="4" placeholder="Share your thoughts, ideas, or suggestions with us..."></textarea>
            </div>

            <!-- Submit -->
            <button type="button" class="btn-submit" id="feedback_save">
                <i class="fas fa-paper-plane"></i>&nbsp; Submit Feedback
            </button>

            </div><!-- /.customer_form -->
        </div><!-- /.page-wrapper -->
    </form>

    <script type="text/javascript">
        var base_url = "<?php echo base_url(); ?>";

        // ── Read branch_id from URL query string ──────────────────────────
        (function() {
            var params = new URLSearchParams(window.location.search);
            var bid = params.get('branch_id');
            if (bid) {
                document.getElementById('branch_id').value = bid;
            }
        })();
    </script>
    <!-- jQuery -->
    <script src="<?php echo base_url(); ?>assets/plugins/jQuery/jQuery-2.1.4.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <!-- Toaster and custom scripts -->
    <script src="<?php echo base_url(); ?>assets/js/remarks.js"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/toaster/jquery.toaster.js"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/select2/select2.full.min.js"></script>
    <link rel="stylesheet" href="<?php echo base_url('assets/plugins/select2/select2.min.css'); ?>">

</body>

</html>