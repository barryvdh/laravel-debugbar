<?php namespace Barryvdh\Debugbar\Controllers;

if(class_exists('Illuminate\Routing\Controller')){
    // Laravel 4.1+ Controller
    class BaseController extends \Illuminate\Routing\Controller{}
}else{
    // Laravel 4.0 Controller
    class BaseController extends \Illuminate\Routing\Controllers\Controller{}
}
