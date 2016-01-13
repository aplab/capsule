<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>
            CapsuleSysControls
        </title>
        <link rel="StyleSheet" href="/capsule/assets/cms/css/cssreset-min.css" type="text/css" media="all" />
        <link rel="StyleSheet" href="control.css" type="text/css" media="all" />
        <script type="text/javascript" src="control.js"></script>
    </head>
    <body style="padding: 20px; background: #f2f1f0;">
        <div class="container">
            Файл
            <div class="capsule-cms-control-choose-file">
                <span></span><div>Обзор...</div>
                <input type="file" onchange="CapsuleCmsControlChooseFile(this)" size="1" name="photo[1]" />
            </div>
            Файл без поля
            <div class="capsule-cms-control-choose-file-button">
                <div>Обзор...</div>
                <input type="file" size="1" name="photo[1]" />
            </div>
            select
            <div class="capsule-cms-control-select">
                <div>
                    <select>
                        <option>rty1234512341234</option>
                        <option>1234512341234</option>
                        <option>1234rty512341234</option>
                        <option>12rty34512341234</option>
                        <option>------------1234512341rtyu5rfg,knhjdfgjklhndfkl;nhd;flhty234-----------</option>
                    </select>
                </div>
            </div>
            кнопка
            <div class="capsule-cms-control-button">
                <span>Кнопка</span>
                <button type="reset">&nbsp;</button>
            </div>
            ссылка как кнопка
            <a class="capsule-cms-control-link" href=""><span>текст</span></a>
            текст
            <div class="capsule-cms-control-text">
                <input type="text" value="Текст Проверка" />
            </div>
            textarea
            <div class="capsule-cms-control-textarea">
                <textarea>Textarea Проверка</textarea>
            </div>
            checkbox
            <div class="capsule-cms-control-checkbox ">
                <input type="checkbox" />
            </div>
            radio
            <div class="capsule-cms-control-radio">
                <input name="r1" type="radio" />
            </div>
            radio
            <div class="capsule-cms-control-radio">
                <input name="r1" type="radio" />
            </div>
            label
            <div class="capsule-cms-control-label">
                Label-text
            </div>
            label text-aligh right
            <div class="capsule-cms-control-label-r">
                Label-text
            </div>
        </div>
    </body>
</html>