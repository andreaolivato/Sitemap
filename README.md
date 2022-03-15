# Sitemap

Dynamic Sitemap generator in PHP with multi-sitemap and sitemap index support.

## Features

Current features include:

- Generate Single Sitemap
- Generate Multiple Sitemaps if submitting more than 500,000 URLs
- Automatically generate Sitemap Index
- Specify if URL is mobile-ready
- Multi-language support with hreflang
- Ping Google

## Working on

- Image support
- Video Support

## Requirements

PHP >= 7.4

## Installation

Install via Composer

```
composer require gimucco/sitemap
```

## Code Example for generating a simple sitemap

The below code shows how to generate a simple sitemap starting from an array of URLs.
Refer to the `examples` folder for more detailed examples and options. 

```PHP
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
```

## Utilities: Frequency
The **Url** class contains default priority values in public constants.

```PHP
Sitemap::FREQ_ALWAYS; // always
Sitemap::FREQ_HOURLY; // hourly
Sitemap::FREQ_DAILY; // daily
Sitemap::FREQ_WEEKLY; // weekly
Sitemap::FREQ_MONTHLY; // monthly
Sitemap::FREQ_YEARLY; // yearly
Sitemap::FREQ_NEVER ; // never
```

## Utilities: Priority
The **Url** class contains default priority values in public constants.

```PHP
Sitemap::PRIORITY_HIGHEST; //1;
Sitemap::PRIORITY_HIGHER; //0.9;
Sitemap::PRIORITY_HIGH; //0.7;
Sitemap::PRIORITY_AVERAGE; //0.5;
Sitemap::PRIORITY_LOW; //0.3;
Sitemap::PRIORITY_LOWER; //0.2;
Sitemap::PRIORITY_LOWEST; //0.1;
```

## Utilities: Last Update

The **Url** class contains public static methods to convert dates and timestamps to the correct format for Sitemaps: ISO8601

```PHP
Url::convertDateToISO8601("2022-03-13 12:34:56");
// Output: 2022-03-13T12:34:56+00:00

Url::convertTimestampToISO8601(1647147559);
// Output: 2022-03-13T04:59:19+00:00
```

Furthermore, the **Url** class contains standard times (already formatted  in ISO8601) as public static methods.

```PHP
Sitemap::timeNow(); // Now
Sitemap::timeYesterday(); // 24 hours ago
Sitemap::timeOneWeek(); // One week ago {
Sitemap::timeOneMonth(); // One month ago
```


## Examples
Refer to the `examples` folder for quick examples on how to use the various options

Available Examples:
- Small, simple sitemap
- Large, random sitemap
- Complex sitemap with multiple languages per URL