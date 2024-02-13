<?php

namespace App\Http\Controllers\Apis;

use App\Helpers\StripePayment;
use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\Country;
use App\Models\Event;
use App\Models\HomeSlide;
use App\Models\Package;
use App\Models\Page;
use App\Models\SettingOption;
use App\Models\User;
use Illuminate\Http\Request;

class CommonController extends Controller
{

    public function home()
    {
        $upcoming_events = Event::upcoming()
            ->with('performer')
            ->latest()
            ->limit(12)
            ->get();
        $this->data['upcoming_events'] = $upcoming_events;

        $popular_channels = User::where('role', '2')
            ->withCount('events')
            ->orderBy('events_count', 'DESC')
            ->limit(8)
            ->get();
        $this->data['popular_channels'] = $popular_channels;

        $slides = HomeSlide::where('status', '1')->get();
        $this->data['slides'] = $slides;

        return $this->response();
    }

    public function get_settings()
    {
        $options_collection = collect(SettingOption::where('status', '1')->get()->toArray());
        $options_filtered = $options_collection->map(function ($item) {
            return collect($item)->only(['id', 'name', 'field']);
        });
        $options_grouped = $options_filtered->groupBy('field');
        $this->data['options'] = $options_grouped->all();


        $social_login_settings = AdminSetting::where(function ($query) {
            $query->where('name', 'google_client_id');
            $query->orWhere('name', 'google_api_key');
            $query->orWhere('name', 'facebook_client_id');
        })->pluck('value', 'name')->all();

        $stripe_settings = AdminSetting::where(function ($query) {
            $query->where('name', 'stripe_test_key');
            $query->orWhere('name', 'stripe_key');
            $query->orWhere('name', 'stripe_mode');
        })->pluck('value', 'name')->all();

        $website_settings = AdminSetting::where(function ($query) {
            $query->where('name', 'website_title');
            $query->orWhere('name', 'logo');
        })->pluck('value', 'name')->all();
        $website_settings['logo'] = storage_url($website_settings['logo']);
        $countries = Country::pluck('name', 'code');

        $events_time_options = AdminSetting::where(function ($query) {
            $query->where('name', 'stream_late_max_time');
            $query->orWhere('name', 'stream_late_penalty');
            $query->orWhere('name', 'stream_early_time');
            $query->orWhere('name', 'stream_auto_leave_timeout');
        })->pluck('value', 'name')->all();

        $pages_to_show_on_website_json = get_setting('pages_to_show_on_website');
        $pages_to_show_on_website = !empty($pages_to_show_on_website_json) ? json_decode($pages_to_show_on_website_json, true) : [];

        $page_columns = ['id', 'slug'];
        foreach ($this->langs as $lang) {
            $page_columns[] = $lang . '_title';
        }
        $pages = Page::select($page_columns)->whereIn('id', $pages_to_show_on_website)->get();

        $this->data['pages_to_show_on_website'] = $pages;

        $this->data['events_time_options'] = $events_time_options;
        $this->data['social_login'] = $social_login_settings;
        $this->data['stripe_settings'] = $stripe_settings;
        $this->data['website_settings'] = $website_settings;
        $this->data['options']['countries'] = $countries;
        $this->data['options']['timezones'] = getTimeZoneList();
        return $this->response();
    }

    public function get_packages(Request $request, $type)
    {
        $user = $request->user();
        if ($type == 'performer') {
            $type_code = '2';
        } elseif ($type == 'user') {
            $type_code = '1';
        } else {
            return abort(404);
        }
        $packages_query = Package::enabled()->where('type', $type_code);
        if ($user->trial_used) {
            $packages_query->where('trial', '0');
        }
        $packages = $packages_query->get();
        $this->data['packages'] = $packages;
        return $this->response();
    }

    public function activate_trial(Request $request)
    {
        $user = $request->user();
        if ($user->trial_used == '1') {
            $this->message = __('package.trial_used_alrady_message');
            return $this->response();
        }

        $package = Package::enabled()
            ->where('type', $user->role)
            ->where('trial', '1')
            ->first();

        $start_date = now()->format('Y-m-d');
        $expiry_date = now()->addDays($package->validity)->format('Y-m-d');

        $order_data = [
            'package_id' => $package->id,
            'amount' => $package->price,
            'events' => $package->events,
            'package_data' => json_encode($package->toArray()),
            'status' => '1',
            'start_date' => $start_date,
            'expiry_date' => $expiry_date,
            'payment_status' => '1',
        ];
        $order = $user->package_orders()->create($order_data);
        if ($order) {
            $user->trial_used = '1';
            $user->save();
            $this->message = __('package.trial_activated_success_message');
            return $this->response();
        }
        return $this->response(false);
    }

