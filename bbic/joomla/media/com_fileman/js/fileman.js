/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

!function ($) {

    if(!this.Fileman) this.Fileman = {};

    Fileman.trackEvent = function(options) {
        options = $.extend({
            category: 'FILEman',
            action: 'View',
            label: null,
            value: null,
            noninteraction: false
        }, options);

        if (typeof _gaq !== 'undefined' && _gat._getTrackers().length) {
            _gaq.push(function() {
                var tracker = _gat._getTrackers()[0];
                tracker._trackEvent(options.category, options.action, options.label, options.value, options.noninteraction);
            });
        }
    };

    // Gallery Class
    var Gallery = function(options){
        this.options = $.extend(true, {}, this.options, options);

        // Fix label heights and make rows prettier
        $(window).on('resize ready', $.proxy(this.setStyles, this));

        this.createModal(this.options.modal);
    };

    //Default class properties
    Gallery.prototype.options = {
        container: '.gallery-thumbnails',
        prefix: 'columns-',
        label_class: '.gallery-label',
        item_width: 220,
        modal: {
            selector: '.gallery-modal',
            type: 'image',
            gallery:{
                enabled:true
            },
            callbacks: {}
        }
    };

    Gallery.prototype.createModal = function(options) {
        // Create the modal
        if (options.trackViews) {
            options['callbacks']['change'] = function() {
                var path = $(this.currItem.el).data('path');

                if (path) {
                    Fileman.trackEvent({label: path});
                }
            }
        }

        $(options.selector).magnificPopup(options);
    };

    // Add classes to the gallery container
    Gallery.prototype.setStyles = function() {
        var self            =  this,
            container       = $(this.options.container),
            container_width = container.width(),
            prefix          = this.options.prefix,
            item_width      = this.options.item_width,
            label_class     = this.options.label_class,
            label_changed   = false;

        // Remove all classes if screen is small
        if (container_width < item_width) {
            this.removeClassesWithPrefix(container, prefix);
        }

        $(label_class).each(function() {
            var oldHeight = $(this).css('height');
            $(this).css('height', 'auto');
            var labelHeight = $(this).outerHeight();
            if ( labelHeight != oldHeight ) {
                label_changed = true;
            }
        });

        // Add column classes and fix the label heights per row
        var columns = Math.ceil(container_width / item_width);

        if (columns > 10) {
            columns = 10;
        }

        this.removeClassesWithPrefix(container, prefix);
        container.addClass(prefix+columns);

        // Only run when the just set container class is different than the old one
        if (label_changed == true) {
            var count = 1;

            container.find('> li').each(function(index) {
                self.removeClassesWithPrefix($(this), 'row');
                $(this).addClass('row' + count);
                self.equalizeLabelHeights($('.row' + count + ' ' + label_class));

                if ((index+1)%columns === 0) {
                    count++;
                }
            });
        }
    };

    // Remove all classes with the given prefix
    Gallery.prototype.removeClassesWithPrefix = function(collection, prefix) {
        collection.each(function(i, it) {
            var classes = it.className.split(" ").map(function (item) {
                return item.indexOf(prefix) === 0 ? "" : item;
            });
            it.className = classes.join("");
        });
    };

    // Set all labels to the maximum label height
    Gallery.prototype.equalizeLabelHeights = function(collection) {
        var maximum_height = -1;

        collection.each(function() {
            var height = parseInt($(this).css('height'), 10);

            if (maximum_height < height) {
                maximum_height = height;
            }
        });

        collection.each(function() {
            $(this).css('height', maximum_height);
        });

        return this;
    };

    //Attach class to global namespace
    Fileman.Gallery = Gallery;


}(window.kQuery);