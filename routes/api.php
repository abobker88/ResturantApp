<?php

use App\Http\Controllers\API\BookingController;
use App\Http\Controllers\API\ReservationController;
use App\Http\Controllers\API\TableController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('login', [UserController::class, 'authenticate']);


Route::group(['middleware' => ['jwt.verify']], function() {
    Route::get('logout', [UserController::class, 'logout']);
    Route::get('get_user', [UserController::class, 'get_user']);
    Route::middleware('checkAdmin')->group(function () {
       Route::resource('table', TableController::class);
       Route::post('register_employee', [UserController::class, 'register']);
       Route::delete('table', [TableController::class, 'destroy']);
    Route::get('get_all_reservations', [BookingController::class, 'allReservation']);

    });
    Route::get('get_shift', [ReservationController::class, 'getShift']);
    Route::get('get_free_slot', [ReservationController::class, 'checkAvailableSlot']);
    Route::post('reserve', [ReservationController::class, 'booking']);
    Route::get('get_today_reservations', [BookingController::class, 'today']);
    Route::delete('reservation/delete', [BookingController::class, 'destroy']);

    
    
});