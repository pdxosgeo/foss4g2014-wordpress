//Get parameters from URL
function getUrlParams() {
    var result = {};
    var params = window.location.search.split(/\?|\&/);
    params.forEach( function(it) {
        if (it) {
            var param = it.split("=");
            result[param[0]] = param[1];
        }
    });
    return result;
}

//Fisher-Yates-Durstenfeld shuffle
function shuffle(sourceArray) {
    for (var n = 0; n < sourceArray.length - 1; n++) {
        var k = n + Math.floor(Math.random() * (sourceArray.length - n));

        var temp = sourceArray[k];
        sourceArray[k] = sourceArray[n];
        sourceArray[n] = temp;
    }
}

function doVote(event) {
    var map = event.data;
    if (!map) {
        alert("map data not found during vote event");
    }

    $.post("/wp-admin/admin-ajax.php?action=ninja_forms_ajax_submit",
        {
            _wpnonce: "8bf1243cb4",
            _wp_http_referer: "%2Fvote-api%2F",
            _ninja_forms_display_submit: 1,
            _form_id: 4,
            ninja_forms_field_31: map.id,
            ninja_forms_field_32: $.now(),
            ninja_forms_field_33: "192.168.0.2"
        },
        function(data, textStatus, jqXHR) {
            //Turn button green to indicate success
            $(event.currentTarget).addClass('btn-success')
        }
    ).fail(function(jqXHR, textStatus, errorThrown) {
        alert(textStatus);
    });
}    