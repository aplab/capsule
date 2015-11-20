<?php include '../../../Capsule/lib/Capsule/Capsule.php';
use Capsule\Capsule;
use Capsule\User\Auth;
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', true);
$app = Capsule::getInstance();
if (!Auth::getInstance()->currentUser) die('unauth'); ?>
<!DOCTYPE HTML>
<html>
<head>
<link media="all" href="/capsule/assets/cms/css/cssreset-min.css" rel="stylesheet">
<link media="all" href="/capsule/assets/share/jquery-ui/jquery-ui-1.11.1.custom/jquery-ui.min.css" rel="stylesheet">
<link media="all" href="/capsule/assets/share/jquery-ui/jquery-ui-1.11.1.custom/jquery-ui.structure.min.css" rel="stylesheet">
<link media="all" href="/capsule/assets/share/jquery-ui/jquery-ui-1.11.1.custom/jquery-ui.theme.min.css" rel="stylesheet">
<link media="all" href="/capsule/Ui/Dialog/cms/CapsuleUiDialog.css" rel="stylesheet">
<script src="/capsule/assets/cms/js/Capsule.js"></script>
<script src="/capsule/assets/share/jquery/jquery-2.0.3.min.js"></script>
<script src="/capsule/assets/share/jquery-ui/jquery-ui-1.11.1.custom/jquery-ui.min.js"></script>
<script src="/capsule/Ui/Dialog/cms/CapsuleUiDialog.js"></script>
</head>
<body>

<script>
$(document).ready(function() {
    $('html, body').css({
        width: '100%',
        height: '100%'
    });
//     $('input').click(function() {
//         CapsuleUiDialog.getInstance('testwindow').center().setFocus();
//     });
//     new CapsuleUiDialog({
//         instanceName: 'testwindow',
//         hidden: false,
//         title: 'Новое окно',
//         width: 200,
//         height: 200,
//         contentType: 'iframe',
//         iframeSrc: '/admin/uploadimagehistory/',
//         opacity: .9
//     });
//     $('#d1').clone(1,1).appendTo($('#d2'));
//     new CapsuleUiDialog({
//         instanceName: 'testwindow1',
//         resizable: 0,
//         left: 200,
//         top: 400
//     });
//     new CapsuleUiDialog({
//         instanceName: 'testwindow2'
//     });
});
</script>
<?php

class SomeTest extends \Capsule\Unit\UnitTs
{

}

\Capsule\Tools\Tools::dump(SomeTest::_configDataJson());

#\Capsule\Tools\Tools::dump(\Capsule\Unit\UnitTs::_configSetEditMode());
#\Capsule\Tools\Tools::dump(\Capsule\Unit\UnitTs::_configApply());

//\Capsule\Tools\Tools::dump($app->worktime);


?>
</body>
</html>