<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\Event;
use App\Models\EventAttendee;
use App\Models\HomeSlide;
use App\Models\Page;
use App\Models\SettingOption;
use App\Models\User;
use Illuminate\Http\Request;

class CommonController extends Controller
{
    public function dashboard()
    {
        $page_title = __('dashboard.page_title');
        $todays_users_count = User::whereRaw("DATE(created_at) = DATE(NOW())")->count();
        $todays_sale_amount = "5000";
        $total_user_count = User::count();
        $total_sale_amount = "65645000";
        $currency = "NT";

        $recent_users = User::latest()->limit(10)->get();
        // return $recent_users;
        return view('admin.dashboard', compact(
            'page_title',
            'todays_users_count',
            'todays_sale_amount',
            'total_sale_amount',
            'total_user_count',
            'recent_users',
            'currency',
        ));
    }

    public function settings_page()
    {
        $page_title = __('settings.page_title');
        $pages = Page::get();
        $event_types = SettingOption::where('field', 'event_types')
            ->where('status', '1')
            ->get();
        return view('admin.settings', compact('page_title', 'pages', 'event_types'));
    }

    public function mail_settings_page()
    {
        $page_title = __('settings.mail.page_title');
        $mail_settings = AdminSetting::where('name', 'LIKE', 'mail_%')->pluck('value', 'name');
        $mail_templates = mail_templates();
        return view('admin.mail-settings', compact('page_title', 'mail_settings', 'mail_templates'));
    }

    public function payment_settings_page()
    {
        $page_title = __('settings.payment.page_title');
        return view('admin.payment-settings', compact('page_title'));
    }

    public function agora_settings_page()
    {
        $page_title = __('settings.agora.page_title');
        return view('admin.agora-settings', compact('page_title'));
    }

    public function app_cache_update()
    {
        $app_settings_update =  AdminSetting::updateorcreate(['name' => 'app_settings_update']);
        $app_settings_update->value = time();
        $app_settings_update->save();
        return back()->with('success', __('common.app_cache_updated_success'));
    }

    public function setting_option_index($field)
    {
        if (!in_array($field, get_setting_fields())) {
            abort(404);
        }
        $settingOption = SettingOption::where('field', $field)->get();
        $field_title =  __('common.setting_options_fields.' . $field);
        $page_title = __('settings.options.page_title', ['field_title' => $field_title]);
        return view('admin.setting_option', compact('page_title', 'settingOption', 'field', 'field_title'));
    }

    public function setting_option_create(Request $request, $field)
    {
        $rules = [];
        foreach ($this->langs as $lang) {
            $rules[$lang . '_name'] = 'required';
        }

        $this->validate($request, $rules);

        $response = [
            'status' => false,
            'message' => __('common.errors.something'),
            'data' => []
        ];

        $option_data = [
            'field' => $field
        ];

        foreach ($this->langs as $lang) {
            $option_data[$lang . '_name'] = $request->{$lang . '_name'};
        }

        $tag = SettingOption::create($option_data);
        if ($tag) {
            $response = [
                'status' => true,
                'message' => __('settings.options.created_success_message'),
                'data' => []
            ];
        }
        return response()->json($response);
    }

    public function setting_option_update(Request $request, $field)
    {
        $rules = [];
        foreach ($this->langs as $lang) {
            $rules[$lang . '_name'] = 'required';
        }

        $this->validate($request, $rules);

        $response = [
            'status' => false,
            'message' => __('common.errors.something'),
            'data' => []
        ];
        $option_data = [];
        foreach ($this->langs as $lang) {
            $option_data[$lang . '_name'] = $request->{$lang . '_name'};
        }

        $edit_tag = SettingOption::where('id', $request->id)
            ->update($option_data);
        if ($edit_tag) {
            $response = [
                'status' => true,
                'message' => __('settings.options.updated_success_message'),
                'data' => []
            ];
        }
        return response()->json($response);
    }

    public function setting_option_change_status(Request $request, $id)
    {
        if (!$request->ajax()) {
            return abort(404);
        }
        $response = [
            'status' => false,
            'message' => __('common.errors.something'),
            'data' => []
        ];

        $tag = SettingOption::find($id);

        if (!$tag) {
            return response()->json($response);
        }

        $tag->status = ($tag->status == "1") ? '0' : '1';

        if ($tag->save()) {
            $response = [
                'status' => true,
                'message' => __('settings.options.status_updated_success_message'),
                'data' => []
            ];
        }
        return response()->json($response);
    }

