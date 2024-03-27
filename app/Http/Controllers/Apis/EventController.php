<?php

namespace App\Http\Controllers\Apis;

use AgoraCloudRecording;
use App\Helpers\StripePayment;
use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\Event;
use App\Models\EventAttendee;
use App\Models\EventGuest;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use RtcTokenBuilder;

use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Transfer;
use Stripe\Payout;
use Stripe\Token;



class EventController extends Controller
{
    public function create(Request $request)
    {
        $userId = $request->input('user_id');

        // Retrieve the user based on the user_id
        $user = User::find($userId);

        $active_package = $user->package_orders()->where('status', '1')->first();

        // Check if there is an active package
        if (!$active_package) {
            return response()->json(['message' => __('event.no_active_package_found')], 404);
        }

        // Check if the active package has events available
        if ($active_package->events <= 0) {
            return response()->json(['message' => __('event.max_events_reached_limit')], 400);
        }

        // Validate the request data
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:191',
            'timezone' => 'required|max:191',
            'description' => 'required',
            'start_time' => 'required|date_format:Y-m-d H:i',
            'end_time' => 'required|date_format:Y-m-d H:i|after:start_time',
            'joining_fee' => 'required|numeric',
            'event_type_id' => 'required|exists:setting_options,id',
            'guests' => 'nullable|array',
            'banner' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create the event
        $event = $user->events()->create([
            'title' => $request->title,
            'timezone' => $request->timezone,
            'description' => $request->description,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'joining_fee' => $request->joining_fee,
            'notification' => $request->notification,
            'event_type_id' => $request->event_type_id,
            'banner' => $request->banner,
            'package_order_id' => $active_package->id
        ]);

        // Decrease the available events count in the package
        $active_package->events--;
        $active_package->save();

        // Create tickets if provided
        if ($request->has('tickets') && is_array($request->tickets)) {
            foreach ($request->tickets as $ticket) {
                $event->tickets()->create([
                    'name' => $ticket['name'],
                    'description' => $ticket['description'],
                    'price' => $ticket['price'],
                    'quantity' => $ticket['quantity'],
                ]);
            }
        }

        return response()->json(['message' => __('event.created_success_message'), 'event' => $event], 201);
    }

        public function processPayment(Request $request)
        {
           Stripe::setApiKey(env('STRIPE_SECRET'));

try {

    $ticketAmount = $request->input('ticket_amount');

    $charge = Charge::create([
        "amount" => $ticketAmount * 100,
        "currency" => "usd",
        "source" => $request->stripeToken,
        "description" => "Payment for event tickets with platform fee."
    ]);





    return response()->json(['message' => 'Payment successful', 'ticketAmount' => $ticketAmount], 200);
} catch (\Exception $e) {
    return response()->json(['error' => $e->getMessage()], 500);
}
        }

        public function initiateWithdrawal(Request $request)
        {
            // Validate user ID and withdrawal amount
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer|exists:users,id', // Ensure valid user ID
                'amount' => 'required|numeric|min:0', // Ensure positive withdrawal amount
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            Stripe::setApiKey(env('STRIPE_SECRET'));

            try {

                $user = User::find($request->user_id);


                $stripeCustomerId = $user->stripe_customer_id;


                if (empty($stripeCustomerId)) {
                    return response()->json(['error' => 'User does not have a Stripe customer ID'], 400);
                }

                $payout = Payout::create([
                    'amount' => $request->amount * 100,
                    'currency' => 'usd',
                    'destination' => $stripeCustomerId,
                ]);


                $user->withdrawals()->create([
                    'amount' => $request->amount,
                    'status' => 'initiated',
                ]);

                return response()->json(['message' => 'Withdrawal initiated successfully'], 200);
            } catch (Stripe\Exception\InvalidRequestException $e) {
                return response()->json(['error' => $e->getMessage()], 400);
            } catch (\Exception $e) {
                // Catch other generic exceptions (e.g., network issues)
                report($e); // Log the error for investigation
                return response()->json(['error' => 'Withdrawal failed. Please try again later.'], 500);
            }
        }



