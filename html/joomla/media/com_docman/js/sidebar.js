
Koowa.Sidebar = new Class({

    Implements: Options,

    options: {
        minHeight: 200,
        sidebar: '.sidebar',
        target: '.sidebar-inner',
        observe: false, //Pass a css selector if the content area isn't the sidebars next sibling DOM element
        affix: false, //If Bootstrap's Affix plugin is loaded, this option will make the sidebar scroll with the view
        offset: {top: 0, bottom: 0, callback: function(){}}
    },

    initialize: function(options){

        this.setOptions(options);

        this.sidebar = document.getElement(this.options.sidebar);

        this.target   = this.sidebar.getElement(this.options.target);
        this.siblings = this.target.getAllNext();

        this.observe = this.options.observe ? document.getElement(this.options.observe) : this.sidebar.getNext();

        //Setup the inner container
        this.target.setStyle('overflow', 'auto');

        //This offset we can assume is static, so we only calculate it once
        this.offset = this.target.getPosition().y - this.observe.getPosition().y;

        //Check if the height is sufficient
        if(this.options.affix) this.options.affix = this.observe.getDimensions().height > window.getHeight();

        this.setHeight();

        if(this.options.affix && this.observe.getDimensions().height) {
            jQuery(this.sidebar).affix({
                offset: {
                    top: jQuery.proxy(function(){
                        return this.sidebar.getParent().getCoordinates().top - this.options.offset.top;
                    }, this),
                    bottom: jQuery.proxy(function(){
                        return document.getScrollHeight() - this.observe.getCoordinates().bottom + this.options.offset.bottom;
                    }, this)
                }
            });
        }

        window.addEvent('resize', this.setHeight.bind(this));
    },

    setHeight: function(){

        //This offset we can't assume never changes
        var offset = 0;
        if(this.siblings) {
            this.siblings.each(function(sibling){
                offset += sibling.getHeight();
            });
        }

        if(this.options.affix) {
            if(this.options.offset.callback && this.options.offset.callback.call) this.options.offset.callback.call(this);

            //Making sure it's positioned correctly
            jQuery(window).trigger('scroll.affix.data-api');

            //Set the right horizontal offset, this changes as the sidebar collapses in responsive layouts
            jQuery(this.sidebar).css('left', this.sidebar.getParent().getCoordinates().left+1)
                                .css('top', this.options.offset.top);
            offset += this.options.offset.top + this.options.offset.bottom;
        }

        var height = this.observe.getDimensions().height - this.offset - offset;
        if(this.options.affix) {
            height = Math.min(window.getHeight() - this.offset - offset, height);
        }
        this.target.setStyle('height', Math.max(height, this.options.minHeight));
    },

    // Internal shortcut to the available height, not the current height of the sidebar
    _getHeight: function(){
        return window.getHeight() - this.options.offset.top;
    }
});