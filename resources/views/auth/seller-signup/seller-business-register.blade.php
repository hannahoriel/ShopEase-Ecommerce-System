<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        ShopEase - Business Information
    </title>


    <!-- =========================================================
         POPPINS
    ========================================================== -->

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    @vite(['resources/css/app.css'])


    <style>

        :root {

            --maroon-dark: #52070B;
            --maroon: #7B1B1B;
            --maroon-light: #A52A2A;

            --peach: #FF876E;
            --peach-light: #FFD8CF;

            --background: #FBF5F2;
            --white: #FFFFFF;

            --text-dark: #161616;
            --text-muted: #999393;

            --border: #D7D7D7;

            --success: #2E7D32;
        }


        * {

            box-sizing:
                border-box;

        }


        body {

            margin:
                0;

            padding:
                0;

            min-height:
                100vh;

            font-family:
                'Poppins',
                sans-serif;

            background:
                var(--background);

            color:
                var(--text-dark);

        }


        /* =========================================================
           PAGE
        ========================================================== */

        .registration-page {

            min-height:
                100vh;

            position:
                relative;

            overflow-x:
                hidden;

        }


        /* =========================================================
           HEADER
        ========================================================== */

        .registration-header {

            display:
                flex;

            align-items:
                center;

            gap:
                26px;

            padding-top:
                28px;

            padding-left:
                0;

        }


        /* =========================================================
           CLICKABLE LOGO
           NOW RETURNS TO LANDING PAGE
        ========================================================== */

        .header-logo-wrapper {

            width:
                128px;

            height:
                105px;

            background:
                var(--maroon-dark);

            border-top-right-radius:
                60px;

            border-bottom-right-radius:
                60px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            flex-shrink:
                0;

            text-decoration:
                none;

            cursor:
                pointer;

            transition:
                opacity 0.2s ease,
                transform 0.2s ease;

        }


        .header-logo-wrapper:hover {

            opacity:
                0.92;

            transform:
                translateX(2px);

        }


        .header-logo-wrapper:focus-visible {

            outline:
                2px solid
                var(--maroon);

            outline-offset:
                4px;

        }


        .signup-logo {

            width:
                68px;

            height:
                auto;

            object-fit:
                contain;

        }


        .header-copy {

            padding-top:
                0;

        }


        .header-title {

            margin:
                0;

            font-size:
                31px;

            line-height:
                1.15;

            font-weight:
                700;

            color:
                var(--text-dark);

        }


        .header-title span {

            color:
                var(--maroon-dark);

        }


        .header-subtitle {

            margin:
                3px 0 0;

            font-size:
                19px;

            line-height:
                1.3;

            font-weight:
                400;

            color:
                #969696;

        }


        /* =========================================================
           STEPPER
        ========================================================== */

        .stepper-wrapper {

            width:
                560px;

            margin:
                18px auto 18px;

            display:
                flex;

            align-items:
                flex-start;

            justify-content:
                center;

        }


        .step {

            flex:
                1;

            display:
                flex;

            flex-direction:
                column;

            align-items:
                center;

            position:
                relative;

            text-decoration:
                none;

            color:
                inherit;

        }


        .step:not(:last-child)::after {

            content:
                "";

            position:
                absolute;

            top:
                16px;

            left:
                50%;

            width:
                100%;

            height:
                1.5px;

            background:
                #9E9E9E;

            z-index:
                0;

        }


        .step-clickable {

            cursor:
                pointer;

        }


        .step-number {

            width:
                34px;

            height:
                34px;

            border-radius:
                50%;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            font-size:
                14px;

            font-weight:
                500;

            border:
                1.5px solid
                #999;

            color:
                #888;

            background:
                var(--background);

            position:
                relative;

            z-index:
                1;

            transition:
                transform 0.2s ease,
                background 0.2s ease,
                border-color 0.2s ease,
                color 0.2s ease;

        }


        .step-clickable:hover .step-number {

            transform:
                scale(1.06);

            border-color:
                var(--maroon);

            color:
                var(--maroon-dark);

        }


        .step-clickable:hover .step-label {

            color:
                var(--maroon-dark);

        }


        .step.active .step-number {

            background:
                var(--maroon);

            border-color:
                var(--maroon);

            color:
                white;

            font-weight:
                600;

        }


        .step-label {

            margin-top:
                6px;

            font-size:
                14px;

            font-weight:
                400;

            color:
                #969696;

            white-space:
                nowrap;

        }


        .step.active .step-label {

            color:
                var(--maroon-dark);

            font-weight:
                500;

        }


        /* =========================================================
           CONTENT
        ========================================================== */

        .registration-content {

            width:
                100%;

            padding:
                0
                55px
                40px;

        }


        .registration-card {

            width:
                100%;

            max-width:
                1580px;

            min-height:
                685px;

            margin:
                0 auto;

            background:
                var(--white);

            border-radius:
                12px;

            box-shadow:
                0 2px 12px
                rgba(
                    0,
                    0,
                    0,
                    0.04
                );

            padding:
                30px
                46px
                26px;

            position:
                relative;

        }


        /* =========================================================
           BUSINESS GRID
        ========================================================== */

        .business-layout {

            display:
                grid;

            grid-template-columns:
                440px
                440px
                1fr;

            column-gap:
                55px;

            width:
                100%;

        }


        /* =========================================================
           FORM FIELD
        ========================================================== */

        .form-group {

            min-width:
                0;

        }


        .form-label {

            display:
                block;

            margin-bottom:
                8px;

            font-size:
                15px;

            line-height:
                1.2;

            font-weight:
                500;

            color:
                var(--text-dark);

        }


        .required {

            color:
                #E01D1D;

        }


        .form-input {

            width:
                100%;

            height:
                48px;

            border:
                1.5px solid
                var(--border);

            border-radius:
                9px;

            padding:
                0
                14px;

            background:
                white;

            font-family:
                'Poppins',
                sans-serif;

            font-size:
                14px;

            color:
                var(--text-dark);

            outline:
                none;

            transition:
                border-color
                0.2s ease,
                box-shadow
                0.2s ease;

        }


        .form-input::placeholder {

            color:
                #A6A6A6;

        }


        .form-input:focus {

            border-color:
                #B8B8B8;

            box-shadow:
                0 0 0 3px
                rgba(
                    123,
                    27,
                    27,
                    0.06
                );

        }


        /* =========================================================
           BUSINESS PERMIT
        ========================================================== */

        .permit-upload {

            width:
                100%;

            height:
                123px;

            border:
                1.5px solid
                var(--border);

            border-radius:
                9px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            cursor:
                pointer;

            background:
                white;

            transition:
                border-color
                0.2s ease,
                background
                0.2s ease,
                box-shadow
                0.2s ease;

        }


        .permit-upload:hover {

            border-color:
                var(--maroon);

            background:
                #FFFDFC;

            box-shadow:
                0 4px 14px
                rgba(
                    82,
                    7,
                    11,
                    0.05
                );

        }


        .permit-upload input {

            display:
                none;

        }


        .permit-placeholder {

            text-align:
                center;

            color:
                #A8A8A8;

            padding:
                10px;

            max-width:
                100%;

        }


        .permit-icon {

            width:
                40px;

            height:
                40px;

            margin:
                0
                auto
                8px;

        }


        .permit-text {

            margin:
                0;

            font-size:
                14px;

            color:
                #A5A5A5;

            max-width:
                360px;

            word-break:
                break-word;

        }


        .permit-selected {

            color:
                var(--maroon);

            font-weight:
                500;

            max-width:
                360px;

            word-break:
                break-word;

        }


        .permit-existing {

            display:
                block;

            margin-top:
                4px;

            color:
                #A5A5A5;

            font-size:
                11px;

        }


        /* =========================================================
           CATEGORY
        ========================================================== */

        .category-section {

            margin-top:
                36px;

            width:
                440px;

        }


        .category-title {

            margin:
                0;

            font-size:
                16px;

            font-weight:
                500;

            color:
                var(--text-dark);

        }


        .category-subtitle {

            margin:
                3px
                0
                12px;

            font-size:
                14px;

            color:
                #A0A0A0;

        }


        .category-list {

            display:
                flex;

            flex-direction:
                column;

            gap:
                7px;

        }


        .category-option {

            display:
                flex;

            align-items:
                center;

            gap:
                12px;

            min-height:
                23px;

            cursor:
                pointer;

            user-select:
                none;

        }


        .category-option input {

            display:
                none;

        }


        .custom-checkbox {

            width:
                18px;

            height:
                18px;

            border-radius:
                4px;

            border:
                1.7px solid
                #9B9B9B;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            flex-shrink:
                0;

            transition:
                border-color
                0.2s ease,
                background
                0.2s ease;

        }


        .category-option input:checked + .custom-checkbox {

            border-color:
                #C92B2B;

        }


        .category-option input:checked + .custom-checkbox {
            background: #7B1B1B;
            border-color: #7B1B1B;
        }
        .category-option input:checked + .custom-checkbox::after {
            content: "";
            width: 5px;
            height: 9px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg) translateY(-1px);
        }


        .category-name {

            font-size:
                15px;

            color:
                #242424;

        }


        /* =========================================================
           BUTTON
        ========================================================== */

        .form-actions {

            position:
                absolute;

            right:
                46px;

            bottom:
                26px;

            display:
                flex;

            justify-content:
                flex-end;

        }


        .continue-button {

            width:
                165px;

            height:
                48px;

            border:
                none;

            border-radius:
                24px;

            background:
                var(--maroon);

            color:
                white;

            font-family:
                'Poppins',
                sans-serif;

            font-size:
                16px;

            font-weight:
                600;

            cursor:
                pointer;

            transition:
                background
                0.2s ease,
                transform
                0.1s ease,
                box-shadow
                0.2s ease;

        }


        .continue-button:hover {

            background:
                #661515;

            box-shadow:
                0 5px 14px
                rgba(
                    123,
                    27,
                    27,
                    0.15
                );

        }


        .continue-button:active {

            transform:
                scale(0.98);

            box-shadow:
                none;

        }


        .continue-button:disabled {

            opacity:
                0.7;

            cursor:
                not-allowed;

            box-shadow:
                none;

        }


        /* =========================================================
           ERROR
        ========================================================== */

        .error-message {

            margin-top:
                5px;

            font-size:
                12px;

            color:
                #C62828;

        }


        /* =========================================================
           RESPONSIVE
        ========================================================== */

        @media (max-width: 1200px) {

            .business-layout {

                grid-template-columns:
                    minmax(0, 1fr)
                    minmax(0, 1fr);

                column-gap:
                    30px;

            }


            .category-section {

                width:
                    100%;

                grid-column:
                    1 / -1;

            }

        }


        @media (max-width: 760px) {

            .registration-header {

                gap:
                    18px;

                padding-top:
                    24px;

            }


            .header-logo-wrapper {

                width:
                    105px;

                height:
                    100px;

                border-top-right-radius:
                    55px;

                border-bottom-right-radius:
                    55px;

            }


            .signup-logo {

                width:
                    70px;

            }


            .header-title {

                font-size:
                    25px;

            }


            .header-subtitle {

                font-size:
                    16px;

            }


            .stepper-wrapper {

                width:
                    95%;

                margin-top:
                    24px;

            }


            .step-label {

                font-size:
                    13px;

            }


            .registration-content {

                padding:
                    0
                    20px
                    30px;

            }


            .registration-card {

                min-height:
                    auto;

                padding:
                    24px
                    20px
                    95px;

            }


            .business-layout {

                display:
                    grid;

                grid-template-columns:
                    1fr;

                gap:
                    25px;

            }


            .category-section {

                grid-column:
                    auto;

                width:
                    100%;

                margin-top:
                    0;

            }


            .form-actions {

                position:
                    static;

                margin-top:
                    30px;

                justify-content:
                    flex-end;

            }


            .continue-button {

                width:
                    150px;

            }

        }

        .se-business-permit-list {margin-top:10px;display:grid;gap:8px;}
    .se-permit-list-item {display:flex;align-items:center;gap:8px;padding:9px 12px;border:1px solid #EADAD5;border-radius:9px;font-size:12px;color:#651319;overflow-wrap:anywhere;}
    .se-permit-list-item small {margin-left:auto;color:#7A7A7A;}
    .se-permit-error{color:#C62828;border-color:#F1C6C6;}
    .se-permit-thumb{height:40px;width:40px;object-fit:cover;border-radius:6px;}
</style>


<style>
/* ShopEase seller registration: validation feedback only; existing layout unchanged */
.se-validation-message{display:block;margin:6px 0 0;font:400 11px/1.5 'Poppins',sans-serif;color:#C62828}
.se-validation-invalid{border-color:#C62828!important;box-shadow:0 0 0 2px rgba(198,40,40,.06)!important}
.se-password-rules{margin-top:10px;padding:12px 14px;border:1px solid #EEE0DC;border-radius:9px;background:#FFFBFA;font:400 12px/1.8 'Poppins',sans-serif}
.se-password-rules span{display:block;color:#8A7774}
.se-password-rules span.ok{color:#25804B}
.se-password-rules span::before{content:'○ ';font-weight:700}
.se-password-rules span.ok::before{content:'✓ '}
</style>

</head>


<body>

<div class="registration-page">


    <!-- =========================================================
         HEADER
    ========================================================== -->

    <header class="registration-header">


        <!-- CLICKABLE LOGO / RETURN TO LANDING PAGE -->

        <a
            href="{{ url('/') }}"
            class="header-logo-wrapper"
            id="sellerRegistrationExit"
            aria-label="Return to ShopEase landing page"
        >

            <img
                src="{{ asset('icons/login/signup-logo.png') }}"
                alt="ShopEase"
                class="signup-logo"
            >

        </a>


        <div class="header-copy">

            <h1 class="header-title">

                Seller <span>Registration</span>

            </h1>


            <p class="header-subtitle">

                Fill the details below to create your seller account.

            </p>

        </div>

    </header>



    <!-- =========================================================
         STEPPER
    ========================================================== -->

    <div class="stepper-wrapper">


        <!-- STEP 1 -->

        <a
            href="{{ route('seller.register') }}"
            class="step step-clickable"
            id="stepOneLink"
            aria-label="Go to Seller Information"
        >

            <div class="step-number">

                1

            </div>


            <div class="step-label">

                Seller Information

            </div>

        </a>



        <!-- STEP 2 -->

        <a
            href="{{ route('seller.business.register') }}"
            class="step step-clickable active"
            id="stepTwoLink"
            aria-label="Go to Business Information"
        >

            <div class="step-number">

                2

            </div>


            <div class="step-label">

                Business Information

            </div>

        </a>



        <!-- STEP 3 -->

        <a
            href="{{ route('seller.review.register') }}"
            class="step step-clickable"
            id="stepThreeLink"
            aria-label="Go to Review Information"
        >

            <div class="step-number">

                3

            </div>


            <div class="step-label">

                Review Information

            </div>

        </a>

    </div>



    <!-- =========================================================
         CONTENT
    ========================================================== -->

    <main class="registration-content">


        <form
            id="businessRegistrationForm"
            method="POST"
            action="{{ route('seller.business.submit') }}"
            enctype="multipart/form-data"
        >

            @csrf


            <div class="registration-card">


                <div class="business-layout">


                    <!-- =================================================
                         BUSINESS NAME
                    ================================================== -->

                    <div class="form-group">


                        <label
                            for="business_name"
                            class="form-label"
                        >

                            Shop Name

                            <span class="required">

                                *

                            </span>

                        </label>


                        <input
                            type="text"
                            id="business_name"
                            name="business_name"
                            class="form-input"
                            placeholder="Enter your shop name"
                            value="{{ old('business_name', $sellerData['business_name'] ?? '') }}"
                            minlength="3"
                            maxlength="30"
                            required
                        >


                        @error('business_name')

                            <p class="error-message">

                                {{ $message }}

                            </p>

                        @enderror


                    </div>



                    <!-- =================================================
                         BUSINESS PERMIT
                    ================================================== -->

                    <div class="form-group">


                        <label
                            class="form-label"
                            for="business_permit"
                        >

                            Business Permit

                            <span class="required">

                                *

                            </span>

                        </label>


                        <label
                            for="business_permit"
                            class="permit-upload"
                        >


                            <input
                                type="file"
                                id="business_permit"
                                name="business_permits[]"
                                multiple
                                accept=".jpg,.jpeg,.png,.pdf"
                            >


                            <div
                                class="permit-placeholder"
                                id="permitPlaceholder"
                            >


                                @if(!empty($sellerData['business_permit_path'] ?? null))


                                    <svg
                                        class="permit-icon"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="#7B1B1B"
                                        stroke-width="1.7"
                                    >

                                        <path
                                            d="M20 6L9 17l-5-5"
                                        />

                                    </svg>


                                    <p class="permit-text permit-selected">

                                        {{ $sellerData['business_permit_original_name'] ?? basename($sellerData['business_permit_path']) }}

                                    </p>


                                    <small class="permit-existing">

                                        Existing permit saved. Select additional permits to upload.

                                    </small>


                                @else


                                    <svg
                                        class="permit-icon"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.7"
                                    >

                                        <rect
                                            x="3"
                                            y="3"
                                            width="18"
                                            height="18"
                                            rx="3"
                                        />


                                        <circle
                                            cx="8.5"
                                            cy="8.5"
                                            r="1.4"
                                        />


                                        <path
                                            d="M21 15l-4.5-4.5L10 17l-3-3-4 4"
                                        />

                                    </svg>


                                    <p class="permit-text">

                                        Upload business permits (multiple files allowed)

                                    </p>


                                @endif


                            </div>

                        </label>


                        <div id="selectedBusinessPermits" class="se-business-permit-list" aria-live="polite">
                            @php
                                $previousPermits = $sellerData['business_permits'] ?? $sellerData['business_permit_paths'] ?? [];
                                if (!is_array($previousPermits)) $previousPermits = [];
                            @endphp
                            @foreach($previousPermits as $permitIndex => $permit)
                                @php
                                    $permitName = is_array($permit)
                                        ? ($permit['original_name'] ?? $permit['name'] ?? basename($permit['path'] ?? 'permit'))
                                        : basename((string) $permit);
                                @endphp
                                <div class="se-permit-list-item" data-persisted="1">✓ {{ $permitName }} <small>Previously uploaded</small></div>
                            @endforeach
                        </div>

                        @error('business_permit')

                            <p class="error-message">

                                {{ $message }}

                            </p>

                        @enderror


                    </div>




                    <!-- =================================================
                         SELLER TYPE, TIN AND REQUIRED CERTIFICATES
                    ================================================== -->
                    <div class="form-group">
                        <label class="form-label" for="seller_type">Seller Type <span class="required">*</span></label>
                        <select id="seller_type" name="seller_type" class="form-input" required>
                            <option value="">Select seller type</option>
                            <option value="Individual" {{ old('seller_type', $sellerData['seller_type'] ?? '') === 'Individual' ? 'selected' : '' }}>Individual</option>
                            <option value="Sole Proprietorship" {{ old('seller_type', $sellerData['seller_type'] ?? '') === 'Sole Proprietorship' ? 'selected' : '' }}>Sole Proprietorship</option>
                            <option value="Corporation" {{ old('seller_type', $sellerData['seller_type'] ?? '') === 'Corporation' ? 'selected' : '' }}>Corporation</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="tin">TIN <span class="required">*</span></label>
                        <input type="text" id="tin" name="tin" class="form-input" placeholder="000-000-000-000" value="{{ old('tin', $sellerData['tin'] ?? '') }}" maxlength="15" inputmode="numeric" pattern="[0-9]{3}-[0-9]{3}-[0-9]{3}-[0-9]{3}" title="Enter a 12-digit TIN in 000-000-000-000 format." required>
                        @error('tin') <p class="error-message">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group" id="dtiUploadGroup" hidden>
                        <label class="form-label" for="dti_certificate">DTI Certificate <span class="required">*</span></label>
                        <input type="file" id="dti_certificate" name="dti_certificate" class="form-input" accept=".jpg,.jpeg,.png,.pdf">
                        <small class="field-help">JPG, PNG, or PDF; maximum 5 MB.</small>
                    </div>

                    <div class="form-group" id="secUploadGroup" hidden>
                        <label class="form-label" for="sec_certificate">SEC Certificate <span class="required">*</span></label>
                        <input type="file" id="sec_certificate" name="sec_certificate" class="form-input" accept=".jpg,.jpeg,.png,.pdf">
                        <small class="field-help">JPG, PNG, or PDF; maximum 5 MB.</small>
                    </div>

                    <div class="form-group" id="birUploadGroup" hidden>
                        <label class="form-label" for="bir_2303">BIR Form 2303 <span class="required">*</span></label>
                        <input type="file" id="bir_2303" name="bir_2303" class="form-input" accept=".jpg,.jpeg,.png,.pdf">
                        <small class="field-help">JPG, PNG, or PDF; maximum 5 MB.</small>
                    </div>

                    <!-- =================================================
                         SHOP CATEGORY
                    ================================================== -->
                    <div class="category-section">


                        <h3 class="category-title">

                            Select Category

                            <span class="required">

                                *

                            </span>

                        </h3>


                        <p class="category-subtitle">

                            You can select multiple category.

                        </p>


                        @php

                            $selectedCategories = old(
                                'categories',
                                $sellerData['categories'] ?? []
                            );

                            if (is_string($selectedCategories)) {

                                $decodedCategories =
                                    json_decode(
                                        $selectedCategories,
                                        true
                                    );

                                $selectedCategories =
                                    is_array(
                                        $decodedCategories
                                    )
                                        ? $decodedCategories
                                        : [];

                            }

                            if (!is_array($selectedCategories)) {

                                $selectedCategories = [];

                            }

                        @endphp



                        <div class="category-list">


                            <!-- PET SUPPLIES -->

                            <label class="category-option">

                                <input
                                    type="checkbox"
                                    name="categories[]"
                                    value="Pet Supplies"
                                    {{ in_array('Pet Supplies', $selectedCategories) ? 'checked' : '' }}
                                >

                                <span class="custom-checkbox"></span>

                                <span class="category-name">

                                    Pet Supplies

                                </span>

                            </label>



                            <!-- ELECTRONICS -->

                            <label class="category-option">

                                <input
                                    type="checkbox"
                                    name="categories[]"
                                    value="Electronics and Gadgets"
                                    {{ in_array('Electronics and Gadgets', $selectedCategories) ? 'checked' : '' }}
                                >

                                <span class="custom-checkbox"></span>

                                <span class="category-name">

                                    Electronics and Gadgets

                                </span>

                            </label>



                            <!-- WOMEN'S APPAREL -->

                            <label class="category-option">

                                <input
                                    type="checkbox"
                                    name="categories[]"
                                    value="Women's Apparel"
                                    {{ in_array("Women's Apparel", $selectedCategories) ? 'checked' : '' }}
                                >

                                <span class="custom-checkbox"></span>

                                <span class="category-name">

                                    Women's Apparel

                                </span>

                            </label>



                            <!-- MEN'S APPAREL -->

                            <label class="category-option">

                                <input
                                    type="checkbox"
                                    name="categories[]"
                                    value="Men's Apparel"
                                    {{ in_array("Men's Apparel", $selectedCategories) ? 'checked' : '' }}
                                >

                                <span class="custom-checkbox"></span>

                                <span class="category-name">

                                    Men's Apparel

                                </span>

                            </label>



                            <!-- KIDS AND BABY -->

                            <label class="category-option">

                                <input
                                    type="checkbox"
                                    name="categories[]"
                                    value="Kids and Baby"
                                    {{ in_array('Kids and Baby', $selectedCategories) ? 'checked' : '' }}
                                >

                                <span class="custom-checkbox"></span>

                                <span class="category-name">

                                    Kids and Baby

                                </span>

                            </label>



                            <!-- HOME AND GARDEN -->

                            <label class="category-option">

                                <input
                                    type="checkbox"
                                    name="categories[]"
                                    value="Home and Garden"
                                    {{ in_array('Home and Garden', $selectedCategories) ? 'checked' : '' }}
                                >

                                <span class="custom-checkbox"></span>

                                <span class="category-name">

                                    Home and Garden

                                </span>

                            </label>



                            <!-- SPORTS AND OUTDOORS -->

                            <label class="category-option">

                                <input
                                    type="checkbox"
                                    name="categories[]"
                                    value="Sports and Outdoors"
                                    {{ in_array('Sports and Outdoors', $selectedCategories) ? 'checked' : '' }}
                                >

                                <span class="custom-checkbox"></span>

                                <span class="category-name">

                                    Sports and Outdoors

                                </span>

                            </label>



                            <!-- HEALTH AND BEAUTY -->

                            <label class="category-option">

                                <input
                                    type="checkbox"
                                    name="categories[]"
                                    value="Health and Beauty"
                                    {{ in_array('Health and Beauty', $selectedCategories) ? 'checked' : '' }}
                                >

                                <span class="custom-checkbox"></span>

                                <span class="category-name">

                                    Health and Beauty

                                </span>

                            </label>



                            <!-- BOOKS AND MEDIA -->

                            <label class="category-option">

                                <input
                                    type="checkbox"
                                    name="categories[]"
                                    value="Books and Media"
                                    {{ in_array('Books and Media', $selectedCategories) ? 'checked' : '' }}
                                >

                                <span class="custom-checkbox"></span>

                                <span class="category-name">

                                    Books and Media

                                </span>

                            </label>



                            <!-- FOOD AND GOURMET -->

                            <label class="category-option">

                                <input
                                    type="checkbox"
                                    name="categories[]"
                                    value="Food and Gourmet"
                                    {{ in_array('Food and Gourmet', $selectedCategories) ? 'checked' : '' }}
                                >

                                <span class="custom-checkbox"></span>

                                <span class="category-name">

                                    Food and Gourmet

                                </span>

                            </label>



                            <!-- AUTOMOTIVE -->

                            <label class="category-option">

                                <input
                                    type="checkbox"
                                    name="categories[]"
                                    value="Automotive & Motorcycle"
                                    {{ in_array('Automotive & Motorcycle', $selectedCategories) ? 'checked' : '' }}
                                >

                                <span class="custom-checkbox"></span>

                                <span class="category-name">

                                    Automotive &amp; Motorcycle

                                </span>

                            </label>



                            <!-- FURNITURE -->

                            <label class="category-option">

                                <input
                                    type="checkbox"
                                    name="categories[]"
                                    value="Furniture and Office Equipment"
                                    {{ in_array('Furniture and Office Equipment', $selectedCategories) ? 'checked' : '' }}
                                >

                                <span class="custom-checkbox"></span>

                                <span class="category-name">

                                    Furniture and Office Equipment

                                </span>

                            </label>



                            <!-- JEWELRY -->

                            <label class="category-option">

                                <input
                                    type="checkbox"
                                    name="categories[]"
                                    value="Jewelry and Watches"
                                    {{ in_array('Jewelry and Watches', $selectedCategories) ? 'checked' : '' }}
                                >

                                <span class="custom-checkbox"></span>

                                <span class="category-name">

                                    Jewelry and Watches

                                </span>

                            </label>



                            <!-- OFFICE -->

                            <label class="category-option">

                                <input
                                    type="checkbox"
                                    name="categories[]"
                                    value="Office and School Supplies"
                                    {{ in_array('Office and School Supplies', $selectedCategories) ? 'checked' : '' }}
                                >

                                <span class="custom-checkbox"></span>

                                <span class="category-name">

                                    Office and School Supplies

                                </span>

                            </label>


                        </div>


                        @error('categories')

                            <p class="error-message">

                                {{ $message }}

                            </p>

                        @enderror


                    </div>


                </div>



                <!-- =====================================================
                     CONTINUE BUTTON
                ====================================================== -->

                <div class="form-actions">

                    <button
                        type="submit"
                        class="continue-button"
                        id="continueBusinessButton"
                    >

                        Continue

                    </button>

                </div>


            </div>

        </form>

    </main>

</div>



<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {


        /* =========================================================
           ELEMENTS
        ========================================================== */

        const form =
            document.getElementById(
                'businessRegistrationForm'
            );


        const permitInput = document.getElementById('business_permit');
        const permitPlaceholder = document.getElementById('permitPlaceholder');
        const sellerTypeInput = document.getElementById('seller_type');
        const hasSelectedShopCategory = () =>
            Boolean(form?.querySelector('input[name="categories[]"]:checked'));
        const docGroups = {
            dti: document.getElementById('dtiUploadGroup'),
            sec: document.getElementById('secUploadGroup'),
            bir: document.getElementById('birUploadGroup')
        };
        const docInputs = {
            dti: document.getElementById('dti_certificate'),
            sec: document.getElementById('sec_certificate'),
            bir: document.getElementById('bir_2303')
        };
        function updateSellerDocuments() {
            const type = sellerTypeInput?.value || '';
            const showDTI = type === 'Sole Proprietorship';
            const showSEC = type === 'Corporation';
            const showBIR = showDTI || showSEC;
            [[docGroups.dti, docInputs.dti, showDTI],
             [docGroups.sec, docInputs.sec, showSEC],
             [docGroups.bir, docInputs.bir, showBIR]].forEach(([group, input, visible]) => {
                if (group) group.hidden = !visible;
                if (input) {
                    input.required = !!visible;
                    if (!visible) input.value = '';
                }
            });
        }
        sellerTypeInput?.addEventListener('change', updateSellerDocuments);
        updateSellerDocuments();


        const continueButton =
            document.getElementById(
                'continueBusinessButton'
            );


        const categoryInputs = [];




        /* =========================================================
           BUSINESS PERMIT PREVIEW
        ========================================================== */

        if (
            permitInput &&
            permitPlaceholder
        ) {

            permitInput.addEventListener(
                'change',
                function () {


                    if (
                        !this.files ||
                        !this.files.length
                    ) {

                        return;

                    }


                    const files = Array.from(this.files);
                    const valid = files.filter(file => {
                        const extension = file.name.split('.').pop().toLowerCase();
                        return ['jpg','jpeg','png','pdf'].includes(extension) && file.size <= 5120 * 1024;
                    });
                    const invalid = files.filter(file => !valid.includes(file));
                    permitPlaceholder.innerHTML = `<p class="permit-text permit-selected">${valid.length} permit(s) selected</p><small class="permit-existing">JPG, PNG or PDF, max 5 MB each</small>`;
                    const list = document.getElementById('selectedBusinessPermits');
                    if (list) {
                        const preserved = Array.from(list.querySelectorAll('[data-persisted]'));
                        list.replaceChildren(...preserved);
                        for (const file of valid) {
                            const item = document.createElement('div');
                            item.className = 'se-permit-list-item';
                            const title = document.createElement('span');
                            title.textContent = file.name + ' (' + (file.size / 1048576).toFixed(2) + ' MB)';
                            item.appendChild(title);
                            if (file.type.startsWith('image/')) {
                                const thumb = document.createElement('img');
                                thumb.className = 'se-permit-thumb';
                                const url = URL.createObjectURL(file);
                                thumb.src = url;
                                thumb.onload = () => URL.revokeObjectURL(url);
                                item.prepend(thumb);
                            }
                            list.appendChild(item);
                        }
                        for (const file of invalid) {
                            const item = document.createElement('div');
                            item.className = 'se-permit-list-item se-permit-error';
                            item.textContent = file.name + ': JPG/PNG/PDF only, max 5 MB each.';
                            list.appendChild(item);
                        }
                    }
                    if (invalid.length) this.setCustomValidity('Remove invalid permit files (JPG, PNG, PDF, max 5 MB each).');
                    else this.setCustomValidity('');

                }
            );

        }



        /* =========================================================
           NORMAL FORM SUBMISSION
        ========================================================== */

        if (form) {

            form.addEventListener(
                'submit',
                function (event) {


                    if (!hasSelectedShopCategory()) {
                        event.preventDefault();
                        alert('Please select one shop category.');
                        form?.querySelector('input[name="categories[]"]')?.focus();
                        return;
                    }


                    const sellerType = sellerTypeInput?.value || '';
                    const requiredDocs = sellerType === 'Sole Proprietorship'
                        ? [['DTI Certificate', docInputs.dti], ['BIR Form 2303', docInputs.bir]]
                        : sellerType === 'Corporation'
                            ? [['SEC Certificate', docInputs.sec], ['BIR Form 2303', docInputs.bir]]
                            : [];
                    for (const [label, input] of requiredDocs) {
                        if (!input || !input.files || !input.files.length) {
                            event.preventDefault();
                            alert('Please upload the required ' + label + '.');
                            input?.focus();
                            return;
                        }
                        const file = input.files[0];
                        const ext = file.name.split('.').pop().toLowerCase();
                        if (!['jpg','jpeg','png','pdf'].includes(ext) || file.size > 5120 * 1024) {
                            event.preventDefault();
                            alert(label + ' must be JPG, PNG, or PDF and no larger than 5 MB.');
                            input.focus();
                            return;
                        }
                    }
                    const tin = document.getElementById('tin');
                    if (!tin || !/^\d{3}-\d{3}-\d{3}-\d{3}$/.test(tin.value.trim())) {
                        event.preventDefault();
                        alert('Enter a valid TIN in 000-000-000-000 format.');
                        tin?.focus();
                        return;
                    }
                    /* ---------------------------------------------
                       PREVENT DOUBLE SUBMISSION
                    ---------------------------------------------- */

                    if (continueButton) {

                        continueButton.disabled =
                            true;


                        continueButton.textContent =
                            'Saving...';

                    }

                }
            );

        }

    }
);

</script>



<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('sellerRegistrationForm') || document.getElementById('businessRegistrationForm');
  if (!form) return;
  const stepOne = form.id === 'sellerRegistrationForm';
  const field = id => document.getElementById(id);
  const rules = {
    first_name: v => nameCheck(v),
    last_name: v => nameCheck(v),
    middle_name: v => v.trim() ? nameCheck(v) : '',
    email: v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v.trim()) ? '' : 'Please enter a valid email address.',
    contact_no: v => /^09\d{9}$/.test(v.trim()) ? '' : 'Enter an 11-digit mobile number starting with 09.',
    zip_code: v => /^\d{4}$/.test(v.trim()) ? '' : 'Postal code must contain exactly 4 digits.',
    password: v => v.length < 8 ? 'Password must be at least 8 characters.' :
      !/[A-Z]/.test(v) ? 'Include at least one uppercase letter.' :
      !/[a-z]/.test(v) ? 'Include at least one lowercase letter.' :
      !/\d/.test(v) ? 'Include at least one number.' :
      !/[^A-Za-z0-9]/.test(v) ? 'Include at least one special character.' : '',
    password_confirmation: v => v !== (field('password')?.value || '') ? 'Passwords do not match.' : '',
    business_name: v => v.trim().length < 3 || v.trim().length > 30 ? 'Shop name must be 3–30 characters.' : '',
    tin: v => /^\d{3}-\d{3}-\d{3}-\d{3}$/.test(v.trim()) ? '' : 'Enter a 12-digit TIN in 000-000-000-000 format.',
    valid_id: () => fileCheck(field('valid_id')),
    business_permit: () => fileCheck(field('business_permit'))
  };
  function nameCheck(value) {
    const v=value.trim();
    if (v.length < 2 || v.length > 50) return 'Name must contain 2–50 characters.';
    return /^[\p{L}]+(?:[ .'-][\p{L}]+)*$/u.test(v) ? '' : 'Only letters are allowed in name fields.';
  }
  function fileCheck(el) {
    const f=el?.files?.[0];
    if (!f) return '';
    const ext=f.name.split('.').pop().toLowerCase();
    if (!['jpg','jpeg','png','pdf'].includes(ext)) return 'Accepted file types: JPG, PNG, PDF only.';
    if (f.size > 5120*1024) return 'File must not exceed 5 MB (5,120 KB).';
    return '';
  }
  function messageNode(el) {
    let node=document.getElementById('se-error-'+el.id);
    if (!node) {
      node=document.createElement('small');
      node.id='se-error-'+el.id;
      node.className='se-validation-message';
      node.setAttribute('role','alert');
      node.hidden=true;
      const wrapper=el.closest('.password-input-wrapper,.contact-wrapper') || el;
      wrapper.insertAdjacentElement('afterend',node);
    }
    return node;
  }
  function show(el,msg) {
    if (!el) return true;
    const node=messageNode(el);
    node.textContent=msg;
    node.hidden=!msg;
    el.classList.toggle('se-validation-invalid',!!msg);
    el.setAttribute('aria-invalid',msg?'true':'false');
    return !msg;
  }
  function validate(el) {
    const fn=rules[el.id];
    if (!fn) return true;
    return show(el,fn(el.value));
  }
  const ids=stepOne ?
    ['last_name','first_name','middle_name','email','contact_no','zip_code','password','password_confirmation','valid_id'] :
    ['business_name','tin'];
  ids.forEach(id => {
    const el=field(id);
    if (!el) return;
    el.addEventListener(el.type==='file'?'change':'blur',()=>validate(el));
    if (el.type!=='file') el.addEventListener('input',()=> {
      if (document.getElementById('se-error-'+id)) validate(el);
      if (id==='password' && field('password_confirmation')?.value) validate(field('password_confirmation'));
      updatePasswordRules();
    });
  });
  const password=field('password');
  let passwordBox=null;
  if (password) {
    passwordBox=document.createElement('div');
    passwordBox.className='se-password-rules';
    passwordBox.innerHTML='<strong>Password requirements</strong><div id="se-password-checks"></div>';
    const anchor=password.closest('.password-input-wrapper') || password;
    anchor.insertAdjacentElement('afterend',passwordBox);
  }
  function updatePasswordRules() {
    if (!passwordBox) return;
    const v=password.value;
    const checks=[
      ['At least 8 characters',v.length>=8],
      ['One uppercase letter',/[A-Z]/.test(v)],
      ['One lowercase letter',/[a-z]/.test(v)],
      ['One number',/\d/.test(v)],
      ['One special character',/[^A-Za-z0-9]/.test(v)]
    ];
    passwordBox.querySelector('#se-password-checks').innerHTML=checks.map(([label,ok])=>
      '<span class="'+(ok?'ok':'')+'">'+label+'</span>').join('');
  }
  updatePasswordRules();
  // Capture-phase validation runs before the existing form submit handler.
  // It does not replace the Laravel controller's validation.
  form.addEventListener('submit', event => {
    let firstInvalid=null;
    ids.forEach(id => {
      const el=field(id);
      if (!el) return;
      if (!validate(el) && !firstInvalid) firstInvalid=el;
    });
    if (firstInvalid) {
      event.preventDefault();
      event.stopImmediatePropagation();
      firstInvalid.focus();
      firstInvalid.scrollIntoView({behavior:'smooth',block:'center'});
    }
  },true);
});
</script>

</body>

</html>
