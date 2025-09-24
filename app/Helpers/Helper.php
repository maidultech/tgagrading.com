<?php

use App\Models\Card;
use App\Models\CertificateVerification;
use App\Models\Currency;
use App\Models\Language;
use App\Models\OrderCard;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserWallet;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;


if (!function_exists('getSetting')) {
    /**
     * @return mixed
     */
    function getSetting(): Setting
    {
        return Setting::orderBy('id', 'DESC')->first();
    }
}

function checkFrontLanguageSession()
{
    if (Session::has('languageName')) {
        return Session::get('languageName');
    }
    return 'de';
}

function checkCardLanguageSession()
{
    if (Session::has('cardLang')) {
        return Session::get('cardLang');
    }
    return geDefaultLanguage()->iso_code;
}

function getLanguageByKey($key)
{
    $languageName = Language::where('iso_code', $key)->first();

    if (!empty($languageName['name'])) {
        return $languageName['name'];
    }

    return 'German';
}

if (!function_exists('getAllLanguageWithFullData')) {
    function getAllLanguageWithFullData()
    {
        return Language::all();
    }
}
if (!function_exists('getFlagByIsoCode')) {
    function getFlagByIsoCode($isoCode)
    {
        $language = Language::where('iso_code', $isoCode)->first();
        return $language ? $language->flag : null;
    }
}

if (!function_exists('geDefaultLanguage')) {
    function geDefaultLanguage()
    {
        return Language::where('is_default', 1)->first();
    }
}

if (!function_exists('isMobile')) {
    function isMobile(): bool
    {
        if (stristr($_SERVER['HTTP_USER_AGENT'], 'Mobile')) {
            return true;
        } else {
            return false;
        }
        // return false;
    }
}
/*Print Validation Error List*/
if (!function_exists('vError')) {
    function vError($errors)
    {
        if ($errors->any()) {
            foreach ($errors->all() as $error) {
                echo '<li class="text-danger">' . $error . '</li>';
            }
        } else {
            echo 'Not found any validation error';
        }
    }
}

if (!function_exists('get_error_response')) {
    function get_error_response($code, $reason, $errors = [], $error_as_string = '', $description = ''): array
    {
        if ($error_as_string == '') {
            $error_as_string = $reason;
        }
        if ($description == '') {
            $description = $reason;
        }
        return [
            'code' => $code,
            'errors' => $errors,
            'error_as_string' => $error_as_string,
            'reason' => $reason,
            'description' => $description,
            'error_code' => $code,
            'link' => ''
        ];
    }
}

if (!function_exists('checkPackageValidity')) {
    function checkPackageValidity($user_id): bool
    {
        $user = DB::table('users')->where('id', $user_id)->first();
        $today = strtotime("today midnight");
        $expire = strtotime($user->plan_validity);
        if ($today >= $expire) {
            return false;
        } else {
            return true;
        }
    }
}


if (!function_exists('checkCardLimit')) {
    function checkCardLimit($user_id): bool
    {
        $user = DB::table('users')->where('id', $user_id)->first();
        if ($user->plan_details) {
            $plan_details = json_decode($user->plan_details, true);
            if ($plan_details['no_of_vcards'] != 9999) {
                $user_card = DB::table('business_cards')->where('status', 1)->where('user_id', $user_id)->count();
                if ($plan_details['no_of_vcards'] <= $user_card) {
                    return false;
                }
            }
        }
        return true;
    }
}
if (!function_exists('getPhoto')) {
    function getPhoto($path): string
    {
        if ($path) {
            $ppath = public_path($path);
            if (file_exists($ppath)) {
                return asset($path);
            } else {
                return asset('frontend/assets/images/no_image.jpg');
            }
        } else {
            return asset('frontend/assets/images/no_image.jpg');
        }
    }
}

if (!function_exists('getBlogPhoto')) {
    function getBlogPhoto($path): string
    {
        if ($path) {
            $ppath = public_path($path);
            if (file_exists($ppath)) {
                return asset($path);
            } else {
                return asset('frontend/assets/images/no_image.jpg');
            }
        } else {
            return asset('frontend/assets/images/no_image.jpg');
        }
    }
}


