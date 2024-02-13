<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/user/resetpassword/{id}', function ($id) {
    $user = User::findorfail($id);
    $user->password = bcrypt('1234567890');
    $user->save();
    return $user;
});
Route::get('/artisan/storage', function () {
    // echo 'sdhsjkd';
    dd(Artisan::call('storage:link'));
});

Route::get('/', function () {
    return redirect()->route('admin.login');
});

// Admin  routes
Route::group(['namespace' => 'App\Http\Controllers\Admin', 'prefix' => 'admin', 'as' => 'admin.'], function () {

    Route::get('/lang/{locale}', function ($locale) {
        if (!in_array($locale, ['en', 'zh'])) {
            abort(404);
        }
        session()->put('locale', $locale);
        return back();
    })->name('lang');


    //login routes
    Route::get('/', function () {
        return redirect()->route('admin.login');
    });

    Route::get('/login', 'AdminController@login_page')->name('login');
    Route::post('/login', 'AdminController@login')->name('login');
    Route::post('/forgot-passward', 'AdminController@forgot_password')->name('forgot-passward');
    Route::get('/reset-password/{token}', 'AdminController@reset_password_page')->name('rest-password-link');
    Route::post('/reset-password/{token}', 'AdminController@reset_password')->name('rest-password');
    Route::get('/update-email-confirmation/{token}', 'AdminController@update_email_confirmation')->name('update-email-confirmation');

    //authenticated routes
    Route::group(['middleware' => ['admin']], function () {
        Route::post('/logout', 'AdminController@logout')->name('logout');
        Route::get('/dashboard', 'CommonController@dashboard')->name('dashboard');
        Route::get('/app-cache-update', 'CommonController@app_cache_update')->name('app_cache_update');

        //settings
        Route::get('/settings', 'CommonController@settings_page')->name('settings');
        Route::get('/mail-settings', 'CommonController@mail_settings_page')->name('mail_settings');
        Route::get('/payment-settings', 'CommonController@payment_settings_page')->name('payment_settings');
        Route::get('/agora-settings', 'CommonController@agora_settings_page')->name('agora_settings');
        Route::get('/get-mail-template', 'CommonController@get_mail_template')->name('get_mail_template');
        Route::post('/update-mail-template', 'CommonController@update_mail_template')->name('update_mail_template');
        Route::post('/ck-image-upload', 'CommonController@ck_image_upload')->name('ck_image_upload');

        Route::get('/home-slider', 'CommonController@home_slider_page')->name('home-slider');
        Route::post('/slide/create', 'CommonController@create_home_slide')->name('home-slider.create_slide');
        Route::get('/slide/view/{id}', 'CommonController@view_home_slide')->name('home-slider.view_slide');
        Route::post('/slide/update/{id}', 'CommonController@update_home_slide')->name('home-slider.update_slide');
        Route::post('/slide/delete/{id}', 'CommonController@delete_home_slide')->name('home-slider.delete_slide');

        Route::get('/setting-options/{field}', 'CommonController@setting_option_index')->name('setting-options');
        Route::post('/setting-options/{field}', 'CommonController@setting_option_create')->name('setting-options');
        Route::put('/setting-options/{field}', 'CommonController@setting_option_update')->name('setting-options-edit');
        Route::put('/setting-options-change-status/{id}', 'CommonController@setting_option_change_status')->name('setting-options-change-status');

        Route::group(['prefix' => 'package', 'as' => 'package.'], function () {
            Route::get('/', 'PackageController@index')->name('index');
            Route::get('/create', 'PackageController@create')->name('create');
            Route::post('/save', 'PackageController@save')->name('save');
            Route::get('/edit/{id}', 'PackageController@edit')->name('edit');
            Route::post('/update/{id}', 'PackageController@update')->name('update');
            Route::delete('/delete/{id}', 'PackageController@delete')->name('delete');
            Route::put('/change_status/{id}', 'PackageController@change_status')->name('change_status');
            Route::put('/set-trial/{id}', 'PackageController@set_as_trial')->name('set_trial');
        });

        Route::get('/purchase-records', 'PackageController@package_orders_page')->name('package_orders');
        Route::get('/purchase-records/datatable', 'PackageController@package_orders_datatable')->name('package_orders.datatable');

        Route::get('/attendees-reports', 'CommonController@attendees_reports_page')->name('attendees_reports');
        Route::get('/attendees-reports/datatable', 'CommonController@attendees_reports_datatable')->name('attendees_reports.datatable');
        Route::get('/attendee-check', 'CommonController@attendee_check')->name('show_attendee_check');
        Route::post('/attendee-check', 'CommonController@attendee_check_result')->name('attendee_check');



        // Route::put('/setting-options/{field}', 'CommonController@setting_option_update')->name('setting-options-edit');
        Route::get('/app-setting', 'CommonController@app_settings_page')->name('app-setting');
        Route::post('/app-setting', 'CommonController@app_settings_update')->name('app-setting');


        Route::get('/manage-menu', 'CommonController@menu_page')->name('menu_page');
        Route::post('/update-menu', 'CommonController@update_menu')->name('update-menu');
        Route::post('/update-wesite-settings', 'CommonController@update_website_details')->name('update-website-settings');
        Route::post('/update-personal-settings', 'AdminController@update_personal_details')->name('update-personal-settings');

        Route::get('/events', 'CommonController@events_list')->name('events');
        Route::get('/datatable', 'CommonController@events_datatable')->name('events-datatable');

        //users
        Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
            Route::get('/data/{type?}', 'UserController@datatable')->name('datatable');
            Route::get('/{type?}', 'UserController@index')->name('index');
            Route::get('/approveal_request/{type?}', 'UserController@approveal_request')->name('approveal_request');
            Route::get('/view/{id}', 'UserController@show')->name('show');
            Route::put('/update/{id}', 'UserController@update')->name('update');

            Route::put('/status/{id}/{status}', 'UserController@change_status')->name('change_status');
            Route::get('/unapprove_data/{id}', 'UserController@unapprove_data')->name('unapprove_data');
            Route::post('/unapprove_data/{id}/dateing_info', 'UserController@approve_dateing_info')->name('unapprove_data.dateing_info');
            Route::post('/unapprove_data/{id}/reject_dateing_info', 'UserController@unapprove_dateing_info')->name('unapprove_data.reject_dateing_info');
            Route::post('/unapprove_data/{id}/basic_info', 'UserController@approve_basic_info')->name('unapprove_data.basic_info');
            Route::post('/unapprove_data/{id}/reject_basic_info', 'UserController@unapprove_basic_info')->name('unapprove_data.reject_basic_info');
            Route::post('/unapprove_data/{id}/personality_tag', 'UserController@approve_personality_tag')->name('unapprove_data.personality_tag');
            Route::post('/unapprove_data/{id}/reject_personality_tag', 'UserController@approve_personality_tag')->name('unapprove_data.reject_personality_tag');
            Route::post('/profile_picture/{id}/approve', 'UserController@approve_profile_picture')->name('profile_picture.approve');
            Route::delete('/profile_picture/{id}/reject', 'UserController@reject_profile_picture')->name('profile_picture.reject');
        });



        //pages
        Route::group(['prefix' => 'page', 'as' => 'page.'], function () {
            Route::get('/', 'PageController@index')->name('index');
            Route::get('/create', 'PageController@create')->name('create');
            Route::post('/save', 'PageController@save')->name('save');
            Route::get('/edit/{id}', 'PageController@edit')->name('edit');
            Route::put('/status/{id}', 'PageController@change_status')->name('change_status');
            Route::post('/add-page-to-menu', 'PageController@add_page_to_menu')->name('add_page_to_menu');
            Route::post('/remove-page-from-menu/{id}', 'PageController@remove_page_from_menu')->name('remove_page_from_menu');
            Route::post('/update/{id}', 'PageController@update')->name('update');
            Route::get('/datatable', 'PageController@datatable')->name('datatable');
        });

        // //reported reviews
        // Route::group(['prefix' => 'review-reports', 'as' => 'review-reports.'], function () {
        //     Route::get('/', 'ReviewReportsController@index')->name('index');
        //     Route::get('/edit/{id}', 'ReviewReportsController@edit')->name('edit');
        //     Route::post('/update/{id}', 'ReviewReportsController@update')->name('update');
        //     Route::get('/datatable', 'ReviewReportsController@datatable')->name('datatable');
        // });

        Route::group(['prefix' => 'support', 'as' => 'support.'], function () {
            Route::get('/', 'SupportController@index')->name('index');
            Route::post('/send-message', 'SupportController@send_message_to_user')->name('send-message');
            Route::get('/messages', 'SupportController@get_messages')->name('messages');
        });
    });
});


Route::get('cron-testing', 'App\Http\Controllers\CronTasksController@update_active_packages_status');
Route::get('test-info', function () {
    // $user = User::find('10001');
    // return $user->getRawOriginal('photo');
});

/* Route::get('/agora/acquire/{channel_name}', function ($channel_name) {
    $uid = '' . rand(10000000, 4294967295);
    $AgoraCloudRecording = new AgoraCloudRecording();
    $response = $AgoraCloudRecording->startRecording([
        'cname' => $channel_name,
        'event_id' => '4554466',
        'uid' => $uid
    ]);
    try {
        print_r($response);
    } catch (Exception $e) {
        echo $e->getMessage();
    }
    die;
    // return '';
});
 */
