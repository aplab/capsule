<?php include '../../Capsule/lib/Capsule/Capsule.php';
use Capsule\Capsule;

use Capsule\User\Auth;
use Capsule\Tools\Mysql;
use Capsule\Loader\GeSHi;
use Usr\Aplab\Model\DevLog;
use Capsule\Plugin\Storage\Storage;
use Capsule\Common\Path;


header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', true);
$app = Capsule::getInstance();
if (!Auth::getInstance()->currentUser) die; ?>
<!DOCTYPE HTML>
<html>
<head>
<script src="/capsule/assets/share/jquery/jquery-2.0.3.min.js"></script>
</head>
<body>
<?php 


\Capsule\Tools\Tools::dump(parse_url('//a.b/home/news/2345.php?id=1&jaja=456#45'));









?>
</body>
</html>