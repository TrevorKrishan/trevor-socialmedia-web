<?php

namespace App\Http\Controllers;

use App\User;
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
        $input = $request->only('email', 'password');
        if (Auth::attempt($input)) {
            return redirect('/');
        }else {
            return redirect()->back()->withErrors('Email Password does not match');
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
        $input = $request->all();
        $path = $request->file('profile_image')->store('profile_images');
        $input['profile_image'] = $path;
        $input['password'] = Hash::make($input['password']);
        if (User::create($input)) {
            return response()->json(['status' => 'success', 'message' => 'User Registerd Successfully.']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Failed To Register User.']);
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
