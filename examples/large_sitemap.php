<?php

use Gimucco\Sitemap\Runner;
use Gimucco\Sitemap\Url;

require_once __DIR__.'/../vendor/autoload.php';

// This is the Path where the xml files will be saved.
// It should be reachable via web
// And it should be writable by the user running the script
$sitemaps_folder_path = __DIR__.'/sitemaps/';
// This is the URL corresponding to the path above
$sitemaps_folder_url = 'https://yourdomain.com/sitemaps/';

// Starting the runner
$_Runner = new Runner();
// Optional: turning on output
$_Runner->setVerbose();
// Required: Set the Output Directory for the sitemaps
$_Runner->setOutputDir($sitemaps_folder_path);
// Required for multi-sitemaps and pinging google: Set the web address
$_Runner->setHTTPDir($sitemaps_folder_url);

// Run 200000 random iterations
for ($i = 0; $i < 200000; $i++) {
	// Push the URL to the sitemap
	$_Runner->pushURL('https://yourdomain.com/'.uniqid(), date("c", mt_rand(strtotime('-1 year'), time())), round(rand(1, 10) / 10, 1), Url::FREQ_DAILY);
}
// Write Sitemaps and cleanup
$_Runner->end();
// Optional, send Ping to Google to refresh the Sitemap
$_Runner->pingGoogle();
