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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/admin', function () {
    return view('admin.login');
});
Route::post('/dashboard', function () {
    return view('admin.dashboard');
})->name('dashboard');


Route::get('/primary_user', function () {
    return view('admin.primary_user');
});

Route::get('/beneficiary_user', function () {
    return view('admin.beneficiary_user');
});


Route::get('/datatable', function () {
    return view('admin.datatable');
});


Route::get('/modified-template', function () {
    return view('admin.modified-template');
});


