<?php

use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UPSController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\GeneralController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\CardController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CanadaPostController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\StateController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\RegionController;
use App\Http\Controllers\Admin\WhyTgaController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\ScannerController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ItemBrandController;
use App\Http\Controllers\Admin\CustomPageController;
use App\Http\Controllers\Admin\NewsLetterController;
use App\Http\Controllers\Admin\UserWalletController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\HomeContentController;
use App\Http\Controllers\Admin\LogsHistoryController;
use App\Http\Controllers\Admin\ManualLabelController;
use App\Http\Controllers\Admin\PermissionsController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\FinalGradingController;
use App\Http\Controllers\Admin\GradingScaleController;
use App\Http\Controllers\Admin\ImageContentController;
use App\Http\Controllers\Admin\ServiceLevelController;
use App\Http\Controllers\Admin\SupportTicketController;
use App\Http\Controllers\Admin\BusinessPartnerController;
use App\Http\Controllers\Admin\OrderCertificateController;
use App\Http\Controllers\Admin\Auth\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\LanguageController as AdminLanguageController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;




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



//====================Admin Authentication=========================


Route::get('admin/login', [AdminLoginController::class, 'showLoginForm'])->name('login.admin');
Route::post('admin/login', [AdminLoginController::class, 'login'])->name('admin.login');
Route::get('admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

Route::group(['as' => 'admin.', 'prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => ['auth:admin'], 'where' => ['locale' => '[a-zA-Z]{2}']], function () {

    Route::get('/', ['as' => 'dashboard', 'uses' => 'DashboardController@dashboard']);
    Route::post('/qr-generator', [DashboardController::class, 'qrGenerate'])->name('qr.generator');

    Route::get('/cc', 'DashboardController@cacheClear')->name('cacheClear');

    Route::post('/gradingCalculation', 'DashboardController@gradingCalculation')->name('gradingCalculation');

    Route::get('/view', [NewsLetterController::class, 'index'])->name('newsletter.list');


    Route::get('/sc', [SettingsController::class, 'setview'])->name('settings.MobileApp.index');
    Route::post('/sc/update', [SettingsController::class, 'MobileAppUpdate'])->name('settings.MobileApp.update');

    Route::get('/currency', [CurrencyController::class, 'currenview'])->name('settings.Currency.index');

    // Route::get('/general',[GeneralController::class,'genview'])->name('settings.General.general');
    Route::get('/smtp', [MailController::class, 'mailview'])->name('settings.Smtp.mail');
    Route::post('/smtp/update', [SettingsController::class, 'SmtpUpdate'])->name('settings.smtp.update');
    Route::post('/test-mail', [MailController::class, 'testMail'])->name('settings.test.mail');



    // Route::get('settings', ['as' => 'settings', 'uses' => 'SettingsController@settings']);


    //Admin Setting
    Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
        Route::get('general', [AdminSettingsController::class, 'general'])->name('general');
        Route::post('general/store', [AdminSettingsController::class, 'generalStore'])->name('general_store');
        /*Route::get('certificate', [AdminSettingsController::class, 'certificate'])->name('certificate');
        Route::post('certificate/store', [AdminSettingsController::class, 'certificateStore'])->name('certificate_store');*/
        Route::get('languages', [AdminLanguageController::class, 'index'])->name('language');
        Route::post('language/store', [AdminLanguageController::class, 'store'])->name('language.store');
        Route::get('language/{id}/edit', [AdminLanguageController::class, 'edit'])->name('language.edit');
        Route::post('language/{id}/update', [AdminLanguageController::class, 'update'])->name('language.update');
        Route::get('language/{id}/delete', [AdminLanguageController::class, 'delete'])->name('language.delete');
        Route::get('home-content', [HomeContentController::class, 'index'])->name('home.content');
        Route::post('home-content/update', [HomeContentController::class, 'update'])->name('homeContent.update');
    });

    // Partner
    Route::group(['prefix' => 'partner', 'as' => 'partner.'], function () {
        Route::get('/', [PartnerController::class, 'index'])->name('index');
    //  Route::get('create', [PartnerController::class, 'create'])->name('create');
    //  Route::post('store', [PartnerController::class, 'store'])->name('store');
        Route::get('{id}/edit', [PartnerController::class, 'edit'])->name('edit')->can('admin.partner.edit');
        Route::post('{id}/update', [PartnerController::class, 'update'])->name('update')->can('admin.partner.edit');
    //  Route::get('{id}/delete', [PartnerController::class, 'delete'])->name('delete');
    });

    // Business Partner
    Route::group(['prefix' => 'business-partner', 'as' => 'business-partner.'], function () {
        Route::get('/', [BusinessPartnerController::class, 'index'])->name('index');
        Route::get('create', [BusinessPartnerController::class, 'create'])->name('create');
        Route::post('store', [BusinessPartnerController::class, 'store'])->name('store');
        Route::get('{id}/edit', [BusinessPartnerController::class, 'edit'])->name('edit');
        Route::post('{id}/update', [BusinessPartnerController::class, 'update'])->name('update');
        Route::get('{id}/delete', [BusinessPartnerController::class, 'delete'])->name('delete');
    
    });

    // certificate
    Route::group(['prefix' => 'card/certificate', 'as' => 'certificate.'], function () {
        Route::get('/', [CertificateController::class, 'index'])->name('index');
        Route::get('create', [CertificateController::class, 'create'])->name('create');
        Route::post('store', [CertificateController::class, 'store'])->name('store');
        Route::get('{id}/edit', [CertificateController::class, 'edit'])->name('edit');
        //            Route::post('{id}/update', [CertificateController::class, 'update'])->name('update');
        Route::get('{id}/delete', [CertificateController::class, 'delete'])->name('delete');
    });

    // Final Grading Name


    Route::prefix('final-grading')->name('finalgrading.')->group(function () {
        Route::get('/', [FinalGradingController::class, 'index'])->name('index');
        Route::post('/update', [FinalGradingController::class, 'update'])->name('update');
    });


    // Manual Label
    Route::group(['prefix' => 'manual-label', 'as' => 'manual-label.'], function () {
        Route::get('/', [ManualLabelController::class, 'index'])->name('index');
        Route::get('create', [ManualLabelController::class, 'create'])->name('create');
        Route::post('store', [ManualLabelController::class, 'store'])->name('store');
        Route::get('{id}/view', [ManualLabelController::class, 'view'])->name('view');
        Route::get('{id}/download', [ManualLabelController::class, 'download'])->name('download');
        Route::get('{id}/edit', [ManualLabelController::class, 'edit'])->name('edit');
        Route::post('{id}/update', [ManualLabelController::class, 'update'])->name('update');
        Route::get('{id}/delete', [ManualLabelController::class, 'delete'])->name('delete');
        Route::post('upload/image', [ManualLabelController::class, 'manualScanAndSave'])->name('upload.image');
        Route::post('upload/scanned-image', [ScannerController::class, 'manualLabelScan'])->name('upload.scanned.image');
        Route::post('upload/scanned-image-chunk', [ScannerController::class, 'manualUploadChunk'])->name('upload.scanned.image.chunk');
    });




    Route::get('ajax/text-editor/image', ['as' => 'text-editor.image', 'uses' => 'CustomPageController@postEditorImageUpload']);
    //Custom Page
    Route::group(['prefix' => 'cpage', 'as' => 'cpage.'], function () {
        Route::get('/', [CustomPageController::class, 'index'])->name('index');
        // Route::get('create', [CustomPageController::class, 'create'])->name('create');
        // Route::post('store', [CustomPageController::class, 'store'])->name('store');
        Route::get('{id}/view', [CustomPageController::class, 'view'])->name('view');
        Route::get('{id}/edit', [CustomPageController::class, 'edit'])->name('edit');
        Route::post('{id}/update', [CustomPageController::class, 'update'])->name('update');
        // Route::get('{id}/delete', [CustomPageController::class, 'getDelete'])->name('delete');
    });


    //Faq
    Route::group(['prefix' => 'faq', 'as' => 'faq.'], function () {
        Route::get('/', [FaqController::class, 'index'])->name('index');
        Route::get('create', [FaqController::class, 'create'])->name('create');
        Route::post('store', [FaqController::class, 'store'])->name('store');
        Route::get('{id}/view', [FaqController::class, 'view'])->name('view');
        Route::get('{id}/edit', [FaqController::class, 'edit'])->name('edit');
        Route::post('{id}/update', [FaqController::class, 'update'])->name('update');
        Route::get('{id}/delete', [FaqController::class, 'delete'])->name('delete');
    });

    //Coupon
    Route::group(['prefix' => 'coupon', 'as' => 'coupon.'], function () {
        Route::get('/', [CouponController::class, 'index'])->name('index');
        Route::get('create', [CouponController::class, 'create'])->name('create');
        Route::post('store', [CouponController::class, 'store'])->name('store');
        Route::get('{id}/edit', [CouponController::class, 'edit'])->name('edit');
        Route::post('{id}/update', [CouponController::class, 'update'])->name('update');
        Route::get('{id}/delete', [CouponController::class, 'delete'])->name('delete');
    });

    //User Wallet
    Route::group(['prefix' => 'wallet', 'as' => 'wallet.'], function () {
        Route::get('/', [UserWalletController::class, 'index'])->name('index');
        // Route::get('create', [UserWalletController::class, 'create'])->name('create');
        // Route::post('store', [UserWalletController::class, 'store'])->name('store');
        Route::get('{id}/details', [UserWalletController::class, 'details'])->name('details');
        Route::get('{id}/balance', [UserWalletController::class, 'balance'])->name('balance');
        Route::post('{id}/update', [UserWalletController::class, 'update'])->name('update');
    });

    // Grading Scale
    Route::group(['prefix' => 'grading-scale', 'as' => 'grading-scale.'], function () {
        Route::get('/', [GradingScaleController::class, 'index'])->name('index');
        Route::get('create', [GradingScaleController::class, 'create'])->name('create');
        Route::post('store', [GradingScaleController::class, 'store'])->name('store');
        Route::get('{id}/view', [GradingScaleController::class, 'view'])->name('view');
        Route::get('{id}/edit', [GradingScaleController::class, 'edit'])->name('edit');
        Route::post('{id}/update', [GradingScaleController::class, 'update'])->name('update');
        Route::get('{id}/delete', [GradingScaleController::class, 'delete'])->name('delete');
    });

    //Testimonial
    Route::group(['prefix' => 'testimonial', 'as' => 'testimonial.'], function () {
        Route::get('/', 'TestimonialController@index')->name('index');
        Route::get('/create', 'TestimonialController@create')->name('create');
        Route::post('/store', 'TestimonialController@store')->name('store');
        Route::get('{id}/edit', 'TestimonialController@edit')->name('edit');
        Route::post('{id}/update', 'TestimonialController@update')->name('update');
        // Route::get('{id}/delete', 'TestimonialController@delete')->name('delete');
        Route::get('{id}/delete', 'TestimonialController@delete')->name('delete');
        Route::get('{id}/view', 'TestimonialController@view')->name('view');
    });

    // Account Setting
    // Route::get('account', ['as'=>'account','uses'=>'AccountController@account']);
    // Route::get('edit-account', ['as'=>'edit.account','uses'=>'AccountController@editAccount']);
    // Route::post('update-account', ['as'=>'update.account','uses'=>'AccountController@updateAccount']);
    // Route::get('change-password', ['as'=>'change.password','uses'=>'AccountController@changePassword']);
    // Route::post('update-password', ['as'=>'update.password','uses'=>'AccountController@updatePassword']);


    // Setting
    Route::get('pages', [AdminSettingsController::class, 'pages'])->name('pages');
    Route::get('page/{home}', [AdminSettingsController::class, 'editHomePage'])->name('edit.home');
    Route::post('page/{home}/update', [AdminSettingsController::class, 'updateHomePage'])->name('update.home');

    Route::get('settings', [AdminSettingsController::class, 'settings'])->name('settings');
    Route::post('change-settings', [AdminSettingsController::class, 'changeSettings'])->name('change.settings');
    Route::get('tax-setting', [AdminSettingsController::class, 'taxSetting'])->name('tax.setting');
    Route::post('update-tex-setting', [AdminSettingsController::class, 'updateTaxSetting'])->name('update.tax.setting');
    Route::post('update-email-setting', [AdminSettingsController::class, 'updateEmailSetting'])->name('update.email.setting');



    // Users
    Route::get('roles', [RolesController::class, 'index'])->name('roles.index');
    Route::get('roles/create', [RolesController::class, 'create'])->name('roles.create');
    Route::post('roles/store', [RolesController::class, 'store'])->name('roles.store');
    Route::get('roles/{id}/show', [RolesController::class, 'show'])->name('roles.show');
    Route::get('roles/{id}/edit', [RolesController::class, 'edit'])->name('roles.edit');
    Route::post('roles/{id}/update', [RolesController::class, 'update'])->name('roles.update');
    // Route::delete('roles/{id}/destroy', [RolesController::class, 'destroy'])->name('roles.destroy');


    // admins
    Route::get('admins', [UserController::class, 'index'])->name('user.index');
    Route::get('/create', [UserController::class, 'create'])->name('user.create');
    Route::post('/store', [UserController::class, 'store'])->name('user.store');
    Route::get('/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::get('/{id}/password-edit', [UserController::class, 'passwordEdit'])->name('user.password.edit');
    Route::post('/{id}/password-update', [UserController::class, 'passwordUpdate'])->name('user.password.update');
    Route::post('/{id}/update', [UserController::class, 'update'])->name('user.update');
    // Route::get('/{id}/destroy', [UserController::class, 'destroy'])->name('user.destroy');

    // Route::resource('roles', RolesController::class);
    // Route::resource('permissions', PermissionsController::class);

    Route::get('edit-user/{id}', [UserController::class, 'editUser'])->name('edit.user');
    Route::post('update-user', [UserController::class, 'updateUser'])->name('update.user');
    Route::get('view-user/{id}', [UserController::class, 'viewUser'])->name('view.user');
    Route::get('change-user-plan/{id}', [UserController::class, 'ChangeUserPlan'])->name('change.user.plan');
    Route::post('update-user-plan', [UserController::class, 'UpdateUserPlan'])->name('update.user.plan');
    Route::get('update-status', [UserController::class, 'updateStatus'])->name('update.status');
    Route::get('active-user/{id}', [UserController::class, 'activeStatus'])->name('update.active-user');
    Route::get('delete-user', [UserController::class, 'deleteUser'])->name('delete.user');
    Route::get('login-as/{id}', [UserController::class, 'authAs'])->name('login-as.user');
    Route::get('user/trash-list', [UserController::class, 'getTrashList'])->name('user.trash-list');


    // Customers
    Route::group(['prefix' => 'customer', 'as' => 'customer.'], function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('create', [CustomerController::class, 'create'])->name('create');
        Route::post('store', [CustomerController::class, 'store'])->name('store');
        Route::get('{id}/edit', [CustomerController::class, 'edit'])->name('edit');
        Route::post('{id}/update', [CustomerController::class, 'update'])->name('update');
        Route::get('{id}/view', [CustomerController::class, 'view'])->name('view');
        Route::get('{id}/plan', [CustomerController::class, 'getPlan'])->name('plan');
        Route::get('{id}/delete', [CustomerController::class, 'delete'])->name('delete');
        Route::post('update-password', [CustomerController::class, 'updatePassword'])->name('password.change');
        Route::post('update-plan', [CustomerController::class, 'changePlan'])->name('plan.change');
        Route::get('{id}/disable', [CustomerController::class, 'disable'])->name('disable');
        Route::get('{id}/login', [CustomerController::class, 'authAs'])->name('login');
        Route::post('cancel-subscription', [CustomerController::class, 'cancelSubscription'])->name('cancel.subscription');
    });

    // Order
    Route::group(['prefix' => 'order', 'as' => 'order.'], function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/subscriptions', [OrderController::class, 'subscriptions'])->name('subscriptions');
        Route::get('create', [OrderController::class, 'create'])->name('create');
        Route::post('store', [OrderController::class, 'store'])->name('store');
        Route::get('{id}/edit', [OrderController::class, 'edit'])->name('edit');
        Route::get('{id}/view', [OrderController::class, 'view'])->name('view');
        Route::get('/{id}/download', [OrderController::class, 'download'])->name('download');
        Route::get('{id}/delete', [OrderController::class, 'delete'])->name('delete');

        Route::post('custom/{id}/update', [OrderController::class, 'updateCustomOrder'])->name('custom.update');
        Route::post('{id}/update', [OrderController::class, 'update'])->name('update');

        Route::get('{id}/view', [OrderController::class, 'view'])->name('view');
        Route::get('{id}/delete', [OrderController::class, 'delete'])->name('delete');
        Route::get('{id}/shipping-method', [OrderController::class, 'shippingMethod'])->name('shipping.method');
        Route::post('{id}/create-label/canada-post', [CanadaPostController::class, 'createShipment'])->name('create.canadaPost.label');
        Route::post('{id}/create-label/ups', [UPSController::class, 'createShipment'])->name('create.ups.label');

        // Order Certification

        Route::prefix('{order}/certificate')->name('certificate.')->group(function () {
            Route::get('/', [OrderCertificateController::class, 'index'])->name('index');
            Route::post('/update', [OrderCertificateController::class, 'update'])->name('update');
            Route::get('/{id}/delete', [OrderCertificateController::class, 'delete'])->name('delete');
            Route::get('{id}/get-label', [OrderCertificateController::class, 'getLabel'])->name('label');
            Route::post('download/pdf', [OrderCertificateController::class, 'makePDF'])->name('labelbulk');
            Route::post('upload/image', [OrderCertificateController::class, 'manualScanAndSave'])->name('upload.image');
            Route::post('upload/scanned-image', [ScannerController::class, 'scan'])->name('upload.scanned.image');
            Route::post('upload/scanned-image-chunk', [ScannerController::class, 'uploadChunk'])->name('upload.scanned.image.chunk');
            Route::post('card-create', [OrderCertificateController::class, 'cardStore'])->name('card.create');

        });
        Route::post('{id}/address-update', [OrderController::class, 'updateAddress'])->name('address.update');
    });


    Route::get('/outgoing-order', [OrderController::class, 'outGoing'])->name('outgoing.order');
    Route::get('/outgoing-order/{id}/view', [OrderController::class, 'outGoingView'])->name('outgoing.view');
    // admin profile
    Route::get('profile', [DashboardController::class, 'adminProfile'])->name('profile');
    Route::get('profile-edit', [DashboardController::class, 'profileEdit'])->name('profile.edit');
    Route::post('profile-update', [DashboardController::class, 'profileUpdate'])->name('profile.update');
    Route::post('password-update', [DashboardController::class, 'passwordUpdate'])->name('password.update');

    //Blog State
    Route::group(['prefix' => 'state', 'as' => 'state.'], function () {
        Route::get('/', [StateController::class, 'index'])->name('index');
        Route::post('/store', [StateController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [StateController::class, 'edit'])->name('edit');
        Route::post('/{id}/update', [StateController::class, 'update'])->name('update');
        Route::get('/{id}/delete', [StateController::class, 'delete'])->name('delete');
    });


    //Blog Category
    Route::group(['prefix' => 'blog-category', 'as' => 'blog-category.'], function () {
        Route::get('/', [BlogCategoryController::class, 'index'])->name('index');
        Route::post('/store', [BlogCategoryController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [BlogCategoryController::class, 'edit'])->name('edit');
        Route::post('/{id}/update', [BlogCategoryController::class, 'update'])->name('update');
        Route::get('/{id}/delete', [BlogCategoryController::class, 'delete'])->name('delete');
    });

    //Blog Post
    Route::group(['prefix' => 'blog-post', 'as' => 'blog-post.'], function () {
        Route::get('/', [BlogPostController::class, 'index'])->name('index');
        Route::get('create', [BlogPostController::class, 'create'])->name('create');
        Route::post('store', [BlogPostController::class, 'store'])->name('store');
        Route::get('{id}/edit', [BlogPostController::class, 'edit'])->name('edit');
        Route::post('{id}/update', [BlogPostController::class, 'update'])->name('update');
        Route::get('{id}/view', [BlogPostController::class, 'view'])->name('view');
        Route::get('{id}/delete', [BlogPostController::class, 'delete'])->name('delete');
    });

    //Contact
    Route::group(['prefix' => 'contact', 'as' => 'contact.'], function () {
        Route::get('/', [ContactController::class, 'index'])->name('index');
        // Route::get('create', [ContactController::class, 'create'])->name('create');
        // Route::post('store', [ContactController::class, 'store'])->name('store');
        // Route::get('{id}/edit', [ContactController::class, 'edit'])->name('edit');
        // Route::post('{id}/update', [ContactController::class, 'update'])->name('update');
        Route::get('{id}/view', [ContactController::class, 'view'])->name('view');
        Route::get('{id}/delete', [ContactController::class, 'delete'])->name('delete');
    });

    // Support Ticket
    // Route::group(['prefix' => 'support-ticket', 'as' => 'support-ticket.'], function () {
    //     Route::get('/', [SupportTicketController::class, 'index'])->name('index');
    //     Route::post('{id}/update', [SupportTicketController::class, 'update'])->name('update');
    //     Route::get('{id}/view', [SupportTicketController::class, 'view'])->name('view');
    //     Route::get('{id}/delete', [SupportTicketController::class, 'delete'])->name('delete');
    //     Route::get('{id}/show', [SupportTicketController::class, 'ticketShow'])->name('show');
    //     Route::post('{id}/reply', [SupportTicketController::class, 'reply'])->name('reply');
    // });



    //Country
    Route::group(['prefix' => 'country', 'as' => 'country.'], function () {
        Route::get('/', [CountryController::class, 'index'])->name('index');
        Route::get('create', [CountryController::class, 'create'])->name('create');
        Route::post('store', [CountryController::class, 'store'])->name('store');
        Route::get('{id}/edit', [CountryController::class, 'edit'])->name('edit');
        Route::post('{id}/update', [CountryController::class, 'update'])->name('update');
        // Route::get('{id}/view', 'CountryController@view')->name('view');
        Route::get('{id}/delete', [CountryController::class, 'delete'])->name('delete');
    });

    //Region
    Route::group(['prefix' => 'region', 'as' => 'region.'], function () {
        Route::get('/', [RegionController::class, 'index'])->name('index');
        Route::get('create', [RegionController::class, 'create'])->name('create');
        Route::post('store', [RegionController::class, 'store'])->name('store');
        Route::get('{id}/edit', [RegionController::class, 'edit'])->name('edit');
        Route::post('{id}/update', [RegionController::class, 'update'])->name('update');
        // Route::get('{id}/view', 'RegionController@view')->name('view');
        Route::get('{id}/delete', [RegionController::class, 'delete'])->name('delete');
    });

    //City
    Route::group(['prefix' => 'city', 'as' => 'city.'], function () {
        Route::get('/', [CityController::class, 'index'])->name('index');
        Route::get('create', [CityController::class, 'create'])->name('create');
        Route::post('store', [CityController::class, 'store'])->name('store');
        Route::get('{id}/edit', [CityController::class, 'edit'])->name('edit');
        Route::post('{id}/update', [CityController::class, 'update'])->name('update');
        Route::get('{id}/view', [CityController::class, 'view'])->name('view');
        Route::get('{id}/delete', [CityController::class, 'delete'])->name('delete');
        Route::get('country/region/{countryId?}', [CityController::class, 'CountryWiseRegion'])->name('countrywise.region');
    });

    // Card
    Route::group(['prefix' => 'card', 'as' => 'card.'], function () {
        Route::get('/', [CardController::class, 'index'])->name('index');
        Route::get('/status/{id}', [CardController::class, 'status'])->name('status');
        Route::get('/edit/{id}', [CardController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [CardController::class, 'update'])->name('update');
        Route::get('/delete/{id}', [CardController::class, 'delete'])->name('delete');
        Route::post('upload/profie', [CardController::class, 'uploadImage'])->name('upload.image');
        Route::post('upload/cover', [CardController::class, 'uploadCover'])->name('upload.cover');
        Route::get('preview/template', [CardController::class, 'previewTemplate'])->name('preview.template');
    });

    // Plan
    Route::group(['prefix' => 'plan', 'as' => 'plan.'], function () {
        Route::get('/', [PlanController::class, 'index'])->name('index');
        Route::get('/create', [PlanController::class, 'create'])->name('create');
        Route::post('/store', [PlanController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [PlanController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [PlanController::class, 'update'])->name('update');
        Route::get('/delete/{id}', [PlanController::class, 'delete'])->name('delete');
    });

    // Brand
    Route::group(['prefix' => 'brand', 'as' => 'brand.'], function () {
        Route::get('/', [BrandController::class, 'index'])->name('index');
        Route::get('/create', [BrandController::class, 'create'])->name('create');
        Route::post('/store', [BrandController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [BrandController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [BrandController::class, 'update'])->name('update');
        Route::get('/delete/{id}', [BrandController::class, 'delete'])->name('delete');
    });


    // Item Brand
    Route::group(['prefix' => 'item-brand', 'as' => 'item-brand.'], function () {
        Route::get('/', [ItemBrandController::class, 'index'])->name('index');
        Route::get('/create', [ItemBrandController::class, 'create'])->name('create');
        Route::post('/store', [ItemBrandController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [ItemBrandController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [ItemBrandController::class, 'update'])->name('update');
        Route::get('/delete/{id}', [ItemBrandController::class, 'delete'])->name('delete');
    });

    // Image Content
    Route::group(['prefix' => 'image-content', 'as' => 'image-content.'], function () {
        Route::get('/', [ImageContentController::class, 'index'])->name('index');
        Route::get('/create', [ImageContentController::class, 'create'])->name('create');
        Route::post('/store', [ImageContentController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [ImageContentController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [ImageContentController::class, 'update'])->name('update');
        Route::get('/delete/{id}', [ImageContentController::class, 'delete'])->name('delete');
    });

    //Why Tga
    Route::group(['prefix' => 'why-tga', 'as' => 'why-tga.'], function () {
        Route::get('/', [WhyTgaController::class, 'index'])->name('index');
        Route::post('/store', [WhyTgaController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [WhyTgaController::class, 'edit'])->name('edit');
        Route::post('/{id}/update', [WhyTgaController::class, 'update'])->name('update');
        Route::get('/{id}/delete', [WhyTgaController::class, 'delete'])->name('delete');
    });

    // Service
    Route::group(['prefix' => 'service', 'as' => 'service.'], function () {
        Route::get('/', [ServiceController::class, 'index'])->name('index');
        Route::get('/create', [ServiceController::class, 'create'])->name('create');
        Route::post('/store', [ServiceController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [ServiceController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [ServiceController::class, 'update'])->name('update');
        Route::get('/delete/{id}', [ServiceController::class, 'delete'])->name('delete');
    });

    // Service Level
    // Route::group(['prefix' => 'service-level', 'as' => 'service.level.'], function () {
    //     Route::get('/', [ServiceLevelController::class, 'index'])->name('index');
    //     Route::get('/create', [ServiceLevelController::class, 'create'])->name('create');
    //     Route::post('/store', [ServiceLevelController::class, 'store'])->name('store');
    //     Route::get('/edit/{id}', [ServiceLevelController::class, 'edit'])->name('edit');
    //     Route::post('/update/{id}', [ServiceLevelController::class, 'update'])->name('update');
    //     Route::get('/delete/{id}', [ServiceLevelController::class, 'delete'])->name('delete');
    // });

    // Subscriber
    Route::group(['prefix' => 'subscriber', 'as' => 'subscriber.'], function () {
        Route::get('/', [NewsLetterController::class, 'index'])->name('index');
    });

    // Logs
    // Route::group(['prefix' => 'log', 'as' => 'log.'], function () {
    //     Route::get('/', [LogsHistoryController::class, 'index'])->name('index');
    // });

    // transaction
    Route::group(['prefix' => 'transaction', 'as' => 'transaction.'], function () {
        Route::get('/', [TransactionController::class, 'index'])->name('index');
        Route::get('{id}/invoic-download', [TransactionController::class, 'invoiceDownload'])->name('invoice');
    });

    // scan cards
    Route::get('/scan-card', [OrderCertificateController::class, 'scanCard'])->name('scan.card');
    Route::post('/set-scan-session', function (Request $request) {
        // Store values in Laravel session
        session([
            'page_type' => $request->page_type,
            'card_id' => $request->card_id,
            'scroll_index' => $request->scroll_index,
            'is_manual' => $request->is_manual,
            'order_id' => $request->order_id
        ]);

        return response()->json(['success' => true]);
    });
});



