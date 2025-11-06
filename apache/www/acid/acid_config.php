<?php
// Database configuration
define('DBHOST', getenv('DB_HOST') ?: 'localhost');
define('DBUSER', getenv('DB_USER') ?: 'dash');
define('DBPASS', getenv('DB_PASS') ?: 'password');
define('DBNAME', getenv('DB_NAME') ?: 'dashdb');

// Make sure to set the correct paths for ADOdb and PHPlot libraries
define('ADODB_PATH', '/usr/share/php/adodb/');
define('PHPLOT_PATH', '/usr/share/php/phplot/');
?>
