jQuery(document).ready(function(){
	jQuery('.evofrontpage-tabbed-fade-in-out').each(function(){
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
	
		evofpElement.hide();
		jQuery('#'+theId+' .animate:first').show();
		jQuery('#'+theId+' .animate:first').addClass('activex');
		jQuery('#'+theId+' #evofptabs li:first').addClass('activex');
		evofpElement.css('position','absolute');
		//evofpElement.css('width',mpfAnimateDiv+'%');
		jQuery('#'+theId+' .evofrontpageid').css('height', jQuery('.animate:first').css('height'));
	
		jQuery('#'+theId+' #evofptabs li').live('click', function(){ 
			var current = jQuery('#'+theId+' #evofptabs li.activex').index();
			var index = jQuery(this).index();
			if( current != index ){
				jQuery('#'+theId+' #evofptabs li.activex').removeClass('activex');
				jQuery(this).addClass('activex'); 
				
				jQuery('#'+theId+' .evofrontpageid').animate({
					height: jQuery(evofpElement[index]).css('height')
				},
				{
					step: function(now, fx) {
						jQuery(evofpElement[current]).fadeOut();
						jQuery(evofpElement[index]).fadeIn();
					}
				});
			}
			return false;
		});
	});
});