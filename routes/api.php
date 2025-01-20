<?php

use App\Http\Controllers\InternalController;
use App\Http\Middleware\EMiddleWareAliases;
use App\Http\Middleware\PreventRequestsDuringMaintenance;
use App\Lib\Utils\RouteNameField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Route::middleware(EMiddleWareAliases::auth->name)->post('/user', [InternalController::class, 'apiGetUsers'])->name(RouteNameField::APIGetUsers->value);
Route::post('/user', [InternalController::class, 'apiGetUsers'])->name(RouteNameField::APIGetUsers->value);

Route::post('/sharetable/create/preview', [\App\Http\Controllers\ShareTablesController::class, 'apiPreviewFileTemporary3']);

//Route::apiResource('animal', AnimalController::class);

Route::post('hashmaker', function () {
    $random = Str::random(60);
    return response()->json([
        $random,
        "hash" => Hash::make($random, [
            'memory' => 4096,
            'time' => 10,
            'threads' => 4,
            'algo' => PASSWORD_ARGON2ID,
        ])
    ]);
});

Route::post('down', function (Request $request) {
    if (Hash::check($request['key'], '$2y$12$7qWtSxwgM5GZCvxaYKOod.Ns9acYLWAD4HWnc4UC6gOVbZolThpIe')) {
        if (App::isDownForMaintenance()) {
            App::maintenanceMode()->deactivate();
            return response()->json([
                "message" => "turn off maintenance Mode"
            ]);
        } else {
            $random = Str::random("60");
            App::maintenanceMode()->activate([
                'except' => App::make(PreventRequestsDuringMaintenance::class)->getExcludedPaths(),
                'redirect' => "/",
                'retry' => 60,
                'refresh' => 15,
                'secret' => $random,
                'status' => 503,
                'template' => null,
            ]);
            return response()->json([
                "message" => "turn on maintenance Mode",
                "token" => $random
            ]);
        }
    }
});
