<?php 

require_once "../vendor/autoload.php";
require_once "./routes/api.php";

use App\Core\Core;
use App\Http\Route;

Core::dispatch(Route::getRoutes());


