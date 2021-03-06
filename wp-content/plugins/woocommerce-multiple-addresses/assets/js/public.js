(function ($) {
	$(document).ready(function () {
		var shipping_alt = $("#shipping_alt");
		var billing_alt = $("#billing_alt");    $('.state_select').attr("required","true");      
		shipping_alt.val("0");
		billing_alt.val("0");

		shipping_alt.on("change", function () {
			$.post(
				WCMA_Ajax.ajaxurl, {
					action               : 'alt_change',
					id                   : $(this).val(),
					wc_multiple_addresses: WCMA_Ajax.wc_multiple_addresses
				}, function (response) {
					$('#shipping_address_1').val(response.shipping_address_1);
					$('#shipping_address_2').val(response.shipping_address_2);
					$('#shipping_city').val(response.shipping_city);
					$('#shipping_company').val(response.shipping_company);
					$('#shipping_country').val(response.shipping_country).change();
					$("#shipping_country_chosen").find('span').html(response.shipping_country_text);
					$('#shipping_first_name').val(response.shipping_first_name);										$('#shipping_phone_number').val(response.shipping_phone_number);										$("select[name='shipping_state']").find("option[value="+response.shipping_state+"]").prop("selected",true);
					$('#shipping_last_name').val(response.shipping_last_name);
					$('#shipping_postcode').val(response.shipping_postcode);
					//$('#shipping_state').val(response.shipping_state);
					var stateName = $('#shipping_state option[value="'+response.shipping_state+'"]').text();					                      $("#select2-chosen-2").text(stateName); 					  $("#select2-chosen-3").text(stateName); 					   $("#select2-chosen-4").text(stateName); 					    $("#select2-chosen-5").text(stateName);$("#select2-chosen-6").text(stateName);						
					//$("#select2-chosen-2").find('span').html(stateName).parent().removeClass('chosen-default');
				}
			);
			return false;
		});

		billing_alt.on("change", function () {
			$.post(
				WCMA_Ajax.ajaxurl, {
					action               : 'alt_change',
					id                   : $(this).val(),
					wc_multiple_addresses: WCMA_Ajax.wc_multiple_addresses
				}, function (response) {
					$('#billing_address_1').val(response.shipping_address_1);
					$('#billing_address_2').val(response.shipping_address_2);
					$('#billing_city').val(response.shipping_city);
					$('#billing_company').val(response.shipping_company);
					$('#billing_country').val(response.shipping_country).change();
					$("#billing_country_chosen").find('span').html(response.shipping_country_text);
					$('#billing_first_name').val(response.shipping_first_name);
					$('#billing_last_name').val(response.shipping_last_name);
					$('#billing_postcode').val(response.shipping_postcode);
					$('#billing_state').val(response.shipping_state);
					var stateName = $('#billing_state option[value="'+response.shipping_state+'"]').text();
					$("#billing_state_chosen").find('span').html(stateName).parent().removeClass('chosen-default');
				}
			);
			return false;
		});
         $( "#shipping_phone_number" ).blur(function() {			 			var phone_len = $("#shipping_phone_number").val().length;			 				  $( "#shipping_phone_number" ).tooltip({								   								   position: {                                                 my: "left top",                                                 at: "right+5 top-5"								   }								   });			 			 if (phone_len<10){				   $( "#shipping_phone_number" ).tooltip( "open" );	 				   /*$( "#shipping_phone_number" ).css( "border-color", "#b85f56" );				   $( "#shipping_phone_number" ).css( "background", "#f4e7e6" );*/			 }			 if (phone_len>=10){				 				   $( "#shipping_phone_number" ).tooltip( "disable" );				    $( "#shipping_phone_number" ).attr("title","请输入正确的电话号码");				  /* $( "#shipping_phone_number" ).css( "border-color", "#84ac50" );				   $( "#shipping_phone_number" ).css( "background", "#e5eeda" );*/			 }			          });

		// wc_country_select_params is required to continue, ensure the object exists
		if ( typeof wc_country_select_params === 'undefined' ) {
			return false;
		}

		/* State/Country select boxes */
		var states_json = wc_country_select_params.countries.replace( /&quot;/g, '"' ),
			states = $.parseJSON( states_json );

		$(document).on("change", "select.country_to_state, input.country_to_state", function () {
			var country = $( this ).val(),
				$statebox = $( this ).closest( 'div' ).find( '#billing_state, #shipping_state, #calc_shipping_state' ),
				$parent = $statebox.parent(),
				input_name = $statebox.attr( 'name' ),
				input_id = $statebox.attr( 'id' ),
				value = $statebox.val(),
				placeholder = $statebox.attr( 'placeholder' );

			if ( states[ country ] ) {
				if ( states[ country ].length === 0 ) {

					$statebox.parent().hide().find( '.chosen-container' ).remove();
					$statebox.replaceWith( '<input type="hidden" class="hidden" name="' + input_name + '" id="' + input_id + '" value="" placeholder="' + placeholder + '" />' );

					$( 'body' ).trigger( 'country_to_state_changed', [country, $( this ).closest( 'div' )] );

				} else {

					var options = '',
						state = states[ country ];

					for( var index in state ) {
						if ( state.hasOwnProperty( index ) ) {
							options = options + '<option value="' + index + '">' + state[ index ] + '</option>';
						}
					}

					$statebox.parent().show();

					if ( $statebox.is( 'input' ) ) {
						// Change for select
						$statebox.replaceWith( '<select name="' + input_name + '" id="' + input_id + '" class="state_select" placeholder="' + placeholder + '"></select>' );
						$statebox = $( this ).closest( 'div' ).find( '#billing_state, #shipping_state, #calc_shipping_state' );
					}

					$statebox.html( '<option value="">' + wc_country_select_params.i18n_select_state_text + '</option>' + options );

					$statebox.val( value );

					$( 'body' ).trigger( 'country_to_state_changed', [country, $( this ).closest( 'div' )] );

				}
			} else {
				if ( $statebox.is( 'select' ) ) {

					$parent.show().find( '.chosen-container' ).remove();
					$statebox.replaceWith( '<input type="text" class="input-text" name="' + input_name + '" id="' + input_id + '" placeholder="' + placeholder + '" />' );

					$( 'body' ).trigger( 'country_to_state_changed', [country, $( this ).closest( 'div' )] );

				} else if ( $statebox.is( '.hidden' ) ) {

					$parent.show().find( '.chosen-container' ).remove();
					$statebox.replaceWith( '<input type="text" class="input-text" name="' + input_name + '" id="' + input_id + '" placeholder="' + placeholder + '" />' );

					$( 'body' ).trigger( 'country_to_state_changed', [country, $( this ).closest( 'div' )] );

				}
			}

			$( 'body' ).trigger( 'country_to_state_changing', [country, $( this ).closest( 'div' )] );

		}).change();

	});
})(jQuery);