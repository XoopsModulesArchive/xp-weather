<?php
if(!defined('_CLASS_WEATHER_LOADED')) {
	define('_CLASS_WEATHER_LOADED', true);

// includes Snoopy class for remote file access
require_once("snoopy.class.php");

	class WeatherData {
		var $feedUrl;		// location of the source weather data feed
		var $cacheFile;		// location of the source weather cache data
		var $responseSize;	// maximum length of request data
		var $proxyHost;
		var $proxyPort;
		var $proxyUser;
		var $proxyPass;

		var $feedError;		// fetch error from data feed source
		var $feedResponse;	// response from data feed source
		var $results;		// data results

		var $v_City;
		var $v_SubDiv;
		var $v_Country;
		var $v_Region;
		var $v_Temp;
		var $v_CIcon;
		var $v_WindS;
		var $v_WindD;
		var $v_Baro;
		var $v_Humid;
		var $v_Real;
		var $v_UV;
		var $v_Vis;
		var $v_LastUp;
		var $v_ConText;
		var $v_Fore;
		var $v_Acid;

		var $v_Location;

		/*
		 * Object constructor
		 */
		function WeatherData() {
			$this->feedUrl		= "";
			$this->cacheFile		= "";
			$this->responseSize	= "";
			$this->proxyHost		= "";
			$this->proxyPort		= 80;
			$this->proxyUser		= "";
			$this->proxyPass		= "";
			$this->feedError		= "";		// fetch error from data feed source
			$this->feedResponse	= "";		// response from data feed source
			$this->results		= "";		// data results

			$this->v_City		= "";
			$this->v_SubDiv		= "";
			$this->Country		= "";
			$this->v_Region		= "";
			$this->v_Temp		= "";
			$this->v_CIcon		= "";
			$this->v_WindS		= "";
			$this->v_WindD		= "";
			$this->v_Baro		= "";
			$this->v_Humid		= "";
			$this->v_Real		= "";
			$this->v_UV			= "";
			$this->v_Vis		= "";
			$this->v_LastUp		= "";
			$this->v_ConText		= "";
			$this->v_Fore		= "";
			$this->v_Acid		= "";

			$this->v_Location		= "";
		}
		/*
		 * Data Feed Fetch
		 *
		 */
		function fetchData( $source_url= "", $cache_file = "" ) {
			$snoopy = new Snoopy;

			if ( !empty($source_url) ) { $this->feedUrl = $source_url; }
			if ( !empty($cache_file) ) { $this->cacheFile = $cache_file; }

			if ( !empty($this->responseSize) ) {
				$snoopy->maxlength = $this->responseSize;
			}

			if ( !empty($this->proxyHost) ) {
				$snoopy->proxy_host = $this->proxyHost;
				if ( !empty($this->proxyPort) ) { $snoopy->proxy_port = $this->proxyPort; }
				if ( !empty($this->proxyUser) ) { $snoopy->user = $this->proxyUser; }
				if ( !empty($this->proxyPass) ) { $snoopy->pass = $this->proxyPass; }
			}
		if ( IsSet($this->feedUrl) && !empty($this->feedUrl) ) {
				$snoopy->fetch($this->feedUrl);
			} else {
				$snoopy->response_code = "HTTP/1.1 400 Bad Request\r\n";
				$snoopy->error = "Unable to use the Supplied URL, it is either empty or badly formatted";
			}
			$this->feedResponse = $snoopy->response_code;
			if ( IsSet($snoopy->error) && !empty($snoopy->error) ) {
				$this->feedError = $snoopy->error;
			} else {
				if ( !empty($this->cacheFile) ) {
					$fp = fopen($this->cacheFile, "w");
					fwrite($fp, $snoopy->results);
					fclose($fp);
				} else {
					$this->results = $snoopy->results;
				}
			}
			return;
		}
		function processData() {
			if ( !empty($this->results) ) {
				$answer			= explode("{", $this->results, 2);
				$v_array			= explode(";", $answer[1]);
				$v_tmp			= explode("=", $v_array[0]);
				$this->v_City		= substr($v_tmp[1],2,strlen($v_tmp[1])-3);
				$v_tmp			= explode("=", $v_array[1]);
				$this->v_SubDiv		= substr($v_tmp[1],2,strlen($v_tmp[1])-3);
				$v_tmp			= explode("=", $v_array[2]);
				$this->v_Country		= substr($v_tmp[1],2,strlen($v_tmp[1])-3);
				$v_tmp			= explode("=", $v_array[3]);
				$this->v_Region		= substr($v_tmp[1],2,strlen($v_tmp[1])-3);
				$v_tmp			= explode("=", $v_array[4]);
				$this->v_Temp		= substr($v_tmp[1],2,strlen($v_tmp[1])-3);
				$v_tmp			= explode("=", $v_array[6]);
				$this->v_CIcon		= substr($v_tmp[1],2,strlen($v_tmp[1])-3);
				$v_tmp			= explode("=", $v_array[7]);
				$this->v_WindS		= substr($v_tmp[1],2,strlen($v_tmp[1])-3);
				$v_tmp			= explode("=", $v_array[8]);
				$this->v_WindD		= substr($v_tmp[1],2,strlen($v_tmp[1])-3);
				$v_tmp			= explode("=", $v_array[9]);
				$this->v_Baro		= substr($v_tmp[1],2,strlen($v_tmp[1])-3);
				$v_tmp			= explode("=", $v_array[10]);
				$this->v_Humid		= substr($v_tmp[1],2,strlen($v_tmp[1])-3);
				$v_tmp			= explode("=", $v_array[11]);
				$this->v_Real		= substr($v_tmp[1],2,strlen($v_tmp[1])-3);
				$v_tmp			= explode("=", $v_array[12]);
				$this->v_UV			= substr($v_tmp[1],2,strlen($v_tmp[1])-3);
				$v_tmp			= explode("=", $v_array[13]);
				$this->v_Vis		= substr($v_tmp[1],2,strlen($v_tmp[1])-3);
				$v_tmp			= explode("=", $v_array[14]);
				$this->v_LastUp		= trim(substr($v_tmp[1],2,strlen($v_tmp[1])-12));
				$v_tmp			= explode("=", $v_array[15]);
				$this->v_ConText		= substr($v_tmp[1],2,strlen($v_tmp[1])-3);
				$v_tmp			= explode("=", $v_array[16]);
				$this->v_Fore		= substr($v_tmp[1],2,strlen($v_tmp[1])-3);
				$v_tmp			= explode("=", $v_array[17]);
				$this->v_Acid		= substr($v_tmp[1],2,strlen($v_tmp[1])-3);
				//$this->v_Fore		= explode("|", $v_Fore);
			} else {
				$this->feedError = "No Data to Process";
			}
			return;
		}
		function getIcon($icon) {
			if( $icon <= 10 ) {
				$icon += 20;
			}
			$icon .= ".gif";
			return $icon;
		}
		function getVisibility() {
			if ( $this->v_Vis == "" || strstr($this->v_Vis, "999") ) {
				$this->v_Vis = _UNLIMITED;
			}
			return $this->v_Vis;
		}
		function formatLastUpdate() {
//			$this->v_LastUp = formatTimestamp(filemtime($this->cacheFile));
			return $this->v_LastUp;
		}
		function getCacheData($forcecache=false) {
			if( $forcecache != true && (!file_exists($this->cacheFile) || (filemtime($this->cacheFile) + $this->cacheTimeout - time()) < 0)) {
				$snoopy = new Snoopy;
				$snoopy->fetch($this->sourceUrl);
				$data = $snoopy->results;

				$fp = fopen($this->cacheFile, "w");
				$fp($cacheFile, $data);
				fclose($fp);
			}
			// fsockopen failed the last time, so force cache
			elseif ( $forcecache == true ) {
				if (file_exists($this->cacheFile)) {
					$data = implode('', file($this->cacheFile));
					// set the modified time to a future time, and let the server have time to come up again
					touch($this->cacheFile, time() + $this->cacheTimeout);
				} else {
					$data = "";
				}
			} else {
				$data = implode('', file($this->cacheFile));
			}
			return $data;
		}
		function setSubDiv($subdiv) {
			$this->v_SubDiv = $subdiv;
		}
		function getLocation() {
			$this->v_Location = $this->v_City.", ";
			if ( $this->v_SubDiv <> "" && $this->v_SubDiv <> $this->v_City ) {
				$this->v_Location .= $this->v_SubDiv;
			}
			if ( $this->v_Country <> "" && $this->v_Country <> $this->v_SubDiv ) {
				$this->v_Location .= " " . $this->v_Country;
			}
			return $this->v_Location;
		}
	}
}
?>