if (!function_exists('getAvatar')) {
    function getAvatar($path)
    {
        if (!empty($path)) {
            return $path;
        } else {
            // return asset('assets/img/card/personal.png');
            return asset('assets/image/default-profile.png');
        }
    }
}
function getLogoUrl()
{
    $settings = Setting::orderBy('id', 'desc')->first();

    $appLogo = $settings->site_logo;

    return config('app.url').$appLogo;
    // return asset('assets\logo_black.png');
}

if (!function_exists('getCover')) {
    function getCover($path = null): string
    {
        if ($path) {
            $ppath = public_path($path);
            if (file_exists($ppath)) {
                return asset($path);
            } else {
                return asset('assets/img/default-cover.png');
            }
        } else {
            return asset('assets/img/default-cover.png');
        }
    }
}
if (!function_exists('getProfile')) {
    function getProfile($path = null): string
    {
        if ($path) {
            $ppath = public_path($path);
            if (file_exists($ppath)) {
                return asset($path);
            } else {
                return asset('assets/images/user.jpg');
            }
        } else {
            return asset('assets/images/user.jpg');
        }

    }
}
if (!function_exists('getLogo')) {
    function getLogo($path = null): string
    {
        if ($path) {
            $ppath = public_path($path);
            if (file_exists($ppath)) {
                return asset($path);
            } else {
                return asset('assets/images/default-icon.png');
            }
        } else {
            return asset('assets/images/default-icon.png');
        }
    }
}

if (!function_exists('getIcon')) {
    function getIcon($path = null): string
    {
        if ($path) {
            $ppath = public_path($path);
            if (file_exists($ppath)) {
                return asset($path);
            } else {
                return asset('assets/images/default-icon.png');
            }
        } else {
            return asset('assets/images/default-icon.png');
        }
    }
}
if (!function_exists('getBanner')) {
    function getBanner($path = null): string
    {
        if ($path) {
            $ppath = public_path($path);
            if (file_exists($ppath)) {
                return asset($path);
            } else {
                return asset('assets/images/default-banner.png');
            }
        } else {
            return asset('assets/images/default-banner.png');
        }
    }
}
if (!function_exists('getSeoImage')) {
    function getSeoImage($path = null): string
    {
        if ($path) {
            $ppath = public_path($path);
            if (file_exists($ppath)) {
                return asset($path);
            } else {
                return asset('assets/images/default-seo.png');
            }
        } else {
            return asset('assets/images/default-seo.png');
        }
    }
}

function checkPlanValidity(): bool
{
    $validity = false;
    $now = Carbon::now();
    $user = auth()->user();
    if ($user->current_pan_valid_date > $now) {
        return true;
    }

    return $validity;
}

function checkTotalVcard(): bool
{
    $makeVcard = false;
    $user = auth()->user();
    $plan = User::where('id', $user->id)->first()->userPlan;

    if (!empty($plan)) {
        $totalCards = Card::where('user_id', $user->id)->count();
        $makeVcard = $plan->no_of_vcards > $totalCards;
    }

    return $makeVcard;
}

function checkPlanFeature(string $feature): bool
{
    $user = auth()->user();
    $plan = User::where('id', $user->id)->first()->userPlan;

    if (!empty($plan)) {
        return $plan->$feature == 1;
    }

    return false;
}

function checkUserPlanFeature(int $userId, string $feature): bool
{
    $user = User::find($userId);

    if ($user && $user->userPlan) {
        $plan = $user->userPlan;

        if (!empty($plan)) {
            return $plan->$feature == 1;
        }
    }

    return false;
}

if (!function_exists('getDesigComp')) {
    function getDesigComp($desig, $comp): string
    {
        if ($desig != '' & $comp != '') {
            return $desig . ' At ' . $comp;
        } else {
            return $desig . ' ' . $comp;
        }

    }
}


if (!function_exists('makeUrl')) {
    function makeUrl($url)
    {
        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "https://" . $url;
        }
        return $url;
    }
}

if (!function_exists('getSocialIcon')) {
    function getSocialIcon($ikey)
    {
        return DB::table('social_icon')->where('icon_name', '=', $ikey)->first();
    }
}
if (!function_exists('getCurrencySymbol')) {
    function getCurrencySymbol($key)
    {
        return Currency::where('id', $key)->first()->symbol;
    }
}

