jQuery(document).ready(function($) {
	$('#registration_sh').submit(function(event){
		event.preventDefault();
		var nonce = $('#registration_nonce').val();
		var fields = $( this ).serializeArray();
		var data = {
			'action': 'registration_sh_ajax_handler',
			'registration_nonce': nonce,
			'data' : fields
		};
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: data,
			success:( function(response){
				if (false == response.success) {
					for (var i = 0; i < response.data.length; i++) {
						var fieldName = response.data[i].code;
						$("input[name="+fieldName+"]").next().text(response.data[i].message);
					}	
				} else if (true == response.success){
					console.log();
					$('#registration_sh .register-submit').slideUp();
					$('.form-success').text(response.data);
					$('#registration_sh').find('input[type=text], input[type=password], input[type=email]').val('');
				}
			}),
			error:( function(xhr){
				console.log(xhr);
			})
		});
	});
});
