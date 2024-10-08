<?php

namespace Gimucco\Sitemap;

use Exception;
use Gimucco\Sitemap\Sitemap;

class Runner {
	// Turn on verbose output
	public const OPTION_VERBOSE = 'verbose';
	// Allows relative path to be used (discouraged)
	public const OPTION_ALLOW_RELATIVE_PATHS = 'allow_relative_paths';
	public const OPTIONS = [
		self::OPTION_VERBOSE => ['method' => 'setVerbose'],
		self::OPTION_ALLOW_RELATIVE_PATHS => ['method' => 'setAllowRelativePaths'],
	];

	/**
	 * Verbose status. True means showing output
	 * @var bool
	 */
	private bool $verbose = false;

	/**
	 * Allow relative paths to be used. (discouraged)
	 * @var bool
	 */
	private bool $allow_relative_paths = false;

	/**
	 * Number of Sitemaps being generated so far
	 * @var int
	 */
	private int $count_sitemaps = 0;

	/**
	 * Current Sitemap being worked on
	 * @var Sitemap
	 */
	private Sitemap $current_sitemap;

	/**
	 * Directory where the sitemaps will be saved.
	 * Should be writable from the user running the script.
	 * Should be reachable via web (see Output Dir)
	 * E.g. /var/www/yourdomain.com/www/sitemaps/
	 * @var string
	 */
	private string $output_dir;

	/**
	 * URL of the directory that will contain the Sitemaps
	 * Must be accessible via web and be a valid URL
	 * E.g. https://yourdomain.com/sitemaps/
	 * @var string
	 */
	private string $http_dir;

	/**
	 * Simple counter to keep track of how many URLs have been added so far
	 * @var int
	 */
	private int $count_urls = 0;

	/**
	 * Filename pattern used to generate the names of Multiple sitemaps
	 * E.g. sitemap_%s.xml
	 * @var string
	 */
	private string $filename_multiple;

	/**
	 * Filename for Single Sitemap or SitemapIndex
	 * @var string
	 */
	private string $filename_single;

	/**
	 * Sitemap Locations Array
	 * @var array
	 */
	private array $sitemaps_locations;

	/**
	 * Max URLs/Items per sitemap
	 * @var int
	 */
	private int $max_urls = Sitemap::MAX_URLS;

	/**
	 * Contructor for the Runner object
	 * @param string $output_dir the directory where sitemaps will be saved
	 * @param string $http_dir the URL to reach the above directory via web
	 * @param array $options valid options: OPTION_VERBOSE or OPTION_ALLOW_RELATIVE_PATHS
	 * @param int $max_urls number of max urls in the sitemap
	 * @return void
	 * @throws Exception
	 */
	public function __construct(string $output_dir = '', string $http_dir = '', array $options = [], int $max_urls = Sitemap::MAX_URLS) {
		foreach ($options as $option) {
			if (empty(self::OPTIONS[$option])) {
				throw new Exception('Invalid Option "'.$option.'". Valid options are only "'.implode(',', array_keys(self::OPTIONS)).'"');
			}
			$option_meta = self::OPTIONS[$option];
			call_user_func(array($this, $option_meta['method']));
		}
		if ($output_dir) {
			$this->setOutputDir($output_dir);
		}
		if ($http_dir) {
			$this->setHTTPDir($http_dir);
		}
		if ($max_urls > 0) {
			$this->setMaxUrls($max_urls);
		}
		$this->sitemaps_locations = [];
		$this->filename_single = Sitemap::BASE_FILE_SINGLE;
		$this->filename_multiple = Sitemap::BASE_FILE;
	}

	/**
	 * Set custom filenames for the sitemaps
	 * @param string $single filename used when there's only one sitemap, or for the sitemapindex.
	 * @param string $multiple filename used for multiple sitemaps. Must be a pattern like sitemap_%s.xml
	 * @return void
	 * @throws Exception
	 */
	public function setFilenames(string $single, string $multiple) {
		if (stripos($multiple, "%s") === false) {
			throw new Exception('The multiple variable must contain a pattern, in form of %s. E.g. sitemap_%s.xml');
		}
		$this->filename_single = $single;
		$this->filename_multiple = $multiple;
	}

	/**
	 * Set Verbose mode. Same as using the OPTION_VERBOSE options on the constructor
	 * @return void
	 */
	public function setVerbose() {
		$this->verbose = true;
	}

