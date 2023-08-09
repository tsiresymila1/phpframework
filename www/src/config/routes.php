<?php

use App\Middleware\AuthMiddleware;
use Core\Http\Request;
use Core\Http\Response;
use Core\Http\Route;
use Core\OpenAPI\OAIParameter;
use Core\OpenAPI\OAIRequestBody;
use Core\OpenAPI\OAIRequestBodyContent;
use Core\OpenAPI\OAIResponse;
use Core\OpenAPI\OAISchema;
use Core\OpenAPI\OAISecurity;
use Core\OpenAPI\OpenApi;
use Core\OpenAPI\SwaggerUI;

//OPENAPI
$response = new OAIResponse();
$secParameter = new OAIParameter('authorization', 'header', 'Authorization key', true);
$userParameter = new OAIParameter('username', 'formData', 'Username', true);
$passParameter = new OAIParameter('password', 'formData', 'Password', true);
$fileParameter = new OAIParameter('file', 'formData', 'File UPload', true, 'file', 'binary');

$userCreateParameter = [
    new OAIParameter('email', 'formData', 'Email', true),
    new OAIParameter('name', 'formData', 'Username', true),
    new OAIParameter('userimage', 'formData', 'Image', true, 'file', 'binary'),
    new OAIParameter('password', 'formData', 'Password', true)
];
$bearerSecurity = new OAISecurity("Bearer", "http", null, null, null, "bearer");
$bearerSecurity->bearerFormat = 'JWT';

$loginRequestBody = new OAIRequestBody(new OAIRequestBodyContent('application/json', "AuthDto"), true);

OpenApi::addSchema(new OAISchema("AuthDto", "object", [
    "username" => ["type" => "string"],
    "password" => ["type" => "string"]
], ["username", "password"]), );


// NED OPENAPI

Route::Get('/test', 'DefaultController@index')->name('test');

Route::Get(['/function/{id}/get/{path?}', '/get/{id?}'], function (Request $request, $id, $path, Response $response) {
    return $response::Json(array('data' => $id . '::test function ->' . $path . '->' . $request->input('key')));
})->addOAIResponse($response)->middleware(function (Request $request, $id) {
    $request->setRequestData('key', 'hello' . $id);
})->middleware(AuthMiddleware::class);

Route::Group('/api', function () use ($loginRequestBody, $response, $userCreateParameter, $bearerSecurity) {

    Route::Get('/docs', function (Response $response) {
        $response->AddHeader('Content-type', 'text/html');
        return $response->Send(SwaggerUI::renderer());
    })->name('swagger')->asApi(false);

    Route::Group('/admin', function () {
        Route::Get('/dashboard', "DefaultController@admin");
        Route::Get('/student', "DefaultController@admin");
    })->name('admin')->asApi()->addOAISecurity([$bearerSecurity]);
    //Route::Get('/login', "LoginController@login")->name('login_get');
    Route::Post('/login', "AuthController@login")->addOAIRequestBody($loginRequestBody)->addOAIResponse($response)->name('login_post');
    Route::Post('/register', "AuthController@register")->addOAIParameter($userCreateParameter)->addOAIResponse($response)->name('register_post');

})->name('api')->asApi()->addOAIResponse($response);

Route::Get('/test', "DefaultController@index");

Route::Post("/api/upload", "ApiController@index")->addOAIParameter($fileParameter)->name("api")->asApi();
Route::Get("/*", "ReactController@index")->name("react_route");