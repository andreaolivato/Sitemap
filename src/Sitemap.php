<?php

namespace Gimucco\Sitemap;

class Sitemap {
	private const BASE_FILE = 'sitemap_%s.xml';
	private const BASE_FILE_SINGLE = 'sitemap.xml';
	private const BASE_XML 	= __DIR__.'/xmls/sitemaps/sitemap.xml';

	// Max number of URLS per sitemap.
	public const MAX_URLS 	= 50000;

	/**
	 * Directory where the sitemaps will be saved.
	 * Should be writable from the user running the script.
	 * Should be reachable via web (see Output Dir)
	 * E.g. /var/www/yourdomain.com/www/sitemaps/
	 * @var string
	 */
	private string $absolute_path;
	/**
	 * Index (number) of this sitemap. Used to generate sequential names for the sitemaps.
	 * @var int
	 */
	private int $sitemap_index;
	/**
	 * Array containing the URLs currently added to this Sitemap
	 * @var Url[]
	 */
	private array $urls;
	/**
	 * Verbose status. True means showing output
	 * @var bool
	 */
	private bool $verbose;

	/**
	 * Constructor of the Sitemap Object
	 * @param string $absolute_path the directory where sitemaps will be saved
	 * @param int $sitemap_index sequential number of the current sitemap
	 * @param bool $is_verbose show output or not
	 * @return void
	 */
	public function __construct(string $absolute_path, int $sitemap_index, bool $is_verbose = false) {
		$this->absolute_path = $absolute_path;
		$this->sitemap_index = $sitemap_index;
		$this->urls = [];
		$this->verbose = $is_verbose;
	}

	/**
	 * Utility to generate the sitemap filename based on the sequential index and if there are more sitemaps.
	 * If there is only 1 sitemap, the only sitemap generate will be sitemap.xml
	 * If there are multiple sitemaps, each sitemap will have an increasing number: sitemap_1.xml, sitemap_2.xml etc...
	 * @param int $index index of the current sitemap
	 * @param bool $has_more indicates if there are more sitemaps after this
	 * @return string
	 */
	public static function generateFilename(int $index, bool $has_more) {
		if ($has_more || $index > 1) {
			return sprintf(self::BASE_FILE, $index);
		} else {
			return self::BASE_FILE_SINGLE;
		}
	}

	/**
	 * Writes the sitemap to the disk. Takes all the URLs previously added, composes the final XML file and writes it to disk.
	 * @param bool $has_more indicates if there are more sitemaps after this
	 * @return void
	 */
	public function write(bool $has_more) {
		$filename = $this->getAbsolutePath().self::generateFilename($this->getSitemapIndex(), $has_more);
		if ($this->isVerbose()) {
			echo "\tWriting Sitemap ".$this->getSitemapIndex()." to ".$filename.PHP_EOL;
		}
		$xml = file_get_contents(self::BASE_XML);
		$urls_xml = "";
		foreach ($this->urls as $url) {
			$urls_xml .= $url->getXml();
		}
		$xml = strtr($xml, [':elements' => $urls_xml]);

		file_put_contents($filename, $xml);
	}

	/**
	 * Add a new URL to the array of URLs
	 * @param Url $url
	 * @return void
	 */
	public function addUrl(Url $url) {
		$this->urls[] = $url;
	}

	/**
	 * Check if we are running in verbose mode
	 * @return bool
	 */
	public function isVerbose() {
		return $this->verbose;
	}

	/**
	 * Get the Absolute Path for the Sitemap folder
	 * @return string
	 */
	public function getAbsolutePath() {
		return $this->absolute_path;
	}

	/**
	 * Get the Index of this sitemap
	 * @return int
	 */
	public function getSitemapIndex() {
		return $this->sitemap_index;
	}

	/**
	 * Get the current number of URLs in this Sitemap
	 * @return int
	 */
	public function getSize() {
		return sizeof($this->urls);
	}
}
