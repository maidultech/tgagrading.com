<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\Payment\StripeController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\PhotoController;

use App\Http\Controllers\TestController;
use App\Http\Controllers\UPSController;

// admin
use App\Http\Controllers\User\AddressController;

// user
use App\Http\Controllers\User\CardController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\ContactController;
use App\Http\Controllers\User\TicketController;
use App\Http\Controllers\User\TransactionController;
use App\Http\Controllers\User\UserDashboardController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('test-email', [HomeController::class, 'testEmail'])->name('test.email');
Route::post('/change-language', [HomeController::class, 'changeLanguage'])->name('change.language');
Route::post('/change-card-language', [HomeController::class, 'changeCardLanguage'])->name('change.card.language');

Route::get('check-user-planValidity', function () {
    try {
        Artisan::call('users:validity-check');
        return true;
    } catch (\Throwable $th) {
        return false;
    }
});

Route::get('/', [HomeController::class, 'index'])->name('frontend.index');
Route::get('/about', [HomeController::class, 'about'])->name('frontend.about');
Route::get('/contact', [HomeController::class, 'contact'])->name('frontend.contact');
Route::post('/contact', [HomeController::class, 'contactSubmit'])->name('frontend.contact.submit');
Route::get('/faq', [HomeController::class, 'faq'])->name('frontend.faq');
Route::get('/terms', [HomeController::class, 'terms'])->name('frontend.terms');
Route::get('/privacy', [HomeController::class, 'privacy'])->name('frontend.privacy');
Route::get('/grading-scale', [HomeController::class, 'gradingScale'])->name('frontend.grading-scale');
Route::get('/pricing', [HomeController::class, 'pricing'])->name('frontend.pricing');
Route::get('certification', [HomeController::class, 'certification'])->name('frontend.certification');

Route::get('partners', [HomeController::class, 'partners'])->name('frontend.partners');

Route::get('how-to-order', [HomeController::class, 'howToOrder'])->name('frontend.howToOrder');
Route::get('sitemap', [HomeController::class, 'sitemap'])->name('frontend.sitemap');
Route::get('/blogs', [HomeController::class, 'blogs'])->name('frontend.blogs');
Route::get('/blog/{slug}', [HomeController::class, 'blogDetails'])->name('frontend.blogs.details');
Route::get('/blogs/category/{slug}', [HomeController::class, 'blogsByCategory'])->name('frontend.blogs.category');
Route::get('/programs', [HomeController::class, 'programs'])->name('frontend.programs');
Route::get('/grading/{slug?}', [HomeController::class, 'cardGradingDetails'])->name('frontend.cardGradingDetails');
Route::get('/sports-card-grading', [HomeController::class, 'sportsCardGrading'])->name('frontend.sportsCardGrading');
Route::get('/trading-card-grading', [HomeController::class, 'cardGradingService'])->name('frontend.cardGradingService');
Route::get('/crossover-card-grading', [HomeController::class, 'crossoverGradingService'])->name('frontend.crossoverGradingService');

Route::get('sitemap.xml', [HomeController::class, 'sitemapXML'])->name('frontend.sitemap.xml');
Route::post('newsletter', [HomeController::class, 'newsletter'])->name('newsletter');
Route::get('auth/{provider}', [SocialLoginController::class, 'redirectToProvider'])->name('social.login');
Route::get('auth/{provider}/callback', [SocialLoginController::class, 'handleProviderCallback']);
Auth::routes([
    'verify' => true
]);

Route::get('/admin', function () {
    return redirect('/admin/login');
});
// Route::get('/{card_url}', [HomeController::class, 'getPreview'])->name('card.preview')->middleware(['analytics', 'setCardLanguage']);
Route::get('/{id}/shipment-tracking', [UserDashboardController::class, 'orderTracking'])->name('user.order.tracking');

