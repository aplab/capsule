function CSSupaWrapper(id) {
    this.id = id;
    this.applet = document.getElementById(this.id);
    
    this.ping = function() {
        try {
            // IE will throw an exception if you try to access the method in a 
            // scalar context, i.e. if( supaApplet.pasteFromClipboard ) ...
            return this.applet.ping();
        } catch(e) {
            return false;
        }
    }
    
    this.ping();
    
    this.pasteDual = function() {
        this.applet.start();
        if (!this.paste()) {
            return false;
        }
        if(!this.ping()) {
            window.alert('Applet not loaded yet.');
            return false;
        }
        var encodedData = this.applet.getEncodedString();
        if(!encodedData) {
            this.applet.start();
            //this.applet.clear();
            if (!this.paste()) {
                return false;
            }
            var encodedData = this.applet.getEncodedString();
            if(!encodedData) {
                window.alert('Cannot paste image.');
                return false;
            }
        }
    }
    
    this.paste = function() {
        if(!this.ping()) {
            window.alert('Applet not loaded yet.');
            return false;
        }
        try { 
            var err = this.applet.pasteFromClipboard();
            switch(err) {
                case 0: 
                    /* no error */
                    return true;
                    break;
                case 1: 
                    window.alert('Unknown Error');
                    break;
                case 2:
                    window.alert('Empty clipboard');
                    break;
                case 3:
                    window.alert('Clipboard content not supported. Only image data is supported.');
                    break;
                case 4:
                    window.alert('Clipboard in use by another application. Please try again in a few seconds.');
                    break;
                default:
                    window.alert('Unknown error code: ' + err );
            }
        } catch (ex) {
            if(typeof ex == 'object') {
                alert('Internal exception: ' + ex.toString() );
            } else {
                alert('Internal exception: ' + ex );
            }
        }
        return false;
    }
    
    this.getData = function() {
        if(!this.ping()) {
            window.alert('Applet not loaded yet.');
            return;
        }
        var encodedData = this.applet.getEncodedString();
        if(!encodedData) {
            window.alert('Paste image first.');
            return false;
        }
        return encodedData;
    }
    
    this.clear = function() {
        if(!this.ping()) {
            window.alert('Applet not loaded yet.');
            return;
        }
        this.applet.clear();
    }
}