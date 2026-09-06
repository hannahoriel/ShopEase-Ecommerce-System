<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>ShopEase - Buyer Registration</title>


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
            box-sizing: border-box;
        }


        body {
            margin: 0;
            padding: 0;

            min-height: 100vh;

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
            min-height: 100vh;

            position: relative;

            overflow-x: hidden;
        }


        /* =========================================================
           HEADER
        ========================================================== */

        .registration-header {
            display: flex;

            align-items: center;

            gap: 26px;

            padding-top: 28px;
            padding-left: 0;
        }


        .header-logo-wrapper {
            width: 128px;
            height: 105px;

            background:
                var(--maroon-dark);

            border-top-right-radius: 60px;
            border-bottom-right-radius: 60px;

            display: flex;

            align-items: center;
            justify-content: center;

            flex-shrink: 0;

            text-decoration: none;

            cursor: pointer;

            transition:
                opacity 0.2s ease,
                transform 0.2s ease;
        }


        .header-logo-wrapper:hover {
            opacity: 0.92;

            transform:
                translateX(2px);
        }


        .header-logo-wrapper:focus-visible {
            outline:
                2px solid
                var(--maroon);

            outline-offset: 4px;
        }


        .signup-logo {
            width: 68px;

            height: auto;

            object-fit: contain;
        }


        .header-copy {
            padding-top: 0;
        }


        .header-title {
            margin: 0;

            font-size: 31px;

            line-height: 1.15;

            font-weight: 700;

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

            font-size: 19px;

            line-height: 1.3;

            font-weight: 400;

            color:
                #969696;
        }


        /* =========================================================
           STEPPER
        ========================================================== */

        .stepper-wrapper {
            width: 560px;

            margin:
                18px auto
                18px;

            display: flex;

            align-items: flex-start;

            justify-content: center;
        }


        .step {
            flex: 1;

            display: flex;

            flex-direction: column;

            align-items: center;

            position: relative;

            text-decoration: none;

            color: inherit;

            cursor: pointer;
        }


        .step:not(:last-child)::after {
            content: "";

            position: absolute;

            top: 16px;

            left: 50%;

            width: 100%;

            height: 1.5px;

            background:
                #9E9E9E;

            z-index: 0;
        }


        .step-number {
            width: 34px;
            height: 34px;

            border-radius: 50%;

            display: flex;

            align-items: center;
            justify-content: center;

            font-size: 14px;

            font-weight: 500;

            border:
                1.5px solid
                #999;

            color:
                #888;

            background:
                var(--background);

            position: relative;

            z-index: 1;

            transition:
                transform 0.2s ease,
                background 0.2s ease,
                border-color 0.2s ease,
                color 0.2s ease;
        }


        .step:hover .step-number {
            transform:
                scale(1.06);

            border-color:
                var(--maroon);

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

            font-weight: 600;
        }


        .step-label {
            margin-top: 6px;

            font-size: 14px;

            font-weight: 400;

            color:
                #969696;

            white-space: nowrap;
        }


        .step:hover .step-label {
            color:
                var(--maroon-dark);
        }


        .step.active .step-label {
            color:
                var(--maroon-dark);

            font-weight: 500;
        }


        /* =========================================================
           MAIN CONTENT
        ========================================================== */

        .registration-content {
            width: 100%;

            padding:
                0
                55px
                40px;
        }


        /* =========================================================
           CARD
        ========================================================== */

        .registration-card {
            width: 100%;

            max-width: 1580px;

            min-height: 680px;

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
                105px;

            position:
                relative;
        }


        /* =========================================================
           FORM GRID
        ========================================================== */

        .buyer-form-grid {
            display: grid;

            grid-template-columns:
                repeat(3, minmax(0, 1fr));

            column-gap: 30px;

            row-gap: 25px;
        }


        .field-group {
            min-width: 0;
        }


        .field-group.full-width {
            grid-column:
                1 / -1;
        }


        /* =========================================================
           LABEL
        ========================================================== */

        .field-label {
            display: block;

            margin-bottom: 8px;

            font-size: 16px;

            line-height: 1.2;

            font-weight: 500;

            color:
                var(--text-dark);
        }


        .required {
            color:
                #D72626;
        }


        /* =========================================================
           INPUT WRAPPER
        ========================================================== */

        .input-wrap {
            position: relative;
        }


        /* =========================================================
           INPUTS
        ========================================================== */

        .form-input,
        .form-select {
            width: 100%;

            height: 48px;

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

            outline: none;

            transition:
                border-color 0.2s ease,
                box-shadow 0.2s ease;
        }


        .form-input::placeholder {
            color:
                #A7A7A7;
        }


        .form-input:focus,
        .form-select:focus {
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
           REQUIRED ERROR STATE
        ========================================================== */

        .form-input.required-error,
        .form-select.required-error,
        .contact-wrapper.required-error,
        .valid-id-upload.required-error {
            border-color:
                var(--error) !important;
        }


        .form-input.required-error:focus,
        .form-select.required-error:focus {
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
           FIELD ERROR
        ========================================================== */

        .field-error {
            margin-top: 5px;

            font-size: 11px;

            line-height: 1.4;

            color:
                var(--error);

            display: none;
        }


        .field-error.show {
            display: block;
        }


        /* =========================================================
           SELECT
        ========================================================== */

        .form-select {
            appearance: none;

            -webkit-appearance: none;

            padding-right: 42px;

            cursor: pointer;

            color:
                #A0A0A0;
        }


        .form-select.has-value {
            color:
                #333333;
        }


        .select-arrow {
            position: absolute;

            right: 15px;

            top: 50%;

            transform:
                translateY(-50%);

            width: 18px;
            height: 18px;

            pointer-events: none;

            color:
                #171717;
        }


        /* =========================================================
           DATE INPUT
        ========================================================== */

        .date-input {
            padding-right: 42px;
        }


        .date-icon {
            position: absolute;

            right: 15px;

            top: 50%;

            transform:
                translateY(-50%);

            width: 18px;
            height: 18px;

            pointer-events: none;

            color:
                #111111;
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
           CONTACT NUMBER
        ========================================================== */

        .contact-wrapper {
            display: flex;

            align-items: center;

            height: 48px;

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
            width: 18px;
            height: 18px;

            margin-left: 14px;

            color:
                #454545;

            flex-shrink: 0;
        }


        .country-code {
            padding:
                0
                10px;

            font-size: 14px;

            color:
                #333333;

            flex-shrink: 0;
        }


        .contact-input {
            flex: 1;

            height: 100%;

            border: none;

            outline: none;

            padding:
                0
                12px
                0
                0;

            font-family:
                'Poppins',
                sans-serif;

            font-size: 14px;

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
           ADDRESS TITLE
        ========================================================== */

        .address-heading {
            margin-top: 2px;

            margin-bottom: -9px;
        }


        .address-heading-title {
            margin: 0;

            font-size: 16px;

            font-weight: 500;

            color:
                #161616;
        }


        .address-heading-subtitle {
            margin:
                3px
                0
                0;

            font-size: 12px;

            color:
                #A0A0A0;
        }


        /* =========================================================
           ADDRESS GRID
        ========================================================== */

        .address-grid {
            grid-column:
                1 / -1;

            display: grid;

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

            display: grid;

            grid-template-columns:
                repeat(4, 1fr);

            gap:
                25px;
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
            width: 345px;

            height: 205px;

            border:
                1.5px solid
                #D2D2D2;

            border-radius:
                10px;

            background:
                #FFFFFF;

            display: flex;

            align-items: center;

            justify-content: center;

            cursor: pointer;

            overflow: hidden;

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


        .upload-placeholder {
            display: flex;

            flex-direction: column;

            align-items: center;

            justify-content: center;

            text-align: center;

            color:
                #B3B3B3;
        }


        .upload-placeholder svg {
            width: 53px;
            height: 53px;

            margin-bottom: 9px;
        }


        .upload-placeholder p {
            margin: 0;

            font-size: 13px;

            font-weight: 500;

            color:
                #A8A8A8;
        }


        .upload-placeholder small {
            margin-top: 4px;

            font-size: 11px;

            color:
                #B6B6B6;
        }


        .valid-id-preview {
            width: 100%;
            height: 100%;

            object-fit: contain;

            padding: 10px;

            background:
                #FFFFFF;
        }


        .valid-id-file-name {
            margin-top: 7px;

            width: 345px;

            overflow: hidden;

            text-overflow: ellipsis;

            white-space: nowrap;

            font-size: 12px;

            color:
                var(--maroon);

            font-weight: 500;
        }


        .hidden-file-input {
            display: none;
        }


        /* =========================================================
           ACTIONS
        ========================================================== */

        .form-actions {
            position: absolute;

            right: 46px;

            bottom: 26px;

            display: flex;

            justify-content: flex-end;
        }


        .continue-button {
            width: 165px;

            height: 48px;

            border: none;

            border-radius: 24px;

            background:
                var(--maroon);

            color:
                #FFFFFF;

            font-family:
                'Poppins',
                sans-serif;

            font-size: 16px;

            font-weight: 600;

            cursor: pointer;

            transition:
                background 0.2s ease,
                transform 0.1s ease,
                box-shadow 0.2s ease;
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
            opacity: 0.65;

            cursor:
                not-allowed;
        }


        /* =========================================================
           RESPONSIVE
        ========================================================== */

        @media (max-width: 1200px) {

            .buyer-form-grid {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
            }


            .address-grid {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
            }


            .address-bottom-grid {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
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
                    105px;
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


            .valid-id-upload,
            .valid-id-file-name {
                width: 100%;
            }

        }


        @media (max-width: 760px) {

            .registration-header {
                gap: 18px;

                padding-top: 24px;
            }


            .header-logo-wrapper {
                width: 105px;
                height: 100px;

                border-top-right-radius:
                    55px;

                border-bottom-right-radius:
                    55px;
            }


            .signup-logo {
                width: 70px;
            }


            .header-title {
                font-size: 25px;
            }


            .header-subtitle {
                font-size: 16px;
            }


            .stepper-wrapper {
                width: 95%;

                margin-top: 24px;
            }


            .step-label {
                font-size: 13px;
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
                font-size: 15px;
            }


            .continue-button {
                width: 150px;
            }


            .form-actions {
                right: 20px;
                bottom: 22px;
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


        <!-- LOGO / RETURN TO LOGIN -->

        <a
            href="{{ route('login') }}"
            class="header-logo-wrapper"
            aria-label="Return to login"
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
                <span>Registration</span>

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
        <!-- CLICKABLE -->

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


        <!-- =====================================================
             BUYER FORM
        ====================================================== -->

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
                            Last Name<span class="required">*</span>
                        </label>


                        <input
                            type="text"
                            id="last_name"
                            name="last_name"
                            class="form-input"
                            placeholder="Enter your last name"
                            value="{{ old('last_name') }}"
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


                    <!-- =================================================
                         FIRST NAME
                    ================================================== -->

                    <div class="field-group">

                        <label
                            for="first_name"
                            class="field-label"
                        >
                            First Name<span class="required">*</span>
                        </label>


                        <input
                            type="text"
                            id="first_name"
                            name="first_name"
                            class="form-input"
                            placeholder="Enter your first name"
                            value="{{ old('first_name') }}"
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


                    <!-- =================================================
                         MIDDLE NAME
                    ================================================== -->

                    <div class="field-group">

                        <label
                            for="middle_name"
                            class="field-label"
                        >
                            Middle Name<span class="required">*</span>
                        </label>


                        <input
                            type="text"
                            id="middle_name"
                            name="middle_name"
                            class="form-input"
                            placeholder="Enter your middle name"
                            value="{{ old('middle_name') }}"
                            required
                        >

                        <div
                            class="field-error"
                            id="middleNameError"
                        >
                            Please enter your middle name.
                        </div>

                    </div>


                    <!-- =================================================
                         SEX
                    ================================================== -->

                    <div class="field-group">

                        <label
                            for="sex"
                            class="field-label"
                        >
                            Sex<span class="required">*</span>
                        </label>


                        <div class="input-wrap">

                            <select
                                id="sex"
                                name="sex"
                                class="form-select"
                                required
                            >

                                <option
                                    value=""
                                    selected
                                    disabled
                                >
                                    Select sex
                                </option>

                                <option value="Male">
                                    Male
                                </option>

                                <option value="Female">
                                    Female
                                </option>

                                <option value="Prefer not to say">
                                    Prefer not to say
                                </option>

                            </select>


                            <svg
                                class="select-arrow"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >

                                <path d="M6 9l6 6 6-6" />

                            </svg>

                        </div>


                        <div
                            class="field-error"
                            id="sexError"
                        >
                            Please select your sex.
                        </div>

                    </div>


                    <!-- =================================================
                         EMAIL
                    ================================================== -->

                    <div class="field-group">

                        <label
                            for="email"
                            class="field-label"
                        >
                            Email<span class="required">*</span>
                        </label>


                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-input"
                            placeholder="Enter your email"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            required
                        >


                        <div
                            class="field-error"
                            id="emailError"
                        >
                            Please enter your email.
                        </div>

                    </div>


                    <!-- =================================================
                         CONTACT NUMBER
                    ================================================== -->

                    <div class="field-group">

                        <label
                            for="contact_no"
                            class="field-label"
                        >
                            Contact No.<span class="required">*</span>
                        </label>


                        <div class="contact-wrapper"
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
                                value="{{ old('contact_no') }}"
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


                    <!-- =================================================
                         BIRTHDAY
                    ================================================== -->

                    <div class="field-group">

                        <label
                            for="birthday"
                            class="field-label"
                        >
                            Birthday<span class="required">*</span>
                        </label>


                        <div class="input-wrap">

                            <input
                                type="date"
                                id="birthday"
                                name="birthday"
                                class="form-input date-input"
                                value="{{ old('birthday') }}"
                                required
                            >


                            <svg
                                class="date-icon"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >

                                <rect
                                    x="3"
                                    y="4"
                                    width="18"
                                    height="18"
                                    rx="2"
                                />

                                <line
                                    x1="16"
                                    y1="2"
                                    x2="16"
                                    y2="6"
                                />

                                <line
                                    x1="8"
                                    y1="2"
                                    x2="8"
                                    y2="6"
                                />

                                <line
                                    x1="3"
                                    y1="10"
                                    x2="21"
                                    y2="10"
                                />

                            </svg>

                        </div>


                        <div
                            class="field-error"
                            id="birthdayError"
                        >
                            Please select your birthday.
                        </div>

                    </div>


                    <!-- =================================================
                         AGE
                    ================================================== -->

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
                            value=""
                            readonly
                        >

                    </div>


                    <!-- =================================================
                         ADDRESS HEADING
                    ================================================== -->

                    <div class="field-group full-width address-heading">

                        <h3 class="address-heading-title">
                            Address
                        </h3>

                        <p class="address-heading-subtitle">
                            Please select your address
                        </p>

                    </div>


                    <!-- =================================================
                         ADDRESS TOP ROW
                    ================================================== -->

                    <div class="address-grid">


                        <!-- PROVINCE -->

                        <div class="field-group">

                            <label
                                for="province"
                                class="field-label"
                            >
                                Province<span class="required">*</span>
                            </label>


                            <div class="input-wrap">

                                <select
                                    id="province"
                                    name="province"
                                    class="form-select"
                                    required
                                >

                                    <option
                                        value=""
                                        selected
                                        disabled
                                    >
                                        Select or search province
                                    </option>

                                    <option value="Laguna">
                                        Laguna
                                    </option>

                                    <option value="Rizal">
                                        Rizal
                                    </option>

                                    <option value="Batangas">
                                        Batangas
                                    </option>

                                    <option value="Quezon">
                                        Quezon
                                    </option>

                                    <option value="Cavite">
                                        Cavite
                                    </option>

                                </select>


                                <svg
                                    class="select-arrow"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >

                                    <path d="M6 9l6 6 6-6" />

                                </svg>

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
                                for="municipality"
                                class="field-label"
                            >
                                Municipality<span class="required">*</span>
                            </label>


                            <div class="input-wrap">

                                <select
                                    id="municipality"
                                    name="municipality"
                                    class="form-select"
                                    required
                                >

                                    <option
                                        value=""
                                        selected
                                        disabled
                                    >
                                        Select or search municipality
                                    </option>

                                </select>


                                <svg
                                    class="select-arrow"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >

                                    <path d="M6 9l6 6 6-6" />

                                </svg>

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
                                for="barangay"
                                class="field-label"
                            >
                                Barangay<span class="required">*</span>
                            </label>


                            <div class="input-wrap">

                                <select
                                    id="barangay"
                                    name="barangay"
                                    class="form-select"
                                    required
                                >

                                    <option
                                        value=""
                                        selected
                                        disabled
                                    >
                                        Select or search barangay
                                    </option>

                                </select>


                                <svg
                                    class="select-arrow"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >

                                    <path d="M6 9l6 6 6-6" />

                                </svg>

                            </div>


                            <div
                                class="field-error"
                                id="barangayError"
                            >
                                Please select your barangay.
                            </div>

                        </div>


                        <!-- ZIP CODE -->

                        <div class="field-group">

                            <label
                                for="zip_code"
                                class="field-label"
                            >
                                Zip Code<span class="required">*</span>
                            </label>


                            <input
                                type="text"
                                id="zip_code"
                                name="zip_code"
                                class="form-input"
                                placeholder="Enter zip code"
                                value="{{ old('zip_code') }}"
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
                         ADDRESS BOTTOM ROW
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
                                value="{{ old('street') }}"
                            >

                        </div>


                        <!-- HOUSE NO -->

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
                                value="{{ old('house_no') }}"
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
                                value="{{ old('building') }}"
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
                                value="{{ old('subdivision') }}"
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
                            Valid ID<span class="required">*</span>
                        </label>


                        <label
                            for="valid_id"
                            class="valid-id-upload"
                            id="validIdUploadBox"
                        >

                            <div
                                class="upload-placeholder"
                                id="validIdPlaceholder"
                            >

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

                            </div>


                            <img
                                src=""
                                alt="Valid ID preview"
                                class="valid-id-preview"
                                id="validIdPreviewImage"
                                style="display:none;"
                            >

                        </label>


                        <input
                            type="file"
                            id="valid_id"
                            name="valid_id"
                            class="hidden-file-input"
                            accept=".jpg,.jpeg,.png,.webp"
                            required
                        >


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
                     CONTINUE BUTTON
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
           AGE AUTO GENERATION
        ========================================================== */

        const birthdayInput =
            document.getElementById(
                'birthday'
            );


        const ageInput =
            document.getElementById(
                'age'
            );


        function calculateAge(
            birthDate
        ) {

            if (!birthDate) {
                ageInput.value = '';
                return;
            }


            const today =
                new Date();


            const birth =
                new Date(
                    birthDate
                );


            let age =
                today.getFullYear()
                -
                birth.getFullYear();


            const monthDifference =
                today.getMonth()
                -
                birth.getMonth();


            if (
                monthDifference < 0 ||
                (
                    monthDifference === 0 &&
                    today.getDate() < birth.getDate()
                )
            ) {

                age--;

            }


            ageInput.value =
                age >= 0
                    ? age
                    : '';

        }


        if (birthdayInput) {

            birthdayInput.addEventListener(
                'change',
                function () {

                    calculateAge(
                        this.value
                    );

                }
            );

        }


        /* =========================================================
           SELECT COLOR
        ========================================================== */

        const selects =
            document.querySelectorAll(
                '.form-select'
            );


        selects.forEach(
            function (select) {

                select.addEventListener(
                    'change',
                    function () {

                        if (
                            this.value
                        ) {

                            this.classList.add(
                                'has-value'
                            );

                        } else {

                            this.classList.remove(
                                'has-value'
                            );

                        }

                        this.classList.remove(
                            'required-error'
                        );

                    }
                );

            }
        );


        /* =========================================================
           CONTACT NUMBER
        ========================================================== */

        const contactInput =
            document.getElementById(
                'contact_no'
            );


        const contactWrapper =
            document.getElementById(
                'contactWrapper'
            );


        if (contactInput) {

            contactInput.addEventListener(
                'input',
                function () {

                    this.value =
                        this.value
                            .replace(
                                /[^0-9]/g,
                                ''
                            )
                            .slice(
                                0,
                                10
                            );

                    if (this.value) {

                        contactWrapper.classList.remove(
                            'required-error'
                        );

                    }

                }
            );

        }


        /* =========================================================
           ZIP CODE
        ========================================================== */

        const zipCodeInput =
            document.getElementById(
                'zip_code'
            );


        if (zipCodeInput) {

            zipCodeInput.addEventListener(
                'input',
                function () {

                    this.value =
                        this.value
                            .replace(
                                /[^0-9]/g,
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

        const validIdInput =
            document.getElementById(
                'valid_id'
            );


        const validIdPlaceholder =
            document.getElementById(
                'validIdPlaceholder'
            );


        const validIdPreviewImage =
            document.getElementById(
                'validIdPreviewImage'
            );


        const validIdFileName =
            document.getElementById(
                'validIdFileName'
            );


        const validIdUploadBox =
            document.getElementById(
                'validIdUploadBox'
            );


        if (validIdInput) {

            validIdInput.addEventListener(
                'change',
                function () {

                    const file =
                        this.files[0];


                    if (!file) {

                        validIdPreviewImage.style.display =
                            'none';

                        validIdPreviewImage.src =
                            '';

                        validIdPlaceholder.style.display =
                            'flex';

                        validIdFileName.style.display =
                            'none';

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

                        this.value = '';

                        validIdUploadBox.classList.add(
                            'required-error'
                        );

                        alert(
                            'Please upload a JPG, JPEG, PNG, or WEBP file.'
                        );

                        return;

                    }


                    validIdUploadBox.classList.remove(
                        'required-error'
                    );


                    const reader =
                        new FileReader();


                    reader.onload =
                        function (event) {

                            validIdPreviewImage.src =
                                event.target.result;

                            validIdPreviewImage.style.display =
                                'block';

                            validIdPlaceholder.style.display =
                                'none';

                            validIdFileName.textContent =
                                file.name;

                            validIdFileName.style.display =
                                'block';

                        };


                    reader.readAsDataURL(
                        file
                    );

                }
            );

        }


        /* =========================================================
           REQUIRED FIELD VALIDATION
        ========================================================== */

        const buyerRegistrationForm =
            document.getElementById(
                'buyerRegistrationForm'
            );


        const continueButton =
            document.getElementById(
                'continueButton'
            );


        function showError(
            element,
            errorElement
        ) {

            if (element) {

                element.classList.add(
                    'required-error'
                );

            }


            if (errorElement) {

                errorElement.classList.add(
                    'show'
                );

            }

        }


        function clearError(
            element,
            errorElement
        ) {

            if (element) {

                element.classList.remove(
                    'required-error'
                );

            }


            if (errorElement) {

                errorElement.classList.remove(
                    'show'
                );

            }

        }


        function validateRequiredFields() {

            let isValid = true;


            /* LAST NAME */

            const lastName =
                document.getElementById(
                    'last_name'
                );

            const lastNameError =
                document.getElementById(
                    'lastNameError'
                );


            if (!lastName.value.trim()) {

                showError(
                    lastName,
                    lastNameError
                );

                isValid = false;

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


            if (!firstName.value.trim()) {

                showError(
                    firstName,
                    firstNameError
                );

                isValid = false;

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


            if (!middleName.value.trim()) {

                showError(
                    middleName,
                    middleNameError
                );

                isValid = false;

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


            if (!sex.value) {

                showError(
                    sex,
                    sexError
                );

                isValid = false;

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


            if (!email.value.trim()) {

                showError(
                    email,
                    emailError
                );

                isValid = false;

            } else if (!email.checkValidity()) {

                showError(
                    email,
                    emailError
                );

                emailError.textContent =
                    'Please enter a valid email address.';

                isValid = false;

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

                isValid = false;

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


            if (!birthdayInput.value) {

                showError(
                    birthdayInput,
                    birthdayError
                );

                isValid = false;

            } else {

                clearError(
                    birthdayInput,
                    birthdayError
                );

            }


            /* PROVINCE */

            const provinceSelect =
                document.getElementById(
                    'province'
                );

            const provinceError =
                document.getElementById(
                    'provinceError'
                );


            if (!provinceSelect.value) {

                showError(
                    provinceSelect,
                    provinceError
                );

                isValid = false;

            } else {

                clearError(
                    provinceSelect,
                    provinceError
                );

            }


            /* MUNICIPALITY */

            const municipalitySelect =
                document.getElementById(
                    'municipality'
                );

            const municipalityError =
                document.getElementById(
                    'municipalityError'
                );


            if (!municipalitySelect.value) {

                showError(
                    municipalitySelect,
                    municipalityError
                );

                isValid = false;

            } else {

                clearError(
                    municipalitySelect,
                    municipalityError
                );

            }


            /* BARANGAY */

            const barangaySelect =
                document.getElementById(
                    'barangay'
                );

            const barangayError =
                document.getElementById(
                    'barangayError'
                );


            if (!barangaySelect.value) {

                showError(
                    barangaySelect,
                    barangayError
                );

                isValid = false;

            } else {

                clearError(
                    barangaySelect,
                    barangayError
                );

            }


            /* ZIP */

            const zipError =
                document.getElementById(
                    'zipError'
                );


            if (
                !zipCodeInput.value.trim()
            ) {

                showError(
                    zipCodeInput,
                    zipError
                );

                isValid = false;

            } else {

                clearError(
                    zipCodeInput,
                    zipError
                );

            }


            /* VALID ID */

            const validIdError =
                document.getElementById(
                    'validIdError'
                );


            if (
                !validIdInput.files.length
            ) {

                showError(
                    validIdUploadBox,
                    validIdError
                );

                isValid = false;

            } else {

                clearError(
                    validIdUploadBox,
                    validIdError
                );

            }


            return isValid;

        }


        /* =========================================================
           CLEAR ERRORS WHILE TYPING
        ========================================================== */

        const textInputs =
            document.querySelectorAll(
                '.form-input, .contact-input'
            );


        textInputs.forEach(
            function (input) {

                input.addEventListener(
                    'input',
                    function () {

                        this.classList.remove(
                            'required-error'
                        );

                        const error =
                            this
                                .closest(
                                    '.field-group'
                                )
                                ?.querySelector(
                                    '.field-error'
                                );

                        if (error) {

                            error.classList.remove(
                                'show'
                            );

                        }

                    }
                );

            }
        );


        /* =========================================================
           FORM SUBMIT
        ========================================================== */

        if (buyerRegistrationForm) {

            buyerRegistrationForm.addEventListener(
                'submit',
                function (event) {

                    const valid =
                        validateRequiredFields();


                    if (!valid) {

                        event.preventDefault();

                        const firstError =
                            document.querySelector(
                                '.required-error'
                            );


                        if (firstError) {

                            firstError.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });


                            if (
                                typeof firstError.focus ===
                                'function'
                            ) {

                                firstError.focus();

                            }

                        }

                        return;

                    }


                    if (continueButton) {

                        continueButton.disabled =
                            true;

                        continueButton.textContent =
                            'Continue...';

                    }

                }
            );

        }


        /* =========================================================
           MUNICIPALITY / BARANGAY DEMO DATA
        ========================================================== */

        const provinceSelect =
            document.getElementById(
                'province'
            );


        const municipalitySelect =
            document.getElementById(
                'municipality'
            );


        const barangaySelect =
            document.getElementById(
                'barangay'
            );


        const locationData = {

            Laguna: {

                municipalities: {

                    'Pila': [
                        'Aplaya',
                        'Bagong Pook',
                        'Bulilan Sur',
                        'Bulilan Norte',
                        'Linga',
                        'Lumban',
                        'Pansol',
                        'Santa Clara Norte',
                        'Santa Clara Sur'
                    ],

                    'Santa Cruz': [
                        'Bagumbayan',
                        'Bubucal',
                        'Jasaan',
                        'Labuin',
                        'Malinao'
                    ]

                }

            },

            Rizal: {

                municipalities: {

                    'Antipolo': [
                        'Dela Paz',
                        'Mayamot',
                        'San Isidro',
                        'San Jose'
                    ],

                    'Cainta': [
                        'San Andres',
                        'San Isidro',
                        'San Juan'
                    ]

                }

            },

            Batangas: {

                municipalities: {

                    'Tanauan': [
                        'Altura Bata',
                        'Altura Matanda',
                        'Darasa'
                    ]

                }

            }

        };


        if (provinceSelect) {

            provinceSelect.addEventListener(
                'change',
                function () {

                    municipalitySelect.innerHTML =
                        `
                            <option
                                value=""
                                selected
                                disabled
                            >
                                Select or search municipality
                            </option>
                        `;


                    barangaySelect.innerHTML =
                        `
                            <option
                                value=""
                                selected
                                disabled
                            >
                                Select or search barangay
                            </option>
                        `;


                    municipalitySelect.classList.remove(
                        'required-error'
                    );

                    barangaySelect.classList.remove(
                        'required-error'
                    );


                    const province =
                        locationData[
                            this.value
                        ];


                    if (
                        province &&
                        province.municipalities
                    ) {

                        Object.keys(
                            province.municipalities
                        ).forEach(
                            function (
                                municipality
                            ) {

                                const option =
                                    document.createElement(
                                        'option'
                                    );

                                option.value =
                                    municipality;

                                option.textContent =
                                    municipality;

                                municipalitySelect.appendChild(
                                    option
                                );

                            }
                        );

                    }

                }
            );

        }


        if (municipalitySelect) {

            municipalitySelect.addEventListener(
                'change',
                function () {

                    barangaySelect.innerHTML =
                        `
                            <option
                                value=""
                                selected
                                disabled
                            >
                                Select or search barangay
                            </option>
                        `;


                    barangaySelect.classList.remove(
                        'required-error'
                    );


                    const province =
                        locationData[
                            provinceSelect.value
                        ];


                    if (
                        province &&
                        province.municipalities &&
                        province.municipalities[
                            this.value
                        ]
                    ) {

                        province.municipalities[
                            this.value
                        ].forEach(
                            function (
                                barangay
                            ) {

                                const option =
                                    document.createElement(
                                        'option'
                                    );

                                option.value =
                                    barangay;

                                option.textContent =
                                    barangay;

                                barangaySelect.appendChild(
                                    option
                                );

                            }
                        );

                    }

                }
            );

        }

    }
);

</script>


</body>

</html>