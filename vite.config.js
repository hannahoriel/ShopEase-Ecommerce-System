import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';
import { existsSync } from 'node:fs';

// All current, real project assets. Inline <style>/<script> inside Blade files
// do not need separate Vite entries.
const input = [
    // APP
    'resources/css/app.css',

    // COMPONENTS
    'resources/css/components/dashboard-announcement-carousel.css',

    // AUTH
    'resources/css/auth.css',

    // LANDING PAGE
    'resources/css/landing-page.css',

    // ADMIN
    'resources/css/admin/account.css',
    'resources/css/admin/commission-order-modal.css',
    'resources/css/admin/commission.css',
    'resources/css/admin/complaints-disputes.css',
    'resources/css/admin/dashboard.css',
    'resources/css/admin/logistics-management.css',
    'resources/css/admin/messages.css',
    'resources/css/admin/notifications.css',
    'resources/css/admin/platform-settings.css',
    'resources/css/admin/registrations.css',
    'resources/css/admin/reports.css',
    'resources/css/admin/seller-compliance.css',
    'resources/css/admin/user-management.css',
    'resources/css/admin/vouchers.css',

    // BUYER
    'resources/css/buyer/components/floating-chat.css',
    'resources/css/buyer/components/footer.css',
    'resources/css/buyer/components/navbar.css',
    'resources/css/buyer/pages/cart.css',
    'resources/css/buyer/pages/categories.css',
    'resources/css/buyer/pages/checkout.css',
    'resources/css/buyer/pages/dashboard.css',
    'resources/css/buyer/pages/my-purchases.css',
    'resources/css/buyer/pages/product.css',
    'resources/css/buyer/pages/search.css',
    'resources/css/buyer/pages/shop.css',

    // SELLER
    'resources/css/seller/account.css',
    'resources/css/seller/customer-feedback.css',
    'resources/css/seller/dashboard.css',
    'resources/css/seller/inventory.css',
    'resources/css/seller/messages.css',
    'resources/css/seller/notifications.css',
    'resources/css/seller/order-status.css',
    'resources/css/seller/reports.css',
    'resources/css/seller/seller-performance.css',
    'resources/css/seller/shipping-status.css',
    'resources/css/seller/wallet.css',

    // LOGISTICS
    'resources/css/logistics/login.css',
    'resources/css/logistics/logistics-landing.css',
    'resources/css/logistics/reports.css',
    'resources/css/logistics/signup-firstpage.css',
    'resources/css/logistics/signup-review.css',
    'resources/css/logistics/signup-secpage.css',

    // APP
    'resources/js/app.js',
    'resources/js/buyer/pages/view-order.js',

    // COMPONENTS
    'resources/js/components/dashboard-announcement-carousel.js',

    // AUTH
    'resources/js/auth.js',

    // LANDING PAGE
    'resources/js/landing-page.js',

    // ADMIN
    'resources/js/admin/account.js',
    'resources/js/admin/vouchers.js',
    'resources/js/admin/commission-order-modal.js',
    'resources/js/admin/commission.js',
    'resources/js/admin/complaints-disputes.js',
    'resources/js/admin/dashboard.js',
    'resources/js/admin/logistics-management.js',
    'resources/js/admin/messages.js',
    'resources/js/admin/notifications.js',
    'resources/js/admin/platform-settings.js',
    'resources/js/admin/registrations.js',
    'resources/js/admin/reports.js',
    'resources/js/admin/seller-compliance.js',
    'resources/js/admin/user-management.js',

    // BUYER
    'resources/js/buyer/components/buyer-likes.js',
    'resources/js/buyer/components/floating-chat.js',
    'resources/js/buyer/components/navbar.js',
    'resources/js/buyer/pages/cart.js',
    'resources/js/buyer/pages/categories.js',
    'resources/js/buyer/pages/checkout.js',
    'resources/js/buyer/pages/dashboard.js',
    'resources/js/buyer/pages/my-purchases.js',
    'resources/js/buyer/pages/product.js',
    'resources/js/buyer/pages/search.js',
    'resources/js/buyer/pages/shop.js',

    // SELLER
    'resources/js/seller/account.js',
    'resources/js/seller/customer-feedback.js',
    'resources/js/seller/dashboard.js',
    'resources/js/seller/inventory.js',
    'resources/js/seller/messages.js',
    'resources/js/seller/notifications.js',
    'resources/js/seller/order-status.js',
    'resources/js/seller/reports.js',
    'resources/js/seller/seller-performance.js',
    'resources/js/seller/shipping-status.js',
    'resources/js/seller/wallet.js',

    // LOGISTICS
    'resources/js/logistics/login.js',
    'resources/js/logistics/logistics-landing.js',
    'resources/js/logistics/reports.js',
    'resources/js/logistics/signup-firstpage.js',
    'resources/js/logistics/signup-review.js',
    'resources/js/logistics/signup-secpage.js',

].filter((path) => {
    if (!existsSync(path)) {
        throw new Error(`Vite entry missing: ${path}. Restore the file or update vite.config.js.`);
    }
    return true;
});

export default defineConfig({
    plugins: [
        laravel({
            input,
            refresh: true,
            fonts: [
                bunny('Instrument Sans', { weights: [400, 500, 600] }),
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
