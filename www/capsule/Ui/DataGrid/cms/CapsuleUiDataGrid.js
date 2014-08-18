function CapsuleUiDataGrid(data) 
{
    // init
    var dataGrid = this;
    this.selectedItem = null;
    this.instanceName = data.instanceName;
    this.top = data.top + 'px';
    this.container = $('#' + this.instanceName);
    this.container.css({top: this.top});
    this.headerPlace = $('#' + this.instanceName + '-header-place');
    this.header = $('#' + this.instanceName + '-header');
    this.headerWrapper = $('#' + this.instanceName + '-header-wrapper');
    this.body = $('#' + this.instanceName + '-body');
    this.footer = $('#' + this.instanceName + '-footer');
    
    /**
     * Static section
     */
    if ('undefined' === typeof(CapsuleUiDataGrid.instances)) {
        CapsuleUiDataGrid.instances = new Array();
    }
    CapsuleUiDataGrid.getInstance = function(instance_name) {
        if ('undefined' !== typeof(CapsuleUiDataGrid.instances[instance_name])) {
            return CapsuleUiDataGrid.instances[instance_name];
        }
        return null;
    }
    if ('undefined' !== typeof(CapsuleUiDataGrid.instances[this.instanceName])) {
        console.log('Instance already exists: ' + this.instanceName);
        console.error('Instance already exists: ' + this.instanceName);
    }
    CapsuleUiDataGrid.instances[this.instanceName] = this;
    /**
     * End of static section
     */
    
    // manage rows init
    this.body.find('.capsule-ui-datagrid-body-row').each(function() {
        $(this).click(function() {
            if ($(this).hasClass('selected')) {
                $(this).removeClass('selected');
                dataGrid.selectedItem = null;
            } else {
                if (dataGrid.selectedItem) {
                    if (dataGrid.selectedItem.hasClass('selected')) {
                        dataGrid.selectedItem.removeClass('selected');
                        dataGrid.selectedItem = null;
                    }
                }
                $(this).addClass('selected');
                dataGrid.selectedItem = $(this);
            }
            if (dataGrid.selectedItem) {
                //alert(dataGrid.selectedItem.find('td:first').text().trim());
            }
        });
        $(this).dblclick(function() {
            if (window.getSelection) {
                if (window.getSelection().empty) {  // Chrome
                  window.getSelection().empty();
                } else if (window.getSelection().removeAllRanges) {  // Firefox
                  window.getSelection().removeAllRanges();
                }
            } else if (document.selection) {  // IE?
                document.selection.empty();
            }
            var o = $(this);
            var input = o.find('input[name="edit"]:hidden').get(0);
            if ('undefined' === typeof(input)) {
                return;
            }
            $(location).attr('href', $(input).val());
        });
    });
    
    // horizontal scroll handler
    this.body.scroll(function() {
        dataGrid.header.css({left: -dataGrid.body.scrollLeft()});
    });
    
    // check/uncheck all handler
    this.header.find('input:checkbox').click(function(){
        if ($(this).prop('checked')) {
            dataGrid.body.find('input:checkbox[name="Checkbox"]').prop('checked', true);
        } else {
            dataGrid.body.find('input:checkbox[name="Checkbox"]').prop('checked', false);
        }
    })
    
    // shift + click group selection handler
    this.lastCheckbox = null;
    this.checkboxes = dataGrid.body.find('input:checkbox[name="Checkbox"]');
    var vidget = this;
    this.checkboxes.each(function(i, o) {
        // prevent double click action
        $(o).dblclick(function(event) {
            event.stopPropagation();
        });
        // click handler
        $(o).click(function(event) {
            event.stopPropagation();
            if (!vidget.lastCheckbox) {
                vidget.lastCheckbox = this;
                return;
            }
            if (vidget.lastCheckbox == this) {
                return;
            }
            if (!event.shiftKey) {
                vidget.lastCheckbox = this;
                return;
            } 
            var flag = 0;
            for (var i = 0; i < vidget.checkboxes.length; i++) {
                if (vidget.checkboxes[i] == this || vidget.checkboxes[i] == vidget.lastCheckbox) {
                    if (flag == 0) {
                        flag = 1;
                    } else if (flag == 1) {
                        vidget.checkboxes[i].checked = vidget.lastCheckbox.checked;
                        break;
                    }
                }
                if (flag == 1) {
                    vidget.checkboxes[i].checked = vidget.lastCheckbox.checked;
                }
            }
            vidget.lastCheckbox = this;
        });
    });
    
    this.getCurrentRow = function() {
        return this.body.find('.capsule-ui-datagrid-body-row').get(this.selectedItem);
    };
    
    this.getSelectedRows = function() {
        return dataGrid.body.find('input:checkbox[name="Checkbox"]:checked').closest('.capsule-ui-datagrid-body-row');
    };
    
    this.del = function() {
        var checked = this.getSelectedRows();
        var list = new Array();
        if (checked.length) {
            var c = confirm('Удалить отмеченные (' + checked.length + ')?');
            if (!c) {
                return;
            }
            checked.find('input:hidden[name="id"]').each(function(i, o) {
                list[i] = $(o).val();
            });
        } else {
            if (!this.selectedItem) {
                return;
            }
            var c = confirm('Удалить выделенный?');
            if (!c) {
                return;
            }
            this.selectedItem.find('input:hidden[name="id"]').each(function(i, o) {
                list[i] = $(o).val();
            });
        }
        if (!list.length) {
            return;
        }
        var del_url = dataGrid.body.find('input:hidden[name="del"]').val();
        if ('undefined' === typeof(del_url)) {
            return;
        }
        var f = document.createElement('form');
        $(f).attr({
            'method': 'post',
            'action': del_url
        });
        for (var i = 0; i < list.length; i++) {
            var e = document.createElement('input');
            $(e).attr({
                'type': 'hidden',
                'value': list[i],
                'name': 'del[]'
            });
            f.appendChild(e);
        }
        $('body').append(f);
        f.submit();
    };
    // pagination handler
    this.footer.find('select').change(function() {
        $(this).closest('form').submit();
    });
    $(document).keydown(function(e) {
        if (!e.ctrlKey) {
            return;
        } 
        if (37 == e.which) {
            dataGrid.footer.find('#previous-page').submit();
            return;
        }
        if (39 == e.which) {
            dataGrid.footer.find('#next-page').submit();
            return;
        }
    });
    this.footer.find('#prev-page-trigger').hover(function() {
        $(this).css({
            'text-decoration': 'underline',
            'cursor': 'pointer'
        });
    }, function() {
        $(this).css('text-decoration', 'none');
    }).click(function() {
        dataGrid.footer.find('#previous-page').submit();
    });
    this.footer.find('#next-page-trigger').hover(function() {
        $(this).css({
            'text-decoration': 'underline',
            'cursor': 'pointer'
        });
    }, function() {
        $(this).css('text-decoration', 'none');
    }).click(function() {
        dataGrid.footer.find('#next-page').submit();
    });
    
    $('input:checkbox[name="Active"]').change(function() {
        var o = $(this);
        o.prop('disabled', true);
        var d = o.val().split('::', 2);
        $.post('/ajax/', {
            'class': d[0],
            'id': d[1],
            'cmd': 'toggleActive'
        }, function(data, status, jq) {
            o.prop('checked', data.active ? true : false);
            o.prop('disabled', false);
        }, 'json');
    }).each(function(i, o) {
        // prevent double click action
        $(o).dblclick(function(event) {
            event.stopPropagation();
        });
    });
}