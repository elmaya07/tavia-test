<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use Illuminate\Mail\Mailable;
use App\Mail\ExampleMailable;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

// $router->get('/account-activation', function () use ($router) {   
//   $data = Mail::send(new ExampleMailable('Account activation','purnamaindah262@gmail.com'));
//   print_r($data);
// });
    
 Route::get('/account-activation/{id}', 'AuthController@account_activation');
 Route::get('/account-activation-process/{id}/{url}', 'AuthController@process_account_activation');
 Route::post('forgot-password', 'AuthController@forgot_password');
 Route::get('process-forgot-password/{id}/{url}', 'AuthController@process_forgot_password');

Route::group([

    'prefix' => 'api'

], function ($router) {
    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::get('profile', 'AuthController@profile');

});


