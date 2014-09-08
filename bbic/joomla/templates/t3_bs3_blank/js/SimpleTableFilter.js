(function(jQuery){
	jQuery.fn.filterTable = function () {
	this.each(function(){
        var table = jQuery(this);
        var trRowArray = tableValuesInArray(table);
        table.find('thead tr:last th').each(function (trhindex) {
            if(jQuery(this).hasClass('applyfilter')) {
                var sel = jQuery('<select class="TableFilterSelect" style="width-min:30px;;width:' + jQuery(this).width() + 'px;text-align:center;background-color:' + jQuery(this).prop("background-color") + ';"/>');
                jQuery("</br>").appendTo(jQuery(this));
                sel.appendTo(jQuery(this));
                sel.change(function () { assignChange(table,1) });
            }
        });
		tableSelectValues(table);
		});
    };
})(jQuery);

function assignChange(table,changeSelect) {
    table.find("tr:hidden").show();
    table.find('thead tr th select').each(function (selectindex) {
        var selectedVal = jQuery.trim(jQuery(this).val());
        if (selectedVal == "<Blank>" ){
			selectedVal="";
		};
        if (selectedVal != "<All>" && selectedVal != "<null>") {
            var thIndex = jQuery(this).parent().index() + 1;
            table.find('tbody tr td:nth-child(' + thIndex + ')').each(function (trindex1) {
                var trEle = jQuery(this).parent()
                if (selectedVal != jQuery.trim(jQuery(this).text())) {
                    trEle.hide();
                };
            });
        }
    });
	if(changeSelect==1)
	{
		tableSelectValues(table);
	}
};
function tableValuesInArray(table) {
    var trRowArray = new Array;
    var trCount = 0;
    table.find('tbody tr:visible').each(function (trindex) {
        var isExist = false;
        var tdCount = 0;
        jQuery(this).find('td').each(function (tdindex) {
            if (trRowArray.length <= tdindex) {
                trRowArray[tdindex] = new Array;
            }
            if (jQuery.inArray(jQuery.trim(jQuery(this).text()), trRowArray[tdindex]) < 0) {
                trRowArray[tdindex][trCount] = jQuery.trim(jQuery(this).text());
                isExist = true;
            }
        });
        if (isExist == true) { trCount++; }
    });
    return trRowArray;
};

function tableSelectValues(table) {    
    table.find('thead tr:last th select').each(function (trhindex) {	
        var tdselIndex = jQuery(this).parent().index() + 1;
        var selVal = jQuery(this).val();	
		if(selVal!="<All>" && selVal!=null){
			jQuery(this).val("<All>");
			assignChange(table,0);
		};
        var sel = jQuery(this);
        var trsArray = new Array;
        var trsArrayNo = 0;
        table.find('tbody tr:visible td:nth-child(' + tdselIndex + ')').each(function (tdsindex) {
			var tdVal=jQuery.trim(jQuery(this).text());
			if(tdVal=="" || tdVal==null){
				tdVal="<Blank>";
			}
            if (jQuery.inArray(tdVal, trsArray) < 0) {
                trsArray[trsArrayNo] = tdVal;
                trsArrayNo++;
            }
        });
        sel.empty();
        jQuery('<option />', { value: "<All>", text: "<All>" }).appendTo(sel);
        jQuery.each(trsArray, function (key, value) {
            jQuery('<option />', { value: value, text: value }).appendTo(sel);
        });
		if(selVal!="<All>" && selVal!=null){
			jQuery(this).val(selVal);
			assignChange(table,0);
		};
    });
	jQuery("#TableLabel").text("selected rows: " + table.find('tbody tr:visible').length);
};
