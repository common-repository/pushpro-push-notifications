jQuery(document).ready(function () {
    var table = jQuery("#pushpro-stats-table");
    table.tablesorter().tablesorterPager({
        page: 0,
        size: 10,
        fixedHeight: true,
        container: jQuery("#pushpro-pager"),
    });

    jQuery('#pushpro-settings-form').on('submit', function (e) {
        if (validateApiKey()) {
            jQuery('#push-pro-notifications').html('<div class="error fade"><p>Use only alphanumeric symbols</p></div>');
            e.preventDefault();
        }
    });

    function validateApiKey() {
        return jQuery('#token').val().match(/^\w+$/) === null
    }
});