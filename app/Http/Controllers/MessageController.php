<?php

namespace App\Http\Controllers;

use App\Message;
use App\User;
use App\Friend;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $friends = app(FriendController::class)->loggedUserFriends();
        return view('messages.index')->with(compact('friends'));
    }

    public function getMessages($id)
    {
        $messages = Message::select('sender_id','message','created_at')->where([
            ['sender_id', Auth::user()->id],
            ['receiver_id', $id],
        ])->orWhere([
            ['receiver_id', Auth::user()->id],
            ['sender_id', $id],
        ])->latest()->limit(20)->get();
        $messages = $messages->toArray();
        for ($i=0; $i < count($messages); $i++) { 
            $created_at = Carbon::parse($messages[$i]['created_at']);
            $created_at->setTimeZone('Asia/Kolkata');
            $messages[$i]['created_at'] =  $created_at->format('d-m-Y h:i a');
            if($messages[$i]['sender_id'] == Auth::user()->id){
                $messages[$i]['type'] = 'send';
            }else{
                $messages[$i]['type'] = 'receive';
            }
            unset($messages[$i]['sender_id']);
        }

        return response()->json(['status' => 'status','data' => $messages], 200);
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $check = Friend::where('user_id',Auth::user()->id)->where('friend_id',$input['friend_id'])->where('status','active')->exists();
        $check2 = Friend::where('friend_id',Auth::user()->id)->where('user_id',$input['friend_id'])->where('status','active')->exists();
        if(!$check && !$check2){
            return response()->json(['status' => 'error','message' => 'User is not a friend.'], 200);
        }
        if($input['message'] == ''){
            return response()->json(['status' => 'error','message' => 'Message should not be empty.'], 200);
        }
        $data['sender_id'] = Auth::user()->id;
        $data['receiver_id'] = $input['friend_id'];
        $data['message'] = $input['message'];
        $store = Message::create($data);
        if($store){
            return response()->json(['status' => 'success','message' => 'Message Sent.'], 200);
        }else{
            return response()->json(['status' => 'error','message' => 'Failed to send Message.'], 200);
        }

    }
}
