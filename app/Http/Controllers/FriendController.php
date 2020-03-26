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

    public function loggedUserFriends(){
        $friends = Auth::user()->friends()->select('id','status','created_at AS accepted_on')->get()->toArray();
        $active = $rejected = $blocked = $pending = [];
        for ($i=0; $i < count($friends); $i++) { 
            $info = Friend::find($friends[$i]['id'])->info()->select('name','email','profile_image')->get()->toArray();
            $friends[$i]['name'] = $info[0]['name'];
            $friends[$i]['email'] = $info[0]['email'];
            $friends[$i]['profile_image'] = Storage::url($info[0]['profile_image']);
            switch ($friends[$i]['status']) {
                case 'active':
                    array_push($active,$friends[$i]);
                    break;

                case 'rejected':
                    array_push($rejected,$friends[$i]);
                    break;

                case 'friends':
                    array_push($friends,$friends[$i]);
                    break;

                case 'pending':
                    array_push($pending,$friends[$i]);
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
        $q = $request->query('q');
        $data = User::select('name','email','profile_image')->where('email','like',$q)->orderBy('email')->get(10);
        return response()->json($data, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    public function update(Request $request, User $user)
    {
        //
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