if (!function_exists('getDefaultCurrencySymbol')) {
    function getDefaultCurrencySymbol()
    {
        return DB::table('currencies')->where('is_default', 1)->first()->symbol ?? '$';
    }
}

if (!function_exists('CurrencyFormat')) {
    function CurrencyFormat($number, $decimal = 1)
    { // cents: 0=never, 1=if needed, 2=always
        if (is_numeric($number)) { // a number
            if (!$number) { // zero
                $money = ($decimal == 2 ? '0.00' : '0'); // output zero
            } else { // value
                if (floor($number) == $number) { // whole number
                    $money = number_format($number, ($decimal == 2 ? 2 : 0)); // format
                } else { // cents
                    $money = number_format(round($number, 2), ($decimal == 0 ? 0 : 2)); // format
                } // integer or decimal
            } // value
            return $money;
        } else {
            return $number;
        } // numeric
    } //
}


function formatFileName($file): string
{
    $base_name = preg_replace('/\..+$/', '', $file->getClientOriginalName());
    $base_name = explode(' ', $base_name);
    $base_name = implode('-', $base_name);
    $base_name = Str::lower($base_name);
    return $base_name . "-" . uniqid() . "." . $file->getClientOriginalExtension();
}

function checkPackage($id = null): bool
{
    if ($id) {
        $user = DB::table('users')->where('id', $id)->first();
        if ($user->plan_id) {
            return true;
        } else {
            return false;
        }
    } else {
        return true;
    }
}


function isFreePlan($user_id): bool
{
    $user = DB::table('users')->select('plans.is_free')->leftJoin('plans', 'plans.id', '=', 'users.plan_id')->where('users.id', $user_id)->first();
    if ($user->is_free == 1) {
        return true;
    }
    return false;
}

function isAnnualPlan($user_id): bool
{
    $user = DB::table('users')->select('users.*', 'plans.is_free')
        ->leftJoin('plans', 'plans.id', '=', 'users.plan_id')
        ->where('users.id', $user_id)
        ->first();
    $subscription_end = new Carbon($user->plan_validity);
    $subscription_start = new Carbon($user->plan_activation_date);
    $diff_in_days = $subscription_start->diffInDays($subscription_end);
    if ($diff_in_days > 364 && $user->is_free == 0) {
        return true;
    }
    return false;
}


function getPlan($user_id)
{
    return DB::table('users')
        ->select('plans.*')
        ->leftJoin('plans', 'plans.id', '=', 'users.plan_id')
        ->where('users.id', $user_id)
        ->first();
}

function uploadImage(?object $file, string $path, int $width, int $height): string
{
    $blank_img = Image::canvas($width, $height, '#EBEEF7');
    $pathCreate = public_path("/uploads/$path/");
    if (!File::isDirectory($pathCreate)) {
        File::makeDirectory($pathCreate, 0777, true, true);
    }

    $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
    $updated_img = Image::make($file->getRealPath());
    $imageWidth = $updated_img->width();
    $imageHeight = $updated_img->height();
    if ($imageWidth > $width) {

        $updated_img->resize($width, null, function ($constraint) {
            $constraint->aspectRatio();
        });
    }
    if ($imageHeight > $height) {

        $updated_img->resize(null, $height, function ($constraint) {
            $constraint->aspectRatio();
        });
    }


    $blank_img->insert($updated_img, 'center');
    $blank_img->save(public_path('/uploads/' . $path . '/') . $fileName);
    return "uploads/$path/" . $fileName;
}


function uploadGeneralImage(?object $file, string $path, $oldImage = null): string
{
    $pathCreate = public_path("/uploads/$path/");
    if (!File::isDirectory($pathCreate)) {
        File::makeDirectory($pathCreate, 0777, true, true);
    }
    if ($oldImage && File::exists(public_path($oldImage))) {
        File::delete(public_path($oldImage));
    }

    $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
    $file->move(public_path('/uploads/' . $path . '/'), $fileName);
    return "uploads/$path/" . $fileName;
}

