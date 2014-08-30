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
        descSrcProperty: 'desc',
        descDstProperty: 'h3',
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
            modal.find('.modal-title').text(element.title || String.fromCharCode(160));
            modal.find('.modal-body').append(element);
            
            /****  Load gallery specific additions ****/

            var desc = modal.find('.modal-desc');            
            //Initialize popover
            desc.popover({html:true});
            //Hack: Lookup map info by title, maps is a global variable set in the root mapgallery template file
            $.each(maps, function (index, map) {
                if (map.title == element.title) {
                    var pophtml = "";
                    pophtml += "<p><i>Category</i>: "+map.category+"</p>";
                    pophtml += "<p><a href='"+map.map_url+"' class='btn btn-default' target='_window'>Go to map</a> ";
                    pophtml += "<a href='"+map.large+"' class='btn btn-default' target='_window'>View high res</a></p>";
                    pophtml += "<p><i>Description</i>: "+map.desc+"</p>";
                    pophtml += "<p><i>Author</i>: "+map.name;
                    if (map.twitter) {
                        pophtml += " <a href='http://twitter.com/"+map.twitter+"' target='_window'><img class='auth-tweet' src='https://2014.foss4g.org/wp-content/themes/foss4g-theme/img/social-twitter.svg' alt='Twitter'></a></p>";
                    }
                    pophtml += "<p><i>Other contributors</i>: "+map.name2+"</p>";
                    pophtml += "<p><i>Organization</i>: "+map.org+"</p>";
                    if (map.license == "Prefer not to specify license") {
                        map.license = "All imagery and files are copyrighted by their owners, used here with permission";
                    } else {
                        map.license = "<a href='http://creativecommons.org/licenses/by-nc-nd/4.0/'>"+map.license+"</a>";
                    }
                    pophtml += "<p><i>License</i>: "+map.license+"</p>";
                    pophtml += "</p>"
                    desc.attr("data-content", pophtml);
                    return false;
                }            
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
