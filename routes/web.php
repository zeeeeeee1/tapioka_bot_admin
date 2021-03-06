<?php

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin'], function () {
    Route::get('/', function () {
        return view('admin.dashboard-home');
    });
    Route::get('/users', function () {
        return view('admin.dashboard-users-archive');
    });
    Route::get('stores', 'StoreController@index')->name('stores');
    Route::get('stores/create', 'StoreController@create')->name('stores.create');
    Route::get('stores/{store}/edit', 'StoreController@edit')->name('stores.edit');
    Route::patch('stores/{store?}', 'StoreController@update')->name('stores.update');
    Route::put('stores/{store?}', 'StoreController@update')->name('stores.create.exec');

    Route::get('coupons', 'CouponController@index')->name('coupons');
    Route::get('coupons/create', 'CouponController@create')->name('coupons.create');
    Route::get('coupons/{coupon}/edit', 'CouponController@edit')->name('coupons.edit');
    Route::patch('coupons/{coupon?}', 'CouponController@update')->name('coupons.update');
    Route::put('coupons/{coupon?}', 'CouponController@update')->name('coupons.create.exec');
});

Route::get('/coupons/{coupon?}', 'CouponController@detail')->name('coupons.detail');