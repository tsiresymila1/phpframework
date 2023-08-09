<?php

use App\Middleware\AuthMiddleware;
use Core\Http\Request;
use Core\Http\Response;
use Core\Http\Route;
use Core\OpenAPI\OAIParameter;
use Core\OpenAPI\OAIRequestBody;
use Core\OpenAPI\OAIRequestBodyContent;
use Core\OpenAPI\OAIResponse;
use Core\OpenAPI\OAISecurity;
use Core\OpenAPI\SwaggerUI;

//OPENAPI
$response = new OAIResponse();
$bearerSecurity = new OAISecurity("Bearer", "http", null, null, "header", "bearer", "Enter JWT token", "JWT");
$loginRequestBody = new OAIRequestBody(new OAIRequestBodyContent('application/json', "AuthDto"), true);
$registerRequestBody = new OAIRequestBody(new OAIRequestBodyContent('multipart/form-data', "RegisterDto"), true);
$fileUploadRequestBody = new OAIRequestBody(new OAIRequestBodyContent('multipart/form-data', "FileUploadDto"), true);

// END OPENAPI

Route::Get('/test', 'DefaultController@index')->name('test');

Route::Get(['/function/{id}/get/{path?}', '/get/{id?}'], function (Request $request, $id, $path, Response $response) {
    return $response::Json(array('data' => $id . '::test function ->' . $path . '->' . $request->input('key')));
})->addOAIResponse($response)->middleware(function (Request $request, $id) {
    $request->setRequestData('key', 'hello' . $id);
})->middleware(AuthMiddleware::class)->asApi();

Route::Group('/api', function () use ($loginRequestBody, $response, $registerRequestBody, $bearerSecurity) {

    // render swagger 
    Route::Get('/docs', function (Response $response) {
        setHeader('Content-type', 'text/html');
        return $response->Send(SwaggerUI::renderer());
    })->name('swagger')->asApi(false);

    Route::Group('/admin', function () {
        Route::Get('/dashboard', "DefaultController@admin");
        Route::Get('/student', "DefaultController@admin");
    })->name('admin')->asApi()->addOAISecurity([$bearerSecurity]);
    //Route::Get('/login', "LoginController@login")->name('login_get');
    Route::Post('/login', "AuthController@login")->addOAIRequestBody($loginRequestBody)->addOAIResponse($response)->name('login_post');
    Route::Post('/register', "AuthController@register")->addOAIRequestBody($registerRequestBody)->addOAIResponse($response)->name('register_post');

})->name('api')->asApi()->addOAIResponse($response);

Route::Get('/test', "DefaultController@index");

Route::Post("/api/upload", "ApiController@index")->addOAIRequestBody($fileUploadRequestBody)->name("api")->asApi();
Route::Get("/*", "ReactController@index")->name("react_route");