<?php

namespace Gimucco\Sitemap;

use Exception;

class Url {
	private const BASE_XML = __DIR__.'/xmls/sitemaps/element.xml';

	// Utility: Priority
	public const PRIORITY_HIGHEST	= 1;
	public const PRIORITY_HIGHER 	= 0.9;
	public const PRIORITY_HIGH 		= 0.7;
	public const PRIORITY_AVERAGE 	= 0.5;
	public const PRIORITY_LOW 		= 0.3;
	public const PRIORITY_LOWER 	= 0.2;
	public const PRIORITY_LOWEST 	= 0.1;

	// Utility: Frequencies
	public const FREQ_ALWAYS 	= 'always';
	public const FREQ_HOURLY 	= 'hourly';
	public const FREQ_DAILY 	= 'daily';
	public const FREQ_WEEKLY 	= 'weekly';
	public const FREQ_MONTHLY 	= 'monthly';
	public const FREQ_YEARLY 	= 'yearly';
	public const FREQ_NEVER 	= 'never';

	/**
	 * URL of the resource added to the sitemap.
	 * Must be a valid Web URL
	 * @var string
	 */
	private $loc;
	/**
	 * Date in which the resource was last edited.
	 * Must be in ISO8601 format
	 * @var string
	 */
	private $lastmod;
	/**
	 * Priority of the URL within the sitemap.
	 * Must be between 0.1 and 1
	 * @var float
	 */
	private $priority;
	/**
	 * Frequency of update of this resource.
	 * Must be a string value between:
	 * never, yearly, monthly, weekly, daily, hourly, always
	 * @var string
	 */
	private $frequency;
	/**
	 * Indicates if this resource is available and optimised for mobile devices.
	 * In 2022 this should always be true.
	 * @var bool
	 */
	private $has_mobile;

	/**
	 * Constructor of the Url object
	 * @param string $loc URL of the resource added to the sitemap. Must be a valid Web URL
	 * @param string $lastmod date in which the resource was last edited. Must be in ISO8601 format. You can use `convertDateToISO8601()` or the `convertTimestampToISO8601` to convert a different format
	 * @param float $priority oriority of the URL within the sitemap. Must be between 0.1 and 1
	 * @param string $frequency frequency of update of this resource. Must be a string value between: never, yearly, monthly, weekly, daily, hourly, always
	 * @param bool $has_mobile indicates if this URL is available and optimised for mobile devices
	 * @return void
	 * @throws Exception
	 */
	public function __construct(string $loc, string $lastmod, float $priority = self::PRIORITY_AVERAGE, string $frequency = self::FREQ_DAILY, bool $has_mobile = false) {
		if (!filter_var($loc, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
			throw new Exception('The specified location "'.$loc.'" is not a valid URL');
		}
		if (!self::validISO8601Date($lastmod)) {
			throw new Exception('Invalid lastmod "'.$lastmod.'" format. Date must be in ISO8601 format. Please use the `convertDateToISO8601()` or the `convertTimestampToISO8601` methods.');
		}
		if ($priority < 0.1 || $priority > 1) {
			throw new Exception('Invalid Priority. Priority must be a value between 0.1 and 1');
		}
		$priority = round($priority, 1);
		$this->loc = $loc;
		$this->lastmod = $lastmod;
		$this->priority = $priority;
		$this->frequency = $frequency;
		$this->has_mobile = $has_mobile;
	}

	/**
	 * Retrieve the XML to be added to the Sitemap, based on the parameters passed to the constructor
	 * @return string
	 */
	public function getXml() {
		$xml = file_get_contents(self::BASE_XML);
		$mobile = '';
		if ($this->getHasMobile()) {
			$mobile = '<mobile:mobile/>';
		}
		$xml = strtr($xml, [
			':loc' => $this->getLoc(),
			':lastmod' => $this->getLastmod(),
			':frequency' => $this->getFrequency(),
			':priority' => $this->getPriority(),
			':langs' => '',
			':mobile' => $mobile
		]);
		return $xml;
	}

	/**
	 * Get Loc URL
	 * @return string
	 */
	public function getLoc() {
		return $this->loc;
	}

	/**
	 * Get Lastmod
	 * @return string
	 */
	public function getLastmod() {
		return $this->lastmod;
	}

	/**
	 * Get Priority
	 * @return float
	 */
	public function getPriority() {
		return $this->priority;
	}

	/**
	 * Get Frequency
	 * @return string
	 */
	public function getFrequency() {
		return $this->frequency;
	}

	/**
	 * get Mobile info
	 * @return bool
	 */
	public function getHasMobile() {
		return $this->has_mobile;
	}

	/**
	 * Utility to convert any date to the ISO8601 format
	 * @param string $date any valid date format, except for unix timestamp.
	 * @return string Date in ISO8601 format
	 */
	public static function convertDateToISO8601(string $date) {
		return date('c', strtotime($date));
	}

	/**
	 * Utility to convert Unix Timestamps to the ISO8601 format
	 * @param int $timestamp
	 * @return string Date in ISO8601 format
	 */
	public static function convertTimestampToISO8601(int $timestamp) {
		return date('c', $timestamp);
	}

	/**
	 * Validate if the current date is indeed in ISO8601.
	 * @param string $date cu
	 * @return bool
	 */
	public function validISO8601Date(string $date) {
		if (!is_string($date)) {
			return false;
		}
		$dateTime = \DateTime::createFromFormat(\DateTime::ISO8601, $date);
		if ($dateTime) {
			$check = explode("+", $dateTime->format(\DateTime::ISO8601))[0];
			$date = explode("+", $date)[0];
			if ($check === $date) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Utility to return the ISO8601 date for now
	 * @return string
	 */
	public static function timeNow() {
		return date('c');
	}

	/**
	 * Utility to return the ISO8601 date for 24 hours ago
	 * @return string
	 */
	public static function timeYesterday() {
		return date('c', strtotime("-1 day"));
	}

	/**
	 * Utility to return the ISO8601 date for one week ago
	 * @return string
	 */
	public static function timeOneWeek() {
		return date('c', strtotime("-1 week"));
	}

	/**
	 * Utility to return the ISO8601 date for one month ago
	 * @return string
	 */
	public static function timeOneMonth() {
		return date('c', strtotime("-1 week"));
	}
}
