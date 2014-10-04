/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 13.09.2014 6:55:12 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */
function CapsuleUiUploadImageHistory(data) 
{
    /**
     * instance name
     * 
     * @var string
     */
    this.instanceName = data.instance_name;
    
    /**
     * ссылка на сам объект для передачи контекста
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
            throw new Error('Instance already exists: ' + i);
        }
        c.instances[i] = o;
    })(this, arguments.callee);
    
    /**
     * console log wrapper
     * 
     * @param mixed foo
     * @return void
     */
    var cl = function(foo) {
        console.log(foo);
    }
    
    /**
     * Проверяет входной параметр, который может содержать простые 
     * арифметические операции. Максимальная длина выражения 15 символов
     * 
     * @param string
     * @return object
     */
    this.checkInputDigit = function(val) {
        var reg = /^[0-9+\/\*-]{0,14}\d$/;
        var ret = new Object({
            value: null,
            valid: false
        });
        if (reg.test(val)) {
            val = eval(val + '');
        } else {
            return ret;
        }
        var reg = /^\d{1,4}$/;
        if (reg.test(val)) {
            ret.value = val;
            ret.valid = true;
        }
        return ret;
    }
    
    /**
     * Составной id. Добавляет # и instanceName
     * для уменьшения длины записи при использованиив селекторах jquery
     * 
     * @param string
     * @return string
     */
    this.id = function(id) {
        id = '#' + this.instanceName + '-' + id;
        return id;
    }
    
    /**
     * declaration
     * не буду расписывать, названия сами за себя говорят
     */
    this.container = $('#' + this.instanceName);
    this.container.css({top: data.top + 'px'});
    this.workplace = $(this.id('workplace'));
    
    this.workplace.find('.item').click(function() {
        var o = $(this);
        window.prompt('Ссылка на изображение (Ctrl + C - копировать ссылку, ESC - закрыть этот диалог)', o.find('img').attr('src'));
    });
    
    
}