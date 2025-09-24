<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Seo;
use App\Models\Blog;
use App\Models\Card;
use App\Models\Plan;
use App\Models\User;
use App\Models\Order;
use App\Models\States;
use App\Mail\OrderMail;
use App\Models\Contact;
use App\Models\Partner;
use App\Models\Service;

use App\Models\Setting;
use App\Models\Language;
use App\Mail\SendContact;
use App\Models\OrderCard;
use App\Models\CustomPage;
use App\Models\Newsletter;
use App\Models\SocialIcon;
use App\Models\HomeContent;
use App\Models\ManualLabel;
use App\Models\Testimonial;
use App\Models\Transaction;
use App\Models\BlogCategory;
use App\Models\HomepageStep;
use Illuminate\Cache\TagSet;
use Illuminate\Http\Request;
use App\Models\HomepageBanner;
use Illuminate\Support\Carbon;
use App\Models\BusinessPartner;
use App\Services\ZendeskService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Crypt;
use App\Models\CertificateVerification;
use Illuminate\Support\Facades\Session;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Builder;

class HomeController extends Controller
{

    protected $zendeskService;
    public function __construct(ZendeskService $zendeskService)
    {
        $this->zendeskService = $zendeskService;
    }

    public function index()
    {
        $data = $this->getMetaData('Tgagrading');
        $data['home'] = HomeContent::first();
        $data['banners'] = HomepageBanner::all();

        $data['plans'] = Plan::where('status', 1)->orderBy('order_number', 'asc')->get();
        $data['steps'] = HomepageStep::get();
        $data['faqs'] = Faq::where('is_active', 1)->oldest('order_id')->get();
        $data['image_contents'] = DB::table('image_contents')->where('status', 1)->orderBy('order_id')->get();
        $data['why_tgas'] = DB::table('why_tga')->where('status', 1)->orderBy('order_id')->get();
        $data['services'] = DB::table('services')
            ->where('status', 1)
            ->whereNot('type', 3)
            ->inRandomOrder()
            ->get();
        // dd($data['services']);
        
        return view('frontend.index', $data);
    }

    public function about()
    {
        $data = $this->getMetaData('About Us');
        $data['row'] = CustomPage::where('url_slug', 'about-us')->first();
        return view('frontend.about', $data);
    }

    public function contact()
    {
        $data = $this->getMetaData('Contact Us');
        $data['settings'] = Setting::first(['email', 'phone_no', 'office_address', 'site_name', 'support_email']);
        return view('frontend.contact', $data);
    }

