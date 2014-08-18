function CapsuleUiStorage(data) {
    // init
    var storage = this;
    this.instanceName = data.instanceName;
    this.top = data.top + 'px';
    this.container = $('#' + this.instanceName);
    this.container.css({top: this.top});
    this.selectedItem = null;
    
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
    
    /**
     * Path to ajax
     */
    this.ajax = '/ajax/';
    
    /**
     * Путь хранилища
     */
    this.path = null;
    
    /**
     * Загрузить список
     */
    this.loadList = function() {
        $.post(storage.ajax, {
                'cmd': 'storageOverview',
                'instance_name': storage.instanceName,
                'path': storage.path
            }, function(data) {
                storage.container.empty();
                storage.container.append(data);
                // post-init
                storage.headerPlace = $('#' + storage.instanceName + '-header-place');
                storage.header = $('#' + storage.instanceName + '-header');
                storage.headerWrapper = $('#' + storage.instanceName + '-header-wrapper');
                storage.body = $('#' + storage.instanceName + '-body');
                storage.footer = $('#' + storage.instanceName + '-footer');
                // horizontal scroll handler
                storage.body.scroll(function() {
                    storage.header.css({left: -storage.body.scrollLeft()});
                });
                storage.initLoadedRows();
            }, 'html'
        );
    }
    
    this.loadList();
    
    // manage rows init
    this.initLoadedRows = function() {
        this.body.find('.capsule-ui-storage-overview-body-row').each(function() {
            $(this).click(function() {
                if ($(this).hasClass('selected')) {
                    $(this).removeClass('selected');
                    storage.selectedItem = null;
                } else {
                    if (storage.selectedItem) {
                        if (storage.selectedItem.hasClass('selected')) {
                            storage.selectedItem.removeClass('selected');
                            storage.selectedItem = null;
                        }
                    }
                    $(this).addClass('selected');
                    storage.selectedItem = $(this);
                }
                if (storage.selectedItem) {
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
                var input = o.find('input:hidden').get(0);
                if ('undefined' === typeof(input)) {
                    return;
                }
                storage.path = $(input).val();
                storage.loadList();
            });
        });
    }
}