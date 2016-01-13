function CapsuleCmsControlChooseFile(o) {
    var container = o.parentNode;
    var label = container.getElementsByTagName('span')[0];
    var value = o.value;
    var exists;
    var splitA = value.split('\\');
    var splitB = value.split('/');
    if (splitA.length > splitB.length) {
        var t = document.createTextNode(splitA[splitA.length-1]);
    } else {
        var t = document.createTextNode(splitB[splitB.length-1]);
    }
    exists = label.firstChild;
    if (exists) {
        label.removeChild(exists);
    }
    label.appendChild(t);
}