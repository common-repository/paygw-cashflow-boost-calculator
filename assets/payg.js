(function($){


	$(document).ready(function(){

		$('body').on('click', '#frmpayg input[name="paygReporting"]', function() {
			var val = $(this).val();

			console.log(val);

			$('.payg-inputs').hide();
			$('.payg-'+val).show();

		});

		$('#frmpayg').on('submit', function(e){
			e.preventDefault();
			var values = $(this).serializeArray();

			var proccessed_values = {};
			$.each(values, function(i, field) {
			    proccessed_values[field.name] = field.value;
			});

			var data = {
				'action': 'calculate_pay',
				'values': proccessed_values
			};

			// We can also pass the url value separately from ajaxurl for front end AJAX implementations
			$.post(pay_object.ajax_url, data, function(response) {
				$('#payg-output').html(response);
			});

			
		})

		$('#frmpayg input[type="text"]').on('focus', function() {
			$(this).css('text-align', 'center');
			$(this).attr("placeholder", '');
		}).on('blur', function() {
			var val = $(this).val();
			if(val) {
				$(this).css('text-align', 'center');
			} else {
				$(this).css('text-align', 'left');
			}
			$(this).attr("placeholder", 'PAYGW estimated amount');
		});

	});


})(jQuery);