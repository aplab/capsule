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

<script>
var iframe = $('<iframe>');
iframe.attr({
    width: 400,
    height: 400
}).css({
    background: '#ccc'
});

$('body').append(iframe);

var HTML = "<html><head></head><body>";
HTML += "<u>Document</u> <b>HTML</b>";
HTML += "</body></html>";



iframe.document.open();
iframe.document.write(HTML);
iframe.document.close();

iframe.document.designMode='on';
</script>

</body>
</html>