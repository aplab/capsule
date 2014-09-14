function CapsuleUiUploadImage(data) 
{
    var IMAGE_SOURCE_CLIPBOARD = 'clipboard';
    var IMAGE_SOURCE_FILE = 'file';
    
    /**
     * instance name
     * 
     * @var string
     */
    this.instanceName = data.instance_name;
    
    /**
     * ссылка на сам объект
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
        }
        c.instances[i] = o;
    })(this, arguments.callee);
    
    /**
     * console log wrapper
     * 
     * @param mixed foo
     * @return void
     */
    this.cl = function(foo) {
        console.log(foo);
    }
    
    /**
     * Составной id
     * 
     * @param sstring
     * @return string
     */
    this.id = function(id) {
        id = '#' + this.instanceName + '-' + id;
        return id;
    }
    
    /**
     * declaration
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
    
    this.inputFilename = $(this.id('filename')).val(null);
    this.inputWidth = $(this.id('input-width')).val(null);
    this.inputHeight = $(this.id('input-height')).val(null);
    this.inputCropWidth = $(this.id('input-crop-width')).val(null);
    this.inputCropHeight = $(this.id('input-crop-height')).val(null);
    this.inputCropX1 = $(this.id('input-crop-x1')).val(null);
    this.inputCropY1 = $(this.id('input-crop-y1')).val(null);
    this.inputCropX2 = $(this.id('input-crop-x2')).val(null);
    this.inputCropY2 = $(this.id('input-crop-y2')).val(null);
    
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
        
        this.resizeMode = false;
        this.cropMode = false;
        
        this.image = null;
        this.imageWrapper = null;
        
        this.resizeMode = null;
        this.cropMode = null;
        this.dragMode = null;
        
        // applet object
        this.applet = null;
        
        // clipboard or file
        this.imageSource = null;
        
        // paste in progress (вставка из буфера обмена в процессе)
        this.pasteProcess = null;
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
                    p.empty();
                    i.load(function() {
                        wr.append(i);
                        p.append(wr);
                        w.imageOriginalFilename = w.getLocalFilename();
                        w.imageOriginalWidth = i.width();
                        w.imageOriginalHeight = i.height();
                        w.inputFilename.val(w.imageOriginalFilename); 
                        w.attachDraggable();
                    });
                    i.attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
                reader = null;
            } else {
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
        this.resizeBar.show();
        this.resizeMode = true;
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
        if (!this.hasImage()) {
            this.resizeMode = null;
            return;
        }
        if (this.isResizable()) {
            this.image.resizable('destroy');
        }
    }
    
    this.isResizable = function() {
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
     * 
     */
    this.setHeight = function() {
        this.imageResizeHeight = this.image.height();
        this.inputHeight.val(this.imageResizeHeight);
    }
    
    /**
     * включить перетаскивание
     */
    this.attachDraggable = function() {
        if (!this.hasImage()) {
            return;
        }
        this.dragMode = true;
        this.imageWrapper.draggable();
    }
    
    /**
     * выключить перетаскивание
     */
    this.detachDraggable = function() {
        this.dragMode = null;
        if (!this.hasImage()) {
            return;
        }
        this.imageWrapper.draggable('destroy');
    }
    
    this.resetImage = function() {
        if (!this.hasImage()) {
            return;
        }
        this.detachDraggable();
        this.detachResizable();
        this.imageWrapper.css({
            top: 0,
            left: 0
        })
    }
    
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
            classid: 'java:de.christophlinder.supa.SupaApplet.class'/*,
            archive: '/capsule/assets/share/supa/0.6a/lib/Supa.jar'*/
        });
        for (var p in param) {
            $(applet).append($(document.createElement('param')).attr({
                name: p,
                value: param[p]
            }));
        }
        //this.form.closest('div').append(applet);
        //widget.applet = document.getElementById('supaApplet');
        widget.applet = applet;
    }
    this.createApplet();
    
    console.log(typeof(this.applet.ping));
    this.form.closest('div').append(this.applet);
    $(this.applet).append($(document.createElement('param')).attr({
        name: 'archive',
        value: '/capsule/assets/share/supa/0.6a/lib/Supa.jar'
    }));
    
    console.log(typeof(this.applet.ping));
    console.log(this.applet.ping());
    
    this.pasteImage = function() {
        this.form.closest('div').load('/capsule/assets/share/supa/applet.html', function() {
            widget.applet = document.getElementById('supaApplet');
            widget.ping();
            widget.applet.start();
            widget.applet.pasteFromClipboard();
            var imgstr = widget.applet.getEncodedString();
            console.log(imgstr);
            
            
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
                w.inputFilename.val(w.imageOriginalFilename); 
                w.attachDraggable();
            });
            i.attr('src', 'data:image/png;base64,' + imgstr);
            
            
            
        });
        
    }
    
    this.ping = function() {
        console.log(this.applet);
        console.log(this.applet.ping);
        try {
            // IE will throw an exception if you try to access the method in a 
            // scalar context, i.e. if( supaApplet.pasteFromClipboard ) ...
            return this.applet.ping();
        } catch(e) {
            return false;
        }
    }
}