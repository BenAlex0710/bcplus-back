<?php

use App\Models\AdminSetting;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

function error_alerts($errors)
{
    $data = '';
    if (session()->has('success')) :
        $data = '<div class="alert alert-success">' . session()->get('success') . '</div>';
        session()->remove('success');
    elseif (session()->has('error')) :
        $data = '<div class="alert alert-danger">' . session()->get('error') . '</div>';
        session()->remove('error');
    endif;
    if ($errors->any()) :
        $data =  '<div class="alert alert-danger"><ul>';
        foreach ($errors->all() as $e) :
            $data .= '<li>' . $e . '</li>';
        endforeach;
        $data .= '</ul></div>';
    endif;
    return $data;
}

function get_setting_fields()
{
    return  [
        'event_types',
        'performer_types',
    ];
}

function get_preformer_types()
{
    return [
        'singer' => __('common.performer_roles.singer'),
        'dancer' => __('common.performer_roles.dancer'),
        'musician' => __('common.performer_roles.musician'),
        'producer' => __('common.performer_roles.producer'),
        'event_manager' => __('common.performer_roles.event_manager')
    ];
}

function mail_templates($template_name = "")
{
    $templates = [
        'forgot_password' => [
            'label' => __('settings.mail.template_names.forgot_password_template'),
            'mail_varibales' => [
                'full_name',
                'email',
                'new_password'
            ]
        ],
        'welcome_email' => [
            'label' => __('settings.mail.template_names.welcome_email_template'),
            'mail_varibales' => [
                'username',
                'full_name',
                'email'
            ]
        ],
        'password_change_notification' => [
            'label' => __('settings.mail.template_names.password_change_notification'),
            'mail_varibales' => [
                'username',
                'full_name',
                'email'
            ]
        ],
        'guest_invitation' => [
            'label' => __('settings.mail.template_names.guest_invitation'),
            'mail_varibales' => [
                'full_name',
                'email',
                'event_title',
                'event_type',
                'event_start_time',
                'event_end_time',
                'event_duration',
                'event_timezone',
                'event_host',
            ]
        ],
        'user_email_verification' => [
            'label' => __('settings.mail.template_names.user_email_verification'),
            'mail_varibales' => [
                'username',
                'full_name',
                'email',
                'confirm_link',
            ]
        ],
        /* 'login_notification' => [
            'label' => __('mail_settings.login_notification'),
            'mail_varibales' => [
                'username',
                'full_name',
                'email'
            ]
        ],
        'system_notification' => [
            'label' => __('mail_settings.system_notification'),
            'mail_varibales' => [
                'username',
                'full_name',
                'email',
                'notification'
            ]
        ],
        'account_deactivation' => [
            'label' => __('mail_settings.account_deactivation'),
            'mail_varibales' => [
                'username',
                'full_name',
                'email'
            ]
        ],
        'account_activation' => [
            'label' => __('mail_settings.account_activation'),
            'mail_varibales' => [
                'username',
                'full_name',
                'email'
            ]
        ],
        'account_deletion' => [
            'label' => __('mail_settings.account_deletion'),
            'mail_varibales' => [
                'username',
                'full_name',
                'email'
            ]
        ],
        'support_mail_for_admin' => [
            'label' => __('mail_settings.support_email_template_admin'),
            'mail_varibales' => [
                'problem',
                'email'
            ]
        ] */
    ];
    if (!empty($template_name)) {
        if (isset($templates[$template_name])) {
            return $templates[$template_name];
        } else {
            return [];
        }
    }
    return $templates;
}



function storage_url($file_path)
{
    $path = str_replace('storage/', '', $file_path);

    /** @var \Illuminate\Support\Facades\Storage $storage **/
    $storage = Storage::disk('public');
    return $storage->url('app/public/' . $path);
}


function get_app_language()
{
    return [
        'en',
        'zh',
    ];
}

function get_app_language_details()
{
    return $language = [
        'en' => [
            'name' => 'English',
            'icon' => asset('admin/images/lang/en.png')
        ],
        'zh' => [
            'name' => 'Chinese',
            'icon' => asset('admin/images/lang/zh.png')
        ]
    ];
}


function get_setting($name)
{
    $setting = AdminSetting::where('name', $name)->first();
    if ($setting) {
        return $setting->value;
    }
}

