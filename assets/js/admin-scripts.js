(function($) {

	$(document).ready(function(e){

		xpushSettingsForm();
		xpushTooltips();
		xpushUploadImage();
		xpushDatepicker();
		xpushMultiselectInit();
		xpushWidgetInit();

		function xpushDatepicker(){
			$picker = $('.datepicker');
			if ($picker.length){

				var dateSettings = {
					closeText: closeText,
					prevText: prevText,
					nextText: nextText,
					currentText: currentText,
					monthNames: monthNames,
					monthNamesShort: monthNamesShort,
					dayNames: dayNames,
					dayNamesShort: dayNamesShort,
					dayNamesMin: dayNamesMin,
					weekHeader: weekHeader,
					dateFormat: 'yy-mm-dd',
					firstDay: 1,
					showAnim: 'slideDown',
					isRTL: false,
					showMonthAfterYear: false,
					yearSuffix: ''
				};
				$.timepicker.regional['ru'] = {
					timeOnlyTitle: timeOnlyTitle,
					timeText: timeText,
					hourText: hourText,
					minuteText: minuteText,
					secondText: secondText,
					millisecText: millisecText,
					timezoneText: timezoneText ,
					currentText: currentTimeText,
					closeText: closeTimeText,
					timeFormat: 'HH:mm',
					amNames: ['AM', 'A'],
					pmNames: ['PM', 'P'],
					isRTL: false
				};
				$.timepicker.setDefaults($.timepicker.regional['ru']);

				$picker.datetimepicker(dateSettings);
			}
		}

		function xpushTooltips(){
			$tooltips = $('.tooltip');
			if ($tooltips.length){
				$tooltips.darkTooltip({gravity : 'west', animation: 'fadeIn'});
			}
		}

		function xpushSettingsForm(){
			var $form = $('.xpush__form'),
				$submit = $form.find('.submit'),
				$checkBtn = $form.find('#check-key-btn'),
				$deleteBtn = $form.find('#remove-key-btn'),
				$apiKeyInput = $form.find('[name="api-key"]');
			$checkBtn.on('click', function(e){
				e.preventDefault();
				if ($apiKeyInput.val().length < 38){
					$apiKeyInput.addClass('error');
				}
				else {
					$form.find('[name="form-action"]').val('check-key');
					$form.unbind('submit').submit();
				}
			});
			$deleteBtn.on('click', function(e){
				e.preventDefault();
				$apiKeyInput.val('').prop('readonly', false);
				$checkBtn.show();
				$(this).hide();
			});
			$form.on('submit', function(e){
				e.preventDefault();
				var $required = $(this).find('.required'),
					error = 0;
				$required.each(function(indx, item){
					if ($(item).val().length < 1){
						error++;
						$(item).addClass('error');
					}
					else {
						$(item).removeClass('error');
					}
				}).promise().done( function(){
					if (error == 0){
						$form.unbind('submit').submit();
					}
				});
			});
		}

		function xpushUploadImage(){

			var $mediaLink  = $('#xpush_media_manager'),
				$formIcon = $('[name="psx_site_icon"]');

			if ($mediaLink.length) {
				$mediaLink.click(function(e) {
					e.preventDefault();
					var media_frame;
					if (media_frame){
						media_frame.open();
					}
					media_frame = wp.media({
						multiple : false,
						library : {
							type : 'image'
						}
					});
					media_frame.on('close',function() {
						var selection =  media_frame.state().get('selection'),
							gallery_ids = new Array(),
							index = 0;
						selection.each(function(attachment) {
							gallery_ids[index] = attachment['id'];
							index++;
						});
						var ids = gallery_ids.join(",");
						xpushRefreshImage(ids);
					});
					media_frame.open();
				});
			}

		}

		function xpushRefreshImage(image_id){
			var data = {
				action: 'xpush_image_upload',
				id: image_id},
				$formIcon = $('[name="psx_site_icon"]'),
				$imageUpload = $('.upload-image'),
				error = '';
			jQuery.get(ajaxurl, data, function(response) {
				if (response.success === true) {
					if (!(response.data.ext && /^(jpg|png|jpeg|JPG|PNG|JPEG)$/.test(response.data.ext))){
						error = 'valid_file';
					}
					if (response.data.width = response.data.height > 192) {
						error = 'image_size';
					}
					if (error.length > 0) {
						$('#modal-trigger').trigger('click');
						$('#TB_ajaxContent').html('<p class="xpush__popup">' + xpushUploadMsg[error] + '</p>');
					}
					else {
						$imageUpload.css('background', 'url(' + response.data.url + ') no-repeat');
						$formIcon.val(response.data.url);
					}
				}
				else {
					return false;
				}
			});
		}

		function xpushMultiselectInit() {
			$select = $('.b-multiselect');
			var valuesNames = ['xpush_sites','xpush_regions','xpush_langs','xpush_tags'];
			var formValues = [[],[],[],[]];
			if ($select.length){
				$select.multiselect({
					enableHTML : false,
					numberDisplayed: 1,
					maxHeight: 180,
					includeSelectAllOption: false,
					enableFiltering: true,
					buttonText: function(options, select) {
						if (options.length === 0) {
							return noOptionText;
						}
						else {
							return optionText + ' ' + options.length;
						}
					},
					onChange: function(option, checked, select) {
						var valueString = '';
						var inputValue, count = 0;
						var inputName = $(option).attr('data-option');
						var nameIndex = valuesNames.lastIndexOf(inputName);

						var $lis = $(option).parents('select').next('.btn-group').find('li');

						$lis.each(function(indx, item){
							if ($(item).hasClass('active')){
								inputValue = $(item).find('input[type="checkbox"]').val();
								if (indx < $lis.length - 1 && count > 0) {
									valueString += ',';
								}
								valueString += inputValue;
								count++;
							}
						}).promise().done(function() {
							for (var i = 0; i < formValues[nameIndex].length; i++) {
								valueString += formValues[nameIndex][i];
								if (i < formValues[nameIndex].length - 1 && formValues[nameIndex].length > 1) {
									valueString += ',';
								}
							}
							$('[name="' + inputName + '"]').val(valueString);
						});
					}
				});
			}
		}

		function xpushWidgetInit(){
			var $widget  = $('.xpush__widget'),
				$check = $widget.find('[name="xpush_send"]');

			if ($check.is(':checked')) {
				$widget.find('.hidden-block').show();
			}
			else {
				$widget.find('.hidden-block').hide();
			}
			if ($widget.length) {
				$check.on('change', function(){
					if ($(this).is(':checked')) {
						$widget.find('.hidden-block').show();
					}
					else {
						$widget.find('.hidden-block').hide();
					}
				});
			}
		}
	});

})( jQuery );
