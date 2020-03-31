<?php

namespace App\Http\Controllers;

use App\User;
use App\Friend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FriendController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $friends = $this->loggedUserFriends();
        return view('friends.index')->with(compact('friends'));
    }

    public function loggedUserFriends()
    {
        $active = $rejected = $blocked = $pending = [];
   
        $friends = Auth::user()->friends()->select('id','status','created_at AS accepted_on')->get()->toArray();

        $friendsTo = Auth::user()->friendTo()->select('id','status','created_at AS accepted_on')->get()->toArray();

        for ($i=0; $i < count($friends); $i++) { 
            $info = Friend::find($friends[$i]['id'])
                            ->info()
                            ->select('id as friend_id','name','email','profile_image')
                            ->get()
                            ->toArray();
            
            $friends[$i]['name'] = $info[0]['name'];
            $friends[$i]['friend_id'] = $info[0]['friend_id'];
            $friends[$i]['email'] = $info[0]['email'];
            $friends[$i]['profile_image'] = Storage::url($info[0]['profile_image']);
            
            switch ($friends[$i]['status']) {
                case 'active':
                    array_push($active,$friends[$i]);
                    break;

                case 'rejected':
                    array_push($rejected,$friends[$i]);
                    break;

                case 'blocked':
                    array_push($blocked,$friends[$i]);
                    break;

                case 'pending':
                    array_push($pending,$friends[$i]);
                    break;
    
                default:
                    break;
            }
        }

        for ($i=0; $i < count($friendsTo); $i++) { 
            $info = Friend::find($friendsTo[$i]['id'])
                            ->friend()
                            ->select('id as friend_id','name','email','profile_image')
                            ->get()
                            ->toArray();
            
            $friendsTo[$i]['name'] = $info[0]['name'];
            $friendsTo[$i]['friend_id'] = $info[0]['friend_id'];
            $friendsTo[$i]['email'] = $info[0]['email'];
            $friendsTo[$i]['profile_image'] = Storage::url($info[0]['profile_image']);
            
            switch ($friendsTo[$i]['status']) {
                case 'active':
                    array_push($active,$friendsTo[$i]);
                    break;

                case 'rejected':
                    array_push($rejected,$friendsTo[$i]);
                    break;

                case 'blocked':
                    array_push($blocked,$friendsTo[$i]);
                    break;

                case 'pending':
                    array_push($pending,$friendsTo[$i]);
                    break;
    
                default:
                    break;
            }
        }

        return ['active' => $active,'rejected' => $rejected,'blocked' => $blocked,'pending' => $pending];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $friends = Auth::user()->friends()->select('id')->get()->toArray();
        $friendsTo = Auth::user()->friendTo()->select('id')->get()->toArray();;
        $total = array_merge($friends,$friendsTo);

        $ids = [Auth::user()->id];

        foreach ($total as $value) {
            array_push($ids,$value['id']);
        }

        $ids = array_unique($ids);

        $q = $request->query('q');

        $data = User::select('name','id','profile_image')->where('name','like',"%$q%")->whereNotIn('id',$ids)->orderBy('name')->take(10)->get();

        return response()->json(['data' => $data], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
       
        $check = Friend::where('user_id',Auth::user()->id)->where('friend_id',$id)->exists();
        $check2 = Friend::where('friend_id',Auth::user()->id)->where('user_id',$id)->exists();
       
        if($check || $check2){
            return response()->json(['status' => 'error','message' => 'Cannot add this user as a friend.'], 200);
        }else{
            $data['user_id'] = Auth::user()->id;
            $data['friend_id'] = $id;
            $data['status'] = 'pending';
            
            $create = Friend::create($data);
            
            if($create){
                return response()->json(['status' => 'success','message' => 'Friend Request Sent.'], 200);
            }else{
                return response()->json(['status' => 'error','message' => 'Failed to send friend request.'], 200);
            }
        }
    }

    public function blockFriend(Request $request)
    {
        $input = $request->all();
        $email = $input['email'];
       
        $data = User::select('id')->where('email',$email)->first()->toArray();
        $id = $data['id'];
        
        $check = Friend::where('user_id',Auth::user()->id)->where('friend_id',$id)->exists();
        $check2 = Friend::where('friend_id',Auth::user()->id)->where('user_id',$id)->exists();
        
        if($check){
            $block = Friend::where('user_id',Auth::user()->id)
                            ->where('friend_id',$id)
                            ->update(['status' => 'blocked']);
        }elseif($check2){
            $block = Friend::where('friend_id',Auth::user()->id)
                            ->where('user_id',$id)
                            ->update(['status' => 'blocked']);
        }else{
            return response()->json(['status' => 'error','message' => 'You can only block users that are friends.'], 200);
        }
        
        if($block){
            return response()->json(['status' => 'success','message' => 'Friend blocked successfully.'], 200);
        }else{
            return response()->json(['status' => 'error','message' => 'Failed to block user.'], 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Friend $friend)
    {
        $input = $request->all();
        
        if($input['value'] == 'accept'){
            $friend->status = 'active';
            $msg = 'accpeted';
        }elseif($input['value'] == 'reject'){
            $friend->status = 'rejected';
            $msg = 'rejected';
        }else{
            return response()->json(['status' => 'error','message' => 'Unknowm Value.'], 200);
        }

        if($friend->save()){
            return response()->json(['status' => 'success','message' => "Friend request $msg."], 200);
        }else{
            return response()->json(['status' => 'error','message' => 'Failed to accept request.'], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