    /**
     * Calculate the platform fee.
     *
     * @param  float  $amount
     * @return float
     */
    private function calculatePlatformFee($amount)
    {
        // Implement your logic to calculate the platform fee
        // For example, you can charge a fixed percentage of the transaction amount
        return $amount * 0.05; // Charging 5% platform fee
    }




    public function list(Request $request,$id)
    {

        $page = 1;
        $limit = 20;
        if ($request->filled('page')) {
            $page = $request->page;
        }
        $offset = ($page - 1) * $limit;
        $user = User::find($id);
        $events = $user->events()
            ->latest()
            ->limit($limit)
            ->offset($offset)
            ->get();
        $this->data['events'] = $events;
        return $this->response();
    }

    public function info(Request $request, $id)
    {
        $user = $request->user('api');
        $event_info = Event::with('performer', 'live_data', 'guests.user', 'attendees.user', 'reviews.reviewer')
            ->withCount('reviews')
            ->with('guests', function ($query) {
                $query->where('status', '!=', '2');
            })
            // ->with('attendees', function ($query) {
            //     $query->where('payment_status',  '1');
            // })
            ->with('reviews', function ($query) {
                $query->latest();
                $query->limit(20);
            })
            ->with('comments', function ($query) {
                $query->latest();
                $query->with('user', 'replies.user');
                $query->with('replies', function ($q) {
                    $q->latest();
                });
                $query->limit(20);
            })
            ->where('id', $id)
            ->first();
        if (!$event_info) {
            $this->message = __('event.invalid_event_error');
            return $this->response(false);
        }

        $settings = AdminSetting::where('name', 'like', '%agora_%')->pluck('value', 'name');

        $appID = $settings['agora_app_id'];
        $appCertificate =  $settings['agora_app_certificate'];
        if (empty($event_info->agora_channel_name)) {
            $channelName = md5($event_info->id);
            $event_info->agora_channel_name = $channelName;
            $event_info->save();
        } else {
            $channelName = $event_info->agora_channel_name;
        }

        $uid = null;
        // $uid = '' . rand(10000000, 4294967295);
        // $uidStr = "2882341273";

        if ($user && $user->events()->where('id', $id)->first()) {
            $role = RtcTokenBuilder::RolePublisher;
        } else {
            $role = RtcTokenBuilder::RoleAttendee;
        }

        $expireTimeInSeconds = 3600;
        $currentTimestamp = (new DateTime("now", new DateTimeZone('UTC')))->getTimestamp();
        $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

        $token = RtcTokenBuilder::buildTokenWithUid($appID, $appCertificate, $channelName, $uid, $role, $privilegeExpiredTs);

        $dateTimeZone = new DateTimeZone($event_info->timezone);
        $dateTime = new DateTime("now", $dateTimeZone);
        $review_posted = false;

        if ($user) {
            $already_exists =  $event_info->reviews()->where('reviewer_id', $user->id)->first();
            if ($already_exists) {
                $review_posted = true;
            }
        }

        // $attendees = $event_info->attendees()->with('user')->get();
        // $guests = $event_info->guests()
        //     ->with('user')
        //     ->where('status', '1')
        //     ->get();

        $this->data['tz_offset'] = $dateTimeZone->getOffset($dateTime);
        $this->data['raw_data']['start_time'] = $event_info->getRawOriginal('start_time');
        $this->data['raw_data']['end_time'] = $event_info->getRawOriginal('end_time');
        $this->data['rtc_channel'] = $channelName;
        $this->data['rtc_uid'] = $uid;
        $this->data['rtc_token'] = $token;
        $this->data['app_id'] = $appID;
        $this->data['event_info'] = $event_info;
        $this->data['review_posted'] = $review_posted;
        // $this->data['attendees'] = $attendees;
        // $this->data['guests'] = $guests;
        return $this->response();
    }

