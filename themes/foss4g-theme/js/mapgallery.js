//Get parameters from URL
function getUrlParams() {
    var result = {};
    var params = window.location.search.split(/\?|\&/);
    for (var i=0; i<params.length; i++) {
        it = params[i];
        if (it) {
            var param = it.split("=");
            result[param[0]] = param[1];
        }
    };
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
  //Check if cookies enabled
  $.cookie('test_cookie', 'cookie_value', { path: '/' });
  if ($.cookie('test_cookie') == 'cookie_value') {
    globals.cookies_allowed = true;
    //Fetch votes from cookie
    globals.votes = $.cookie('mapvotes');
  }

  //Initialize votes if none in cookie
  if (!globals.votes) {    
    globals.votes = {};
    if (globals.cookies_allowed) {
        $.cookie('mapvotes', {}, { expires: 7 });        
    }
  }

  //Fetch votes from server
  jQuery.ajax({
    dataType: "json",
    url: "/map-gallery/vote-api/?action=fetch"
  }).done(function (result) {
    if (!result.ip || !result.votes) {
        alert('fetch failed');
    }
    globals.curip = result.ip;
    globals.nonce = result.nonce;
    globals.ip_votes = result.votes;
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
      checkLoad();
    });          
  });    
}

function checkLoad() {
    //fetch URL parameters
    params = getUrlParams();
    if (params.id) {
        var map_ele = $('#img'+params.id);
        if (map_ele) {
            map_ele.click();
        }
    }
}

function doVote(event) {
    //Disable the button
    $(event.currentTarget).prop('disabled', true);

    var map = event.data;
    if (!map) {
        alert("map data not found during vote event");
    }

    var curtime = $.now();
    $.post("/wp-admin/admin-ajax.php?action=ninja_forms_ajax_submit",
        {
            _wpnonce: globals.nonce,
            _wp_http_referer: "%2Fvote-api%2F?action=fetch",
            _ninja_forms_display_submit: 1,
            _form_id: 4,
            ninja_forms_field_31: map.id,
            ninja_forms_field_32: curtime,
            ninja_forms_field_33: globals.curip
        },

        //Success function
        function(data, textStatus, jqXHR) {
            //Turn button green to indicate success
            $(event.currentTarget).addClass('btn-success');
            $(event.currentTarget).addClass('disabled');
            var voteicon = $(event.currentTarget).find('.voteglyph');
            voteicon.removeClass('glyphicon-thumbs-up');
            voteicon.addClass('glyphicon-ok');

            //Store vote locally
            var vote_obj = {
                ip: globals.curip,
                mapid: map.id,
                timestamp: curtime
            };
            
            //Add vote locally
            globals.votes[map.id.toString()] = vote_obj;            
            //Update cookie
            if (globals.cookies_allowed) {
                $.cookie('mapvotes', globals.votes);
            }
        }
    ).fail(function(jqXHR, textStatus, errorThrown) {
        alert(textStatus);
        $(event.currentTarget).prop('disabled', false);
    });
}    