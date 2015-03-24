jQuery(document).ready(function(){
	jQuery('.evofrontpage-tabbed-slide-left-right').each(function(){
		var theId = jQuery(this).attr('id');

		var evofpElement = jQuery('#'+theId+' .animate');
		var evofpElementLength = evofpElement.length;
		
		if(evofpElementLength>1){
			var addto = '<ul>';
			for(var i=0;i<evofpElementLength;i++){
				addto += '<li><span>'+ (i+1) + '</span></li>';
			}
			addto += '</ul>';
			addto += '<div class="clr">&nbsp;</div>';
			jQuery(addto).appendTo('#'+theId+' #evofptabs');
		}
		jQuery('#'+theId+' .evofrontpageid').css('height', jQuery('.animate:first').css('height'));
		jQuery('#'+theId+' .evofrontpageid').css('overflow', 'hidden');
		
		var elWidth = evofpElement.css('width').replace('px','');
		var elHeight = evofpElement.css('height').replace('px','');
		jQuery('#'+theId+' .anim').css('width', elWidth+'px');
		jQuery('#'+theId+' .anim').css('height', elHeight+'px');
		jQuery('#'+theId+' .anim').css('overflow', 'hidden');
	
		evofpElement.css('position', 'absolute');
		evofpElement.css('z-index', '10 !important');
		//evofpElement.css('width',mpfAnimateDiv+'%');
	
		for(var i=0; i<evofpElement.length; i++){
			var leftPos = elWidth*i;
			var leftPos = leftPos + 'px';
			jQuery(evofpElement[i]).css('left', leftPos);
		}
	
		jQuery('#'+theId+' .animate:first').addClass('activex');
		jQuery('#'+theId+' #evofptabs li:first').addClass('activex');
	
		jQuery('#'+theId+' #evofptabs li').live('click', function(){ 
			var current = jQuery('#'+theId+' #evofptabs li.activex').index();
			var index = jQuery(this).index();
			if( current != index ){
				jQuery('#'+theId+' #evofptabs li.activex').removeClass('activex');
				jQuery(this).addClass('activex'); 
	
				jQuery('#'+theId+' .evofrontpageid').animate({
					height: jQuery(evofpElement[index]).css('height')
				});

				jQuery('#'+theId+' .anim').animate({
					height: jQuery(evofpElement[index]).css('height')
				});
	
				jQuery('#'+theId+' .anim-div').animate({
					left:0-(index*elWidth)
				});
			}
			return false;
		});
	});
});
