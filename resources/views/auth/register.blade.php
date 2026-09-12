@extends('layouts.auth')

@section('title', 'Sign Up')

@section('content')
<div class="view active" id="signupView">

    <h1>Create Your Account</h1>

    <p class="subhead">
        Join ShopEase and start your journey today.
    </p>

    @if ($errors->any())
        <div class="field-group">
            @foreach ($errors->all() as $error)
                <p style="color:#b02020; font-size:13px; margin-bottom:4px;">
                    {{ $error }}
                </p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- ROLE SELECTION -->
        <div class="role-label">
            I want to sign up as
        </div>

        <div class="roles">

            <!-- SELLER -->
            <div
                class="role-card selected"
                id="roleSeller"
                data-role="seller"
            >
                <div class="role-radio"></div>

                <div class="role-icon">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M4 9l1-5h14l1 5"/>
                        <path d="M4 9h16v10a1 1 0 01-1 1H5a1 1 0 01-1-1V9z"/>
                        <path d="M9 13a3 3 0 006 0"/>
                    </svg>
                </div>

                <div class="role-title">
                    Seller
                </div>

                <div class="role-desc">
                    I want to sell products and manage my store.
                </div>
            </div>


            <!-- BUYER -->
            <div
                class="role-card"
                id="roleBuyer"
                data-role="buyer"
            >
                <div class="role-radio"></div>

                <div class="role-icon">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <circle cx="9" cy="8" r="2.5"/>
                        <circle cx="17" cy="9" r="2"/>
                        <path d="M4 19c0-2.8 2.2-5 5-5s5 2.2 5 5"/>
                        <path d="M14.5 14.5c2 .2 3.5 1.8 3.5 4.5"/>
                    </svg>
                </div>

                <div class="role-title">
                    Buyer
                </div>

                <div class="role-desc">
                    I want to buy products from trusted shops.
                </div>
            </div>

        </div>


        <!-- HIDDEN ROLE VALUE -->
        <input
            type="hidden"
            name="role"
            id="roleInput"
            value="seller"
        >


        <!-- PROCEED -->
        <button
            type="button"
            id="continueRegistration"
            class="btn-primary"
        >
            Proceed
        </button>

    </form>


    <p class="foot-note">
        Already have an account?
        <a
            href="{{ route('login') }}"
            class="link"
        >
            Log in
        </a>
    </p>

</div>
@endsection


@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const roleCards = document.querySelectorAll('.role-card');
    const roleInput = document.getElementById('roleInput');
    const continueButton = document.getElementById('continueRegistration');

    /*
    |--------------------------------------------------------------------------
    | Initial Role
    |--------------------------------------------------------------------------
    | Seller is selected by default.
    */

    let selectedRole = roleInput.value || 'seller';


    /*
    |--------------------------------------------------------------------------
    | Role Selection
    |--------------------------------------------------------------------------
    */

    roleCards.forEach(card => {

        card.addEventListener('click', function () {

            roleCards.forEach(item => {
                item.classList.remove('selected');
            });

            this.classList.add('selected');

            selectedRole = this.dataset.role;
            roleInput.value = selectedRole;

        });

    });


    /*
    |--------------------------------------------------------------------------
    | Proceed Button
    |--------------------------------------------------------------------------
    */

    continueButton.addEventListener('click', function () {

        /*
        | Make sure the hidden input is always synchronized.
        */
        selectedRole = roleInput.value;


        /*
        |--------------------------------------------------------------------------
        | Seller
        |--------------------------------------------------------------------------
        */

        if (selectedRole === 'seller') {

            window.location.href =
                "{{ route('seller.register') }}";

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Buyer
        |--------------------------------------------------------------------------
        */

        if (selectedRole === 'buyer') {

            window.location.href =
                "{{ route('buyer.register') }}";

            return;
        }

    });

});
</script>
@endsection