	jQuery(document).ready(function ($) {
		var	formSeedConfirm = $('#seed-confirm-form');
			inputName = $('#seed-confirm-name');
			inputContact = $('#seed-confirm-contact');
			inputOrder = $('#seed-confirm-order');
			inputAmount = $('#seed-confirm-amount');
            inputAccountNumber = $('input[name=seed-confirm-account-number]');
            inputDate = $('#seed-confirm-date');
            inputSlip = $('#seed-confirm-slip');
            buttonSubmit = $('#seed-confirm-btn-submit');

			var optionOrderSelected = $('#seed-confirm-order option:selected');

			$orderAmountIndexStart = optionOrderSelected.text().lastIndexOf( ':' );
			$orderAmountIndexEnd = optionOrderSelected.text().indexOf( ' ', $orderAmountIndexStart + 2 );

			newAmount = optionOrderSelected.text().substring( $orderAmountIndexStart + 2, $orderAmountIndexEnd );

			inputAmount.val( newAmount );

			inputOrder.on( 'change', function () {
				var optionOrderSelected = $('#seed-confirm-order option:selected');

				$orderAmountIndexStart = optionOrderSelected.text().lastIndexOf( ':' );
				$orderAmountIndexEnd = optionOrderSelected.text().indexOf( ' ', $orderAmountIndexStart + 2 );

				newAmount = optionOrderSelected.text().substring( $orderAmountIndexStart + 2, $orderAmountIndexEnd );

				inputAmount.val( newAmount );
			});

            buttonSubmit.on( 'click', function ( event ) {
				var hasError = false;

				if ( inputName.hasClass( 'required' ) && $.trim( inputName.val() ) == '' ) {
					inputName.addClass('-invalid');
					hasError = true;
				} else {
					inputName.removeClass('-invalid');
				}

				if ( inputContact.hasClass( 'required' ) && $.trim( inputContact.val() ) == '' ) {
					inputContact.addClass('-invalid');
					hasError = true;					
				} else {
					inputContact.removeClass('-invalid');
				}

				if ( inputOrder.hasClass( 'required' ) && $.trim( inputOrder.val() ) == '' ) {
					inputOrder.addClass('-invalid');
					hasError = true;
				} else {
					inputOrder.removeClass('-invalid');
				}

				if ( inputAmount.hasClass( 'required' ) && $.trim( inputAmount.val() ) == '' ) {
					inputAmount.addClass('-invalid');
					hasError = true;
				} else {
					inputAmount.removeClass('-invalid');
				}

				if ( inputAccountNumber.hasClass( 'required' )) {
					hasError = true;
                    inputAccountNumber.addClass('-invalid');

                    inputAccountNumber.each(function(){
                        if($(this).prop('checked') == true){
                            hasError = false;
                            inputAccountNumber.removeClass('-invalid');
                        }
                    });
				}

				if ( inputDate.hasClass( 'required' ) && $.trim( inputDate.val() ) == '' ) {
					inputDate.addClass('-invalid');
					hasError = true;
				} else {
					inputDate.removeClass('-invalid');
				}

				if ( inputSlip.hasClass( 'required' ) && $.trim( inputSlip.val() ) == '' ) {
					inputSlip.addClass('-invalid');
					hasError = true;
				} else {
					inputSlip.removeClass('-invalid');
				}

				if( hasError ) {
					$(window).scrollTop($('#seed-confirm-form').offset().top);
					return;
				}else{
                    formSeedConfirm.submit();
                }

			});

         /**
          * Form Validate
          * @type {object}
          */
         $.validate({
         	borderColorOnError : '#c00',
         });

         /**
          * Slip check file upload type
          * @param  array fileExtension
          * @return string
          */
			$('#seed-confirm-slip').change( function () {
				var fileExtension = ['jpg','png','gif','pdf'];
				if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
				   alert("This is not an allowed file type. Only JPG, PNG, GIF and PDF files are allowed.");
				   this.value = '';
				   return false;
				}
         });		
	});