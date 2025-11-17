<?php 

require_once "../vendor/autoload.php";
require_once "./Routes/api.php";
require_once "../config.php";

use App\Core\Core;
use App\Http\Route;

Core::dispatch(Route::getRoutes());


