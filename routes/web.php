<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');


});
Route::get('/cancel', function () {
    return view('cancel');  // assuming you have a cancel.blade.php file in your views directory
})->name('cancel');
Route::get('/success', function () {
    return view('success');  // assuming you have a success.blade.php file in your views directory
})->name('success');
Route::get('/payout', 'PayoutController@showPayoutForm')->name('payout');

//Route::get('success', [PaymentController::class, 'success'])->name('success');
//Route::get('cancel', [PaymentController::class, 'cancel'])->name('cancel');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
