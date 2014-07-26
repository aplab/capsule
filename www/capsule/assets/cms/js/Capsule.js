(function(window) {
    var Capsule = function() {
        
        
        
        
    }
    
    Capsule.plugins = new Array();
    
    /**
     * Register object
     */
    if ( typeof window === "object" && typeof window.document === "object" ) {
        window.Capsule = Capsule;
    }
})(window);