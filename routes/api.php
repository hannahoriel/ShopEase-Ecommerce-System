<?php

use App\Http\Controllers\Admin\CommissionController;
use App\Http\Controllers\Admin\LogisticsManagementController;
use App\Http\Controllers\Admin\RegistrationController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Api\Admin\ComplaintController as AdminComplaintController;
use App\Http\Controllers\Api\Admin\MessagesController as AdminMessagesController;
use App\Http\Controllers\Api\Admin\SellerComplianceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Buyer\CartController as BuyerCartController;
use App\Http\Controllers\Api\Buyer\DashboardController as BuyerDashboardController;
use App\Http\Controllers\Api\Buyer\MessagesController as BuyerMessagesController;
use App\Http\Controllers\Api\Buyer\OrderController as BuyerOrderController;
use App\Http\Controllers\Api\Buyer\ProductReviewController;
use App\Http\Controllers\Api\Seller\CustomerFeedbackController;
use App\Http\Controllers\Api\Seller\DashboardController as SellerDashboardController;
use App\Http\Controllers\Api\Seller\InventoryController as SellerInventoryController;
use App\Http\Controllers\Api\Seller\MessagesController as SellerMessagesController;
use App\Http\Controllers\Api\Seller\OrderStatusController;
use App\Http\Controllers\Api\Seller\ReportsController as SellerReportsController;
use App\Http\Controllers\Api\Seller\ShippingStatusController;
use App\Http\Controllers\LocationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::get('/orders/verify/{orderNumber}', [BuyerOrderController::class, 'verify'])->name('buyer.api.orders.verify');

    Route::prefix('locations')->group(function () {
        Route::get('/provinces', [LocationController::class, 'provinces']);
        Route::get('/provinces/{code}/cities', [LocationController::class, 'municipalities']);
        Route::get('/cities/{code}/barangays', [LocationController::class, 'barangays']);
    });

    Route::middleware('auth:sanctum')->group(function () {

        Route::get('/auth/me', fn (Request $request) => response()->json($request->user()));
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/dashboard', fn (Request $request) => response()->json([
            'user' => $request->user(),
            'role' => $request->user()->role,
        ]));

        Route::middleware('role:admin')->prefix('admin')->group(function () {
            Route::get('/messages', [AdminMessagesController::class, 'index'])->name('admin.api.messages.index');
            Route::get('/messages/contacts', [AdminMessagesController::class, 'contacts'])->name('admin.api.messages.contacts');
            Route::post('/messages/conversations', [AdminMessagesController::class, 'storeConversation'])->name('admin.api.messages.conversations.store');
            Route::get('/messages/conversations/{conversation}', [AdminMessagesController::class, 'show'])->name('admin.api.messages.conversations.show');
            Route::post('/messages/conversations/{conversation}/messages', [AdminMessagesController::class, 'send'])->name('admin.api.messages.send');
            Route::get('/messages/conversations/{conversation}/messages/{message}/attachment', [AdminMessagesController::class, 'attachment'])
                ->name('admin.api.messages.attachment');
            Route::get('/commissions', [CommissionController::class, 'index'])->name('admin.api.commissions.index');
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
            Route::get('/registrations', [RegistrationController::class, 'list']);
            Route::get('/registrations/approved', [RegistrationController::class, 'approvedArchive']);
            Route::get('/registrations/rejected', [RegistrationController::class, 'rejectedArchive']);
            Route::get('/registrations/{registration}', [RegistrationController::class, 'show']);
            Route::post('/registrations/{registration}/approve', [RegistrationController::class, 'approve']);
            Route::post('/registrations/{registration}/reject', [RegistrationController::class, 'reject']);
            Route::get('/users', [UserManagementController::class, 'list']);
            Route::get('/users/{user}', [UserManagementController::class, 'show']);
            Route::patch('/users/{user}/status', [UserManagementController::class, 'updateStatus']);

            Route::prefix('seller-compliance')->controller(SellerComplianceController::class)->group(function () {
                Route::get('', 'index');
                Route::get('{seller}', 'show');
                Route::post('{seller}/suspend', 'suspend');
                Route::post('products/{product}/approve', 'approveProduct');
                Route::post('products/{product}/warn', 'warnProduct');
                Route::post('products/{product}/remove', 'removeProduct');
            });
        });

        Route::middleware('role:buyer')->prefix('buyer')->group(function () {
            Route::get('/dashboard/announcements', [BuyerDashboardController::class, 'announcements'])->name('buyer.api.dashboard.announcements');
            Route::get('/messages', [BuyerMessagesController::class, 'index'])->name('buyer.api.messages.index');
            Route::post('/messages/conversations', [BuyerMessagesController::class, 'storeConversation'])->name('buyer.api.messages.conversations.store');
            Route::get('/messages/conversations/{conversation}', [BuyerMessagesController::class, 'show'])->name('buyer.api.messages.conversations.show');
            Route::post('/messages/conversations/{conversation}/messages', [BuyerMessagesController::class, 'send'])->name('buyer.api.messages.send');
            Route::get('/orders', [BuyerOrderController::class, 'index'])->name('buyer.api.orders.index');
            Route::get('/dashboard/products', [BuyerDashboardController::class, 'products'])->name('buyer.api.dashboard.products');
            Route::get('/products/{product}', [BuyerDashboardController::class, 'show'])->name('buyer.api.products.show');
            Route::get('/products/{product}/reviews', [ProductReviewController::class, 'index'])->name('buyer.api.products.reviews.index');
            Route::post('/products/{product}/reviews', [ProductReviewController::class, 'store'])->name('buyer.api.products.reviews.store');
            Route::post('/orders', [BuyerOrderController::class, 'store'])->name('buyer.api.orders.store');
            Route::post('/orders/{order}/complaints', [BuyerOrderController::class, 'storeComplaint'])->name('buyer.api.orders.complaints.store');

            // Cart
            Route::get('/cart', [BuyerCartController::class, 'index'])->name('buyer.api.cart.index');
            Route::post('/cart', [BuyerCartController::class, 'store'])->name('buyer.api.cart.store');
            Route::patch('/cart/{item}', [BuyerCartController::class, 'update'])->name('buyer.api.cart.update');
            Route::delete('/cart/{item}', [BuyerCartController::class, 'destroy'])->name('buyer.api.cart.destroy');
            Route::delete('/cart', [BuyerCartController::class, 'clear'])->name('buyer.api.cart.clear');
        });

        Route::middleware('role:seller')->prefix('seller')->group(function () {
            Route::get('/dashboard', [SellerDashboardController::class, 'apiIndex']);
            Route::get('/messages', [SellerMessagesController::class, 'index'])->name('seller.api.messages.index');
            Route::get('/messages/contacts', [SellerMessagesController::class, 'contacts'])->name('seller.api.messages.contacts');
            Route::post('/messages/conversations', [SellerMessagesController::class, 'storeConversation'])->name('seller.api.messages.conversations.store');
            Route::get('/messages/conversations/{conversation}', [SellerMessagesController::class, 'show'])->name('seller.api.messages.conversations.show');
            Route::post('/messages/conversations/{conversation}/messages', [SellerMessagesController::class, 'send'])->name('seller.api.messages.send');
            Route::get('/messages/conversations/{conversation}/messages/{message}/attachment', [SellerMessagesController::class, 'attachment'])->name('seller.api.messages.attachment');
            Route::get('/reports', [SellerReportsController::class, 'data'])->name('seller.api.reports');
            Route::get('/feedback', [CustomerFeedbackController::class, 'index'])->name('seller.api.feedback.index');
            Route::get('/feedback/products/{product}', [CustomerFeedbackController::class, 'showProduct'])->name('seller.api.feedback.products.show');
            Route::get('/order-status', [OrderStatusController::class, 'index']);
            Route::get('/orders/{order}', [OrderStatusController::class, 'show']);
            Route::patch('/orders/{order}/status', [OrderStatusController::class, 'update']);
            Route::post('/orders/{order}/schedule', [OrderStatusController::class, 'schedule']);
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

    });

});
