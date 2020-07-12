<?php
/***********************************************************************************/
/*                                                                                 */
/* PNWeather version 0.71b                                                          */
/*         Converted by JNJ (jnj@infobin.com                                       */
/*         http://www.infobin.com                                                  */
/*                                                                                 */
/***********************************************************************************/
/*                                                                                 */
/* Based on MyWeather version 1.0                                                  */
/*         PHP and mySQL Code changes by Richard Benfield aka PcProphet            */
/*         http://www.pc-prophet.com                                               */
/*         http://www.benfield.ws                                                  */
/*                                                                                 */
/*         Html and Graphic work by Chris Myden                                    */
/*         http://www.chrismyden.com/                                              */
/*                                                                                 */
/* MyWeather version 1.0 based on World_Weather version 1.1                        */
/*         By NukeTest.com team                                                    */
/*         http://www.nuketest.com                                                 */
/*                                                                                 */
/***********************************************************************************/
/*                                                                                 */
/* Previously part of PHP-NUKE Add-On 5.0 : Weather AddOn                          */
/* ======================================================                          */
/* Brought to you by PHPNuke Add-On Team                                           */
/* Copyright (c) 2001 by Richard Tirtadji AKA King Richard (rtirtadji@hotmail.com) */
/* http://www.nukeaddon.com                                                        */
/*                                                                                 */
/***********************************************************************************/
/*                                                                                 */
/* Original code based on METEO live v1.0                                          */
/* By Martin Bolduc at martin.bolduc@littera.com                                   */
/* License : Free, do what you want, but please, let my name in reference.         */
/*                                                                                 */
/***********************************************************************************/

function ConvTemp($number, $tpc) {
	$number *= 1;
	if ($number == 0) { return "N/A"; }
	if ($tpc == 0) {
		$number = (5 / 9) * ($number - 32);
		$number = round ($number);
		return "$number&deg;C";
	} else {
		return "$number&deg;F";
	}
}

function ConvPress($number, $tpc) {
	$number *= 1;
	if ($number == 0) { return "N/A"; }
	if ($tpc == 0) {
		$number = $number *33.86;
		$number = round ($number);
		return "$number mbar";
	} else {
		return "$number inHg";
	}
}

function ConvLength($number, $tps) {
	if ( !is_numeric($number) ) { return $number; }
	$number *= 1;
	if ($number == 0) { return "N/A"; }
	if ($tps == 0) {
		$number = $number * 1.609;
		$number = round ($number);
		return "$number m";
	} else {
		return "$number mi";
	}
}

function ConvSpeed($number, $tps) {
	$number *= 1;
	if ($number == 0) { return ""; }
	if ($tps == 0) {
		$number = $number * 1.609;
		$number = round ($number);
		return "$number km/h";
	} else {
		return "$number mph";
	}
}

function ConvReal($number, $tpc) {
	$number *= 1;
	if ($number == 0) { return "N/A"; }
	if ($tpc == 0) {
		$number = (5 / 9) * ($number - 32);
		$number = round ($number);
		return "$number&deg;C";
	} else {
		return "$number&deg;F";
	}
}

function Fore($numbers) {
	if ($numbers == "1") {
		$date=""._WSUN."";
		return "$date";
	} elseif ($numbers == "2") {
		$date=""._WMON."";
		return "$date";
	} elseif ($numbers == "3") {
		$date=""._WTUE."";
		return "$date";
	} elseif ($numbers == "4") {
		$date=""._WWED."";
		return "$date";
	} elseif ($numbers == "5") {
		$date=""._WTHU."";
		return "$date";
	} elseif ($numbers == "6") {
		$date=""._WFRI."";
		return "$date";
	} else {
		$date=""._WSAT."";
		return "$date";
	}
}
?>