    public function home_slider_page()
    {
        $page_title = __('settings.home_slider.page_title');
        $slides = HomeSlide::get();
        return view('admin.home-page-slider', compact('page_title', 'slides'));
    }

    public function view_home_slide(Request $request, $id)
    {
        if (!$request->ajax()) {
            abort(404);
        }
        $response = [
            'status' => false,
            'message' => __('common.errors.something'),
            'data' => []
        ];
        $slide = HomeSlide::find($id);
        $slide->append('update_url');
        if (!$slide) {
            $response['message'] = __('settings.home_slider.invalid_slide_id');
            return response()->json($response);
        }
        $response['slide'] = $slide;
        $response['status'] = true;
        return response()->json($response);
    }

    public function create_home_slide(Request $request)
    {
        if (!$request->ajax()) {
            abort(404);
        }
        $response = [
            'status' => false,
            'message' => __('common.errors.something'),
            'data' => []
        ];
        $this->validate($request, [
            'language' => 'required',
            'image' => 'required',
            'title' => 'required',
            'subtitle' => 'required',
            // 'button_url' => 'required',
            // 'button_label' => 'required',
            'slide_order' => 'required',
        ]);
        $slide_data = $request->only([
            'language',
            'title',
            'subtitle',
            'button_url',
            'button_label',
            'slide_order',
        ]);

        $img_result = base64_to_image($request->image, '/home-slider');

        if ($img_result['status']) {
            $slide_data['image'] = $img_result['image_path'];
            $slide = HomeSlide::create($slide_data);
            if ($slide) {
                $response['status'] = true;
                $response['message'] = __('settings.home_slider.slide_create_success');
            } else {
                $response['message'] = __('settings.home_slider.slide_create_failed');
            }
        } else {
            $response['message'] = $img_result['message'];
        }
        return response()->json($response);
    }

    public function update_home_slide(Request $request, $id)
    {
        if (!$request->ajax()) {
            abort(404);
        }

        $response = [
            'status' => false,
            'message' => __('common.errors.something'),
            'data' => []
        ];
        $this->validate($request, [
            // 'image' => 'required',
            'language' => 'required',
            'title' => 'required',
            'subtitle' => 'required',
            // 'button_url' => 'required',
            // 'button_label' => 'required',
            'slide_order' => 'required',
        ]);

        $slide = HomeSlide::find($id);
        if (!$slide) {
            $response['message'] = __('settings.home_slider.invalid_slide_id');
            return response()->json($response);
        }

        $slide_data = $request->only([
            'language',
            'title',
            'subtitle',
            'button_url',
            'button_label',
            'slide_order',
        ]);

        $old_image_path = $slide->image;
        if ($request->filled('image')) {
            $img_result = base64_to_image($request->image, '/home-slider');
            if ($img_result['status']) {
                $slide_data['image'] = $img_result['image_path'];
            } else {
                $response['message'] = $img_result['message'];
                return response()->json($response);
            }
        }

        if ($slide->update($slide_data)) {
            @unlink(public_path($old_image_path));
            $response['status'] = true;
            $response['message'] = __('settings.home_slider.slide_create_success');
        } else {
            $response['message'] = __('settings.home_slider.slide_create_failed');
        }
        return response()->json($response);
    }

    public function delete_home_slide(Request $request, $id)
    {
        if (!$request->ajax()) {
            abort(404);
        }
        $response = [
            'status' => false,
            'message' => __('common.errors.something'),
            'data' => []
        ];
        $slide = HomeSlide::find($id);
        $slide->append('update_url');
        if (!$slide) {
            $response['message'] = __('settings.home_slider.invalid_slide_id');
            return response()->json($response);
        }
        $response['status'] = true;
        $slide->delete();
        return response()->json($response);
    }

