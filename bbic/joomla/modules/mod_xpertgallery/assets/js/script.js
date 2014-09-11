/**
 * @package Xpert Gallery
 * @version 2.2
 * @author ThemeXpert http://www.themexpert.com
 * @copyright Copyright (C) 2009 - 2011 ThemeXpert
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */

jQuery(window).load(function() {
    // init Isotope
    var container = jQuery('.tx-gallery-container').isotope({
        itemSelector: '.tx-gallery-container li',
        layoutMode: 'fitRows',
        getSortData: {
          title: '.tx-gallery-title',
          date: '.tx-gallery-date'
        }
    });

    // bind filter button click
    jQuery('.tx-gallery-filters').on( 'click', 'li', function() {
        var filterValue = jQuery( this ).attr('data-filter');
        container.isotope({ filter: filterValue });
        // Assign active class
        jQuery(this).siblings().removeClass('active');
        jQuery(this).addClass('active');
    });

     // bind sort button click
    jQuery('.tx-gallery-sort').on( 'click', 'li', function() {
        var sortValue = jQuery(this).attr('data-sort');
        container.isotope({ sortBy: sortValue });
        // Assign active class
        jQuery(this).siblings().removeClass('active');
        jQuery(this).addClass('active');
    });

    //Magnific popup options
     jQuery('.tx-gallery-image a, .tx-gallery-image-preview').magnificPopup({type:'image'});
     jQuery('.tx-gallery-image-link').magnificPopup({
        type: 'ajax',
        alignTop: true,
        overflowY: 'scroll',
        closeOnBgClick : false
    });
});