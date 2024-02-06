<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Config;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $data = [];
    public $errors;
    public $message = '';
    public $langs = [];

    function __construct()
    {
        $this->langs = get_app_language();
        $this->set_mail_configs();
    }

    private function set_mail_configs()
    {
        $mail_settings = AdminSetting::where('name', 'LIKE', 'mail_%')->pluck('value', 'name');
        // dd($mail_settings);
        if ($mail_settings->isEmpty()) return;
        try {
            Config::set('mail.default', $mail_settings['mail_mailer']);
            Config::set('mail.mailers.smtp.host', $mail_settings['mail_host']);
            Config::set('mail.mailers.smtp.port', $mail_settings['mail_port']);
            Config::set('mail.mailers.smtp.encryption', $mail_settings['mail_encryption']);
            Config::set('mail.mailers.smtp.username', $mail_settings['mail_username']);
            Config::set('mail.mailers.smtp.password', $mail_settings['mail_password']);
            Config::set('mail.from.address', $mail_settings['mail_from_address']);
            Config::set('mail.from.name', $mail_settings['mail_from_name']);
        } catch (\Exception $e) {
            session()->flash('error', __('settings.mail.not_updated'));
        }
    }

    public function response($status = true)
    {
        $errors = [];
        if (!empty($this->errors)) {
            foreach ($this->errors->toArray() as $key => $value) {
                $errors[$key] = $value[0];
            }
            $status = false;
        }

        return response()
            ->json([
                'status_code' => 200,
                'status' => $status,
                'errors' => $errors,
                'message' => $this->message,
                'data' => $this->data,
            ])->header('X-Bcnews-Settings', get_setting('app_settings_update'));
    }
}
