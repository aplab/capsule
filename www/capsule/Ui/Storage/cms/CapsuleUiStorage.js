function CapsuleUiStorage(data) {
    // init
    var storage = this;
    this.instanceName = data.instanceName;
    this.top = data.top + 'px';
    this.container = $('#' + this.instanceName);
    this.container.css({top: this.top});
    
    var cl = function(foo) {
        console.log(foo);
    }
    
    /**
     * Static section
     */
    if ('undefined' === typeof(CapsuleUiStorage.instances)) {
        CapsuleUiStorage.instances = new Array();
    }
    CapsuleUiStorage.getInstance = function(instance_name) {
        if ('undefined' !== typeof(CapsuleUiStorage.instances[instance_name])) {
            return CapsuleUiStorage.instances[instance_name];
        }
        return null;
    }
    if ('undefined' !== typeof(CapsuleUiStorage.instances[this.instanceName])) {
        console.log('Instance already exists: ' + this.instanceName);
        console.error('Instance already exists: ' + this.instanceName);
    }
    CapsuleUiStorage.instances[this.instanceName] = this;
    /**
     * End of static section
     */
    
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