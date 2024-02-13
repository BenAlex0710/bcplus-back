<?php

return [
    'page_title' => 'Admin Settings',
    'breadcrumb_dashboard' => 'Dashboard',
    'breadcrumb_name' => 'Settings',
    'update_btn' => 'Update',

    'website' => [
        'box_heading' => 'Website Settings',
        'title' => 'Website Title',
        'subtitle' => 'Website Subtitle',
        'support_email' => 'Support Email',
        'support_number' => 'Support Phone Number',
        'reset_password_link' => 'Reset Password Link Expiry Time (in minutes)',
        'address' => 'Address',
        'logo' => 'Logo',
        'pages_to_show_on_website' => 'Pages will show in footer and sidemenu.',
        'go_live_event_type' => 'Go Live Event type',
        'stream_early_time' => 'Stream Early Time (in seconds)',
        'stream_early_time_instruction' => 'how much early performer can start streaming.',
        'stream_auto_leave_timeout' => 'Stream Auto Leave Timeout (in seconds)',
        'stream_auto_leave_timeout_instruction' => 'how much early show timer for auto leave stream.',
        'stream_late_max_time' => 'Stream Late Max Time (in seconds)',
        'stream_late_max_time_instruction' => 'Max late time to start streaming for performer, after that a penalty will be charged.',
        'stream_late_penalty' => 'Stream Late Penalty amount',
        'stream_late_penalty_instruction' => 'Penalty for performer to start straming late (included Stream Late Max Time)',
        'login_register' => 'Login/Register page image',
        'update_success' => 'Website settings updated successfully.',
        'trending_minimum_attendees' => 'Trending Minimum Attendees',
        'trending_minimum_attendees_instruction' => 'Mininum attendees required for event to be a trending event',
    ],

    "personal" => [
        'box_heading' => 'Personal Details',
        'name' => 'Name',
        'email' => 'Email <small class="text-info">(Login email)</small>',
        'new_password' => 'New Password <small class="text-warning">(leave blank if dont want to change)</small>',
        'old_password' => 'Old Password',
        'email_confirmation_mail_subject' => 'Confirmation Email for updating current email for :website_title',
        'email_changed_message' => ' We noticed that you have changed your login email, so have sent an email for confirmation, please confirm your email to update your admin login email.',
        'email_cant_change_now' => 'Email can not be change at this time',
        'email_link_expired' => 'Sorry, your link has been expired. please try again',
        'email_updated_success' => 'Email successfully updated.',
        'errors' => [
            'incorrect_old_password' => 'Sorry your Old password is incorrect'
        ],
        'update_success' => 'Setting updated successfully.'
    ],

    "social_login" => [
        'box_heading' => 'Social Login Settings',
        'google_details' => 'Google API Details',
        'facebook_details' => 'Facebook API Details',
        'client_id' => 'Client ID',
        'client_secret' => 'Client Secret',
        'api_key' => 'API Key',
        'redirect_url' => 'Redirect Url',
    ],

    'mail' => [
        'page_title' => 'Mail Settings',
        'breadcrumb_name' => 'Mail Settings',
        'box_heading' => 'Mail SMTP Settings',
        'mailer' => 'Mailer',
        'host' => 'Host',
        'port' => 'Port',
        'username' => 'Username',
        'password' => 'Password',
        'encryption' => 'Encryption',
        'from_name' => 'From Name',
        'from_email' => 'From Email',

        'not_updated' => 'Mail settings not updated, please update your mail settings.',

        'templates' => [
            'subject' => 'Subject',
            'content' => 'Content',
            'varibales' => 'Mail Varibales',
            'varibale_instructions' => 'If you use these varibales, these will be replaced with their appropriate value.',
            'box_heading' => 'Mail Templates Settings',
            'box_subheading' => 'Update mail template as you want using the mail varibales.',
            'select_template' => 'Select Template',
            'update_success' => 'Mail Template updated successfully.'
        ],

        'template_names' => [
            'forgot_password_template' => 'Forgot Password Template',
            'welcome_email_template' => 'Welcome Template',
            'password_change_notification' => 'Password change notification Template',
            'guest_invitation' => 'Event invitation for Guest Template',
        ],

        'footer_links' => [
            'box_heading' => 'Footer Links',
            'box_subheading' => 'You can drag up and down as you want footer links.',
            'url' => 'URL',
            'text' => 'Text'
        ]
    ],

    'home_slider' => [
        'page_title' => 'Home Page Slider',
        'breadcrumb_name' => 'Manage Home Page Slider',
        'breadcrumb_dashboard' => 'Dashboard',
        'box_heading' => 'Home Page Slides',
        'image' => 'Image',
        'language' => 'Language',
        'title' => 'Title',
        'subtitle' => 'Subtitle',
        'button_label' => 'Button Label',
        'button_url' => 'Button URL',
        'slide_order' => 'Slide Order',
        'action' => 'Action',
        'create_new_slide_btn' => 'Create new slide',
        'create_new_slide' => 'Create new slide',
        'submit_btn' => 'Submit',
        'invalid_slide_id' => 'Invalid Slide Id.',
        'slide_create_success' => 'Slide created successfully.',
        'slide_create_failed' => 'Opps!!.., Slide not created. please try again.',
    ],

    'payment' => [
        'page_title' => 'Payment Settings',
        'not_updated' => 'Payment settings not updated, please update your Payment settings.',
        'breadcrumb_name' => 'Payment Settings',
        'box_heading' => 'Payment (Stripe) Settings',

        'mode' => 'Mode',
        'mode_live' => 'Live',
        'mode_stage' => 'Test',

        'stripe_key' => 'STRIPE KEY',
        'stripe_secret' => 'STRIPE SECRET',

        'stripe_test_key' => 'STRIPE TEST KEY',
        'stripe_test_secret' => 'STRIPE TEST SECRET',
    ],

    'agora' => [
        'page_title' => 'Agora Settings',
        'not_updated' => 'Agora settings not updated, please update your Agora settings.',
        'breadcrumb_name' => 'Agora Settings',
        'box_heading' => 'Agora Settings',

        'app_url' => 'App Url',
        'app_id' => 'App Id',
        'app_certificate' => 'App Certificate',
        'customer_id' => 'Customer Id',
        'customer_certificate' => 'Customer Certificate',

        'recording_settings' => 'Recording Settings',
        'vendor' => 'Vendor',
        'select_vendor' => 'Select Cloud Vendor',
        'region' => 'Region',
        'bucket' => 'Bucket',
        'access_key' => 'Access Key',
        'secret_key' => 'Secret Key',

    ],

    'options' => [
        'page_title' => 'Setting Options - :field_title',
        'add_new' => 'Add New',
        'add_field_option' => 'Add New :field_title',
        'update_field_option' => 'Update :field_title Option',
        'id' => 'ID',
        'name' => 'Name',
        'created_at' => 'Created At',
        'save_option' => 'Save Option',
        'update_option' => 'Update Option',
        'update' => 'Update',
        'status_updated_success_message' => 'Option status update successfully.',
        'created_success_message' => 'Option created successfully.',
        'updated_success_message' => 'Option updated successfully.',
        'status_disable_popup' => [
            'title' => 'Are you sure?',
            'subtitle' => 'You want to disable this option.',
            'confirm_text' => 'Yes, Disable it!',
            'result_success_title' => 'Disabled!!!',
            'result_failed_title' => 'Error!!!',
            'cancel_text' => 'Cancel',
        ],
        'status_enable_popup' => [
            'title' => 'Are you sure?',
            'subtitle' => 'You want to enable this option.',
            'confirm_text' => 'Yes, Enable it!',
            'result_success_title' => 'Enabled!!!',
            'result_failed_title' => 'Error!!!',
            'cancel_text' => 'Cancel',
        ],
    ]

];
