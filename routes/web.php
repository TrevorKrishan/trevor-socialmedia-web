<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['guest']], function () {
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');

    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
});

Route::post('/login', 'UserController@login')->name('login');
Route::post('/logout', 'UserController@logout')->name('logout');
Route::get('/notification', 'UserController@notification')->name('notification');

Route::resource('user', 'UserController');

Route::resource('friend', 'FriendController');
Route::get('/search', 'FriendController@search')->name('friend.search');
Route::post('/block', 'FriendController@blockFriend')->name('friend.block');

Route::get('/message', 'MessageController@index')->name('messages');
Route::get('/message/{id}', 'MessageController@getMessages');
Route::post('/message', 'MessageController@store')->name('messages.store');

Route::get('/', 'DashboardController@index')->name('dashboard');
