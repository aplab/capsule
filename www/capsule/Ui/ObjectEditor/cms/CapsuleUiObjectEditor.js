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
        return null;
    }
    if ('undefined' !== typeof(CapsuleUiObjectEditor.instances[instance_name])) {
        console.log('Instance already exists: ' + instance_name);
        console.error('Instance already exists: ' + instance_name);
        return;
    }
    CapsuleUiObjectEditor.instances[instance_name] = this;
    /**
     * End of static section
     */
    var vidget = this;
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
        var height = vidget.container.height();
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
                var height = vidget.container.height();
                editor.resize(null, height);
                editor.on('afterCommandExec', function( e ) {
                    var height = vidget.container.height();
                    editor.resize(null, height);
                } );
                $(window).resize(function() {
                    vidget.fitEditors();
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
    })
}