    public function get_page_data($slug)
    {
        $page = Page::where('slug', $slug)->first();
        if ($page) {
            $this->data = $page;
            return $this->response();
        }
        return $this->response(false);
    }

    public function package_payment(Request $request, $package_id)
    {
        $user = $request->user();
        $stripe = new StripePayment();
        $package = Package::enabled()->where('type', $user->role)->where('id', $package_id)->first();
        if (!$package) {
            $this->message = __('package.error_invalid_package_id');
            return $this->response(false);
        }
        $order_data = [
            'package_id' => $package->id,
            'amount' => $package->price,
            'events' => $package->events,
            'package_data' => json_encode($package->toArray()),
        ];
        $order = $user->package_orders()->create($order_data);
        if ($package->price > 0) {
            $chargeData = $stripe->createCharge($request->stripe_source, $order);
            $order->payment_response = json_encode($chargeData);
            $order->payment_status = $chargeData->status == 'succeeded' ? '1' : '2';
        } else {
            $order->payment_response = '';
            $order->payment_status = '1';
        }
        if ($order->save()) {
            $this->message = __('package.purchase_success_message');
            return $this->response();
        }
        // return $chargeData;
    }

    public function get_package_orders(Request $request)
    {
        $page = 1;
        $limit = 20;
        if ($request->filled('page')) {
            $page = $request->page;
        }
        $offset = ($page - 1) * $limit;
        $user = $request->user();
        $package_orders = $user->package_orders()
            ->latest()
            ->limit($limit)
            ->offset($offset)
            ->get();
        $this->data['package_orders'] = $package_orders;
        return $this->response();
    }

    public function activate_order_package(Request $request, $order_id)
    {
        $user = $request->user();
        $package_order = $user->package_orders()->find($order_id);
        if (!$package_order || $package_order->status == '2' || $package_order->payment_status != '1') {
            $this->message = __('package.error_invalid_package_order_id');
            return $this->response(false);
        }
        $start_date = now()->format('Y-m-d');
        $active_package_order = $user->package_orders()->where('status', '1')->first();
        if ($active_package_order) {
            $active_package_order->status = '2';
            $active_package_order->decatived_at = $start_date;
            $active_package_order->save();
        }
        $validity = $package_order->package_data->validity;
        $expiry_date = now()->addDays($validity)->format('Y-m-d');
        $package_order->status = '1';
        $package_order->start_date = $start_date;
        $package_order->expiry_date = $expiry_date;
        if ($package_order->save()) {
            $this->message = __('package.activated_successufully');
            return $this->response();
        }
        $this->message = __('package.error_activation_failed');
        return $this->response(false);
    }

    /* public function contact_support(Request $request)
    {
        $user = $request->user();
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'problem' => 'required'
        ]);

        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return $this->response();
        }
        $support_mail = SupportMail::create($request->only(['email', 'problem']));

        $website_title =  get_setting('website_title');
        $template_data = json_decode(get_setting('mail_template_support_mail_for_admin'));

        $content = str_replace(
            ['{{email}}', '{{problem}}'],
            [$support_mail->email, $support_mail->problem],
            $template_data->content
        );
        $support_email = get_setting('website_support_email');
        $mail_data = [
            'email' => $support_email,
            'name' => 'admin',
            'subject' => $template_data->subject . ' - ' . $website_title,
            'content' => $content
        ];

        try {
            Mail::send('emails.users.new-password', $mail_data, function ($message) use ($mail_data) {
                $message->from(get_setting('mail_from_address'), get_setting('mail_from_name'));
                $message->to($mail_data['email'], $mail_data['name'])->subject($mail_data['subject']);
            });
            $this->message = __('user.contact_support_success_message');
            return $this->response();
        } catch (\Exception $e) {
            $this->message = $e->getMessage();
            return $this->response(false);
        }
    } */
}
