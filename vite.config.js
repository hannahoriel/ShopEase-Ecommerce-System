import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // Global
                'resources/css/app.css',
                'resources/css/auth.css',
                'resources/js/app.js',
                'resources/js/auth.js',

                // =========================
                // ADMIN
                // =========================

                // CSS
                'resources/css/admin/commission.css',
                'resources/css/admin/complaints-disputes.css',
                'resources/css/admin/dashboard.css',
                'resources/css/admin/logistics-management.css',
                'resources/css/admin/messages.css',
                'resources/css/admin/platform-settings.css',
                'resources/css/admin/registrations.css',
                'resources/css/admin/seller-compliance.css',
                'resources/css/admin/user-management.css',

                // JS
                'resources/js/admin/commission.js',
                'resources/js/admin/complaints-disputes.js',
                'resources/js/admin/dashboard.js',
                'resources/js/admin/logistics-management.js',
                'resources/js/admin/messages.js',
                'resources/js/admin/platform-settings.js',
                'resources/js/admin/registrations.js',
                'resources/js/admin/seller-compliance.js',
                'resources/js/admin/user-management.js',

                // =========================
                // BUYER COMPONENTS
                // =========================

                // CSS
                'resources/css/buyer/components/floating-chat.css',
                'resources/css/buyer/components/footer.css',
                'resources/css/buyer/components/navbar.css',

                // JS
                'resources/js/buyer/components/floating-chat.js',
                'resources/js/buyer/components/navbar.js',

                // =========================
                // BUYER PAGES
                // =========================

                // CSS
                'resources/css/buyer/pages/cart.css',
                'resources/css/buyer/pages/checkout.css',
                'resources/css/buyer/pages/dashboard.css',
                'resources/css/buyer/pages/my-purchases.css',
                'resources/css/buyer/pages/product.css',

                // JS
                'resources/js/buyer/pages/cart.js',
                'resources/js/buyer/pages/checkout.js',
                'resources/js/buyer/pages/dashboard.js',
                'resources/js/buyer/pages/my-purchases.js',
                'resources/js/buyer/pages/product.js',

                // =========================
                // SELLER
                // =========================

                // CSS
                'resources/css/seller/customer-feedback.css',
                'resources/css/seller/dashboard.css',
                'resources/css/seller/inventory.css',
                'resources/css/seller/messages.css',
                'resources/css/seller/order-status.css',
                'resources/css/seller/reports.css',
                'resources/css/seller/shipping-status.css',

                // JS
                'resources/js/seller/customer-feedback.js',
                'resources/js/seller/dashboard.js',
                'resources/js/seller/inventory.js',
                'resources/js/seller/messages.js',
                'resources/js/seller/order-status.js',
                'resources/js/seller/reports.js',
                'resources/js/seller/shipping-status.js',
            ],

            refresh: true,

            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),

        tailwindcss(),
    ],

    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});