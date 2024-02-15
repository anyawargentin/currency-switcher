jQuery(function($) {

    $('#currency-overview').on('submit', function(e) {
        e.preventDefault();
        updateCurrencies($);
    });
});

function updateCurrencies($) {
    $('#update-currencies').addClass('loading');
    $('#currency-overview .spin-loader').fadeIn('fast');

    $.ajax({
		type: 'post',
		url: currencySwitcherSettingsAdmin.ajaxurl,
		data: {
			action: 'update_currencies', 
			update: 'true',
			nonce: currencySwitcherSettingsAdmin.nonce
		}
	})
	.done(function(response) {

		if (response.success === true) {
			let currencies = response.data.split('||');
			let lastUpdated = currencies[0].split('&')[2];
			$('#updated-timestamp').text(lastUpdated);
	
			$.each(currencies, function(index, currency) {
				let code = currency.split('&')[0];
				let rate = currency.split('&')[1];
				let targetId = '#currency-' + code.toLowerCase();
		
				rate == 'base' ? $(targetId).hide() : $(targetId).show();
		
				$(targetId).val(parseFloat(rate));
			});
		}

	})
	.fail(function(jqXHR, textStatus, errorThrown) {
		console.error('Error:', textStatus, errorThrown);
	})
	.always(function() {
		$('#update-currencies').removeClass('loading');
		$('#currency-overview .spin-loader').fadeOut('fast');
		$('#update-currencies').val('Currencies updated!');

		setTimeout(() => { 
			$('#update-currencies').val('Update currencies');
		}, 2000);
	});
	
}