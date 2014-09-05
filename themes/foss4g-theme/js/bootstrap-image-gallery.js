/*
 * Bootstrap Image Gallery 3.0.1
 * https://github.com/blueimp/Bootstrap-Image-Gallery
 *
 * Copyright 2013, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

/*global define, window */

(function (factory) {
    'use strict';
    if (typeof define === 'function' && define.amd) {
        define([
            'jquery',
            './blueimp-gallery'
        ], factory);
    } else {
        factory(
            window.jQuery,
            window.blueimp.Gallery
        );
    }
}(function ($, Gallery) {
    'use strict';

    $.extend(Gallery.prototype.options, {
        useBootstrapModal: true,
        onopened: function(){console.log('open!')}
    });

    var close = Gallery.prototype.close,
        imageFactory = Gallery.prototype.imageFactory,
        videoFactory = Gallery.prototype.videoFactory,
        textFactory = Gallery.prototype.textFactory;

    $.extend(Gallery.prototype, {

        modalFactory: function (obj, callback, factoryInterface, factory) {
            if (!this.options.useBootstrapModal || factoryInterface) {
                return factory.call(this, obj, callback, factoryInterface);
            }
            var that = this,
                modalTemplate = this.container.children('.modal'),
                modal = modalTemplate.clone().show()
                    .on('click', function (event) {
                        // Close modal if click is outside of modal-content:
                        if (event.target === modal[0] ||
                                event.target === modal.children()[0]) {
                            event.preventDefault();
                            event.stopPropagation();
                            that.close();
                        }
                    }),
                element = factory.call(this, obj, function (event) {
                    callback({
                        type: event.type,
                        target: modal[0]
                    });
                    modal.addClass('in');
                }, factoryInterface);
            modal.find('.modal-title').html(element.title || String.fromCharCode(160));
            modal.find('.modal-body').append(element);
            
            /****  Load gallery specific additions ****/

            //Hack: Lookup map info by title, maps is a global variable set in the root mapgallery template file
            var cur_map = null;
            var pophtml = "";
            $.each(globals.maps, function (index, map) {
                if (map.title.replace(/["']/g, "&#39;") == element.title) {
                    cur_map = map;

                    pophtml += "<p><i>View</i>:</p>";
                    pophtml += "<p><a href='"+map.map_url+"' class='btn btn-default btn-more' target='_window'>map</a> ";
                    pophtml += "<a href='"+map.large+"' class='btn btn-default btn-more' target='_window'>jpg</a> ";
                    pophtml += "<a href='"+map.orig+"' class='btn btn-default btn-more' target='_window'>png</a> ";
                    pophtml += "<a href='"+map.orig+"' class='btn btn-default btn-more' target='_window'>sources</a>";
                    if (map.other_url) {
                        pophtml += "<a href='"+map.other_url+"' class='btn btn-default btn-more' target='_window'>View sources</a>";                        
                    }
                    pophtml += "</p>";

                    pophtml += "<p><i>Categories</i>:</p>";
                    pophtml += "<p>";
                    $.each(map.category, function (index, cat) {
                        if (cat == "Open Source software integration") { cat = "open software"; }
                        if (cat == "Open Source data integration") { cat = "open data"; }
                        if (cat == "Web Map Application") { cat = "web map"; }
                        if (cat == "Static Map") { cat = "static map"; }
                        pophtml += "<span class='badge'>" + cat + "</span> ";
                    });
                    pophtml += "</p>";

                    pophtml += "<p class='descp expandable'><i>Description</i>: "+map.desc+"</p>";
                    pophtml += "<p><i>Author</i>: "+map.name;
                    
                    if (map.twitter) {
                        pophtml += " <a href='http://twitter.com/"+map.twitter+"' target='_window'><img class='auth-tweet' src='https://2014.foss4g.org/wp-content/themes/foss4g-theme/img/social-twitter.svg' alt='Twitter'></a>";
                    }
                    
                    if (map.name2) {
                        pophtml += ", "+map.name2;
                    }
                    pophtml += "</p>";

                    if (map.org) {
                        pophtml += "<p><i>Organization</i>: "+map.org+"</p>";
                    }
                    
                    if (map.license == "Prefer not to specify license") {
                        map.license = "All imagery and files copyrighted unless otherwise stated";
                    } else {
                        map.license = "<a href='http://creativecommons.org/licenses/by-nc-nd/4.0/'>"+map.license+"</a>";
                    }
                    pophtml += "<p><i>License</i>: "+map.license+"</p>";

                    //Check if already voted for this map and set accordingly
                    var vote_button = modal.find('.modal-vote')
                    if (globals.votes && globals.votes[map.id.toString()]) {
                        //Set to voted state
                        vote_button.addClass('btn-success');
                        vote_button.addClass('disabled');
                        var voteicon = $(vote_button).find('.voteglyph');
                        voteicon.removeClass('glyphicon-thumbs-up');
                        voteicon.addClass('glyphicon-ok');
                    } else {
                        //Set click handler
                        vote_button.on('click', cur_map, doVote);
                    }

                    return false;
                }            
            });

            //Initialize popover
            var desc = modal.find('.modal-desc');

            //Wait until popover elements load into DOM, then adjust
            desc.on('mouseup',function() {                
                setTimeout(function() {
                    $('.expandable').expander({'slicePoint': 150});
                }, 500);
            });            

            desc.popover({
                html:true,
                content: pophtml
            });            

            return modal[0];
        },

        imageFactory: function (obj, callback, factoryInterface) {
            return this.modalFactory(obj, callback, factoryInterface, imageFactory);
        },

        videoFactory: function (obj, callback, factoryInterface) {
            return this.modalFactory(obj, callback, factoryInterface, videoFactory);
        },

        textFactory: function (obj, callback, factoryInterface) {
            return this.modalFactory(obj, callback, factoryInterface, textFactory);
        },

        close: function () {
            this.container.find('.modal').removeClass('in');
            close.call(this);
        }

    });

}));
