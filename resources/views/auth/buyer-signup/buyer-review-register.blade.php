<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>ShopEase - Buyer Review</title>


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

            border-top-right-radius:
                60px;

            border-bottom-right-radius:
                60px;

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
           REVIEW TITLE
        ========================================================== */

        .review-title {
            margin:
                0
                0
                20px;

            font-size:
                20px;

            font-weight:
                600;

            color:
                var(--text-dark);
        }


        /* =========================================================
           REVIEW SECTION
        ========================================================== */

        .review-section {
            width:
                100%;

            margin-bottom:
                28px;

            border:
                1px solid
                #E7E0DC;

            border-radius:
                10px;

            overflow:
                hidden;

            background:
                #FFFFFF;
        }


        .review-section-header {
            padding:
                14px
                18px;

            background:
                #FBF5F2;

            border-bottom:
                1px solid
                #E7E0DC;

            display:
                flex;

            align-items:
                center;

            justify-content:
                space-between;
        }


        .review-section-title {
            margin:
                0;

            font-size:
                16px;

            font-weight:
                600;

            color:
                var(--maroon-dark);
        }


        .review-section-badge {
            font-size:
                11px;

            font-weight:
                500;

            color:
                var(--success);

            background:
                #EEF7EF;

            padding:
                5px
                10px;

            border-radius:
                20px;
        }


        .review-section-body {
            padding:
                20px;
        }


        /* =========================================================
           INFORMATION GRID
           Matches Buyer Registration Arrangement
        ========================================================== */

        .information-grid {
            display:
                grid;

            grid-template-columns:
                repeat(12, minmax(0, 1fr));

            gap:
                18px
                22px;
        }


        .information-item {
            min-width:
                0;
        }


        /* =========================================================
           PERSONAL INFORMATION
        ========================================================== */

        .buyer-last-name,
        .buyer-first-name,
        .buyer-middle-name {
            grid-column:
                span 4;
        }


        .buyer-sex,
        .buyer-email,
        .buyer-contact {
            grid-column:
                span 4;
        }


        .buyer-birthday {
            grid-column:
                span 4;
        }


        .buyer-age {
            grid-column:
                span 4;
        }


        /* =========================================================
           ADDRESS HEADING
        ========================================================== */

        .buyer-address-heading {
            grid-column:
                1 / -1;

            margin-top:
                6px;

            padding-top:
                6px;

            border-top:
                1px solid
                #EEE8E4;
        }


        .buyer-address-heading .information-label {
            margin-bottom:
                3px;

            color:
                var(--maroon-dark);

            font-size:
                12px;

            font-weight:
                600;
        }


        .buyer-address-subtitle {
            font-size:
                11px;

            color:
                #A0A0A0;
        }


        /* =========================================================
           ADDRESS
        ========================================================== */

        .buyer-province,
        .buyer-municipality,
        .buyer-barangay,
        .buyer-zip {
            grid-column:
                span 3;
        }


        .buyer-street,
        .buyer-house,
        .buyer-building,
        .buyer-subdivision {
            grid-column:
                span 3;
        }


        /* =========================================================
           LABEL
        ========================================================== */

        .information-label {
            display:
                block;

            margin-bottom:
                5px;

            font-size:
                11px;

            font-weight:
                500;

            color:
                #999393;

            text-transform:
                uppercase;

            letter-spacing:
                0.3px;
        }


        /* =========================================================
           INFORMATION VALUE
        ========================================================== */

        .information-value {
            min-height:
                38px;

            padding:
                8px
                11px;

            border:
                1px solid
                #E2DEDC;

            border-radius:
                7px;

            background:
                #FAFAFA;

            color:
                #333;

            font-size:
                13px;

            font-weight:
                500;

            display:
                flex;

            align-items:
                center;

            word-break:
                break-word;
        }


        .information-value.empty {
            color:
                #AAAAAA;

            font-weight:
                400;

            font-style:
                italic;
        }


        /* =========================================================
           VALID ID
        ========================================================== */

        .valid-id-review-section {
            width:
                345px;

            max-width:
                100%;
        }


        .document-label {
            display:
                block;

            margin-bottom:
                8px;

            font-size:
                17px;

            font-weight:
                500;

            color:
                var(--text-dark);
        }


        .document-preview {
            width:
                345px;

            max-width:
                100%;

            height:
                205px;

            border:
                1.5px solid
                var(--border);

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

            overflow:
                hidden;

            cursor:
                pointer;

            position:
                relative;

            transition:
                border-color 0.2s ease,
                box-shadow 0.2s ease,
                transform 0.2s ease;
        }


        .document-preview:hover {
            border-color:
                var(--maroon);

            box-shadow:
                0 6px 18px
                rgba(
                    82,
                    7,
                    11,
                    0.08
                );

            transform:
                translateY(-2px);
        }


        .document-image {
            width:
                100%;

            height:
                100%;

            object-fit:
                contain;

            padding:
                10px;

            background:
                #FFFFFF;
        }


        .document-placeholder {
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
                #B6B6B6;

            padding:
                20px;
        }


        .document-placeholder svg {
            width:
                58px;

            height:
                58px;

            margin-bottom:
                10px;
        }


        .document-placeholder p {
            margin:
                0;

            font-size:
                14px;

            font-weight:
                500;

            color:
                #A1A1A1;
        }


        .document-placeholder small {
            display:
                block;

            margin-top:
                4px;

            font-size:
                11px;

            color:
                #B6B6B6;
        }


        .document-file-name {
            margin-top:
                8px;

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
           NOTE
        ========================================================== */

        .document-note {
            margin-top:
                30px;

            max-width:
                1180px;

            font-size:
                14px;

            line-height:
                1.75;

            color:
                #979797;
        }


        .document-note strong {
            color:
                #161616;

            font-weight:
                500;
        }


        /* =========================================================
           ACTION
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
                0.65;

            cursor:
                not-allowed;
        }


        /* =========================================================
           DOCUMENT MODAL
        ========================================================== */

        .document-modal {
            position:
                fixed;

            inset:
                0;

            background:
                rgba(
                    22,
                    12,
                    12,
                    0.72
                );

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            padding:
                30px;

            opacity:
                0;

            visibility:
                hidden;

            pointer-events:
                none;

            transition:
                opacity 0.2s ease,
                visibility 0.2s ease;

            z-index:
                9999;
        }


        .document-modal.active {
            opacity:
                1;

            visibility:
                visible;

            pointer-events:
                auto;
        }


        .modal-content {
            width:
                min(
                    100%,
                    1050px
                );

            height:
                min(
                    88vh,
                    820px
                );

            background:
                #FFFFFF;

            border-radius:
                16px;

            overflow:
                hidden;

            box-shadow:
                0 25px
                70px
                rgba(
                    0,
                    0,
                    0,
                    0.28
                );

            display:
                flex;

            flex-direction:
                column;

            transform:
                scale(0.95);

            transition:
                transform 0.2s ease;
        }


        .document-modal.active
        .modal-content {
            transform:
                scale(1);
        }


        .modal-header {
            display:
                flex;

            align-items:
                center;

            justify-content:
                space-between;

            padding:
                16px
                20px;

            border-bottom:
                1px solid
                #ECE6E2;

            flex-shrink:
                0;
        }


        .modal-title {
            margin:
                0;

            font-size:
                17px;

            font-weight:
                600;

            color:
                #161616;
        }


        .modal-close {
            width:
                36px;

            height:
                36px;

            border:
                none;

            border-radius:
                50%;

            background:
                #F7F2EF;

            color:
                #5A1717;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            cursor:
                pointer;

            transition:
                background 0.2s ease;
        }


        .modal-close:hover {
            background:
                #F0E5E0;
        }


        .modal-close svg {
            width:
                18px;

            height:
                18px;
        }


        .modal-body {
            flex:
                1;

            min-height:
                0;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            padding:
                20px;

            background:
                #F5F1EF;

            overflow:
                auto;
        }


        .modal-body img {
            max-width:
                100%;

            max-height:
                100%;

            width:
                auto;

            height:
                auto;

            object-fit:
                contain;

            border-radius:
                6px;

            background:
                white;

            box-shadow:
                0
                4px
                18px
                rgba(
                    0,
                    0,
                    0,
                    0.08
                );
        }


        .modal-empty {
            text-align:
                center;

            color:
                #9A9A9A;

            font-size:
                14px;
        }


        /* =========================================================
           EMAIL VERIFICATION MODAL
        ========================================================== */

        .email-verification-modal {
            position:
                fixed;

            inset:
                0;

            background:
                rgba(
                    22,
                    12,
                    12,
                    0.55
                );

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            padding:
                20px;

            opacity:
                0;

            visibility:
                hidden;

            pointer-events:
                none;

            transition:
                opacity 0.2s ease,
                visibility 0.2s ease;

            z-index:
                10000;
        }


        .email-verification-modal.active {
            opacity:
                1;

            visibility:
                visible;

            pointer-events:
                auto;
        }


        .email-verification-card {
            width:
                100%;

            max-width:
                480px;

            background:
                #FFFFFF;

            border-radius:
                22px;

            padding:
                40px
                32px
                30px;

            text-align:
                center;

            box-shadow:
                0
                20px
                55px
                rgba(
                    0,
                    0,
                    0,
                    0.20
                );

            transform:
                scale(0.95);

            transition:
                transform 0.2s ease;
        }


        .email-verification-modal.active
        .email-verification-card {
            transform:
                scale(1);
        }


        .email-verification-icon-wrapper {
            width:
                88px;

            height:
                88px;

            margin:
                0
                auto
                20px;

            border-radius:
                50%;

            background:
                #FFE1DC;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;
        }


        .email-verification-icon {
            width:
                48px;

            height:
                48px;

            object-fit:
                contain;
        }


        .email-verification-title {
            margin:
                0;

            font-size:
                24px;

            line-height:
                1.2;

            font-weight:
                700;

            color:
                #000000;
        }


        .email-verification-subtitle {
            margin:
                8px
                0
                28px;

            font-size:
                16px;

            line-height:
                1.4;

            font-weight:
                400;

            color:
                #919191;
        }


        .email-code-container {
            display:
                flex;

            justify-content:
                center;

            gap:
                10px;

            margin-bottom:
                25px;
        }


        .email-code-input {
            width:
                52px;

            height:
                62px;

            border:
                1.5px solid
                #6D6D6D;

            border-radius:
                11px;

            background:
                #FFFFFF;

            text-align:
                center;

            font-family:
                'Poppins',
                sans-serif;

            font-size:
                23px;

            font-weight:
                600;

            color:
                #161616;

            outline:
                none;

            transition:
                border-color 0.2s ease,
                box-shadow 0.2s ease;
        }


        .email-code-input:focus {
            border-color:
                #7B1B1B;

            box-shadow:
                0
                0
                0
                3px
                rgba(
                    123,
                    27,
                    27,
                    0.08
                );
        }


        .email-spam-message {
            width:
                100%;

            min-height:
                48px;

            padding:
                10px
                12px;

            margin-bottom:
                22px;

            border-radius:
                11px;

            background:
                #FFE8E4;

            display:
                flex;

            align-items:
                center;

            text-align:
                left;

            color:
                #6E1717;

            font-size:
                13px;

            font-weight:
                400;
        }


        .email-spam-icon {
            width:
                19px;

            height:
                19px;

            margin-right:
                9px;

            flex-shrink:
                0;
        }


        .email-divider {
            display:
                flex;

            align-items:
                center;

            gap:
                14px;

            margin:
                0
                10px
                20px;
        }


        .email-divider-line {
            flex:
                1;

            height:
                1px;

            background:
                #BDBDBD;
        }


        .email-divider-text {
            font-size:
                17px;

            color:
                #929292;

            font-weight:
                400;
        }


        .email-resend-button {
            border:
                none;

            background:
                transparent;

            padding:
                0;

            margin-bottom:
                24px;

            display:
                inline-flex;

            align-items:
                center;

            justify-content:
                center;

            gap:
                6px;

            font-family:
                'Poppins',
                sans-serif;

            font-size:
                15px;

            font-weight:
                400;

            color:
                #A52A2A;

            cursor:
                pointer;
        }


        .email-resend-button:hover {
            color:
                #7B1B1B;
        }


        .email-resend-icon {
            width:
                18px;

            height:
                18px;
        }


        .email-verify-button {
            width:
                100%;

            height:
                48px;

            border:
                none;

            border-radius:
                24px;

            background:
                #A52A2A;

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


        .email-verify-button:hover {
            background:
                #8F211F;

            box-shadow:
                0
                5px
                14px
                rgba(
                    123,
                    27,
                    27,
                    0.18
                );
        }


        .email-verify-button:active {
            transform:
                scale(0.98);
        }


        .email-verify-button:disabled {
            opacity:
                0.6;

            cursor:
                not-allowed;

            box-shadow:
                none;
        }


        /* =========================================================
           REGISTRATION SUBMITTED MODAL
        ========================================================== */

        .registration-submitted-modal {
            position:
                fixed;

            inset:
                0;

            z-index:
                10001;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            padding:
                20px;

            background:
                rgba(
                    0,
                    0,
                    0,
                    0.48
                );

            opacity:
                0;

            visibility:
                hidden;

            pointer-events:
                none;

            transition:
                opacity 0.25s ease,
                visibility 0.25s ease;
        }


        .registration-submitted-modal.active {
            opacity:
                1;

            visibility:
                visible;

            pointer-events:
                auto;
        }


        .registration-submitted-card {
            width:
                100%;

            max-width:
                480px;

            max-height:
                calc(
                    100vh - 40px
                );

            overflow-y:
                auto;

            background:
                #FFFFFF;

            border-radius:
                22px;

            padding:
                38px
                32px
                30px;

            text-align:
                center;

            box-shadow:
                0
                25px
                60px
                rgba(
                    80,
                    0,
                    10,
                    0.18
                );

            transform:
                translateY(10px)
                scale(0.98);

            transition:
                transform 0.25s ease;
        }


        .registration-submitted-modal.active
        .registration-submitted-card {
            transform:
                translateY(0)
                scale(1);
        }


        .registration-submitted-icon-wrapper {
            width:
                88px;

            height:
                88px;

            margin:
                0
                auto
                22px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            background:
                #FFE2DF;

            border-radius:
                50%;
        }


        .registration-submitted-icon {
            width:
                48px;

            height:
                48px;

            object-fit:
                contain;

            display:
                block;
        }


        .registration-submitted-title {
            margin:
                0;

            font-family:
                'Poppins',
                sans-serif;

            font-size:
                24px;

            font-weight:
                700;

            line-height:
                1.2;

            color:
                #111111;
        }


        .registration-submitted-description {
            margin:
                9px
                auto
                27px;

            max-width:
                390px;

            font-family:
                'Poppins',
                sans-serif;

            font-size:
                16px;

            font-weight:
                400;

            line-height:
                1.45;

            color:
                #8F8F8F;
        }


        .registration-submitted-notice {
            width:
                100%;

            display:
                flex;

            align-items:
                flex-start;

            gap:
                14px;

            padding:
                18px;

            box-sizing:
                border-box;

            background:
                #FFE7E5;

            border-radius:
                14px;

            text-align:
                left;

            margin-bottom:
                28px;
        }


        .registration-submitted-check {
            flex:
                0
                0
                auto;

            width:
                38px;

            height:
                38px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            margin-top:
                1px;

            background:
                #76080F;

            border-radius:
                50%;
        }


        .registration-submitted-check svg {
            width:
                20px;

            height:
                20px;

            stroke:
                #FFFFFF;

            stroke-width:
                2.5;
        }


        .registration-submitted-notice-content {
            flex:
                1;
        }


        .registration-submitted-notice-title {
            margin:
                0
                0
                5px;

            font-family:
                'Poppins',
                sans-serif;

            font-size:
                16px;

            font-weight:
                700;

            line-height:
                1.35;

            color:
                #171717;
        }


        .registration-submitted-notice-text {
            margin:
                0;

            font-family:
                'Poppins',
                sans-serif;

            font-size:
                14px;

            font-weight:
                400;

            line-height:
                1.55;

            color:
                #262626;
        }


        .registration-submitted-button {
            width:
                100%;

            height:
                48px;

            border:
                none;

            border-radius:
                24px;

            background:
                #B52D2D;

            color:
                #FFFFFF;

            font-family:
                'Poppins',
                sans-serif;

            font-size:
                16px;

            font-weight:
                700;

            cursor:
                pointer;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            transition:
                background 0.2s ease,
                transform 0.2s ease;
        }


        .registration-submitted-button:hover {
            background:
                #9F2525;

            transform:
                translateY(-1px);
        }


        .registration-submitted-button:active {
            transform:
                translateY(0);
        }


        /* =========================================================
           RESPONSIVE
        ========================================================== */

        @media (max-width: 1200px) {

            .information-grid {
                grid-template-columns:
                    repeat(12, minmax(0, 1fr));
            }


            .buyer-last-name,
            .buyer-first-name,
            .buyer-middle-name,
            .buyer-sex,
            .buyer-email,
            .buyer-contact {
                grid-column:
                    span 6;
            }


            .buyer-birthday,
            .buyer-age {
                grid-column:
                    span 6;
            }


            .buyer-province,
            .buyer-municipality,
            .buyer-barangay,
            .buyer-zip,
            .buyer-street,
            .buyer-house,
            .buyer-building,
            .buyer-subdivision {
                grid-column:
                    span 6;
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


            .buyer-last-name,
            .buyer-first-name,
            .buyer-middle-name,
            .buyer-sex,
            .buyer-email,
            .buyer-contact,
            .buyer-birthday,
            .buyer-age,
            .buyer-province,
            .buyer-municipality,
            .buyer-barangay,
            .buyer-zip,
            .buyer-street,
            .buyer-house,
            .buyer-building,
            .buyer-subdivision {
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
                padding:
                    24px
                    20px
                    105px;
            }


            .review-title {
                font-size:
                    19px;
            }


            .information-grid {
                grid-template-columns:
                    1fr;
            }


            .buyer-last-name,
            .buyer-first-name,
            .buyer-middle-name,
            .buyer-sex,
            .buyer-email,
            .buyer-contact,
            .buyer-birthday,
            .buyer-age,
            .buyer-province,
            .buyer-municipality,
            .buyer-barangay,
            .buyer-zip,
            .buyer-street,
            .buyer-house,
            .buyer-building,
            .buyer-subdivision {
                grid-column:
                    1 / -1;
            }


            .document-preview {
                width:
                    100%;
            }


            .document-file-name {
                width:
                    100%;
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


            .document-modal {
                padding:
                    15px;
            }


            .modal-content {
                height:
                    90vh;

                border-radius:
                    12px;
            }


            .modal-body {
                padding:
                    10px;
            }


            .email-verification-card {
                max-width:
                    400px;

                padding:
                    32px
                    22px
                    25px;

                border-radius:
                    20px;
            }


            .email-verification-icon-wrapper {
                width:
                    75px;

                height:
                    75px;

                margin-bottom:
                    17px;
            }


            .email-verification-icon {
                width:
                    42px;

                height:
                    42px;
            }


            .email-verification-title {
                font-size:
                    21px;
            }


            .email-verification-subtitle {
                font-size:
                    14px;

                margin-bottom:
                    24px;
            }


            .email-code-container {
                gap:
                    7px;

                margin-bottom:
                    22px;
            }


            .email-code-input {
                width:
                    45px;

                height:
                    55px;

                border-radius:
                    9px;

                font-size:
                    21px;
            }


            .email-spam-message {
                font-size:
                    12px;

                min-height:
                    48px;
            }


            .email-resend-button {
                font-size:
                    14px;
            }


            .email-verify-button {
                height:
                    46px;

                font-size:
                    15px;
            }


            .registration-submitted-card {
                max-width:
                    400px;

                padding:
                    32px
                    24px
                    25px;

                border-radius:
                    20px;
            }


            .registration-submitted-icon-wrapper {
                width:
                    76px;

                height:
                    76px;

                margin-bottom:
                    18px;
            }


            .registration-submitted-icon {
                width:
                    42px;

                height:
                    42px;
            }


            .registration-submitted-title {
                font-size:
                    21px;
            }


            .registration-submitted-description {
                font-size:
                    14px;

                margin-top:
                    8px;

                margin-bottom:
                    22px;
            }


            .registration-submitted-notice {
                gap:
                    11px;

                padding:
                    15px;

                border-radius:
                    12px;

                margin-bottom:
                    23px;
            }


            .registration-submitted-check {
                width:
                    34px;

                height:
                    34px;
            }


            .registration-submitted-check svg {
                width:
                    18px;

                height:
                    18px;
            }


            .registration-submitted-notice-title {
                font-size:
                    14px;
            }


            .registration-submitted-notice-text {
                font-size:
                    13px;
            }


            .registration-submitted-button {
                height:
                    46px;

                font-size:
                    15px;
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
    href="{{ url('/') }}"
    class="header-logo-wrapper"
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

        <a
            href="{{ route('buyer.register') }}"
            class="step step-clickable"
            id="stepOneLink"
            aria-label="Go to Buyer Information"
        >

            <div class="step-number">
                1
            </div>


            <div class="step-label">
                Buyer Information
            </div>

        </a>


        <!-- STEP 2 -->

        <div
            class="step active"
            id="stepTwoCurrent"
        >

            <div class="step-number">
                2
            </div>


            <div class="step-label">
                Review Information
            </div>

        </div>

    </div>


    <!-- =========================================================
         MAIN CONTENT
    ========================================================== -->

    <main class="registration-content">


        <!-- =====================================================
             FINAL FORM
        ====================================================== -->

        <form
            id="buyerCompletionForm"
            method="POST"
            action="{{ route('buyer.register.complete') }}"
        >

            @csrf


            <div class="registration-card">


                <h2 class="review-title">

                    Review your information

                </h2>


                <!-- =================================================
                     BUYER INFORMATION
                ================================================== -->

                <div class="review-section">


                    <div class="review-section-header">

                        <h3 class="review-section-title">

                            Buyer Information

                        </h3>


                        <span class="review-section-badge">

                            Completed

                        </span>

                    </div>


                    <div class="review-section-body">


                        <div class="information-grid">


                            <!-- =================================================
                                 LAST NAME
                            ================================================== -->

                            <div class="information-item buyer-last-name">

                                <span class="information-label">
                                    Last Name
                                </span>


                                <div class="information-value
                                    {{ empty($buyerData['last_name'] ?? null) ? 'empty' : '' }}"
                                >

                                    {{ $buyerData['last_name'] ?? 'Not provided' }}

                                </div>

                            </div>


                            <!-- =================================================
                                 FIRST NAME
                            ================================================== -->

                            <div class="information-item buyer-first-name">

                                <span class="information-label">
                                    First Name
                                </span>


                                <div class="information-value
                                    {{ empty($buyerData['first_name'] ?? null) ? 'empty' : '' }}"
                                >

                                    {{ $buyerData['first_name'] ?? 'Not provided' }}

                                </div>

                            </div>


                            <!-- =================================================
                                 MIDDLE NAME
                            ================================================== -->

                            <div class="information-item buyer-middle-name">

                                <span class="information-label">
                                    Middle Name
                                </span>


                                <div class="information-value
                                    {{ empty($buyerData['middle_name'] ?? null) ? 'empty' : '' }}"
                                >

                                    {{ $buyerData['middle_name'] ?? 'Not provided' }}

                                </div>

                            </div>


                            <!-- =================================================
                                 SEX
                            ================================================== -->

                            <div class="information-item buyer-sex">

                                <span class="information-label">
                                    Sex
                                </span>


                                <div class="information-value
                                    {{ empty($buyerData['sex'] ?? null) ? 'empty' : '' }}"
                                >

                                    {{ $buyerData['sex'] ?? 'Not provided' }}

                                </div>

                            </div>


                            <!-- =================================================
                                 EMAIL
                            ================================================== -->

                            <div class="information-item buyer-email">

                                <span class="information-label">
                                    Email
                                </span>


                                <div class="information-value
                                    {{ empty($buyerData['email'] ?? null) ? 'empty' : '' }}"
                                >

                                    {{ $buyerData['email'] ?? 'Not provided' }}

                                </div>

                            </div>


                            <!-- =================================================
                                 CONTACT
                            ================================================== -->

                            <div class="information-item buyer-contact">

                                <span class="information-label">
                                    Contact No.
                                </span>


                                <div class="information-value
                                    {{ empty($buyerData['contact_no'] ?? null) ? 'empty' : '' }}"
                                >

                                    {{ $buyerData['contact_no'] ?? 'Not provided' }}

                                </div>

                            </div>


                            <!-- =================================================
                                 BIRTHDAY
                            ================================================== -->

                            <div class="information-item buyer-birthday">

                                <span class="information-label">
                                    Birthday
                                </span>


                                <div class="information-value
                                    {{ empty($buyerData['birthday'] ?? null) ? 'empty' : '' }}"
                                >

                                    {{ $buyerData['birthday'] ?? 'Not provided' }}

                                </div>

                            </div>


                            <!-- =================================================
                                 AGE
                            ================================================== -->

                            <div class="information-item buyer-age">

                                <span class="information-label">
                                    Age
                                </span>


                                <div class="information-value
                                    {{ empty($buyerData['age'] ?? null) ? 'empty' : '' }}"
                                >

                                    {{ $buyerData['age'] ?? 'Not provided' }}

                                </div>

                            </div>


                            <!-- =================================================
                                 ADDRESS HEADING
                            ================================================== -->

                            <div class="information-item buyer-address-heading">

                                <span class="information-label">

                                    Address

                                </span>


                                <div class="buyer-address-subtitle">

                                    Please review your complete address.

                                </div>

                            </div>


                            <!-- =================================================
                                 PROVINCE
                            ================================================== -->

                            <div class="information-item buyer-province">

                                <span class="information-label">
                                    Province
                                </span>


                                <div class="information-value
                                    {{ empty($buyerData['province'] ?? null) ? 'empty' : '' }}"
                                >

                                    {{ $buyerData['province'] ?? 'Not provided' }}

                                </div>

                            </div>


                            <!-- =================================================
                                 MUNICIPALITY
                            ================================================== -->

                            <div class="information-item buyer-municipality">

                                <span class="information-label">
                                    Municipality
                                </span>


                                <div class="information-value
                                    {{ empty($buyerData['municipality'] ?? null) ? 'empty' : '' }}"
                                >

                                    {{ $buyerData['municipality'] ?? 'Not provided' }}

                                </div>

                            </div>


                            <!-- =================================================
                                 BARANGAY
                            ================================================== -->

                            <div class="information-item buyer-barangay">

                                <span class="information-label">
                                    Barangay
                                </span>


                                <div class="information-value
                                    {{ empty($buyerData['barangay'] ?? null) ? 'empty' : '' }}"
                                >

                                    {{ $buyerData['barangay'] ?? 'Not provided' }}

                                </div>

                            </div>


                            <!-- =================================================
                                 ZIP
                            ================================================== -->

                            <div class="information-item buyer-zip">

                                <span class="information-label">
                                    Zip Code
                                </span>


                                <div class="information-value
                                    {{ empty($buyerData['zip_code'] ?? null) ? 'empty' : '' }}"
                                >

                                    {{ $buyerData['zip_code'] ?? 'Not provided' }}

                                </div>

                            </div>


                            <!-- =================================================
                                 STREET
                            ================================================== -->

                            <div class="information-item buyer-street">

                                <span class="information-label">
                                    Street
                                </span>


                                <div class="information-value
                                    {{ empty($buyerData['street'] ?? null) ? 'empty' : '' }}"
                                >

                                    {{ $buyerData['street'] ?? 'Not provided' }}

                                </div>

                            </div>


                            <!-- =================================================
                                 HOUSE NO.
                            ================================================== -->

                            <div class="information-item buyer-house">

                                <span class="information-label">
                                    House No.
                                </span>


                                <div class="information-value
                                    {{ empty($buyerData['house_no'] ?? null) ? 'empty' : '' }}"
                                >

                                    {{ $buyerData['house_no'] ?? 'Not provided' }}

                                </div>

                            </div>


                            <!-- =================================================
                                 BUILDING
                            ================================================== -->

                            <div class="information-item buyer-building">

                                <span class="information-label">
                                    Building
                                </span>


                                <div class="information-value
                                    {{ empty($buyerData['building'] ?? null) ? 'empty' : '' }}"
                                >

                                    {{ $buyerData['building'] ?? 'Not provided' }}

                                </div>

                            </div>


                            <!-- =================================================
                                 SUBDIVISION
                            ================================================== -->

                            <div class="information-item buyer-subdivision">

                                <span class="information-label">
                                    Subdivision
                                </span>


                                <div class="information-value
                                    {{ empty($buyerData['subdivision'] ?? null) ? 'empty' : '' }}"
                                >

                                    {{ $buyerData['subdivision'] ?? 'Not provided' }}

                                </div>

                            </div>


                        </div>

                    </div>

                </div>


                <!-- =================================================
                     VALID ID
                ================================================== -->

                @php

                    $buyerData =
                        is_array($buyerData ?? null)
                            ? $buyerData
                            : [];


                    $validIdPath =
                        $buyerData['valid_id_path']
                            ?? null;


                    $validIdOriginalName =
                        $buyerData['valid_id_original_name']
                            ?? null;


                    $validIdUrl =
                        !empty($validIdPath)
                            ? \Illuminate\Support\Facades\Storage::url(
                                $validIdPath
                            )
                            : null;


                    $validIdExtension =
                        !empty($validIdPath)
                            ? strtolower(
                                pathinfo(
                                    $validIdPath,
                                    PATHINFO_EXTENSION
                                )
                            )
                            : null;

                @endphp


                <div class="review-section">


                    <div class="review-section-header">

                        <h3 class="review-section-title">

                            Uploaded Documents

                        </h3>


                        <span class="review-section-badge">

                            Review

                        </span>

                    </div>


                    <div class="review-section-body">


                        <div class="valid-id-review-section">


                            <label class="document-label">

                                Valid ID

                            </label>


                            <div
                                class="document-preview"
                                id="validIdPreview"
                                role="button"
                                tabindex="0"
                                aria-label="View uploaded Valid ID"
                            >


                                @if($validIdPath)


                                    @if(
                                        in_array(
                                            $validIdExtension,
                                            [
                                                'jpg',
                                                'jpeg',
                                                'png',
                                                'webp'
                                            ]
                                        )
                                    )


                                        <img
                                            src="{{ $validIdUrl }}"
                                            alt="Uploaded Valid ID"
                                            class="document-image"
                                        >


                                    @else


                                        <div
                                            class="document-placeholder"
                                        >

                                            <svg
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="#7B1B1B"
                                                stroke-width="1.6"
                                            >

                                                <path
                                                    d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"
                                                />

                                                <path
                                                    d="M14 2v6h6"
                                                />

                                                <path
                                                    d="M8 13h8"
                                                />

                                                <path
                                                    d="M8 17h6"
                                                />

                                            </svg>


                                            <p
                                                style="
                                                    color:#7B1B1B;
                                                "
                                            >

                                                Document Uploaded

                                            </p>


                                            <small>

                                                Click to view

                                            </small>

                                        </div>


                                    @endif


                                @else


                                    <div
                                        class="document-placeholder"
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

                                            Valid ID not uploaded

                                        </p>


                                        <small>

                                            Please return to Step 1

                                        </small>

                                    </div>


                                @endif


                            </div>


                            @if($validIdPath)

                                <div class="document-file-name">

                                    {{
                                        $validIdOriginalName
                                            ?? basename($validIdPath)
                                    }}

                                </div>

                            @endif


                        </div>


                        <!-- =================================================
                             NOTE
                        ================================================== -->

                        <div class="document-note">

                            <strong>

                                NOTE:

                            </strong>

                            Your information and uploaded document will only
                            be used to process your buyer registration.

                        </div>

                    </div>

                </div>


                <!-- =================================================
                     CONTINUE / SUBMIT
                ================================================== -->

                <div class="form-actions">

                    <button
                        type="submit"
                        class="continue-button"
                        id="continueButton"
                    >

                        Submit

                    </button>

                </div>


            </div>

        </form>

    </main>

</div>


<!-- =========================================================
     DOCUMENT PREVIEW MODAL
========================================================== -->

<div
    class="document-modal"
    id="documentModal"
    aria-hidden="true"
>

    <div
        class="modal-content"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modalTitle"
    >

        <div class="modal-header">

            <h3
                class="modal-title"
                id="modalTitle"
            >

                Document Preview

            </h3>


            <button
                type="button"
                class="modal-close"
                id="modalClose"
                aria-label="Close document preview"
            >

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >

                    <path
                        d="M6 6L18 18"
                    />

                    <path
                        d="M18 6L6 18"
                    />

                </svg>

            </button>

        </div>


        <div
            class="modal-body"
            id="modalBody"
        >

            <div class="modal-empty">

                Select a document to preview.

            </div>

        </div>

    </div>

</div>


<!-- =========================================================
     EMAIL VERIFICATION MODAL
========================================================== -->

<div
    class="email-verification-modal"
    id="emailVerificationModal"
    aria-hidden="true"
>

    <div
        class="email-verification-card"
        role="dialog"
        aria-modal="true"
        aria-labelledby="emailVerificationTitle"
    >


        <!-- ICON -->

        <div class="email-verification-icon-wrapper">

            <img
                src="{{ asset('icons/login/email-verify.png') }}"
                alt="Email verification"
                class="email-verification-icon"
            >

        </div>


        <!-- TITLE -->

        <h2
            class="email-verification-title"
            id="emailVerificationTitle"
        >

            Verify Your Email

        </h2>


        <!-- SUBTITLE -->

        <p class="email-verification-subtitle">

            We’ve sent a 6-digit code to your email.

        </p>


        <!-- CODE -->

        <div class="email-code-container">

            @for($i = 1; $i <= 6; $i++)

                <input
                    type="text"
                    maxlength="1"
                    inputmode="numeric"
                    class="email-code-input"
                    aria-label="Digit {{ $i }}"
                >

            @endfor

        </div>


        <!-- SPAM MESSAGE -->

        <div class="email-spam-message">

            <svg
                class="email-spam-icon"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
            >

                <circle
                    cx="12"
                    cy="12"
                    r="9"
                />

                <path
                    d="M12 8v5"
                />

                <circle
                    cx="12"
                    cy="16.5"
                    r=".5"
                    fill="currentColor"
                />

            </svg>


            <span>

                Didn’t receive the code? Check your spam folder

            </span>

        </div>


        <!-- DIVIDER -->

        <div class="email-divider">

            <div class="email-divider-line"></div>

            <span class="email-divider-text">
                or
            </span>

            <div class="email-divider-line"></div>

        </div>


        <!-- RESEND -->

        <button
            type="button"
            class="email-resend-button"
            id="emailResendButton"
        >

            <svg
                class="email-resend-icon"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
            >

                <path
                    d="M20 11a8 8 0 1 0 2 5"
                />

                <path
                    d="M20 5v6h-6"
                />

            </svg>


            <span>

                Resend Code

            </span>

        </button>


        <!-- VERIFY -->

        <button
            type="button"
            class="email-verify-button"
            id="emailVerifyButton"
        >

            Verify Email

        </button>

    </div>

</div>


<!-- =========================================================
     REGISTRATION SUBMITTED MODAL
========================================================== -->

<div
    class="registration-submitted-modal"
    id="registrationSubmittedModal"
    aria-hidden="true"
>

    <div
        class="registration-submitted-card"
        role="dialog"
        aria-modal="true"
        aria-labelledby="registrationSubmittedTitle"
    >


        <!-- ICON -->

        <div class="registration-submitted-icon-wrapper">

            <img
                src="{{ asset('icons/login/registration-submitted.png') }}"
                alt="Registration submitted"
                class="registration-submitted-icon"
            >

        </div>


        <!-- TITLE -->

        <h2
            class="registration-submitted-title"
            id="registrationSubmittedTitle"
        >

            Registration Submitted

        </h2>


        <!-- DESCRIPTION -->

        <p class="registration-submitted-description">

            Your buyer account registration has been successfully submitted

        </p>


        <!-- APPROVAL NOTICE -->

        <div class="registration-submitted-notice">


            <div
                class="registration-submitted-check"
                aria-hidden="true"
            >

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.4"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >

                    <path
                        d="M5 12.5l4.2 4.2L19 7"
                    />

                </svg>

            </div>


            <div class="registration-submitted-notice-content">

                <h3 class="registration-submitted-notice-title">

                    Waiting for Administrator’s Approval

                </h3>


                <p class="registration-submitted-notice-text">

                    Please wait for the administrator’s approval.
                    You will receive an email once your account
                    has been reviewed.

                </p>

            </div>

        </div>


        <!-- OKAY -->

        <button
            type="button"
            class="registration-submitted-button"
            id="registrationSubmittedOkayButton"
        >

            Okay, Got it

        </button>

    </div>

</div>


<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {


        /* =========================================================
           DATA
        ========================================================== */

        const validIdUrl =
            @json($validIdUrl);


        const validIdName =
            @json(
                $validIdOriginalName
                    ?? (
                        $validIdPath
                            ? basename($validIdPath)
                            : null
                    )
            );


        /* =========================================================
           DOCUMENT MODAL
        ========================================================== */

        const validIdPreview =
            document.getElementById(
                'validIdPreview'
            );


        const documentModal =
            document.getElementById(
                'documentModal'
            );


        const modalTitle =
            document.getElementById(
                'modalTitle'
            );


        const modalBody =
            document.getElementById(
                'modalBody'
            );


        const modalClose =
            document.getElementById(
                'modalClose'
            );


        function openDocumentModal() {

            if (
                !validIdUrl ||
                !documentModal
            ) {

                return;

            }


            modalTitle.textContent =
                'Valid ID';


            modalBody.innerHTML =
                '';


            const image =
                document.createElement(
                    'img'
                );


            image.src =
                validIdUrl;


            image.alt =
                'Uploaded Valid ID';


            modalBody.appendChild(
                image
            );


            documentModal.classList.add(
                'active'
            );


            documentModal.setAttribute(
                'aria-hidden',
                'false'
            );


            document.body.style.overflow =
                'hidden';

        }


        function closeDocumentModal() {

            if (!documentModal) {

                return;

            }


            documentModal.classList.remove(
                'active'
            );


            documentModal.setAttribute(
                'aria-hidden',
                'true'
            );


            modalBody.innerHTML =

                `
                    <div class="modal-empty">
                        Select a document to preview.
                    </div>
                `;


            document.body.style.overflow =
                '';

        }


        if (validIdPreview) {

            validIdPreview.addEventListener(
                'click',
                function () {

                    openDocumentModal();

                }
            );


            validIdPreview.addEventListener(
                'keydown',
                function (event) {

                    if (
                        event.key === 'Enter' ||
                        event.key === ' '
                    ) {

                        event.preventDefault();

                        openDocumentModal();

                    }

                }
            );

        }


        if (modalClose) {

            modalClose.addEventListener(
                'click',
                closeDocumentModal
            );

        }


        if (documentModal) {

            documentModal.addEventListener(
                'click',
                function (event) {

                    if (
                        event.target ===
                        documentModal
                    ) {

                        closeDocumentModal();

                    }

                }
            );

        }


        /* =========================================================
           ELEMENTS
        ========================================================== */

        const buyerCompletionForm =
            document.getElementById(
                'buyerCompletionForm'
            );


        const continueButton =
            document.getElementById(
                'continueButton'
            );


        const emailVerificationModal =
            document.getElementById(
                'emailVerificationModal'
            );


        const emailCodeInputs =
            document.querySelectorAll(
                '.email-code-input'
            );


        const emailVerifyButton =
            document.getElementById(
                'emailVerifyButton'
            );


        const emailResendButton =
            document.getElementById(
                'emailResendButton'
            );


        const registrationSubmittedModal =
            document.getElementById(
                'registrationSubmittedModal'
            );


        const registrationSubmittedOkayButton =
            document.getElementById(
                'registrationSubmittedOkayButton'
            );


        /* =========================================================
           OPEN EMAIL VERIFICATION
        ========================================================== */

        function openEmailVerificationModal() {

            if (!emailVerificationModal) {

                return;

            }


            emailVerificationModal.classList.add(
                'active'
            );


            emailVerificationModal.setAttribute(
                'aria-hidden',
                'false'
            );


            document.body.style.overflow =
                'hidden';


            if (
                emailCodeInputs.length > 0
            ) {

                setTimeout(
                    function () {

                        emailCodeInputs[0].focus();

                    },
                    200
                );

            }

        }


        /* =========================================================
           CLOSE EMAIL VERIFICATION
        ========================================================== */

        function closeEmailVerificationModal() {

            if (!emailVerificationModal) {

                return;

            }


            emailVerificationModal.classList.remove(
                'active'
            );


            emailVerificationModal.setAttribute(
                'aria-hidden',
                'true'
            );


            document.body.style.overflow =
                '';

        }


        /* =========================================================
           OPEN REGISTRATION SUBMITTED
        ========================================================== */

        function openRegistrationSubmittedModal() {

            if (!registrationSubmittedModal) {

                return;

            }


            if (emailVerificationModal) {

                emailVerificationModal.classList.remove(
                    'active'
                );


                emailVerificationModal.setAttribute(
                    'aria-hidden',
                    'true'
                );

            }


            registrationSubmittedModal.classList.add(
                'active'
            );


            registrationSubmittedModal.setAttribute(
                'aria-hidden',
                'false'
            );


            document.body.style.overflow =
                'hidden';

        }


        /* =========================================================
           FORM SUBMISSION
        ========================================================== */

        if (buyerCompletionForm) {

            buyerCompletionForm.addEventListener(
                'submit',
                function (event) {

                    /*
                     * Keep buyer on the review page
                     * while showing email verification.
                     */

                    event.preventDefault();


                    if (continueButton) {

                        continueButton.disabled =
                            true;

                        continueButton.textContent =
                            'Submitting...';

                    }


                    openEmailVerificationModal();

                }
            );

        }


        /* =========================================================
           CODE INPUTS
        ========================================================== */

        emailCodeInputs.forEach(
            function (input, index) {


                input.addEventListener(
                    'input',
                    function () {

                        this.value =
                            this.value.replace(
                                /[^0-9]/g,
                                ''
                            );


                        if (
                            this.value &&
                            index <
                            emailCodeInputs.length - 1
                        ) {

                            emailCodeInputs[
                                index + 1
                            ].focus();

                        }

                    }
                );


                input.addEventListener(
                    'keydown',
                    function (event) {

                        if (
                            event.key ===
                                'Backspace' &&
                            !this.value &&
                            index > 0
                        ) {

                            emailCodeInputs[
                                index - 1
                            ].focus();

                        }

                    }
                );


                input.addEventListener(
                    'paste',
                    function (event) {

                        event.preventDefault();


                        const pastedData =
                            event.clipboardData
                                .getData('text')
                                .replace(
                                    /[^0-9]/g,
                                    ''
                                )
                                .slice(
                                    0,
                                    6
                                );


                        if (!pastedData) {

                            return;

                        }


                        pastedData
                            .split('')
                            .forEach(
                                function (
                                    digit,
                                    digitIndex
                                ) {

                                    if (
                                        emailCodeInputs[
                                            digitIndex
                                        ]
                                    ) {

                                        emailCodeInputs[
                                            digitIndex
                                        ].value =
                                            digit;

                                    }

                                }
                            );


                        const nextIndex =
                            Math.min(
                                pastedData.length,
                                emailCodeInputs.length - 1
                            );


                        emailCodeInputs[
                            nextIndex
                        ].focus();

                    }
                );

            }
        );


        /* =========================================================
           VERIFY EMAIL
        ========================================================== */

        if (emailVerifyButton) {

            emailVerifyButton.addEventListener(
                'click',
                function () {


                    let verificationCode =
                        '';


                    emailCodeInputs.forEach(
                        function (input) {

                            verificationCode +=
                                input.value;

                        }
                    );


                    if (
                        verificationCode.length !==
                        6
                    ) {

                        emailCodeInputs[0].focus();

                        return;

                    }


                    /*
                     * Connect your real backend
                     * verification here later.
                     *
                     * For now:
                     * Email Verification
                     * ->
                     * Registration Submitted
                     */

                    console.log(
                        'Verification code:',
                        verificationCode
                    );


                    openRegistrationSubmittedModal();

                }
            );

        }


        /* =========================================================
           RESEND CODE
        ========================================================== */

        if (emailResendButton) {

            emailResendButton.addEventListener(
                'click',
                function () {

                    console.log(
                        'Resend verification code'
                    );

                }
            );

        }


        /* =========================================================
           CLICK OUTSIDE EMAIL MODAL
        ========================================================== */

        if (emailVerificationModal) {

            emailVerificationModal.addEventListener(
                'click',
                function (event) {

                    if (
                        event.target ===
                        emailVerificationModal
                    ) {

                        closeEmailVerificationModal();

                    }

                }
            );

        }


        /* =========================================================
           OKAY GOT IT
           -> LOGIN
        ========================================================== */

        if (
            registrationSubmittedOkayButton
        ) {

            registrationSubmittedOkayButton.addEventListener(
                'click',
                function () {

                    window.location.href =
                        "{{ route('login') }}";

                }
            );

        }


        /* =========================================================
           ESCAPE
        ========================================================== */

        document.addEventListener(
            'keydown',
            function (event) {

                if (
                    event.key === 'Escape'
                ) {


                    if (
                        documentModal &&
                        documentModal.classList.contains(
                            'active'
                        )
                    ) {

                        closeDocumentModal();

                        return;

                    }


                    if (
                        emailVerificationModal &&
                        emailVerificationModal.classList.contains(
                            'active'
                        )
                    ) {

                        closeEmailVerificationModal();

                        return;

                    }

                }

            }
        );

    }
);

</script>


</body>

</html>