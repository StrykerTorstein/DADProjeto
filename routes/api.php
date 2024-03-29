<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
/*
Route::group(['middleware' => 'operator'], function(){
    //Create a payment
    Route::post('movements/payment', 'MovementController@payment');
});
*/
Route::group(['middleware' => ['auth:api','operator']], function(){
    //Create a payment
    Route::post('movements/payment', 'MovementController@payment');
});


Route::middleware(['auth:api','admin'])->get('users/all', 'UserControllerAPI@all');
Route::middleware(['auth:api','admin'])->get('movements/movementStatistics','MovementController@movementStatistics');


Route::group(['middleware' => 'auth:api'], function(){
    Route::get('users/checkNewPassword','UserControllerAPI@checkNewPassword');
    Route::get('categories/all','CategoryController@all');
    Route::post('movements/show/{id}', 'MovementController@show');
    Route::get('wallets/allemails', 'WalletController@allEmails');
    Route::post('movements/expense/external','MovementController@expenseExternal');
    Route::post('movements/expense/transfer','MovementController@expenseTranfer');
    Route::get('movements/getAllUserMovements','MovementController@getAllUserMovements');
    Route::put('movements/updateMovementDescriptionAndCategory/{id}','MovementController@updateMovementDescriptionAndCategory');
});

Route::get('wallets/exists/{email}', 'WalletController@exists');

Route::get('departments', 'DepartmentControllerAPI@index');
Route::get('users/emailavailable', 'UserControllerAPI@emailAvailable');

Route::get('users/getphotobyemail/{email}','UserControllerAPI@getPhotoByEmail');

Route::get('users/{id}', 'UserControllerAPI@show');
Route::post('users', 'UserControllerAPI@store');
Route::put('users/{id}', 'UserControllerAPI@update');

Route::group(['middleware' => ['auth:api','admin']], function(){
    Route::get('users', 'UserControllerAPI@index');
    Route::delete('users/{id}', 'UserControllerAPI@destroy');
    Route::post('users/add', 'UserControllerAPI@addUser');
    Route::put('users/{user}/active', 'UserControllerAPI@activeOrInactive');
});



//get porque vou buscar um número
Route::get('wallets/count', 'WalletController@count');

Route::post('login', 'LoginControllerAPI@login');
Route::middleware('auth:api')->post('logout','LoginControllerAPI@logout');

//Route::middleware('auth:api')->get('{id}/movements', 'MovementController@show');

Route::middleware('auth:api')->get('{id}/movements', 'MovementController@filter');

Route::get('categories/names/{type}','CategoryController@names');
Route::get('categories/names','CategoryController@categoryName');
Route::get('wallet/{id}/balance', 'WalletController@getBalance');

Route::get('categories/names','CategoryController@categoryName');

Route::get('movements/types','MovementController@types');



/*
Caso prefiram usar Resource Routes para o user, podem implementar antes as rotas:
NOTA: neste caso, o parâmetro a receber nos métodos do controlador é user e não id

Route::apiResource('users','UserControllerAPI');
Route::get('users/emailavailable', 'UserControllerAPI@emailAvailable');
*/