function base64_to_image($image, $imagePath, $imageName = "")
{
    $response = [
        'status' => false
    ];
    $imgArr = explode(',', $image);
    $b64 = end($imgArr);

    $bin = base64_decode($b64);

    $size = getImageSizeFromString($bin);

    if (empty($size['mime']) || strpos($size['mime'], 'image/') !== 0) {
        $response['message'] = __('common.errors.invalid_image');
        return $response;
    }

    $ext = substr($size['mime'], 6);

    if (!in_array($ext, ['png', 'gif', 'jpeg'])) {
        $response['message'] = __('common.errors.invalid_image');
        return $response;
    }

    if ($imageName != '') {
        $imageName = str_replace(' ', '_', $imageName);
        $imageName = $imageName . '-' . time() . '.' . $ext;
    } else {
        $imageName = str_replace(' ', '_', $imageName);
        $imageName = md5(microtime()) . '.' . $ext;
    }

    $full_path = storage_path('app/public') . $imagePath;

    if (!File::isDirectory($full_path)) {
        File::makeDirectory($full_path, 0777, true, true);
    }
    File::put($full_path . '/' . $imageName, $bin);

    $response['status'] = true;
    $response['image_path'] = 'storage' . $imagePath . '/' . $imageName;
    $response['image_type'] = $ext;
    $response['image_name'] = $imageName;
    return $response;
}

function get_extension_from_media_type($mediaType) {
    switch ($mediaType) {
        case 'image/jpeg':
            return 'jpg';
        case 'image/png':
            return 'png';
        case 'image/gif':
            return 'gif';
        case 'video/mp4':
            return 'mp4';
        case 'video/webm':
            return 'webm';
        // Add more cases as needed for other media types
        default:
            return null; // Unknown or unsupported media type
    }
}

function base64_to_media($media, $mediaPath, $mediaName = "")
{
    $response = ['status' => false];

    // Extract the base64 content
    $mediaArr = explode(',', $media);
    $b64 = end($mediaArr);
    $bin = base64_decode($b64);

    // Get the media type
    $finfo = new \finfo(FILEINFO_MIME_TYPE);
    $mediaType = $finfo->buffer($bin);

    // Determine file extension based on media type
    $ext = get_extension_from_media_type($mediaType);

    if (empty($ext) || !in_array($ext, ['png', 'gif', 'jpg', 'mp4', 'webm'])) {
        $response['message'] = __('common.errors.invalid_media');
        return $response;
    }

    // Generate a unique name if not provided
    if ($mediaName === '') {
        $mediaName = md5(microtime()) . '.' . $ext;
    }

    // Sanitize the name
    $mediaName = str_replace(' ', '_', $mediaName);

    // Set the full path
    $fullPath = storage_path('app/public') . $mediaPath;

    // Create the directory if it doesn't exist
    if (!File::isDirectory($fullPath)) {
        File::makeDirectory($fullPath, 0777, true, true);
    }

    // Save the media file
    File::put($fullPath . '/' . $mediaName, $bin);

    // Prepare the response
    $response['status'] = true;
    $response['media_path'] = 'storage' . $mediaPath . '/' . $mediaName;
    $response['media_type'] = $ext;
    $response['media_name'] = $mediaName;

    return $response;
}

function getTimeZoneList()
{
    $timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
    // print_r($timezones);
    $timezones_data = [];
    foreach ($timezones as $key => $timezone) {
        $dateTimeZone[$key] = new DateTimeZone($timezone);
        $dateTime[$key] = new DateTime("now", $dateTimeZone[$key]);
        $timezones_data[] = [
            'timezone' => $timezone,
            'offset' => $dateTimeZone[$key]->getOffset($dateTime[$key])
        ];
    }
    return $timezones_data;
}

function format_amount($amount)
{
    return '$' . $amount;
}

function get_currency()
{
    return 'USD';
}

function send_guest_invitation_email($event, $full_name, $email, $event_host)
{
    try {
        $website_title =  get_setting('website_title');
        $template_data = json_decode(get_setting('mail_template_guest_invitation'));

        $varibales = [
            '{{full_name}}',
            '{{email}}',
            '{{event_title}}',
            '{{event_type}}',
            '{{event_start_time}}',
            '{{event_end_time}}',
            '{{event_duration}}',
            '{{event_timezone}}',
            '{{event_host}}',
        ];
        $values = [
            $full_name,
            $email,
            $event->title,
            $event->event_type,
            $event->start_time,
            $event->end_time,
            $event->duration,
            $event->timezone,
            $event_host->full_name . '(' . $event_host->username . ')',
        ];

        $content = str_replace($varibales, $values, $template_data->content);


        $mail_data = [
            'email' => $email,
            // 'email' => 'pankajsoni.letscms@gmail.com',
            'name' => $full_name,
            'subject' => $template_data->subject . ' - ' . $website_title,
            'content' => $content
        ];
        // return view('emails.users.new-password', $mail_data);
        Mail::send('emails.user-defined', $mail_data, function ($message) use ($mail_data) {
            // $message->from(get_setting('mail_from_address'), get_setting('mail_from_name'));
            $message->to($mail_data['email'], $mail_data['name'])->subject($mail_data['subject']);
        });
        return true;
    } catch (\Exception $e) {
        return false;
    }
}
