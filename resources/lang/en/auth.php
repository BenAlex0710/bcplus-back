<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'login' => [
        'title' => 'Admin Login',
        'email' => 'Email',
        'password' => 'Password',
        'forgot_password' => 'Forgot your password?',
        'remember' => 'Remember me',
        'login_btn' => 'Login',
        'success_message' => 'Login Successfully !!!!!',
        'placeholders' => [
            'email' => 'Enter Your Email',
            'password' => 'Enter Your Password'
        ],
        'forgot_pass_modal' => [
            'title' => 'Recover your password',
            'note' => 'Please enter your email we send you a reset password link, please click on the click and reset your password.',
            'send_email_btn' => 'Send Email',
        ],
        'errors' => [
            'failed' => 'These credentials do not match our records.',
            'suspended' => 'Your account is suspended, please contact support team if you think this is misunderstanding.',
            'password' => 'The provided password is incorrect.',
            'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',
        ]
    ],

    'forgot_password' => [
        'mail_subject' => 'Instructions for password reset of :website_title account',
        'password_changed_success' => 'Password changed successfully, Now you can login with new password.',
        'link_sent_success' => 'We have emailed you reset password link with instructions on how to reset your password.',
        'errors' => [
            'link_expired' => 'Sorry, your link has been expired. please try again.'
        ],
    ],

    'reset_password' => [
        'page_title' => 'Reset Password',
        'password' => 'Password',
        'confirm_password' => 'Password',
        'submit_btn' => 'Reset Password'
    ]

];
