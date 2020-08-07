<?php

$config = array(
    'HOST' => 'localhost',
    'USER' => 'root',
    'PASS' => 'pass',
    'DB'   => 'auth',
    'CORE' => 'AzerothCore'
);


// General Settings
define('EXPANSION', 2); // 0 = Vanilla / 1 = TBC / 2 = WOTLK
define('REALMLIST', 'set realmlist logon.servername.com');

// Google ReCaptcha Settings
define('CAPTCHA_SECRET', 'PUT_YOUR_SECRET_KEY_HERE');
define('CAPTCHA_CLIENT_ID', 'PUT_YOUR_SITE_KEY_HERE');

// Message Settings
define('SUCCESS_MESSAGE', 'Successfully Registered!');

?>
