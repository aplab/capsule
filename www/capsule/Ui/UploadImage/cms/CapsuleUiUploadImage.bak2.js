function CapsuleUiUploadImage(data) {
    /**
     * instance name
     * 
     * @var string
     */
    this.instanceName = data.instanceName;
    
    /**
     * ссылка на сам объект
     */
    var vidget = this;
    
    /**
     * static init
     * 
     * @param self o same object
     */
    (function(o) {
        var i = o.instanceName;
        var _self = CapsuleUiUploadImage; // classname
        if ('undefined' === typeof(_self.instances)) {
            _self.instances = new Array();
        }
        _self.getInstance = function(instance_name) {
            if ('undefined' !== typeof(_self.instances[instance_name])) {
                return _self.instances[instance_name];
            }
            return null;
        }
        if ('undefined' !== typeof(_self.instances[i])) {
            console.log('Instance already exists: ' + i);
            console.error('Instance already exists: ' + i);
        }
        _self.instances[i] = o;
    })(this);
    
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
     * test
     * 
     * @paramm void
     * @return void
     */
    this.test = function() {
        alert('passed');
    }
    
    /**
     * Составной id
     * 
     * @param sstring
     * @return string
     */
    this.id = function(id) {
        id = '#' + this.instanceName + '-' + id;
        //this.cl(id);
        return id;
    }
    
    /**
     * Начальная инициализация
     * 
     * @param void
     * @return void
     */
    this.init = function() {
        this.top = data.top + 'px';
        this.container = $('#' + this.instanceName);
        this.container.css({top: this.top});
        this.form = $(this.id('form'));
        this.inputFile = this.form.find(':file');
        this.inputWidth = $(this.id('width'));
        this.inputHeight = $(this.id('height'));
        this.inputX1 = $(this.id('X1'));
        this.inputY1 = $(this.id('Y1'));
        this.inputX2 = $(this.id('X2'));
        this.inputY2 = $(this.id('Y2'));
        this.imagePlace = $(this.id('workplace'));
        
        //declaration
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
        
        this.hasCrop = false;
        
        this.image = $(new Image()).addClass('image');
        
        this.imageWrapper = $(document.createElement('div')).addClass('image-wrapper');
        
        this.imageAreaSelect = null;
    }
    
    this.init(); // сразу инициализируем
    
    this.setWidth = function() {
        this.imageResizeWidth = this.image.width();
        $('#width-value :text').val(this.imageResizeWidth);
    }
    
    this.setHeight = function() {
        this.imageResizeHeight = this.image.height();
        $('#height-value :text').val(this.imageResizeHeight);
    }
    
    /**
     * Возвращает есть ли картинка 
     */
    this.hasImage = function() {
        return !!this.imagePlace.find('img').length;
    }
    
    this.attachDraggable = function() {
        if (this.hasImage()) {
            this.imageWrapper.draggable();
        }
    }
    
    this.detachDraggable = function() {
        this.imageWrapper.draggable('destroy');
    }
    
    this.attachResizable = function() {
        this.image.resizable({
            aspectRatio: true,
            resize: function(event, ui) {
                vidget.setWidth();
                vidget.setHeight();
            }
        });
    }
    
    this.detachResizable = function() {
        this.image.resizable('destroy');
    }
    
    this.cropModeOn = function() {
        if (this.hasImage()) {
            this.imageAreaSelect = this.image.imgAreaSelect({
                handles: true,
                instance: true,
                parent: vidget.imagePlace,
                instance: true,
                persistent: true,
                x1: 0,
                y1: 0,
                x2: vidget.image.width() - 1,
                y2: vidget.image.height() - 1,
                show: true
            });
        }
    }
    
    this.cropModeOff = function() {
        if (this.hasImage()) {
            this.imageAreaSelect.setOptions({remove: true});
            this.imageAreaSelect = null
        }
    }
    
    this.toggleCrop = function() {
        if (this.imageAreaSelect) {
            this.cropModeOff();
        } else {
            this.cropModeOn();
        }
    }
    
    /**
     * Вычисляет локальное имя выбранного файла
     * 
     * @param void
     * @return string
     */
    this.getLocalFilename = function() {
        var value = this.inputFile.val();
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
    this.inputFile.change(function() {
        var input = $(this)[0];
        if (input.files && input.files[0]) {
            if (input.files[0].type.match('image.*')) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var v = vidget;
                    var i = v.image;
                    var w = v.imageWrapper;
                    var p = v.imagePlace;
                    p.empty();
                    i.load(function() {
                        w.append(i);
                        p.append(w);
                        v.imageOriginalFilename = v.getLocalFilename();
                        v.imageOriginalWidth = i.width();
                        v.imageOriginalHeight = i.height();
                        v.setWidth(v.imageOriginalWidth);
                        v.setHeight(v.imageOriginalHeight);
                        v.attachDraggable();
                        v.attachResizable();
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
    });
    
    /**
     * Открыть диалоговое окно выбора одного файла
     */
    this.selectFile = function() {
        this.inputFile.click();
    }
    
    
    
    
    
    
    
}