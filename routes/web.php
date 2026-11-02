<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DemoAuthController;
use App\Http\Controllers\FpoDashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SupplierDashboardController;
use App\Models\Crop;
use App\Models\Manufacturer;
use Illuminate\Support\Facades\Route;

// Home (Database-Driven)
Route::get('/', [HomeController::class, 'index'])->name('home');

// Language Localization Switcher
Route::get('/locale/{lang}', function ($lang) {
    if (in_array($lang, ['en', 'hi', 'gu'])) {
        session(['locale' => $lang]);
    }

    return redirect()->back();
})->name('locale.set');

// Multi-Role Registration & Production Authentication
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::get('/register/{role}', [AuthController::class, 'showRegister'])->name('register.role');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

// Demo Role Authentication & Simulated OTP Flow (Active for testing & demonstration)
Route::post('/demo/switch-role', [DemoAuthController::class, 'switchRole'])->name('demo.switch-role');
Route::get('/login', [DemoAuthController::class, 'showLogin'])->name('login');
Route::post('/demo/send-otp', [DemoAuthController::class, 'simulateSendOtp'])->name('demo.send-otp');
Route::post('/demo/verify-otp', [DemoAuthController::class, 'simulateVerifyOtp'])->name('demo.verify-otp');
Route::post('/logout', [DemoAuthController::class, 'logout'])->name('logout');

use App\Http\Controllers\AdvisoryController;
use App\Http\Controllers\FarmerDashboardController;

// Crop Advisory Workflows (Database & AI Diagnostic Driven)
Route::get('/advisory', [AdvisoryController::class, 'index'])->name('advisory.index');
Route::post('/advisory', [AdvisoryController::class, 'store'])->name('advisory.store');
Route::get('/advisory/history', [AdvisoryController::class, 'history'])->name('advisory.history');
Route::get('/advisory/ticket/{ticket}', [AdvisoryController::class, 'show'])->name('advisory.show');
Route::post('/advisory/ticket/{ticket}/escalate', [AdvisoryController::class, 'escalate'])->name('advisory.escalate');

Route::get('/advisory/expert', function () {
    return view('pages.advisory-expert');
})->name('advisory.expert');

Route::get('/advisory/upload', function () {
    return view('pages.advisory-upload');
})->name('advisory.upload');

use App\Http\Controllers\WeatherController;

Route::get('/weather', [WeatherController::class, 'index'])->name('weather.index');

Route::get('/weather/stations', function () {
    return view('pages.weather-stations');
})->name('weather.stations');

Route::get('/crops/{slug}', function (string $slug) {
    $crop = Crop::where('slug', $slug)->firstOrFail();

    return view('pages.crop-detail', compact('crop'));
})->name('crops.show');

use App\Http\Controllers\MandiController;

Route::get('/satellite', function () {
    return view('pages.satellite-full');
})->name('satellite.index');

Route::get('/mandi', [MandiController::class, 'index'])->name('mandi.index');

use App\Http\Controllers\DistributorController;
use App\Http\Controllers\ProductController;

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

Route::get('/manufacturers', function () {
    $manufacturers = Manufacturer::withCount('products')->where('is_verified', true)->get();

    return view('pages.manufacturers-preview', compact('manufacturers'));
})->name('manufacturers.index');

Route::get('/distributors', [DistributorController::class, 'index'])->name('distributors.index');

Route::get('/about', function () {
    return view('pages.about');
})->name('about');

// Solutions Informational Routes (Client PDF 4 Core Pillars)
Route::get('/solutions', function () {
    return view('pages.solutions-overview');
})->name('solutions.index');

Route::get('/solutions/quality-grading', function () {
    return view('pages.solutions-quality-grading');
})->name('solutions.quality-grading');

Route::get('/solutions/precision-agriculture', function () {
    return view('pages.solutions-precision-agri');
})->name('solutions.precision-agri');

