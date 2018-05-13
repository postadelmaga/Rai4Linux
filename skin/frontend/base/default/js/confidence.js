jQuery(document).ready(function () {
    initServicesTabs();
});
jQuery()
{
    var servicesTab = -1;

    function initServicesTabs() {
        jQuery("#confidence li").each(function (i) {

            jQuery(this).hover(function () {
                serviceTabOpen(i);
            }, function () {
                servicesTab = -1;
                stto = setTimeout(serviceTabClose(i), 100);
            });
        });

        var position = jQuery("#confidence").position();
        var top = position.top - 47;
        var left = position.left;

        jQuery(".confidencePop").each(function () {
            jQuery(this).css('top', top);
            jQuery(this).css('left', left);
        });

//        jQuery("#confidence").hover(function () {
//                clearTimeout(stto)
//            }, function () {
//                servicesTab = -1;
//                serviceTabClose();
//            }
//        );
    }

    function serviceTabOpen(i) {
        serviceTabClose();

        if (!jQuery("#confidence li:eq(" + i + ")").hasClass("off")) {
            servicesTab = i;
            jQuery("#confidence").addClass("pop" + i);
            jQuery(".confidencePop").eq(i).show();
        }
    }

    function serviceTabClose(i) {
        servicesTab == -1 ? jQuery("#confidence").removeClass() && jQuery(".confidencePop").hide() : jQuery(".confidencePop").eq(i).hide();
    }
}