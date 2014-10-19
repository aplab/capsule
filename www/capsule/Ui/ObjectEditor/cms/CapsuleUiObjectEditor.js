function CapsuleUiObjectEditor(instance_name) {
    /**
     * Static section
     */
    if ('undefined' === typeof(CapsuleUiObjectEditor.instances)) {
        CapsuleUiObjectEditor.instances = new Array();
    }
    CapsuleUiObjectEditor.getInstance = function(instance_name) {
        if ('undefined' !== typeof(CapsuleUiObjectEditor.instances[instance_name])) {
            return CapsuleUiObjectEditor.instances[instance_name];
        }
        for(var prop in CapsuleUiObjectEditor.instances) {
            if (CapsuleUiObjectEditor.instances.hasOwnProperty(prop)) {
                return CapsuleUiObjectEditor.instances[prop];
            }
            break;
        }
        return null;
    };
    if ('undefined' !== typeof(CapsuleUiObjectEditor.instances[instance_name])) {
        console.log('Instance already exists: ' + instance_name);
        console.error('Instance already exists: ' + instance_name);
        return;
    }
    CapsuleUiObjectEditor.instances[instance_name] = this;
    /**
     * End of static section
     */
    var widget = this;
    this.instanceName = instance_name;
    this.block = $('#' + this.instanceName);
    this.container = $('#' + this.instanceName + '-container');
    this.form = $('#' + this.instanceName + '-form');
    this.saveAndExit = function() {
        var e = document.createElement('input');
        $(e).attr({
            'name': 'saveAndExit',
            'value': 'Y'
        });
        this.form.append(e);
        this.save();
    }
    this.saveAndAdd = function() {
        var e = document.createElement('input');
        $(e).attr({
            'name': 'saveAndAdd',
            'value': 'Y'
        });
        this.form.append(e);
        this.save();
    }
    this.save = function() {
        var t = CapsuleUiTabControl.getInstance('object-editor-tab-control');
        if ('undefined' != typeof(t)) {
            var e = document.createElement('input');
            $(e).attr({
                'name': 'activeTabNumber',
                'value': t.activeTabNumber
            });
            this.form.append(e);
        }
        this.form.submit();
    }
    this.fitEditors = function() {
        var height = widget.container.height();
        for (var o in CKEDITOR.instances) {
            CKEDITOR.instances[o].resize(null, height);
        }
    }
    $(document).ready(function() {
        /**
         * ckeditor handler
         */
        if ('undefined' != typeof(CKEDITOR)) {
            CKEDITOR.on('instanceReady', function( ev ) {
                var editor = ev.editor;
                var height = widget.container.height();
                editor.resize(null, height);
                editor.on('afterCommandExec', function( e ) {
                    var height = widget.container.height();
                    editor.resize(null, height);
                } );
                $(window).resize(function() {
                    widget.fitEditors();
                })
            });
        }
        /**
         * nested handler
         */
        (function() {
            var prefix = 'capsule-ui-oe-el-nested-';
            $('select[id^=' + prefix + 'master]').each(function(i, o) {
                var master = $(o);
                var id = master.attr('id').match(/\d+$/);
                if (!id) return;
                id = id[0];
                var slave = $('#' + prefix + 'slave' + id);
                var classname = $('#' + prefix + 'classname' + id).val();
                var slave_default = $('#' + prefix + 'slave-default-value' + id).val();
                var loadNested = function() {
                    $.post('/ajax/', {
                        'class': classname,
                        'id': master.val(),
                        'cmd': 'loadNested'
                    }, function(data, status, jq) {
                        slave.empty();
                        slave.append($('<option></option>').attr('value', 0).text(''));
                        var val;
                        for (var p in data) {
                            val = data[p].value;
                            slave.append($('<option></option>').attr({
                                value: val,
                                selected: val == slave_default ? 'selected' : false
                            }).text(data[p].text));
                        };
                    }, 'json');
                }; 
                master.change(loadNested);
                loadNested();
            });
        })();
        
        $('.capsule-ui-oe-el-image input').click(function() {
            $('.capsule-ui-oe-el-image input').removeClass('capsule-ui-oe-el-image-selected');
            $(this).addClass('capsule-ui-oe-el-image-selected');
            widget.selectImage();
        }).blur(function() {
            widget.handleImages();
        });
        widget.handleImages();
    });
    
    var fullViewImage = null;
    
    this.handleImages = function() {
        $('.capsule-ui-oe-el-image').each(function(i, o) {
            var o = $(o);
            var input = o.find('input');
            var preview = o.find('[class$="preview"]');
            var meta = o.find('[class$="meta"]');
            var img = $(document.createElement('img'));
            img.load(function() {
                var h = this.height;
                var w = this.width;
                var o = $(this);
                preview.empty().append(this);
                var m = (preview.height() - o.height()) / 2;
                o.css({
                    marginTop: parseInt(m.toString(10), 10)
                });
                meta.empty().append('Width: ' + w + '<br>Height: ' + h);
            }).attr({
                src: input.val()
            }).click(function() {
                var im = $(this);
                if (null === fullViewImage) {
                    fullViewImage = new CapsuleUiDialog({
                        instanceName: this.instanceName + '-fullviewimage',
                        hidden: false,
                        title: 'Изображение',
                        width: 200,
                        height: 200,
                        contentType: 'iframe',
                        iframeSrc: im.attr('src'),
                        opacity: .9
                    });
                    fullViewImage.iframe.attr({
                        scrolling: 'yes',
                    });
                    fullViewImage.showCenter();
                } else {
                    fullViewImage.iframe.attr({
                        src: im.attr('src')
                    });
                    fullViewImage.showCenter();
                }
            }).error(function() {
                alert('Неправильно указан адрес картинки');
            });
        });
    };
    
    var imagesWindow = null;
    
    this.selectImage = function() {
        if (Capsule.isFramed()) {
            location.href = '/admin/uploadimage/';
            return;
        }
        if (null === imagesWindow) {
            imagesWindow = new CapsuleUiDialog({
                instanceName: this.instanceName + '-uploadimage',
                hidden: true,
                title: 'Загрузка изображения',
                width: 640,
                height: 480,
                contentType: 'iframe',
                iframeSrc: '/admin/uploadimage/',
                opacity: .9
            });
        }
        imagesWindow.showCenter();
    }
    
    this.setImageVal = function(val) {
        var result = 0;
        $('.capsule-ui-oe-el-image input.capsule-ui-oe-el-image-selected').each(function(i, o) {
            $(o).val(val).blur();
            result ++;
        });
        return !!result;
    }
}