    public function update_website_details(Request $request)
    {
        if (!$request->ajax()) {
            abort(404);
        }
        $response = [
            'status' => true,
            'message' => __('settings.website.update_success'),
            'data' => []
        ];
        $settings = $request->all();
        unset($settings['_token']);
        foreach ($settings as $key => $value) {
            $setting = AdminSetting::updateorcreate([
                'name' => $key
            ]);
            if ($key == 'logo' || $key == 'login_page_image') {
                $old_path = $setting->value;
                $img_result = base64_to_image($value, '/settings', $key);
                if ($img_result['status']) {
                    $value = $img_result['image_path'];
                } else {
                    $image_errors[$key . '_error'] = $img_result['message'];
                    continue;
                }
            }
            // $setting->value = empty($value) ? "" : $value;
            $setting->value = $value;
            $setting->save();
        }
        if (!empty($image_errors)) {
            array_merge($response, $image_errors);
        } else {
            @unlink(public_path($old_path));
        }
        return response()->json($response);
    }

    /* public function ck_image_upload(Request $request)
    {
        $this->validate($request, [
            'upload' => 'required|mimes:jpg,jpeg,png'
        ]);

        $file = $request->file('upload');
        $org_file_name = $file->getClientOriginalName();
        $file_base_name = basename($file->getClientOriginalName(), '.' . $file->getClientOriginalExtension());
        $file_extension = $file->getClientOriginalExtension();
        $file_name = Str::slug($file_base_name) . '.' . $file_extension;
        $image = $file->storeAs(
            'pages',
            $file_name,
            'public'
        );

        $response = [
            'url' => url('storage/' . $image),
        ];
        return response()->json($response);
    } */

    public function get_mail_template(Request $request)
    {
        $this->validate($request, [
            'template_name' => 'required'
        ]);

        $temp_name = 'mail_template_' . $request->template_name;
        $mail_template = mail_templates($request->template_name);

        $mail_varibales = [];
        if (isset($mail_template['mail_varibales'])) {
            $mail_varibales = $mail_template['mail_varibales'];
        }

        $template_data = AdminSetting::where('name', $temp_name)->first();
        if (!$template_data) {
            $template_data = AdminSetting::create([
                'name' => 'mail_template_' . $request->template_name,
                'value' => json_encode(['subject' => '', 'content' => '']),
            ]);
        }

        $response = [
            'status' => true,
            'message' => '',
            'mail_varibales' => $mail_varibales,
            'data' => json_decode($template_data->value)
        ];
        return response()->json($response);
    }

    public function update_mail_template(Request $request)
    {
        $this->validate($request, [
            'template_name' => 'required',
            'subject' => 'required',
            'content' => 'required',
        ]);

        $temp_name = 'mail_template_' . $request->template_name;
        AdminSetting::where('name', $temp_name)
            ->update([
                'value' => json_encode([
                    'subject' => $request->subject,
                    'content' => $request->content,
                ])
            ]);

        $response = [
            'status' => true,
            'message' => __('settings.mail.templates.update_success'),
            'data' => []
        ];
        return response()->json($response);
    }

    public function events_list()
    {
        $page_title = __('event.page_title');
        return view('admin.events-list', compact('page_title'));
    }

    public function events_datatable(Request $request, $type = "")
    {
        $draw = $request->draw;
        $start = $request->start;
        $length = $request->length;
        // $search = $request->search['value'];
        // $sort_by = $request->order[0]['column'];
        // $sort_direction = $request->order[0]['dir'];
        $events_query = Event::with('performer')->select('*');

        if ($request->filled('title')) {
            $events_query->where('title', 'LIKE', '%' . $request->title . '%');
        }
        if ($request->filled('performer')) {
            $events_query->whereHas('performer', function ($query) use ($request) {
                $query->where('username', 'LIKE', '%' . $request->performer . '%');
            });
        }

        //search
        // if (!empty($search)) {
        //     $events_query->where(function ($query) use ($search) {
        //         $query->orWhere('users.id', '=', $search);
        //         $query->orWhere('users.username', 'like', '%' . $search . '%');
        //         $query->orWhere('users.email', 'like', '%' . $search . '%');
        //     });
        // }

        //sorting
        // if ($sort_by == 0) {
        //     $events_query->orderBy('id', $sort_direction);
        // } elseif ($sort_by == 1) {
        //     $events_query->orderBy('username', $sort_direction);
        // } elseif ($sort_by == 2) {
        //     $events_query->orderBy('email', $sort_direction);
        // } elseif ($sort_by == 3) {
        //     $events_query->orderBy('first_name', $sort_direction);
        // } elseif ($sort_by == 4) {
        //     $events_query->orderBy('last_name', $sort_direction);
        // } elseif ($sort_by == 5) {
        //     $events_query->orderBy('status', $sort_direction);
        // }

        $total_events = $events_query->count();
        $events = $events_query->limit($length)->offset($start)->get();
        $events->each->append('event_type');

        $data = array(
            'draw' => $draw,
            'recordsTotal' => $total_events,
            'recordsFiltered' => $total_events,
            'data' => $events
        );
        return response()->json($data);
    }

