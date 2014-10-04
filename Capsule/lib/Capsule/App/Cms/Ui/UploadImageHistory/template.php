<?php
use Capsule\Common\TplVar;
$tplvar = TplVar::getInstance();
$in = $tplvar->instanceName;
?><div class="capsule-ui-upload-image-history" id="<?=$in?>">
    <div class="workplace" id="<?=$in?>-workplace">
    <?php ob_start() ?>
    <div class="item"><div class="img"><img src="/capsule/storage/files/22a/ab4/571/22aab457137b6b64d56f56d4cdab2bd7.gif"><div class="helper"></div></div><div class="comment">filename.jpg<br>filename.jpg</div></div>
    <div class="item"><div class="img"><img src="/capsule/storage/files/22a/ab4/571/22aab457137b6b64d56f56d4cdab2bd7.gif"><div class="helper"></div></div><div class="comment">filename.jpg</div></div>
    <div class="item"><div class="img"><img src="/capsule/storage/files/7db/df6/fb8/7dbdf6fb8c1a5b197d4da2126ed4a0c4.jpg"><div class="helper"></div></div><div class="comment">filename.jpg</div></div>
    <div class="item"><div class="img"><img src="/capsule/storage/files/7db/df6/fb8/7dbdf6fb8c1a5b197d4da2126ed4a0c4.jpg"><div class="helper"></div></div><div class="comment">filename.jpg</div></div>
    <?=str_repeat(ob_get_clean(), 250)?></div>
</div>