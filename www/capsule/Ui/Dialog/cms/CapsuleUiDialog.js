/**
 * CapsuleUiDialog
 * 
 * @param data
 * 
 * @property string instanceName
 * 
 * @property string title
 * 
 * @property int width
 * @property int height
 * 
 * @property int minWidth
 * @property int minHeight
 * 
 * @property int maxWidth
 * @property int maxHeight
 * 
 * @property boolean resizable
 * @property boolean hidden
 * 
 * @property string contentType = iframe|normal
 * 
 * @property string content
 * @property string iframeSrc
 */
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
        var reg = /^\d+$/;
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
     * init shared zIndex
     */
    (function(c) {
        if ('undefined' === typeof(c.z)) {
            c.z = 1000000;
        }
        dialog.share = c;
    })(arguments.callee);
    
    this.newZ = function() {
        return this.share.z++;
    };
    
    /**
     * init param
     */
    var absMinWidth = 100;
    var absMinHeight = 100;
    this.minWidth = data.minWidth || absMinWidth;
    this.minHeight = data.minHeight || absMinHeight;
    if (!digit(this.minWidth)) this.minWidth = absMinWidth;
    if (!digit(this.minHeight)) this.minHeight = absMinHeight;
    if (this.minWidth < absMinWidth) this.minWidth = absMinWidth;
    if (this.minHeight < absMinHeight) this.minHeight = absMinHeight;
    
    var absMaxWidth = 1920;
    var absMaxHeight = 1080;
    this.maxWidth = data.maxWidth || absMaxWidth;
    this.maxHeight = data.maxHeight || absMaxHeight;
    if (!digit(this.maxWidth)) this.maxWidth = absMaxWidth;
    if (!digit(this.maxHeight)) this.maxHeight = absMaxHeight;
    if (this.maxWidth < absMaxWidth) this.maxWidth = absMaxWidth;
    if (this.maxHeight < absMaxHeight) this.maxHeight = absMaxHeight;
    
    var defaultWidth = 320;
    var defaultHeight = 240;
    this.width = data.width || defaultWidth;
    this.height = data.height || defaultHeight;
    if (!digit(this.width)) this.width = defaultWidth;
    if (!digit(this.height)) this.height = defaultHeight;
    if (this.width < this.minWidth) this.width = this.minWidth;
    if (this.width > this.maxWidth) this.width = this.maxWidth;
    if (this.height < this.minHeight) this.height = this.minHeight;
    if (this.height > this.maxHeight) this.height = this.maxHeight;
    
    this.appendTo = data.appendTo || $('body');
    
    this.hidden = ('undefined' === typeof(data.hidden)) ? false : (data.hidden ? true :false);
    
    this.container = c().addClass('capsule-ui-dialog').css({
        display: this.hidden ? 'none' : 'block',
        width: this.width,
        height: this.height,
        zIndex: this.newZ()
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
    
    this.resizable = ('undefined' === typeof(data.resizable)) ? true : (data.resizable ? true :false);
    if (this.resizable) {
        this.bottomBorder = c().addClass('capsule-ui-dialog-resizable').appendTo(this.shadow);
        $(this.container).resizable({
            minWidth: this.minWidth,
            minHeight: this.minHeight,
            maxWidth: this.maxWidth,
            maxHeight: this.maxHeight
        });
        this.content = c().addClass('capsule-ui-dialog-content-resizable').appendTo(this.container);
    } else {
        this.content = c().addClass('capsule-ui-dialog-content-fixed').appendTo(this.container);
    }
    
    /**
     * attach dragable behavior
     */
    $(this.container).draggable({
        handle: this.head,
    });
    
    /**
     * focus etc
     * 
     * @param void 
     * @return void
     */
    this.container.mousedown(function() {
        dialog.setFocus();
    });
    
    /**
     * move dialog top up level
     * 
     * @param void
     * @return void
     */
    this.setFocus = function() {
        this.container.css({
            zIndex: this.newZ()
        });
    };
    
    /**
     * Закрыть окно
     */
    this.closeBtn.mousedown(function(event) {
        event.stopPropagation();
    }).click(function(event) {
        event.stopPropagation();
        dialog.hide();
    });
    
    /**
     * Спрятать окно
     * 
     * @param void
     * @return void
     */
    this.hide = function() {
        this.container.hide();
    };
    
    /**
     * Показать окно
     * 
     * @param void
     * @return void
     */
    this.show = function() {
        this.container.show();
        this.setFocus();
    };
    
    /**
     * Мигнуть окном
     * 
     * @param void
     * @return void
     */
    this.blinking = function() {
        if (this.container.is(':visible')) {
            this.blinkRunning = false;
            this.container.hide();
            setTimeout(function() {
                dialog.blinking();
            }, 50);
            return;
        } else {
            this.container.show();
            this.setFocus();
        }
    };
    
    /**
     * Помигать окном если оно видимое
     * 
     * @param void
     * @return void
     */
    this.blink = function() {
        if (this.container.is(':visible')) {
            for (var i = 1; i < 4; i++) {
                setTimeout(function() {
                    dialog.blinking();
                }, i * 100);
            }
        }
        this.show();
    };
}