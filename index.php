<?php
/**
 * Created by PhpStorm.
 * User: lunguandrei
 * Date: 06.08.17
 * Time: 16:32
 */

define('PROJECT_PATH', __DIR__);
use App\ParkingProcessor;
require_once("Autoloader.php");
try {
    $classLoader = new AutoLoader('App');
    $classLoader->register();

    $parkingProcessor = new ParkingProcessor();
    $parkingProcessor->init();
} catch (\Exception $e) {
    echo "PHP Fatal error: " . $e->getMessage(), "\n";
}