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
    
    /** 
     * Установка выделения в инпуте 
     * 
     * @param inputBox Элемент в котором устанавливается выделение (HTMLInputElement) 
     * @param start Начальная позиция выделения (int) 
     * @param end Конечная позиция выделения (int) 
     */  
    Capsule.setSelection = function(inputBox) {
        var start = 0;
        var end = inputBox.value.length;
        if (start > end) {  
             start = end;  
        }  
        if ("selectionStart" in inputBox) { //gecko  
             inputBox.setSelectionRange(start,end);  
        } else {  
            r = inputBox.createTextRange();  
            r.collapse(true);  
            r.moveStart('character',start);  
            r.moveEnd('character',end-start);  
            r.select();  
        }
        return true;  
    }  
})(window);