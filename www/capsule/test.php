<?php include '../../Capsule/lib/Capsule/Capsule.php';
use Capsule\Capsule;

use Capsule\User\Auth;
use Capsule\Plugin\Storage\Storage;
use Capsule\File\Image\ImageInfo;


header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', true);
$app = Capsule::getInstance();
if (!Auth::getInstance()->currentUser) die('unauth'); ?>
<!DOCTYPE HTML>
<html>
<head>
<script src="/capsule/assets/share/jquery/jquery-2.0.3.min.js"></script>

</head>
<body>

<object id="supaApplet" width="2048" height="2048" type="application/x-java-applet">
    <param name="ClickForPaste" value="true">
    <param name="imagecodec" value="png">
    <param name="encoding" value="base64">
    <param name="previewscaler" value="original size">
    <param name="archive" value="/capsule/assets/share/supa/0.6a/lib/Supa.jar">
    <param name="classid" value="java:de.christophlinder.supa.SupaApplet.class">
    Applets disabled. Please enable applets.
</object>

</body>
</html>