	/**
	 * Allows relative paths to be used. Same as using the OPTION_ALLOW_RELATIVE_PATHS options on the constructor (discouraged)
	 * @return void
	 */
	public function setAllowRelativePaths() {
		$this->allow_relative_paths = true;
	}

	/**
	 * Sets the maximum number of items for each sitemap
	 * @return void
	 */
	public function setMaxUrls(int $max_items) {
		$this->max_urls = $max_items;
	}

	/**
	 * Check if we are running in verbose mode
	 * @return bool
	 */
	public function isVerbose() {
		return $this->verbose;
	}

	/**
	 * Check if it's allowed to use relative paths (discouraged)
	 * @return bool
	 */
	public function getAllowRelativePaths() {
		return $this->allow_relative_paths;
	}

	/**
	 * Get the Sitemap that is currently being written
	 * @return Sitemap
	 */
	public function getCurrentSitemap() {
		return $this->current_sitemap;
	}

	/**
	 * Get the number of Sitemaps currently created
	 * @return int
	 */
	public function getCountSitemaps() {
		return $this->count_sitemaps;
	}

	/**
	 * Get the local path to the Directory where the Sitemaps will be saved
	 * @return string
	 */
	public function getDir() {
		return $this->output_dir;
	}

	/**
	 * Get the URL of the directory of the Sitemaps.
	 * @return string
	 */
	public function getHTTPDir() {
		return $this->http_dir;
	}

	/**
	 * Get how many URLs have been added in total
	 * @return int
	 */
	public function getCountURLs() {
		return $this->count_urls;
	}

	/**
	 * +1 added URL
	 * @return void
	 */
	public function addCountURLs() {
		$this->count_urls++;
	}

	/**
	 * Get the max number of urls per sitemap
	 * @return void
	 */
	public function getMaxUrls() {
		return $this->max_urls;
	}

	/**
	 * Increase the counter of Sitemaps and set a new Sitemap as the current one.
	 * @return void
	 */
	public function addSitemap() {
		$this->count_sitemaps++;
		$this->current_sitemap = new Sitemap($this->getDir(), $this->getHTTPDir(), $this->getCountSitemaps(), $this->isVerbose());
		$this->current_sitemap->setFilenames($this->getFilenameSingle(), $this->getFilenameMultiple());
	}

	/**
	 * Get the filename for Single sitemap or SitemapIndex
	 * @return string
	 */
	public function getFilenameSingle() {
		return $this->filename_single;
	}

	/**
	 * Get the filename pattern for Multiple sitemaps
	 * @return string
	 */
	public function getFilenameMultiple() {
		return $this->filename_multiple;
	}

	/**
	 * Get the list of sitemaps web locations for the sitemap index
	 * @return array
	 */
	public function getSitemapsLocations() {
		return $this->sitemaps_locations;
	}

	/**
	 * Manually add a Sitemap Location
	 * Useful if you have a static file and you want to add it to the SitemapIndex
	 * @return array
	 */
	public function appendSitemapsLocation(string $location) {
		return $this->sitemaps_locations[] = $location;
	}

	/**
	 * Set the URL to reach the sitemaps via web. Same as using the second parameter of the constructor. Should be a publicly-accessible URL
	 * @param string $url the URL to reach the sitemaps via web
	 * @return void
	 * @throws Exception
	 */
	public function setHTTPDir(string $url) {
		if (!filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
			throw new Exception('The specified location "'.$url.'" is not a valid URL. https://yourdomain.com/sitemaps/');
		}
		if (substr($url, -1, 1) != "/") {
			$url .= '/';
		}
		$this->http_dir = $url;
	}

	/**
	 * Set the directory where sitemaps will be saved. Same as using the first parameter of the constructor.
	 * Should be an absolute path unless the `setAllowRelativePaths` method is used (discouraged).
	 * @param string $output_dir he directory where sitemaps will be saved
	 * @return void
	 * @throws Exception
	 */
	public function setOutputDir(string $output_dir) {
		if (substr($output_dir, 0, 1) != "/") {
			if (!$this->getAllowRelativePaths()) {
				throw new Exception('Please provide an absolute path. If you want to use relative paths (discouraged), please use the `setAllowRelativePaths()` method');
			}
			$backtrace = debug_backtrace();
			$root = dirname($backtrace[0]['file']);
			$output_dir = $root."/".$output_dir;
		}
		if (!file_exists($output_dir)) {
			if (!@mkdir($output_dir)) {
				throw new Exception('The "'.$output_dir.'" path was not found in your system and you don\'t have the permissions to create it');
			}
		}
		if (!is_writable($output_dir)) {
			throw new Exception('You don\'t have the permissions to write the  "'.$output_dir.'" path ');
		}
		if (substr($output_dir, -1, 1) != "/") {
			$output_dir .= '/';
		}
		$this->output_dir = $output_dir;
	}

