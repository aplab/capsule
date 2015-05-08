<?php include '../../../Capsule/lib/Capsule/Capsule.php';
use Capsule\Capsule;
use Capsule\Unit\Nested\Tree\Item;
use Capsule\User\Auth;
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', true);
$app = Capsule::getInstance();
if (!Auth::getInstance()->currentUser) die;
?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>
Capsule 1.0.0 Alpha 6
</title>
<style>
* {
    font-size: 11px;
    font-family: "Segoe UI", Tahoma, "DejaVu Sans", sans-serif;
}
h1 { 
    font-size: 22px; 
} 
textarea {
    display: block;
    width: 99%;
}
</style>
</head>
<body id="capsule-cms-body">
<h1>Преобразователь изображений и др. в base64</h1>
<div>
<textarea rows="40"><?php 
$a = function() {
    if (!isset($_FILES['file'])) return;
    $keys = array('name', 'type', 'tmp_name','error','size');
    $file = $_FILES['file'];
    foreach ($keys as $key) {
        if (!isset($file[$key])) return;
        if (!is_scalar($file[$key])) return;
        $$key = $file[$key];
    }
    if ($error) return;
    $data = file_get_contents($tmp_name);
    $base64 = 'data:' . $type . ';base64,' . base64_encode($data);
    echo 'url("' . $base64 . '");';
};
$a(); ?></textarea>
</div>
<div>
<form method="post" enctype="multipart/form-data">
<input type="file" name="file"><input type="submit">
</form>
</div>
</body>
</html>