<?php

use App\Middleware\AuthMiddleware;
use Core\Http\Request;
use Core\Http\Response;
use Core\Http\Route;
use Core\OpenAPI\OAIParameter;
use Core\OpenAPI\OAIResponse;
use Core\OpenAPI\SwaggerUI;

//OPENAPI
$response = new OAIResponse();
$secParameter = new OAIParameter('authorization', 'header', 'Authorization key', true);
$userParameter = new OAIParameter('username', 'formData', 'Username', true);
$passParameter = new OAIParameter('password', 'formData', 'Password', true);
$fileParameter = new OAIParameter('file', 'formData', 'File UPload', true,'file','binary');
//

Route::Get('/test', 'DefaultController@index')->name('test');

Route::Get(['/function/{id}/get/{path?}', '/get/{id?}'], function (Request $request, $id, $path, Response $response) {
    return $response::Json(array('data' => $id . '::test function ->' . $path . '->' . $request->input('key')));
})->addOAIResponse($response)->middleware(function (Request $request, $id) {
    $request->setRequestData('key', 'hello' . $id);
})->middleware(AuthMiddleware::class);

Route::Group('/api', function () use ($userParameter, $passParameter, $response, $secParameter) {

    Route::Get('/docs', function (Response $response) {
        $response->AddHeader('Content-type', 'text/html');
        return $response->Send(SwaggerUI::renderer());
    })->name('swagger')->asApi(false);

    Route::Group('/admin', function () use ($response, $secParameter) {
        Route::Get('/dashboard', "DefaultController@admin");
        Route::Get('/student', "DefaultController@admin");
    })->addOAIParameter($secParameter)->name('admin')->asApi();

    Route::Get('/test',"DefaultController@index");
    //Route::Get('/login', "LoginController@login")->name('login_get');
    Route::Post('/login', "LoginController@login")->addOAIParameter([$userParameter, $passParameter])->addOAIResponse($response)->name('login_post');

})->name('api')->asApi()->addOAIResponse($response);


Route::Post("/api/upload", "ApiController@index")->addOAIParameter($fileParameter)->name("api")->asApi();
Route::Get("/*", "ReactController@index")->name("react_route");