    public function attendees_reports_page()
    {
        $page_title = __('attendees_report.page_title');
        $events = Event::get();
        $users = User::get();
        return view('admin.attendees_report', compact('page_title', 'events', 'users'));
    }

    public function attendees_reports_datatable(Request $request, $type = "")
    {
        $draw = $request->draw;
        $start = $request->start;
        $length = $request->length;
        // $search = $request->search['value'];
        // $sort_by = $request->order[0]['column'];
        // $sort_direction = $request->order[0]['dir'];
        $events_query = EventAttendee::with('user', 'event.performer');

        if ($request->filled('event')) {
            $events_query->whereHas('event', function ($query) use ($request) {
                $query->where('id', $request->event);
            });
        }
        if ($request->filled('user')) {
            $events_query->whereHas('user', function ($query) use ($request) {
                $query->where('id', $request->user);
            });
        }
        if ($request->filled('performer')) {
            $events_query->whereHas('event.performer', function ($query) use ($request) {
                $query->where('id', $request->performer);
            });
        }

        //search
        // if (!empty($search)) {
        //     $events_query->where(function ($query) use ($search) {
        //         $query->orWhere('users.id', '=', $search);
        //         $query->orWhere('users.username', 'like', '%' . $search . '%');
        //         $query->orWhere('users.email', 'like', '%' . $search . '%');
        //     });
        // }

        //sorting
        // if ($sort_by == 0) {
        //     $events_query->orderBy('id', $sort_direction);
        // } elseif ($sort_by == 1) {
        //     $events_query->orderBy('username', $sort_direction);
        // } elseif ($sort_by == 2) {
        //     $events_query->orderBy('email', $sort_direction);
        // } elseif ($sort_by == 3) {
        //     $events_query->orderBy('first_name', $sort_direction);
        // } elseif ($sort_by == 4) {
        //     $events_query->orderBy('last_name', $sort_direction);
        // } elseif ($sort_by == 5) {
        //     $events_query->orderBy('status', $sort_direction);
        // }

        $events_query_totals = clone $events_query;
        $total_events = $events_query->count();
        $events = $events_query->limit($length)->offset($start)->get();
        $events->each->append('payment_status_label');

        $events_query_totals->where('payment_status', '1');
        $total_amount = $events_query_totals->sum('total_amount');
        $total_admin_commission = $events_query_totals->sum('admin_commission');
        $total_performers_amount = $events_query_totals->sum('amount');

        // $total_amount = EventAttendee::where('payment_status', '1')->sum('total_amount');
        // $total_admin_commission = EventAttendee::where('payment_status', '1')->sum('admin_commission');
        // $total_performers_amount = EventAttendee::where('payment_status', '1')->sum('amount');

        $data = array(
            'draw' => $draw,
            'recordsTotal' => $total_events,
            'recordsFiltered' => $total_events,
            'data' => $events,
            'total_amount' => $total_amount,
            'total_admin_commission' => $total_admin_commission,
            'total_performers_amount' => $total_performers_amount,
        );
        return response()->json($data);
    }

    public function attendee_check()
    {
        return view('admin.attendee_check');
    }

    public function attendee_check_result(Request $request)
    {
        $attendee_id = $request->attendee_id;
        $ret = array(
            'success' => FALSE,
            'message' => 'no such attendee'
        );

        $event_attendee = EventAttendee::with('user', 'event')->find($attendee_id);
        if ($event_attendee && $event_attendee->user && $event_attendee->event) {
            $ret = array(
                'success' => TRUE,
                'event' => $event_attendee->event,
                'attendee' => array(
                    'name' => $event_attendee->user->first_name . ' ' . $event_attendee->user->last_nam,
                    'email' => $event_attendee->user->email,
                    'photo' => $event_attendee->user->photo,
                )
            );
        }

        return response()->json($ret);
    }
}
