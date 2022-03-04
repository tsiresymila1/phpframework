<?php
use Core\Http\Request;
use Core\Http\Response;
use Core\Http\Route;
use Core\OpenAPI\OAIParameter;
use Core\OpenAPI\OAIResponse;
use Core\OpenAPI\SwaggerUI;

//OPENAPI
$response = new OAIResponse();
$secParameter = new OAIParameter('authorization', 'header','Authorization key',true);
$userParameter = new OAIParameter('username', 'formData','Username',true);
$passParameter = new OAIParameter('password', 'formData','Password',true);
//


Route::Get(['/function/{id}/get/{path?}', '/get/{id?}'],function(Request $request, $id, $path,Response $response){
    return $response::Json(array('data'=> $id.'::test function ->'.$path.'->'.$request->input('key')));
},function(Request $request, $id){
    $request->setRequestData('key','hello'.$id);
}, false)->addResponse($response);

Route::Group('/api', null, function () use ($userParameter, $passParameter, $response, $secParameter){
    Route::Get('/docs',function (Response $response){
        $response::AddHeader('Content-type','text/html');
        $content = SwaggerUI::renderer('https://petstore.swagger.io/v2/swagger.json');
        return $response::Send($content);
    },null,false)->name('swagger');
    Route::Group('/admin', null, function() use ($response, $secParameter) {
        Route::Get('/dashboard', "DefaultController@admin");
        Route::Get('/student', "DefaultController@admin");
    })->addParameter($secParameter)->name('admin');
    Route::Get('/login', "LoginController@login")->name('login_get');
    Route::Post('/login', "LoginController@login")->addParameter([$userParameter,$passParameter])->name('login_post');
},true)->name('api')->addResponse($response);

Route::Get("/*", "ReactController@index")->name("react_route")->addParameter($secParameter)->addResponse($response);
