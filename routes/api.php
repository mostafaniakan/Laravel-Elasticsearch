<?php

use App\Http\Controllers\ElasticController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('search', [ElasticController::class,'search']);
Route::post('create',[ElasticController::class,'store']);
Route::get('sync-data',[ElasticController::class,'syncData']);

Route::get('/config', [ElasticController::class,'config']);
