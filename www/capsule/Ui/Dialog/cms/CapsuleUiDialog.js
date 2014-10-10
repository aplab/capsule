function CapsuleUiDialog(data) {
    data = data || {};
    this.instanceName = data.instanceName || null;
    
    /**
     * ссылка на сам объект для передачи контекста
     */
    var dialog = this;
    
    /**
     * console log wrapper
     * 
     * @param mixed foo
     * @return void
     */
    var cl = function(foo) {
        console.log(foo);
    };
    
    /**
     * Проверка на цифры
     * 
     * @param mixed
     * @return boolean
     */
    var digit = function(val) {
        var reg = /^\d*$/;
        return reg.test(val);
    };
    
    /**
     * Create div
     * 
     * @param void
     * @return object
     */
    var c = function(tag) {
        var tag = tag || 'div';
        return $(document.createElement(tag));
    };
    
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
        if ('undefined' === typeof(c.getInstance)) {
            c.getInstance = function(instance_name) {
                if ('undefined' !== typeof(c.instances[instance_name])) {
                    return c.instances[instance_name];
                }
                return null;
            };
        }
        if ('undefined' !== typeof(c.instances[i])) {
            console.log('Instance already exists: ' + i);
            console.error('Instance already exists: ' + i);
            throw new Error('Instance already exists: ' + i);
        }
        c.instances[i] = o;
    })(this, arguments.callee);
    
    /**
     * init param
     */
    this.defaultWidth = 320;
    this.defaultHeight = 240;
    this.width = data.width || this.defaultWidth;
    this.height = data.height || this.defaultHeight;
    if (!digit(this.width)) this.width = this.defaultWidth;
    if (!digit(this.height)) this.height = this.defaultHeight;
    this.appendTo = data.appendTo || $('body');
    this.container = c().addClass('capsule-ui-dialog').css({
        width: this.width,
        height: this.height,
    }).appendTo(this.appendTo);
    this.head = c().addClass('capsule-ui-dialog-head').appendTo(this.container);
    this.caption = c().addClass('capsule-ui-dialog-caption').appendTo(this.head);
    this.leftCorner = c().addClass('capsule-ui-dialog-left-corner').appendTo(this.head);
    this.rightCorner = c().addClass('capsule-ui-dialog-right-corner').appendTo(this.head);
    this.closeBtn = c().addClass('capsule-ui-dialog-close-btn').appendTo(this.head);
    this.shadow = c().addClass('capsule-ui-dialog-shadow').appendTo(this.container);
    this.bottomBorder = c().addClass('capsule-ui-dialog-bottom-border').appendTo(this.shadow);
    this.bottomBorder = c().addClass('capsule-ui-dialog-left-border').appendTo(this.shadow);
    this.bottomBorder = c().addClass('capsule-ui-dialog-right-border').appendTo(this.shadow);
    this.bottomBorder = c().addClass('capsule-ui-dialog-resizable').appendTo(this.shadow);
    
    $(this.container).draggable({
        handle: this.head
    });
    
    cl(this);
}