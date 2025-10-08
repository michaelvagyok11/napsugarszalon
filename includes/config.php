<?php
// config.php

// Adatbázis beállítások
define('DB_HOST', 'localhost');
define('DB_NAME', 'nail_salon');
define('DB_USER', 'dbuser');
define('DB_PASS', 'dbpassword');

// Az alkalmazás (például) alap URL-je (állítsd be a szerver domainre)
define('BASE_URL', 'https://napsugarszalon.hu/');

// Egyéb konfigurációk (pl. admin e-mail)
define('ADMIN_EMAIL', 'admin@napsugarszalon.hu');

// E-mail küldési beállítások — pl. SMTP konfigurációk (ha PHPMailer-t használsz)
define('SMTP_HOST', 'smtp.example.com');
define('SMTP_USER', 'smtpuser');
define('SMTP_PASS', 'smtppass');
define('SMTP_PORT', 587);
define('SMTP_FROM_EMAIL', 'no-reply@napsugarszalon.hu');
define('SMTP_FROM_NAME', 'Napsugár Szalon');
