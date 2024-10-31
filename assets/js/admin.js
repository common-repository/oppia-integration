( function ( $ ) {
    var configs = php_vars.config_list;
    "use strict";
    $('#config_to_delete').val($('#oppia_fetcher_configs').val());
    var tabName = getParameterByName('tab',window.location.href);
    if(tabName == 'null' || tabName == null) {
        tabName = 'selection_settings';
    }

    $('#oppia_fetcher_configs').on('change',function(e){
        var searchPart = '?page=oppia&tab=' + tabName + '&config='+ $(this).val();
        window.location.href = window.location.origin + window.location.pathname + searchPart;
    });

    $('input[id^=section-]').on('change',function(){
        if(typeof $(this).attr('checked') === 'undefined' || $(this).attr('checked') === false) {
            $('#section_quantity_' + $(this).val()).val('');
        }

    });
    $('#delete').on('click', function (event) {

        var searchPart = '?page=oppia&tab=' +  tabName + '&config='+ Object.keys(configs)[0];
        $('#_wp_http_referer').val(window.location.origin + window.location.pathname + searchPart);
        $('#oppia-fetcher-settings-form').submit();
    });

    $('#submit').on('click', function (event) {

        if($('input[name=oppia_newconfig]').val() != '' ){
            var searchPart = '?page=oppia&tab=' + tabName + '&config='+ $('input[name=oppia_newconfig]').val();
            $('#_wp_http_referer').val(window.location.origin + window.location.pathname + searchPart);
            $('#oppia-fetcher-settings-form').submit();
        }

    });

}( jQuery ) );

function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}