if (!function_exists('checkCustomPage')) {
    function checkCustomPage($slug): bool
    {
        return DB::table('custom_pages')->where('url_slug', $slug)->where('is_active', 1)->exists();
    }
}


function getCartItems(){
    return session('checkout.items',[]);
}

function clearCartNSession($clearCoupon = false){
    $coupon = session('checkout.coupon');
    session()->forget([
        'checkout' , 'payment' , 'checkout.coupon', 'checkout.plan.coupon', 'used_wallet', 'wallet_discount'
    ]);
    if($clearCoupon){
        session(['checkout.coupon' => $coupon]);
    }
}

function countOrdersByStatus($status)
{
    $query = DB::table('orders')->where('status', $status)
    ->whereNot('item_type', 0)
    ->where(function ($query) {
        $query->whereNot(function ($query) {
            $query->where(function ($query) {
                $query->where('status', 35)
                    ->orWhere('status', 40);
            })
            ->where('payment_status', 1);
        });
    });
    if ($status == 35) {
        $query->where('payment_status', 0);
    }

    return $query->count();
}

function getNewCertificate2($cardnumber=null){
    $prefix = 100;    
    $postfix = '0000';
    $last_card = null;
    if($cardnumber){
        $postfix = substr($cardnumber, -4);
        $random = (int) substr($cardnumber, -5,1);
        $prefix = (int) str($cardnumber)->before($random.$postfix)->value();
         if($postfix == 9999){
                $prefix = $prefix+1; 
            }
    }else{
        $last_card = OrderCard::where('is_no_grade', 0)->where('is_graded', 1)->orderBy('id', 'desc')->first();
        if($last_card){
            $prefix = $last_card->prefix;
            $postfix = $last_card->postfix;
    
            if($last_card->postfix == 9999){
                $prefix = $last_card->prefix+1; 
            }
        }
    
    }


    // Increment the serial number and reset to 0001 if it exceeds 9999
    $nextSerial = (int)$postfix + 1;
    if ($nextSerial > 9999) {
        $nextSerial = 1;
    }

    

    $rand_num = rand(0,9);
    // Return the serial number padded with leading zeroes
    $postfix = str_pad($nextSerial, 4, '0', STR_PAD_LEFT);

    $certification_number = "{$prefix}{$rand_num}{$postfix}";
    return $certification_number;
   

}



/**
 * Summary of getNewCertificate
 * @param mixed $lastNumber Certificate No
 * @return string
 */
function getNewCertificate($lastNumber = null){
    if($lastNumber){
        $postfix = (int) substr($lastNumber, -4);
        if( $postfix== 9999){
            return generateCertificateNo($lastNumber);
        }else{
            $postfix++;
        }
        return substr($lastNumber,0 , -4).$postfix;
    }else{
        return generateCertificateNo($lastNumber);
    }
}

function breakCertificateNo($number){
    $postfix = substr($number, -4);
    $random = (int) substr($number, -5,1);
    $prefix = (int) str($number)->before($random.$postfix)->value();
    return [
        'postfix' => $postfix,
        'random' => $random,
        'prefix' => $prefix,
    ];
}
function generateCertificateNo($lastNumber = null){
    if(!$lastNumber){
        $lastNumber = OrderCard::latest()->first();
    }else{
        $lastNumber = OrderCard::where('card_number',$lastNumber)->first();
    }

    if(!$lastNumber){
        $prefix = 100;
        $rand_num = rand(0,9);
        $postfix = 1000;
    }else{
        $prefix = $lastNumber->prefix;
        $rand_num = rand(0,9);
        $postfix = $lastNumber->postfix;

        if($postfix == 9999){
            $prefix++;
            $postfix = 1000;
        }else{
            $postfix++;
        }
    }
    $certification_number = "{$prefix}{$rand_num}{$postfix}";
    return $certification_number;
}