Route::get('/solutions/digital-farmer-platform', function () {
    return view('pages.solutions-digital-farmer');
})->name('solutions.digital-farmer');

Route::get('/solutions/integrated-marketplace', function () {
    return view('pages.solutions-marketplace');
})->name('solutions.marketplace');

// Marketplace Ecosystem Overview Route
Route::get('/marketplace', function () {
    return view('pages.marketplace-overview');
})->name('marketplace.index');

// Stakeholder Ecosystem Route
Route::get('/ecosystem', function () {
    return view('pages.ecosystem');
})->name('ecosystem.index');

// Planned Company Roadmap Route
Route::get('/roadmap', function () {
    return view('pages.roadmap');
})->name('roadmap.index');

// Role Protected Portals
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminGateOperationsController;
use App\Http\Controllers\AdminProduceController;
use App\Http\Controllers\BuyerMarketplaceController;
use App\Http\Controllers\BuyerRequirementController;
use App\Http\Controllers\FpoAggregationController;
use App\Http\Controllers\FpoApprovalController;
use App\Http\Controllers\FpoExceptionController;
use App\Http\Controllers\FpoGateEntryController;
use App\Http\Controllers\FpoGateExitController;
use App\Http\Controllers\FpoInventoryPostingController;
use App\Http\Controllers\FpoQualityTestingController;
use App\Http\Controllers\FpoReportController;
use App\Http\Controllers\FpoWeightmentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProduceLotController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\RfqController;
use App\Http\Controllers\SupplierProductController;

