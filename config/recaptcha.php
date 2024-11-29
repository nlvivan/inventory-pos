<?php

return [
    'url' => 'https://www.google.com/recaptcha/api/siteverify',
    'site_key' => env('GOOGLE_RECAPTCHA_SITE_KEY'),
    'secret_key' => env('GOOGLE_RECAPTCHA_SECRET_SITE_KEY'),
];