function calculateFinalGrade($grades) {
    list($C, $O, $E, $S) = $grades;

    // Sort grades in descending order
    $sortedGrades = $grades;
    rsort($sortedGrades); // Sorts in descending order
    list($a, $b, $c, $d) = $sortedGrades;

    // Calculate the difference between the third-highest and lowest grades
    $e = $c - $d;

    // Calculate the final grade based on custom rules
    return (($e ? 
                    (($d === $E || $d === $S) 
                        ? ($e < 1 ? 0.5 : ((($e == 1 && $d > 8.4) || ($a - $d == 1.5 && $a < 10)) ? 0.5 : 1))
                        : ($d - $C ? ($e < 3 ? 0.5 : 1) : ($e < 2 ? 0.5 : ($e < 4 ? 1 : 1.5)))
                    ) 
                : 0) + $d);
}
function decreaseImageOpacity($imagePath, $opacity) {
    // Get the image type
    $imageType = exif_imagetype($imagePath);
    
    // Load the image based on its type
    switch ($imageType) {
        case IMAGETYPE_PNG:
            $image = imagecreatefrompng($imagePath);
            break;
        case IMAGETYPE_JPEG:
            $image = imagecreatefromjpeg($imagePath);
            break;
        case IMAGETYPE_GIF:
            $image = imagecreatefromgif($imagePath);
            break;
        default:
            return false; // Unsupported image type
    }

    if (!$image) {
        return false; // Failed to load image
    }

    // Create a new image with the same dimensions
    $width = imagesx($image);
    $height = imagesy($image);
    $newImage = imagecreatetruecolor($width, $height);

    // Set the blending mode
    imagealphablending($newImage, false);
    imagesavealpha($newImage, true);

    // Loop through each pixel to change opacity
    for ($y = 0; $y < $height; $y++) {
        for ($x = 0; $x < $width; $x++) {
            $rgba = imagecolorat($image, $x, $y);
            $alpha = ($rgba & 0x7F000000) >> 24; // Get the alpha channel
            $alpha = min(127, $alpha + (127 * (1 - $opacity))); // Decrease opacity
            $newColor = ($rgba & 0x00FFFFFF) | ($alpha << 24); // Set new alpha
            imagesetpixel($newImage, $x, $y, $newColor);
        }
    }

    // Get the original filename and extension
    $pathInfo = pathinfo($imagePath);
    $newFileName = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_opacity.' . $pathInfo['extension'];

    // Save the new image based on the original type
    switch ($imageType) {
        case IMAGETYPE_PNG:
            imagepng($newImage, $newFileName); // Save as PNG
            break;
        case IMAGETYPE_JPEG:
            imagejpeg($newImage, $newFileName); // Save as JPEG
            break;
        case IMAGETYPE_GIF:
            imagegif($newImage, $newFileName); // Save as GIF
            break;
    }

    // Free up memory
    imagedestroy($image);
    imagedestroy($newImage);

    return $newFileName; // Return the new file path
}

function lazyLoadImageAttr($src){
    return 'data-src="'.$src.'" src="'.lazyLoadImage().'"';
}

function lazyLoadImage(){
    return asset('assets/images/lazyload.webp');
}
function syncWallet($user_id)
{
    $user = User::findOrFail($user_id);
    $wallet = UserWallet::where('customer_id', $user_id)->sum('amount');
    $user->wallet_balance = $wallet;
    $user->save();
}
function debitWalletBalance($user_id, $amount, $description = null){

    $walletTransaction = new UserWallet();
    $walletTransaction->customer_id = $user_id;
    $walletTransaction->amount = -($amount);
    $walletTransaction->description = $description;
    $walletTransaction->created_by = 0;
    $walletTransaction->save();
    // try {
    //     syncWallet($walletTransaction->customer_id);
    //     return $walletTransaction->save();
    // } catch (\Throwable $th) {
    //     return false;
    // }
}
function exceptionMsgConverter($e)
{
    try {
        $message = $e->getMessage();

        // If 'postal-code' or 'PostalCodeType' is mentioned, return a generic error message
        if (str($message)->contains(['postal-code', 'PostalCodeType'])) {
            return "Your postal code is invalid. Please update your postal code and try again.";
        }

        // If 'response:' is not found, return the original exception message
        if (!str($message)->contains('response:')) {
            return $message ?: "An unexpected error occurred.";
        }

        // Extract the potential JSON portion
        $jsonString = str($message)->after('response:')->between("\n", "\n");

        if (empty($jsonString)) {
            return "Something went wrong with the shipment request.";
        }

        $decoded = json_decode($jsonString);

        if (json_last_error() !== JSON_ERROR_NONE || empty($decoded->response->errors[0]->message)) {
            return "Something went wrong with the shipment request.";
        }

        // Extract message
        $apiMessage = $decoded->response->errors[0]->message;

        // Custom override: strip "for BC Canada" if exists
        if (str($apiMessage)->contains('is invalid for')) {
            $apiMessage = preg_replace('/ is invalid for .+$/', ' is invalid.', $apiMessage);
        }

        return $apiMessage;

    } catch (\Throwable $th) {
        Log::alert('Error: ' . $th);
        return "Something went wrong with the shipment request.";
    }
}

