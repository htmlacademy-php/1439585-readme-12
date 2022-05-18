<?php

declare(strict_types=1);
session_start();

require_once('config/db_connect.php');
require_once('config/site_config.php');
require_once('functions.php');

isUserLoggedIn();

$pageContent = include_template('no-content.php');
$nothingShowPage = include_template('layout.php',
    ['pageContent' => $pageContent, 'titleName' => 'readme: нечего показать', 'is_auth' => AUTH]);

print_r($nothingShowPage);
