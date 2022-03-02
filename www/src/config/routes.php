<?php

use Core\Http\Route;

Route::Group('/api', null, function () {
    Route::Group('/admin', null, function(){
        Route::Get('/dashboard', "DefaultController@admin");
        Route::Get('/student', "DefaultController@admin");
    });
    Route::Post('/login', "LoginController@login")->name('app_login');
});
Route::Get("/*", "ReactController@index")->name("react_route");
