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
    
    /**
     * Ниже универсальное решение на языке JavaScript, которое учитывает 3 
     * способа определения, в каком окне браузера открыта страница.
     * 
     * @param void
     * @return boolean
     */
    Capsule.isFramed = function() {
        var isFramed = false;
        try {
          isFramed = window != window.top || document != top.document || self.location != top.location;
        } catch (e) {
          isFramed = true;
        }
        return isFramed;
    }
    
    if (Capsule.isFramed()) {
        var name = window.name;
        if (!name) return;
        var p = window.parent;
        if ('object' != typeof(p)) return;
        var d = window.parent.document;
        if ('object' != typeof(d)) return;
        $(document).ready(function() {
            $(this).click(function() {
                $('#' + name, window.parent.document).mousedown();
            });
        });
    }
})(window);