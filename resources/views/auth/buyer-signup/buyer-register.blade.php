@php

    /*
    |--------------------------------------------------------------------------
    | BUYER REGISTRATION DATA
    |--------------------------------------------------------------------------
    |
    | The buyer registration session is provided by the route:
    |
    | session('buyer_registration', [])
    |
    | This allows the information to remain while the user moves
    | between Step 1 and Step 2.
    |
    */

    $buyerData =
        is_array($buyerData ?? null)
            ? $buyerData
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
        ShopEase - Buyer Registration
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
                3px
                0
                0;

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


        /* =========================================================
           CARD
        ========================================================== */

        .registration-card {

            width:
                100%;

            max-width:
                1580px;

            min-height:
                680px;

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
                36px
                110px;

            position:
                relative;

        }


        /* =========================================================
           FORM GRID
        ========================================================== */

        .buyer-form-grid {

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
                30px;

            row-gap:
                25px;

        }


        .field-group {

            min-width:
                0;

        }


        .field-group.full-width {

            grid-column:
                1 / -1;

        }


        /* =========================================================
           LABEL
        ========================================================== */

        .field-label {

            display:
                block;

            margin-bottom:
                8px;

            font-size:
                16px;

            line-height:
                1.2;

            font-weight:
                500;

            color:
                var(--text-dark);

        }


        .required {

            color:
                #D72626;

        }


        /* =========================================================
           INPUT
        ========================================================== */

        .form-input,
        .search-select-input {

            width:
                100%;

            height:
                48px;

            padding:
                0
                13px;

            border:
                1.5px solid
                #D2D2D2;

            border-radius:
                9px;

            background:
                #FFFFFF;

            color:
                #333333;

            font-family:
                'Poppins',
                sans-serif;

            font-size:
                14px;

            font-weight:
                400;

            outline:
                none;

            transition:
                border-color 0.2s ease,
                box-shadow 0.2s ease;

        }


        .form-input::placeholder,
        .search-select-input::placeholder {

            color:
                #A7A7A7;

        }


        .form-input:focus,
        .search-select-input:focus {

            border-color:
                #9D9D9D;

            box-shadow:
                0 0 0 3px
                rgba(
                    130,
                    130,
                    130,
                    0.08
                );

        }


        /* =========================================================
           NORMAL SELECT
        ========================================================== */

        .normal-select-wrapper {

            position:
                relative;

        }


        .normal-select {

            appearance:
                none;

            -webkit-appearance:
                none;

            padding-right:
                42px;

            cursor:
                pointer;

        }


        .normal-select-arrow {

            position:
                absolute;

            right:
                15px;

            top:
                50%;

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

        }


        /* =========================================================
           ERROR
        ========================================================== */

        .field-error {

            margin-top:
                5px;

            font-size:
                11px;

            line-height:
                1.4;

            color:
                var(--error);

            display:
                none;

        }


        .field-error.show {

            display:
                block;

        }


        .required-error {

            border-color:
                var(--error) !important;

        }


        /* =========================================================
           CONTACT
        ========================================================== */

        .contact-wrapper {

            display:
                flex;

            align-items:
                center;

            height:
                48px;

            border:
                1.5px solid
                #D2D2D2;

            border-radius:
                9px;

            background:
                #FFFFFF;

            overflow:
                hidden;

            transition:
                border-color 0.2s ease,
                box-shadow 0.2s ease;

        }


        .contact-wrapper:focus-within {

            border-color:
                #9D9D9D;

            box-shadow:
                0 0 0 3px
                rgba(
                    130,
                    130,
                    130,
                    0.08
                );

        }


        .contact-icon {

            width:
                18px;

            height:
                18px;

            margin-left:
                14px;

            color:
                #454545;

            flex-shrink:
                0;

        }


        .country-code {

            padding:
                0 10px;

            font-size:
                14px;

            color:
                #333333;

            flex-shrink:
                0;

        }


        .contact-input {

            flex:
                1;

            height:
                100%;

            border:
                none;

            outline:
                none;

            padding:
                0
                12px
                0
                0;

            font-family:
                'Poppins',
                sans-serif;

            font-size:
                14px;

            color:
                #333333;

            background:
                transparent;

        }


        .contact-input::placeholder {

            color:
                #A7A7A7;

        }


        /* =========================================================
           AGE
        ========================================================== */

        .age-input {

            background:
                #F1F1F1;

            color:
                #999999;

            cursor:
                default;

        }


        /* =========================================================
           ADDRESS HEADING
        ========================================================== */

        .address-heading {

            margin-top:
                2px;

            margin-bottom:
                -8px;

        }


        .address-heading-title {

            margin:
                0;

            font-size:
                16px;

            font-weight:
                500;

            color:
                #161616;

        }


        .address-heading-subtitle {

            margin:
                3px
                0
                0;

            font-size:
                12px;

            color:
                #A0A0A0;

        }


        /* =========================================================
           ADDRESS GRID
        ========================================================== */

        .address-grid {

            grid-column:
                1 / -1;

            display:
                grid;

            grid-template-columns:
                1.15fr
                1.15fr
                1.15fr
                0.55fr;

            gap:
                20px;

        }


        .address-bottom-grid {

            grid-column:
                1 / -1;

            display:
                grid;

            grid-template-columns:
                repeat(
                    4,
                    1fr
                );

            gap:
                25px;

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


        .search-select.disabled
        .search-select-input {

            background:
                #F1F1F1;

            color:
                #999999;

            cursor:
                not-allowed;

        }


        .search-select-input {

            padding:
                0
                42px
                0
                13px;

            cursor:
                text;

        }


        .search-select.open
        .search-select-input {

            border-color:
                #9D9D9D;

            box-shadow:
                0 0 0 3px
                rgba(
                    130,
                    130,
                    130,
                    0.08
                );

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
           VALID ID
        ========================================================== */

        .valid-id-section {

            grid-column:
                1 / -1;

            margin-top:
                1px;

        }


        .valid-id-upload {

            width:
                345px;

            height:
                205px;

            border:
                1.5px solid
                #D2D2D2;

            border-radius:
                10px;

            background:
                #FFFFFF;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            cursor:
                pointer;

            overflow:
                hidden;

            transition:
                border-color 0.2s ease,
                box-shadow 0.2s ease;

        }


        .valid-id-upload:hover {

            border-color:
                var(--maroon);

            box-shadow:
                0 5px 15px
                rgba(
                    82,
                    7,
                    11,
                    0.06
                );

        }


        .hidden-file-input {

            display:
                none;

        }


        .upload-placeholder {

            width:
                100%;

            height:
                100%;

            display:
                flex;

            flex-direction:
                column;

            align-items:
                center;

            justify-content:
                center;

            text-align:
                center;

            color:
                #B3B3B3;

            padding:
                20px;

        }


        .upload-placeholder svg {

            width:
                53px;

            height:
                53px;

            margin-bottom:
                10px;

        }


        .upload-placeholder p {

            margin:
                0;

            font-size:
                13px;

            font-weight:
                500;

            color:
                #A8A8A8;

        }


        .upload-placeholder small {

            margin-top:
                4px;

            font-size:
                11px;

            color:
                #B6B6B6;

        }


        .stored-file {

            max-width:
                290px;

            word-break:
                break-word;

            color:
                var(--maroon) !important;

            font-weight:
                500;

        }


        .valid-id-file-name {

            margin-top:
                7px;

            width:
                345px;

            max-width:
                100%;

            overflow:
                hidden;

            text-overflow:
                ellipsis;

            white-space:
                nowrap;

            font-size:
                12px;

            color:
                var(--maroon);

            font-weight:
                500;

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


        .continue-button:hover {

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


        .continue-button:active {

            transform:
                scale(0.98);

            box-shadow:
                none;

        }


        .continue-button:disabled {

            opacity:
                0.65;

            cursor:
                not-allowed;

            box-shadow:
                none;

        }


        /* =========================================================
           RESPONSIVE
        ========================================================== */

        @media (max-width: 1200px) {

            .buyer-form-grid {

                grid-template-columns:
                    repeat(
                        2,
                        minmax(
                            0,
                            1fr
                        )
                    );

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


        @media (max-width: 900px) {

            .registration-content {

                padding:
                    0
                    25px
                    35px;

            }


            .registration-card {

                padding-bottom:
                    110px;

            }


            .buyer-form-grid {

                grid-template-columns:
                    1fr;

            }


            .address-grid {

                grid-template-columns:
                    1fr;

            }


            .address-bottom-grid {

                grid-template-columns:
                    1fr;

            }


            .valid-id-upload {

                width:
                    100%;

            }


            .valid-id-file-name {

                width:
                    100%;

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

                padding:
                    24px
                    20px
                    105px;

            }


            .field-label {

                font-size:
                    15px;

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
        Do NOT use url('/') here.

        buyer.register.exit clears the buyer registration
        session and then redirects to the landing page.
        -->

        <a
            href="{{ route('buyer.register.exit') }}"
            class="header-logo-wrapper"
            aria-label="Exit buyer registration and return to ShopEase landing page"
        >

            <img
                src="{{ asset('icons/login/signup-logo.png') }}"
                alt="ShopEase"
                class="signup-logo"
            >

        </a>


        <div class="header-copy">


            <h1 class="header-title">

                Buyer

                <span>
                    Registration
                </span>

            </h1>


            <p class="header-subtitle">

                Fill the details below to create your account.

            </p>


        </div>

    </header>



    <!-- =========================================================
         STEPPER
    ========================================================== -->

    <div class="stepper-wrapper">


        <!-- STEP 1 -->

        <div
            class="step active"
            id="stepOneCurrent"
        >

            <div class="step-number">
                1
            </div>


            <div class="step-label">
                Buyer Information
            </div>

        </div>



        <!-- STEP 2 -->

        <a
            href="{{ route('buyer.review.register') }}"
            class="step step-clickable"
            id="stepTwoLink"
            aria-label="Go to Review Information"
        >

            <div class="step-number">
                2
            </div>


            <div class="step-label">
                Review Information
            </div>

        </a>

    </div>



    <!-- =========================================================
         MAIN CONTENT
    ========================================================== -->

    <main class="registration-content">


        <form
            id="buyerRegistrationForm"
            method="POST"
            action="{{ route('buyer.register.submit') }}"
            enctype="multipart/form-data"
        >

            @csrf


            <div class="registration-card">


                <div class="buyer-form-grid">


                    <!-- =================================================
                         LAST NAME
                    ================================================== -->

                    <div class="field-group">

                        <label
                            for="last_name"
                            class="field-label"
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
                            value="{{ old('last_name', $buyerData['last_name'] ?? '') }}"
                            autocomplete="family-name"
                            required
                        >


                        <div
                            class="field-error"
                            id="lastNameError"
                        >
                            Please enter your last name.
                        </div>

                    </div>



                    <!-- FIRST NAME -->

                    <div class="field-group">

                        <label
                            for="first_name"
                            class="field-label"
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
                            value="{{ old('first_name', $buyerData['first_name'] ?? '') }}"
                            autocomplete="given-name"
                            required
                        >


                        <div
                            class="field-error"
                            id="firstNameError"
                        >
                            Please enter your first name.
                        </div>

                    </div>



                    <!-- MIDDLE NAME -->

                    <div class="field-group">

                        <label
                            for="middle_name"
                            class="field-label"
                        >

                            Middle Name

                            <span class="required">
                                *
                            </span>

                        </label>


                        <input
                            type="text"
                            id="middle_name"
                            name="middle_name"
                            class="form-input"
                            placeholder="Enter your middle name"
                            value="{{ old('middle_name', $buyerData['middle_name'] ?? '') }}"
                            required
                        >


                        <div
                            class="field-error"
                            id="middleNameError"
                        >
                            Please enter your middle name.
                        </div>

                    </div>



                    <!-- SEX -->

                    <div class="field-group">

                        <label
                            for="sex"
                            class="field-label"
                        >

                            Sex

                            <span class="required">
                                *
                            </span>

                        </label>


                        <div class="normal-select-wrapper">

                            <select
                                id="sex"
                                name="sex"
                                class="
                                    form-input
                                    normal-select
                                "
                                required
                            >

                                <option
                                    value=""
                                    disabled
                                    {{ old('sex', $buyerData['sex'] ?? '') === '' ? 'selected' : '' }}
                                >
                                    Select sex
                                </option>


                                <option
                                    value="Male"
                                    {{ old('sex', $buyerData['sex'] ?? '') === 'Male' ? 'selected' : '' }}
                                >
                                    Male
                                </option>


                                <option
                                    value="Female"
                                    {{ old('sex', $buyerData['sex'] ?? '') === 'Female' ? 'selected' : '' }}
                                >
                                    Female
                                </option>


                                <option
                                    value="Prefer not to say"
                                    {{ old('sex', $buyerData['sex'] ?? '') === 'Prefer not to say' ? 'selected' : '' }}
                                >
                                    Prefer not to say
                                </option>

                            </select>


                            <span
                                class="normal-select-arrow"
                            ></span>

                        </div>


                        <div
                            class="field-error"
                            id="sexError"
                        >
                            Please select your sex.
                        </div>

                    </div>



                    <!-- EMAIL -->

                    <div class="field-group">

                        <label
                            for="email"
                            class="field-label"
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
                            value="{{ old('email', $buyerData['email'] ?? '') }}"
                            autocomplete="email"
                            required
                        >


                        <div
                            class="field-error"
                            id="emailError"
                        >
                            Please enter a valid email address.
                        </div>

                    </div>



                    <!-- CONTACT -->

                    <div class="field-group">

                        <label
                            for="contact_no"
                            class="field-label"
                        >

                            Contact No.

                            <span class="required">
                                *
                            </span>

                        </label>


                        <div
                            class="contact-wrapper"
                            id="contactWrapper"
                        >

                            <svg
                                class="contact-icon"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >

                                <path
                                    d="M22 16.92v3a2 2 0 0 1-2.18 2
                                    19.79 19.79 0 0 1-8.63-3.07
                                    19.5 19.5 0 0 1-6-6
                                    19.79 19.79 0 0 1-3.07-8.67
                                    A2 2 0 0 1 4.11 2h3
                                    a2 2 0 0 1 2 1.72
                                    12.84 12.84 0 0 0 .7 2.81
                                    2 2 0 0 1-.45 2.11L8.09 9.91
                                    a16 16 0 0 0 6 6l1.27-1.27
                                    a2 2 0 0 1 2.11-.45
                                    12.84 12.84 0 0 0 2.81.7
                                    A2 2 0 0 1 22 16.92z"
                                />

                            </svg>


                            <span class="country-code">
                                +63
                            </span>


                            <input
                                type="text"
                                id="contact_no"
                                name="contact_no"
                                class="contact-input"
                                placeholder="Enter your contact number"
                                value="{{ old('contact_no', $buyerData['contact_no'] ?? '') }}"
                                inputmode="numeric"
                                maxlength="10"
                                autocomplete="tel"
                                required
                            >

                        </div>


                        <div
                            class="field-error"
                            id="contactError"
                        >
                            Please enter your contact number.
                        </div>

                    </div>



                    <!-- BIRTHDAY -->

                    <div class="field-group">

                        <label
                            for="birthday"
                            class="field-label"
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
                            value="{{ old('birthday', $buyerData['birthday'] ?? '') }}"
                            required
                        >


                        <div
                            class="field-error"
                            id="birthdayError"
                        >
                            Please select your birthday.
                        </div>

                    </div>



                    <!-- AGE -->

                    <div class="field-group">

                        <label
                            for="age"
                            class="field-label"
                        >

                            Age

                        </label>


                        <input
                            type="text"
                            id="age"
                            name="age"
                            class="form-input age-input"
                            placeholder="Auto generated"
                            value="{{ old('age', $buyerData['age'] ?? '') }}"
                            readonly
                        >

                    </div>



                    <div></div>



                    <!-- ADDRESS HEADING -->

                    <div
                        class="
                            field-group
                            full-width
                            address-heading
                        "
                    >

                        <h3 class="address-heading-title">

                            Address

                        </h3>


                        <p class="address-heading-subtitle">

                            Type to search and select your address

                        </p>

                    </div>



                    <!-- =================================================
                         ADDRESS GRID
                    ================================================== -->

                    <div class="address-grid">


                        <!-- PROVINCE -->

                        <div class="field-group">

                            <label
                                for="provinceSearch"
                                class="field-label"
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
                                    value="{{ old('province', $buyerData['province'] ?? '') }}"
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
                                    value="{{ old('province', $buyerData['province'] ?? '') }}"
                                >

                            </div>


                            <div
                                class="field-error"
                                id="provinceError"
                            >
                                Please select your province.
                            </div>

                        </div>



                        <!-- MUNICIPALITY -->

                        <div class="field-group">

                            <label
                                for="municipalitySearch"
                                class="field-label"
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
                                    value="{{ old('municipality', $buyerData['municipality'] ?? '') }}"
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
                                    value="{{ old('municipality', $buyerData['municipality'] ?? '') }}"
                                >

                            </div>


                            <div
                                class="field-error"
                                id="municipalityError"
                            >
                                Please select your municipality.
                            </div>

                        </div>



                        <!-- BARANGAY -->

                        <div class="field-group">

                            <label
                                for="barangaySearch"
                                class="field-label"
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
                                    value="{{ old('barangay', $buyerData['barangay'] ?? '') }}"
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
                                    value="{{ old('barangay', $buyerData['barangay'] ?? '') }}"
                                >

                            </div>


                            <div
                                class="field-error"
                                id="barangayError"
                            >
                                Please select your barangay.
                            </div>

                        </div>



                        <!-- ZIP -->

                        <div class="field-group">

                            <label
                                for="zip_code"
                                class="field-label"
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
                                value="{{ old('zip_code', $buyerData['zip_code'] ?? '') }}"
                                inputmode="numeric"
                                maxlength="4"
                                required
                            >


                            <div
                                class="field-error"
                                id="zipError"
                            >
                                Please enter your zip code.
                            </div>

                        </div>

                    </div>



                    <!-- =================================================
                         ADDRESS BOTTOM
                    ================================================== -->

                    <div class="address-bottom-grid">


                        <!-- STREET -->

                        <div class="field-group">

                            <label
                                for="street"
                                class="field-label"
                            >
                                Street
                            </label>


                            <input
                                type="text"
                                id="street"
                                name="street"
                                class="form-input"
                                placeholder="Enter street"
                                value="{{ old('street', $buyerData['street'] ?? '') }}"
                            >

                        </div>



                        <!-- HOUSE -->

                        <div class="field-group">

                            <label
                                for="house_no"
                                class="field-label"
                            >
                                House No.
                            </label>


                            <input
                                type="text"
                                id="house_no"
                                name="house_no"
                                class="form-input"
                                placeholder="Enter house no."
                                value="{{ old('house_no', $buyerData['house_no'] ?? '') }}"
                            >

                        </div>



                        <!-- BUILDING -->

                        <div class="field-group">

                            <label
                                for="building"
                                class="field-label"
                            >
                                Building
                            </label>


                            <input
                                type="text"
                                id="building"
                                name="building"
                                class="form-input"
                                placeholder="Enter building"
                                value="{{ old('building', $buyerData['building'] ?? '') }}"
                            >

                        </div>



                        <!-- SUBDIVISION -->

                        <div class="field-group">

                            <label
                                for="subdivision"
                                class="field-label"
                            >
                                Subdivision
                            </label>


                            <input
                                type="text"
                                id="subdivision"
                                name="subdivision"
                                class="form-input"
                                placeholder="Enter subdivision"
                                value="{{ old('subdivision', $buyerData['subdivision'] ?? '') }}"
                            >

                        </div>

                    </div>



                    <!-- =================================================
                         VALID ID
                    ================================================== -->

                    <div class="valid-id-section">


                        <label
                            for="valid_id"
                            class="field-label"
                        >

                            Valid ID

                            <span class="required">
                                *
                            </span>

                        </label>


                        <label
                            for="valid_id"
                            class="valid-id-upload"
                            id="validIdUploadBox"
                        >

                            <input
                                type="file"
                                id="valid_id"
                                name="valid_id"
                                class="hidden-file-input"
                                accept=".jpg,.jpeg,.png,.webp"
                                {{ empty($buyerData['valid_id_path'] ?? null) ? 'required' : '' }}
                            >


                            <div
                                class="upload-placeholder"
                                id="validIdPlaceholder"
                            >

                                @if(
                                    !empty(
                                        $buyerData['valid_id_path']
                                        ?? null
                                    )
                                )

                                    <svg
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
                                            $buyerData[
                                                'valid_id_original_name'
                                            ]
                                            ??
                                            basename(
                                                $buyerData[
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
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.6"
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
                                            r="1.5"
                                        />

                                        <path
                                            d="M21 15l-4.5-4.5L10 17l-3-3-4 4"
                                        />

                                    </svg>


                                    <p>

                                        Upload your valid ID

                                    </p>


                                    <small>

                                        JPG, JPEG, PNG, or WEBP

                                    </small>

                                @endif

                            </div>

                        </label>


                        <div
                            class="valid-id-file-name"
                            id="validIdFileName"
                            style="display:none;"
                        ></div>


                        <div
                            class="field-error"
                            id="validIdError"
                        >
                            Please upload your valid ID.
                        </div>

                    </div>

                </div>



                <!-- =================================================
                     PASSWORD
                ================================================== -->

                <div
                    class="
                        field-group
                        full-width
                        password-field
                    "
                >

                    <label
                        for="password"
                        class="field-label"
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
                        field-group
                        full-width
                        password-confirmation-field
                    "
                >

                    <label
                        for="password_confirmation"
                        class="field-label"
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
                     CONTINUE
                ================================================== -->

                <div class="form-actions">

                    <button
                        type="submit"
                        class="continue-button"
                        id="continueButton"
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
                'buyerRegistrationForm'
            );


        const continueButton =
            document.getElementById(
                'continueButton'
            );


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


        const contactWrapper =
            document.getElementById(
                'contactWrapper'
            );


        const zipInput =
            document.getElementById(
                'zip_code'
            );


        const validId =
            document.getElementById(
                'valid_id'
            );


        const validIdUploadBox =
            document.getElementById(
                'validIdUploadBox'
            );


        const validIdPlaceholder =
            document.getElementById(
                'validIdPlaceholder'
            );


        const validIdFileName =
            document.getElementById(
                'validIdFileName'
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
           AGE
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
                monthDifference < 0 ||
                (
                    monthDifference === 0 &&
                    today.getDate() <
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
           CONTACT
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


                contactWrapper?.classList.remove(
                    'required-error'
                );

            }
        );



        /* =========================================================
           ZIP
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

                const file =
                    this.files?.[0];


                if (
                    !file
                ) {

                    return;

                }


                const allowedTypes = [

                    'image/jpeg',

                    'image/png',

                    'image/webp'

                ];


                if (
                    !allowedTypes.includes(
                        file.type
                    )
                ) {

                    this.value =
                        '';


                    alert(
                        'Please upload a JPG, JPEG, PNG, or WEBP file.'
                    );


                    return;

                }


                validIdUploadBox?.classList.remove(
                    'required-error'
                );


                if (
                    validIdPlaceholder
                ) {

                    validIdPlaceholder.innerHTML =

                        `
                            <svg
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

                                ${file.name}

                            </p>

                            <small>
                                File selected
                            </small>
                        `;

                }


                if (
                    validIdFileName
                ) {

                    validIdFileName.textContent =
                        file.name;

                    validIdFileName.style.display =
                        'block';

                }


                document
                    .getElementById(
                        'validIdError'
                    )
                    ?.classList.remove(
                        'show'
                    );

            }
        );



        /* =========================================================
           LOCATION API
        ========================================================== */

        const locationsApiBase =
            '/api/v1/locations';



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
                        headers:
                            {
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
           SEARCH SELECT
        ========================================================== */

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
                message
            ) {

                loading =
                    true;


                optionsContainer.innerHTML =

                    `
                        <div class="search-select-loading">

                            ${message}

                        </div>
                    `;

            }


            function stopLoading() {

                loading =
                    false;

            }


            function render(
                searchText = ''
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

                            return getLocationName(
                                location
                            )
                                .toLowerCase()
                                .includes(
                                    query
                                );

                        }
                    );


                highlightedIndex =
                    -1;


                if (
                    filteredLocations.length ===
                    0
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

                        const option =
                            document.createElement(
                                'button'
                            );


                        option.type =
                            'button';


                        option.className =
                            'search-select-option';


                        option.textContent =
                            getLocationName(
                                location
                            );


                        option.dataset.index =
                            index;


                        if (
                            getLocationName(
                                location
                            )
                            ===
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
                    getLocationName(
                        location
                    );


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
                open
            );


            input.addEventListener(
                'click',
                open
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
                            highlightedIndex >= 0 &&
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

                const options =
                    optionsContainer.querySelectorAll(
                        '.search-select-option'
                    );


                options.forEach(
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


                options[
                    highlightedIndex
                ]?.scrollIntoView({
                    block:
                        'nearest'
                });

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
           ADDRESS STATES
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
                provinceLocation.isRegion === true;


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


                document
                    .getElementById(
                        'provinceError'
                    )
                    ?.classList.remove(
                        'show'
                    );


                provinceSearch.classList.remove(
                    'required-error'
                );


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


                document
                    .getElementById(
                        'municipalityError'
                    )
                    ?.classList.remove(
                        'show'
                    );


                municipalitySearch.classList.remove(
                    'required-error'
                );


                loadBarangays(
                    municipalityLocation
                );

            }
        );



        /* =========================================================
           BARANGAY SELECTED
        ========================================================== */

        barangaySearch.addEventListener(
            'location:selected',
            function () {

                document
                    .getElementById(
                        'barangayError'
                    )
                    ?.classList.remove(
                        'show'
                    );


                barangaySearch.classList.remove(
                    'required-error'
                );

            }
        );



        /* =========================================================
           RESTORE SAVED ADDRESS
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


                await loadMunicipalities(
                    matchedProvince
                );


                if (
                    !savedMunicipality
                ) {

                    return;

                }


                /*
                |--------------------------------------------------------------------------
                | Reload municipalities separately so we can
                | identify the saved municipality.
                |--------------------------------------------------------------------------
                */

                const provinceCode =
                    getLocationCode(
                        matchedProvince
                    );


                const isRegion =
                    matchedProvince.isRegion === true;


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


                await loadBarangays(
                    matchedMunicipality
                );


                if (
                    !savedBarangay
                ) {

                    return;

                }


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

                [

                    provinceWrapper,

                    municipalityWrapper,

                    barangayWrapper

                ].forEach(
                    function (
                        wrapper
                    ) {

                        if (
                            wrapper &&
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
           FORM VALIDATION
        ========================================================== */

        function showError(
            element,
            errorElement
        ) {

            element?.classList.add(
                'required-error'
            );


            errorElement?.classList.add(
                'show'
            );

        }


        function clearError(
            element,
            errorElement
        ) {

            element?.classList.remove(
                'required-error'
            );


            errorElement?.classList.remove(
                'show'
            );

        }


        function validateForm() {

            let isValid =
                true;


            /* LAST NAME */

            const lastName =
                document.getElementById(
                    'last_name'
                );


            const lastNameError =
                document.getElementById(
                    'lastNameError'
                );


            if (
                !lastName.value.trim()
            ) {

                showError(
                    lastName,
                    lastNameError
                );


                isValid =
                    false;

            } else {

                clearError(
                    lastName,
                    lastNameError
                );

            }


            /* FIRST NAME */

            const firstName =
                document.getElementById(
                    'first_name'
                );


            const firstNameError =
                document.getElementById(
                    'firstNameError'
                );


            if (
                !firstName.value.trim()
            ) {

                showError(
                    firstName,
                    firstNameError
                );


                isValid =
                    false;

            } else {

                clearError(
                    firstName,
                    firstNameError
                );

            }


            /* MIDDLE NAME */

            const middleName =
                document.getElementById(
                    'middle_name'
                );


            const middleNameError =
                document.getElementById(
                    'middleNameError'
                );


            if (
                !middleName.value.trim()
            ) {

                showError(
                    middleName,
                    middleNameError
                );


                isValid =
                    false;

            } else {

                clearError(
                    middleName,
                    middleNameError
                );

            }


            /* SEX */

            const sex =
                document.getElementById(
                    'sex'
                );


            const sexError =
                document.getElementById(
                    'sexError'
                );


            if (
                !sex.value
            ) {

                showError(
                    sex,
                    sexError
                );


                isValid =
                    false;

            } else {

                clearError(
                    sex,
                    sexError
                );

            }


            /* EMAIL */

            const email =
                document.getElementById(
                    'email'
                );


            const emailError =
                document.getElementById(
                    'emailError'
                );


            if (
                !email.value.trim() ||
                !email.checkValidity()
            ) {

                showError(
                    email,
                    emailError
                );


                isValid =
                    false;

            } else {

                clearError(
                    email,
                    emailError
                );

            }


            /* CONTACT */

            const contactError =
                document.getElementById(
                    'contactError'
                );


            if (
                !contactInput.value.trim()
            ) {

                showError(
                    contactWrapper,
                    contactError
                );


                isValid =
                    false;

            } else {

                clearError(
                    contactWrapper,
                    contactError
                );

            }


            /* BIRTHDAY */

            const birthdayError =
                document.getElementById(
                    'birthdayError'
                );


            if (
                !birthday.value
            ) {

                showError(
                    birthday,
                    birthdayError
                );


                isValid =
                    false;

            } else {

                clearError(
                    birthday,
                    birthdayError
                );

            }


            /* PROVINCE */

            const provinceError =
                document.getElementById(
                    'provinceError'
                );


            if (
                !provinceHidden.value.trim()
            ) {

                provinceSearch.classList.add(
                    'required-error'
                );


                provinceError.classList.add(
                    'show'
                );


                isValid =
                    false;

            } else {

                provinceSearch.classList.remove(
                    'required-error'
                );


                provinceError.classList.remove(
                    'show'
                );

            }


            /* MUNICIPALITY */

            const municipalityError =
                document.getElementById(
                    'municipalityError'
                );


            if (
                !municipalityHidden.value.trim()
            ) {

                municipalitySearch.classList.add(
                    'required-error'
                );


                municipalityError.classList.add(
                    'show'
                );


                isValid =
                    false;

            } else {

                municipalitySearch.classList.remove(
                    'required-error'
                );


                municipalityError.classList.remove(
                    'show'
                );

            }


            /* BARANGAY */

            const barangayError =
                document.getElementById(
                    'barangayError'
                );


            if (
                !barangayHidden.value.trim()
            ) {

                barangaySearch.classList.add(
                    'required-error'
                );


                barangayError.classList.add(
                    'show'
                );


                isValid =
                    false;

            } else {

                barangaySearch.classList.remove(
                    'required-error'
                );


                barangayError.classList.remove(
                    'show'
                );

            }


            /* ZIP */

            const zipError =
                document.getElementById(
                    'zipError'
                );


            if (
                !zipInput.value.trim()
            ) {

                showError(
                    zipInput,
                    zipError
                );


                isValid =
                    false;

            } else {

                clearError(
                    zipInput,
                    zipError
                );

            }


            /* VALID ID */

            const validIdError =
                document.getElementById(
                    'validIdError'
                );


            const hasExistingId =
                {{ !empty($buyerData['valid_id_path'] ?? null) ? 'true' : 'false' }};


            if (
                !validId.files.length &&
                !hasExistingId
            ) {

                validIdUploadBox.classList.add(
                    'required-error'
                );


                validIdError.classList.add(
                    'show'
                );


                isValid =
                    false;

            } else {

                validIdUploadBox.classList.remove(
                    'required-error'
                );


                validIdError.classList.remove(
                    'show'
                );

            }


            /* PASSWORD */

            const password =
                passwordInput.value.trim();


            const confirmation =
                passwordConfirmationInput.value.trim();


            if (
                !password
            ) {

                passwordInput.classList.add(
                    'required-error'
                );


                isValid =
                    false;

            } else {

                passwordInput.classList.remove(
                    'required-error'
                );

            }


            if (
                !confirmation
            ) {

                passwordConfirmationInput.classList.add(
                    'password-mismatch'
                );


                passwordConfirmationError.textContent =
                    'Please confirm your password.';


                passwordConfirmationError.classList.add(
                    'show'
                );


                isValid =
                    false;

            } else if (
                password !==
                confirmation
            ) {

                passwordConfirmationInput.classList.add(
                    'password-mismatch'
                );


                passwordConfirmationError.textContent =
                    'Passwords do not match.';


                passwordConfirmationError.classList.add(
                    'show'
                );


                isValid =
                    false;

            } else {

                passwordConfirmationInput.classList.remove(
                    'password-mismatch'
                );


                passwordConfirmationError.classList.remove(
                    'show'
                );

            }


            return isValid;

        }



        /* =========================================================
           CLEAR ERRORS WHILE TYPING
        ========================================================== */

        document
            .querySelectorAll(
                '.form-input, .contact-input'
            )
            .forEach(
                function (
                    input
                ) {

                    input.addEventListener(
                        'input',
                        function () {

                            this.classList.remove(
                                'required-error'
                            );


                            this.closest(
                                '.field-group'
                            )
                                ?.querySelector(
                                    '.field-error'
                                )
                                ?.classList.remove(
                                    'show'
                                );

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

                    const valid =
                        validateForm();


                    if (
                        !valid
                    ) {

                        event.preventDefault();


                        const firstError =
                            document.querySelector(
                                '.required-error'
                            );


                        if (
                            firstError
                        ) {

                            firstError.scrollIntoView({
                                behavior:
                                    'smooth',

                                block:
                                    'center'
                            });


                            setTimeout(
                                function () {

                                    firstError.focus?.();

                                },
                                250
                            );

                        }


                        return;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Continue
                    |--------------------------------------------------------------------------
                    |
                    | The form is allowed to submit normally.
                    | The POST route saves the buyer information
                    | into buyer_registration session.
                    |
                    | When the review page returns to this page,
                    | $buyerData will repopulate the fields.
                    |
                    */

                    if (
                        continueButton
                    ) {

                        continueButton.disabled =
                            true;


                        continueButton.textContent =
                            'Saving...';

                    }

                }
            );

        }

    });

</script>


</body>

</html>