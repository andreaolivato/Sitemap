<?php

namespace Gimucco\Sitemap;

use Exception;

class UrlLang {
	/**
	 * The Base XML
	 */
	private const XML = '
		<xhtml:link rel="alternate" hreflang=":lang" href=":url"/>';

	/**
	 * The language ISO code (2 letters)
	 * @var string
	 */
	private string $lang;

	/**
	 * The URL of the localised page
	 * @var string
	 */
	private string $url;

	/**
	 * Construct for the UrlLang object
	 * @param string $lang the language ISO code (2 letters)
	 * @param string $url the URL of the localised page
	 * @return void
	 * @throws Exception
	 */
	public function __construct(string $lang, string $url) {
		if (!filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
			throw new Exception('The specified url "'.$url.'" is not a valid URL');
		}
		$this->lang = $lang;
		$this->url = $url;
	}

	/**
	 * Retrieve the Language
	 * @return string
	 */
	public function getLang() {
		return $this->lang;
	}

	/**
	 * Retrieve the URL
	 * @return string
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * Generates and Returns the XML of the hreflang
	 * @return string
	 */
	public function getXml() {
		return strtr(self::XML, [
			':lang' => $this->getLang(),
			':url' => $this->getUrl()
		]);
	}
}
