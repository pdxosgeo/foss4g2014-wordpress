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

function fetchVotes() {
  //Fetch votes
  jQuery.ajax({
    dataType: "json",
    url: "/map-gallery/vote-api/?action=fetch",
  }).done(function (result) {
    if (!result.ip || !result.votes) {
        alert('fetch failed');
    }
    globals.ip = result.ip;
    globals.votes = result.votes;
    fetchMaps();
  });
}

function fetchMaps() {
  //Fetch maps
  jQuery.ajax({
    dataType: "json",
    url: "/map-gallery/map-gallery-feed/",
  }).done(function (result) {
    //Set global maps variable to result
    globals.maps = result;
    console.log(globals);

    //fetch URL parameters
    params = getUrlParams();
    if (params.order != 'yes') {
        shuffle(globals.maps);
    }

    var grid = jQuery('#thumb-grid');
    // Add images to grid with attributes to drive modal gallery
    $.each(result, function (index, sub) {
      var griditem = jQuery.parseHTML("<div class='item col-md-4 col-sm-6 col-lg-3'><a id='img"+sub.id+"' href='"+sub.medium+"' class='thumbnail' data-gallery title=''><img src='"+sub.small+"'/></a><p class='small-title'>"+sub.title+"</p></div>");
      var esctitle = sub.title.replace(/["']/g, "&#39;");
      var aelement = $(griditem).find("#img"+sub.id.toString());
      aelement.attr('title', esctitle);
      grid.append(griditem);      
    });           
    $('#thumb-grid').imagesLoaded( function(){
      $('#thumb-grid').isotope({
        itemSelector : '.item'
      });
    });          
  });    
}

function doVote(event) {
    //Disable the button
    $(event.currentTarget).prop('disabled', true);

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
            ninja_forms_field_33: globals.ip
        },
        function(data, textStatus, jqXHR) {
            //Turn button green to indicate success
            $(event.currentTarget).addClass('btn-success')
            var voteicon = $(event.currentTarget).find('.voteglyph');
            voteicon.removeClass('glyphicon-thumbs-up');
            voteicon.addClass('glyphicon-ok');
        }
    ).fail(function(jqXHR, textStatus, errorThrown) {
        alert(textStatus);
    });
}    