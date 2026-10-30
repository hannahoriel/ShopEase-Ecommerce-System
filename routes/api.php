<?php

use App\Http\Controllers\Admin\AdminAccountController;
use App\Http\Controllers\Admin\AdminAccountSecurityController;
use App\Http\Controllers\Admin\AdminManagementController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\CommissionController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\LogisticsManagementController;
use App\Http\Controllers\Admin\PlatformSettingsController;
use App\Http\Controllers\Admin\RegistrationController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Api\Admin\ComplaintController as AdminComplaintController;
use App\Http\Controllers\Api\Admin\MessagesController as AdminMessagesController;
use App\Http\Controllers\Api\Admin\SellerComplianceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MobilePasswordSecurityController;
use App\Http\Controllers\Api\MobileRegistrationVerificationController;
use App\Http\Controllers\Api\Buyer\AccountController as BuyerAccountController;
use App\Http\Controllers\Api\Buyer\LikeController as BuyerLikeController;
use App\Http\Controllers\Api\Buyer\ComplaintMessagesController as BuyerComplaintMessagesController;
use App\Http\Controllers\Api\Buyer\AccountSecurityController as BuyerAccountSecurityController;
use App\Http\Controllers\Api\Buyer\CartController as BuyerCartController;
use App\Http\Controllers\Api\Buyer\DashboardController as BuyerDashboardController;
use App\Http\Controllers\Api\Buyer\MessagesController as BuyerMessagesController;
use App\Http\Controllers\Api\Buyer\NotificationController as BuyerNotificationController;
use App\Http\Controllers\Api\Buyer\OrderController as BuyerOrderController;
use App\Http\Controllers\Api\Buyer\OrderReturnController as BuyerOrderReturnController;
use App\Http\Controllers\Api\Buyer\VoucherController as BuyerVoucherController;
use App\Http\Controllers\Api\Buyer\ProductReviewController;
use App\Http\Controllers\Api\Logistics\DeliveryController as LogisticsDeliveryController;
use App\Http\Controllers\Api\Logistics\NetworkHubController;
use App\Http\Controllers\Api\Logistics\MessagesController as LogisticsMessagesController;
use App\Http\Controllers\Api\Logistics\AccountController as LogisticsAccountController;
use App\Http\Controllers\Api\Logistics\ParcelTrackingController;
use App\Http\Controllers\Api\Logistics\ListRiderRemittancesController;
use App\Http\Controllers\Api\Logistics\ListRiderSupportTicketsController;
use App\Http\Controllers\Api\Logistics\ListRiderDeliveryActionsController;
use App\Http\Controllers\Api\Logistics\ReviewRiderRemittanceController;
use App\Http\Controllers\Api\Logistics\ReviewRiderSupportTicketController;
use App\Http\Controllers\Api\Logistics\ReviewRiderDeliveryActionController;
use App\Http\Controllers\Api\Logistics\ShowRiderRemittanceReceiptController;
use App\Http\Controllers\Api\Logistics\StoreRiderEarningController;
use App\Http\Controllers\Api\ProfilePhotoController;
use App\Http\Controllers\Api\OrderRiderMessagesController;
use App\Http\Controllers\Api\Rider\Homepage\Attendance\ClockInController;
use App\Http\Controllers\Api\Rider\Homepage\Attendance\ClockOutController;
use App\Http\Controllers\Api\Rider\Homepage\Attendance\ListAttendanceController;
use App\Http\Controllers\Api\Rider\Homepage\Calculator\ClearCalculationHistoryController;
use App\Http\Controllers\Api\Rider\Homepage\Calculator\ListCalculationsController;
use App\Http\Controllers\Api\Rider\Homepage\Calculator\StoreCalculationController;
use App\Http\Controllers\Api\Rider\Homepage\Contact\ListOrderMessagesController;
use App\Http\Controllers\Api\Rider\Homepage\Contact\StoreOrderMessageController;
use App\Http\Controllers\Api\Rider\Homepage\DeliveryTask\CreateDeliveryActionController;
use App\Http\Controllers\Api\Rider\Homepage\DeliveryTask\ListDeliveriesController;
use App\Http\Controllers\Api\Rider\Homepage\DeliveryTask\ShowDeliveryController;
use App\Http\Controllers\Api\Rider\Homepage\DeliveryTask\ShowDeliveryProofPhotoController;
use App\Http\Controllers\Api\Rider\Homepage\DeliveryTask\SubmitDeliveryProofController;
use App\Http\Controllers\Api\Rider\Homepage\DeliveryTask\UpdateDeliveryStatusController;
use App\Http\Controllers\Api\Rider\Homepage\MoneyReport\ListEarningsController;
use App\Http\Controllers\Api\Rider\Homepage\MoneyReport\ShowRiderFinanceController;
use App\Http\Controllers\Api\Rider\Homepage\MoneyReport\StoreRemittanceController;
use App\Http\Controllers\Api\Rider\Homepage\PickupDeliveryRecord\ListPickupDeliveryRecordsController;
use App\Http\Controllers\Api\Rider\Homepage\PickupTask\ListPickupTasksController;
use App\Http\Controllers\Api\Rider\Homepage\PickupTask\ShowPickupProofPhotoController;
use App\Http\Controllers\Api\Rider\Homepage\PickupTask\SubmitPickupProofController;
use App\Http\Controllers\Api\Rider\Homepage\Notifications\ListNotificationsController;
use App\Http\Controllers\Api\Rider\Homepage\Notifications\MarkAllNotificationsReadController;
use App\Http\Controllers\Api\Rider\Homepage\Notifications\MarkNotificationReadController;
use App\Http\Controllers\Api\Rider\Homepage\ShowRiderDashboardController;
use App\Http\Controllers\Api\Rider\Homepage\VerifyOrder\ArchiveVerifyOrderController;
use App\Http\Controllers\Api\Rider\Homepage\VerifyOrder\DeleteArchivedVerifyOrderController;
use App\Http\Controllers\Api\Rider\Homepage\VerifyOrder\ListVerifyOrdersController;
use App\Http\Controllers\Api\Rider\Homepage\VerifyOrder\RestoreVerifyOrderController;
use App\Http\Controllers\Api\Rider\Content\ShowRiderPageContentController;
use App\Http\Controllers\Api\Rider\User\Profile\ShowRiderProfileController;
use App\Http\Controllers\Api\Rider\User\Profile\UpdateRiderProfileController;
use App\Http\Controllers\Api\Rider\User\Recipients\DeleteRecipientController;
use App\Http\Controllers\Api\Rider\User\Recipients\ListRecipientsController;
use App\Http\Controllers\Api\Rider\User\Recipients\StoreRecipientController;
use App\Http\Controllers\Api\Rider\User\Recipients\UpdateRecipientController;
use App\Http\Controllers\Api\Rider\User\Support\ArchiveSupportTicketController;
use App\Http\Controllers\Api\Rider\User\Support\ListSupportTicketsController;
use App\Http\Controllers\Api\Rider\User\Support\StoreSupportTicketController;
use App\Http\Controllers\Api\Rider\User\Support\ListSupportTicketMessagesController;
use App\Http\Controllers\Api\Rider\User\Support\StoreSupportTicketMessageController;
use App\Http\Controllers\Api\Rider\Homepage\StarCourier\ShowStarCourierMetricsController;
use App\Http\Controllers\Api\Logistics\StoreRiderSupportTicketMessageController;
use App\Http\Controllers\Api\Rider\Register\StoreRiderRegistrationController;
use App\Http\Controllers\Api\Rider\Register\ShowRiderRegistrationStatusController;
use App\Http\Controllers\Api\Seller\CustomerFeedbackController;
use App\Http\Controllers\Api\Seller\DashboardController as SellerDashboardController;
use App\Http\Controllers\Api\Seller\InventoryController as SellerInventoryController;
use App\Http\Controllers\Api\Seller\MessagesController as SellerMessagesController;
use App\Http\Controllers\Api\Seller\OrderStatusController;
use App\Http\Controllers\Api\Seller\ReportsController as SellerReportsController;
use App\Http\Controllers\Api\Seller\ReturnsRefundsController as SellerReturnsRefundsController;
use App\Http\Controllers\Api\Seller\SalesController as SellerSalesController;
use App\Http\Controllers\Api\Seller\ShippingStatusController;
use App\Http\Controllers\Api\Seller\VouchersController as SellerVouchersController;
use App\Http\Controllers\LocationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::prefix('auth/register/email/otp')->middleware('throttle:10,1')->group(function () {
        Route::post('/send', [MobileRegistrationVerificationController::class, 'sendCode']);
        Route::post('/verify', [MobileRegistrationVerificationController::class, 'verifyCode']);
    });
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::prefix('auth/password/forgot')->middleware('throttle:5,1')->group(function () {
        Route::post('/otp/send', [MobilePasswordSecurityController::class, 'sendForgotOtp']);
        Route::post('/otp/verify', [MobilePasswordSecurityController::class, 'verifyForgotOtp']);
        Route::post('/reset', [MobilePasswordSecurityController::class, 'resetForgottenPassword']);
    });
    Route::post('/rider/register', StoreRiderRegistrationController::class)->name('rider.api.register');
    Route::get('/rider/registration-status/{token}', ShowRiderRegistrationStatusController::class)
        ->name('rider.api.registration-status');
    Route::get('/orders/verify/{orderNumber}', [BuyerOrderController::class, 'verify'])->name('buyer.api.orders.verify');

    Route::prefix('locations')->group(function () {
        Route::get('/regions', [LocationController::class, 'regions']);
        Route::get('/regions/{code}/provinces', [LocationController::class, 'regionProvinces'])->whereNumber('code');
        Route::get('/regions/{code}/districts', [LocationController::class, 'regionDistricts'])->whereNumber('code');
        Route::get('/regions/{code}/cities-municipalities', [LocationController::class, 'regionCities'])->whereNumber('code');
        Route::get('/provinces/{code}/cities-municipalities', [LocationController::class, 'provinceCities'])->whereNumber('code');
        Route::get('/districts/{code}/cities-municipalities', [LocationController::class, 'districtCities'])->whereNumber('code');
        Route::get('/cities-municipalities/{code}/barangays', [LocationController::class, 'cityBarangays'])->whereNumber('code');
        Route::get('/provinces', [LocationController::class, 'provinces']);
        Route::get('/provinces/{code}/cities', [LocationController::class, 'municipalities']);
        Route::get('/cities/{code}/barangays', [LocationController::class, 'barangays']);
    });

    Route::middleware('auth:sanctum')->group(function () {

        Route::get('/auth/me', fn (Request $request) => response()->json($request->user()));
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::put('/profile/photo', [ProfilePhotoController::class, 'update'])->name('profile.photo.update');
        Route::put('/buyer/account', [BuyerAccountController::class, 'update'])->name('buyer.api.account.update');
        Route::get('/dashboard', fn (Request $request) => response()->json([
            'user' => $request->user(),
            'role' => $request->user()->role,
        ]));
        Route::prefix('account/security/password')->middleware('throttle:10,1')->group(function () {
            Route::post('/otp/send', [MobilePasswordSecurityController::class, 'sendChangeOtp']);
            Route::post('/otp/verify', [MobilePasswordSecurityController::class, 'verifyChangeOtp']);
            Route::post('/change', [MobilePasswordSecurityController::class, 'changePassword']);
        });

        Route::middleware('role:admin')->prefix('admin')->group(function () {
            Route::get('/logistics/account-change-requests', [LogisticsAccountController::class, 'requests'])->name('admin.api.logistics-account.requests');
            Route::patch('/logistics/account-change-requests/{changeRequest}', [LogisticsAccountController::class, 'review'])->name('admin.api.logistics-account.review');
            Route::get('/dashboard', [AdminDashboardController::class, 'apiIndex'])->name('admin.api.dashboard');
            Route::get('/messages', [AdminMessagesController::class, 'index'])->name('admin.api.messages.index');
            Route::get('/messages/contacts', [AdminMessagesController::class, 'contacts'])->name('admin.api.messages.contacts');
            Route::post('/messages/conversations', [AdminMessagesController::class, 'storeConversation'])->name('admin.api.messages.conversations.store');
            Route::get('/messages/conversations/{conversation}', [AdminMessagesController::class, 'show'])->name('admin.api.messages.conversations.show');
            Route::post('/messages/conversations/{conversation}/messages', [AdminMessagesController::class, 'send'])->name('admin.api.messages.send');
            Route::get('/messages/conversations/{conversation}/messages/{message}/attachment', [AdminMessagesController::class, 'attachment'])
                ->name('admin.api.messages.attachment');
            Route::get('/commissions', [CommissionController::class, 'index'])->name('admin.api.commissions.index');
            Route::get('/notifications', [AdminNotificationController::class, 'apiIndex'])->name('admin.api.notifications.index');
            Route::patch('/notifications/read-all', [AdminNotificationController::class, 'markAllRead'])->name('admin.api.notifications.read-all');
            Route::patch('/notifications/{notification}/read', [AdminNotificationController::class, 'markRead'])->name('admin.api.notifications.read');
            Route::get('/account', [AdminAccountController::class, 'show'])->name('admin.api.account.show');
            Route::put('/account', [AdminAccountController::class, 'update'])->name('admin.api.account.update');
            Route::post('/account/notifications', [AdminAccountController::class, 'notifications'])->name('admin.api.account.notifications');
            Route::get('/account/admins', [AdminManagementController::class, 'index'])->name('admin.api.account.admins.index');
            Route::post('/account/admins', [AdminManagementController::class, 'store'])->name('admin.api.account.admins.store');
            Route::get('/account/admins/{user}/id-photo', [AdminManagementController::class, 'idPhoto'])->name('admin.api.account.admins.id-photo');
            Route::patch('/account/admins/{user}', [AdminManagementController::class, 'update'])->name('admin.api.account.admins.update');
            Route::middleware('throttle:60,1')->prefix('account/security')->name('admin.api.account.security.')->group(function () {
                Route::post('/otp/send', [AdminAccountSecurityController::class, 'sendOtp'])->name('otp.send');
                Route::post('/otp/verify', [AdminAccountSecurityController::class, 'verifyOtp'])->name('otp.verify');
                Route::post('/password/change', [AdminAccountSecurityController::class, 'changePassword'])->name('password.change');
                Route::post('/password/reset', [AdminAccountSecurityController::class, 'resetPassword'])->name('password.reset');
            });
            Route::get('/logistics-management', [LogisticsManagementController::class, 'index'])->name('admin.api.logistics.index');
            Route::post('/logistics-management/registrations/{registration}/approve', [LogisticsManagementController::class, 'approve'])
                ->name('admin.api.logistics.approve');
            Route::post('/logistics-management/registrations/{registration}/reject', [LogisticsManagementController::class, 'reject'])
                ->name('admin.api.logistics.reject');
            Route::get('/logistics-management/companies/{logistics}/branches', [LogisticsManagementController::class, 'branches'])
                ->name('admin.api.logistics.branches.index');
            Route::post('/logistics-management/companies/{logistics}/branches', [LogisticsManagementController::class, 'storeBranch'])
                ->name('admin.api.logistics.branches.store');
            Route::patch('/logistics-management/branches/{branch}', [LogisticsManagementController::class, 'updateBranch'])
                ->name('admin.api.logistics.branches.update');
            Route::delete('/logistics-management/branches/{branch}', [LogisticsManagementController::class, 'destroyBranch'])
                ->name('admin.api.logistics.branches.destroy');
            Route::get('/complaints', [AdminComplaintController::class, 'index'])->name('admin.api.complaints.index');
            Route::patch('/complaints/{complaint}/status', [AdminComplaintController::class, 'updateStatus'])->name('admin.api.complaints.status');
            Route::prefix('platform-settings')->controller(PlatformSettingsController::class)->name('admin.api.platform-settings.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/announcements', 'storeAnnouncement')->name('announcements.store');
                Route::patch('/announcements/{announcement}', 'updateAnnouncement')->name('announcements.update');
                Route::delete('/announcements/{announcement}', 'destroyAnnouncement')->name('announcements.destroy');
                Route::post('/policies', 'storePolicy')->name('policies.store');
                Route::patch('/policies/{policy}', 'updatePolicy')->name('policies.update');
                Route::delete('/policies/{policy}', 'destroyPolicy')->name('policies.destroy');
            });
            Route::get('/registrations', [RegistrationController::class, 'list'])->name('admin.api.registrations.index');
            Route::get('/registrations/approved', [RegistrationController::class, 'approvedArchive'])->name('admin.api.registrations.approved');
            Route::get('/registrations/rejected', [RegistrationController::class, 'rejectedArchive'])->name('admin.api.registrations.rejected');
            Route::get('/registrations/{registration}', [RegistrationController::class, 'show'])->name('admin.api.registrations.show');
            Route::post('/registrations/{registration}/approve', [RegistrationController::class, 'approve'])->name('admin.api.registrations.approve');
            Route::post('/registrations/{registration}/reject', [RegistrationController::class, 'reject'])->name('admin.api.registrations.reject');
            Route::get('/users', [UserManagementController::class, 'list'])->name('admin.api.users.index');
            Route::get('/users/{user}', [UserManagementController::class, 'show'])->name('admin.api.users.show');
            Route::patch('/users/{user}/status', [UserManagementController::class, 'updateStatus'])->name('admin.api.users.status');

            Route::prefix('seller-compliance')->controller(SellerComplianceController::class)->name('admin.api.seller-compliance.')->group(function () {
                Route::get('', 'index')->name('index');
                Route::get('{seller}', 'show')->name('show');
                Route::post('{seller}/suspend', 'suspend')->name('suspend');
                Route::post('products/{product}/approve', 'approveProduct')->name('products.approve');
                Route::post('products/{product}/warn', 'warnProduct')->name('products.warn');
                Route::post('products/{product}/remove', 'removeProduct')->name('products.remove');
            });
        });

        Route::middleware('role:buyer')->prefix('buyer')->group(function () {
            Route::get('/account', [BuyerAccountController::class, 'show'])->name('buyer.api.account.show');
            Route::get('/likes', [BuyerLikeController::class, 'index'])->name('buyer.api.likes.index');
            Route::post('/likes', [BuyerLikeController::class, 'store'])->name('buyer.api.likes.store');
            Route::delete('/likes/{product}', [BuyerLikeController::class, 'destroy'])->name('buyer.api.likes.destroy');
            Route::prefix('account/security')->name('buyer.api.account.security.')->middleware('throttle:10,1')->group(function () {
                Route::post('/otp/send', [BuyerAccountSecurityController::class, 'sendOtp'])->name('otp.send');
                Route::post('/otp/verify', [BuyerAccountSecurityController::class, 'verifyOtp'])->name('otp.verify');
                Route::post('/password/change', [BuyerAccountSecurityController::class, 'changePassword'])->name('password.change');
                Route::post('/password/reset', [BuyerAccountSecurityController::class, 'resetPassword'])->name('password.reset');
            });
            Route::get('/notifications', [BuyerNotificationController::class, 'index'])->name('buyer.api.notifications.index');
            Route::patch('/notifications/{notification}/read', [BuyerNotificationController::class, 'markRead'])->name('buyer.api.notifications.read');
            Route::post('/notifications/read-all', [BuyerNotificationController::class, 'markAllRead'])->name('buyer.api.notifications.read-all');
            Route::get('/dashboard/announcements', [BuyerDashboardController::class, 'announcements'])->name('buyer.api.dashboard.announcements');
            Route::get('/messages', [BuyerMessagesController::class, 'index'])->name('buyer.api.messages.index');
            Route::post('/messages/conversations', [BuyerMessagesController::class, 'storeConversation'])->name('buyer.api.messages.conversations.store');
            Route::get('/messages/conversations/{conversation}', [BuyerMessagesController::class, 'show'])->name('buyer.api.messages.conversations.show');
            Route::post('/messages/conversations/{conversation}/messages', [BuyerMessagesController::class, 'send'])->name('buyer.api.messages.send');
            Route::get('/orders', [BuyerOrderController::class, 'index'])->name('buyer.api.orders.index');
            Route::post('/orders/{order}/cancel', [BuyerOrderController::class, 'cancel'])->name('buyer.api.orders.cancel');
            Route::get('/dashboard/products', [BuyerDashboardController::class, 'products'])->name('buyer.api.dashboard.products');
            Route::get('/products/{product}', [BuyerDashboardController::class, 'show'])->name('buyer.api.products.show');
            Route::get('/products/{product}/reviews', [ProductReviewController::class, 'index'])->name('buyer.api.products.reviews.index');
            Route::post('/products/{product}/reviews', [ProductReviewController::class, 'store'])->name('buyer.api.products.reviews.store');
            Route::post('/orders', [BuyerOrderController::class, 'store'])->name('buyer.api.orders.store');
            Route::post('/vouchers/preview', [BuyerVoucherController::class, 'preview'])->name('buyer.api.vouchers.preview');
            Route::get('/vouchers', [BuyerVoucherController::class, 'index'])->name('buyer.api.vouchers.index');
            Route::post('/orders/{order}/complaints', [BuyerOrderController::class, 'storeComplaint'])->name('buyer.api.orders.complaints.store');
            Route::get('/returns', [BuyerOrderReturnController::class, 'index'])->name('buyer.api.returns.index');
            Route::post('/orders/{order}/returns', [BuyerOrderReturnController::class, 'store'])->name('buyer.api.orders.returns.store');
            Route::get('/complaints/{complaint}/messages', [BuyerComplaintMessagesController::class, 'index'])->name('buyer.api.complaints.messages.index');
            Route::post('/complaints/{complaint}/messages', [BuyerComplaintMessagesController::class, 'store'])->name('buyer.api.complaints.messages.store');
            Route::get('/orders/{order}/rider-messages', [OrderRiderMessagesController::class, 'index'])->name('buyer.api.orders.rider-messages.index');
            Route::post('/orders/{order}/rider-messages', [OrderRiderMessagesController::class, 'store'])->name('buyer.api.orders.rider-messages.store');

            // Cart
            Route::get('/cart', [BuyerCartController::class, 'index'])->name('buyer.api.cart.index');
            Route::post('/cart', [BuyerCartController::class, 'store'])->name('buyer.api.cart.store');
            Route::patch('/cart/{item}', [BuyerCartController::class, 'update'])->name('buyer.api.cart.update');
            Route::delete('/cart/{item}', [BuyerCartController::class, 'destroy'])->name('buyer.api.cart.destroy');
            Route::delete('/cart', [BuyerCartController::class, 'clear'])->name('buyer.api.cart.clear');
        });

        Route::middleware('role:seller')->prefix('seller')->group(function () {
            Route::get('/account', [\App\Http\Controllers\Api\Seller\AccountController::class, 'show']);
            Route::put('/account', [\App\Http\Controllers\Api\Seller\AccountController::class, 'update']);
            Route::get('/dashboard', [SellerDashboardController::class, 'apiIndex']);
            Route::get('/messages', [SellerMessagesController::class, 'index'])->name('seller.api.messages.index');
            Route::get('/messages/contacts', [SellerMessagesController::class, 'contacts'])->name('seller.api.messages.contacts');
            Route::post('/messages/conversations', [SellerMessagesController::class, 'storeConversation'])->name('seller.api.messages.conversations.store');
            Route::get('/messages/conversations/{conversation}', [SellerMessagesController::class, 'show'])->name('seller.api.messages.conversations.show');
            Route::post('/messages/conversations/{conversation}/messages', [SellerMessagesController::class, 'send'])->name('seller.api.messages.send');
            Route::get('/messages/conversations/{conversation}/messages/{message}/attachment', [SellerMessagesController::class, 'attachment'])->name('seller.api.messages.attachment');
            Route::get('/reports', [SellerReportsController::class, 'data'])->name('seller.api.reports');
            Route::get('/sales/products', [SellerSalesController::class, 'index'])->name('seller.api.sales.products.index');
            Route::patch('/sales/products/{product}', [SellerSalesController::class, 'update'])->name('seller.api.sales.products.update');
            Route::get('/returns-refunds', [SellerReturnsRefundsController::class, 'index'])->name('seller.api.returns.index');
            Route::patch('/returns-refunds/{return}/decision', [SellerReturnsRefundsController::class, 'decide'])->name('seller.api.returns.decision');
            Route::post('/returns-refunds/{return}/refund', [SellerReturnsRefundsController::class, 'completeRefund'])->name('seller.api.returns.refund');
            Route::get('/vouchers', [SellerVouchersController::class, 'index'])->name('seller.api.vouchers.index');
            Route::post('/vouchers', [SellerVouchersController::class, 'store'])->name('seller.api.vouchers.store');
            Route::patch('/vouchers/{voucher}', [SellerVouchersController::class, 'update'])->name('seller.api.vouchers.update');
            Route::get('/feedback', [CustomerFeedbackController::class, 'index'])->name('seller.api.feedback.index');
            Route::get('/feedback/products/{product}', [CustomerFeedbackController::class, 'showProduct'])->name('seller.api.feedback.products.show');
            Route::get('/order-status', [OrderStatusController::class, 'index']);
            Route::get('/orders/{order}', [OrderStatusController::class, 'show']);
            Route::patch('/orders/{order}/status', [OrderStatusController::class, 'update']);
            Route::get('/orders/{order}/rider-messages', [OrderRiderMessagesController::class, 'index'])->name('seller.api.orders.rider-messages.index');
            Route::post('/orders/{order}/rider-messages', [OrderRiderMessagesController::class, 'store'])->name('seller.api.orders.rider-messages.store');
            Route::post('/orders/{order}/schedule', [OrderStatusController::class, 'schedule']);
            Route::post('/orders/{order}/cancel-shipment', [OrderStatusController::class, 'cancelScheduledShipment']);
            Route::post('/orders/{order}/cancellation-requests/{cancellationRequest}/approve', [OrderStatusController::class, 'approveCancellationRequest']);
            Route::post('/orders/{order}/cancellation-requests/{cancellationRequest}/reject', [OrderStatusController::class, 'rejectCancellationRequest']);
            Route::post('/orders/{order}/waybill', [OrderStatusController::class, 'waybill']);
            Route::get('/shipping-status', [ShippingStatusController::class, 'index']);
            Route::get('/shipping/{order}', [ShippingStatusController::class, 'show']);
            Route::patch('/shipping/{order}/status', [ShippingStatusController::class, 'update']);

            Route::get('/inventory', [SellerInventoryController::class, 'index'])->name('seller.api.inventory.index');
            Route::post('/inventory', [SellerInventoryController::class, 'store'])->name('seller.api.inventory.store');
            Route::get('/inventory/{product}', [SellerInventoryController::class, 'show'])->name('seller.api.inventory.show');
            Route::patch('/inventory/{product}', [SellerInventoryController::class, 'update'])->name('seller.api.inventory.update');
            Route::patch('/inventory/{product}/archive', [SellerInventoryController::class, 'archive'])->name('seller.api.inventory.products.archive');
            Route::patch('/inventory/{product}/unarchive', [SellerInventoryController::class, 'unarchive'])->name('seller.api.inventory.products.unarchive');
            Route::delete('/inventory/{product}', [SellerInventoryController::class, 'destroy'])->name('seller.api.inventory.products.destroy');

            Route::post('/inventory/products', [SellerInventoryController::class, 'store'])->name('seller.api.inventory.products.store');
            Route::patch('/inventory/products/{product}/archive', [SellerInventoryController::class, 'archive'])->name('seller.api.inventory.products.archive.alias');
            Route::patch('/inventory/products/{product}/unarchive', [SellerInventoryController::class, 'unarchive'])->name('seller.api.inventory.products.unarchive.alias');
            Route::delete('/inventory/products/{product}', [SellerInventoryController::class, 'destroy'])->name('seller.api.inventory.products.destroy.alias');
        });

        Route::middleware('role:logistics')->prefix('logistics')->group(function () {
            Route::get('/account', [LogisticsAccountController::class, 'show'])->name('logistics.api.account.show');
            Route::post('/account/profile-requests', [LogisticsAccountController::class, 'requestProfileChange'])->name('logistics.api.account.profile-requests.store');
            Route::put('/account/preferences', [LogisticsAccountController::class, 'savePreferences'])->name('logistics.api.account.preferences.update');
            Route::post('/account/cover-photo', [LogisticsAccountController::class, 'uploadCoverPhoto'])->name('logistics.api.account.cover-photo.store');
            Route::get('/messages', [LogisticsMessagesController::class, 'index'])->name('logistics.api.messages.index');
            Route::post('/messages/thread', [LogisticsMessagesController::class, 'thread'])->name('logistics.api.messages.thread');
            Route::get('/messages/attachments/{type}/{conversation}/{message}', [LogisticsMessagesController::class, 'attachment'])->name('logistics.api.messages.attachments.show');
            Route::post('/messages', [LogisticsMessagesController::class, 'send'])->name('logistics.api.messages.send');
            Route::get('/parcel-tracking', [ParcelTrackingController::class, 'index'])->name('logistics.api.parcel-tracking.index');
            Route::get('/network-hubs', [NetworkHubController::class, 'index'])->name('logistics.api.network-hubs.index');
            Route::get('/network-hubs/archive', [NetworkHubController::class, 'archive'])->name('logistics.api.network-hubs.archive');
            Route::post('/network-hubs', [NetworkHubController::class, 'store'])->name('logistics.api.network-hubs.store');
            Route::put('/network-hubs/{center}', [NetworkHubController::class, 'update'])->name('logistics.api.network-hubs.update');
            Route::post('/network-hubs/{center}/archive', [NetworkHubController::class, 'archiveCenter'])->name('logistics.api.network-hubs.archive-center');
            Route::post('/network-hubs/{center}/restore', [NetworkHubController::class, 'restoreCenter'])->name('logistics.api.network-hubs.restore');
            Route::get('/network-hubs/{center}/delivery-hubs', [NetworkHubController::class, 'hubs'])->name('logistics.api.network-hubs.delivery-hubs.index');
            Route::get('/network-hubs/{center}/delivery-hubs/archive', [NetworkHubController::class, 'hubArchive'])->name('logistics.api.network-hubs.delivery-hubs.archive');
            Route::post('/network-hubs/{center}/delivery-hubs', [NetworkHubController::class, 'storeHub'])->name('logistics.api.network-hubs.delivery-hubs.store');
            Route::put('/delivery-hubs/{hub}', [NetworkHubController::class, 'updateHub'])->name('logistics.api.delivery-hubs.update');
            Route::post('/delivery-hubs/{hub}/archive', [NetworkHubController::class, 'archiveHub'])->name('logistics.api.delivery-hubs.archive');
            Route::post('/delivery-hubs/{hub}/restore', [NetworkHubController::class, 'restoreHub'])->name('logistics.api.delivery-hubs.restore');
            Route::get('/branches', [LogisticsDeliveryController::class, 'branches'])->name('logistics.api.branches.index');
            Route::get('/deliveries', [LogisticsDeliveryController::class, 'index'])->name('logistics.api.deliveries.index');
            Route::get('/available-deliveries', [LogisticsDeliveryController::class, 'available'])->name('logistics.api.deliveries.available');
            Route::get('/riders', [LogisticsDeliveryController::class, 'riders'])->name('logistics.api.riders.index');
            Route::patch('/riders/{rider}/branch', [LogisticsDeliveryController::class, 'assignBranch'])->name('logistics.api.riders.branch');
            Route::post('/deliveries/{order}/assign', [LogisticsDeliveryController::class, 'assign'])->name('logistics.api.deliveries.assign');
            Route::get('/rider-remittances', ListRiderRemittancesController::class)->name('logistics.api.rider-remittances.index');
            Route::patch('/rider-remittances/{remittance}/review', ReviewRiderRemittanceController::class)->name('logistics.api.rider-remittances.review');
            Route::get('/rider-remittances/{remittance}/receipt', ShowRiderRemittanceReceiptController::class)->name('logistics.api.rider-remittances.receipt');
            Route::post('/rider-earnings', StoreRiderEarningController::class)->name('logistics.api.rider-earnings.store');
            Route::get('/rider-support-tickets', ListRiderSupportTicketsController::class)->name('logistics.api.rider-support-tickets.index');
            Route::patch('/rider-support-tickets/{ticket}/review', ReviewRiderSupportTicketController::class)->name('logistics.api.rider-support-tickets.review');
            Route::post('/rider-support-tickets/{ticket}/messages', StoreRiderSupportTicketMessageController::class)->name('logistics.api.rider-support-tickets.messages.store');
            Route::get('/rider-delivery-actions', ListRiderDeliveryActionsController::class)->name('logistics.api.rider-delivery-actions.index');
            Route::patch('/rider-delivery-actions/{action}/review', ReviewRiderDeliveryActionController::class)->name('logistics.api.rider-delivery-actions.review');
        });

        foreach (['sorting_center', 'delivery_hub'] as $facilityRole) {
            Route::middleware("role:{$facilityRole}")->prefix($facilityRole)->group(function () use ($facilityRole) {
                Route::get('/messages', [LogisticsMessagesController::class, 'index'])->name("{$facilityRole}.api.messages.index");
                Route::get('/messages/attachments/{type}/{conversation}/{message}', [LogisticsMessagesController::class, 'attachment'])->name("{$facilityRole}.api.messages.attachments.show");
                Route::get('/messages/{conversation}', [LogisticsMessagesController::class, 'show'])->name("{$facilityRole}.api.messages.show");
                Route::post('/messages', [LogisticsMessagesController::class, 'send'])->name("{$facilityRole}.api.messages.send");
            });
        }

        Route::middleware('role:rider')->prefix('rider')->group(function () {
            Route::post('/location', \App\Http\Controllers\Api\Rider\UpdateRiderLocationController::class)->name('rider.api.location.update');
            Route::get('/dashboard', ShowRiderDashboardController::class)->name('rider.api.dashboard');
            Route::get('/star-courier', ShowStarCourierMetricsController::class)->name('rider.api.star-courier');
            Route::get('/content/{page}', ShowRiderPageContentController::class)->name('rider.api.content.show');
            Route::get('/deliveries/{order}/messages', ListOrderMessagesController::class)->name('rider.api.deliveries.messages.index');
            Route::post('/deliveries/{order}/messages', StoreOrderMessageController::class)->name('rider.api.deliveries.messages.store');
            Route::get('/profile', ShowRiderProfileController::class)->name('rider.api.profile.show');
            Route::patch('/profile', UpdateRiderProfileController::class)->name('rider.api.profile.update');
            Route::get('/attendance', ListAttendanceController::class)->name('rider.api.attendance.index');
            Route::post('/attendance/clock-in', ClockInController::class)->name('rider.api.attendance.clock-in');
            Route::post('/attendance/clock-out', ClockOutController::class)->name('rider.api.attendance.clock-out');
            Route::get('/notifications', ListNotificationsController::class)->name('rider.api.notifications.index');
            Route::patch('/notifications/read-all', MarkAllNotificationsReadController::class)->name('rider.api.notifications.read-all');
            Route::patch('/notifications/{notification}/read', MarkNotificationReadController::class)->name('rider.api.notifications.read');
            Route::get('/recipients', ListRecipientsController::class)->name('rider.api.recipients.index');
            Route::post('/recipients', StoreRecipientController::class)->name('rider.api.recipients.store');
            Route::patch('/recipients/{recipient}', UpdateRecipientController::class)->name('rider.api.recipients.update');
            Route::delete('/recipients/{recipient}', DeleteRecipientController::class)->name('rider.api.recipients.destroy');
            Route::get('/support-tickets', ListSupportTicketsController::class)->name('rider.api.support-tickets.index');
            Route::post('/support-tickets', StoreSupportTicketController::class)->name('rider.api.support-tickets.store');
            Route::patch('/support-tickets/{ticket}/archive', ArchiveSupportTicketController::class)->name('rider.api.support-tickets.archive');
            Route::get('/support-tickets/{ticket}/messages', ListSupportTicketMessagesController::class)->name('rider.api.support-tickets.messages.index');
            Route::post('/support-tickets/{ticket}/messages', StoreSupportTicketMessageController::class)->name('rider.api.support-tickets.messages.store');
            Route::get('/finance', ShowRiderFinanceController::class)->name('rider.api.finance');
            Route::get('/earnings', ListEarningsController::class)->name('rider.api.earnings');
            Route::post('/remittances', StoreRemittanceController::class)->name('rider.api.remittances.store');
            Route::get('/calculations', ListCalculationsController::class)->name('rider.api.calculations.index');
            Route::post('/calculations', StoreCalculationController::class)->name('rider.api.calculations.store');
            Route::delete('/calculations', ClearCalculationHistoryController::class)->name('rider.api.calculations.clear');
            Route::get('/verify-orders', ListVerifyOrdersController::class)->name('rider.api.verify-orders.index');
            Route::patch('/verify-orders/{order}/archive', ArchiveVerifyOrderController::class)->name('rider.api.verify-orders.archive');
            Route::delete('/verify-orders/{order}/archive', RestoreVerifyOrderController::class)->name('rider.api.verify-orders.restore');
            Route::delete('/verify-orders/{order}', DeleteArchivedVerifyOrderController::class)->name('rider.api.verify-orders.dismiss');
            Route::get('/pickup-tasks', ListPickupTasksController::class)->name('rider.api.pickup-tasks.index');
            Route::post('/pickup-tasks/{order}/proof', SubmitPickupProofController::class)->name('rider.api.pickup-tasks.proof');
            Route::get('/pickup-tasks/{order}/proofs/{proof}/photo', ShowPickupProofPhotoController::class)->name('rider.api.pickup-tasks.proofs.photo');
            Route::get('/pickup-delivery-records', ListPickupDeliveryRecordsController::class)->name('rider.api.pickup-delivery-records.index');
            Route::get('/deliveries', ListDeliveriesController::class)->name('rider.api.deliveries.index');
            Route::get('/deliveries/{order}', ShowDeliveryController::class)->name('rider.api.deliveries.show');
            Route::post('/deliveries/{order}/actions', CreateDeliveryActionController::class)->name('rider.api.deliveries.actions.store');
            Route::patch('/deliveries/{order}/status', UpdateDeliveryStatusController::class)->name('rider.api.deliveries.status');
            Route::post('/deliveries/{order}/proof', SubmitDeliveryProofController::class)->name('rider.api.deliveries.proof');
            Route::get('/deliveries/{order}/proofs/{proof}/photo', ShowDeliveryProofPhotoController::class)->name('rider.api.deliveries.proofs.photo');
        });

    });

});
