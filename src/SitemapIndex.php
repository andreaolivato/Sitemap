<?php

namespace Gimucco\Sitemap;

use Exception;

class SitemapIndex {
	private const BASE_XML 			= __DIR__.'/xmls/sitemap_index/sitemap_index.xml';
	private const BASE_ELEMENT_XML 	= __DIR__.'/xmls/sitemap_index/element.xml';

	/**
	 * Directory where the sitemaps will be saved.
	 * Should be writable from the user running the script.
	 * Should be reachable via web (see Output Dir)
	 * E.g. /var/www/yourdomain.com/www/sitemaps/
	 * @var string
	 */
	private string $absolute_path;

	/**
	 * Array containing all the locations of the sitemaps generated by Runner
	 * @var array
	 */
	private array $sitemaps_locations;

	/**
	 * Filename of the SitemapIndex
	 * @var string
	 */
	private string $filename;

	/**
	 * Construct of the SitemapIndex Object
	 * @param array $sitemaps_locations Array containing all the locations of the sitemaps generated by Runner
	 * @param string $absolute_path Directory where the sitemaps will be saved.
	 * @param string $filename Filename of the SitemapIndex
	 * @return void
	 */
	public function __construct(array $sitemaps_locations, string $absolute_path, string $filename = Sitemap::BASE_FILE_SINGLE) {
		$this->absolute_path = $absolute_path;
		$this->sitemaps_locations = $sitemaps_locations;
		$this->filename = $filename;
	}

	/**
	 * Get the Absolute Path for the Sitemap folder
	 * @return string
	 */
	public function getAbsolutePath() {
		return $this->absolute_path;
	}

	/**
	 * Get the number of Sitemaps generated by the Runner
	 * @return array
	 */
	public function getSitemapsLocations() {
		return $this->sitemaps_locations;
	}

	/**
	 * Get the Filename of the SitemapIndex
	 * @return string
	 */
	public function getFilename() {
		return $this->filename;
	}

	/**
	 * Write the Sitemap Index file
	 * It will be saved in the Absolute Path folder
	 * @return void
	 */
	public function write() {
		$base_xml = file_get_contents(self::BASE_ELEMENT_XML);
		$elements_xml = '';
		foreach ($this->getSitemapsLocations() as $sitemap_location) {
			$elements_xml .= strtr($base_xml, [
				':loc' => $sitemap_location,
				':lastmod' => date('c')
			]);
		}
		$xml = file_get_contents(self::BASE_XML);
		$xml = strtr($xml, [':elements' => $elements_xml]);
		file_put_contents($this->getAbsolutePath().$this->getFilename(), $xml);
	}
}
