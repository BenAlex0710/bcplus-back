<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FriendRequestController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\Apis\EventController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('/send-friend-request', [FriendRequestController::class,'sendFriendRequest']);
Route::post('/accept-friend-request/{id}', [FriendRequestController::class,'acceptFriendRequest']);
Route::get('/check-friend-request/{user_id}/{friend_id}', [FriendRequestController::class,'checkFriendRequest']);
Route::post('/create/message', [FriendRequestController::class,'messagelist']);
Route::get('/get/messages', [FriendRequestController::class,'getmessages']);
Route::get('/friend-list/{id}',[FriendRequestController::class,'friendlist']);
Route::get('/friend-pending-list/{id}',[FriendRequestController::class,'friendlistpending']);
Route::get('/user-message/{user_id}/{friend_id}',[FriendRequestController::class,'getusermessage']);
Route::get('/get-blocked-user/{id}',[FriendRequestController::class,'blocklist']);
Route::post('/create/post',[PostController::class,'createpost']);
Route::post('/like/post/{id}',[PostController::class,'likepost']);
Route::post('/comment/post/{id}',[PostController::class,'commentpost']);
Route::get('/getpost/{id}',[PostController::class,'getPostWithLikesAndComments']);

Route::post('/billing/add-card',[BillingController::class,'addCard']);
Route::post('create-event', [EventController::class,'create']);
Route::get('/events/list/{id}',[EventController::class,'list']);
Route::post('/ticket/payment',[EventController::class,'processPayment']);
Route::post('/stripe/payout',[EventController::class,'initiateWithdrawal']);

Route::group(['namespace' => 'App\Http\Controllers\Apis'], function () {

    Route::get('/home', 'CommonController@home');

    Route::post('/register', 'UsersController@register');
    Route::post('/login', 'UsersController@login');
    Route::post('/social-login', 'UsersController@social_login');
    Route::post('/send-new-password', 'UsersController@send_new_password');

    Route::get('/page/{slug}', 'CommonController@get_page_content');

    Route::get('/get-settings', 'CommonController@get_settings');

    Route::post('/contact-support', 'CommonController@contact_support');

    Route::get('search-events', 'EventController@search_events');
    Route::get('page/{slug}', 'CommonController@get_page_data');

    Route::get('event-info/{id}', 'EventController@info');

    Route::get('performer/{username}', 'UsersController@get_performer_profile');

    // Route::group(['middleware' => ['auth:api', 'not_suspended']], function () {

        Route::get('notification-count', 'CommonController@get_new_notification_count');
        Route::get('unread-messages-count', 'CommonController@get_unread_messages_count');

        Route::get('/profile', 'UsersController@profile');
        // Route::get('/performer-dashboard', 'UsersController@performer_dashboard');


        Route::get('/guests-list', 'EventController@guests_list');
        Route::get('/attendess-list', 'EventController@attendees_list');

        Route::post('/update-profile', 'UsersController@update_profile');
        Route::post('/change-password', 'UsersController@change_password');

        Route::get('performers-list', 'UsersController@performers_list');
        Route::post('create-event', 'EventController@create');
        Route::get('events', 'EventController@list');
        Route::post('event/{event_id}/add-review', 'EventController@add_review');
        Route::get('event/{event_id}/get-reviews', 'EventController@get_reviews');
        Route::post('event/{event_id}/start-stream', 'EventController@start_stream');
        Route::post('event/{event_id}/guest-start-stream', 'EventController@guest_start_stream');
        Route::post('event/{event_id}/end-stream', 'EventController@end_stream');
        Route::post('event/{event_id}/guest-end-stream', 'EventController@guest_end_stream');
        Route::post('update-event/{id}', 'EventController@update');
        Route::post('event/{event_id}/comment/insert', 'EventController@insert_comment');
        Route::get('event/{event_id}/comment/list', 'EventController@comment_list');
        Route::post('event/{event_id}/attendee/add', 'EventController@add_attendee');
        Route::post('event/invitations/send', 'EventController@send_invitations');
        Route::post('event/invitations/send-mail/{id}', 'EventController@send_invitation_email_again');
        Route::post('event/invitations/delete/{id}', 'EventController@delete_invitation');
        Route::get('event/invitations', 'EventController@get_invitations');
        Route::post('event/invitation/accept/{id}', 'EventController@accept_invitation');
        Route::post('event/invitation/reject/{id}', 'EventController@reject_invitation');

        Route::get('golive-token/{performer_username?}', 'EventController@get_go_live_token');
        Route::post('golive/start-stream', 'EventController@start_live_stream');


        Route::get('packages/{type?}', 'CommonController@get_packages');
        Route::post('packages/activate-trial', 'CommonController@activate_trial');
        Route::post('packages/{id}/payment', 'CommonController@package_payment');

        Route::get('package-orders', 'CommonController@get_package_orders');
        Route::post('package-order/activate/{order_id}', 'CommonController@activate_order_package');

        Route::get('performer/{username}/get-reviews', 'UsersController@get_reviews');
        Route::get('performer/{username}/get-events', 'UsersController@get_events');
    // });
});
