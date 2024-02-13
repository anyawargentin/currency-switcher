jQuery(function($) {

    $('#currency-overview').on('submit', function(e) {
        e.preventDefault();
        updateCurrencies($);
    });
});

function updateCurrencies($) {
    $('#update-currencies').addClass('loading');

    $.ajax({
        type: 'post',
        url: '/wp-admin/admin-ajax.php',
        data: {
            action: 'update_currencies', 
            update: 'true'
        },
        success: function(data){
            let currencies = data.split('||');
            let lastUpdated = currencies[0].split('&')[2];
            $('#updated-timestamp').text(lastUpdated);

            $.each(currencies, function(index, currency) {
                let code = currency.split('&')[0];
                let rate = currency.split('&')[1];
                let targetId = '#currency-' + code.toLowerCase();

                rate == 'base' ? $(targetId).hide() : $(targetId).show();

                $(targetId).val(parseFloat(rate));
            });

            $('#update-currencies').removeClass('loading');

        }
    });
}