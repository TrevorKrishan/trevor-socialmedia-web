<?php

namespace App\Http\Controllers;

use App\User;
use App\Friend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth', ['except' => ['login', 'store']]);
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Server Validation Error.']);
        }

        $input = $request->only('email', 'password');
        
        if (Auth::attempt($input)) {
            return redirect('/');
        }else {
            return redirect()->back()->withErrors(['email' => 'Invalid Combination','password' => 'Invalid Combination']);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('login');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // try {
            $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required',
                'profile_image' => 'required|file',
            ]);
        // } catch (\Exception $e) {
        //     return response()->json(['status' => 'error', 'message' => 'Server Validation Error.']);
        // }
        
       try {
            $path = $request->file('profile_image')->store('profile_images');
       } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Image Upload Error.']);
       }
       
        $input = $request->all();
        $input['profile_image'] = $path;
        $input['password'] = Hash::make($input['password']);
        
        try {
            $create = User::create($input);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Server Error Registring User.']);
        }

        if ($create) {
            return response()->json(['status' => 'success', 'message' => 'User Registerd Successfully.']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Failed To Register User.']);
        }
    }

    public function notification()
    {
        $friend_request = Auth::user()
                                ->friendTo()
                                ->select('id')
                                ->where('status','pending')
                                ->latest()
                                ->limit(5)
                                ->get()
                                ->toArray();
        
        $data = [];
        
        for ($i=0; $i < count($friend_request); $i++) { 
            $info = Friend::find($friend_request[$i]['id'])
                            ->friend()
                            ->select('name','profile_image')
                            ->get()
                            ->toArray();
           
            array_push($data,[
                                'id'=>$friend_request[$i]['id'],
                                'name'=>$info[0]['name'],
                                'profile_image'=>$info[0]['profile_image']
                            ]);
        }
       
        if ($data) {
            return response()->json(['status' => 'success', 'data' => $data]);
        } else {
            return response()->json(['status' => 'error', 'message' => 'No Notification Found.']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\user  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(user $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\user  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, user $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\user  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(user $user)
    {
        //
    }
}
