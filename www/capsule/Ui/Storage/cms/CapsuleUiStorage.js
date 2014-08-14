function CapsuleUiStorage(data) {
 // init
    var vidget = this;
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
}