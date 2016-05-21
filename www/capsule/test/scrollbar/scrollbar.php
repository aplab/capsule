<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <link rel="stylesheet" href="/capsule/assets/cms/css/cssreset-min.css">
    <link rel="stylesheet" href="/capsule/assets/cms/font/opensans.css">
    <link rel="stylesheet" href="/capsule/test/scrollbar/CapsuleUiScrollable.css">
    <script src="/capsule/assets/share/jquery/jquery-2.0.3.min.js"></script>
    <script src="/capsule/test/scrollbar/CapsuleUiScrollable.js"></script>
</head>
<body>
<div id="main-container">
    <div class="capsule-ui-scrollable-wrapper" id="test-scrollable">
        <div class="capsule-ui-scrollable-container">
            <div class="capsule-ui-scrollable-content">
                <?php for($i = 1; $i < 15; $i++) : ?>
                asd<br>
                <?php endfor ?>
            </div>
        </div>
        <div class="capsule-ui-scrollable-scrollbar"><div>
    </div>
</div>
</body>
</html>