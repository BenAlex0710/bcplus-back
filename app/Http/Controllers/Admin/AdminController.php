<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    public function login_page()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    public function login(Request $request)
    {
        if (!$request->ajax()) {
            return abort(404);
        }
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $response = [
            'status' => false,
            'message' => __('common.errors.something'),
            'data' => []
        ];

        $remember_me = $request->has('remember') ? true : false;

        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password], $remember_me)) {

            $message = __('auth.login.success_message');
            $response['status'] = true;
            $response['message'] = $message;

            if (Auth::check()) {
                Auth::logout();
            }
        } else {
            $response['message'] = __('auth.login.errors.failed');
        }
        return response()->json($response);
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }

    public function forgot_password(Request $request)
    {
        if (!$request->ajax()) {
            return abort(404);
        }
        $this->validate($request, [
            'email' => 'required|email'
        ]);
        $response = [
            'status' => false,
            'message' => __('common.errors.something'),
            'data' => []
        ];

        if ($admin = Admin::where('email', $request->email)->first()) {

            $token = md5(microtime()) . '##' . strtotime('+' . get_setting('reset_password_link_expire_time') . ' minutes', time());
            $admin->password = $token;
            $website_title = get_setting('website_title');
            $password_reset_link = route('admin.rest-password-link', encrypt($token));

            $mail_data = [
                'name' => $admin->name,
                'email' => $admin->email,
                'password_reset_link' => $password_reset_link,
                'subject' => __('auth.forgot_password.mail_subject', ['website_title' => $website_title])
            ];
            try {
                Mail::send('emails.forgot-password-email', $mail_data, function ($message) use ($mail_data) {
                    // $message->from(get_setting('mail_from_address'), get_setting('mail_from_name'));
                    $message->to($mail_data['email'], $mail_data['name'])->subject($mail_data['subject']);
                });
                $admin->save();
                $response['status'] = true;
                $response['message'] = __('auth.forgot_password.link_sent_success');
            } catch (\Exception $e) {
                $response['message'] = $e->getMessage();
            }
        }
        return response()->json($response);
    }

    public function reset_password_page(Request $request, $token)
    {
        try {
            $password = decrypt($token);
        } catch (DecryptException $e) {
            return abort(404);
        }
        $expiry_time = explode('##', $password);
        if ($expiry_time[1] < time()) {
            return redirect()->route('admin.login')->with('error',  __('auth.forgot_password.errors.link_expired'));
        }
        return view('admin.reset-password', compact('token'));
    }

    public function reset_password(Request $request, $token)
    {
        $this->validate($request, [
            'password' => 'required|min:8|confirmed',
        ]);
        $response = [
            'status' => false,
            'message' =>  __('auth.forgot_password.errors.link_expired'),
            'data' => []
        ];
        try {
            $password = decrypt($token);
        } catch (DecryptException $e) {
            return response()->json($response);
        }

        $expiry_time = explode('##', $password);
        if ($expiry_time[1] < time()) {
            return response()->json($response);
        }

        if ($admin = Admin::where('password', $password)->first()) {
            $admin->password = bcrypt($request->password);
            $admin->save();
            $response['status'] = true;
            $response['message'] = __('auth.forgot_password.password_changed_success');
        }
        return response()->json($response);
    }

    public function update_personal_details(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'new_password' => 'nullable|min:8',
            'old_password' => 'required',
        ]);
        $response = [
            'status' => false,
            'message' => __('common.errors.something'),
            'data' => []
        ];

        $admin = Auth::guard('admin')->user();
        $admin_id = $admin->id;

        if (!Hash::check($request->old_password, $admin->password)) {
            $response['message'] = __('settings.personal.errors.incorrect_old_password');
        } else {
            $mail_confirm_message = '';
            $admin->name = $request->name;

            // email change proccess
            if ($admin->email != $request->email) {

                $token = base64_encode($request->email . '##' . $admin_id) . '##' . strtotime('+1 minutes', time());
                $confirm_email_link = route('admin.update-email-confirmation', encrypt($token));
                $website_title = get_setting('website_title');

                $mail_data = [
                    'name' => $admin->name,
                    'email' => $request->email,
                    'confirm_email_link' => $confirm_email_link,
                    'subject' => __('settings.personal.email_confirmation_mail_subject', ['website_title' => $website_title])
                ];

                try {
                    Mail::send('emails.admin-email-update', $mail_data, function ($message) use ($mail_data) {
                        $message->from(get_setting('mail_from_address'), get_setting('mail_from_name'));
                        $message->to($mail_data['email'], $mail_data['name'])->subject($mail_data['subject']);
                    });
                    $mail_confirm_message = __('settings.personal.email_changed_message');
                } catch (\Exception $e) {
                    // $mail_confirm_message = ' mail not triggered so Email can not be change at this time.';
                    $mail_confirm_message = __('settings.personal.email_cant_change_now');
                }
            }

            //password change
            if ($request->has('new_password') && $request->new_password != null) {
                $admin->password = $request->new_password;
            }

            // return $request->all();
            if ($admin->save()) {
                $response['status'] = true;
                $response['message'] = __('settings.personal.update_success') . ' ' . $mail_confirm_message;
            }
        }
        return response()->json($response);
    }

    public function update_email_confirmation(Request $request, $token)
    {
        try {
            $token_data = decrypt($token);
        } catch (DecryptException $e) {
            return abort(404);
        }
        $en_data = explode('##', $token_data);
        $admin_data = explode('##', base64_decode($en_data[0]));
        $expiry_time = $en_data[1];
        $admin_new_email = $admin_data[0];
        $admin_id = $admin_data[1];

        if ($expiry_time < time()) {
            if (Auth::guard('admin')->check()) {
                return redirect()->route('admin.settings')->with('error',  __('settings.personal.email_link_expired'));
            }
            return redirect()->route('admin.login')->with('error',  __('settings.personal.email_link_expired'));
        }

        $admin = Admin::findorFail($admin_id);
        $admin->email = $admin_new_email;

        if ($admin->save()) {
            if (Auth::guard('admin')->check()) {
                return redirect()->route('admin.settings')->with('success',  __('settings.personal.email_updated_success'));
            }
            return redirect()->route('admin.login')->with('success',  __('settings.personal.email_updated_success'));
        }
        return redirect()->route('admin.login')->with('error',  __('common.errors.something'));
    }
}
