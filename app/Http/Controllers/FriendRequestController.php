<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FriendRequests;
use App\Models\MessageModel;
use App\Models\User;
use Illuminate\Support\Facades\Response;

class FriendRequestController extends Controller
{
    public function sendFriendRequest(Request $request)
    {
              $friend_requests=new FriendRequests();
              $friend_requests->user_id=$request->user_id;
              $friend_requests->friend_id=$request->friend_id;
              $friend_requests->status=$request->status;
              $friend_requests->save();


            return Response::json([
             'message' => 'Friend request created successfully',
               'friend_request' => $friend_requests
            ], 200);
    }

    public function acceptFriendRequest(Request $request, $id)
    {

        $friendRequest = FriendRequests::find($id);

        if (!$friendRequest) {
            return response()->json(['message' => 'Friend request not found'], 404);
        }


        $friendRequest->status = 'accepted';
        $friendRequest->save();

        return response()->json(['message' => 'Friend request accepted successfully', 'friend_request' => $friendRequest], 200);
    }


public function checkFriendRequest($user_id, $friend_id)
{
    $userToFriendRequest = FriendRequests::where('user_id', $user_id)
                                        ->where('friend_id', $friend_id)
                                        ->first();

    $friendToUserRequest = FriendRequests::where('user_id', $friend_id)
                                        ->where('friend_id', $user_id)
                                        ->first();

    if ($userToFriendRequest) {
        return response()->json(['status' => $userToFriendRequest->status], 200);
    }
    else if($friendToUserRequest){
        return response()->json(['status' => $friendToUserRequest->status], 200);
    }
    else{
         return response()->json(['message' => 'Friend request status not found'], 404);
    }

}
  public function messagelist(Request $request){
        $message=new MessageModel();
        $message->room_id=$request->room_id;
        $message->message=$request->message;
        $message->sender=$request->sender;
        $message->receiver=$request->receiver;
        $message->save();
        return Response::json([
            'message' => 'Your Messsage created successfully',
              'message' => $message
           ], 200);
  }
  public function getmessages(){
    $messages=MessageModel::all();
    return response()->json(['messages' => $messages], 200);
  }
  public function friendlist($user_id){
$userToFriendRequest = FriendRequests::where('user_id', $user_id)
->where('status','accepted')
->get();

$friendToUserRequest = FriendRequests::where('friend_id', $user_id)
->where('status','accepted')
->get();

$userIds = $userToFriendRequest->pluck('friend_id')->merge($friendToUserRequest->pluck('user_id'))->unique();

$users = User::whereIn('id', $userIds)->get();

return response()->json(['friends' => $users], 200);

}
public function friendlistpending($user_id){
    $userToFriendRequest = FriendRequests::where('user_id', $user_id)
    ->where('status','pending')
    ->get();

    $friendToUserRequest = FriendRequests::where('friend_id', $user_id)
    ->where('status','pending')
    ->get();

    // $userIds = $userToFriendRequest->pluck('friend_id')->merge($friendToUserRequest->pluck('user_id'))->unique();

    // $users = User::whereIn('id', $userIds)->get();

    // return response()->json(['friends' => $users], 200);
    $friendListWithUserInfo = [];
    $userIds = $userToFriendRequest->merge($friendToUserRequest);
    foreach ($userIds as $friend) {
        $user = User::find($friend->user_id);
        if ($user) {
            $friendListWithUserInfo[] = [
                'id' => $friend->id,
                'user_id' => $friend->user_id,
                'friend_id' => $friend->friend_id,
                'status' => $friend->status,
                'created_at' => $friend->created_at,
                'updated_at' => $friend->updated_at,
                'user_info' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email'=>$user->email,
                ]
            ];
        }
    }

    return response()->json([
        'Friendlist' => $friendListWithUserInfo,
    ], 200);

    }
public function blocklist($user_id){
    $userToFriendRequest = FriendRequests::where('user_id', $user_id)
    ->where('status','blocked')
    ->get();

    $friendToUserRequest = FriendRequests::where('friend_id', $user_id)
    ->where('status','blocked')
    ->get();

    $userIds = $userToFriendRequest->pluck('friend_id')->merge($friendToUserRequest->pluck('user_id'))->unique();

    $users = User::whereIn('id', $userIds)->get();

    return response()->json(['friends' => $users], 200);

    }

  public function getusermessage($user_id, $friend_id) {
    $user_messages = MessageModel::where('sender', $user_id)
        ->where('receiver', $friend_id)
        ->orWhere(function($query) use ($user_id, $friend_id) {
            $query->where('sender', $friend_id)
                  ->where('receiver', $user_id);
        })
        ->get();

    $messages = [];

    foreach($user_messages as $message) {
        $messages[] = [
            'sender' => $message->sender,
            'receiver' => $message->receiver,
            'room_id' => $message->room_id,
            'message' => $message->message,
            'created_at' => $message->created_at,

        ];
    }

    return response()->json(['messages' => $messages], 200);
}

}
