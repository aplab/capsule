<?php include '../Capsule/lib/Capsule/Capsule.php';
use Capsule\Capsule;
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', true);
Capsule::getInstance()->run();
#test