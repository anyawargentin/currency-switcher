jQuery(function($) {

    $('#active-customer-currency').on('click', function() {
        let activeCurrency = $(this).find('.currency-code').text();

        $('#currency-overlay').fadeIn('fast');
        $('#currency-switcher').fadeIn('fast');
        $('#currency-list .currency-item').removeClass('active');
        $('#currency-list .currency-item[data-code="' + activeCurrency + '"').addClass('active');
    });

    $('#currency-overlay').on('click', function() {
        if($('#loading-currency').is(":hidden")) {
            $('#currency-overlay').fadeOut('fast');
            $('#currency-switcher').fadeOut('fast');
        }
    });

    $('#currency-switcher #currency-list .currency-item').on('click', function() {
        let target = $(this);
        
        $('#currency-switcher').fadeOut('fast', function() {
            $('#loading-currency').css('display', 'flex').hide().fadeIn('fast');

            $.ajax({
                type: 'post',
                url: currencySwitcherSettings.ajaxurl,
                data: {
                    action: 'update_customer_currency', 
                    code: target.attr('data-code'),
                    nonce: currencySwitcherSettings.nonce
                },
                success: function(data){
                    location.reload();
                }
            });
        });
    });
});
