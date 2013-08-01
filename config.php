<?php

/*

 * Database configurations.

 *

 * This file has the following editable configurations: Debugging, Timezone, MySQL settings, 
 
 * and Site Info.

 *
 
 * You really should not edit Paths unless you know why you're doing it.
 
 *

 * Upcoming Feature - This file will be modified by the install script during the installation. 

 * Until then you can just fill in these values manually.

 *
 
 *

 */

/*
 * Error Debugging - set to false if not debugging.
 *
 * Can be a security risk when set to true.
 *
 */

// Show Errors
 ini_set('display_errors', true); 
 
/*
 * Set Timezone
 *
 * Go to http://www.php.net/manual/en/timezones.php and pick a city in your timezone.
 *
 * This is used when setting the dates and times of your posts. 
 *
 */
 
 // Timezone
 date_default_timezone_set('America/New_York');
 
/*
 * MySQL Settings
 *
 * You can get this info from your web host
 *
 */

 /* DEV NOTE
 *
 * Move $dbName, $dbUser, $dbPassword, $dbHost to the /install files once the install script is complete.
 *
 * END
 */
 
 $dbName = 'Your DB Name'; // *CHANGE* your database name
 $dbUser = 'Your DB Username'; // *CHANGE* your database username
 $dbPassword = 'Your DB Password'; // *CHANGE* your database username password
 $dbHost = 'localhost'; // Almost always localhost. Don't change unless you know what you're doing...
  
// The name of the database
define('DB_NAME', $dbName);

// MySQL database username
define('DB_USER', $dbUser);

// MySQL database password
define('DB_PASSWORD', $dbPassword);

// MySQL hostname
define('DB_HOST', $dbHost);


/*
 * Site Info - admin name and pass, how many articles to display 
 *
 * This is crude. Put these in their appropriate areas in future updates.
 *
 */

// How many articles to display on the homepage
define('NUM_ARTICLES', 5);

// Admin username
define('ADMIN_USER', 'admin');

// Admin pass
define('ADMIN_PASS', 'admin123');


// END EDITABLE AREAS 

/* 
 * Paths - Defines folder paths
 *
 * Don't change anything unless you know what you're doing.
 *
 */
 
 
// CLass path
define('CLASS_PATH', 'classes');

// Template path
define('TEMPLATE_PATH', 'templates');

// Plugin path
define('PLUGIN_PATH', 'plugins');


/*
 * DEV NOTE
 * 
 * article.php does not exist yet
 *
 * END
 */
 
// get article class path
require(CLASS_PATH . '/article.php');

/*  
 * Handle Exceptions  
 * 
 * Displays a simple message to the user and logs the error for debugging.
 *
 * This exception handler is quick and dirty. The best way to handle exceptions is 
 * to wrap all the PDO calls within article.php in try & catch blocks. 
 * See http://php.net/manual/en/language.exceptions.php
 *
 */

function handleException($exception) {
	echo 'Sorry, a problem occured. Please try again later.';
	error_log($exception->getMessage());
}

set_exception_handler('handleException');

?>