    public function contactSubmit(Request $request)
    {
        $setting = getSetting();
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required_if:contact_type,1',
            // 'g-recaptcha-response' => $setting->recaptcha_site_key ? 'required' : 'nullable',
            'message' => 'required',
        ], [
            // 'g-recaptcha-response.required' => 'The captcha-response field is required.',
            'phone.required_if' => 'The phone field is required.'
        ]);


        if ($setting->google_recaptcha == '1') {
            $recaptchaResponse = $request->input('g-recaptcha-response');
            $secretKey = $setting->recaptcha_site_secret;

            // Verify reCAPTCHA response with Google
            $response = Http::get('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secretKey,
                'response' => $recaptchaResponse,
            ]);
            $responseBody = $response->json();
            Log::info('reCAPTCHA API Response:', $responseBody);

            // Validate reCAPTCHA success and score
            if (!isset($responseBody['success']) || !$responseBody['success']) {
                Toastr::error(trans('Google reCAPTCHA verification failed. Please try again.'), 'Error', ["positionClass" => "toast-top-right"]);
                return back();
            }

            if ($responseBody['score'] < 0.3) { // Adjust threshold as needed
                Toastr::error(trans('Suspicious activity detected. Please try again.'), 'Error', ["positionClass" => "toast-top-right"]);
                return back();
            }
        }

        try {
            $contact = new Contact();
            $contact->name = $request->name;
            $contact->email = $request->email;
            $contact->contact_type = $request->contact_type ?? 0;
            $contact->phone = $request->phone;
            $contact->customer_number = $request->customer_number;
            $contact->message = $request->message;
            $contact->save();

            if ($request->contact_type == 1) {

                // $body  = $request->contact_type == '1' ? 'You have a new contact sale message.' : 'You have a new contact message.';
                // $data = [
                //     'greeting' => 'Hi Admin,',
                //     'body' => $body,
                //     'name' => $request->name,
                //     'email' => $request->email,
                //     'phone' => $request->phone,
                //     'msg' => $request->message,
                //     'thanks' => 'Thanks to be with '. $setting->site_name,
                //     'site_name' => $setting->site_name,
                //     'site_url' => url('/'),
                //     'footer' => 1,
                // ];
                // try {
                //     Mail::to($setting->email)->send(new SendContact($data));
                // } catch (\Exception $e) {
                //     Log::alert('Contact mail not sent. Error: ' . $e->getMessage());
                // }

                $subject = "Contact Sales Inquiry";
                $attachments = [];
                $type = "question";
                $priority = 'normal';

                $ticket = $this->zendeskService->createTicketWithAttachments(
                    $subject,
                    $request->message,
                    [
                        'name' => $request->name,
                        'email' => $request->email,
                    ],
                    $attachments,
                    $priority,
                    $type,
                );
            } else {
                $subject = "Customer Inquiry";
                $attachments = [];
                $type = "question";
                $priority = 'normal';

                $ticket = $this->zendeskService->createTicketWithAttachments(
                    $subject,
                    $request->message,
                    [
                        'name' => $request->name,
                        'email' => $request->email,
                    ],
                    $attachments,
                    $priority,
                    $type,
                );
            }

            Toastr::success('Message sent successfully.');
            return redirect()->back();

        } catch (GuzzleException $e) {
            Toastr::error(exceptionMsgConverter($e->getMessage()));
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error("Something went wrong");
            return redirect()->back();
        }

    }

    public function faq()
    {
        $data = $this->getMetaData('FAQ');
        $data['faqs'] = Faq::where('is_active', 1)->oldest('order_id')->get();
        return view('frontend.faq', $data);
    }

    public function terms()
    {
        $data = $this->getMetaData('Terms & Conditions');
        $data['row'] = CustomPage::where('url_slug', 'terms-conditions')->first();
        return view('frontend.terms', $data);
    }

    public function privacy()
    {
        $data = $this->getMetaData('Privacy Policy');
        $data['row'] = CustomPage::where('url_slug', 'privacy-policy')->first();
        return view('frontend.privacy', $data);
    }

    public function gradingScale()
    {
        $data = $this->getMetaData('Grading Scale');
        $data['row'] = CustomPage::where('url_slug', 'grading-scale')->first();
        $data['rows'] = DB::table('grading_scale')->orderBy('order_id', 'asc')->get();
        return view('frontend.grading_scale', $data);
    }
    public function pricing()
    {
        $data['setting']  = getSetting();
        $data = $this->getMetaData('Pricing');
        $data['plans'] = Plan::where('status', 1)->orderBy('order_number', 'asc')->get();
        return view('frontend.pricing', $data);
    }

    public function cardGradingDetails($slug = null)
    {
        @$blockedSlugs = ['trading-card-grading-service', 'crossover-card-grading-service', 'sports-card-grading-service']; // Replace with your actual slugs

        if (in_array(@$slug, @$blockedSlugs)) {
            abort(404);
        }
        // dd($slug);
        $data = $this->getMetaData('Single Page');
        $data['home'] = HomeContent::first();
        $data['service'] = Service::whereRaw('BINARY `slug` = ?', [$slug])->firstOrFail();
        $data['banners'] = HomepageBanner::all();

        $data['plans'] = Plan::where('status', 1)->orderBy('order_number', 'asc')->get();
        $data['steps'] = HomepageStep::get();
        // $data['faqs'] = Faq::where('is_active', 1)->oldest('order_id')->get();
        $data['why_tgas'] = DB::table('why_tga')->where('status', 1)->orderBy('order_id')->get();
        $data['blogs'] = Blog::where('status', 1)->latest()->take(5)->get();

        // Exclude the current service from the list
        $data['services'] = DB::table('services')
            ->where('status', 1)
            ->where('id', '!=', $data['service']->id)
            ->whereNot('type', 3)
            ->inRandomOrder()
            ->get();

        $data['faqs'] = DB::table('faqs')
            ->join('faq_service_map', 'faqs.id', '=', 'faq_service_map.faq_id')
            ->where('faq_service_map.service_id', $data['service']->id)
            ->where('faqs.is_active', 1)
            ->orderBy('faqs.order_id')
            ->select('faqs.*')
            ->get();

        return view('frontend.card_grading_details', $data);
    }

    public function sportsCardGrading()
    {
        $data = $this->getMetaData('Single Page');
        $data['service'] = Service::where('slug', 'sports-card-grading-service')->firstOrFail();

        $data['home'] = HomeContent::first();
        $data['banners'] = HomepageBanner::all();

        $data['plans'] = Plan::where('status', 1)->orderBy('order_number', 'asc')->get();
        $data['steps'] = HomepageStep::get();

        $data['why_tgas'] = DB::table('why_tga')->where('status', 1)->orderBy('order_id')->get();
        $data['faqs'] = DB::table('faqs')
            ->join('faq_service_map', 'faqs.id', '=', 'faq_service_map.faq_id')
            ->where('faq_service_map.service_id', $data['service']->id)
            ->where('faqs.is_active', 1)
            ->orderBy('faqs.order_id')
            ->select('faqs.*')
            ->get();
        $data['services'] = DB::table('services')
            ->whereNot('type', 3)
            ->where('status', 1)
            ->inRandomOrder()
            ->get();
        return view('frontend.sports_card_grading', $data);
    }

    public function crossoverGradingService()
    {
        $data = $this->getMetaData('Single Page');
        $data['service'] = Service::where('slug', 'crossover-card-grading-service')->firstOrFail();

        $data['home'] = HomeContent::first();
        $data['banners'] = HomepageBanner::all();

        $data['plans'] = Plan::where('status', 1)->orderBy('order_number', 'asc')->get();
        $data['steps'] = HomepageStep::get();
        $data['why_tgas'] = DB::table('why_tga')->where('status', 1)->orderBy('order_id')->get();
        $data['faqs'] = DB::table('faqs')
            ->join('faq_service_map', 'faqs.id', '=', 'faq_service_map.faq_id')
            ->where('faq_service_map.service_id', $data['service']->id)
            ->where('faqs.is_active', 1)
            ->orderBy('faqs.order_id')
            ->select('faqs.*')
            ->get();
        $data['services'] = DB::table('services')
            ->where('status', 1)
            ->whereNot('type', 3)
            ->inRandomOrder()
            ->get();
        return view('frontend.crossover_card_grading', $data);
    }

    public function cardGradingService()
    {
        $data = $this->getMetaData('Single Page');
        $data['service'] = Service::where('slug', 'trading-card-grading-service')->firstOrFail();

        $data['home'] = HomeContent::first();
        $data['banners'] = HomepageBanner::all();
        $data['why_tgas'] = DB::table('why_tga')->where('status', 1)->orderBy('order_id')->get();
        $data['plans'] = Plan::where('status', 1)->orderBy('order_number', 'asc')->get();
        $data['steps'] = HomepageStep::get();
        $data['faqs'] = DB::table('faqs')
            ->join('faq_service_map', 'faqs.id', '=', 'faq_service_map.faq_id')
            ->where('faq_service_map.service_id', $data['service']->id)
            ->where('faqs.is_active', 1)
            ->orderBy('faqs.order_id')
            ->select('faqs.*')
            ->get();
        $data['services'] = DB::table('services')
            ->where('status', 1)
            ->whereNot('type', 3)
            ->inRandomOrder()
            ->get();
        return view('frontend.card_grading_service', $data);
    }

    public function certification()
    {
        $data = $this->getMetaData('Certification Verification');
        $data['row'] = CustomPage::where('url_slug', 'certification-verification')->first();
        $number = request()->query('number');
        $data['pop_count'] = 0;
        
        $data['certificate'] = OrderCard::where('card_number', $number)->whereHas('details.order',
            function (Builder $q) { return $q->where('status', '>=', 15); }
        )->first();

        if ($number && !$data['certificate']) {
            $data['certificate'] = ManualLabel::where('card_number', $number)->first();
            if ($data['certificate']) {
                $data['isManual'] = true;
            }
        }

        if (!empty($data['certificate'])) {
            // Prepare fields
            $year = $data['certificate']->year;
            $brand_name = $data['certificate']->brand_name;
            $card = $data['certificate']->card;
            $card_name = $data['certificate']->card_name;
            $grading = isset($data['isManual']) && $data['isManual'] ? $data['certificate']->grade : $data['certificate']->final_grading;

            // OrderCard match
            $orderCardQuery = OrderCard::whereHas('details.order', function (Builder $q) {
                $q->where('status', '>=', 15);
            })
                ->where('year', $year)
                ->where('brand_name', $brand_name)
                ->where('card', $card)
                ->where('card_name', $card_name)
                ->where('final_grading', $grading);

            $orderCardCount = $orderCardQuery->count();

            // ManualLabel match
            $manualLabelQuery = ManualLabel::where('year', $year)
                ->where('brand_name', $brand_name)
                ->where('card', $card)
                ->where('card_name', $card_name)
                ->where('grade', $grading);

            $manualLabelCount = $manualLabelQuery->count();

            $data['pop_count'] = $orderCardCount + $manualLabelCount;

            $orderCardHigherCount = OrderCard::whereHas('details.order', function (Builder $q) {
                $q->where('status', '>=', 15);})
                ->where('year', $year)
                ->where('brand_name', $brand_name)
                ->where('card', $card)
                ->where('card_name', $card_name)
                ->whereRaw('CAST(final_grading AS DECIMAL(5,2)) > ?', [(float) $grading])
                ->count();

            $manualLabelHigherCount = ManualLabel::where('year', $year)
                ->where('brand_name', $brand_name)
                ->where('card', $card)
                ->where('card_name', $card_name)
                ->whereRaw('CAST(grade AS DECIMAL(5,2)) > ?', [(float) $grading])
                ->count();

            $data['pop_higher_count'] = $orderCardHigherCount + $manualLabelHigherCount;
        }

        $data['number'] = $number;
        return view('frontend.certification', $data);
    }

    public function partners()
    {
        $data = $this->getMetaData('Partners');
        $data['rows'] =BusinessPartner::where('status',1)->orderBy('order_id')->get();
        return view('frontend.partners',$data);
    }

    public function howToOrder()
    {
        $data = $this->getMetaData('How to Order');
        $data['row'] = CustomPage::where('url_slug', 'how-to-order')->first(); 
        return view('frontend.how_to_order', $data);
    }

    public function sitemap()
    {
        $data = $this->getMetaData('Sitemap');
        $data['blogs'] = Blog::where('status', 1)->get();
        return view('frontend.sitemap', $data);
    }

    public function sitemapXML()
    {
        $blogs = Blog::where('status', 1)->get();
        $blog_category = BlogCategory::where('status', 1)->get();
        return response()->view('sitemap.index', [
            'blogs' => $blogs,
            'bcategories' => $blog_category,
        ])->header('Content-Type', 'text/xml');

    }

    public function blogs(Request $request)
    {
        $data = $this->getMetaData('News');

        $query = Blog::where('status', 1);

        if ($request->has('tag')) {
            $tag = $request->tag;
            $query->where('tags', 'like', '%"%' . $tag . '%"%');
        }

        if ($request->has('category')) {
            $categorySlug = $request->category;
            $category = BlogCategory::where('slug', $categorySlug)->first();

            if ($category) {
                $query->where('category_id', $category->id);
            } else {
                $query->whereNull('category_id');
            }
        }
        $data['blogs'] = $query->latest()->paginate(10);
        $data['recentBlogs'] = Blog::where('status', 1)->latest()->take(5)->get();
        $data['categories'] = BlogCategory::where('status', 1)->get();

        $data['tags'] = Blog::where('status', 1)
            ->pluck('tags')
            ->flatMap(function ($tags) {
                return collect(json_decode($tags))->flatMap(function ($tag) {
                    return explode(',', $tag);
                });
            })->unique()->values();

        return view('frontend.blogs.index', $data);
    }
    public function blogsByCategory($slug)
    {
        $data = $this->getMetaData('Blogs');

        // Find the category based on the slug
        $category = BlogCategory::where('slug', $slug)->firstOrFail();

        // Get blogs in that category
        $data['blogs'] = $category->blogs()->where('status', 1)->paginate(6);

        // Fetch all categories to display in the sidebar
        $data['categories'] = BlogCategory::where('status', 1)->get();

        return view('frontend.blogs.index', $data);
    }
    public function blogDetails($slug)
    {

        // Find the blog based on the slug
        $blog = Blog::where('slug', $slug)->where('status', 1)->firstOrFail();

        $data = $this->getBlogMetaData($blog);


        $data['blog'] = $blog;
        // Optionally, fetch categories to display in the sidebar
        $data['categories'] = BlogCategory::where('status', 1)->get();
        $data['recentBlogs'] = Blog::where('status', 1)->latest()->take(5)->get();

        $data['tags'] = Blog::where('slug', $slug)->where('status', 1)
            ->pluck('tags')
            ->flatMap(function ($tags) {
                return collect(json_decode($tags))->flatMap(function ($tag) {
                    return explode(',', $tag);
                });
            })->unique()->values();
        // $data['relatedPosts'] = Blog::where('category_id', $data['blog']->category_id)
        //                             ->where('id', '!=', $data['blog']->id)
        //                             ->where('status', 1)
        //                             ->take(5)
        //                             ->get();
        // dd($data);

        return view('frontend.blogs.details', $data);
    }


    public function programs()
    {
        $data = $this->getMetaData('Programs');
        $data['partners'] = Partner::where('status', 1)->get();
        return view('frontend.programs', $data);
    }



    public function newsletter(Request $request)
    {
        $setting = getSetting();

        $request->validate([
            'email' => 'required|email|unique:newsletters,email',
            // 'g-recaptcha-response' => $setting->recaptcha_site_key ? 'required' : 'nullable',
        ], [
            'email.unique' => 'This email is already subscribed.',
            // 'g-recaptcha-response.required' => 'The captcha-response field is required.',
        ]);

        $newsletter = Newsletter::create([
            'email' => $request->email,
        ]);

        Toastr::success('Thank you for subscribing to our newsletter.');
        return redirect()->back();
    }

    /**
     * @param null $title
     * @return array
     */
    private function getMetaData($title = null): array
    {
        $setting = getSetting();
        $data['title'] = $title;
        $data['og_title'] = $title . ' - ' . $setting->site_name;
        $data['og_description'] = $setting->seo_meta_description;
        $data['og_image'] = $setting->seo_image;
        $data['meta_keywords'] = $setting->seo_keywords;
        return $data;
    }

    private function getBlogMetaData(\App\Models\Blog $blog): array
    {
        $setting = getSetting();

        // Meta title: Blog > Site fallback
        $meta_title = $blog->meta_title ?: $blog->title ?: $setting->site_name;

        // Meta description: Blog > Strip details > Site fallback
        $meta_description = $blog->meta_description ?? $setting->seo_meta_description   ;

        // Keywords: Blog > Site
        $meta_keywords = $blog->meta_keywords ? json_decode($blog->meta_keywords,false)[0] : $setting->seo_keywords;

        // Image: Blog image > Site default image
        $meta_image = $blog->image ?? $setting->seo_image;

        return [
            'title'           => $blog->title,
            'og_title'        => $meta_title . ' - ' . $setting->site_name,
            'og_description'  => $meta_description,
            'og_image'        => $meta_image,
            'meta_keywords'   => $meta_keywords,
            'schema_markup'   => $blog->schema_markup ?? null,
        ];
    }

    public function orderInvoice($encryptedId)
    {
        $title = 'Invoice';
        $id = Crypt::decryptString($encryptedId);
        $order = Order::find($id);
        $trnx = Transaction::where('user_id', Auth::user()->id)->where('order_id', $id)->first();
        abort_if(!$trnx, 404);
        return view('frontend.invoice', compact('title', 'trnx', 'order'));
    }

    public function getStateTaxes($state)
    {
        $state = States::where('name', $state)->first();

        if (!$state) {
            return response()->json(['error' => 'State not found'], 404);
        }

        return response()->json([
            'gst' => $state->gst,
            'pst' => $state->pst,
        ]);
    }

    public function readyShippingReminders()
    {
        $orders = Order::where('status', 35)
            ->where('payment_status', 0)
            ->where('ready_shipping_mail', 0)
            ->where('ready_shipping_at', '<=', Carbon::now()->subDays(5))
            ->with('rUser')
            ->get();

        $setting = Setting::first();

        foreach ($orders as $order) {

            $body = "Thank you for your business. Just a reminder your order is ready to be shipped! Please <a href=\"" . route('login') . "\">login</a> and pay for your order at your earliest convenience. We will update your order with a tracking number within 24 hours.";
            $msg = 'Please let us know if you require anything further. Thank you for your business!';
            $subject = 'ACTION REQUIRED: Payment Required To Ship Order';

            $data = [
                'subject' => $subject,
                'greeting' => 'Hello, ' . $order->rUser?->name . ' ' . $order->rUser?->last_name,
                'body' => $body,
                'site_name' => $setting->site_name ?? config('app.name'),
                'thanks' => $msg,
                'site_url' => url('/'),
                'footer' => 1,
            ];

            try {
                Mail::to($order->rUser->email)->send(new OrderMail($data));
                $order->ready_shipping_mail = 1;
                $order->save();
            } catch (\Exception $e) {
                Log::alert("Order mail not sent for Order ID {$order->id}. Error: " . $e->getMessage());
                // return response()->json(['error' => 'Something went wrong. Check logs.'], 500);

            }
        }
        return response()->json(['message' => 'Ready shipping emails processed.'], 200);
    }
}