    public function get_go_live_token(Request $request, $performer_username = '')
    {
        if (empty($performer_username)) {
            do {
                $channelName = md5(microtime());
                $channel_name_exits = Event::where('agora_channel_name', $channelName)->first();
            } while ($channel_name_exits);
        } else {
            $performer = User::where('username', $performer_username)->first();
            $event = $performer->events()->where('event_type_id', get_setting('go_live_event_type'))
                ->where('timezone', 'UTC')
                ->where('title', 'Live Now')
                ->latest()
                ->first();
            if ($event) {
                $channelName = $event->agora_channel_name;

                $this->data['event_id'] = $event->id;
                $this->data['event_slug'] = $event->slug;
            } else {
                $this->message = __('event.performer_is_not_live');
                return $this->response(false);
            }
        }

        $role = RtcTokenBuilder::RolePublisher;
        $uid = null;
        $settings = AdminSetting::where('name', 'like', '%agora_%')->pluck('value', 'name');
        $appID = $settings['agora_app_id'];
        $appCertificate =  $settings['agora_app_certificate'];

        $expireTimeInSeconds = 3600;
        $currentTimestamp = (new DateTime("now", new DateTimeZone('UTC')))->getTimestamp();
        $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

        $token = RtcTokenBuilder::buildTokenWithUid($appID, $appCertificate, $channelName, $uid, $role, $privilegeExpiredTs);

        $this->data['rtc_channel'] = $channelName;
        $this->data['rtc_uid'] = $uid;
        $this->data['rtc_token'] = $token;
        $this->data['app_id'] = $appID;
        return $this->response();
    }

    public function start_stream(Request $request, $id)
    {
        $user = $request->user();
        $event_info = $user->events()->find($id);
        if (!$event_info) {
            $this->message = __('event.invalid_event_error');
            return $this->response(false);
        }
        $channel_name = md5($event_info->id);

        $validator = Validator::make($request->all(), [
            'uid' => 'required',
        ]);

        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return $this->response();
        }

