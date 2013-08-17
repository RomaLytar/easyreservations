var errors_state = false;
var submit_state = false;
function easyreservations_send_validate(y,form){
	if(!errors_state || (!submit_state && y == 'send')){
		errors_state = true;
		submit_state = false;
		var errornr = 1, custom = '', ids = false, theid = '';
		if(y == "send"){
            submit_state = true;
            easyOverlayDimm(0);
        }
		jQuery("#easy-show-error-div").addClass('hide-it');
		jQuery("[id^='easy-form-'],[id^='easy-custom-']").removeClass('form-error');
		jQuery("label[id^='easy-error-field-']").fadeOut("slow", function(){
			jQuery(this).remove();
		});
        jQuery('#'+form+' #easy-show-error').innerHTML = '';
        if(y) var mode = y;
        else mode = 'normal';

        if(jQuery('#'+form+' input[name="editID"]').length > 0) theid = jQuery('#'+form+' input[name="editID"]').val();
        if(easyReservationIDs) ids = easyReservationIDs;

        jQuery("[id^='easy-custom-req-']").each ( function (i){
            if(custom.indexOf(this.id+',') >= 0) return;
            if(this.value == '') custom +=	this.id + ',';
            else if(this.type == 'checkbox' && this.checked == false) custom +=	this.id + ',';
            else if( this.type == 'radio' && this.checked == false) custom += this.id + ',';
        });

		var data = easy_get_data(form);
        data['action'] = 'easyreservations_send_validate';
        data['customs'] = custom;
        data['mode'] = mode;
        data['id'] = theid;
        data['ids'] = ids;

		var the_error_field = 'easy-form-units';
		if(document.getElementById('easy-form-to')) the_error_field = 'easy-form-to';

		if(errornr == 1){
			jQuery.post(easyAjax.ajaxurl , data, function(response) {
        if(y !== "send" && submit_state)return;
				var errornr = 0;
				var warning = '';
                var elem  = '';
				if(response != '' && response != null && response != 1){
					errornr++;
					if(mode == 'send' && response.length > 0) jQuery("#easy-show-error-div").removeClass('hide-it');
					var warningli = '';
                    document.getElementById('easy-show-error').innerHTML = '';
					for(var i = 0; i < response.length; i++){
						var field = response[i];
						i++;
						var error = response[i];
						if(field == 'date'){
							jQuery('#easy-form-from').addClass('form-error');
							jQuery('#'+the_error_field ).addClass('form-error');
							warning = '<label class="easy-show-error" id="easy-error-field-'+field+'" for="'+the_error_field+'">'+error+'</label>'
							if(document.getElementById(the_error_field )) elem = jQuery('#' +the_error_field ).parent().get(0);
							else elem = jQuery('#easy-form-from').parent().get(0);
							if(elem && elem.tagName == 'SPAN'){
								jQuery(elem).after(warning);
							} else {
								if(document.getElementById(the_error_field )) jQuery('#'+the_error_field ).after(warning);
								else jQuery('#easy-form-from').after(warning);
							}
							if(mode == 'send'){
								warningli = '<li><label for="'+the_error_field+'">'+error+'</label></li>'
								document.getElementById('easy-show-error').innerHTML += warningli;
							}
						} else {
                            if(field == 'easy-form-to' && field != the_error_field) field = the_error_field;
							jQuery('#'+field + ':last').addClass('form-error');
							warning = '<label for="'+field+'" class="easy-show-error" id="easy-error-field-'+field+'">'+error+'</label>'
							if(mode == 'send'){
								warningli = '<li><label for="'+field+'">'+error+'</label></li>'
								document.getElementById('easy-show-error').innerHTML += warningli;
                            }
							if(field == 'easy-form-captcha') field = 'easy-form-captcha-img';
							elem = jQuery('#'+field).parent().get(0);
							if(elem && elem.tagName == 'SPAN') jQuery(elem).after(warning);
							else jQuery('#'+field).after(warning);
						}
						jQuery("label[class=easy-show-error]").hide();
						jQuery("label[class=easy-show-error]").fadeIn("slow");
					}
				} else {
                    //jQuery(".easyFrontendFormular .easy-button").removeClass('deactive1').removeAttr('disabled');
                }

				errors_state = false;
				if(errornr == 0 && mode == 'send'){
					jQuery('*[id^="custom_price"]:checked,select[id^="custom_price"],input[id^="custom_price"][type="radio"]').each(function(){
						var addprice = this;
						var Type = this.type;
                        var explodenormalprice = ''

						if(Type == "select-one"){
							explodenormalprice = this.value.split(':');
							addprice = jQuery(this).find('option:selected');
						} else if(Type == "radio" &&  this.checked != undefined && this.checked){
							explodenormalprice = this.value.split(':');
						} else if(Type == "checkbox" &&  this.checked){
						    explodenormalprice = this.value.split(':');
						} else if(Type == "hidden"){
							explodenormalprice = this.value.split(':');
						} else return;
						var fieldprice = explodenormalprice[1];
						fieldprice = fakeIfStatements(fieldprice, data['persons'], data['childs'], data['tnights'], data['room']);
						if(fieldprice !== false){
							if(!isNaN(parseFloat(fieldprice)) && isFinite(fieldprice)){
								var classname = '';
								if(this.className && this.className != '') classname = ':'+this.className;
								jQuery(addprice).attr('value', explodenormalprice[0]+':'+fieldprice+classname);
							}
						}
					});
					if(easyReservationAtts['multiple'] == 0){
						document.getElementById('easyFrontendFormular').submit();
					} else {
						if(easyReservationEdit) easyFormSubmit();
						else easyInnerlay(1);
					}
				} else easyOverlayDimm(1);
			});
		}
	}
}