Route::group(['middleware' => ['auth', 'verified', 'activeUser']], function () {
    Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
        Route::get('/', [UserDashboardController::class, 'index'])->name('dashboard');
        Route::get('/edit-account', [UserDashboardController::class, 'editProfile'])->name('edit.profile');
        Route::post('/profile-update', [UserDashboardController::class, 'profileUpdate'])->name('profile.update');
        Route::post('/change-password', [UserDashboardController::class, 'changePassword'])->name('change.password');

        Route::prefix('order')->group(function(){
            Route::get('/', [UserDashboardController::class, 'orders'])->name('orders');
            Route::get('/{id}/cards', [UserDashboardController::class, 'cards'])->name('cards');
            Route::get('{id}/edit', [CheckoutController::class, 'orderEdit'])->name('order.edit');
            Route::get('{id}/shipping-billing', [CheckoutController::class, 'orderBilling'])->name('order.billing');
            Route::get('{id}/shipping/rate', [UPSController::class, 'rate'])->name('order.shipping.rate');
            Route::post('{order}/apply-coupon', [CheckoutController::class, 'applyCoupon'])->name('order.apply.coupon');
            Route::post('/{order:order_number}/tracking-info/store', [UserDashboardController::class, 'orderTrackingInfoStore'])->name('order.trackingInfoStore');
            Route::get('/{id}/invoice', [UserDashboardController::class, 'orderInvoice'])->name('order.invoice');
            Route::get('/{id}/invoice/download', [UserDashboardController::class, 'orderInvoiceDownload'])->name('order.invoice.download');
            Route::get('/payment/{id}/invoice', [UserDashboardController::class, 'orderPaymentInvoice'])->name('order.payment.invoice');
            Route::get('/payment/{id}/invoice/download', [UserDashboardController::class, 'orderPaymentInvoiceDownload'])->name('order.payment.invoice.download');
            Route::post('update/shipping-method/{order}',[CheckoutController::class, 'updateShippingMethod'])->name('update.shipping.method');
        });

        Route::group(['as' => 'ticket.', 'prefix' => 'ticket'], function () {
            Route::get('/', [TicketController::class, 'index'])->name('index');
            Route::get('/create', [TicketController::class, 'create'])->name('create');
            Route::post('/store', [TicketController::class, 'store'])->name('store');
            Route::post('/reply/{id}/{author_id}', [TicketController::class, 'reply'])->name('reply');

            // Route::get('/show', [TicketController::class, 'ticketShow'])->name('show');
            Route::get('/show/{id}', [TicketController::class, 'ticketShow'])->name('show');


        });

        // Route::get('support', [UserDashboardController::class, 'support'])->name('support');
        // Route::get('support/create', [UserDashboardController::class, 'createSupport'])->name('support.create');
        // Route::post('support/store', [UserDashboardController::class, 'storeSupport'])->name('support.store');


        Route::post('/address-book/store', [AddressController::class, 'store'])->name('address.store');
        Route::get('/address-book/{id}/edit', [AddressController::class, 'edit'])->name('address.edit');
        Route::post('/address-book/{id}/update', [AddressController::class, 'update'])->name('address.update');
        Route::get('/address-book/{id}', [AddressController::class, 'destroy'])->name('address.delete');
        Route::get('/address-book/{id}/set-default', [AddressController::class, 'setDefault'])->name('address.setDefault');

        Route::get('/invoice/{id}', [UserDashboardController::class, 'showInvoice'])->name('invoice.show');

    });

    // Checkout
    Route::group(['prefix' => 'checkout', 'as' => 'checkout.'], function () {

        Route::get('{id}/item-type', [CheckoutController::class, 'itemType'])->name('item.type');
        Route::post('{id}/item-type/store', [CheckoutController::class, 'itemTypeStore'])->name('item.type.store');

        Route::get('{id}/submission-type', [CheckoutController::class, 'submissionType'])->name('submission.type');
        Route::post('{id}/submission-type/store', [CheckoutController::class, 'submissionTypeStore'])->name('submission.type.store');

        // Route::get('{id}/service-level', [CheckoutController::class, 'serviceLevel'])->name('service.level');
        // Route::post('{id}/service-level', [CheckoutController::class, 'serviceLevelStore'])->name('service.level.store');

        Route::get('{id}/item-entry', [CheckoutController::class, 'itemEntry'])->name('item.entry');
        Route::post('{id}/item-entry', [CheckoutController::class, 'itemEntryStore'])->name('item.entry.store');
        Route::middleware([
            'auth:user',
        ])->group(function () {
            Route::get('{id}/shipping-billing', [CheckoutController::class, 'shippingBilling'])->name('shipping.billing');

            Route::post('{id}/order', [PaymentController::class, 'shippingBilling'])->name('order.store');
            Route::post('{id}/order/address/update', [CheckoutController::class, 'addressUpdate'])->name('order.address.update');

            Route::get('{id}/{edit_flag}/confirmation/{order_number}', [CheckoutController::class, 'confirmation'])->name('confirmation');
            Route::get('{id}/plan-confirmation/{order_number}', [CheckoutController::class, 'planConfirmation'])->name('plan.confirmation');
        });

        Route::post('{plan}/plan/apply-coupon', [CheckoutController::class, 'applyPlanCoupon'])->name('plan.apply.coupon');
        Route::get('{id}/plan', [CheckoutController::class, 'planCheckout'])->name('plan');

    });

    // route
    Route::prefix('payment/')->name('payment.')->group(function () {
        Route::post('/', [PaymentController::class, 'payment'])->name('store');
        Route::post('/update', [PaymentController::class, 'updateOrder'])->name('update');
        Route::post('/plan', [PaymentController::class, 'planPayment'])->name('plan.store');
        Route::post('/order', [PaymentController::class, 'orderPayment'])->name('order.store');
        Route::prefix('stripe')->group(function () {
            // Route::get('webhook')->name('stripe.webhook');
        });

        // success/cancel
        Route::get('success/{provider}/{source?}', [PaymentController::class, 'success'])->name('success');
        Route::get('cancel/{provider}/{source?}', [PaymentController::class, 'cancel'])->name('cancel');

        Route::post('stripe/webhook', [StripeController::class, 'handleWebhook']);
    });


});

// for dev testing

Route::get('test',[TestController::class,'index'])->name('test');
Route::get('invoice/{id}', [HomeController::class, 'orderInvoice'])->name('order.invoice');
Route::get('/get-state-taxes/{state}', [HomeController::class, 'getStateTaxes']);
Route::get('/ready-shipping-email-alert', [HomeController::class, 'readyShippingReminders']);

Route::get('/refresh-csrf', function () {
    Session::regenerateToken();

    return response()->json([
        'token' => csrf_token(),
    ]);
})->name('refresh-csrf');
