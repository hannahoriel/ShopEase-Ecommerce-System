<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>ShopEase - Review Documents</title>


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
                3px 0 0;

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
                18px auto 18px;

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
                white;

            font-weight: 600;
        }


        .step-label {
            margin-top: 6px;

            font-size: 14px;

            font-weight: 400;

            color:
                #969696;

            white-space:
                nowrap;
        }


        .step.active .step-label {
            color:
                var(--maroon-dark);

            font-weight: 500;
        }


        /* =========================================================
           CONTENT
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
                0 0 20px;

            font-size:
                20px;

            font-weight:
                600;

            color:
                var(--text-dark);
        }


        /* =========================================================
           INFORMATION SECTIONS
        ========================================================== */

        .review-section {
            width: 100%;

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
                14px 18px;

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
                5px 10px;

            border-radius:
                20px;
        }


        .review-section-body {
            padding:
                20px;
        }


        /* =========================================================
           INFORMATION GRID
           ARRANGED LIKE BUYER REGISTRATION
        ========================================================== */

        .information-grid {
            display:
                grid;

            grid-template-columns:
                repeat(12, minmax(0, 1fr));

            gap:
                18px 22px;
        }


        .information-item {
            min-width:
                0;

            grid-column:
                span 4;
        }


        /*
        |------------------------------------------------------------------
        | ADDRESS ITEMS
        | 4-column arrangement:
        | Province | Municipality | Barangay | ZIP Code
        |------------------------------------------------------------------
        */

        .information-item.address-item {
            grid-column:
                span 3;
        }


        /*
        |------------------------------------------------------------------
        | FULL WIDTH
        |------------------------------------------------------------------
        */

        .information-item.full-width {
    grid-column: 1 / -1;
}

