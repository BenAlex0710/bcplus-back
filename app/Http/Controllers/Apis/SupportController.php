<?php

namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\SupportMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupportController extends Controller
{
    public function send_message_to_admin(Request $request)
    {
        $user = $request->user();
        if ($user->role != '2') {
            $this->message = __("support.not_allowed");
            return $this->response(false);
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required'
        ]);

        if ($validator->fails()) {
            $this->message = __('common.errors.form_error');
            $this->errors = $validator->errors();
            return $this->response();
        }

        $message = SupportMessage::create([
            'from' => $user->id,
            'to' => '0',
            'message' => $request->message,
        ]);
        if ($message) {
            $this->data['message'] = $message;
            $this->message = __('support.message_sent_success');
            return $this->response();
        }
        $this->message = __('support.message_sent_failed');
        return $this->response(false);
    }


    public function get_messages(Request $request)
    {
        $page = 1;
        $limit = 20;
        if ($request->filled('page')) {
            $page = $request->page;
        }
        $offset = ($page - 1) * $limit;
        $user = $request->user();

        $messages = SupportMessage::where('from', $user->id)
            ->orWhere('to', $user->id)
            ->latest()
            ->limit($limit)
            ->offset($offset)
            ->get();
        $this->data['messages'] = $messages;
        return $this->response();
    }
}
