<?php

/**
 * Force cookie authentication so phpMyAdmin always shows the login form.
 * Without this, some setups still inject user/password (config auth) and skip the form.
 *
 * @see https://docs.phpmyadmin.net/en/latest/config.html#authentication-mode
 */
foreach ($cfg['Servers'] ?? [] as $i => $_) {
    $cfg['Servers'][$i]['auth_type'] = 'cookie';
    unset($cfg['Servers'][$i]['user'], $cfg['Servers'][$i]['password']);
}
