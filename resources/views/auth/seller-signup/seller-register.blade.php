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
        }


        /* =========================================================
           RESET
        ========================================================== */

        * {

            box-sizing:
                border-box;

        }


        /* =========================================================
           BODY
        ========================================================== */

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
           LOGO WRAPPER
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
                opacity
                0.2s
                ease,

                transform
                0.2s
                ease;

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


        /* =========================================================
           SIGNUP LOGO
        ========================================================== */

        .signup-logo {

            width:
                68px;

            height:
                auto;

            object-fit:
                contain;

        }


        /* =========================================================
           HEADER COPY
        ========================================================== */

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
                transform
                0.2s
                ease,

                background
                0.2s
                ease,

                border-color
                0.2s
                ease,

                color
                0.2s
                ease;

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
           MAIN CONTENT
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


        /* =========================================================
           FORM GROUP
        ========================================================== */

        .form-group {

            min-width:
                0;

        }


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
           INPUT / SELECT
        ========================================================== */

        .form-input,
        .form-select {

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
                0 14px;

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
                0.2s
                ease,

                box-shadow
                0.2s
                ease;

        }


        .form-input::placeholder {

            color:
                #A6A6A6;

        }


        .form-input:focus,
        .form-select:focus {

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
           SELECT
        ========================================================== */

        .select-wrapper {

            position:
                relative;

        }


        .form-select {

            appearance:
                none;

            padding-right:
                45px;

            cursor:
                pointer;

        }


        .select-arrow {

            position:
                absolute;

            right:
                15px;

            top:
                50%;

            transform:
                translateY(-50%)
                rotate(45deg);

            width:
                10px;

            height:
                10px;

            border-right:
                2px solid
                #111;

            border-bottom:
                2px solid
                #111;

            pointer-events:
                none;

        }


        /* =========================================================
           CONTACT NUMBER
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
                white;

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
                #555;

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
                0 14px;

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
                0 0 16px;

            font-size:
                14px;

            color:
                #A1A1A1;

        }


        .address-grid {

            display:
                grid;

            grid-template-columns:
                minmax(
                    0,
                    1.15fr
                )
                minmax(
                    0,
                    1.15fr
                )
                minmax(
                    0,
                    1.15fr
                )
                minmax(
                    120px,
                    0.52fr
                );

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
                white;

            transition:
                border-color
                0.2s
                ease,

                background
                0.2s
                ease,

                box-shadow
                0.2s
                ease;

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
                0 auto 8px;

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
           NEXT BUTTON
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
                0.2s
                ease,

                transform
                0.1s
                ease,

                box-shadow
                0.2s
                ease;

        }


        .next-button:hover {

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


        .next-button:active {

            transform:
                scale(
                    0.98
                );

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

        }

    </style>

</head>


<body>


<div class="registration-page">


    <!-- =========================================================
         HEADER
    ========================================================== -->

    <header class="registration-header">


        <!-- =====================================================
             CLICKABLE LOGO
             RETURNS DIRECTLY TO LANDING PAGE
        ====================================================== -->

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
                                class="form-select"
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


                            <span class="select-arrow"></span>


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
                            required
                        >


                        @error('email')

                            <p class="error-message">

                                {{ $message }}

                            </p>

                        @enderror


                    </div>



                    <!-- =================================================
                         CONTACT NUMBER
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



                    <!-- EMPTY COLUMN -->

                    <div></div>



                    <!-- =================================================
                         ADDRESS
                    ================================================== -->

                    <div class="address-section">


                        <h3 class="address-title">

                            Address

                        </h3>


                        <p class="address-subtitle">

                            Please select your address

                        </p>



                        <!-- =================================================
                             PROVINCE / MUNICIPALITY / BARANGAY / ZIP
                        ================================================== -->

                        <div class="address-grid">


                            <!-- PROVINCE -->

                            <div class="form-group">


                                <label
                                    class="form-label"
                                    for="province"
                                >

                                    Province

                                    <span class="required">
                                        *
                                    </span>

                                </label>


                                <div class="select-wrapper">


                                    <select
                                        id="province"
                                        name="province"
                                        class="form-select"
                                        required
                                    >

                                        <option
                                            value=""
                                            disabled
                                            {{ old('province', $sellerData['province'] ?? '') === '' ? 'selected' : '' }}
                                        >

                                            Select or search province

                                        </option>


                                        <option
                                            value="Laguna"
                                            {{ old('province', $sellerData['province'] ?? '') === 'Laguna' ? 'selected' : '' }}
                                        >

                                            Laguna

                                        </option>


                                        <option
                                            value="Cavite"
                                            {{ old('province', $sellerData['province'] ?? '') === 'Cavite' ? 'selected' : '' }}
                                        >

                                            Cavite

                                        </option>


                                        <option
                                            value="Batangas"
                                            {{ old('province', $sellerData['province'] ?? '') === 'Batangas' ? 'selected' : '' }}
                                        >

                                            Batangas

                                        </option>


                                        <option
                                            value="Rizal"
                                            {{ old('province', $sellerData['province'] ?? '') === 'Rizal' ? 'selected' : '' }}
                                        >

                                            Rizal

                                        </option>

                                    </select>


                                    <span class="select-arrow"></span>


                                </div>


                            </div>



                            <!-- MUNICIPALITY -->

                            <div class="form-group">


                                <label
                                    class="form-label"
                                    for="municipality"
                                >

                                    Municipality

                                    <span class="required">
                                        *
                                    </span>

                                </label>


                                <div class="select-wrapper">


                                    <select
                                        id="municipality"
                                        name="municipality"
                                        class="form-select"
                                        required
                                    >

                                        <option
                                            value=""
                                            disabled
                                            {{ old('municipality', $sellerData['municipality'] ?? '') === '' ? 'selected' : '' }}
                                        >

                                            Select or search municipality

                                        </option>


                                        <option
                                            value="Calamba"
                                            {{ old('municipality', $sellerData['municipality'] ?? '') === 'Calamba' ? 'selected' : '' }}
                                        >

                                            Calamba

                                        </option>


                                        <option
                                            value="Santa Rosa"
                                            {{ old('municipality', $sellerData['municipality'] ?? '') === 'Santa Rosa' ? 'selected' : '' }}
                                        >

                                            Santa Rosa

                                        </option>


                                        <option
                                            value="Biñan"
                                            {{ old('municipality', $sellerData['municipality'] ?? '') === 'Biñan' ? 'selected' : '' }}
                                        >

                                            Biñan

                                        </option>

                                    </select>


                                    <span class="select-arrow"></span>


                                </div>


                            </div>



                            <!-- BARANGAY -->

                            <div class="form-group">


                                <label
                                    class="form-label"
                                    for="barangay"
                                >

                                    Barangay

                                    <span class="required">
                                        *
                                    </span>

                                </label>


                                <div class="select-wrapper">


                                    <select
                                        id="barangay"
                                        name="barangay"
                                        class="form-select"
                                        required
                                    >

                                        <option
                                            value=""
                                            disabled
                                            {{ old('barangay', $sellerData['barangay'] ?? '') === '' ? 'selected' : '' }}
                                        >

                                            Select or search barangay

                                        </option>


                                        <option
                                            value="Masico"
                                            {{ old('barangay', $sellerData['barangay'] ?? '') === 'Masico' ? 'selected' : '' }}
                                        >

                                            Masico

                                        </option>


                                        <option
                                            value="Canlubang"
                                            {{ old('barangay', $sellerData['barangay'] ?? '') === 'Canlubang' ? 'selected' : '' }}
                                        >

                                            Canlubang

                                        </option>


                                        <option
                                            value="Real"
                                            {{ old('barangay', $sellerData['barangay'] ?? '') === 'Real' ? 'selected' : '' }}
                                        >

                                            Real

                                        </option>

                                    </select>


                                    <span class="select-arrow"></span>


                                </div>


                            </div>



                            <!-- ZIP CODE -->

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



                            <!-- HOUSE NUMBER -->

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


                                @if(!empty($sellerData['valid_id_path']))


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

                                        {{ $sellerData['valid_id_original_name'] ?? basename($sellerData['valid_id_path']) }}

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



                <!-- =====================================================
                     NEXT BUTTON
                ====================================================== -->

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


            if (!birthday.value) {

                age.value =
                    '';

                return;

            }


            const birthDate =
                new Date(
                    birthday.value
                );


            const today =
                new Date();


            let calculatedAge =
                today.getFullYear() -
                birthDate.getFullYear();


            const monthDifference =
                today.getMonth() -
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



        if (birthday) {

            birthday.addEventListener(
                'change',
                calculateAge
            );

        }


        calculateAge();



        /* =========================================================
           CONTACT NUMBER
        ========================================================== */

        if (contactInput) {

            contactInput.addEventListener(
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

        }



        /* =========================================================
           ZIP CODE
        ========================================================== */

        if (zipInput) {

            zipInput.addEventListener(
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

        }



        /* =========================================================
           VALID ID PREVIEW
        ========================================================== */

        if (validId) {

            validId.addEventListener(
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


                    if (!placeholder) {

                        return;

                    }


                    placeholder.innerHTML = `

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

        }



        /* =========================================================
           STEP 2 NAVIGATION
        ========================================================== */

        const stepTwoLink =
            document.getElementById(
                'stepTwoLink'
            );


        if (stepTwoLink) {

            stepTwoLink.addEventListener(
                'click',
                function () {

                    /*
                     * Step 1 information is already
                     * saved to MySQL when Continue
                     * was clicked.
                     *
                     * No browser storage is used.
                     */

                }
            );

        }



        /* =========================================================
           STEP 3 NAVIGATION
        ========================================================== */

        const stepThreeLink =
            document.getElementById(
                'stepThreeLink'
            );


        if (stepThreeLink) {

            stepThreeLink.addEventListener(
                'click',
                function () {

                    /*
                     * Step 3 reads the seller
                     * registration record from MySQL.
                     */

                }
            );

        }



        /* =========================================================
           FORM SUBMIT
        ========================================================== */

        if (form) {

            form.addEventListener(
                'submit',
                function () {

                    /*
                     * Laravel route:
                     *
                     * seller.register.submit
                     *
                     * saves/updates Step 1
                     * in seller_registrations.
                     */

                }
            );

        }

    }
);

</script>


</body>

</html>