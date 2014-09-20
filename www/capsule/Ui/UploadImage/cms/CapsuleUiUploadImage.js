/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 13.09.2014 6:55:12 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */
function CapsuleUiUploadImage(data) 
{
    /**
     * instance name
     * 
     * @var string
     */
    this.instanceName = data.instance_name;
    
    /**
     * ссылка на сам объект для передачи контекста
     */
    var widget = this;
    
    /**
     * static init
     * 
     * @param self o same object
     * @param c same function
     */
    (function(o, c) {
        var i = o.instanceName;
        if ('undefined' === typeof(c.instances)) {
            c.instances = new Array();
        }
        c.getInstance = function(instance_name) {
            if ('undefined' !== typeof(c.instances[instance_name])) {
                return c.instances[instance_name];
            }
            return null;
        }
        if ('undefined' !== typeof(c.instances[i])) {
            console.log('Instance already exists: ' + i);
            console.error('Instance already exists: ' + i);
            throw new Error('Instance already exists: ' + i);
        }
        c.instances[i] = o;
    })(this, arguments.callee);
    
    /**
     * console log wrapper
     * 
     * @param mixed foo
     * @return void
     */
    var cl = function(foo) {
        console.log(foo);
    }
    
    /**
     * Проверяет входной параметр, который может содержать простые 
     * арифметические операции. Максимальная длина выражения 15 символов
     * 
     * @param string
     * @return object
     */
    this.checkInputDigit = function(val) {
        var reg = /^[0-9+\/\*-]{0,14}\d$/;
        var ret = new Object({
            value: null,
            valid: false
        });
        if (reg.test(val)) {
            val = eval(val + '');
        } else {
            return ret;
        }
        var reg = /^\d{1,4}$/;
        if (reg.test(val)) {
            ret.value = val;
            ret.valid = true;
        }
        return ret;
    }
    
    /**
     * Составной id. Добавляет # и instanceName
     * для уменьшения длины записи при использованиив селекторах jquery
     * 
     * @param string
     * @return string
     */
    this.id = function(id) {
        id = '#' + this.instanceName + '-' + id;
        return id;
    }
    
    /**
     * declaration
     * не буду расписывать, названия сами за себя говорят
     */
    this.container = $('#' + this.instanceName);
    this.container.css({top: data.top + 'px'});
    this.imagePlace = $(this.id('workplace')).empty();
    this.infoBar = $(this.id('info-bar'));
    this.resizeBar = $(this.id('resize-bar'));
    this.cropBar = $(this.id('crop-bar'));
    
    this.form = $(this.id('form'));
    this.formFile = this.form.find(':file').val(null);
    this.formWidth = $(this.id('width'));
    this.formHeight = $(this.id('height'));
    this.formX1 = $(this.id('x1'));
    this.formY1 = $(this.id('y1'));
    this.formX2 = $(this.id('x2'));
    this.formY2 = $(this.id('y2'));
    this.formImageString = $(this.id('image-string'));
    
    this.inputFilename = $(this.id('filename')).val(null);
    this.inputWidth = $(this.id('input-width')).val(null);
    this.inputHeight = $(this.id('input-height')).val(null);
    this.inputCropWidth = $(this.id('input-crop-width')).val(null);
    this.inputCropHeight = $(this.id('input-crop-height')).val(null);
    this.inputCropX1 = $(this.id('input-crop-x1')).val(null);
    this.inputCropY1 = $(this.id('input-crop-y1')).val(null);
    this.inputCropX2 = $(this.id('input-crop-x2')).val(null);
    this.inputCropY2 = $(this.id('input-crop-y2')).val(null);
    
    this.inputFilename.change(function() {
        Capsule.setSelection(this);
    }).click(function() {
        Capsule.setSelection(this);
    }).focus(function() {
        Capsule.setSelection(this);
    }).attr({
        readonly: true
    });
    
    /**
     * Обработчик нажатия enter поля ввода ширины изображения в плагине resize
     * 
     * @param void
     * @return void
     */
    this.inputWidth.keyup(function(e) {
        if (e.keyCode == 13) {
            this.blur();
        }
    });
    
    /**
     * Обработчик потери фокуса поля ввода ширины изображения в плагине resize
     * 
     * @param void
     * @return void
     */
    this.inputWidth.blur(function() {
        var o = $(this);
        var value = $(this).val();
        var check = widget.checkInputDigit(value);
        if (!check.valid) {
            $(this).val(widget.imageResizeWidth);
            return;
        }
        $(this).val(check.value);
        widget.detachResizable();
        widget.image.css({
            width: check.value,
            height: 'auto'
        });
        widget.attachResizable();
    });
    
    /**
     * Обработчик нажатия enter
     */
    this.inputHeight.keyup(function(e) {
        if (e.keyCode == 13) {
            this.blur();
        }
    });
    
    /**
     * Обработчик потери фокуса
     */
    this.inputHeight.blur(function() {
        var o = $(this);
        var value = $(this).val();
        var check = widget.checkInputDigit(value);
        if (!check.valid) {
            $(this).val(widget.imageResizeHeight);
            return;
        }
        $(this).val(check.value);
        widget.detachResizable();
        widget.image.css({
            width: 'auto',
            height: check.value
        });
        widget.attachResizable();
    });
    
    /**
     * Начальная инициализация
     * 
     * @param void
     * @return void
     */
    this.init = function() {
        this.imageOriginalFilename = null;
        
        this.imageOriginalWidth = null;
        this.imageOriginalHeight = null;
        
        this.imageResizeWidth = null;
        this.imageResizeHeight = null;
        
        this.imageCropWidth = null;
        this.imageCropHeight = null;
        
        this.imageCropX1 = null;
        this.imageCropY1 = null;
        
        this.imageCropX2 = null;
        this.imageCropY2 = null;
        
        this.image = null;
        this.imageWrapper = null;
        
        // applet object
        this.applet = null;
        
        // paste in progress (вставка из буфера обмена в процессе)
        this.pasteInProgress = null;
        
        // строковое представление изображения из буфера обмена
        this.imageString = '';
    }
    
    this.init(); // сразу инициализируем
    
    /**
     * Возвращает есть ли картинка 
     */
    this.hasImage = function() {
        return !!this.imagePlace.find('img').length;
    }
    
    /**
     * Вычисляет локальное имя выбранного файла
     * 
     * @param void
     * @return string
     */
    this.getLocalFilename = function() {
        var value = this.formFile.val();
        var splitA = value.split('\\');
        var splitB = value.split('/');
        if (splitA.length > splitB.length) {
            return splitA[splitA.length-1];
        } else {
            return splitB[splitB.length-1];
        }
    }
    
    /**
     * Обработать если выбран файл
     */
    this.formFile.change(function() {
        widget.retrieveImage();
    });
    
    /**
     * Получение изображения из поля ввода формы
     * 
     * @param void
     * @return void
     */
    this.retrieveImage = function() {
        var input = this.formFile.get(0);
        if (input.files && input.files[0]) {
            if (input.files[0].type.match('image.*')) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var w = widget;
                    var p = w.imagePlace;
                    w.imageWrapper = $(document.createElement('div')).addClass('image-wrapper');
                    w.image = $(new Image()).addClass('image');
                    var wr = w.imageWrapper;
                    var i = w.image;
                    p.empty();// очистка image place
                    i.load(function() {
                        wr.append(i);
                        p.append(wr);
                        w.imageOriginalFilename = w.getLocalFilename();
                        w.imageOriginalWidth = i.width();
                        w.imageOriginalHeight = i.height();
                        w.inputFilename.val(w.imageOriginalFilename); 
                        w.attachDraggable();
                        w.imageString = '';
                    });
                    i.attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
                reader = null;
            } else {
                this.reset();
                alert('is not image mime type');
            }
        } else {
            alert('not isset files data or files API not supordet');
        }
    }
    
    /**
     * Открыть диалоговое окно выбора одного файла
     */
    this.selectFile = function() {
        this.formFile.click();
    }
    
    /**
     * включить режим resize
     */
    this.attachResizable = function() {
        if (!this.hasImage()) {
            console.log('picture not found');
            return;
        }
        this.cropModeOff();
        this.resizeBar.show();
        this.image.resizable({
            aspectRatio: true,
            create: function(event, ui) {
                widget.setWidth();
                widget.setHeight();
            },
            resize: function(event, ui) {
                widget.setWidth();
                widget.setHeight();
            }
        });
    }
    
    /**
     * выключить режим resize
     */
    this.detachResizable = function() {
        this.resizeBar.hide();
        if (this.isResizable()) {
            this.image.resizable('destroy');
        }
    }
    
    /**
     * изменить состояние режима resize на противоположное
     * 
     * @param void
     * @return void
     */
    this.toggleResizable = function() {
        if (this.isResizable()) {
            this.detachResizable();
        } else {
            this.attachResizable();
        }
    }
    
    /**
     * Определяет, включен ли плагин resizable
     * 
     * @param void
     * @return boolean
     */
    this.isResizable = function() {
        if (!this.hasImage()) {
            return false;
        }
        return !('undefined' == typeof(this.image.resizable('instance')));
    }
    
    /**
     * 
     */
    this.setWidth = function() {
        this.imageResizeWidth = this.image.width();
        this.inputWidth.val(this.imageResizeWidth);
    }
    
    /**
     * Resize callback
     * 
     * @param void
     * @return void
     */
    this.setHeight = function() {
        this.imageResizeHeight = this.image.height();
        this.inputHeight.val(this.imageResizeHeight);
    }
    
    /**
     * Включает перетаскивание
     * 
     * @param void
     * @return void
     */
    this.attachDraggable = function() {
        if (!this.isDraggable()) {
            this.imageWrapper.draggable();
        }
    }
    
    /**
     * Выключает перетаскивание
     * 
     * @param void
     * @return void
     */
    this.detachDraggable = function() {
        if (this.isDraggable()) {
            this.imageWrapper.draggable('destroy');
        }
    }
    
    /**
     * Определяет, включен ли плагин draggable
     * 
     * @param void
     * @return boolean
     */
    this.isDraggable = function() {
        if (!this.hasImage()) {
            return false;
        }
        return !!this.imageWrapper.draggable('instance')
    }
    
    /**
     * отменить все изменения картинки и вернуть исходное состояние как
     * после вставки / загрузки
     * 
     * @param void
     * @return void
     */
    this.resetImage = function() {
        if (!this.hasImage()) {
            return;
        }
        this.detachDraggable();
        this.detachResizable();
        this.cropModeOff();
        this.imageWrapper.css({
            top: 0,
            left: 0
        });
        this.image.css({
            width: this.imageOriginalWidth,
            height: this.imageOriginalHeight
        });
        this.attachDraggable();
    }
    
    /**
     * Вернуть первоначально есостояние, удалить картинку
     * 
     * @param void
     * @return void
     */
    this.reset = function() {
        this.resetForm();
        this.detachDraggable();
        this.detachResizable();
        this.cropModeOff();
        this.imagePlace.empty();
        this.inputFilename.val(null);
    }
    
    /**
     * Create applet
     * 
     * @param void
     * @return void
     */
    this.createApplet = function() {
        var applet = document.createElement('object');
        $(applet).attr({
            id: "supaApplet", 
            width: 2048, 
            height: 2048, 
            type: "application/x-java-applet" 
        });
        var param = new Object({
            ClickForPaste: 'true',
            imagecodec: 'png',
            encoding: 'base64',
            previewscaler: 'original size',
            classid: 'java:de.christophlinder.supa.SupaApplet.class',
            archive: '/capsule/assets/share/supa/0.6a/lib/Supa.jar',
            java_arguments: '-Xmx512m',
            trace: true
        });
        for (var p in param) {
            $(applet).append($(document.createElement('param')).attr({
                name: p,
                value: param[p]
            }));
        }
        this.form.closest('div').append(applet);
        widget.applet = document.getElementById('supaApplet');
    }
    
    /**
     * Destroy applet
     * 
     * @param void
     * @return void
     */
    this.destroyApplet = function() {
        this.form.closest('div').find('object').remove();
        this.applet = null;
    }
    
    /**
     * paste from clipboard key handler
     * 
     * @param void
     * @return void
     */
    this.pasteClipboard = function() {
        if (this.pasteInProgress) {
            console.log('paste in progress');
            return;
        }
        this.pasteInProgress = true;
        if (this.ping()) {
            // апплет пингуется, вставку делаем
            this._pasteImage();
            return;
        }
        // иначе надо создать апплет
        this.createApplet();
        var interval = setInterval(function() {
            // пингуем 10 раз в секунду
            if (widget.ping()) {
                // апплет начал пинговаться, удаляем интервал в первую очередь
                // иначе при краше апплета у нас будет пинговать и делать непонятно что %)
                clearInterval(interval);
                // потом запускаем вставку.
                widget._pasteImage();
            }
        }, 100);
    }
    
    /**
     * Real paste image
     * 
     * @param void
     * @return boolean
     */
    this._pasteImage = function() {
        widget.applet.start();
        widget.applet.clear();
        try { 
            var err = this.applet.pasteFromClipboard();
            switch(err) {
                case 0: 
                    /* no error */
                    break;
                case 1: 
                    window.alert('Unknown Error');
                    break;
                case 2:
                    window.alert('Empty clipboard');
                    break;
                case 3:
                    window.alert('Clipboard content not supported. Only image data is supported.');
                    break;
                case 4:
                    window.alert('Clipboard in use by another application. Please try again in a few seconds.');
                    break;
                default:
                    window.alert('Unknown error code: ' + err );
            }
            if (err) {
                this.pasteInProgress = null;// сброс флага
                return false;
            }
        } catch (ex) {
            if(typeof ex == 'object') {
                alert('Internal exception: ' + ex.toString() );
            } else {
                alert('Internal exception: ' + ex );
            }
            this.pasteInProgress = null;// сброс флага
            // applet crashed
            this.destroyApplet();
            return false;
        }
        try {
            var imgstr = widget.applet.getEncodedString();
            widget.applet.clear();
        } catch (ex) {
            if(typeof ex == 'object') {
                alert('Internal exception: ' + ex.toString() );
            } else {
                alert('Internal exception: ' + ex );
            }
            this.pasteInProgress = null;// сброс флага
            // applet crashed
            this.destroyApplet();
            return false;
        }
        var w = widget;
        var p = w.imagePlace;
        w.imageWrapper = $(document.createElement('div')).addClass('image-wrapper');
        w.image = $(new Image()).addClass('image');
        var wr = w.imageWrapper;
        var i = w.image;
        p.empty();
        i.load(function() {
            wr.append(i);
            p.append(wr);
            w.imageOriginalFilename = w.getLocalFilename(); 
            w.imageOriginalWidth = i.width();
            w.imageOriginalHeight = i.height();
            w.inputFilename.val('clipboard.png'); 
            w.attachDraggable();
        });
        i.attr('src', 'data:image/png;base64,' + imgstr);
        this.pasteInProgress = null;// сброс флага
        this.resetForm();
        this.imageString = imgstr;
        return true;
    }
    
    /**
     * Сброс формы, например если там выбран файл
     * 
     * @param void
     * @return void
     */
    this.resetForm = function() {
        this.form.get(0).reset();
    }
    
    /**
     * Ping applet
     * 
     * @param void
     * @return boolean
     */
    this.ping = function() {
        if (!this.applet) {
            return false;
        }
        if ('undefined' == this.applet.ping) {
            return false;
        }
        try {
            // IE will throw an exception if you try to access the method in a 
            // scalar context, i.e. if( supaApplet.pasteFromClipboard ) ...
            return this.applet.ping();
        } catch(e) {
            return false;
        }
    }
    
    /**
     * Переключить режим crop
     * 
     * @param void
     * @return void
     */
    this.toggleCrop = function() {
        if (this.isCrop()) {
            this.cropModeOff();
        } else {
            this.cropModeOn();
        }
    }
    
    /**
     * Включить режим crop
     * 
     * @param void
     * @return void
     */
    this.cropModeOn = function() {
        if (this.hasImage()) {
            this.detachResizable();
            this.image.imgAreaSelect({
                handles: true,
                parent: widget.imagePlace,
                persistent: true,
                x1: 0,
                y1: 0,
                x2: widget.image.width() - 1,
                y2: widget.image.height() - 1,
                show: true,
                minWidth: 1,
                minHeight: 1,
                maxWidth: widget.image.width(),
                maxHeight: widget.image.height(),
                onSelectStart: function(img, selection) {
                    widget.cropCallback(selection);
                },
                onSelectChange: function(img, selection) {
                    widget.cropCallback(selection);
                },
                onSelectEnd: function(img, selection) {
                    widget.cropCallback(selection);
                },
                onInit: function(img, selection) {
                    widget.cropCallback(selection);
                }
            });
            this.cropBar.show();
        }
    }
    
    /**
     * Выключить режим crop
     * 
     * @param void
     * @return void
     */
    this.cropModeOff = function() {
        if (!this.hasImage()) {
            return;
        }
        if (!this.isCrop()) {
            return;
        }
        this.image.imgAreaSelect({
            disable: true
        });
        this.image.imgAreaSelect({
            remove: true
        });
        this.cropBar.hide();
        this.imagePlace.mouseup();
    }
    
    /**
     * Определяет, включен ли плагин crop
     * 
     * @param void
     * @return boolean
     */
    this.isCrop = function() {
        return !!this.imagePlace.find('[class^=\'imgareaselect\']').length;
    }
    
    /**
     * При выделении области или изменении выделения
     * 
     * @param selection
     */
    this.updateCropParameters = function(selection) {
        this.imageCropHeight = selection.height;
        this.inputCropHeight.val(this.imageCropHeight);
        
        this.imageCropWidth = selection.width;
        this.inputCropWidth.val(this.imageCropWidth);
        
        this.imageCropX1 = selection.x1;
        this.inputCropX1.val(this.imageCropX1);
        
        this.imageCropY1 = selection.y1;
        this.inputCropY1.val(this.imageCropY1);
        
        this.imageCropX2 = selection.x2;
        this.inputCropX2.val(this.imageCropX2);
        
        this.imageCropY2 = selection.y2;
        this.inputCropY2.val(this.imageCropY2);
    }
    
    /**
     * При выделении области или изменении выделения
     * 
     * @param selection
     * @return void
     */
    this.cropCallback = function(selection) {
        var need_adjust = false;
        var xMax = widget.image.width() - 1;
        var yMax = widget.image.height() - 1;
        var x1 = selection.x1;
        var x2 = selection.x2;
        var y1 = selection.y1;
        var y2 = selection.y2;
        if (x1 < 0) {
            x1 = 0;
            need_adjust = true;
        }
        if (y1 < 0) {
            y1 = 0;
            need_adjust = true;
        }
        if (x2 < 0) {
            x2 = 0;
            need_adjust = true;
        }
        if (y2 < 0) {
            y2 = 0;
            need_adjust = true;
        }
        if (x1 > xMax) {
            x1 = xMax;
            need_adjust = true;
        }
        if (y1 > yMax) {
            y1 = yMax;
            need_adjust = true;
        }
        if (x2 > xMax) {
            x2 = xMax;
            need_adjust = true;
        }
        if (y2 > yMax) {
            y2 = yMax;
            need_adjust = true;
        }
        if (x1 == x2) {
            if (x2 == xMax) {
                x1 = xMax - 1;
                x2 = xMax;
            } else if (x2 == 0) {
                x1 = 0
                x2 = 1;
            } else {
                x2 = x1 + 1;
            }
            need_adjust = true;
        }
        if (y1 == y2) {
            if (y1 == yMax) {
                y1 = yMax - 1;
                y2 = yMax;
            } else if (y1 == 0) {
                y1 = 0;
                y2 = 1;
            } else {
                y2 = y1 + 1;
            }
            need_adjust = true;
        }
        if (need_adjust) {
            console.log('adjust');
            this.setSelection(x1, y1, x2, y2);
        }
        this.updateCropParameters(selection);
    }
    
    /**
     * Принудительно задать выделение crop
     */
    this.setSelection = function(x1, y1, x2, y2) {
        var ias = this.image.imgAreaSelect({
            instance: true
        });
        ias.setSelection(x1, y1, x2, y2);
        ias.update(true);
        this.updateCropParameters(ias.getSelection());
    }
    
    /**
     * Возвращает объект selection
     */
    this.getSelection = function() {
        /**
         * @TODO возможно, надо добавить проверки на существование crop
         */
        var ias = this.image.imgAreaSelect({
            instance: true
        });
        return ias.getSelection();
    }
    
    /**
     * Обработчик нажатия enter
     */
    this.inputCropX1.keyup(function(e) {
        if (e.keyCode == 13) {
            this.blur();
        }
    });
    
    /**
     * Обработчик потери фокуса
     */
    this.inputCropX1.blur(function() {
        var o = $(this);
        var value = $(this).val();
        var check = widget.checkInputDigit(value);
        if (!check.valid) {
            $(this).val(widget.imageCropX1);
            return;
        }
        value = check.value;
        var xMax = widget.image.width() - 1;
        var selection = widget.getSelection();
        var x1 = selection.x1;
        var x2 = selection.x2;
        var y1 = selection.y1;
        var y2 = selection.y2;
        if (value >= xMax) {
            value = xMax - 1;
        }
        var delta = value - x1;
        x1 = value;
        x2 += delta;
        if (x2 > xMax) {
            x2 = xMax;
        }
        widget.setSelection(x1, y1, x2, y2);
    });
    
    /**
     * Обработчик нажатия enter
     */
    this.inputCropY1.keyup(function(e) {
        if (e.keyCode == 13) {
            this.blur();
        }
    });
    
    /**
     * Обработчик потери фокуса
     */
    this.inputCropY1.blur(function() {
        var o = $(this);
        var value = $(this).val();
        var check = widget.checkInputDigit(value);
        if (!check.valid) {
            $(this).val(widget.imageCropY1);
            return;
        }
        value = check.value;
        var yMax = widget.image.height() - 1;
        var selection = widget.getSelection();
        var x1 = selection.x1;
        var x2 = selection.x2;
        var y1 = selection.y1;
        var y2 = selection.y2;
        if (value >= yMax) {
            value = yMax - 1;
        }
        var delta = value - y1;
        y1 = value;
        y2 += delta;
        if (y2 > yMax) {
            y2 = yMax;
        }
        widget.setSelection(x1, y1, x2, y2);
    });
    
    /**
     * Обработчик нажатия enter
     */
    this.inputCropX2.keyup(function(e) {
        if (e.keyCode == 13) {
            this.blur();
        }
    });
    
    /**
     * Обработчик потери фокуса
     */
    this.inputCropX2.blur(function() {
        var o = $(this);
        var value = $(this).val();
        var check = widget.checkInputDigit(value);
        if (!check.valid) {
            $(this).val(widget.imageCropX2);
            return;
        }
        value = check.value;
        var xMax = widget.image.width() - 1;
        var selection = widget.getSelection();
        var x1 = selection.x1;
        var x2 = selection.x2;
        var y1 = selection.y1;
        var y2 = selection.y2;
        if (value >= xMax) {
            value = xMax;
        }
        var delta = value - x2;
        x2 = value;
        x1 += delta;
        if (x1 >= xMax) {
            x1 = xMax - 1;
        }
        if (x1 < 0) {
            x1 = 0;
        }
        widget.setSelection(x1, y1, x2, y2);
    });
    
    /**
     * Обработчик нажатия enter
     */
    this.inputCropY2.keyup(function(e) {
        if (e.keyCode == 13) {
            this.blur();
        }
    });
    
    /**
     * Обработчик потери фокуса
     */
    this.inputCropY2.blur(function() {
        var o = $(this);
        var value = $(this).val();
        var check = widget.checkInputDigit(value);
        if (!check.valid) {
            $(this).val(widget.imageCropY2);
            return;
        }
        value = check.value;
        var yMax = widget.image.height() - 1;
        var selection = widget.getSelection();
        var x1 = selection.x1;
        var x2 = selection.x2;
        var y1 = selection.y1;
        var y2 = selection.y2;
        if (value >= yMax) {
            value = yMax;
        }
        var delta = value - y2;
        y2 = value;
        y1 += delta;
        if (y1 >= yMax) {
            y1 = yMax - 1;
        }
        if (y1 < 0) {
            y1 = 0;
        }
        widget.setSelection(x1, y1, x2, y2);
    });
    
    /**
     * Обработчик нажатия enter
     */
    this.inputCropWidth.keyup(function(e) {
        if (e.keyCode == 13) {
            this.blur();
        }
    });
    
    /**
     * Обработчик потери фокуса
     */
    this.inputCropWidth.blur(function() {
        var o = $(this);
        var value = $(this).val();
        var check = widget.checkInputDigit(value);
        if (!check.valid) {
            $(this).val(widget.imageCropWidth);
            return;
        }
        value = check.value;
        var xMax = widget.image.width() - 1;
        var selection = widget.getSelection();
        var x1 = selection.x1;
        var x2 = selection.x2;
        var y1 = selection.y1;
        var y2 = selection.y2;
        if (0 == value) value = 1;
        var delta = value - widget.imageCropWidth;
        x2 += delta;
        if (x2 > xMax) {
            x1 -= x2 - xMax;
            x2 = xMax;
        }
        if (x1 < 0) x1 = 0;
        widget.setSelection(x1, y1, x2, y2);
    });
    
    /**
     * Обработчик нажатия enter
     */
    this.inputCropHeight.keyup(function(e) {
        if (e.keyCode == 13) {
            this.blur();
        }
    });
    
    /**
     * Обработчик потери фокуса
     */
    this.inputCropHeight.blur(function() {
        var o = $(this);
        var value = $(this).val();
        var check = widget.checkInputDigit(value);
        if (!check.valid) {
            $(this).val(widget.imageCropHeight);
            return;
        }
        value = check.value;
        var yMax = widget.image.height() - 1;
        var selection = widget.getSelection();
        var x1 = selection.x1;
        var x2 = selection.x2;
        var y1 = selection.y1;
        var y2 = selection.y2;
        if (0 == value) value = 1;
        var delta = value - widget.imageCropHeight;
        y2 += delta;
        if (y2 > yMax) {
            y1 -= y2 - yMax;
            y2 = yMax;
        }
        if (y1 < 0) y1 = 0;
        widget.setSelection(x1, y1, x2, y2);
    });
    
    /**
     * Флаг, обозначает что идет процесс загрузки файла на сервер
     * 
     * @var boolean
     */
    this.uploadInProgress = null;
    
    /**
     * Загрузить файл (закачать)
     * 
     * @param void
     * @return void
     */
    this.upload = function() {
        if (!this.hasImage()) {
            alert('Add or paste image first');
            return;
        }
        if (this.uploadInProgress) {
            alert('another request waiting');
            return;
        }
        this.createIframe();
        if (this.imageString.length) {
            this.formImageString.val(this.imageString); 
        } else {
            this.formImageString.val('');
        }
        var reg = /^\d+$/;
        if (reg.test(this.image.width())) {
            this.formWidth.val(this.image.width());
        }
        if (reg.test(this.image.height())) {
            this.formHeight.val(this.image.height());
        }
        if (this.isCrop()) {
            if (reg.test(this.imageCropX1)) {
                this.formX1.val(this.imageCropX1);
            }
            if (reg.test(this.imageCropX2)) {
                this.formX2.val(this.imageCropX2);
            }
            if (reg.test(this.imageCropY1)) {
                this.formY1.val(this.imageCropY1);
            }
            if (reg.test(this.imageCropY2)) {
                this.formY2.val(this.imageCropY2);
            }
        } else {
            this.formX1.val('');
            this.formX2.val('');
            this.formY1.val('');
            this.formY2.val('');
        }
        this.form.submit();
        setTimeout(function() {
            widget.errorProcessing()
        }, 10000);
        this.uploadInProgress = true; // поставим флаг
    }
    
    /**
     * Создать iframe
     * 
     * @param void
     * @return void
     */
    this.createIframe = function() {
        this.deleteIframe();
        var iframe = $(document.createElement('iframe'));
        iframe.attr({
            name: this.instanceName,
            width: 0,
            height: 0,
            border: 0
        }).css({
            width: 0,
            height: 0,
            border: 0
        });
        this.form.closest('div').append(iframe);
    }
    
    /**
     * Удаляет iframe если он есть
     * 
     * @param void
     * @return void
     */
    this.deleteIframe = function() {
        this.form.closest('div').find('iframe').remove();
    }
    
    /**
     * Проверяет, есть ли iframe
     * 
     * @param void
     * @return boolean
     */
    this.hasIframe = function() {
        return !!this.form.closest('div').find('iframe').length;
    }
    
    /**
     * Произошла ошибка при закачке, не дождались ответ сервера
     * 
     * @param void
     * @return void
     */
    this.errorProcessing = function() {
        if (!this.uploadInProgress) return;
        this.deleteIframe();
        this.uploadInProgress = false;
        alert('No response from the server');
    }
    
    /**
     * Обработка ответа сервера
     * 
     * @param object
     * @return void
     */
    this.response = function(o) {
        this.deleteIframe();
        this.uploadInProgress = false;
        if (o.error) {
            alert(o.error);
            return;
        }
        this.inputFilename.val(o.url);
    }
}