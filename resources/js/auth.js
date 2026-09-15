function showView(view) {

    const loginView =
        document.getElementById('loginView');

    const signupView =
        document.getElementById('signupView');


    if (loginView) {

        loginView.classList.toggle(
            'active',
            view === 'login'
        );

    }


    if (signupView) {

        signupView.classList.toggle(
            'active',
            view === 'signup'
        );

    }
}


/* =========================================================
   PASSWORD TOGGLE
========================================================= */

function togglePass(inputId, btnId) {

    const input =
        document.getElementById(inputId);

    const btn =
        document.getElementById(btnId);

    const icon =
        document.getElementById('loginEyeIcon');


    if (!input || !btn || !icon) {
        return;
    }


    const isHidden =
        input.type === 'password';


    if (isHidden) {

        /* Show password */
        input.type =
            'text';


        /* show-password.png = password visible */
        icon.src =
            '/icons/login/show-password.png';

        icon.alt =
            'Password visible';


        btn.setAttribute(
            'aria-label',
            'Hide password'
        );


        btn.setAttribute(
            'aria-pressed',
            'true'
        );

    } else {

        /* Hide password */
        input.type =
            'password';


        /* hide-password.png = password hidden */
        icon.src =
            '/icons/login/hide-password.png';

        icon.alt =
            'Password hidden';


        btn.setAttribute(
            'aria-label',
            'Show password'
        );


        btn.setAttribute(
            'aria-pressed',
            'false'
        );

    }
}


/* =========================================================
   PASSWORD INPUT
========================================================= */

function onPassInput(inputId, btnId) {

    const input =
        document.getElementById(inputId);

    const btn =
        document.getElementById(btnId);

    const icon =
        document.getElementById('loginEyeIcon');


    if (!input || !btn || !icon) {
        return;
    }


    /* =====================================================
       PASSWORD HAS TEXT
    ====================================================== */

    if (input.value.length > 0) {

        btn.classList.add(
            'show'
        );


        /*
         * Password is hidden by default.
         * Therefore use hide-password.png.
         */
        if (input.type === 'password') {

            icon.src =
                '/icons/login/hide-password.png';

            icon.alt =
                'Password hidden';


            btn.setAttribute(
                'aria-label',
                'Show password'
            );


            btn.setAttribute(
                'aria-pressed',
                'false'
            );

        }

    }


    /* =====================================================
       PASSWORD EMPTY
    ====================================================== */

    else {

        input.type =
            'password';


        btn.classList.remove(
            'show'
        );


        icon.src =
            '/icons/login/hide-password.png';

        icon.alt =
            'Password hidden';


        btn.setAttribute(
            'aria-label',
            'Show password'
        );


        btn.setAttribute(
            'aria-pressed',
            'false'
        );

    }
}


/* =========================================================
   ROLE SELECT
========================================================= */

function selectRole(role) {

    const sellerCard =
        document.getElementById('roleSeller');

    const buyerCard =
        document.getElementById('roleBuyer');

    const roleInput =
        document.getElementById('roleInput');


    if (sellerCard) {

        sellerCard.classList.toggle(
            'selected',
            role === 'seller'
        );

    }


    if (buyerCard) {

        buyerCard.classList.toggle(
            'selected',
            role === 'buyer'
        );

    }


    if (roleInput) {

        roleInput.value =
            role;

    }
}


/* =========================================================
   MAKE FUNCTIONS AVAILABLE TO INLINE HTML EVENTS
========================================================= */

window.showView =
    showView;

window.togglePass =
    togglePass;

window.onPassInput =
    onPassInput;

window.selectRole =
    selectRole;


/* =========================================================
   DOM READY
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    function () {


        /* =====================================================
           ROLE CARDS
        ====================================================== */

        const roleCards =
            document.querySelectorAll(
                '.role-card'
            );


        roleCards.forEach(
            function (card) {

                card.addEventListener(
                    'click',
                    function () {

                        selectRole(
                            card.dataset.role
                        );

                    }
                );

            }
        );


        /* =====================================================
           INITIAL ROLE
        ====================================================== */

        const roleInput =
            document.getElementById(
                'roleInput'
            );


        const initialRole =
            roleInput?.value ||
            'seller';


        selectRole(
            initialRole
        );


        /* =====================================================
           INITIAL PASSWORD STATE
        ====================================================== */

        const passwordInput =
            document.getElementById(
                'loginPass'
            );

        const passwordButton =
            document.getElementById(
                'loginEyeBtn'
            );

        const passwordIcon =
            document.getElementById(
                'loginEyeIcon'
            );


        if (
            passwordInput &&
            passwordButton &&
            passwordIcon
        ) {

            /*
             * Password starts hidden
             */
            passwordInput.type =
                'password';


            /*
             * Hidden password uses
             * hide-password.png
             */
            passwordIcon.src =
                '/icons/login/hide-password.png';

            passwordIcon.alt =
                'Password hidden';


            passwordButton.setAttribute(
                'aria-label',
                'Show password'
            );


            passwordButton.setAttribute(
                'aria-pressed',
                'false'
            );


            /*
             * Show button when password
             * already contains text.
             */
            if (
                passwordInput.value.length > 0
            ) {

                passwordButton.classList.add(
                    'show'
                );

            } else {

                passwordButton.classList.remove(
                    'show'
                );

            }

        }

    }
);