function CapsuleUiDialogWindow(data) 
{
    var dialogWindow = this;
    this.instanceName = data.instanceName; // id объекта
    /**
     * Static section
     */
    if ('undefined' === typeof(CapsuleUiDialogWindow.instances)) {
        CapsuleUiDialogWindow.instances = new Array();
    }
    CapsuleUiDialogWindow.getInstance = function(instance_name) {
        if ('undefined' !== typeof(CapsuleUiDialogWindow.instances[instance_name])) {
            return CapsuleUiDialogWindow.instances[instance_name];
        }
        return null;
    }
    if ('undefined' !== typeof(CapsuleUiDialogWindow.instances[this.instanceName])) {
        console.log('Instance already exists: ' + this.instanceName);
        console.error('Instance already exists: ' + this.instanceName);
    }
    CapsuleUiDialogWindow.instances[this.instanceName] = this;
    /**
     * End of static section
     */
    
    /* input parameters */
    this.left = parseInt(data.left, 10); // начальное положение по горизонтали
    this.top = parseInt(data.top, 10); // начальное положение по вертикали
    this.width = parseInt(data.width, 10); // начальная ширина окна
    this.height = parseInt(data.height, 10); // начальная высота окна
    this.opacity = parseFloat(data.opacity); // степень прозрачности
    this.hidden = data.hidden ? true : false; // не показывать, вывести как скрытое
    
    /* settings */
    this.captionHeight = 24; // Высота шапки окна
    this.workplaceHeightCorrection = -2; // Корректировка высоты рабочей области окна с учетом рамок (border)
    this.shadowCorrection = 5; // Корректировка высоты и положения области отбрасывания тени
    this.draggableAreaOffsetLeft = 0; // Расстояние от указателя мыши до левого края перемещаемого объекта в момент захвата 
    this.draggableAreaOffsetTop = 0; // Расстояние от указателя мыши до верхнего края перемещаемого объекта в момент захвата
    this.draggableAreaBorderWidth = 4;
    // Вычисление высоты рабочей области
    this.workplaceHeight = this.height - this.captionHeight + this.workplaceHeightCorrection;
    
    /* init */
    this.d = document; // Ссылка на документ
    this.get = function(id) { // alias of document.getElementById() 
        return this.d.getElementById(id);
    }
    // Статический счетчик для управления наложением окон общий для всех объектов окон
    if (typeof(CapsuleUiDialogWindow.zIndexCounter) == 'undefined') { // ещё не определен 
        CapsuleUiDialogWindow.zIndexCounter = 100000; // Начальное значение
    } else {
        CapsuleUiDialogWindow.zIndexCounter ++; // Уже был инициализирован значит для нового окна увеличим на 1
    }
    // Инициализация блока-контейнера
    this.container = this.get(this.instanceName);
    this.transparentImageUrl = $(this.container).css('background-image');// Ссылка на прозрачную картинку
    this.container.style.width = this.width + 'px';
    this.container.style.height = this.height + 'px';
    this.container.style.left = this.left + 'px';
    this.container.style.top = this.top + 'px';
    this.container.style.zIndex = CapsuleUiDialogWindow.zIndexCounter;
    if (1 != this.opacity) {
        this.container.style.opacity = this.opacity;
    }
    this.container.instance = this; // Ссылка на текущий объект  
    this.container.onmousedown = function() {
        this.instance.focus();
    }
    if (!this.hidden) { // Отображаем окно
        this.container.style.display = 'block';
    }
    /* помещаем подложку из iframe в контейнер */
    var iframe = document.createElement('iframe');
    iframe.frameBorder = 0;
    iframe.width = parseInt(this.width) + 'px';
    iframe.height = parseInt(this.height) + 'px';
    iframe.style.zIndex = -1;
    iframe.style.filter = 'progid:DXImageTransform.Microsoft.BasicImage(opacity = 0)';
    this.container.appendChild(iframe);
    // Инициализация блока-обертки элементов окна
    this.wrapper = this.get(this.instanceName + '-wrapper');
    this.wrapper.style.width = this.width + 'px';
    this.wrapper.style.height = this.height + 'px';
    // Инициализация блока тени
    this.shadow = this.get(this.instanceName + '-shadow');
    this.shadow.style.width = this.width + 'px';
    this.shadow.style.height = this.height - this.shadowCorrection + 'px';
    this.shadow.style.top = this.shadowCorrection + 'px';
    this.cellImageUrl = $(this.shadow).css('backgroundImage');// Ссылка на картинку-сетку
    this.shadow.style.backgroundImage = this.transparentImageUrl;
    // Инициализация рабочей области окна
    this.workplace = this.get(this.instanceName + '-workplace');
    this.workplace.style.height = this.workplaceHeight + 'px';
    // Инициализация кнопки "Закрыть"
    this.closeButton = this.get(this.instanceName + '-close-button');
    this.closeButton.instance = this; // Ссылка на текущий объект
    this.closeButton.onmouseover = function() {
        this.className = 'capsule-ui-dialog-window-close-button-hover';
    }
    this.closeButton.onmouseout = function() {
        this.className = 'capsule-ui-dialog-window-close-button';
    }
    this.closeButton.onmousedown = function(event) {
        event = event || window.event;
        if (event.stopPropagation) {
            // Вариант стандарта W3C:
            event.stopPropagation()
        } else {
            // Вариант Internet Explorer:
            event.cancelBubble = true
        }
        this.className = 'capsule-ui-dialog-window-close-button-active';
    }
    this.closeButton.onmouseup = function() {
        this.className = 'capsule-ui-dialog-window-close-button-hover';
    }
    this.closeButton.onclick = function() {
        this.instance.container.style.display = 'none';
    }
    // общий перемещаемый блок для перемещения всех окон
    if (typeof(CapsuleUiDialogWindow.draggableArea) == 'undefined') {
        CapsuleUiDialogWindow.draggableArea = document.createElement('div');
        CapsuleUiDialogWindow.draggableArea.style.position = 'absolute';
        CapsuleUiDialogWindow.draggableArea.style.cursor = 'default';
        CapsuleUiDialogWindow.draggableArea.style.backgroundImage = this.transparentImageUrl;
        // Флаг, который обозначает, что перемещаемый блок активен
        CapsuleUiDialogWindow.draggableAreaIsActive = false;
        for (var i = 0; i < 4; i++) {
            var e = document.createElement('div');
            e.style.position = 'absolute';
            e.style.backgroundImage = this.cellImageUrl;
            e.style.overflow = 'hidden';
            e.style.fontSize = '1px';
            CapsuleUiDialogWindow.draggableArea.appendChild(e);
        }
        CapsuleUiDialogWindow.draggableArea.style.userSelect = 'none';
        CapsuleUiDialogWindow.draggableArea.setAttribute('unselectable', 'on');
    }
    // Объект-контейнер, общий для всех окон, относительно которого они позиционируются и перемещаются 
    if (typeof(CapsuleUiDialogWindow.commonContainer) == 'undefined') {
        // Этот контейнер должен занимать всю область окна браузера, не иметь полос прокрутки
        // и менять свои размеры в зависимости от размеров окна браузера.
        // Это специфично для системы Capsule, в которой всё выводится в объект - контейнер
        CapsuleUiDialogWindow.commonContainer = this.container.parentNode;
        // Хранит предыдущий обработчик на время перемещения окна
        CapsuleUiDialogWindow.commonContainerDefaultHandlerBackup = null;
    }
    
    /**
     * Перводит окно на передний план
     * 
     * @param void
     * @return void
     */
    this.focus = function() {
        if (parseInt(this.container.style.zIndex, 10) == CapsuleUiDialogWindow.zIndexCounter) {
            return;
        }
        CapsuleUiDialogWindow.zIndexCounter ++;
        this.container.style.zIndex = CapsuleUiDialogWindow.zIndexCounter;
    }
    
    this.initDraggableArea = function() {
        var draggableArea = CapsuleUiDialogWindow.draggableArea;
        draggableArea.instance = this; // Ссылка на текущий объект окна
        draggableArea.style.zIndex = CapsuleUiDialogWindow.zIndexCounter ++;
        draggableArea.style.width = this.width + 'px';
        draggableArea.style.height = this.height + 'px';
        draggableArea.style.left = this.left + 'px';
        draggableArea.style.top = this.top + 'px';
        // draggableArea.style.opacity = .5;
        
        draggableArea.onmouseup = function() {
            this.parentNode.removeChild(this);
            this.instance.container.style.left = this.instance.left + 'px';
            this.instance.container.style.top = this.instance.top + 'px';
            CapsuleUiDialogWindow.draggableAreaIsActive = false;
            CapsuleUiDialogWindow.commonContainer.onmousemove = CapsuleUiDialogWindow.commonContainerDefaultHandlerBackup; 
            document.onselectstart = null;
        }
        var borders = draggableArea.getElementsByTagName('div');
        borders[0].style.left = '0';
        borders[0].style.top = '0';
        borders[0].style.width = this.width + 'px';
        borders[0].style.height = this.draggableAreaBorderWidth + 'px';
        
        borders[1].style.left = '0';
        borders[1].style.bottom = '0';
        borders[1].style.width = this.width + 'px';
        borders[1].style.height = this.draggableAreaBorderWidth + 'px';
        
        borders[2].style.left = '0';
        borders[2].style.top = this.draggableAreaBorderWidth + 'px';
        borders[2].style.width = this.draggableAreaBorderWidth + 'px';
        borders[2].style.height = this.height - 2 * this.draggableAreaBorderWidth + 'px';
        
        borders[3].style.right = '0';
        borders[3].style.top = this.draggableAreaBorderWidth + 'px';
        borders[3].style.width = this.draggableAreaBorderWidth + 'px';
        borders[3].style.height = this.height - 2 * this.draggableAreaBorderWidth + 'px';
        
        CapsuleUiDialogWindow.commonContainer.appendChild(draggableArea);
    }
    
    // Инициализация шапки окна
    this.caption = this.get(this.instanceName + '-caption');
    this.caption.instance = this; // Ссылка на текущий объект
    this.caption.onmousedown = function(event) {
        if (CapsuleUiDialogWindow.draggableAreaIsActive) {
            return false; // Объект занят другим окном
        }
        CapsuleUiDialogWindow.draggableAreaIsActive = true;
        event = event || window.event;
        if (event.stopPropagation) {
            // Вариант стандарта W3C:
            event.stopPropagation()
        } else {
            // Вариант Internet Explorer:
            event.cancelBubble = true
        }
        /* capture */
        this.instance.focus();
        this.instance.initDraggableArea();
        this.instance.draggableAreaOffsetLeft = event.clientX - this.instance.left;
        this.instance.draggableAreaOffsetTop = event.clientY - this.instance.top;
        CapsuleUiDialogWindow.commonContainerDefaultHandlerBackup = CapsuleUiDialogWindow.commonContainer.onmousemove;
        
        CapsuleUiDialogWindow.commonContainer.onmousemove = function(event) {
            event = event || window.event;
            var draggableArea = CapsuleUiDialogWindow.draggableArea;
            if (!draggableArea) {
                return false;
            }
            if (draggableArea.parentNode != this) {
                return false;
            }
            draggableArea.instance.left = event.clientX - draggableArea.instance.draggableAreaOffsetLeft;
            draggableArea.instance.top = event.clientY - draggableArea.instance.draggableAreaOffsetTop;
            
            if (draggableArea.instance.top < 25) {
                draggableArea.instance.top = 25;
            }
            
            if (draggableArea.instance.left < 0) {
                draggableArea.instance.left = 0;
            }
            
            var limit = $(this).width() - dialogWindow.width; 
            
            if (draggableArea.instance.left > limit) {
                draggableArea.instance.left = limit;
            }    
            
            var limit = $(this).height() - dialogWindow.height; 
                
            if (draggableArea.instance.top > limit) {
                draggableArea.instance.top = limit;
            }
               
            draggableArea.style.left = draggableArea.instance.left + 'px';
            draggableArea.style.top = draggableArea.instance.top + 'px';
            document.onselectstart = function() {
                return false;
            }
            if (document.getSelection) {
                if (document.getSelection().removeAllRanges) {
                    document.getSelection().removeAllRanges();
                }
            } else if (document.selection && document.selection.clear) {
                document.selection.clear();
            }
            return false;
        }
    }
    
    this.show = function() {
        this.container.style.display = 'block';
        this.focus();
    }
    
    this.hide = function() {
        this.container.style.display = 'none';
    }
    
    this.blink = function() {
        if (this.container.style.display == 'block') {
            this.container.style.display = 'none';
            setTimeout('CapsuleUiDialogWindow.getInstance("' + this.instanceName + '").blink()', 50);
            return;
        } else {
            this.container.style.display = 'block';
            this.focus();
        }
    }
    
    this.showCenter = function() {
        if (this.container.style.display == 'block') {
            setTimeout('CapsuleUiDialogWindow.getInstance("' + this.instanceName + '").blink()', 100);
            setTimeout('CapsuleUiDialogWindow.getInstance("' + this.instanceName + '").blink()', 200);
            setTimeout('CapsuleUiDialogWindow.getInstance("' + this.instanceName + '").blink()', 300);
            return;
        }
        var cw = parseInt($(CapsuleUiDialogWindow.commonContainer).width());
        var ch = parseInt($(CapsuleUiDialogWindow.commonContainer).height());
        this.left = (cw - this.width) / 2;
        this.top = (ch - this.height) / 2;
        if (this.left < 0) {
            this.left = 0;
        }
        if (this.top < 25) {
            this.top = 25;
        }
        this.container.style.left = this.left + 'px';
        this.container.style.top = this.top + 'px';
        this.container.style.display = 'block';
        this.focus();
    }
}