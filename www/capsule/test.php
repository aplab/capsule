<?php include '../../Capsule/lib/Capsule/Capsule.php';
use Capsule\Capsule;

use Capsule\User\Auth;
use Capsule\Plugin\Storage\Storage;
use Capsule\File\Image\ImageInfo;
use Capsule\App\Cms\Model\HistoryUploadImage;


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
<?php 
Storage::getInstance()->delFile('ce17be480dafed95bc4a422a20ab1551.jpg');
?>
</body>
</html>