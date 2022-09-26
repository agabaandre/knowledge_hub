'use strict';
$(document).ready(function() {
    // [ date masking ]
    $(".date").inputmask({
        mask: "99/99/9999"
    });
    // [ date2 masking ]
    $(".date2").inputmask({
        mask: "99-99-9999"
    });
    // [ hour masking ]
    $(".hour").inputmask({
        mask: "99:99:99"
    });
    // [ date-Hour masking ]
    $(".dateHour").inputmask({
        mask: "99/99/9999 99:99:99"
    });
    // [ mobile-num masking ]
    $(".mob_no").inputmask({
        mask: "9999-999-999"
    });
    // [ phone masking ]
    $(".phone").inputmask({
        mask: "9999-9999"
    });
    // [ telphone-code masking ]
    $(".telphone_with_code").inputmask({
        mask: "(99) 9999-9999"
    });
    // [ us-telphone masking ]
    $(".us_telephone").inputmask({
        mask: "(999) 999-9999"
    });
    // [ Ip masking ]
    $(".ip").inputmask({
        mask: "999.999.999.999"
    });
    // [ Ipv4 masking ]
    $(".ipv4").inputmask({
        mask: "999.999.999.9999"
    });
    // [ Ipv6 masking ]
    $(".ipv6").inputmask({
        mask: "9999:9999:9999:9:999:9999:9999:9999"
    });
    // [ Auto-number masking ]
    $('.autonumber').autoNumeric('init');

});