        $uid = '' . rand(10000000, 4294967295);
        try {
            $AgoraCloudRecording = new AgoraCloudRecording();
            $response = $AgoraCloudRecording->startRecording([
                'cname' => $channel_name,
                'event_title' => $event_info->title,
                'uid' => $uid,
                'event_id' => $event_info->id
            ]);
            $event_info->live_status = 'published';
            $event_info->save();

            $live_data = $event_info->live_data()->create([
                'resource_id' => $response['resourceId'],
                'sid' => $response['sid'],
                'uid' => $request->uid
            ]);
            if ($live_data) {
                $this->message = __('event.live_success');
                return $this->response();
            } else {
                $this->message = __('event.live_failed');
            }
        } catch (Exception $e) {
            $this->message =  $e->getMessage();
        }
        return $this->response(false);
    }

    public function start_live_stream(Request $request)
    {
        $user = $request->user();
        $active_package = $user->package_orders()->where('status', '1')->first();
        $this->data['active_package'] = true;
        if (!$active_package) {
            $this->data['active_package'] = false;
            $this->message = __('event.no_active_package_found');
            return $this->response(false);
        }

        if ($active_package->events == '0') {
            $this->data['active_package'] = false;
            $this->message = __('event.max_events_reached_limit');
            return $this->response(false);
        }

        $validator = Validator::make($request->all(), [
            'uid' => 'required',
            'channel_name' => 'required',
        ]);

        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return $this->response();
        }

        $timezone = "UTC";

        $start_time = now($timezone)->format('Y-m-d H:i:s');
        $end_time = now($timezone)->addMinutes(30);

        $channel_name = $request->channel_name;

        $data = [
            'title' => 'Live Now',
            'joining_fee' => '0',
            'start_time' => $start_time,
            'end_time' => $end_time,
            'timezone' => $timezone,
            'description' => '',
            'notification' => '',
            'event_type_id' => get_setting('go_live_event_type'),
            'package_order_id' => $active_package->id,
            'banner' => $user->getRawOriginal('photo'),
            'agora_channel_name' => $channel_name
        ];

        if ($event_info = $user->events()->create($data)) {
            $active_package->events = ($active_package->events - 1);
            $active_package->save();
        }
        $this->data['event_id'] = $event_info->id;
        $this->data['event_slug'] = $event_info->slug;

        $uid = '' . rand(10000000, 4294967295);
        try {
            $AgoraCloudRecording = new AgoraCloudRecording();
            $response = $AgoraCloudRecording->startRecording([
                'cname' => $channel_name,
                'event_title' => $event_info->title,
                'uid' => $uid,
                'event_id' => $event_info->id
            ]);
            $event_info->live_status = 'published';
            $event_info->save();

            $live_data = $event_info->live_data()->create([
                'resource_id' => $response['resourceId'],
                'sid' => $response['sid'],
                'uid' => $request->uid
            ]);

            if ($live_data) {
                $this->message = __('event.live_success');
                return $this->response();
            } else {
                $this->message = __('event.live_failed');
            }
        } catch (Exception $e) {
            $this->message =  $e->getMessage();
        }
        return $this->response(false);
    }



    public function end_stream(Request $request, $id)
    {
        $user = $request->user();
        $event_info = $user->events()->find($id);
        if (!$event_info) {
            $this->message = __('event.invalid_event_error');
            return $this->response(false);
        }
        $channel_name = $event_info->agora_channel_name;
        $last_live_data = $event_info->live_data()->latest()->first();

        $uid = '' . rand(10000000, 4294967295);
        try {
            $AgoraCloudRecording = new AgoraCloudRecording();
            $response = $AgoraCloudRecording->stopRecording([
                'cname' => $channel_name,
                'event_title' => $event_info->title,
                'uid' => $uid,
                'resource_id' => $last_live_data->resource_id,
                'sid' => $last_live_data->sid
            ]);
            // $event_info->live_status = 'published';
            // $event_info->save();
            $this->data['last_live'] = $last_live_data;
            $this->message = __('event.completed_success');
            return $this->response();
        } catch (Exception $e) {
            $this->message =  $e->getMessage();
        }
        return $this->response(false);
    }

    public function guest_start_stream(Request $request, $id)
    {
        $user = $request->user();
        $event_info = $user->event_invitations()
            ->where('event_id', $id)
            ->where('status', '1')
            ->first();
        if (!$event_info) {
            $this->message = __('event.invalid_guest');
            return $this->response(false);
        }
        $channel_name = md5($event_info->id);
        $last_live_data = $event_info->live_data()->latest()->first();

        $uid = '' . rand(10000000, 4294967295);
        try {
            $AgoraCloudRecording = new AgoraCloudRecording();
            $response = $AgoraCloudRecording->updateLayout([
                'cname' => $channel_name,
                'uid' => $uid,
                'recordingId' => "",
                'resource_id' => $last_live_data->resource_id,
                'sid' => $last_live_data->sid
            ]);
            $this->message = __('event.guest_live_success');
            return $this->response();
        } catch (Exception $e) {
            $this->message =  $e->getMessage();
        }
        return $this->response(false);
    }

    public function guest_end_stream(Request $request, $id)
    {
        $user = $request->user();
        $event_info = $user->event_invitations()
            ->where('event_id', $id)
            ->where('status', '1')
            ->first();
        if (!$event_info) {
            $this->message = __('event.invalid_event_error');
            return $this->response(false);
        }
        $channel_name = md5($event_info->id);
        $last_live_data = $event_info->live_data()->latest()->first();

        $uid = '' . rand(10000000, 4294967295);
        try {
            $AgoraCloudRecording = new AgoraCloudRecording();
            $response = $AgoraCloudRecording->updateLayout([
                'cname' => $channel_name,
                'uid' => $uid,
                'recordingId' => "",
                'resource_id' => $last_live_data->resource_id,
                'sid' => $last_live_data->sid
            ]);
            $this->message = __('event.guest_stream_end');
            return $this->response();
        } catch (Exception $e) {
            $this->message =  $e->getMessage();
        }
        return $this->response(false);
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();
        $event_info = $user->events()->where('id', $id)->first();

        if (!$event_info) {
            $this->message = __('event.invalid_event_error');
            return $this->response(false);
        }

        /*   if ($event_info->live_status == 'live') {
            $this->message = __('event.event_live_cant_be_update');
            return $this->response(false);
        } */

        if ($event_info->live_status == 'published') {
            $this->message = __('event.event_published_cant_be_update');
            return $this->response(false);
        }

        $active_package = $event_info->package_data;
        $active_package_data = $active_package->package_data;

        $currentTime = Carbon::now($event_info->timezone)->format('Y-m-d H:i');

        $validator = Validator::make($request->all(), [
            'title' => 'required|max:191',
            'timezone' => 'required|max:191',
            'description' => 'required',
            'start_time' => 'required|date_format:Y-m-d H:i|before:' . $active_package->expiry_date . ' 23:59|after:' . $currentTime,
            'end_time' => 'required|date_format:Y-m-d H:i|before:' . $active_package->expiry_date . ' 23:59',
            'joining_fee' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'guests' => 'nullable|array|max:' . $active_package_data->max_guests,
            'event_type_id' => 'required|exists:setting_options,id'
        ]);

        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return $this->response();
        }

        $end_time = Carbon::parse($request->end_time);
        $start_time = Carbon::parse($request->start_time);
        $diff_min = $end_time->diffInMinutes($start_time);



        if ($diff_min > $active_package_data->event_max_duration) {
            $this->message = __('event.max_duration_error', ['duration' => $active_package_data->event_max_duration]);
            return $this->response(false);
        }

        $data = [
            'title' => $request->title,
            'joining_fee' => $request->joining_fee,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'timezone' => $request->timezone,
            'description' => $request->description,
            'notification' => $request->notification,
            'event_type_id' => $request->event_type_id,
        ];

        if ($request->filled('banner')) {
            $image = base64_to_image($request->banner, '/event_banners', 'banner_' . time());
            if (!$image['status']) {
                $this->message = $image['message'];
                return $this->response(false);
            }
            $data['banner'] = $image['image_path'];
            @unlink(public_path() . '/' . $event_info->getRawOriginal('banner'));
        }

        if ($event_info->update($data)) {
            // foreach ($request->guests as $guest_id) {
            //     $existed = $event_info->guests()
            //         ->where('user_id', $guest_id)
            //         ->where('status', '!=', '2')
            //         ->first();
            //     if (!$existed) {
            //         $created = $event_info->guests()
            //             ->create(['user_id' => $guest_id]);
            //         if ($created) {
            //             send_guest_invitation_email($event_info, $guest_id, $user);
            //         }
            //     }
            // }
            // $event_info->guests()
            //     ->where('status', '0')
            //     ->whereNotIn('user_id', $request->guests)
            //     ->update(['status' => '2']);

            $this->message = __('event.updated_success_message');
        }
        return $this->response();
    }

    public function search_events(Request $request)
    {
        $trending_minimum_attendees = get_setting('trending_minimum_attendees');
        $page = 1;
        $limit = 12;
        if ($request->filled('page')) {
            $page = $request->page;
        }
        $offset = ($page - 1) * $limit;
        $events_query = Event::latest();
        if ($request->filled('keywords')) {
            $keywords = explode(' ', $request->keywords);
            $events_query->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->orWhere('title', 'LIKE', '%' . $keyword . '%');
                }
            });
        }
        if ($request->filled('event_type')) {
            $events_query->where('event_type_id', $request->event_type);
        }
        if ($request->filled('timing')) {
            $timing = $request->timing;
            if ($timing == 'past') {
                $events_query->where('start_time', '<', now('UTC')->format('Y-m-d H:i:s'));
            } else {
                $events_query->upcoming();
                if ($timing == 'today') {
                    $events_query->whereRaw('date(start_time) = "' . now('UTC')->format('Y-m-d') . '"');
                }
                if ($timing == 'this-week') {
                    $endOfWeek = now('UTC')->endOfWeek()->format('Y-m-d');
                    $events_query->whereRaw('date(start_time) <= "' . $endOfWeek . '"');
                }
            }
        }

        if ($request->filled('trending')) {
            $events_query->withCount('attendees')->having('attendees_count', '>=', $trending_minimum_attendees);
        }

        if ($request->filled('recommended')) {
            $user = $request->user('api');
            if ($user) {
                $attended_event_ids = EventAttendee::where('user_id', $user->id)->pluck('event_id');
                $event_type_ids = Event::whereIn('id', $attended_event_ids)->pluck('event_type_id');
                $events_query->upcoming();
                $events_query->whereIn('event_type_id',  $event_type_ids);
            } else {
                $events_query->withCount('attendees')->having('attendees_count', '>=', $trending_minimum_attendees);
            }
        }
        $events = $events_query
            ->with('performer')
            ->limit($limit)
            ->offset($offset)
            ->get();
        $this->data['events'] = $events;
        return $this->response();
    }

    public function add_attendee(Request $request, $event_id)
    {
        $user = $request->user();
        $event_info = Event::find($event_id);
         echo $event_info;

        if (!$event_info) {
            $this->message = __('event.invalid_event_error');
            return $this->response(false);
        }

        $total = $event_info->joining_fee;
        $active_package = $event_info->package_data;
        $active_package_data = $active_package->package_data;

        $admin_commission =  ($total * $active_package_data->ticket_commission) / 100;
        $amount = $total -  $admin_commission;
        $attendee = $event_info->attendees()->create([
            'user_id' => $user->id,
            'amount' => $amount,
            'admin_commission' => $admin_commission,
            'total_amount' => $total,
            'payment_status' => '0',
        ]);

        if ($total > 0) {
            $stripe = new StripePayment();
            $chargeData = $stripe->createCharge($request->stripe_source, $attendee);
            $attendee->payment_response = json_encode($chargeData);
            $attendee->payment_status = $chargeData->status == 'succeeded' ? '1' : '2';
        } else {
            $attendee->payment_response = "";
            $attendee->payment_status = '1';
        }
        if ($attendee->save() && $attendee->payment_status == '1') {
            $mail_data = [
                'email' => $user->email,
                'name' => $user->full_name,
                'subject' => 'CONCOCURE',
                'event_info' => $event_info,
                'attendee' => $attendee
            ];

            Mail::send('emails.event-purchased', $mail_data, function ($message) use ($mail_data) {
                $message->to($mail_data['email'], $mail_data['name'])->subject($mail_data['subject']);
            });

            $this->message = __('event.attendee_enrolled_success');
            $this->data['attendee'] = $attendee;
            return $this->response();
        } else {
            $this->message = __('event.attendee_enrolled_failed');
            return $this->response(false);
        }
    }

    public function attendees_list(Request $request)
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
            ->latest()
            ->limit($limit)
            ->offset($offset)
            ->get();
        $attendees->each->append('payment_status_label');

        $users = User::whereIn('id', EventAttendee::pluck('user_id'))->get();

        $this->data['users'] = $users;
        $this->data['attendees'] = $attendees;
        $this->data['events'] = $user->events;
        return $this->response();
    }

    public function guests_list(Request $request)
    {
        $page = 1;
        $limit = 20;
        if ($request->filled('page')) {
            $page = $request->page;
        }

        $offset = ($page - 1) * $limit;
        $user = $request->user();

        if ($request->filled('event')) {
            $event_id = $request->event;
            $event_info = $user->events()->where('id', $event_id)->first();
            if (!$event_info) {
                $this->message = __('event.invalid_event_error');
                return $this->response(false);
            } else {
                $event_ids = [$event_id];
            }
        } else {
            $event_ids = $user->events()->pluck('id');
        }

        $guests_query = EventGuest::with('user', 'event')
            ->select('*');
        $guests_query->whereIn('event_id', $event_ids);

        $guests = $guests_query
            ->latest()
            ->limit($limit)
            ->offset($offset)
            ->get();

        $this->data['guests'] = $guests;
        return $this->response();
    }

    public function insert_comment(Request $request, $event_id)
    {
        $user = $request->user();
        $event_info = Event::find($event_id);

        if (!$event_info) {
            $this->message = __('event.invalid_event_error');
            return $this->response(false);
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return $this->response();
        }

        $comment = $event_info->comments()->create([
            'user_id' => $user->id,
            'message' => $request->message,
            'parent_comment_id' => $request->parent_comment_id,
        ]);

        if ($comment) {
            $comment->user = $user;
            $comment->replies = [];
            $this->data['comment'] = $comment;
            return $this->response();
        }

        $this->message = __('common.errors.something');
        return $this->response(false);
    }

    public function comment_list(Request $request, $event_id)
    {
        $page = 2;
        $limit = 20;
        if ($request->filled('page')) {
            $page = $request->page;
        }
        $offset = ($page - 1) * $limit;
        $event_info = Event::find($event_id);

        if (!$event_info) {
            $this->message = __('event.invalid_event_error');
            return $this->response(false);
        }
        $comments =  $event_info->comments()
            ->with('user', 'replies')
            ->latest()
            ->limit($limit)
            ->offset($offset)
            ->get();
        $this->data['comments'] = $comments;
        return $this->response();
    }

    public function add_review(Request $request, $event_id)
    {
        $user = $request->user();
        $validator = Validator::make($request->all(), [
            'stars' => 'required',
            'review' => 'required'
        ]);

        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return $this->response();
        }

        $event = Event::find($event_id);
        if ($event) {
            $already_exists =  $event->reviews()->where('reviewer_id', $user->id)->first();
            if ($already_exists) {
                $this->message = __('event.review_already_posted');
            } else {
                $review = $event->reviews()->create([
                    'stars' => $request->stars,
                    'review' => $request->review,
                    'reviewer_id' => $user->id,
                    'performer_id' => $event->user_id,
                ]);
                if ($review) {
                    $this->data['review'] = $review;
                    return $this->response();
                }
                $this->message = __('common.errors.something');
            }
        } else {
            $this->message = __('event.event_not_found');
        }
        return $this->response(false);
    }

    public function get_reviews(Request $request, $event_id)
    {
        $page = 2;
        $limit = 20;
        if ($request->filled('page')) {
            $page = $request->page;
        }
        $offset = ($page - 1) * $limit;
        $event = Event::find($event_id);

        if ($event) {
            $reviews =  $event->reviews()
                ->with('reviewer')
                ->latest()
                ->limit($limit)
                ->offset($offset)
                ->get();
            $this->data['reviews'] = $reviews;
            return $this->response();
        } else {
            $this->message = __('event.event_not_found');
        }
    }

    public function send_invitations(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required',
            'email' => 'required|email',
            'event_id' => 'required',
        ]);

        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return $this->response();
        }

        $user = $request->user();
        $event_info = $user->events()->where('id', $request->event_id)->first();

        if (!$event_info) {
            $this->message = __('event.invalid_event_error');
            return $this->response(false);
        }

        $check_exists = EventGuest::where('event_id', $request->event_id)
            ->where('email', $request->email)
            ->first();
        if ($check_exists) {
            $this->message = __('event.invitation_send_already');
            return $this->response(false);
        }

        $guest = EventGuest::create([
            'event_id' => $request->event_id,
            'user_id' => 0,
            'full_name' => $request->full_name,
            'email' => $request->email
        ]);

        if ($guest) {
            $mail_sent = send_guest_invitation_email($event_info,  $request->full_name, $request->email, $user);
            if ($mail_sent) {
                $this->message = __('event.invitation_send_success');
                return $this->response();
            }
        }
        $this->message = __('common.errors.something');;
        return $this->response(false);
    }

    public function send_invitation_email_again(Request $request, $id)
    {
        $guest = EventGuest::find($id);

        if (!$guest) {
            $this->message = __('event.invalid_event_guest_error');
            return $this->response(false);
        }

        $user = $request->user();
        $event_info = $user->events()->where('id', $guest->event_id)->first();

        if (!$event_info) {
            $this->message = __('event.invalid_event_error');
            return $this->response(false);
        }

        $mail_sent = send_guest_invitation_email($event_info,  $guest->full_name, $guest->email, $user);
        if ($mail_sent) {
            $this->message = __('event.invitation_send_success');
            return $this->response();
        }
        $this->message = __('common.errors.something');;
        return $this->response(false);
    }

    public function delete_invitation(Request $request, $id)
    {
        $guest = EventGuest::find($id);

        if (!$guest) {
            $this->message = __('event.invalid_event_guest_error');
            return $this->response(false);
        }

        $user = $request->user();
        $event_info = $user->events()->where('id', $guest->event_id)->first();

        if (!$event_info) {
            $this->message = __('event.invalid_event_error');
            return $this->response(false);
        }

        if ($guest->delete()) {
            $this->message = __('event.invitation_deleted_success');
            return $this->response();
        }
        $this->message = __('common.errors.something');;
        return $this->response(false);
    }

    public function get_invitations(Request $request)
    {
        $page = 1;
        $limit = 5;
        if ($request->filled('page')) {
            $page = $request->page;
        }
        $offset = ($page - 1) * $limit;
        $user = $request->user();
        $event_invitations =  $user->event_invitations()
            ->with('user', 'event.performer')
            ->latest()
            ->limit($limit)
            ->offset($offset)
            ->get();
        $this->data['event_invitations'] = $event_invitations;
        return $this->response();
    }

    public function accept_invitation(Request $request, $id)
    {
        $user = $request->user();
        $invitation = $user->event_invitations()->find($id);
        if ($invitation && $invitation->status == '0') {
            $invitation->status = '1';
            $invitation->user_id =  $user->id;
            if ($invitation->save()) {
                $this->message = __('event.invitation_accepted');
                return $this->response();
            }
        }
        $this->message = __('event.invitation_accepted_failed');
        return $this->response(false);
    }

    public function reject_invitation(Request $request, $id)
    {
        $user = $request->user();
        $invitation = $user->event_invitations()->find($id);
        if ($invitation && $invitation->status == '0') {
            $invitation->status = '2';
            if ($invitation->save()) {
                $this->message = __('event.invitation_rejected');
                return $this->response();
            }
        }
        $this->message = __('event.invitation_rejacted_failed');
        return $this->response(false);
    }

    public function save_event(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'event_id' => 'required',
        ]);
        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return $this->response();
        }

        $saved = $user->saved_events()->updateOrcreate([
            'event_id' => $request->event_id
        ]);
        if ($saved) {
            $this->message = __('event.saved_success');
            return $this->response();
        }
        $this->message = __('event.saved_failed');
        return $this->response(false);
    }

    public function remove_event(Request $request, $id)
    {
        $user = $request->user();
        $saved_event = $user->saved_events()->find($id);
        if (!$saved_event) {
            $this->message =  __('common.errors.something');
            return $this->response(false);
        }
        if ($saved_event->delete()) {
            $this->message = __('event.removed_success');
            return $this->response();
        }
        $this->message = __('event.removed_failed');
        return $this->response(false);
    }

    public function saved_events(Request $request)
    {
        $page = 1;
        $limit = 5;
        if ($request->filled('page')) {
            $page = $request->page;
        }
        $offset = ($page - 1) * $limit;
        $user = $request->user();
        $saved_events =  $user->saved_events()
            ->with('event_info.performer')
            ->latest()
            ->limit($limit)
            ->offset($offset)
            ->get();
        $this->data['events'] = $saved_events;
        return $this->response();
    }
}
