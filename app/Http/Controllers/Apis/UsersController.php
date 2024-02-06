<?php

namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\EventAttendee;
use App\Models\User;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class UsersController extends Controller
{

    //////////////////////////////////////////// user auth///////////////////////////////////////////////////////////////////////////////////////

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users|max:191',
            'confirm_email' => 'required|same:email',
            'first_name' => 'required|max:191',
            'last_name' => 'required|max:191',
            'password' => 'required|min:8|max:25',
        ]);

        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return $this->response();
        }

        $user = User::create([
            'username' => 'bcplus' . time(),
            'email' => $request->email,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'password' => bcrypt($request->password),
            'status' => '0',
        ]);

        Auth::login($user);
        $token = $user->createToken(microtime())->accessToken;
        $this->send_email_verification_email($user);
        $this->data['token'] = $token;
        $this->data['user'] = $user;
        return $this->response();
    }

    public function resend_verification_email(Request $request)
    {
        $user = $request->user();
        $send = $this->send_email_verification_email($user);
        $this->data['send'] = $send;
        if ($send) {
            $this->message = 'Email sent Successfully.';
            return $this->response();
        } else {
            $this->message = 'Email not sent Successfully.';
            return $this->response(false);
        }
    }

    public function send_email_verification_email($user)
    {
        try {
            // $user = User::find($user);
            $website_title =  get_setting('website_title');
            $template_data = json_decode(get_setting('mail_template_user_email_verification'));
            $token = base64_encode($user->email . '##' . $user->username) . '##' . strtotime('+1 minutes', time());
            $confirm_email_link = route('verify_user_email', encrypt($token));

            $varibales = [
                '{{username}}',
                '{{full_name}}',
                '{{email}}',
                '{{confirm_link}}'
            ];
            $values = [
                $user->username,
                $user->full_name,
                $user->email,
                $confirm_email_link
            ];

            $content = str_replace($varibales, $values, $template_data->content);
            $mail_data = [
                'email' => $user->email,
                'name' => $user->full_name,
                'subject' => $template_data->subject . ' - ' . $website_title,
                'confirm_email_link' => $confirm_email_link,
                'content' => $content
            ];
            Mail::send('emails.user-defined', $mail_data, function ($message) use ($mail_data) {
                $message->to($mail_data['email'], $mail_data['name'])->subject($mail_data['subject']);
            });
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
            return false;
        }
    }

    public function verify_user_email($token)
    {
        try {
            $token_data = decrypt($token);
        } catch (DecryptException $e) {
            return abort(404);
        }
        $en_data = explode('##', $token_data);
        $user_data = explode('##', base64_decode($en_data[0]));
        $expiry_time = $en_data[1];
        $user_email = $user_data[0];
        $username = $user_data[1];

        if ($expiry_time < time()) {
            return  __('settings.personal.email_link_expired');
        }
        $user = User::where('email', $user_email)->where('username', $username)->first();
        if ($user) {
            $user->status = '1';
            if ($user->save()) {
                return redirect('https://bcplusnews.com/verification-pending');
            }
        }
        return abort(404);
    }


    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return $this->response();
        }

        $credentials = $request->only('email', 'password');
        $auth = Auth::attempt($credentials);

        if (!$auth) {
            $this->message = __('auth.login.errors.failed');
            return $this->response(false);
        }

        $user = Auth::user();
        if ($user->status == '2') {
            $this->message = __('auth.login.errors.suspended');
            Auth::logout();
            return $this->response(false);
        }

        $token = $user->createToken(microtime())->accessToken;
        $this->data['user'] = $user;
        $this->data['token'] = $token;
        return $this->response();
    }

    public function social_login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provider_token' => 'required',
            'provider' => 'required',
            // 'email' => 'required|email|max:191|exists:users'
        ], [
            // 'email.exists' => 'not_exists'
        ]);

        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return $this->response();
        }

        if ($request->provider == 'facebook') {
            $user_info = Socialite::driver($request->provider)->fields([
                'first_name', 'last_name', 'email'
            ])->userFromToken($request->provider_token);

            $user_data = collect($user_info->user)->only('first_name', 'last_name', 'email');
        } else if ($request->provider == 'google') {
            // return $request->all();
            $user_info = Socialite::driver($request->provider)->userFromToken($request->provider_token);
            // $user_data = collect($user_info->user)->only('given_name', 'family_name', 'email');
            $user_data = [
                'first_name' => $user_info->user['given_name'],
                'last_name' => $user_info->user['family_name'],
                'email' => $user_info->user['email']
            ];
        } else {
            $this->message = "Only facebook and google are supported for login";
            return $this->response(false);
        }

        $user = User::where('email', $user_info->email)->first();
        if (!$user) {
            $this->data['user_info'] = $user_data;
            $this->data['not_registerd'] = true;
            return $this->response();
        }
        Auth::login($user);
        $token = $user->createToken(microtime())->accessToken;
        $this->data['user'] = $user;
        $this->data['token'] = $token;
        return $this->response();
    }

    public function send_new_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users'
        ], [
            'email.exists' => __('user.email_not_found')
        ]);
        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return $this->response();
        }
        try {
            $user = User::where('email', $request->email)->first();
            $website_title =  get_setting('website_title');
            $template_data = json_decode(get_setting('mail_template_forgot_password'));
            $mail_data = [
                'email' => $user->email,
                'name' => $user->full_name,
                'subject' => $template_data->subject . ' - ' . $website_title,
                'content' => $this->replace_variables_email_content($template_data->content, $user, 'forgot_password')
            ];
            // return view('emails.users.new-password', $mail_data);
            Mail::send('emails.user-defined', $mail_data, function ($message) use ($mail_data) {
                // $message->from(get_setting('mail_from_address'), get_setting('mail_from_name'));
                $message->to($mail_data['email'], $mail_data['name'])->subject($mail_data['subject']);
            });
        } catch (\Exception $e) {
            $this->message = $e->getMessage();
            return $this->response(false);
        }
        $this->message = __('user.password_sent_to_email_success', ['email' => $request->email]);
        return $this->response();
    }

    public function recover_account(Request $request)
    {
        // return view('emails.users.new-password');
        $validator = Validator::make($request->all(), [
            'username' => 'required|exists:users'
        ], [
            'username.exists' => __('user.username_not_found')
        ]);
        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return $this->response();
        }
        $user = User::where('username', $request->username)->first();
        $email_chars = array_unique(str_split(explode('@', $user->email)[0]));
        shuffle($email_chars);
        $replaceable_chars = array_splice($email_chars, 0, 5);
        $encoded_email =  str_replace($replaceable_chars, ['*', '*', '*', '*', '*'], $user->email);
        // $replaceable_chars = array_splice(shuffle($email_chars), 0, 5);
        // foreach ($email_chars as $key => &$char) {
        //     if ($key % 2 != 0 && $char != '@' && $char != '.' &&  in_array($char, $replaceable_chars)) {
        //         $char = '*';
        //     }
        // }
        // $encoded_email = implode('', $email_chars);
        $this->message = __('user.account_recovered_success');
        $this->data['email'] = $encoded_email;
        return $this->response();
    }

    private function replace_variables_email_content($content, $user, $template_name)
    {
        $varibales = [
            '{{full_name}}',
            '{{email}}',
            '{{username}}',
            '{{new_password}}'
        ];

        if ($template_name == 'forgot_password') {
            $new_password = Str::random(15);
            $user->password = bcrypt($new_password);
            $user->save();
        }
        $values = [
            $user->full_name,
            $user->email,
            $user->username,
            $new_password
        ];

        return str_replace($varibales, $values, $content);
    }

    public function change_password(Request $request)
    {
        $user = $request->user();
        $validator = Validator::make($request->all(), [
            'new_password' => 'required|min:8|max:15',
            'confirm_password' => 'required|same:new_password',
            'password' => 'required|min:8|max:15',
        ]);

        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return $this->response();
        }

        if (!Hash::check($request->password, $user->password)) {
            $this->message = 'user.invalid_current_password';
            return $this->response(false);
        }

        $update = $user->update(['password' => bcrypt($request->new_password)]);
        if ($update) {
            $this->message = __('user.password_update_success_message');
            return $this->response();
        }
        $this->message = __('user.password_update_failed_message');
        return $this->response(false);
    }

    public function profile(Request $request)
    {
        $user = $request->user();
        if (!$user->country) {
            $user->country = 'GB';
        }
        $performer_types = $user->performer_types->pluck('performer_type_id');
        $user->performer_type = $performer_types;
        $this->data['user'] = $user;
        return $this->response();
    }

    public function update_profile(Request $request)
    {
        $user = $request->user();
        $validator = Validator::make($request->all(), [
            'email' => 'required|max:191|unique:users,email,' . $user->id,
            'username' => 'required|max:191|unique:users,username,' . $user->id,
            'first_name' => 'required|max:191',
            'last_name' => 'required|max:191',
            'headline' => 'required|max:191',
            'bio' => 'required',
            'performer_type' => 'required_if:become_perfomer,true|array',
            'organization_name' => 'max:191|required_if:become_perfomer,true',
            'organization_email' => 'max:191|required_if:become_perfomer,true',
            'address_line1' => 'required|max:191',
            'address_line2' => 'max:191',
            'city' => 'required|max:191',
            'state' => 'max:191',
            'country' => 'required|max:191|exists:countries,code',
            'zip' => 'max:191',
        ]);

        if ($validator->fails()) {
            $this->message = __('common.errors.form_error');
            $this->errors = $validator->errors();
            return $this->response();
        }

        $data = $request->only([
            'email',
            'first_name',
            'last_name',
            'username',
            'headline',
            'bio',
            'organization_name',
            'organization_email',
            'address_line1',
            'address_line2',
            'city',
            'state',
            'country',
            'zip',
        ]);

        $data['social_profiles'] = json_encode($request->only([
            'google_url',
            'facebook_url',
            'linkedin_url',
            'instagram_url',
            'tiktok_url',
            'snapchat_url',
            'whatsapp_url',
        ]));

        if ($request->become_perfomer) {
            $data['role'] = '2';
        }

        if ($request->filled('performer_type') && is_array($request->performer_type)) {
            foreach ($request->performer_type as $performer_type_id) {
                $user->performer_types()->create([
                    'performer_type_id' => $performer_type_id
                ]);
            }
        }

        if ($request->filled('photo')) {
            $image = base64_to_image($request->photo, '/profile', $user->username);
            if (!$image['status']) {
                $this->message = $image['message'];
                return $this->response(false);
            }
            $data['photo'] = $image['image_path'];
            @unlink(public_path() . '/' . $user->getRawOriginal('photo'));
        }

        if ($request->filled('banner')) {
            $image = base64_to_media($request->banner, '/banner');
            if (!$image['status']) {
                $this->message = $image['message'];
                return $this->response(false);
            }
            $data['banner'] = $image['media_path'];
            @unlink(public_path() . '/' . $user->getRawOriginal('banner'));
        }

        if ($user->update($data)) {
            $this->data['user'] = $user;
            $this->message = __('user.update_profile_success_message');
            return $this->response();
        }
        $this->message = __('common.errors.something');
        return $this->response();
    }

    /* public function performer_dashboard(Request $request)
    {
        $page = 1;
        $limit = 20;
        if ($request->filled('page')) {
            $page = $request->page;
        }
        $offset = ($page - 1) * $limit;
        $user = $request->user();
        $attendees_query = EventAttendee::with('user', 'event')->select('*');

        $attendees_query->whereHas('event.performer', function ($query) use ($user) {
            $query->where('id', $user->id);
        });

        if ($request->filled('event')) {
            $attendees_query->whereHas('event', function ($query) use ($request) {
                $query->where('id', $request->event);
            });
        }

        if ($request->filled('user')) {
            $attendees_query->whereHas('user', function ($query) use ($request) {
                $query->where('id', $request->user);
            });
        }

        $attendees = $attendees_query
            ->limit($limit)
            ->offset($offset)
            ->get();
        $attendees->each->append('payment_status_label');

        $users = User::whereIn('id', EventAttendee::pluck('user_id'))->get();

        $this->data['users'] = $users;
        $this->data['attendees'] = $attendees;
        $this->data['events'] = $user->events;
        return $this->response();
    } */

    public function get_performer_profile(Request $request, $username)
    {
        $user = $request->user();
        $performer = User::where('username', $username)
            ->withCount('reviews')
            ->with('events', 'reviews.reviewer', 'reviews.event')
            ->with('reviews', function ($query) {
                $query->latest();
                $query->limit(20);
            })
            ->with('events', function ($query) {
                $query->latest();
                $query->limit(12);
            })
            ->where('role', '2')
            ->first();
        if ($performer) {
            $posted_review = false;

            if ($user) {
                $posted_review =  $performer->reviews()->where('reviewer_id', $user->id)->first();
                return $posted_review;
            }
            $this->data['performer'] =  $performer;
            $this->data['review_posted'] =  $posted_review;
            return $this->response();
        }
        $this->message = __('user.performer_not_found');
        return $this->response(false);
    }

    public function performers_list(Request $request)
    {
        $user = $request->user();
        $performers = User::where('role', '2')
            ->where('id', '!=', $user->id)
            ->get();
        $this->data['performers'] = $performers;
        return $this->response();
    }


    public function get_reviews(Request $request, $username)
    {
        $page = 2;
        $limit = 20;
        if ($request->filled('page')) {
            $page = $request->page;
        }
        $offset = ($page - 1) * $limit;
        $performer = User::where('username', $username)
            ->where('role', '2')
            ->first();

        if ($performer) {
            $reviews =  $performer->reviews()
                ->with('reviewer')
                ->latest()
                ->limit($limit)
                ->offset($offset)
                ->get();
            $this->data['reviews'] = $reviews;
            return $this->response();
        } else {
            $this->message = __('user.performer_not_found');
        }
    }

    public function get_events(Request $request, $username)
    {
        $page = 2;
        $limit = 12;
        if ($request->filled('page')) {
            $page = $request->page;
        }
        $offset = ($page - 1) * $limit;
        $performer = User::where('username', $username)
            ->where('role', '2')
            ->first();

        if ($performer) {
            $events =  $performer->events()
                ->latest()
                ->limit($limit)
                ->offset($offset)
                ->get();
            $this->data['events'] = $events;
            return $this->response();
        } else {
            $this->message = __('user.performer_not_found');
        }
    }
}
