<?php

use Core\Http\Route;

// Route::Get("/", "DefaultController@index")->name('home');
Route::Any("/admin/{hello}/{id}/{key?}", "DefaultController@hello")->name('admin_hello');
Route::Get("/admin", "DefaultController@admin")->name('admin');
Route::Any("/login", "LoginController@login")->name('app_login');;
Route::Get("/teste", "DefaultController@json");
Route::Group('/api', null, function () {
       return [
              Route::Get('/login', "LoginController@login"),
              Route::Get('/admin', "DefaultController@admin")
       ];
});
Route::Get("/{react?}", "ReactController@index")->name("react_route");
