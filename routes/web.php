<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
//use tablespacesController:
use App\Http\Controllers\tablespacesController;
use Illuminate\Http\Request;


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

//Route::get('tablespaces/schemas', [tablespacesController::class, 'schemas']);
//Route::get('tablespaces/publicPath', [tablespacesController::class, 'publicPath']);
//Route::post('tablespaces/createTablespace', [tablespacesController::class, 'createTablespace']);
//Route::post('tablespaces/temporary', [tablespacesController::class, 'createTemporaryTablespace']);
//Route::delete('tablespaces/delete/{tablespace}', [tablespacesController::class, 'deleteTablespace']);
//Route::get('tablespaces/list', [tablespacesController::class, 'tablespaces']);

//ver los tablespaces
Route::get('/tablespaces', [tablespacesController::class, 'tablespaces']);
//rize a tablespace
Route::get('/resize/{tablespace}/{size}', [tablespacesController::class, 'resizeTablespace']);
//create a tablespace
Route::get('/create/{tablespace}', [tablespacesController::class, 'createTablespace']);
//create a temporary tablespace
Route::get('/create-temp/{tablespace}', [tablespacesController::class, 'createTemporaryTablespace']);
//Delete a tablespace
Route::get('/delete/{tablespace}', [tablespacesController::class, 'deleteTablespace']);