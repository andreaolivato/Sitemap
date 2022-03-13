<?php

use Gimucco\Sitemap\Runner;
use Gimucco\Sitemap\Url;

require_once __DIR__.'/../vendor/autoload.php';

// This is the Path where the xml files will be saved.
$sitemaps_folder_path = __DIR__.'/sitemaps/';
// This is the URL corresponding to the path above
$sitemaps_folder_url = 'https://yourdomain.com/sitemaps/';
// Array containing your URLs
$urls = ['https://yourdomain.com/', 'https://yourdomain.com/contacts', 'https://yourdomain.com/signup', 'https://yourdomain.com/login'];

// Starting the Runner
// First parameter is the local path to the folder where Sitemaps will be saved
// Second parameter is the URL to reach the sitemaps
// Third parameter are options, E.g. Verbosity
$_Runner = new Runner($sitemaps_folder_path, $sitemaps_folder_url, [Runner::OPTION_VERBOSE]);
foreach ($urls as $url) {
	// Push the URL to the sitemap
	// First parameter is the URL of the resource you want to add to the sitemap
	// Second parameter is the Date of last update in ISO8601 format
	// Third parameter is  the priority (0.1 to 1)
	// Fourth parameter is the update frequency (e.g. daily)
	$_Runner->pushURL($url, Url::timeNow(), Url::PRIORITY_HIGHEST, Url::FREQ_ALWAYS);
}
// Write Sitemaps and cleanup
$_Runner->end();
// Optional, send Ping to Google to refresh the Sitemap
$_Runner->pingGoogle();
