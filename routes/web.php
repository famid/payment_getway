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

use App\Http\Repository\SubscriptionRepository;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::middleware(['auth'])->namespace('User')->group(function (){
    Route::get('/create-blog', 'BlogController@showCreateBlog')->name('createBlog');
    Route::post('/create-blog', 'BlogController@createBlog')->name('createBlog');
    Route::get('/get-blog-list', 'BlogController@getBlogList')->name('getBlogList');
    Route::post('/delete-blog/id', 'BlogController@deleteBlog')->name('deleteBlog');
    Route::get('/get-all-blog','BlogController@getAllBlog')->name('getAllBlog');

    Route::get('/export', 'BlogController@export')->name('export');
    Route::get('/import-export-view', 'BlogController@importExportView')->name('importExportView');
    Route::post('import', 'BlogController@import')->name('import');

    Route::get('view-image', 'BlogController@viewImage')->name('viewImage');
    Route::post('/mollie-payment','MollieController@preparePayment')->name('mollie.payment');
    Route::get('/just-friend-callback-url/{orderId}','MollieController@redirectUrlOfJustFriend')->name('mollie.justFriend.redirectUrl');
    Route::get('/best-friend-callback-url/{orderId}','MollieController@redirectUrlOfBestFriend')->name('mollie.bestFriend.redirectUrl');
    Route::post('/subscription/webhook/{subscriptionId}','MollieController@webhook')->name('mollie.webhook');
    Route::get('/cancel/subscription','MollieController@cancelSubscription')->name('mollie.cancelSubscription');

    Route::get('stripe', 'StripePaymentController@stripe');
//Route::get('stripe-payment', 'StripePaymentController@stripePost')->name('stripe.post');
    Route::post('stripe', 'StripePaymentController@stripePost')->name('stripe.post');

});

Route::get('/test-class', 'TestClassController@testClass')->name('testClass');


