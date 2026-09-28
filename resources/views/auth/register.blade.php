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
                    <img
                        src="{{ asset('icons/login/seller-selected.png') }}"
                        alt="Seller"
                    >
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
                    <img
                        src="{{ asset('icons/login/buyer-unselected.png') }}"
                        alt="Buyer"
                    >
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
<style>
    /*
    |--------------------------------------------------------------------------
    | Role Icons
    |--------------------------------------------------------------------------
    | The PNGs already contain their own circular design,
    | so no additional circle/background is added here.
    */

    .role-icon {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;

        display: flex;
        align-items: center;
        justify-content: center;
    }

    .role-icon img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        display: block;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const roleCards = document.querySelectorAll('.role-card');
    const roleInput = document.getElementById('roleInput');
    const continueButton = document.getElementById('continueRegistration');

    const roleImages = {
        seller: {
            selected: "{{ asset('icons/login/seller-selected.png') }}",
            unselected: "{{ asset('icons/login/seller-unselected.png') }}"
        },
        buyer: {
            selected: "{{ asset('icons/login/buyer-selected.png') }}",
            unselected: "{{ asset('icons/login/buyer-unselected.png') }}"
        }
    };


    /*
    |--------------------------------------------------------------------------
    | Update Role Icons
    |--------------------------------------------------------------------------
    */

    function updateRoleIcons(selectedRole) {

        roleCards.forEach(card => {

            const role = card.dataset.role;
            const icon = card.querySelector('.role-icon img');

            if (!icon || !roleImages[role]) {
                return;
            }

            if (role === selectedRole) {

                icon.src = roleImages[role].selected;

            } else {

                icon.src = roleImages[role].unselected;

            }

        });
    }


    /*
    |--------------------------------------------------------------------------
    | Initial Role
    |--------------------------------------------------------------------------
    | Seller is selected by default.
    */

    let selectedRole = roleInput.value || 'seller';

    updateRoleIcons(selectedRole);


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

            updateRoleIcons(selectedRole);

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