// 1. Farmer Portal & Profile Management
Route::middleware(['role:farmer'])->group(function () {
    Route::get('/dashboard', [FarmerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/farmer/dashboard', [FarmerDashboardController::class, 'index'])->name('farmer.dashboard');
    Route::post('/farmer/profile', [FarmerDashboardController::class, 'updateProfile'])->name('farmer.profile.update');
});

// 2. Supplier / Input Distributor Business Portal & Organization Management
Route::middleware(['role:supplier,distributor'])->prefix('supplier')->name('supplier.')->group(function () {
    Route::get('/dashboard', [SupplierDashboardController::class, 'index'])->name('dashboard');
    Route::post('/profile', [SupplierDashboardController::class, 'updateProfile'])->name('profile.update');
    Route::post('/members', [SupplierDashboardController::class, 'addMember'])->name('members.store');
    Route::delete('/members/{member}', [SupplierDashboardController::class, 'removeMember'])->name('members.destroy');

    // Supplier Product Catalogue & Inventory Management
    Route::get('/products', [SupplierProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [SupplierProductController::class, 'create'])->name('products.create');
    Route::post('/products', [SupplierProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [SupplierProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [SupplierProductController::class, 'update'])->name('products.update');
    Route::post('/products/{product}/stock', [SupplierProductController::class, 'updateStock'])->name('products.stock');
    Route::delete('/products/{product}', [SupplierProductController::class, 'destroy'])->name('products.destroy');
});

// 3. FPO / Cooperative Organization Management & Farmer Membership Portal
Route::middleware(['role:fpo,cooperative,admin'])->prefix('fpo')->name('fpo.')->group(function () {
    Route::get('/dashboard', [FpoDashboardController::class, 'index'])->name('dashboard');
    Route::post('/profile', [FpoDashboardController::class, 'updateProfile'])->name('profile.update');
    Route::post('/members', [FpoDashboardController::class, 'addFarmerMember'])->name('members.store');
    Route::post('/members/{membership}/status', [FpoDashboardController::class, 'updateMemberStatus'])->name('members.status');

    // FPO Collective Produce Aggregation & Bulk Sales
    Route::get('/collective', [FpoAggregationController::class, 'index'])->name('collective.index');
    Route::get('/collective/create', [FpoAggregationController::class, 'create'])->name('collective.create');
    Route::post('/collective', [FpoAggregationController::class, 'store'])->name('collective.store');
    Route::get('/collective/{listing}', [FpoAggregationController::class, 'show'])->name('collective.show');
    Route::post('/collective/{listing}/withdraw', [FpoAggregationController::class, 'withdraw'])->name('collective.withdraw');

    // FPO Gate Operations (Batch 3)
    Route::get('/gate-entries', [FpoGateEntryController::class, 'index'])->name('gate-entries.index');
    Route::get('/gate-entries/create', [FpoGateEntryController::class, 'create'])->name('gate-entries.create');
    Route::post('/gate-entries', [FpoGateEntryController::class, 'store'])->name('gate-entries.store');
    Route::get('/gate-entries/{entry}', [FpoGateEntryController::class, 'show'])->name('gate-entries.show');
    Route::post('/gate-entries/{entry}/status', [FpoGateEntryController::class, 'updateStatus'])->name('gate-entries.status');

    // Weightment Workflows & Reweighment
    Route::post('/gate-entries/{entry}/weightments', [FpoWeightmentController::class, 'store'])->name('weightments.store');
    Route::post('/gate-entries/{entry}/weightments/reweigh', [FpoWeightmentController::class, 'reweigh'])->name('weightments.reweigh');
    Route::post('/gate-entries/{entry}/weightments/{weightment}/resolve', [FpoWeightmentController::class, 'resolveDiscrepancy'])->name('weightments.resolve');
    Route::get('/gate-entries/{entry}/weightments/{weightment}/slip', [FpoWeightmentController::class, 'printSlip'])->name('weightments.slip');

    // Sample Collection & Quality Testing
    Route::post('/gate-entries/{entry}/samples', [FpoQualityTestingController::class, 'collectSample'])->name('samples.store');
    Route::post('/gate-entries/{entry}/samples/{sample}/tests', [FpoQualityTestingController::class, 'recordTest'])->name('tests.store');
    Route::post('/gate-entries/{entry}/submit-approval', [FpoQualityTestingController::class, 'submitForApproval'])->name('gate-entries.submit-approval');

    // Quality Approval & Sign-Off
    Route::post('/gate-entries/{entry}/approve', [FpoApprovalController::class, 'approve'])->name('approvals.store');

    // Inventory Posting & Reversal
    Route::post('/gate-entries/{entry}/inventory/post', [FpoInventoryPostingController::class, 'post'])->name('inventory.post');
    Route::post('/gate-entries/{entry}/inventory/{posting}/reversal', [FpoInventoryPostingController::class, 'reversal'])->name('inventory.reversal');
    Route::get('/reconciliation', [FpoInventoryPostingController::class, 'reconciliation'])->name('reconciliation');

    // Gate Exit & Outgoing Dispatch Authorization
    Route::get('/gate-exits', [FpoGateExitController::class, 'index'])->name('gate-exits.index');
    Route::get('/gate-exits/create', [FpoGateExitController::class, 'create'])->name('gate-exits.create');
    Route::post('/gate-exits', [FpoGateExitController::class, 'store'])->name('gate-exits.store');
    Route::post('/gate-exits/{exit}/confirm-exit', [FpoGateExitController::class, 'confirmActualExit'])->name('gate-exits.confirm-exit');
    Route::post('/gate-exits/{exit}/confirm-handover', [FpoGateExitController::class, 'confirmHandover'])->name('gate-exits.confirm-handover');

    // Operational Exceptions
    Route::get('/exceptions', [FpoExceptionController::class, 'index'])->name('exceptions.index');
    Route::post('/exceptions', [FpoExceptionController::class, 'store'])->name('exceptions.store');
    Route::post('/exceptions/{exception}/resolve', [FpoExceptionController::class, 'resolve'])->name('exceptions.resolve');

    // Individual Gate Entry Reports & PDF Export
    Route::get('/reports/{entry}', [FpoReportController::class, 'show'])->name('reports.show');
    Route::get('/reports/{entry}/pdf', [FpoReportController::class, 'downloadPdf'])->name('reports.pdf');
    Route::get('/reports/{entry}/print', [FpoReportController::class, 'printView'])->name('reports.print');
});

// Farmer Produce Trading & Quality Assessment Routes
Route::get('/sell-produce', [ProduceLotController::class, 'create'])->name('farmer.sell.create');
Route::post('/sell-produce', [ProduceLotController::class, 'store'])->name('farmer.sell.store');
Route::get('/farmer/lots', [ProduceLotController::class, 'index'])->name('farmer.lots.index');
Route::get('/farmer/lots/{lot}', [ProduceLotController::class, 'show'])->name('farmer.lots.show');
Route::get('/farmer/lots/{lot}/edit', [ProduceLotController::class, 'edit'])->name('farmer.lots.edit');
Route::put('/farmer/lots/{lot}', [ProduceLotController::class, 'update'])->name('farmer.lots.update');
Route::post('/farmer/lots/{lot}/withdraw', [ProduceLotController::class, 'withdraw'])->name('farmer.lots.withdraw');
Route::post('/farmer/lots/{lot}/submit', [ProduceLotController::class, 'submitForReview'])->name('farmer.lots.submit');

// Buyer Marketplace & Real-Time Notification Center Routes
Route::get('/buy-produce', [BuyerMarketplaceController::class, 'index'])->name('buyer.marketplace');
Route::get('/buy-produce/lot/{lot}', [BuyerMarketplaceController::class, 'show'])->name('buyer.lot.show');
Route::post('/buy-produce/lot/{lot}/reserve', [BuyerMarketplaceController::class, 'reserve'])->name('buyer.lot.reserve');
Route::post('/buy-produce/lot/{lot}/interest', [BuyerMarketplaceController::class, 'expressInterest'])->name('buyer.lot.interest');
Route::get('/buyer/dashboard', [BuyerMarketplaceController::class, 'dashboard'])->name('buyer.dashboard');
Route::get('/buyer/notifications', [BuyerMarketplaceController::class, 'notifications'])->name('buyer.notifications');
Route::get('/buyer/notifications/poll', [BuyerMarketplaceController::class, 'pollNotifications'])->name('buyer.notifications.poll');
Route::post('/buyer/notifications/{id}/read', [BuyerMarketplaceController::class, 'markNotificationRead'])->name('buyer.notifications.read');
Route::post('/buyer/notifications/mark-all-read', [BuyerMarketplaceController::class, 'markAllNotificationsRead'])->name('buyer.notifications.mark-all-read');

// Buyer Bulk Procurement Requirements Routes
Route::prefix('buyer/requirements')->name('buyer.requirements.')->group(function () {
    Route::get('/', [BuyerRequirementController::class, 'index'])->name('index');
    Route::get('/create', [BuyerRequirementController::class, 'create'])->name('create');
    Route::post('/', [BuyerRequirementController::class, 'store'])->name('store');
    Route::get('/{requirement}', [BuyerRequirementController::class, 'show'])->name('show');
    Route::post('/{requirement}/cancel', [BuyerRequirementController::class, 'destroy'])->name('cancel');
});

// Request for Quotation (RFQ) Routes
Route::get('/rfqs', [RfqController::class, 'index'])->name('rfqs.index');
Route::get('/rfqs/seller', [RfqController::class, 'index'])->name('rfqs.seller');
Route::get('/rfqs/create', [RfqController::class, 'create'])->name('rfqs.create');
Route::post('/rfqs', [RfqController::class, 'store'])->name('rfqs.store');
Route::get('/rfqs/{rfq}', [RfqController::class, 'show'])->name('rfqs.show');
Route::get('/rfqs/{rfq}/compare', [RfqController::class, 'compare'])->name('rfqs.compare');
Route::post('/rfqs/{rfq}/close', [RfqController::class, 'close'])->name('rfqs.close');

// Quotation Submission, Viewing & Acceptance Routes
Route::get('/rfqs/{rfq}/quote', [QuotationController::class, 'create'])->name('quotations.create');
Route::post('/rfqs/{rfq}/quote', [QuotationController::class, 'store'])->name('quotations.store');
Route::get('/quotations/{quotation}', [QuotationController::class, 'show'])->name('quotations.show');
Route::post('/quotations/{quotation}/accept', [QuotationController::class, 'accept'])->name('quotations.accept');
Route::post('/quotations/{quotation}/withdraw', [QuotationController::class, 'withdraw'])->name('quotations.withdraw');

// Purchase Order Routes
Route::get('/orders', [PurchaseOrderController::class, 'index'])->name('orders.index');
Route::get('/orders/{order}', [PurchaseOrderController::class, 'show'])->name('orders.show');
Route::post('/orders/{order}/status', [PurchaseOrderController::class, 'updateStatus'])->name('orders.status');
Route::post('/orders/{order}/cancel', [PurchaseOrderController::class, 'cancel'])->name('orders.cancel');

// Unified Multi-Role Notification Center Routes
Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
Route::get('/notifications/poll', [NotificationController::class, 'poll'])->name('notifications.poll');
Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::post('/advisories/{advisory}/status', [AdminController::class, 'updateAdvisoryStatus'])->name('advisories.status');

    // User Management & Account Suspension / Activation
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users/{user}/status', [AdminController::class, 'updateUserStatus'])->name('users.status');

    // Supplier Organization Verification & Management
    Route::get('/suppliers', [AdminController::class, 'suppliers'])->name('suppliers');
    Route::post('/suppliers/{supplier}/review', [AdminController::class, 'reviewSupplier'])->name('suppliers.review');

    // FPO Cooperative Organization Verification & Management
    Route::get('/fpos', [AdminController::class, 'fpos'])->name('fpos');
    Route::post('/fpos/{fpo}/review', [AdminController::class, 'reviewFpo'])->name('fpos.review');

    // Product Management CRUD
    Route::get('/products', [AdminController::class, 'products'])->name('products');
    Route::post('/products', [AdminController::class, 'storeProduct'])->name('products.store');
    Route::put('/products/{product}', [AdminController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{product}', [AdminController::class, 'destroyProduct'])->name('products.destroy');

    // Distributor Management CRUD
    Route::get('/distributors', [AdminController::class, 'distributors'])->name('distributors');
    Route::post('/distributors', [AdminController::class, 'storeDistributor'])->name('distributors.store');
    Route::put('/distributors/{distributor}', [AdminController::class, 'updateDistributor'])->name('distributors.update');
    Route::delete('/distributors/{distributor}', [AdminController::class, 'destroyDistributor'])->name('distributors.destroy');

    // Produce Quality Grading & Procurement Management
    Route::get('/produce', [AdminProduceController::class, 'index'])->name('produce.index');
    Route::get('/produce/{lot}', [AdminProduceController::class, 'show'])->name('produce.show');
    Route::post('/produce/{lot}/override-grade', [AdminProduceController::class, 'overrideGrade'])->name('produce.override-grade');
    Route::post('/produce/{lot}/approve-procurement', [AdminProduceController::class, 'approveProcurement'])->name('produce.approve-procurement');
    Route::post('/produce/{lot}/confirm-purchase', [AdminProduceController::class, 'confirmPurchase'])->name('produce.confirm-purchase');
    Route::post('/produce/{lot}/publish', [AdminProduceController::class, 'publishInventory'])->name('produce.publish');
    Route::post('/produce/{lot}/reject', [AdminProduceController::class, 'reject'])->name('produce.reject');
    Route::get('/produce-reservations', [AdminProduceController::class, 'reservations'])->name('produce.reservations');
    Route::get('/produce-notifications', [AdminProduceController::class, 'notificationHistory'])->name('produce.notifications');

    // Cross-Organization Gate Operations Oversight
    Route::get('/gate-operations', [AdminGateOperationsController::class, 'index'])->name('gate-operations.index');
});
