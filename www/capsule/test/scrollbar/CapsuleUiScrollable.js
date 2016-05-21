/**
 * Created by polyanin on 20.05.2016.
 */
function CapsuleUiScrollable(instance_name)
{
    var scrollable = this;

    var wrapper = $('#' + instance_name);
    var container = wrapper.find('.capsule-ui-scrollable-container');
    var content = wrapper.find('.capsule-ui-scrollable-content');
    var scrollbar = wrapper.find('.capsule-ui-scrollable-scrollbar');

    $(window).resize(function() {
        recalc();
    })

    var recalc = function() {
        var wh = wrapper.height();
        var ch = content.height();
        if (ch > wh) {
            scrollbar.show();
        } else {
            scrollbar.hide();
        }
    }
}
$(document).ready(function() {
    new CapsuleUiScrollable('test-scrollable');
});