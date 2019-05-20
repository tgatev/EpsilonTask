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

Route::get('/', "DashboardController@index")->name("Home");

Route::get('/services_datatable','DashboardController@displayServices')->name('ServicesDatatable');
Route::post('/services_datatable_data','DashboardController@getServicesData')->name('ServicesDatatable.data');
Route::get("/service_details/{id}/", 'DashboardController@displayServiceDetails')->name("ServiceDetails");
