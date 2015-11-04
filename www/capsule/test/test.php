<?php include '../../../Capsule/lib/Capsule/Capsule.php';
use Capsule\Capsule;
use Capsule\User\Auth;
use Capsule\Module\TreeMenu\Punct;
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

\Capsule\Tools\Tools::dump(\Capsule\DataObject\Mysql\DataObject::_configLocation());

$sql = '


/*[22:04:25][0 ms]*/ SELECT SCHEMA();
/*[22:04:25][0 ms]*/ EXPLAIN SELECT SCHEMA();
/*[22:04:25][0 ms]*/ SELECT SCHEMA();
/*[22:04:25][0 ms]*/ EXPLAIN SELECT SCHEMA();
/*[22:04:25][0 ms]*/ SELECT SCHEMA();
/*[22:04:25][0 ms]*/ EXPLAIN SELECT SCHEMA();
/*[22:28:53][0 ms]*/ SHOW CREATE TABLE beta_energoboard_ru.`capsule_user`;
/*[22:29:10][1 ms]*/ SHOW FULL FIELDS FROM `information_schema`.`TABLES`;
/*[22:29:10][0 ms]*/ SHOW KEYS FROM `information_schema`.`TABLES`;
/*[22:29:10][0 ms]*/ SHOW CREATE TABLE `information_schema`.`TABLES`;
/*[22:29:34][1 ms]*/ SHOW FULL FIELDS FROM `information_schema`.`TABLES`;
/*[22:29:42][0 ms]*/ SHOW CREATE TABLE beta_energoboard_ru.`capsule_user`;
/*[22:30:03][1 ms]*/ SHOW FULL FIELDS FROM `beta_energoboard_ru`.`energoboard_module_user_user`;
/*[22:30:03][3 ms]*/ SHOW KEYS FROM `beta_energoboard_ru`.`energoboard_module_user_user`;
/*[22:30:03][0 ms]*/ SHOW CREATE TABLE `beta_energoboard_ru`.`energoboard_module_user_user`;
/*[22:30:03][0 ms]*/ USE `beta_energoboard_ru`;


SELECT * FROM `u6291_eb`.`cs_linear_comment` WHERE `comment` LIKE "%<a %";
DELETE FROM `u6291_eb`.`cs_linear_comment` WHERE `comment` LIKE "%<a %";
SELECT * FROM `u6291_ot`.`cs_linear_comment` WHERE `comment` LIKE "%<a %";
DELETE FROM `u6291_ot`.`cs_linear_comment` WHERE `comment` LIKE "%<a %";

';

\Capsule\Tools\Tools::dump(\Capsule\Db\Db::getInstance()->splitMultiQuery($sql));

?>
</body>
</html>