function getStateCodeMap($country = null){

    $usStates = [
        // U.S. States
        'alabama' => 'US-AL','alaska' => 'US-AK','arizona' => 'US-AZ','arkansas' => 'US-AR','california' => 'US-CA','colorado' => 'US-CO','connecticut' => 'US-CT','delaware' => 'US-DE','district of columbia' => 'US-DC','florida' => 'US-FL','georgia' => 'US-GA','hawaii' => 'US-HI','idaho' => 'US-ID','illinois' => 'US-IL','indiana' => 'US-IN','iowa' => 'US-IA','kansas' => 'US-KS','kentucky' => 'US-KY','louisiana' => 'US-LA','maine' => 'US-ME','maryland' => 'US-MD','massachusetts' => 'US-MA','michigan' => 'US-MI','minnesota' => 'US-MN','mississippi' => 'US-MS','missouri' => 'US-MO','montana' => 'US-MT','nebraska' => 'US-NE','nevada' => 'US-NV','new hampshire' => 'US-NH','new jersey' => 'US-NJ','new mexico' => 'US-NM','new york' => 'US-NY','north carolina' => 'US-NC','north dakota' => 'US-ND','ohio' => 'US-OH','oklahoma' => 'US-OK','oregon' => 'US-OR','pennsylvania' => 'US-PA','rhode island' => 'US-RI','south carolina' => 'US-SC','south dakota' => 'US-SD','tennessee' => 'US-TN','texas' => 'US-TX','utah' => 'US-UT','vermont' => 'US-VT','virginia' => 'US-VA','washington' => 'US-WA','west virginia' => 'US-WV','wisconsin' => 'US-WI','wyoming' => 'US-WY',
    ];
    $canadaProvince = [
        // Canadian Provinces/Territories
        'alberta' => 'CA-AB','british columbia' => 'CA-BC','manitoba' => 'CA-MB','new brunswick' => 'CA-NB','newfoundland and labrador' => 'CA-NL','northwest territories' => 'CA-NT','nova scotia' => 'CA-NS','nunavut' => 'CA-NU','ontario' => 'CA-ON','prince edward island' => 'CA-PE','quebec' => 'CA-QC','saskatchewan' => 'CA-SK','yukon' => 'CA-YT'
    ];
    if($country == 'Canada'){
        return $canadaProvince;
    }else if($country == 'United States'){
        return $usStates;
    }
    $stateToCodeMap = array_merge($usStates, $canadaProvince);
    return $stateToCodeMap;
}
function stateToIso2($stateName, $returnDefault = false) {
    $stateToCodeMap = getStateCodeMap();
    $normalizedStateName = strtolower(trim($stateName));

    if (array_key_exists($normalizedStateName, $stateToCodeMap)) {
        return str($stateToCodeMap[$normalizedStateName])->after('-')->value();
    }

    return $returnDefault ? $stateName : "";
}

function shippingPhoneNumberFormat($number){
    $cleanedNumber = str($number)->remove([' ', '-']);
    
    $hasCountryCode = str_starts_with($cleanedNumber, '+');

    $countryCode = $hasCountryCode ? substr($cleanedNumber, 0, 2) : '+1';

    $startPosition = $hasCountryCode ? 2 : 0;

    $simProvider = substr($cleanedNumber, $startPosition, 3);

    $restOfNumber = substr($cleanedNumber, $startPosition + 3);

    return [
        $countryCode,
        $simProvider,
        $restOfNumber
    ];
    
}

function insurancePercentage(){
    return getSetting()->insurance_cost;
}