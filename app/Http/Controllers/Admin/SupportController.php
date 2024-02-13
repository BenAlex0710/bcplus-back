<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupportController extends Controller
{
    public function index()
    {
        $performers = User::select('id', 'first_name', 'last_name', 'username', 'photo')
            ->withCount(['support_messages' => function ($query) {
                $query->where('status', '0');
            }])
            ->orderBy('support_messages_count', 'desc')
            ->where('role', '2')
            ->get();
        $page_title = __('support.admin.page_title');
        return view('admin.support', compact('page_title', 'performers'));
    }

    public function send_message_to_user(Request $request)
    {
        $message = SupportMessage::create([
            'from' => '0',
            'to' => $request->user_id,
            'message' => $request->message,
        ]);
        $response = [
            'status' => false,
            'message' => __('common.errors.something'),
            'data' => []
        ];

        if ($message) {
            $response['status'] = true;
            $response['message'] = __('support.message_sent_success');
            $response['data'] = $message;
            return response()->json($response);
        }
        $response['message'] = __('support.message_sent_failed');
        return response()->json($response);
    }

    public function get_messages(Request $request)
    {
        $page = 1;
        $limit = 20;
        if ($request->filled('page')) {
            $page = $request->page;
        }
        $offset = ($page - 1) * $limit;
        $user_id = $request->user_id;
        $user = User::find($user_id);
        $response = [
            'status' => false,
            'message' => __('common.errors.something'),
            'data' => []
        ];
        if (!$user) {
            return response()->json($response);
        }

        $messages = SupportMessage::where('from', $user->id)
            ->orWhere('to', $user->id)
            ->latest()
            ->limit($limit)
            ->offset($offset)
            ->get();

        SupportMessage::where('from', $user->id)
            ->orWhere('to', $user->id)->update(['status' => '1']);

        $response['message'] = '';
        $response['data'] = $messages;
        $response['status'] = true;
        return response()->json($response);
    }
}
