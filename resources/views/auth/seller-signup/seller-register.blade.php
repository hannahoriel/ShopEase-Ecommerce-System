@php

    /*
    |--------------------------------------------------------------------------
    | SELLER REGISTRATION DATA
    |--------------------------------------------------------------------------
    |
    | This data comes from:
    |
    | session('seller_registration', [])
    |
    | It remains available while the user moves between
    | Seller Step 1, Step 2, and Step 3.
    |
    */

    $sellerData =
        is_array($sellerData ?? null)
            ? $sellerData
            : [];

@endphp


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        ShopEase - Seller Registration
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
            --peach-soft: #FFF2EE;

            --background: #FBF5F2;
            --white: #FFFFFF;

            --text-dark: #161616;
            --text-muted: #999393;

            --border: #D8D8D8;

            --success: #2E7D32;
            --error: #D32F2F;

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
                translateX(
                    2px
                );

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
                scale(
                    1.06
                );

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
                #FFFFFF;

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
                40px
                40px;

        }


        /* =========================================================
           FORM GRID
        ========================================================== */

        .form-grid {

            display:
                grid;

            grid-template-columns:
                repeat(
                    3,
                    minmax(
                        0,
                        1fr
                    )
                );

            column-gap:
                32px;

            row-gap:
                25px;

            width:
                100%;

        }


        .form-group {

            min-width:
                0;

        }


        /* =========================================================
           LABEL
        ========================================================== */

        .form-label {

            display:
                block;

            margin-bottom:
                7px;

            font-size:
                15px;

            font-weight:
                500;

            color:
                var(--text-dark);

        }


        .required {

            color:
                #E01D1D;

        }


        /* =========================================================
           INPUT
        ========================================================== */

        .form-input {

            width:
                100%;

            height:
                49px;

            border:
                1.5px solid
                var(--border);

            border-radius:
                9px;

            padding:
                0
                14px;

            background:
                #FFFFFF;

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
                border-color 0.2s ease,
                box-shadow 0.2s ease;

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
           PASSWORD
        ========================================================== */

        .password-field {

            margin-top:
                25px;

        }


        .password-confirmation-field {

            margin-top:
                0;

        }


        .field-help {

            display:
                block;

            margin-top:
                8px;

            margin-bottom:
                10px;

            font-size:
                12px;

            line-height:
                1.5;

            color:
                #777777;

        }


        .password-match-error {

            margin:
                5px
                0
                0;

            font-size:
                11px;

            line-height:
                1.4;

            color:
                var(--error);

            display:
                none;

        }


        .password-match-error.show {

            display:
                block;

        }


        .form-input.password-mismatch {

            border-color:
                var(--error) !important;

        }


        .form-input.password-mismatch:focus {

            border-color:
                var(--error) !important;

            box-shadow:
                0 0 0 3px
                rgba(
                    211,
                    47,
                    47,
                    0.10
                );

        }


        /* =========================================================
           SEARCHABLE ADDRESS
        ========================================================== */

        .search-select {

            position:
                relative;

            width:
                100%;

        }


        .search-select-input {

            width:
                100%;

            height:
                49px;

            border:
                1.5px solid
                var(--border);

            border-radius:
                9px;

            padding:
                0
                42px
                0
                14px;

            background:
                #FFFFFF;

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
                border-color 0.2s ease,
                box-shadow 0.2s ease;

        }


        .search-select-input::placeholder {

            color:
                #A0A0A0;

        }


        .search-select.open
        .search-select-input {

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


        .search-select.disabled
        .search-select-input {

            background:
                #F5F5F5;

            color:
                #A5A5A5;

            cursor:
                not-allowed;

        }


        .search-select-arrow {

            position:
                absolute;

            top:
                50%;

            right:
                15px;

            width:
                10px;

            height:
                10px;

            transform:
                translateY(-65%)
                rotate(45deg);

            border-right:
                2px solid
                #111111;

            border-bottom:
                2px solid
                #111111;

            pointer-events:
                none;

            transition:
                transform 0.2s ease;

        }


        .search-select.open
        .search-select-arrow {

            transform:
                translateY(-30%)
                rotate(225deg);

        }


        .search-select-options {

            position:
                absolute;

            left:
                0;

            right:
                0;

            top:
                calc(
                    100% + 6px
                );

            max-height:
                250px;

            overflow-y:
                auto;

            background:
                #FFFFFF;

            border:
                1px solid
                #DED8D4;

            border-radius:
                10px;

            box-shadow:
                0 10px 30px
                rgba(
                    32,
                    12,
                    12,
                    0.12
                );

            padding:
                6px;

            z-index:
                5000;

            display:
                none;

        }


        .search-select.open
        .search-select-options {

            display:
                block;

        }


        .search-select-option {

            width:
                100%;

            border:
                none;

            background:
                transparent;

            border-radius:
                7px;

            padding:
                10px 11px;

            text-align:
                left;

            font-family:
                'Poppins',
                sans-serif;

            font-size:
                13px;

            color:
                #333333;

            cursor:
                pointer;

            transition:
                background 0.15s ease,
                color 0.15s ease;

        }


        .search-select-option:hover,
        .search-select-option.highlighted {

            background:
                var(--peach-soft);

            color:
                var(--maroon-dark);

        }


        .search-select-option.selected {

            background:
                #F8E8E4;

            color:
                var(--maroon-dark);

            font-weight:
                500;

        }


        .search-select-no-results,
        .search-select-loading {

            padding:
                11px;

            text-align:
                center;

            font-size:
                12px;

            color:
                #999999;

        }


        /* =========================================================
           CONTACT
        ========================================================== */

        .contact-wrapper {

            display:
                flex;

            gap:
                0;

            height:
                49px;

            border:
                1.5px solid
                var(--border);

            border-radius:
                9px;

            overflow:
                hidden;

            background:
                #FFFFFF;

        }


        .contact-wrapper:focus-within {

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


        .contact-prefix {

            width:
                80px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            color:
                #555555;

            font-size:
                14px;

            border-right:
                1px solid
                #E2E2E2;

            flex-shrink:
                0;

        }


        .contact-input {

            flex:
                1;

            border:
                none;

            outline:
                none;

            padding:
                0
                14px;

            font-family:
                'Poppins',
                sans-serif;

            font-size:
                14px;

            color:
                var(--text-dark);

        }


        .contact-input::placeholder {

            color:
                #A6A6A6;

        }


        /* =========================================================
           AGE
        ========================================================== */

        .readonly {

            background:
                #F1F1F1;

            color:
                #9A9A9A;

            cursor:
                not-allowed;

        }


        /* =========================================================
           ADDRESS
        ========================================================== */

        .address-section {

            margin-top:
                4px;

            grid-column:
                1 / -1;

        }


        .address-title {

            margin-bottom:
                1px;

            font-size:
                17px;

            font-weight:
                500;

            color:
                var(--text-dark);

        }


        .address-subtitle {

            margin:
                0
                0
                16px;

            font-size:
                14px;

            color:
                #A1A1A1;

        }


        .address-grid {

            display:
                grid;

            grid-template-columns:
                minmax(0, 1.15fr)
                minmax(0, 1.15fr)
                minmax(0, 1.15fr)
                minmax(120px, 0.52fr);

            gap:
                18px;

            width:
                100%;

        }


        .address-bottom-grid {

            display:
                grid;

            grid-template-columns:
                repeat(
                    4,
                    minmax(
                        0,
                        1fr
                    )
                );

            gap:
                24px;

            margin-top:
                24px;

            width:
                100%;

        }


        /* =========================================================
           VALID ID
        ========================================================== */

        .upload-section {

            margin-top:
                28px;

            grid-column:
                1 / -1;

        }


        .upload-label {

            display:
                block;

            margin-bottom:
                8px;

            font-size:
                16px;

            font-weight:
                500;

        }


        .upload-box {

            width:
                340px;

            height:
                145px;

            border:
                1.5px solid
                #D6D6D6;

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
                #FFFFFF;

            transition:
                border-color 0.2s ease,
                background 0.2s ease,
                box-shadow 0.2s ease;

        }


        .upload-box:hover {

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


        .upload-box input[type="file"] {

            display:
                none;

        }


        .upload-placeholder {

            text-align:
                center;

            color:
                #A8A8A8;

            padding:
                10px;

        }


        .upload-icon {

            width:
                40px;

            height:
                40px;

            margin:
                0
                auto
                8px;

        }


        .upload-placeholder p {

            margin:
                0;

            font-size:
                13px;

            word-break:
                break-word;

        }


        .upload-placeholder small {

            display:
                block;

            margin-top:
                4px;

            font-size:
                11px;

            color:
                #B1B1B1;

        }


        .stored-file {

            color:
                var(--maroon);

            font-weight:
                500;

            max-width:
                290px;

            word-break:
                break-word;

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
           BUTTON
        ========================================================== */

        .form-actions {

            margin-top:
                30px;

            display:
                flex;

            justify-content:
                flex-end;

        }


        .next-button {

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
                #FFFFFF;

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
                background 0.2s ease,
                transform 0.1s ease,
                box-shadow 0.2s ease;

        }


        .next-button:hover {

            background:
                #661515;

            box-shadow:
                0 5px
                14px
                rgba(
                    123,
                    27,
                    27,
                    0.15
                );

        }


        .next-button:active {

            transform:
                scale(0.98);

            box-shadow:
                none;

        }


        .next-button:disabled {

            opacity:
                0.65;

            cursor:
                not-allowed;

            box-shadow:
                none;

        }


        /* =========================================================
           TABLET
        ========================================================== */

        @media (max-width: 1100px) {

            .header-title {

                font-size:
                    30px;

            }


            .header-subtitle {

                font-size:
                    18px;

            }


            .registration-card {

                padding:
                    25px
                    30px
                    35px;

            }


            .form-grid {

                grid-template-columns:
                    repeat(
                        2,
                        minmax(
                            0,
                            1fr
                        )
                    );

                column-gap:
                    22px;

                row-gap:
                    22px;

            }


            .address-grid {

                grid-template-columns:
                    repeat(
                        2,
                        minmax(
                            0,
                            1fr
                        )
                    );

            }


            .address-bottom-grid {

                grid-template-columns:
                    repeat(
                        2,
                        minmax(
                            0,
                            1fr
                        )
                    );

            }

        }


        /* =========================================================
           MOBILE
        ========================================================== */

        @media (max-width: 760px) {

            .registration-header {

                gap:
                    20px;

                padding-top:
                    25px;

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
                    28px;

            }


            .step-label {

                font-size:
                    13px;

            }


            .registration-content {

                padding:
                    0
                    20px
                    35px;

            }


            .registration-card {

                padding:
                    24px
                    20px
                    30px;

            }


            .form-grid {

                grid-template-columns:
                    1fr;

                gap:
                    22px;

            }


            .address-grid,
            .address-bottom-grid {

                grid-template-columns:
                    1fr;

                gap:
                    20px;

            }


            .upload-box {

                width:
                    100%;

            }


            .next-button {

                width:
                    150px;

            }


            .search-select-options {

                max-height:
                    220px;

            }

        }

    </style>

</head>


<body>


<div class="registration-page">


    <!-- =========================================================
         HEADER
    ========================================================== -->

    <header class="registration-header">


        <!--
        IMPORTANT:

        Going to the landing page must use the named landing route.

        The landing route itself clears the temporary seller and buyer
        registration sessions before displaying the landing page.
        -->

        <a
    href="{{ route('seller.register.exit') }}"
    class="header-logo-wrapper"
    id="sellerRegistrationExit"
    aria-label="Exit seller registration and return to ShopEase landing page"
>

            <img
                src="{{ asset('icons/login/signup-logo.png') }}"
                alt="ShopEase"
                class="signup-logo"
            >

        </a>


        <div class="header-copy">


            <h1 class="header-title">

                Seller

                <span>
                    Registration
                </span>

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
            class="step step-clickable active"
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
            class="step step-clickable"
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
            id="sellerRegistrationForm"
            method="POST"
            action="{{ route('seller.register.submit') }}"
            enctype="multipart/form-data"
        >

            @csrf


            <div class="registration-card">


                <div class="form-grid">


                    <!-- =================================================
                         LAST NAME
                    ================================================== -->

                    <div class="form-group">

                        <label
                            class="form-label"
                            for="last_name"
                        >

                            Last Name

                            <span class="required">
                                *
                            </span>

                        </label>


                        <input
                            type="text"
                            id="last_name"
                            name="last_name"
                            class="form-input"
                            placeholder="Enter your last name"
                            value="{{ old('last_name', $sellerData['last_name'] ?? '') }}"
                            autocomplete="family-name"
                            required
                        >


                        @error('last_name')

                            <p class="error-message">

                                {{ $message }}

                            </p>

                        @enderror

                    </div>



                    <!-- =================================================
                         FIRST NAME
                    ================================================== -->

                    <div class="form-group">

                        <label
                            class="form-label"
                            for="first_name"
                        >

                            First Name

                            <span class="required">
                                *
                            </span>

                        </label>


                        <input
                            type="text"
                            id="first_name"
                            name="first_name"
                            class="form-input"
                            placeholder="Enter your first name"
                            value="{{ old('first_name', $sellerData['first_name'] ?? '') }}"
                            autocomplete="given-name"
                            required
                        >


                        @error('first_name')

                            <p class="error-message">

                                {{ $message }}

                            </p>

                        @enderror

                    </div>



                    <!-- =================================================
                         MIDDLE NAME
                    ================================================== -->

                    <div class="form-group">

                        <label
                            class="form-label"
                            for="middle_name"
                        >

                            Middle Name

                        </label>


                        <input
                            type="text"
                            id="middle_name"
                            name="middle_name"
                            class="form-input"
                            placeholder="Enter your middle name"
                            value="{{ old('middle_name', $sellerData['middle_name'] ?? '') }}"
                        >

                    </div>



                    <!-- =================================================
                         SEX
                    ================================================== -->

                    <div class="form-group">

                        <label
                            class="form-label"
                            for="sex"
                        >

                            Sex

                            <span class="required">
                                *
                            </span>

                        </label>


                        <div class="select-wrapper">


                            <select
                                id="sex"
                                name="sex"
                                class="form-input"
                                required
                            >

                                <option
                                    value=""
                                    disabled
                                    {{ old('sex', $sellerData['sex'] ?? '') === '' ? 'selected' : '' }}
                                >

                                    Select sex

                                </option>


                                <option
                                    value="Male"
                                    {{ old('sex', $sellerData['sex'] ?? '') === 'Male' ? 'selected' : '' }}
                                >

                                    Male

                                </option>


                                <option
                                    value="Female"
                                    {{ old('sex', $sellerData['sex'] ?? '') === 'Female' ? 'selected' : '' }}
                                >

                                    Female

                                </option>

                            </select>

                        </div>


                        @error('sex')

                            <p class="error-message">

                                {{ $message }}

                            </p>

                        @enderror

                    </div>



                    <!-- =================================================
                         EMAIL
                    ================================================== -->

                    <div class="form-group">

                        <label
                            class="form-label"
                            for="email"
                        >

                            Email

                            <span class="required">
                                *
                            </span>

                        </label>


                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-input"
                            placeholder="Enter your email"
                            value="{{ old('email', $sellerData['email'] ?? '') }}"
                            autocomplete="email"
                            required
                        >


                        @error('email')

                            <p class="error-message">

                                {{ $message }}

                            </p>

                        @enderror

                    </div>



                    <!-- =================================================
                         CONTACT
                    ================================================== -->

                    <div class="form-group">

                        <label
                            class="form-label"
                            for="contact_no"
                        >

                            Contact No.

                            <span class="required">
                                *
                            </span>

                        </label>


                        <div class="contact-wrapper">


                            <div class="contact-prefix">
                                +63
                            </div>


                            <input
                                type="text"
                                id="contact_no"
                                name="contact_no"
                                class="contact-input"
                                placeholder="Enter your contact number"
                                maxlength="10"
                                inputmode="numeric"
                                autocomplete="tel"
                                value="{{ old('contact_no', $sellerData['contact_no'] ?? '') }}"
                                required
                            >

                        </div>


                        @error('contact_no')

                            <p class="error-message">

                                {{ $message }}

                            </p>

                        @enderror

                    </div>



                    <!-- =================================================
                         BIRTHDAY
                    ================================================== -->

                    <div class="form-group">

                        <label
                            class="form-label"
                            for="birthday"
                        >

                            Birthday

                            <span class="required">
                                *
                            </span>

                        </label>


                        <input
                            type="date"
                            id="birthday"
                            name="birthday"
                            class="form-input"
                            value="{{ old('birthday', $sellerData['birthday'] ?? '') }}"
                            required
                        >


                        @error('birthday')

                            <p class="error-message">

                                {{ $message }}

                            </p>

                        @enderror

                    </div>



                    <!-- =================================================
                         AGE
                    ================================================== -->

                    <div class="form-group">

                        <label
                            class="form-label"
                            for="age"
                        >

                            Age

                        </label>


                        <input
                            type="text"
                            id="age"
                            name="age"
                            class="form-input readonly"
                            placeholder="Auto generated"
                            value="{{ old('age', $sellerData['age'] ?? '') }}"
                            readonly
                        >

                    </div>



                    <div></div>



                    <!-- =================================================
                         ADDRESS
                    ================================================== -->

                    <div class="address-section">


                        <h3 class="address-title">

                            Address

                        </h3>


                        <p class="address-subtitle">

                            Type to search and select your address

                        </p>



                        <div class="address-grid">


                            <!-- PROVINCE -->

                            <div class="form-group">


                                <label
                                    class="form-label"
                                    for="provinceSearch"
                                >

                                    Province

                                    <span class="required">
                                        *
                                    </span>

                                </label>


                                <div
                                    class="search-select"
                                    id="provinceSearchWrapper"
                                >

                                    <input
                                        type="text"
                                        id="provinceSearch"
                                        class="search-select-input"
                                        placeholder="Type to search province"
                                        autocomplete="off"
                                        role="combobox"
                                        aria-expanded="false"
                                        aria-controls="provinceOptions"
                                        value="{{ old('province', $sellerData['province'] ?? '') }}"
                                    >


                                    <span
                                        class="search-select-arrow"
                                    ></span>


                                    <div
                                        class="search-select-options"
                                        id="provinceOptions"
                                        role="listbox"
                                    ></div>


                                    <input
                                        type="hidden"
                                        id="province"
                                        name="province"
                                        value="{{ old('province', $sellerData['province'] ?? '') }}"
                                        required
                                    >

                                </div>

                            </div>



                            <!-- MUNICIPALITY -->

                            <div class="form-group">


                                <label
                                    class="form-label"
                                    for="municipalitySearch"
                                >

                                    Municipality

                                    <span class="required">
                                        *
                                    </span>

                                </label>


                                <div
                                    class="
                                        search-select
                                        disabled
                                    "
                                    id="municipalitySearchWrapper"
                                >

                                    <input
                                        type="text"
                                        id="municipalitySearch"
                                        class="search-select-input"
                                        placeholder="Select province first"
                                        autocomplete="off"
                                        role="combobox"
                                        aria-expanded="false"
                                        aria-controls="municipalityOptions"
                                        value="{{ old('municipality', $sellerData['municipality'] ?? '') }}"
                                        disabled
                                    >


                                    <span
                                        class="search-select-arrow"
                                    ></span>


                                    <div
                                        class="search-select-options"
                                        id="municipalityOptions"
                                        role="listbox"
                                    ></div>


                                    <input
                                        type="hidden"
                                        id="municipality"
                                        name="municipality"
                                        value="{{ old('municipality', $sellerData['municipality'] ?? '') }}"
                                        required
                                    >

                                </div>

                            </div>



                            <!-- BARANGAY -->

                            <div class="form-group">


                                <label
                                    class="form-label"
                                    for="barangaySearch"
                                >

                                    Barangay

                                    <span class="required">
                                        *
                                    </span>

                                </label>


                                <div
                                    class="
                                        search-select
                                        disabled
                                    "
                                    id="barangaySearchWrapper"
                                >

                                    <input
                                        type="text"
                                        id="barangaySearch"
                                        class="search-select-input"
                                        placeholder="Select municipality first"
                                        autocomplete="off"
                                        role="combobox"
                                        aria-expanded="false"
                                        aria-controls="barangayOptions"
                                        value="{{ old('barangay', $sellerData['barangay'] ?? '') }}"
                                        disabled
                                    >


                                    <span
                                        class="search-select-arrow"
                                    ></span>


                                    <div
                                        class="search-select-options"
                                        id="barangayOptions"
                                        role="listbox"
                                    ></div>


                                    <input
                                        type="hidden"
                                        id="barangay"
                                        name="barangay"
                                        value="{{ old('barangay', $sellerData['barangay'] ?? '') }}"
                                        required
                                    >

                                </div>

                            </div>



                            <!-- ZIP -->

                            <div class="form-group">


                                <label
                                    class="form-label"
                                    for="zip_code"
                                >

                                    Zip Code

                                    <span class="required">
                                        *
                                    </span>

                                </label>


                                <input
                                    type="text"
                                    id="zip_code"
                                    name="zip_code"
                                    class="form-input"
                                    placeholder="Enter zip code"
                                    maxlength="4"
                                    inputmode="numeric"
                                    value="{{ old('zip_code', $sellerData['zip_code'] ?? '') }}"
                                    required
                                >

                            </div>

                        </div>



                        <!-- =================================================
                             STREET / HOUSE / BUILDING / SUBDIVISION
                        ================================================== -->

                        <div class="address-bottom-grid">


                            <!-- STREET -->

                            <div class="form-group">

                                <label
                                    class="form-label"
                                    for="street"
                                >

                                    Street

                                </label>


                                <input
                                    type="text"
                                    id="street"
                                    name="street"
                                    class="form-input"
                                    placeholder="Enter street"
                                    value="{{ old('street', $sellerData['street'] ?? '') }}"
                                >

                            </div>



                            <!-- HOUSE -->

                            <div class="form-group">

                                <label
                                    class="form-label"
                                    for="house_no"
                                >

                                    House No.

                                </label>


                                <input
                                    type="text"
                                    id="house_no"
                                    name="house_no"
                                    class="form-input"
                                    placeholder="Enter house no."
                                    value="{{ old('house_no', $sellerData['house_no'] ?? '') }}"
                                >

                            </div>



                            <!-- BUILDING -->

                            <div class="form-group">

                                <label
                                    class="form-label"
                                    for="building"
                                >

                                    Building

                                </label>


                                <input
                                    type="text"
                                    id="building"
                                    name="building"
                                    class="form-input"
                                    placeholder="Enter building"
                                    value="{{ old('building', $sellerData['building'] ?? '') }}"
                                >

                            </div>



                            <!-- SUBDIVISION -->

                            <div class="form-group">

                                <label
                                    class="form-label"
                                    for="subdivision"
                                >

                                    Subdivision

                                </label>


                                <input
                                    type="text"
                                    id="subdivision"
                                    name="subdivision"
                                    class="form-input"
                                    placeholder="Enter subdivision"
                                    value="{{ old('subdivision', $sellerData['subdivision'] ?? '') }}"
                                >

                            </div>

                        </div>

                    </div>



                    <!-- =================================================
                         VALID ID
                    ================================================== -->

                    <div class="upload-section">


                        <label class="upload-label">

                            Valid ID

                            <span class="required">
                                *
                            </span>

                        </label>


                        <label
                            for="valid_id"
                            class="upload-box"
                        >

                            <input
                                type="file"
                                id="valid_id"
                                name="valid_id"
                                accept=".jpg,.jpeg,.png"
                                {{ empty($sellerData['valid_id_path'] ?? null) ? 'required' : '' }}
                            >


                            <div class="upload-placeholder">


                                @if(
                                    !empty(
                                        $sellerData['valid_id_path']
                                        ?? null
                                    )
                                )


                                    <svg
                                        class="upload-icon"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="#7B1B1B"
                                        stroke-width="1.7"
                                    >

                                        <path
                                            d="M20 6L9 17l-5-5"
                                        />

                                    </svg>


                                    <p class="stored-file">

                                        {{
                                            $sellerData[
                                                'valid_id_original_name'
                                            ]
                                            ??
                                            basename(
                                                $sellerData[
                                                    'valid_id_path'
                                                ]
                                            )
                                        }}

                                    </p>


                                    <small>

                                        Choose another file to replace it

                                    </small>


                                @else


                                    <svg
                                        class="upload-icon"
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


                                    <p>
                                        Upload a valid ID
                                    </p>


                                    <small>
                                        JPG, JPEG, PNG
                                    </small>


                                @endif

                            </div>

                        </label>


                        @error('valid_id')

                            <p class="error-message">

                                {{ $message }}

                            </p>

                        @enderror

                    </div>

                </div>



                <!-- =================================================
                     PASSWORD
                ================================================== -->

                <div
                    class="
                        form-group
                        password-field
                    "
                >

                    <label
                        class="form-label"
                        for="password"
                    >

                        Password

                        <span class="required">
                            *
                        </span>

                    </label>


                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input"
                        placeholder="Create a password"
                        minlength="8"
                        autocomplete="new-password"
                        required
                    >


                    <small class="field-help">

                        At least 8 characters with uppercase and lowercase
                        letters, a number, and a special character.

                    </small>

                </div>



                <!-- =================================================
                     CONFIRM PASSWORD
                ================================================== -->

                <div
                    class="
                        form-group
                        password-confirmation-field
                    "
                >

                    <label
                        class="form-label"
                        for="password_confirmation"
                    >

                        Confirm Password

                        <span class="required">
                            *
                        </span>

                    </label>


                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        class="form-input"
                        placeholder="Confirm your password"
                        minlength="8"
                        autocomplete="new-password"
                        required
                    >


                    <p
                        class="password-match-error"
                        id="passwordConfirmationError"
                    >
                        Passwords do not match.
                    </p>

                </div>



                <!-- =================================================
                     BUTTON
                ================================================== -->

                <div class="form-actions">


                    <button
                        type="submit"
                        class="next-button"
                        id="nextButton"
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

        const birthday =
            document.getElementById(
                'birthday'
            );


        const age =
            document.getElementById(
                'age'
            );


        const contactInput =
            document.getElementById(
                'contact_no'
            );


        const zipInput =
            document.getElementById(
                'zip_code'
            );


        const validId =
            document.getElementById(
                'valid_id'
            );


        const form =
            document.getElementById(
                'sellerRegistrationForm'
            );


        const nextButton =
            document.getElementById(
                'nextButton'
            );


        /* =========================================================
           PASSWORD MATCH
        ========================================================== */

        const passwordInput =
            document.getElementById(
                'password'
            );


        const passwordConfirmationInput =
            document.getElementById(
                'password_confirmation'
            );


        const passwordConfirmationError =
            document.getElementById(
                'passwordConfirmationError'
            );


        function checkPasswordMatch() {

            if (
                !passwordInput ||
                !passwordConfirmationInput ||
                !passwordConfirmationError
            ) {

                return true;

            }


            if (
                !passwordConfirmationInput.value
            ) {

                passwordConfirmationInput.classList.remove(
                    'password-mismatch'
                );


                passwordConfirmationError.classList.remove(
                    'show'
                );


                return false;

            }


            if (
                passwordInput.value !==
                passwordConfirmationInput.value
            ) {

                passwordConfirmationInput.classList.add(
                    'password-mismatch'
                );


                passwordConfirmationError.textContent =
                    'Passwords do not match.';


                passwordConfirmationError.classList.add(
                    'show'
                );


                return false;

            }


            passwordConfirmationInput.classList.remove(
                'password-mismatch'
            );


            passwordConfirmationError.classList.remove(
                'show'
            );


            return true;

        }


        passwordInput?.addEventListener(
            'input',
            checkPasswordMatch
        );


        passwordConfirmationInput?.addEventListener(
            'input',
            checkPasswordMatch
        );



        /* =========================================================
           AGE CALCULATION
        ========================================================== */

        function calculateAge() {

            if (
                !birthday ||
                !age
            ) {

                return;

            }


            if (
                !birthday.value
            ) {

                age.value =
                    '';

                return;

            }


            const birthDate =
                new Date(
                    birthday.value +
                    'T00:00:00'
                );


            const today =
                new Date();


            let calculatedAge =
                today.getFullYear()
                -
                birthDate.getFullYear();


            const monthDifference =
                today.getMonth()
                -
                birthDate.getMonth();


            if (
                monthDifference < 0
                ||
                (
                    monthDifference === 0
                    &&
                    today.getDate()
                    <
                    birthDate.getDate()
                )
            ) {

                calculatedAge--;

            }


            age.value =
                calculatedAge >= 0
                    ? calculatedAge
                    : '';

        }


        birthday?.addEventListener(
            'change',
            calculateAge
        );


        calculateAge();



        /* =========================================================
           CONTACT NUMBER
        ========================================================== */

        contactInput?.addEventListener(
            'input',
            function () {

                this.value =
                    this.value
                        .replace(
                            /\D/g,
                            ''
                        )
                        .slice(
                            0,
                            10
                        );

            }
        );



        /* =========================================================
           ZIP CODE
        ========================================================== */

        zipInput?.addEventListener(
            'input',
            function () {

                this.value =
                    this.value
                        .replace(
                            /\D/g,
                            ''
                        )
                        .slice(
                            0,
                            4
                        );

            }
        );



        /* =========================================================
           VALID ID
        ========================================================== */

        validId?.addEventListener(
            'change',
            function () {

                if (
                    !this.files ||
                    !this.files.length
                ) {

                    return;

                }


                const file =
                    this.files[0];


                const uploadBox =
                    document.querySelector(
                        '.upload-box'
                    );


                const placeholder =
                    uploadBox?.querySelector(
                        '.upload-placeholder'
                    );


                if (
                    !placeholder
                ) {

                    return;

                }


                const allowedTypes = [

                    'image/jpeg',

                    'image/png'

                ];


                if (
                    !allowedTypes.includes(
                        file.type
                    )
                ) {

                    this.value =
                        '';


                    alert(
                        'Please upload a JPG, JPEG, or PNG file.'
                    );


                    return;

                }


                placeholder.innerHTML =

                    `
                        <svg
                            class="upload-icon"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="#7B1B1B"
                            stroke-width="1.7"
                        >

                            <path
                                d="M20 6L9 17l-5-5"
                            />

                        </svg>


                        <p
                            class="stored-file"
                        >

                            ${file.name}

                        </p>


                        <small>

                            File selected

                        </small>

                    `;

            }
        );



        /* =========================================================
           SEARCHABLE ADDRESS COMPONENT
        ========================================================== */

        const locationsApiBase =
            '/api/v1/locations';



        function createSearchSelect(
            wrapper,
            input,
            optionsContainer,
            hiddenInput
        ) {

            let locations =
                [];


            let filteredLocations =
                [];


            let highlightedIndex =
                -1;


            let loading =
                false;



            function open() {

                if (
                    input.disabled
                ) {

                    return;

                }


                wrapper.classList.add(
                    'open'
                );


                input.setAttribute(
                    'aria-expanded',
                    'true'
                );


                render(
                    input.value
                );

            }



            function close() {

                wrapper.classList.remove(
                    'open'
                );


                input.setAttribute(
                    'aria-expanded',
                    'false'
                );


                highlightedIndex =
                    -1;

            }



            function setLoading(
                message =
                    'Loading...'
            ) {

                loading =
                    true;


                optionsContainer.innerHTML =

                    `
                        <div
                            class="search-select-loading"
                        >
                            ${message}
                        </div>
                    `;

            }



            function stopLoading() {

                loading =
                    false;

            }



            function render(
                searchText =
                    ''
            ) {

                if (
                    loading
                ) {

                    return;

                }


                const query =
                    searchText
                        .trim()
                        .toLowerCase();


                filteredLocations =
                    locations.filter(
                        function (
                            location
                        ) {

                            const name =
                                location.name
                                ||
                                location.prov_name
                                ||
                                location.city_name
                                ||
                                location.mun_name
                                ||
                                location.brgy_name
                                ||
                                '';


                            return name
                                .toLowerCase()
                                .includes(
                                    query
                                );

                        }
                    );


                highlightedIndex =
                    -1;


                if (
                    filteredLocations.length === 0
                ) {

                    optionsContainer.innerHTML =

                        `
                            <div
                                class="search-select-no-results"
                            >
                                No matching result found.
                            </div>
                        `;

                    return;

                }


                optionsContainer.innerHTML =
                    '';


                filteredLocations.forEach(
                    function (
                        location,
                        index
                    ) {

                        const name =
                            location.name
                            ||
                            location.prov_name
                            ||
                            location.city_name
                            ||
                            location.mun_name
                            ||
                            location.brgy_name
                            ||
                            '';


                        const option =
                            document.createElement(
                                'button'
                            );


                        option.type =
                            'button';


                        option.className =
                            'search-select-option';


                        option.textContent =
                            name;


                        option.dataset.index =
                            index;


                        if (
                            name ===
                            hiddenInput.value
                        ) {

                            option.classList.add(
                                'selected'
                            );

                        }


                        option.addEventListener(
                            'mousedown',
                            function (
                                event
                            ) {

                                event.preventDefault();


                                selectLocation(
                                    location
                                );

                            }
                        );


                        optionsContainer.appendChild(
                            option
                        );

                    }
                );

            }



            function selectLocation(
                location
            ) {

                const name =
                    location.name
                    ||
                    location.prov_name
                    ||
                    location.city_name
                    ||
                    location.mun_name
                    ||
                    location.brgy_name
                    ||
                    '';


                input.value =
                    name;


                hiddenInput.value =
                    name;


                wrapper.classList.remove(
                    'open'
                );


                input.setAttribute(
                    'aria-expanded',
                    'false'
                );


                input.dispatchEvent(
                    new CustomEvent(
                        'location:selected',
                        {
                            detail:
                                location
                        }
                    )
                );

            }



            function setLocations(
                newLocations
            ) {

                locations =
                    Array.isArray(
                        newLocations
                    )
                        ? newLocations
                        : [];


                filteredLocations =
                    locations.slice();


                stopLoading();


                render(
                    input.value
                );

            }



            function clear() {

                locations =
                    [];


                filteredLocations =
                    [];


                highlightedIndex =
                    -1;


                input.value =
                    '';


                hiddenInput.value =
                    '';


                optionsContainer.innerHTML =
                    '';


                close();

            }



            input.addEventListener(
                'focus',
                function () {

                    open();

                }
            );


            input.addEventListener(
                'click',
                function () {

                    open();

                }
            );


            input.addEventListener(
                'input',
                function () {

                    hiddenInput.value =
                        '';


                    render(
                        this.value
                    );


                    open();

                }
            );



            input.addEventListener(
                'keydown',
                function (
                    event
                ) {

                    if (
                        input.disabled
                    ) {

                        return;

                    }


                    if (
                        event.key ===
                        'ArrowDown'
                    ) {

                        event.preventDefault();


                        if (
                            !wrapper.classList.contains(
                                'open'
                            )
                        ) {

                            open();

                        }


                        highlightedIndex =
                            Math.min(
                                highlightedIndex + 1,
                                filteredLocations.length - 1
                            );


                        updateHighlight();

                    }


                    if (
                        event.key ===
                        'ArrowUp'
                    ) {

                        event.preventDefault();


                        highlightedIndex =
                            Math.max(
                                highlightedIndex - 1,
                                0
                            );


                        updateHighlight();

                    }


                    if (
                        event.key ===
                        'Enter'
                    ) {

                        if (
                            highlightedIndex >= 0
                            &&
                            filteredLocations[
                                highlightedIndex
                            ]
                        ) {

                            event.preventDefault();


                            selectLocation(
                                filteredLocations[
                                    highlightedIndex
                                ]
                            );

                        }

                    }


                    if (
                        event.key ===
                        'Escape'
                    ) {

                        close();

                    }

                }
            );



            function updateHighlight() {

                const optionElements =
                    optionsContainer.querySelectorAll(
                        '.search-select-option'
                    );


                optionElements.forEach(
                    function (
                        option,
                        index
                    ) {

                        option.classList.toggle(
                            'highlighted',
                            index ===
                            highlightedIndex
                        );

                    }
                );


                const activeOption =
                    optionElements[
                        highlightedIndex
                    ];


                if (
                    activeOption
                ) {

                    activeOption.scrollIntoView({
                        block:
                            'nearest'
                    });

                }

            }



            input.addEventListener(
                'blur',
                function () {

                    setTimeout(
                        function () {

                            if (
                                hiddenInput.value !==
                                input.value
                            ) {

                                input.value =
                                    hiddenInput.value;

                            }


                            close();

                        },
                        180
                    );

                }
            );



            return {

                open,

                close,

                setLoading,

                stopLoading,

                setLocations,

                clear

            };

        }



        /* =========================================================
           ADDRESS ELEMENTS
        ========================================================== */

        const provinceWrapper =
            document.getElementById(
                'provinceSearchWrapper'
            );


        const provinceSearch =
            document.getElementById(
                'provinceSearch'
            );


        const provinceOptions =
            document.getElementById(
                'provinceOptions'
            );


        const provinceHidden =
            document.getElementById(
                'province'
            );


        const municipalityWrapper =
            document.getElementById(
                'municipalitySearchWrapper'
            );


        const municipalitySearch =
            document.getElementById(
                'municipalitySearch'
            );


        const municipalityOptions =
            document.getElementById(
                'municipalityOptions'
            );


        const municipalityHidden =
            document.getElementById(
                'municipality'
            );


        const barangayWrapper =
            document.getElementById(
                'barangaySearchWrapper'
            );


        const barangaySearch =
            document.getElementById(
                'barangaySearch'
            );


        const barangayOptions =
            document.getElementById(
                'barangayOptions'
            );


        const barangayHidden =
            document.getElementById(
                'barangay'
            );



        /* =========================================================
           CONTROLS
        ========================================================== */

        const provinceControl =
            createSearchSelect(
                provinceWrapper,
                provinceSearch,
                provinceOptions,
                provinceHidden
            );


        const municipalityControl =
            createSearchSelect(
                municipalityWrapper,
                municipalitySearch,
                municipalityOptions,
                municipalityHidden
            );


        const barangayControl =
            createSearchSelect(
                barangayWrapper,
                barangaySearch,
                barangayOptions,
                barangayHidden
            );



        /* =========================================================
           LOCATION HELPERS
        ========================================================== */

        function getLocationName(
            location
        ) {

            return (
                location.name
                ||
                location.prov_name
                ||
                location.city_name
                ||
                location.mun_name
                ||
                location.brgy_name
                ||
                ''
            );

        }



        function getLocationCode(
            location
        ) {

            return (
                location.code
                ||
                location.psgc_code
                ||
                location.prov_code
                ||
                location.mun_code
                ||
                location.city_code
                ||
                location.brgy_code
                ||
                ''
            );

        }



        async function loadLocations(
            endpoint
        ) {

            const response =
                await fetch(
                    `${locationsApiBase}/${endpoint}`,
                    {
                        headers: {
                            Accept:
                                'application/json'
                        }
                    }
                );


            if (
                !response.ok
            ) {

                throw new Error(
                    'Unable to load location options.'
                );

            }


            return response.json();

        }



        /* =========================================================
           DISABLE / ENABLE
        ========================================================== */

        function disableMunicipality() {

            municipalityControl.clear();


            municipalitySearch.disabled =
                true;


            municipalitySearch.placeholder =
                'Select province first';


            municipalityWrapper.classList.add(
                'disabled'
            );

        }



        function disableBarangay() {

            barangayControl.clear();


            barangaySearch.disabled =
                true;


            barangaySearch.placeholder =
                'Select municipality first';


            barangayWrapper.classList.add(
                'disabled'
            );

        }



        function enableMunicipality() {

            municipalitySearch.disabled =
                false;


            municipalitySearch.placeholder =
                'Type to search municipality';


            municipalityWrapper.classList.remove(
                'disabled'
            );

        }



        function enableBarangay() {

            barangaySearch.disabled =
                false;


            barangaySearch.placeholder =
                'Type to search barangay';


            barangayWrapper.classList.remove(
                'disabled'
            );

        }



        /* =========================================================
           LOAD MUNICIPALITIES
        ========================================================== */

        async function loadMunicipalities(
            provinceLocation
        ) {

            const provinceCode =
                getLocationCode(
                    provinceLocation
                );


            const isRegion =
                provinceLocation.isRegion ===
                true;


            const query =
                isRegion
                    ? '?is_region=1'
                    : '';


            municipalityControl.clear();


            municipalityControl.setLoading(
                'Loading municipalities...'
            );


            enableMunicipality();


            try {

                const municipalities =
                    await loadLocations(
                        `provinces/${encodeURIComponent(
                            provinceCode
                        )}/cities${query}`
                    );


                municipalityControl.setLocations(
                    municipalities
                );


                disableBarangay();

            } catch (
                error
            ) {

                console.error(
                    'Municipality loading error:',
                    error
                );


                municipalityOptions.innerHTML =

                    `
                        <div
                            class="search-select-no-results"
                        >
                            Unable to load municipalities.
                        </div>
                    `;


                disableBarangay();

            }

        }



        /* =========================================================
           LOAD BARANGAYS
        ========================================================== */

        async function loadBarangays(
            municipalityLocation
        ) {

            const municipalityCode =
                getLocationCode(
                    municipalityLocation
                );


            barangayControl.clear();


            barangayControl.setLoading(
                'Loading barangays...'
            );


            enableBarangay();


            try {

                const barangays =
                    await loadLocations(
                        `cities/${encodeURIComponent(
                            municipalityCode
                        )}/barangays`
                    );


                barangayControl.setLocations(
                    barangays
                );

            } catch (
                error
            ) {

                console.error(
                    'Barangay loading error:',
                    error
                );


                barangayOptions.innerHTML =

                    `
                        <div
                            class="search-select-no-results"
                        >
                            Unable to load barangays.
                        </div>
                    `;

            }

        }



        /* =========================================================
           PROVINCE SELECTED
        ========================================================== */

        provinceSearch.addEventListener(
            'location:selected',
            function (
                event
            ) {

                const provinceLocation =
                    event.detail;


                municipalityControl.clear();


                barangayControl.clear();


                enableMunicipality();


                disableBarangay();


                loadMunicipalities(
                    provinceLocation
                );

            }
        );



        /* =========================================================
           MUNICIPALITY SELECTED
        ========================================================== */

        municipalitySearch.addEventListener(
            'location:selected',
            function (
                event
            ) {

                const municipalityLocation =
                    event.detail;


                barangayControl.clear();


                enableBarangay();


                loadBarangays(
                    municipalityLocation
                );

            }
        );



        /* =========================================================
           INITIALIZE / RESTORE SAVED ADDRESS
        ========================================================== */

        async function initializeLocations() {

            const savedProvince =
                provinceHidden.value.trim();


            const savedMunicipality =
                municipalityHidden.value.trim();


            const savedBarangay =
                barangayHidden.value.trim();


            disableMunicipality();


            disableBarangay();


            try {

                provinceControl.setLoading(
                    'Loading provinces...'
                );


                const provinces =
                    await loadLocations(
                        'provinces'
                    );


                provinceControl.setLocations(
                    provinces
                );


                if (
                    !savedProvince
                ) {

                    return;

                }


                /* -----------------------------------------------------
                   RESTORE PROVINCE
                ----------------------------------------------------- */

                const matchedProvince =
                    provinces.find(
                        function (
                            province
                        ) {

                            return (
                                getLocationName(
                                    province
                                )
                                ===
                                savedProvince
                            );

                        }
                    );


                if (
                    !matchedProvince
                ) {

                    return;

                }


                provinceSearch.value =
                    savedProvince;


                provinceHidden.value =
                    savedProvince;


                /* -----------------------------------------------------
                   RESTORE MUNICIPALITY
                ----------------------------------------------------- */

                const provinceCode =
                    getLocationCode(
                        matchedProvince
                    );


                const isRegion =
                    matchedProvince.isRegion ===
                    true;


                const query =
                    isRegion
                        ? '?is_region=1'
                        : '';


                const municipalities =
                    await loadLocations(
                        `provinces/${encodeURIComponent(
                            provinceCode
                        )}/cities${query}`
                    );


                municipalityControl.setLocations(
                    municipalities
                );


                enableMunicipality();


                if (
                    !savedMunicipality
                ) {

                    return;

                }


                const matchedMunicipality =
                    municipalities.find(
                        function (
                            municipality
                        ) {

                            return (
                                getLocationName(
                                    municipality
                                )
                                ===
                                savedMunicipality
                            );

                        }
                    );


                if (
                    !matchedMunicipality
                ) {

                    return;

                }


                municipalitySearch.value =
                    savedMunicipality;


                municipalityHidden.value =
                    savedMunicipality;


                /* -----------------------------------------------------
                   RESTORE BARANGAY
                ----------------------------------------------------- */

                const municipalityCode =
                    getLocationCode(
                        matchedMunicipality
                    );


                const barangays =
                    await loadLocations(
                        `cities/${encodeURIComponent(
                            municipalityCode
                        )}/barangays`
                    );


                barangayControl.setLocations(
                    barangays
                );


                enableBarangay();


                if (
                    !savedBarangay
                ) {

                    return;

                }


                const matchedBarangay =
                    barangays.find(
                        function (
                            barangay
                        ) {

                            return (
                                getLocationName(
                                    barangay
                                )
                                ===
                                savedBarangay
                            );

                        }
                    );


                if (
                    matchedBarangay
                ) {

                    barangaySearch.value =
                        savedBarangay;


                    barangayHidden.value =
                        savedBarangay;

                }

            } catch (
                error
            ) {

                console.error(
                    'Location initialization error:',
                    error
                );

            }

        }


        initializeLocations();



        /* =========================================================
           OUTSIDE CLICK
        ========================================================== */

        document.addEventListener(
            'click',
            function (
                event
            ) {

                const wrappers = [

                    provinceWrapper,

                    municipalityWrapper,

                    barangayWrapper

                ];


                wrappers.forEach(
                    function (
                        wrapper
                    ) {

                        if (
                            wrapper
                            &&
                            !wrapper.contains(
                                event.target
                            )
                        ) {

                            wrapper.classList.remove(
                                'open'
                            );


                            wrapper
                                .querySelector(
                                    '.search-select-input'
                                )
                                ?.setAttribute(
                                    'aria-expanded',
                                    'false'
                                );

                        }

                    }
                );

            }
        );



        /* =========================================================
           FORM SUBMIT
        ========================================================== */

        if (
            form
        ) {

            form.addEventListener(
                'submit',
                function (
                    event
                ) {

                    /* -------------------------------------------------
                       PASSWORD
                    ------------------------------------------------- */

                    const password =
                        passwordInput
                            ? passwordInput.value.trim()
                            : '';


                    const confirmation =
                        passwordConfirmationInput
                            ? passwordConfirmationInput.value.trim()
                            : '';


                    if (
                        !password
                    ) {

                        event.preventDefault();


                        passwordInput?.focus();


                        return;

                    }


                    if (
                        !confirmation
                    ) {

                        event.preventDefault();


                        passwordConfirmationInput?.classList.add(
                            'password-mismatch'
                        );


                        if (
                            passwordConfirmationError
                        ) {

                            passwordConfirmationError.textContent =
                                'Please confirm your password.';


                            passwordConfirmationError.classList.add(
                                'show'
                            );

                        }


                        passwordConfirmationInput?.focus();


                        return;

                    }


                    if (
                        password !==
                        confirmation
                    ) {

                        event.preventDefault();


                        passwordConfirmationInput?.classList.add(
                            'password-mismatch'
                        );


                        if (
                            passwordConfirmationError
                        ) {

                            passwordConfirmationError.textContent =
                                'Passwords do not match.';


                            passwordConfirmationError.classList.add(
                                'show'
                            );

                        }


                        passwordConfirmationInput?.focus();


                        return;

                    }



                    /* -------------------------------------------------
                       ADDRESS VALIDATION
                    ------------------------------------------------- */

                    if (
                        !provinceHidden.value.trim()
                    ) {

                        event.preventDefault();


                        provinceSearch.focus();


                        provinceControl.open();


                        return;

                    }


                    if (
                        !municipalityHidden.value.trim()
                    ) {

                        event.preventDefault();


                        municipalitySearch.focus();


                        municipalityControl.open();


                        return;

                    }


                    if (
                        !barangayHidden.value.trim()
                    ) {

                        event.preventDefault();


                        barangaySearch.focus();


                        barangayControl.open();


                        return;

                    }



                    /* -------------------------------------------------
                       PREVENT DOUBLE SUBMISSION
                    ------------------------------------------------- */

                    if (
                        nextButton
                    ) {

                        nextButton.disabled =
                            true;


                        nextButton.textContent =
                            'Saving...';

                    }

                }
            );

        }

    }

);

</script>


</body>

</html>