.information-item.business-name-field {
    grid-column: span 6;
}


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


        .information-value {
            min-height:
                38px;

            padding:
                8px 11px;

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
           CATEGORIES
        ========================================================== */

        .categories-list {
            display:
                flex;

            flex-wrap:
                wrap;

            gap:
                8px;
        }


        .category-badge {
            display:
                inline-flex;

            align-items:
                center;

            padding:
                7px 12px;

            border-radius:
                20px;

            background:
                var(--peach-soft);

            border:
                1px solid
                var(--peach-light);

            color:
                var(--maroon);

            font-size:
                12px;

            font-weight:
                500;
        }


        /* =========================================================
           DOCUMENT GRID
        ========================================================== */

        .documents-grid {
            display:
                grid;

            grid-template-columns:
                490px
                335px;

            gap:
                62px;

            align-items:
                start;

            margin-top:
                10px;
        }


        .document-section {
            min-width:
                0;
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


        /* =========================================================
           DOCUMENT PREVIEW
        ========================================================== */

        .document-preview {
            width:
                100%;

            height:
                228px;

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


        .permit-preview {
            height:
                385px;
        }


        /* =========================================================
           ACTUAL IMAGE
        ========================================================== */

        .document-image {
            width:
                100%;

            height:
                100%;

            object-fit:
                contain;

            padding:
                12px;

            background:
                #FFFFFF;
        }


        /* =========================================================
           EMPTY DOCUMENT
        ========================================================== */

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


        /* =========================================================
           PDF PREVIEW
        ========================================================== */

        .pdf-preview-content {
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
        }


        .pdf-preview-content svg {
            width:
                60px;

            height:
                60px;

            margin-bottom:
                10px;
        }


        .pdf-preview-content p {
            margin:
                0;

            color:
                var(--maroon);

            font-size:
                14px;

            font-weight:
                500;
        }


        .pdf-preview-content small {
            margin-top:
                4px;

            color:
                #B6B6B6;

            font-size:
                11px;
        }


        /* =========================================================
           FILE NAME
        ========================================================== */

        .document-file-name {
            margin-top:
                8px;

            font-size:
                12px;

            color:
                var(--maroon);

            font-weight:
                500;

            white-space:
                nowrap;

            overflow:
                hidden;

            text-overflow:
                ellipsis;

            max-width:
                100%;
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
                white;

            border-radius:
                16px;

            overflow:
                hidden;

            box-shadow:
                0 25px 70px
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
                16px 20px;

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
                0 4px 18px
                rgba(
                    0,
                    0,
                    0,
                    0.08
                );
        }


        .modal-body iframe {
            width:
                100%;

            height:
                100%;

            min-height:
                600px;

            border:
                none;

            border-radius:
                6px;

            background:
                white;
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
                0 20px 55px
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
                0 0 0 3px
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
                0 5px 14px
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
                    22,
                    12,
                    12,
                    0.55
                );

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
                10001;
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
                0 20px 55px
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

            box-sizing:
                border-box;
        }


        .registration-submitted-modal.active
        .registration-submitted-card {
            transform:
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

            line-height:
                1.2;

            font-weight:
                700;

            color:
                #000000;
        }


        .registration-submitted-description {
            margin:
                8px
                auto
                28px;

            max-width:
                390px;

            font-family:
                'Poppins',
                sans-serif;

            font-size:
                16px;

            line-height:
                1.4;

            font-weight:
                400;

            color:
                #919191;
        }


        .registration-submitted-notice {
            width:
                100%;

            padding:
                14px
                15px;

            margin-bottom:
                24px;

            border-radius:
                11px;

            background:
                #FFE8E4;

            display:
                flex;

            align-items:
                flex-start;

            gap:
                11px;

            text-align:
                left;

            box-sizing:
                border-box;
        }


        .registration-submitted-check {
            width:
                38px;

            height:
                38px;

            flex:
                0
                0
                38px;

            border-radius:
                50%;

            background:
                #76080F;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            margin-top:
                1px;
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

            fill:
                none;

            stroke-linecap:
                round;

            stroke-linejoin:
                round;
        }


        .registration-submitted-notice-content {
            flex:
                1;

            min-width:
                0;
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

            line-height:
                1.35;

            font-weight:
                700;

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
                13px;

            line-height:
                1.5;

            font-weight:
                400;

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

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            transition:
                background 0.2s ease,
                transform 0.1s ease,
                box-shadow 0.2s ease;
        }


        .registration-submitted-button:hover {
            background:
                #8F211F;

            box-shadow:
                0 5px 14px
                rgba(
                    123,
                    27,
                    27,
                    0.18
                );
        }


        .registration-submitted-button:active {
            transform:
                scale(0.98);

            box-shadow:
                none;
        }


        /* =========================================================
           RESPONSIVE
        ========================================================== */

        @media (max-width: 1200px) {

            /*
             * Buyer-style 2-column layout
             */

            .information-grid {
                grid-template-columns:
                    repeat(12, minmax(0, 1fr));
            }


            .information-item {
                grid-column:
                    span 6;
            }


            .information-item.address-item {
                grid-column:
                    span 6;
            }


            .information-item.full-width {
                grid-column:
                    1 / -1;
            }


            .documents-grid {
                grid-template-columns:
                    minmax(0, 1fr)
                    minmax(0, 0.8fr);

                gap:
                    35px;
            }

        }


        @media (max-width: 900px) {

            .documents-grid {
                grid-template-columns:
                    1fr;
            }


            .document-preview {
                height:
                    300px;
            }


            .permit-preview {
                height:
                    380px;
            }


            .registration-card {
                padding-bottom:
                    110px;
            }


            .information-item,
            .information-item.address-item {
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


            .document-label {
                font-size:
                    16px;
            }


            .documents-grid {
                gap:
                    25px;
            }


            .document-preview {
                height:
                    260px;
            }


            .permit-preview {
                height:
                    330px;
            }


            .document-note {
                margin-top:
                    25px;

                font-size:
                    13px;

                line-height:
                    1.6;
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


            .email-verification-card,
            .registration-submitted-card {
                max-width:
                    400px;

                padding:
                    32px
                    22px
                    25px;

                border-radius:
                    20px;
            }


            .email-verification-icon-wrapper,
            .registration-submitted-icon-wrapper {
                width:
                    75px;

                height:
                    75px;

                margin-bottom:
                    17px;
            }


            .email-verification-icon,
            .registration-submitted-icon {
                width:
                    42px;

                height:
                    42px;
            }


            .email-verification-title,
            .registration-submitted-title {
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


            .registration-submitted-description {
                font-size:
                    14px;

                margin:
                    7px
                    auto
                    24px;
            }


            .registration-submitted-notice {
                gap:
                    9px;

                padding:
                    12px;

                margin-bottom:
                    22px;

                border-radius:
                    10px;
            }


            .registration-submitted-check {
                width:
                    34px;

                height:
                    34px;

                flex-basis:
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
                    12px;

                line-height:
                    1.5;
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

        <div
            class="step active"
            id="stepThreeCurrent"
        >

            <div class="step-number">
                3
            </div>


            <div class="step-label">
                Review Documents
            </div>

        </div>

    </div>


    <!-- =========================================================
         MAIN CONTENT
    ========================================================== -->

    <main class="registration-content">

        <form
            id="completionForm"
            method="POST"
            action="{{ route('seller.register.complete') }}"
            enctype="multipart/form-data"
        >

            @csrf


            <div class="registration-card">


                <h2 class="review-title">
                    Review your information and documents
                </h2>


                <!-- =================================================
                     SELLER INFORMATION
                ================================================== -->

                <div class="review-section">

                    <div class="review-section-header">

                        <h3 class="review-section-title">
                            Seller Information
                        </h3>

                        <span class="review-section-badge">
                            Completed
                        </span>

                    </div>


                    <div class="review-section-body">

                        <div class="information-grid">


                            <!-- LAST NAME -->

                            <div class="information-item">

                                <span class="information-label">
                                    Last Name
                                </span>

                                <div class="information-value
                                    {{ empty($sellerData['last_name'] ?? null) ? 'empty' : '' }}"
                                >

                                    {{ $sellerData['last_name'] ?? 'Not provided' }}

                                </div>

                            </div>


                            <!-- FIRST NAME -->

                            <div class="information-item">

                                <span class="information-label">
                                    First Name
                                </span>

                                <div class="information-value
                                    {{ empty($sellerData['first_name'] ?? null) ? 'empty' : '' }}"
                                >

                                    {{ $sellerData['first_name'] ?? 'Not provided' }}

                                </div>

                            </div>


                            <!-- MIDDLE NAME -->

                            <div class="information-item">

                                <span class="information-label">
                                    Middle Name
                                </span>

                                <div class="information-value
                                    {{ empty($sellerData['middle_name'] ?? null) ? 'empty' : '' }}"
                                >

                                    {{ $sellerData['middle_name'] ?? 'Not provided' }}

                                </div>

                            </div>


                            <!-- SEX -->

                            <div class="information-item">

                                <span class="information-label">
                                    Sex
                                </span>

                                <div class="information-value
                                    {{ empty($sellerData['sex'] ?? null) ? 'empty' : '' }}"
                                >

                                    {{ $sellerData['sex'] ?? 'Not provided' }}

                                </div>

                            </div>


                            <!-- EMAIL -->

                            <div class="information-item">

                                <span class="information-label">
                                    Email Address
                                </span>

                                <div class="information-value
                                    {{ empty($sellerData['email'] ?? null) ? 'empty' : '' }}"
                                >

                                    {{ $sellerData['email'] ?? 'Not provided' }}

                                </div>

                            </div>


                            <!-- CONTACT -->

                            <div class="information-item">

                                <span class="information-label">
                                    Contact Number
                                </span>

                                <div class="information-value
                                    {{ empty($sellerData['contact_no'] ?? null) ? 'empty' : '' }}"
                                >

                                    {{ $sellerData['contact_no'] ?? 'Not provided' }}

                                </div>

                            </div>


                            <!-- BIRTHDAY -->

                            <div class="information-item">

                                <span class="information-label">
                                    Birthday
                                </span>

                                <div class="information-value
                                    {{ empty($sellerData['birthday'] ?? null) ? 'empty' : '' }}"
                                >

                                    {{ $sellerData['birthday'] ?? 'Not provided' }}

                                </div>

                            </div>


                            <!-- AGE -->

                            <div class="information-item">

                                <span class="information-label">
                                    Age
                                </span>

                                <div class="information-value
                                    {{ empty($sellerData['age'] ?? null) ? 'empty' : '' }}"
                                >

                                    {{ $sellerData['age'] ?? 'Not provided' }}

                                </div>

                            </div>


                            <!-- PROVINCE -->

                            <div class="information-item address-item">

                                <span class="information-label">
                                    Province
                                </span>

                                <div class="information-value
                                    {{ empty($sellerData['province'] ?? null) ? 'empty' : '' }}"
                                >

                                    {{ $sellerData['province'] ?? 'Not provided' }}

                                </div>

                            </div>


                            <!-- MUNICIPALITY -->

                            <div class="information-item address-item">

                                <span class="information-label">
                                    Municipality
                                </span>

                                <div class="information-value
                                    {{ empty($sellerData['municipality'] ?? null) ? 'empty' : '' }}"
                                >

                                    {{ $sellerData['municipality'] ?? 'Not provided' }}

                                </div>

                            </div>


                            <!-- BARANGAY -->

                            <div class="information-item address-item">

                                <span class="information-label">
                                    Barangay
                                </span>

                                <div class="information-value
                                    {{ empty($sellerData['barangay'] ?? null) ? 'empty' : '' }}"
                                >

                                    {{ $sellerData['barangay'] ?? 'Not provided' }}

                                </div>

                            </div>


                            <!-- ZIP -->

                            <div class="information-item address-item">

                                <span class="information-label">
                                    ZIP Code
                                </span>

                                <div class="information-value
                                    {{ empty($sellerData['zip_code'] ?? null) ? 'empty' : '' }}"
                                >

                                    {{ $sellerData['zip_code'] ?? 'Not provided' }}

                                </div>

                            </div>


                            <!-- STREET -->

                            <div class="information-item address-item">

                                <span class="information-label">
                                    Street
                                </span>

                                <div class="information-value
                                    {{ empty($sellerData['street'] ?? null) ? 'empty' : '' }}"
                                >

                                    {{ $sellerData['street'] ?? 'Not provided' }}

                                </div>

                            </div>


                            <!-- HOUSE NUMBER -->

                            <div class="information-item address-item">

                                <span class="information-label">
                                    House No.
                                </span>

                                <div class="information-value
                                    {{ empty($sellerData['house_no'] ?? null) ? 'empty' : '' }}"
                                >

                                    {{ $sellerData['house_no'] ?? 'Not provided' }}

                                </div>

                            </div>


                            <!-- BUILDING -->

                            <div class="information-item address-item">

                                <span class="information-label">
                                    Building
                                </span>

                                <div class="information-value
                                    {{ empty($sellerData['building'] ?? null) ? 'empty' : '' }}"
                                >

                                    {{ $sellerData['building'] ?? 'Not provided' }}

                                </div>

                            </div>


                            <!-- SUBDIVISION -->

                            <div class="information-item address-item">

                                <span class="information-label">
                                    Subdivision
                                </span>

                                <div class="information-value
                                    {{ empty($sellerData['subdivision'] ?? null) ? 'empty' : '' }}"
                                >

                                    {{ $sellerData['subdivision'] ?? 'Not provided' }}

                                </div>

                            </div>


                        </div>

                    </div>

                </div>


                <!-- =================================================
                     BUSINESS INFORMATION
                ================================================== -->

                <div class="review-section">

                    <div class="review-section-header">

                        <h3 class="review-section-title">
                            Business Information
                        </h3>

                        <span class="review-section-badge">
                            Completed
                        </span>

                    </div>


                    <div class="review-section-body">

                        <div class="information-grid">


                            <!-- BUSINESS NAME -->

                            <div class="information-item business-name-field">

                                <span class="information-label">
                                    Business Name
                                </span>

                                <div class="information-value
                                    {{ empty($sellerData['business_name'] ?? null) ? 'empty' : '' }}"
                                >

                                    {{ $sellerData['business_name'] ?? 'Not provided' }}

                                </div>

                            </div>


                            <!-- CATEGORIES -->

                            <div class="information-item full-width">

                                <span class="information-label">
                                    Business Categories
                                </span>


                                @php

                                    $selectedCategories =
                                        $sellerData['categories'] ?? [];

                                    if (!is_array($selectedCategories)) {

                                        $selectedCategories = [
                                            $selectedCategories
                                        ];

                                    }

                                @endphp


                                @if(count($selectedCategories) > 0)

                                    <div class="categories-list">

                                        @foreach($selectedCategories as $category)

                                            <span class="category-badge">

                                                {{ $category }}

                                            </span>

                                        @endforeach

                                    </div>

                                @else

                                    <div class="information-value empty">

                                        No categories selected

                                    </div>

                                @endif

                            </div>


                        </div>

                    </div>

                </div>


                <!-- =================================================
                     DOCUMENTS
                ================================================== -->

                @php

                    $validIdPath =
                        is_array($sellerData ?? null)
                            ? ($sellerData['valid_id_path'] ?? null)
                            : null;


                    $validIdUrl =
                        $validIdPath
                            ? \Illuminate\Support\Facades\Storage::url($validIdPath)
                            : null;


                    $validIdOriginalName =
                        is_array($sellerData ?? null)
                            ? ($sellerData['valid_id_original_name'] ?? null)
                            : null;


                    $businessPermitPath =
                        is_array($sellerData ?? null)
                            ? ($sellerData['business_permit_path'] ?? null)
                            : null;


                    $businessPermitUrl =
                        $businessPermitPath
                            ? \Illuminate\Support\Facades\Storage::url($businessPermitPath)
                            : null;


                    $businessPermitOriginalName =
                        is_array($sellerData ?? null)
                            ? ($sellerData['business_permit_original_name'] ?? null)
                            : null;


                    $businessPermitExtension =
                        $businessPermitPath
                            ? strtolower(
                                pathinfo(
                                    $businessPermitPath,
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


                        <div class="documents-grid">


                            <!-- VALID ID -->

                            <div class="document-section">

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

                                        @php

                                            $validIdExtension =
                                                strtolower(
                                                    pathinfo(
                                                        $validIdPath,
                                                        PATHINFO_EXTENSION
                                                    )
                                                );

                                        @endphp


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

                                        {{ $validIdOriginalName ?? basename($validIdPath) }}

                                    </div>

                                @endif

                            </div>


                            <!-- BUSINESS PERMIT -->

                            <div class="document-section">

                                <label class="document-label">
                                    Business Permit
                                </label>


                                <div
                                    class="document-preview permit-preview"
                                    id="businessPermitPreview"
                                    role="button"
                                    tabindex="0"
                                    aria-label="View uploaded Business Permit"
                                >


                                    @if($businessPermitPath)

                                        @if(
                                            in_array(
                                                $businessPermitExtension,
                                                [
                                                    'jpg',
                                                    'jpeg',
                                                    'png',
                                                    'webp'
                                                ]
                                            )
                                        )

                                            <img
                                                src="{{ $businessPermitUrl }}"
                                                alt="Uploaded Business Permit"
                                                class="document-image"
                                            >

                                        @elseif(
                                            $businessPermitExtension === 'pdf'
                                        )

                                            <div
                                                class="pdf-preview-content"
                                            >

                                                <svg
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="#7B1B1B"
                                                    stroke-width="1.6"
                                                >

                                                    <path
                                                        d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2-2V8z"
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


                                                <p>
                                                    PDF Document
                                                </p>


                                                <small>
                                                    Click to view
                                                </small>

                                            </div>


                                        @else

                                            <div
                                                class="document-placeholder"
                                            >

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
                                            id="businessPermitPlaceholder"
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
                                                Business Permit not uploaded
                                            </p>


                                            <small>
                                                Please return to Step 2
                                            </small>

                                        </div>

                                    @endif

                                </div>


                                @if($businessPermitPath)

                                    <div class="document-file-name">

                                        {{ $businessPermitOriginalName ?? basename($businessPermitPath) }}

                                    </div>

                                @endif

                            </div>


                        </div>


                        <div class="document-note">

                            <strong>
                                NOTE:
                            </strong>

                            Uploaded documents will only be used to verify
                            your seller registration and will be accessible
                            to authorized administrators for review.

                        </div>

                    </div>

                </div>


                <!-- =================================================
                     SUBMIT
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
     DOCUMENT MODAL
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

        <div class="email-verification-icon-wrapper">

            <img
                src="{{ asset('icons/login/email-verify.png') }}"
                alt="Email verification"
                class="email-verification-icon"
            >

        </div>


        <h2
            class="email-verification-title"
            id="emailVerificationTitle"
        >
            Verify Your Email
        </h2>


        <p class="email-verification-subtitle">
            We’ve sent a 6-digit code to your email.
        </p>


        <div class="email-code-container">

            @for($i = 1; $i <= 6; $i++)

                <input
                    type="text"
                    maxlength="1"
                    inputmode="numeric"
                    autocomplete="{{ $i === 1 ? 'one-time-code' : 'off' }}"
                    class="email-code-input"
                    aria-label="Digit {{ $i }}"
                >

            @endfor

        </div>


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


        <div class="email-divider">

            <div class="email-divider-line"></div>

            <span class="email-divider-text">
                or
            </span>

            <div class="email-divider-line"></div>

        </div>


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


        <div class="registration-submitted-icon-wrapper">

            <img
                src="{{ asset('icons/login/registration-submitted.png') }}"
                alt="Registration submitted"
                class="registration-submitted-icon"
            >

        </div>


        <h2
            class="registration-submitted-title"
            id="registrationSubmittedTitle"
        >
            Registration Submitted
        </h2>


        <p class="registration-submitted-description">

            Your seller account registration has been successfully submitted

        </p>


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
           SERVER-PROVIDED DOCUMENT DATA
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


        const businessPermitUrl =
            @json($businessPermitUrl);


        const businessPermitName =
            @json(
                $businessPermitOriginalName
                    ?? (
                        $businessPermitPath
                            ? basename($businessPermitPath)
                            : null
                    )
            );


        const businessPermitExtension =
            @json($businessPermitExtension);


        /* =========================================================
           ELEMENTS
        ========================================================== */

        const validIdPreview =
            document.getElementById(
                'validIdPreview'
            );


        const businessPermitPreview =
            document.getElementById(
                'businessPermitPreview'
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


        const completionForm =
            document.getElementById(
                'completionForm'
            );


        const continueButton =
            document.getElementById(
                'continueButton'
            );


        /* =========================================================
           CHECK PDF
        ========================================================== */

        function isPdf(
            extension
        ) {

            return extension === 'pdf';

        }


        /* =========================================================
           OPEN DOCUMENT MODAL
        ========================================================== */

        function openModal(
            url,
            title,
            extension
        ) {

            if (!url) {
                return;
            }


            modalTitle.textContent =
                title;


            modalBody.innerHTML =
                '';


            if (
                isPdf(
                    extension
                )
            ) {

                const iframe =
                    document.createElement(
                        'iframe'
                    );


                iframe.src =
                    url;


                iframe.title =
                    title;


                modalBody.appendChild(
                    iframe
                );

            } else {

                const image =
                    document.createElement(
                        'img'
                    );


                image.src =
                    url;


                image.alt =
                    title;


                modalBody.appendChild(
                    image
                );

            }


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


        /* =========================================================
           CLOSE DOCUMENT MODAL
        ========================================================== */

        function closeModal() {

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


        /* =========================================================
           VALID ID
        ========================================================== */

        if (validIdPreview) {

            validIdPreview.addEventListener(
                'click',
                function () {

                    if (!validIdUrl) {
                        return;
                    }


                    const extension =
                        validIdName
                            ? validIdName
                                .split('.')
                                .pop()
                                .toLowerCase()
                            : null;


                    openModal(
                        validIdUrl,
                        'Valid ID',
                        extension
                    );

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


                        if (!validIdUrl) {
                            return;
                        }


                        const extension =
                            validIdName
                                ? validIdName
                                    .split('.')
                                    .pop()
                                    .toLowerCase()
                                : null;


                        openModal(
                            validIdUrl,
                            'Valid ID',
                            extension
                        );

                    }

                }
            );

        }


        /* =========================================================
           BUSINESS PERMIT
        ========================================================== */

        if (businessPermitPreview) {

            businessPermitPreview.addEventListener(
                'click',
                function () {

                    if (!businessPermitUrl) {
                        return;
                    }


                    const extension =
                        businessPermitExtension
                        ||
                        (
                            businessPermitName
                                ? businessPermitName
                                    .split('.')
                                    .pop()
                                    .toLowerCase()
                                : null
                        );


                    openModal(
                        businessPermitUrl,
                        'Business Permit',
                        extension
                    );

                }
            );


            businessPermitPreview.addEventListener(
                'keydown',
                function (event) {

                    if (
                        event.key === 'Enter' ||
                        event.key === ' '
                    ) {

                        event.preventDefault();


                        if (!businessPermitUrl) {
                            return;
                        }


                        const extension =
                            businessPermitExtension
                            ||
                            (
                                businessPermitName
                                    ? businessPermitName
                                        .split('.')
                                        .pop()
                                        .toLowerCase()
                                    : null
                            );


                        openModal(
                            businessPermitUrl,
                            'Business Permit',
                            extension
                        );

                    }

                }
            );

        }


        /* =========================================================
           CLOSE DOCUMENT MODAL
        ========================================================== */

        if (modalClose) {

            modalClose.addEventListener(
                'click',
                closeModal
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

                        closeModal();

                    }

                }
            );

        }


        document.addEventListener(
            'keydown',
            function (event) {

                if (
                    event.key === 'Escape' &&
                    documentModal &&
                    documentModal.classList.contains(
                        'active'
                    )
                ) {

                    closeModal();

                }

            }
        );


        /* =========================================================
           FINAL SUBMISSION
        ========================================================== */

        if (completionForm) {

            completionForm.addEventListener(
                'submit',
                function () {

                    if (continueButton) {

                        continueButton.disabled =
                            true;

                        continueButton.textContent =
                            'Submitting...';

                    }

                }
            );

        }

    }
);


/* =========================================================
   EMAIL VERIFICATION
========================================================= */

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


/* =========================================================
   REGISTRATION SUBMITTED
========================================================= */

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
========================================================= */

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
========================================================= */

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
   SUBMIT → EMAIL VERIFICATION
========================================================= */

if (completionForm) {

    completionForm.addEventListener(
        'submit',
        function (event) {

            event.preventDefault();

            openEmailVerificationModal();

        }
    );

}


/* =========================================================
   CODE INPUT BEHAVIOR
========================================================= */

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
                    event.key === 'Backspace' &&
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
========================================================= */

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
                verificationCode.length !== 6
            ) {

                emailCodeInputs[0].focus();

                return;

            }


            /*
             * UI FLOW:
             *
             * Email Verification
             *          ↓
             * Registration Submitted
             */

            if (emailVerificationModal) {

                emailVerificationModal.classList.remove(
                    'active'
                );


                emailVerificationModal.setAttribute(
                    'aria-hidden',
                    'true'
                );

            }


            if (registrationSubmittedModal) {

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

        }
    );

}


/* =========================================================
   RESEND CODE
========================================================= */

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
   OKAY GOT IT → LOGIN
========================================================= */

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
   EMAIL MODAL OUTSIDE CLICK
========================================================= */

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
   ESCAPE
========================================================= */

document.addEventListener(
    'keydown',
    function (event) {

        if (
            event.key === 'Escape' &&
            emailVerificationModal &&
            emailVerificationModal.classList.contains(
                'active'
            )
        ) {

            closeEmailVerificationModal();

        }

    }
);

</script>


</body>

</html>