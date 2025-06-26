
<?php

return [


    'host'        => env('MAIL_HOST'),
    'username'    => env('MAIL_USERNAME'),
    'password'    => env('MAIL_PASSWORD'),
    "smtp_secure" => env('MAIL_ENCRYPTION','ssl'),
    "port"        => env('MAIL_PORT','465'),
    "sender"      => env('MAIL_SENDERNAME')

];