	/**
	 * Add an URL to the Runner, generating the URL Object
	 * If the Sitemap has space, the URL will be added to the current sitemap.
	 * Otherwise, the Runner will close the previous Sitemap, create a new one and add the URL to the new Sitemap
	 * @param string $loc the URL to be added to the sitemap. Must be a valid URL.
	 * @param string $lastmod the date in which the URL last changed. Must be in ISO8601 format
	 * @param float $priority the priority of the URL. Must be between 0.1 and 1
	 * @param bool $has_mobile indicates if the URL is mobile-ready
	 * @return void
	 * @throws Exception
	 */
	public function pushURL(string $loc, string $lastmod, float $priority, string $frequency, bool $has_mobile = false) {
		$url = new Url($loc, $lastmod, $priority, $frequency, $has_mobile);
		$this->pushURLObject($url);
	}

	/**
	 * Add an URL Object to the Runner.
	 * If the Sitemap has space, the URL will be added to the current sitemap.
	 * Otherwise, the Runner will close the previous Sitemap, create a new one and add the URL to the new Sitemap
	 * @param Url $url The URL object
	 * @return void
	 * @throws Exception
	 */
	public function pushURLObject(Url $url) {
		if (!isset($this->current_sitemap)) {
			$this->addSitemap();
		}
		if (empty($this->output_dir)) {
			throw new Exception('Please set the path of the sitemap via the `setOutputDir` method before populating the sitemap');
		}
		if (empty($this->http_dir)) {
			throw new Exception('Please set the Web path of the sitemap via the `setHTTPDir` method before populating the sitemap');
		}
		if ($this->isVerbose()) {
			if ($this->getCurrentSitemap()->getSize() == 0) {
				echo "Starting Sitemap ".$this->getCountSitemaps().PHP_EOL;
			} elseif ($this->getCurrentSitemap()->getSize() % 10000 == 0) {
				echo "\t\tAdded ".number_format($this->getCurrentSitemap()->getSize())." URLs".PHP_EOL;
			}
		}

		if ($this->getCurrentSitemap()->getSize() >= $this->getMaxUrls()) {
			$this->getCurrentSitemap()->write(true);
			$this->sitemaps_locations[] = $this->getCurrentSitemap()->getWebAddress(true);
			$this->addSitemap();
			if ($this->isVerbose()) {
				echo "Starting Sitemap ".$this->getCountSitemaps().PHP_EOL;
			}
		}

		$this->getCurrentSitemap()->addUrl($url);
		$this->addCountURLs();
	}

	/**
	 * Completed the Runner job.
	 * If there are pending sitemaps, they will be written to the dirk
	 * If a multi-sitemap is being created, a SitemapIndex will be added
	 * @return void
	 */
	public function end() {
		$this->getCurrentSitemap()->write(false);
		$this->sitemaps_locations[] = $this->getCurrentSitemap()->getWebAddress(false);
		if ($this->getCountSitemaps() > 1) {
			if ($this->isVerbose()) {
				echo "Writing Sitemap Index to ".$this->getDir().$this->getFilenameSingle().PHP_EOL;
			}
			$index = new SitemapIndex($this->getSitemapsLocations(), $this->getDir(), $this->getFilenameSingle());
			$index->write();
		}
		if ($this->isVerbose()) {
			echo "Remembed to update your robots.txt to include the sitemap for easier discovery".PHP_EOL;
		}
	}

	/**
	 * Ping Google to inform of the sitemap changes.
	 * You need to have set the HTTP Directory via the `setHTTPDir` for this to work
	 * @return void
	 * @throws Exception
	 */
	public function pingGoogle() {
		if (empty($this->http_dir)) {
			throw new Exception('Please set the HTTP path of the sitemap via the `setHTTPDir` method before pinging Google');
		}
		$sitemap_url = $this->getHTTPDir().$this->getFilenameSingle();
		if ($this->isVerbose()) {
			echo "*** PINGING GOOGLE FOR ".$sitemap_url.PHP_EOL;
		}
		file_get_contents('https://www.google.com/webmasters/tools/ping?sitemap='.urlencode($sitemap_url));
	}
}
