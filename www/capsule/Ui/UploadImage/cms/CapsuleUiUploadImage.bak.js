function CapsuleUiUploadImage(data) {
    /**
     * instance name
     * 
     * @var string
     */
    this.instanceName = data.instanceName;
    
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
    
    this.id = function(id) {
        id = '#' + this.instanceName + '-' + id;
        //this.cl(id);
        return id;
    }
    
    this.init = function() {
        var vidget = this;
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
    }
    
    this.init();
    
    /**
     * Обработать если выбран файл
     */
    this.inputFile.change(function() {
        
    });
    
    /**
     * Открыть диалоговое окно выбора одного файла
     */
    this.selectFile = function() {
        this.inputFile.click();
    }
    
    
    
    
    
    
    var iframe = document.createElement('iframe');
    this.iframe = $(iframe);
    this.iframe.attr({
        name: this.instanceName,
        width: 0,
        height: 0,
        border: 0
    }).css({
        width: 0,
        height: 0,
        border: 0
    });
    
    this.processing = 0;
    
    this.uploadForm = $('#' + this.instanceName + '-form');
    this.inputFile = this.uploadForm.find(':file');
    this.inputFile.change(function() {
        if (storage.processing) {
            alert('another request waiting');
        }
        $('body').append(storage.iframe);
        storage.uploadForm.submit();
        storage.processing = 1;
        setTimeout(function() {
            storage.errorProcessing()
        }, 4000);
    });
    
    this.response = function(o) {
        storage.iframe.remove();
        storage.processing = 0;
        if (o.error) {
            $('#' + this.instanceName + '-uf-result :text').val(o.error);
        } else {
            $('#' + this.instanceName + '-uf-result :text').val(o.url).
                change(function() {
                    Capsule.setSelection(this);
                }).
                click(function() {
                    Capsule.setSelection(this);
                }).
                focus(function() {
                    Capsule.setSelection(this);
                }).attr({
                    readonly: true
                });
            if (o.isImage) {
                this.handleImage(o);
            }
        }
    }
    
    this.errorProcessing = function() {
        storage.iframe.remove();
        storage.processing = 0;
        cl('post-handler');
    }
    
    this.handleImage = function(o) {
        var c = $('#' + this.instanceName + '-uf-image');
        c.css({
            width: o.width,
            height: o.height
        }).show();
        var i = $(new Image());
        i.attr({
            src: o.url
        });
        c.empty().append(i);
        var ias = i.imgAreaSelect({
            handles: true,
            instance: true,
            parent: i.closest('.capsule-ui-storage-elements')
        });
        i.closest('.capsule-ui-storage-elements').scroll(function() {
            //ias.update();
        });
    }
}