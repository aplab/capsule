function CapsuleCmsMainMenu(instance_name) 
{
    this.instanceName = instance_name;
    this.wrapper = document.getElementById(this.instanceName);
    this.container = document.getElementById(this.instanceName + '-container');
    this.activated = false;
    this.puncts = new Array();
    
    this.initSubpuncts = function(punct) {
        var sub_container = document.getElementById(punct.id + '-sc'); 
        if (!sub_container) {
            return false;
        }
        var child = sub_container.childNodes;
        for (var i = 0; i < child.length; i++) {
            if (child[i].tagName && child[i].className) {
                if (child[i].className == 'sub-punct') {
                    this.initSubpuncts(child[i]);
                    child[i].capsule = this;
                    child[i].onclick = function(event) {
                        event = event || window.event;
                        if (event.stopPropagation) {
                            event.stopPropagation()
                        } else {
                            event.cancelBubble = true
                        }
                        var child = document.getElementById(this.id + '-f');
                        if (child) {
                            child.submit();
                            var punct = $(this).closest('.punct');
                            $('#' + punct.attr('id') + '-sp').css({
                                display: 'none'
                            });
                            this.capsule.activated = false;
                        }
                    }
                    child[i].onmouseover = function() {
                        this.className = this.className + ' sub-punct-hover';
                        var child = document.getElementById(this.id + '-sp');
                        if (child) {
                            child.style.display = 'block';
                            var arrow = document.getElementById(this.id + '-a');
                            arrow.className = 'sub-punct-arrow-hover';
                        }
                    }
                    child[i].onmouseout = function() {
                        this.className = this.className.replace(' sub-punct-hover', '');
                        var child = document.getElementById(this.id + '-sp');
                        if (child) {
                            child.style.display = 'none';
                            var arrow = document.getElementById(this.id + '-a');
                            arrow.className = 'sub-punct-arrow';
                        }
                    }
                    continue;
                }
                if (child[i].tagName.toLowerCase() == 'div' && child[i].className == 'sub-punct-disabled') {
                    child[i].onclick = function(event) {
                        event = event || window.event;
                        if (event.stopPropagation) {
                            event.stopPropagation()
                        } else {
                            event.cancelBubble = true
                        }
                    }
                    continue;
                }
                if (child[i].tagName.toLowerCase() == 'div' && child[i].className == 'delimiter') {
                    child[i].onclick = function(event) {
                        event = event || window.event;
                        if (event.stopPropagation) {
                            event.stopPropagation()
                        } else {
                            event.cancelBubble = true
                        }
                    }
                    continue;
                }
            }
        }
    }
    
    this.hideAllPuncts = function() {
        var child;
        for (var i = 0; i < this.puncts.length; i++) {
            this.puncts[i].className = 'punct';
            child = document.getElementById(this.puncts[i].id + '-sp');
            if (child) {
                child.style.display = 'none';
            }
        }
    }
    
    this.initPuncts = function() {
        var child = this.container.childNodes;
        var sizeof = child.length;
        for (var i = 0; i < sizeof; i++) {
            if (child[i].tagName && child[i].className) {
                if (child[i].tagName.toLowerCase() == 'div' && child[i].className == 'punct') {
                    this.puncts.push(child[i]); 
                    this.initSubpuncts(child[i]);
                    child[i].capsule = this;
                    child[i].onclick = function() {
                        if (this.capsule.activated) {
                            this.capsule.activated = false;
                            this.className = 'punct';
                            var child = document.getElementById(this.id + '-sp');
                            if (child) {
                                child.style.display = 'none';
                            }
                        } else {
                            this.capsule.activated = true;
                            this.className = 'punct punct-hover';
                            var child = document.getElementById(this.id + '-sp');
                            if (child) {
                                child.style.display = 'block';
                            }
                        }
                    }
                    child[i].onmouseover = function() {
                        this.capsule.hideAllPuncts();
                        this.className = 'punct punct-hover';
                        if (this.capsule.activated) {
                            var child = document.getElementById(this.id + '-sp');
                            if (child) {
                                child.style.display = 'block';
                            }
                        }
                    }
                    child[i].onmouseout = function() {
                        this.className = 'punct';
                        var child = document.getElementById(this.id + '-sp');
                        if (child) {
                            child.style.display = 'none';
                        }
                    }
                }
            }
        }
    }
    
    this.initPuncts();
}