function CapsuleUiTabControl(data) {
    var instance_name = data.instanceName;
    /**
     * Static section
     */
    if ('undefined' === typeof(CapsuleUiTabControl.instances)) {
        CapsuleUiTabControl.instances = new Array();
    }
    CapsuleUiTabControl.getInstance = function(instance_name) {
        if ('undefined' !== typeof(CapsuleUiTabControl.instances[instance_name])) {
            return CapsuleUiTabControl.instances[instance_name];
        }
        return null;
    }
    if ('undefined' !== typeof(CapsuleUiTabControl.instances[instance_name])) {
        console.log('Instance already exists: ' + instance_name);
        console.error('Instance already exists: ' + instance_name);
        return;
    }
    CapsuleUiTabControl.instances[instance_name] = this;
    /**
     * End of static section
     */
    var vidget = this;
    this.data = data;
    this.instanceName = this.data.instanceName;
    this.activeTabNumber = -1;
    this.container = $('#' + this.instanceName);
    this.wrapper = $('#' + this.instanceName + '-wrapper');
    this.items;
    this.itemsNumber = 0;
    this.zIndexes = new Array();
    
    /**
     * Set active tab
     * 
     * @param void
     * @return void
     */
    this.setActive = function(n) {
        var items = this.items;
        var l = items.length;
        n = parseInt(n, 10);
        if (isNaN(n)) {
            n = 0;
        }
        if (n < 0) {
            n = 0;
        }
        if (n > l) {
            n = l;
        }
        var a = this.activeTabNumber;
        if (n == a) {
            return;
        }
        // deactivate 
        if (a >= 0) {
            $(items[a]).css({
                zIndex: (items.length - n) * 10
            });
            $(items[a]).removeClass('active');
            var bind = this.data.items[a].bind; 
            if ('undefined' != typeof(bind) && bind) {
                $('#' + bind).css({
                    display: 'none'
                });
            }
        }
        // activate current item 
        var o = $(items[n]);
        o.css({
            zIndex: 10000
        });
        o.addClass('active');console.log(o.attr('class'));
        bind = this.data.items[n].bind; 
        if ('undefined' != typeof(bind) && bind) {
            $('#' + this.data.items[n].bind).css({
                display: 'block'
            });
        }
        var callback = this.data.items[n].callback; 
        if ('undefined' != typeof(callback) && callback) {
            eval(callback);
        }
        this.activeTabNumber = n;
    }
    
    /**
     * Init
     * 
     * @param void
     * @return int
     */
    this.init = function() {
        this.items = $(this.wrapper).find('div.tab');
        var items = this.items;
        var active = 0;
        items.each(function(i, o) {
            var o = $(o);
            o.css({
                zIndex: (items.length - i) * 10
            });
            o.click(function(e) {
                vidget.setActive(i);
            });
            bind = vidget.data.items[i].bind; 
            if ('undefined' != typeof(bind) && bind) {
                $('#' + vidget.data.items[i].bind).css({
                    display: 'none'
                });
            }
            if (vidget.data.items[i].active) {
                active = i;
            }
        });
        return active;
    }
    
    this.setActive(this.init());
}