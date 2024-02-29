<?php

include SITE_ROOT . '/app/database/DBInterface.php';
include SITE_ROOT . '/app/database/DBFunctional.php';

session_start();

$host = 'localhost';
const SMARTCAPTCHA_CLIENT_KEY = 'smartcaptcha1';
const SMARTCAPTCHA_SERVER_KEY = 'smartcaptcha2';
$db_name = '/app/database/my_db.sqlite';
$my_db = SQLiteDB::getInstance(SITE_ROOT . $db_name);