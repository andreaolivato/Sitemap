<?php

use Gimucco\Sitemap\Runner;
use Gimucco\Sitemap\Url;
use Gimucco\Sitemap\UrlLang;

require_once __DIR__.'/../vendor/autoload.php';

// This is the Path where the xml files will be saved.
// It should be reachable via web
// And it should be writable by the user running the script
$sitemaps_folder_path = __DIR__.'/sitemaps/';
// This is the URL corresponding to the path above
$sitemaps_folder_url = 'https://yourdomain.com/sitemaps/';

$base_url = 'https://yourdomain.com';
$urls = [
	[
		'url' => '/',
		'p' => Url::PRIORITY_HIGHEST,
		'time' => Url::timeNow(),
		'freq' => Url::FREQ_ALWAYS,
		'langs' => [
			[
				'iso' => 'en',
				'url' => '/en'
			], [
				'iso' => 'fr',
				'url' => '/fr'
			], [
				'iso' => 'de',
				'url' => '/de'
			], [
				'iso' => 'es',
				'url' => '/es'
			]
		]
	],
	[
		'url' => '/login',
		'p' => Url::PRIORITY_HIGHER,
		'time' => Url::timeYesterday(),
		'freq' => Url::FREQ_DAILY,
		'langs' => [
			[
				'iso' => 'en',
				'url' => '/en/login'
			], [
				'iso' => 'fr',
				'url' => '/fr/login'
			], [
				'iso' => 'de',
				'url' => '/de/login'
			], [
				'iso' => 'es',
				'url' => '/es/login'
			]
		]
	]
];

// Starting the runner
// First parameter is the local path to the folder where Sitemaps will be saved
// Second parameter is the URL to reach the sitemaps
// Third parameter are options, E.g. Verbosity
$_Runner = new Runner($sitemaps_folder_path, $sitemaps_folder_url, [Runner::OPTION_VERBOSE]);
foreach ($urls as $url) {
	$URL = new Url($base_url.$url['url'], $url['time'], $url['p'], $url['freq'], true);
	foreach ($url['langs'] as $lang) {
		$LANG = new UrlLang($lang['iso'], $base_url.$lang['url']);
		$URL->pushLang($LANG);
	}
	// Push the URL to the sitemap
	$_Runner->pushURLObject($URL);
}
// Write Sitemaps and cleanup
$_Runner->end();
// Optional, send Ping to Google to refresh the Sitemap
$_Runner->pingGoogle();
