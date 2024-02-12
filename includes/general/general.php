<?php
/*
* Decide the correct symbol placement for the currency
*/
function get_symbol_placement($currency_code) {

    switch ($currency_code) {
        case 'AED':
            return 'right';
        case 'QAR':
            return 'right';
        case 'OMR':
            return 'right';
        case 'SAR':
            return 'right';
        case 'SEK':
                return 'space-right';
        case 'NOK':
            return 'space-right';
        case 'DKK':
            return 'space-right';
        default:
            return 'left';
    }
}