function CapsuleUiToolbar(data) {
    var instance_name = data.instanceName;
    if ('undefined' === typeof(CapsuleUiToolbar.instances)) {
        CapsuleUiToolbar.instances = new Array();
    }
    CapsuleUiToolbar.getInstance = function(instance_name) {
        if ('undefined' !== typeof(CapsuleUiToolbar.instances[data.instanceName])) {
            return CapsuleUiToolbar.instances[data.instanceName];
        }
        return null;
    }
    if ('undefined' !== typeof(CapsuleUiToolbar.instances[instance_name])) {
        console.log('Instance already exists: ' + instance_name);
        console.error('Instance already exists: ' + instance_name);
    }
    CapsuleUiToolbar.instances[instance_name] = this;
    this.instanceName = data.instanceName;
    this.wrapper = $('#' + this.instanceName);
    this.container = $('#' + this.instanceName + '-container');
    $(this.container).children('div').each(function(num, o) {
        $(o).onselectstart = function() { return false; };
        $(o).unselectable = "on";
        $(o).css({
            '-moz-user-select': 'none',
            '-khtml-user-select': 'none',
            '-webkit-user-select': 'none',
            '-o-user-select': 'none',
            'user-select': 'none'
        });
        if ($(o).is('.delimiter, .disabled')) {
            return;
        }
        $(o).hover(function() {
            $(this).addClass('item-hover');
        }, function() {
            $(this).removeClass('item-hover');
            $(this).removeClass('item-active');
        });
        $(o).mousedown(function() {
            $(this).addClass('item-active');
        })
        $(o).mouseup(function() {
            $(this).removeClass('item-active');
        })
        $(o).click(function() {
            var d = data.items[num];
            if ('undefined' !== typeof(d.action)) {
                jQuery.globalEval(d.action);
            }
            if ('undefined' === typeof(d.url)) {
                return;
            }
            if (!d.url) {
                return;
            }
            if ('object' !== typeof(d.parameters)) {
                $(location).attr('href', d.url);
                return;
            }
            if (d.parameters.length < 1) {
                $(location).attr('href', d.url);
                return;
            }
            var form = document.createElement('form');
            $(form).attr({
                'action': d.url
            });
            var get = new Array();
            var post = new Array();
            for (var i = 0; i < d.parameters.length; i++) {
                var parameter = d.parameters[i];
                if ('undefined' === typeof(parameter.post)) {
                    get.push(parameter);
                    continue;
                }
                if (!parameter.post) {
                    get.push(parameter);
                    continue;
                }
                post.push(parameter);
            }
            if (post.length) {
                $(form).attr({
                    'method': 'post'
                });
                for (var i = 0; i < post.length; i++) {
                    var p = post[i];
                    var e = document.createElement('input');
                    $(e).attr('type', 'hidden');
                    if ('undefined' !== typeof(p.name)) {
                        $(e).attr('name', p.name);
                    }
                    if ('undefined' !== typeof(p.value)) {
                        $(e).attr('value', p.value);
                    }
                    $(form).append(e);
                }
            }
            for (var i = 0; i < get.length; i++) {
                var p = get[i];
                if ('undefined' !== typeof(p.name)) {
                    if (p.name.length) {
                        var action = $(form).attr('action');
                        if (i === 0) {
                            action += '?';
                        } else {
                            action += '&';
                        }
                        action += p.name;
                        action += '=';
                        if ('undefined' !== typeof(p.value)) {
                            action += p.value;
                        }
                        $(form).attr('action', action);
                    }
                }
            }
            $(o).append(form);
            form.submit();
        });
    });
}