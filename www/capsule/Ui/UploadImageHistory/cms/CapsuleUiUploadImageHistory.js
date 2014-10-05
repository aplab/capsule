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
    var instance = this;
    
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
    
    /**
     * add handlers
     */
    this.workplace.find('.item').click(function() {
        var o = $(this);
        window.prompt('Ссылка на изображение (Ctrl + C - копировать ссылку, ESC - закрыть этот диалог)', o.find('img').attr('src'));
    }).hover(function(event) {
            var o = $(this);
            o.find('.fav').addClass('is-act').click(function(event) {
                event.stopPropagation();
                instance.fav(o);
            });
            o.find('.pen').addClass('is-act').click(function(event) {
                event.stopPropagation();
                instance.pen(o);
            });
            o.find('.del').addClass('is-act').click(function(event) {
                event.stopPropagation();
                instance.del(o);
            });
        }, function() {
            var o = $(this);
            o.find('.fav').removeClass('is-act').unbind('click');
            o.find('.pen').removeClass('is-act').unbind('click');
            o.find('.del').removeClass('is-act').unbind('click');
        }
    );
    
    this.del = function(o) {
        if (!confirm('Подтверждение удаления')) return false;
        var container = o.closest('div.item');
        var id = container.find('input[name="id"]').val();
        $.post('/ajax/', {
            id: id,
            cmd: 'deleteImage'
        }, function(data, status, jq) {
            if (data.error) {
                alert('error');
                return;
            }
            container.animate({
                opacity: 0,
                width: 0
            }, 300, 'swing', function() {
                container.remove();
            });
        }, 'json');
    }
    
    this.pen = function(o) {
        var container = o.closest('div.item');
        var id = container.find('input[name="id"]').val();
        var comment = container.attr('title');
        var new_comment = prompt('Комментарий к изображению', comment);
        if (comment == new_comment) return; // не изменено
        $.post('/ajax/', {
            id: id,
            cmd: 'commentImage',
            comment: new_comment
        }, function(data, status, jq) {
            if (data.error) {
                alert('error');
                return;
            }
            container.attr({
                title: new_comment
            });
        }, 'json');
    }
    
    this.fav = function(o) {
        var container = o.closest('div.item');
        var id = container.find('input[name="id"]').val();
        $.post('/ajax/', {
            id: id,
            cmd: 'toggleFavoritesImage'
        }, function(data, status, jq) {
            if (data.error) {
                alert('error');
                return;
            }
            if (data.favorites) {
                container.find('.fav').addClass('is-fav');
            } else {
                container.find('.fav').removeClass('is-fav');
            }
        }, 'json');
    }
}