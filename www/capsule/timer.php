<?php include '../../Capsule/lib/Capsule/Capsule.php';
use Capsule\Capsule;

use Capsule\User\Auth;
use Capsule\Plugin\Storage\Storage;
use Capsule\File\Image\ImageInfo;
use Capsule\App\Cms\Model\HistoryUploadImage;
use Capsule\Reference\Imaged;
use Capsule\Unit\DatedAdvanced;


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
<style>
body {
    font-family: sans-serif;
}
.uname {
    font-size: 200px;
    padding: 20px;
    border: 2px #000 solid;
    margin: 10px;
    display: none;
}
.active {
    font-weight: bold;
    color:gold;
    background: green;
    display: block;
    text-shadow: 2px 0 1px #000, 0 2px 1px #000, -2px 0 1px #000, 0 -2px 1px #000;
    border-radius: 4px;
}
.progress-border {
    height: 32px;
    border: 2px #000 solid;
    margin: 10px;
    overflow: hidden;
    border-radius: 4px;
}
.progress {
    background: green;
    width: 0%;
    height: 32px;
    overflow: hidden;
    white-space: nowrap;
    line-height: 31px;
    font-size: 24px;
    font-weight: bold;
    text-align: center;
    color: #fff;
    text-shadow: 2px 0 1px #000, 0 2px 1px #000, -2px 0 1px #000, 0 -2px 1px #000;
    border-right: 2px #000 solid;
    border-radius: 0 4px 4px 0;
}
</style>
</head>
<body>
<div id="kirill" class="uname active">
Кирилл
</div>
<div id="leva" class="uname">
Лёва
</div>
<div class="progress-border">
    <div class="progress"></div>
</div>
<script>
$(document).ready(function() {
    $('html, body').css({
        width: '100%',
        height: '100%'
    });
    var max = 5 * 60 * 1000; // interval
    var progress = $('.progress');
    var counter = function() {
        progress.css({
            width: 0
        });
        progress.animate({
                width: '100%'
            }, {
                duration: max,
                progress: function(animation, prog, remainingMs) {
                    progress.text(parseInt(100 * prog.toString(10), 10) + '%')
                },
                done: function() {
                    $('.uname').each(function(i, o) {
                        var o = $(o);
                        if (o.hasClass('active')) {
                            o.removeClass('active');
                        } else {
                            o.addClass('active');
                        }
                    });
                    counter();
                }
            }
        );
    };
    counter();
});
</script>

</body>
</html>