<?php

namespace Gimucco\Sitemap;

class SitemapIndex {
	private const BASE_FILE 		= 'sitemap.xml';
	private const BASE_XML 			= __DIR__.'/xmls/sitemap_index/sitemap_index.xml';
	private const BASE_ELEMENT_XML 	= __DIR__.'/xmls/sitemap_index/element.xml';

	/**
	 * Total number of sitemaps that have been generated by the Runner
	 * @var int
	 */
	private int $num_sitemaps;
	/**
	 * Filename of the Sitemap Index file
	 * @var string
	 */
	private string $filename;
	/**
	 * Base URL for the sitemaps
	 * @var string
	 */
	private string $www_path;

	/**
	 * Construct of the SitemapIndex Object
	 * @param string $absolute_path absolute path of the directory where sitemaps are saved
	 * @param string $www_path URL of the directory where sitemaps are saved
	 * @param int $num_sitemaps number of sitemaps that have been generated by the runner
	 * @return void
	 */
	public function __construct(string $absolute_path, string $www_path, int $num_sitemaps = 1) {
		$this->filename = $absolute_path.self::BASE_FILE;
		$this->num_sitemaps = $num_sitemaps;
		$this->www_path = $www_path;
	}

	/**
	 * Get the number of Sitemaps generated by the Runner
	 * @return int
	 */
	public function getNumSitemaps() {
		return $this->num_sitemaps;
	}

	/**
	 * Get the base URL of the sitemaps
	 * @return string
	 */
	public function getWwwPath() {
		return $this->www_path;
	}

	/**
	 * Write the Sitemap Index file
	 * It will be saved in the Absolute Path folder
	 * @return void
	 */
	public function write() {
		$base_xml = file_get_contents(self::BASE_ELEMENT_XML);
		$elements_xml = '';
		for ($i = 1; $i <= $this->getNumSitemaps(); $i++) {
			$elements_xml .= strtr($base_xml, [
				':loc' => $this->getWwwPath().Sitemap::generateFilename($i, true),
				':lastmod' => date('c')
			]);
		}
		$xml = file_get_contents(self::BASE_XML);
		$xml = strtr($xml, [':elements' => $elements_xml]);
		file_put_contents($this->filename, $xml);
	}
}
