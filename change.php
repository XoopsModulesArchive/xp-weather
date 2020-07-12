<?php
/*****************************************************************************/
/* XP-Weather version 1.72                                                   */
/* 23/01/2004 - sylvainb                                                     */
/*            New adaptation from the 1.7 exoops module to xoops 2.0.5.2     */
/*            No credits to me concerning the original code                  */
/*                                                                           */
/* XP-Weather version 1.3                                                    */
/*    8/4/2002 - davidd, integrated wmo_stations table data into city query  */
/*                                                                           */
/* XP-Weather version 1.1                                                    */
/* XP-Weather version 1.0                                                    */
/* XP-Weather version 0.98                                                   */
/*         Converted by Davidd                                               */
/* 6/19/2002            Fixed module includes                                */
/*                                                                           */
/*****************************************************************************/
/*                                                                           */
/* PNWeather version 0.71c                                                   */
/*         Converted by JNJ (jnj@infobin.com                                 */
/*         http://www.infobin.com                                            */
/*bug with the user infos were detected and killed by Fred Blastov 2002/05/23*/
/*This is why the c of 0.71 that I decided by myself....                     */
/*                                                                           */
/*****************************************************************************/
/*                                                                           */
/* Based on MyWeather version 1.0                                            */
/*         PHP and mySQL Code changes by Richard Benfield aka PcProphet      */
/*         http://www.pc-prophet.com                                         */
/*         http://www.benfield.ws                                            */
/*                                                                           */
/*         Html and Graphic work by Chris Myden                              */
/*         http://www.chrismyden.com/                                        */
/*                                                                           */
/* MyWeather version 1.0 based on World_Weather version 1.1                  */
/*         By NukeTest.com team                                              */
/*         http://www.nuketest.com                                           */
/*                                                                           */
/*****************************************************************************/
/*                                                                           */
/* Previously part of PHP-NUKE Add-On 5.0 : Weather AddOn                    */
/* ======================================================                    */
/* Brought to you by PHPNuke Add-On Team                                     */
/* Copyright (c) 2001 by Richard Tirtadji (rtirtadji@hotmail.com)            */
/* http://www.nukeaddon.com                                                  */
/*                                                                           */
/*****************************************************************************/
/*                                                                           */
/* Original code based on METEO live v1.0                                    */
/* By Martin Bolduc at martin.bolduc@littera.com                             */
/* License : Free, do what you want, but please, let my name in reference.   */
/*                                                                           */
/*****************************************************************************/
include_once("../../mainfile.php");
include_once("header.php");
include_once(XOOPS_ROOT_PATH."/modules/".$xoopsModule->dirname()."/class/phpsimplexml.class.php");

function frmRegion($utype, $regionid = 0) {
	global $xoopsDB;

	echo "<form action=\"change.php\" method=\"post\"><TD><b>"._REG.": </b><br></TD>\n";
	echo "<TD><input type=\"hidden\" name=\"weatherop\" value=\"$utype"."Country\">";

	$rlist = $xoopsDB->query("SELECT region_title, region_id from ".$xoopsDB->prefix("region")." ORDER BY region_title");

	echo "<select name=\"Selection\" onChange='submit()'>\n";
	if ($regionid == 0) {
		echo "<option selected value=\"0\">"._WSELECT._REG."</option>\n";
		$regionid='';
	}
	while(list($wregionl, $wregionidl) = $xoopsDB->fetchRow($rlist)) {
		if ($wregionidl==$regionid) { $sel = "selected "; }
		echo "<option $sel value=\"$wregionidl\">$wregionl</option>\n";
		$sel = "";
	}
	echo "</select><br></TD></form>\n";
}

function frmCountry($utype, $regionid, $countryid = 0) {
	global $xoopsDB;

	echo "<form action=\"change.php\" method=\"post\"><TD><b>"._WCNTRY.": </b><br></TD>\n";
	echo "<TD><input type=\"hidden\" name=\"weatherop\" value=\"$utype"."SubDiv\">";

	$clist = $xoopsDB->query("SELECT country_title, country_id from ".$xoopsDB->prefix("country")." where region_id='$regionid' ORDER BY country_title");

	echo "<select name=\"Selection\" onChange='submit()'>\n";
	if ($countryid == 0) {
		echo "<option selected value=\"0\">"._WSELECT._WCNTRY."</option>\n";
		$countryid='';
	}
	while(list($wcountryl, $wcountryidl) = $xoopsDB->fetchRow($clist)) {
		if ($wcountryidl==$countryid) { $sel = "selected "; }
		echo "<option $sel value=\"$wcountryidl\">$wcountryl</option>\n";
		$sel = "";
	}
	echo "</select><br></TD></form>\n";
}

function frmSubDiv($utype, $countryid, $subdivid = 0) {
	global $xoopsDB;

	echo "<form action=\"change.php\" method=\"post\"><TD><b>"._WSUBDIV.": </b><br></TD>\n";
	echo "<TD><input type=\"hidden\" name=\"weatherop\" value=\"$utype"."City\">";

	$slist = $xoopsDB->query("SELECT subdiv_title, subdiv_id from ".$xoopsDB->prefix("subdiv")." where country_id='$countryid' ORDER BY subdiv_title");

	echo "<select name=\"Selection\" onChange='submit()'>\n";
	if ($subdivid == 0) {
		echo "<option selected value=\"0\">"._WSELECT._WSUBDIV."</option>\n";
		$subdivid='';
	}
	while(list($wsubdivl, $wsubdividl) = $xoopsDB->fetchRow($slist)) {
		if ($wsubdividl==$subdivid) { $sel = "selected "; }
		echo "<option $sel value=\"$wsubdividl\">$wsubdivl</option>\n";
		$sel = "";
	}
	echo "</select><br></TD></form>\n";
}

function frmCity($utype, $subdivid, $newaccid = 'NA', $regionid = 0) {
	global $xoopsDB;

	echo "<form action=\"change.php\" method=\"post\"><TD><b>"._WCITY.": </b><br></TD>\n";
	echo "<TD><input type=\"hidden\" name=\"weatherop\" value=\"$utype"."Station\">";

	if (($regionid >= 4 and $regionid <= 6) or $regionid == 10) {
		$ctylist = $xoopsDB->query("SELECT city_title, accid from ".$xoopsDB->prefix("city")." where subdiv_id='$subdivid' ORDER BY city_title");
	} else {
		$ctylist = $xoopsDB->query("SELECT station_name as city_title, icao_code as accid from ".$xoopsDB->prefix("wmo_stations")." where subdiv_id='$subdivid' ORDER BY station_name");
	}
	echo "<select name=\"Selection\" onChange='submit()'>\n";

	if ($newaccid == 'NA') {
		echo "<option selected value=\"0\">"._WSELECT._WCITY."</option>\n";
		$newaccid='';
	}
	while(list($wcityl, $waccidl) = $xoopsDB->fetchRow($ctylist)) {
		if ($waccidl==$newaccid) { $sel = "selected "; }
		echo "<option $sel value=\"$waccidl\">$wcityl</option>\n";
		$sel = "";
	}
	echo "</select><br></TD></form>\n";
}

function frmStation($utype, $subdivid, $newaccid, $wcid='NA', $regionid=0) {
	global $xoopsDB, $xoopsModule, $cache_time, $wcdata_url, $debugurl;

	$_root="search";
	$_node="loc";
	$_locid="id";
	$_name="content";

	$cache_file = "".XOOPS_ROOT_PATH."/modules/".$xoopsModule->dirname()."/cache/wcdata.$newaccid";
	$time = split(" ", microtime());
	srand((double)microtime()*1000000);
	$cache_time_rnd = 300 - rand(0, 600);

	$simpleXML = new phpSimpleXML();

	echo "<form action=\"change.php\" method=\"post\"><TD><b>"._WSTATION.": </b><br></TD>\n";
	echo "<TD><input type=\"hidden\" name=\"weatherop\" value=\"$utype"."PreSave\">";
	echo "<input type=\"hidden\" name=\"accid\" value=\"$newaccid\">";

//	if ( $regionid == 11 || $regionid == 10 || $regioninid == 9 || ($regionid >=4 && $regionid <=6) ) {
//	if ( $regionid < 4 && $regionid >= 6 && $regionid != 10 ) {

	list($subdiv_title, $countryid) = $xoopsDB->fetchRow($xoopsDB->query("SELECT subdiv_title, country_id from ".$xoopsDB->prefix("subdiv")." where subdiv_id='$subdivid'"));
	list($country_title) = $xoopsDB->fetchRow($xoopsDB->query("SELECT country_title from ".$xoopsDB->prefix("country")." where country_id='$countryid'"));

	// North/Central/South America Region
	if (($regionid >= 4 && $regionid <= 6) || $regionid == 10 || $regionid == 9) {
		list($city_title) = $xoopsDB->fetchRow($xoopsDB->query("SELECT city_title from ".$xoopsDB->prefix("city")." where accid='$newaccid'"));
		$station_type = "TWC";
	}
	if (!$city_title) {
	// The rest of the World
		list($city_title) = $xoopsDB->fetchRow($xoopsDB->query("SELECT station_name as city_title from ".$xoopsDB->prefix("wmo_stations")." where icao_code='$newaccid'"));
		$station_type = "WMO";
	}
	$area = ",$subdiv_title";
	// Canada, US
	if (($subdiv_title <> $country_title && $regionid <> 10) || $regionid == 4 ) {
		$area = ",$country_title";
	}
	echo "<select name=\"Selection\" onChange='submit()'>\n";

	if ($wcid == 'NA') {
		echo "<option selected value=\"0\">"._WSELECT._WSTATION."</option>\n";
		$wcid='';
	}
	if ( (!(file_exists($cache_file))) || (((filectime($cache_file) + $cache_time - $time[1]) + $cache_time_rnd < 0) || (!(filesize($cache_file))) && cache_time != -1) ) {
		$mywd = new WeatherData;

		$dcount = substr_count($city_title, "-");
		if ($dcount == 1) {
			if (strpos($city_title, "-") >= 4) {
				if ($regionid == 2) {
					$city_title = str_replace("-", " ", $city_title);
				} else {
					$city_title = str_replace("-", "/", $city_title);
				}
			}
		} elseif ($dcount > 1) {
			$city_title = str_replace("-", " ", $city_title);
		}
		$city_title = explode("/", $city_title);
		$city_title = trim($city_title[0]);
		$pos = strlen($city_title);

		if (stristr($city_title, "Airport") ||
		    stristr($city_title, "Aerodrome") ||
		    stristr($city_title, "Aero.") ||
		    stristr($city_title, "Aeroporto") ||
		    stristr($city_title, "Tw-Afb") ||
		    stristr($city_title, "Bel-Afb") ||
		    stristr($city_title, " Ab") ||
		    stristr($city_title, "Observatorio")) {
			$pos = strrpos($city_title, " ");
		}
		if (stristr($city_title, "International Airport")) {
			$pos = strrpos($city_title, "International Airport");
		} elseif (stristr($city_title, "Marine Corps Air Station")) {
			$pos = strrpos($city_title, "Marine Corps Air Station");
		} elseif (stristr($city_title, "United States Naval Air Station")) {
			$pos = strrpos($city_title, "United States Naval Air Station");
		} elseif (stristr($city_title, "Weather Centre")) {
			$pos = strrpos($city_title, "Weather Centre");
		} elseif (stristr($city_title, "Royal Air Force Base")) {
			$pos = strrpos($city_title, "Royal Air Force Base");
		}
		$city_title = substr( $city_title, 0, $pos );
		$wcdata_url .= "&where=" . urlencode($city_title). urlencode($area);

		if ( !empty($proxy_host) ) {
			$mywd->proxyHost = $proxy_host;
			if ( !empty($proxy_port) ) { $mywd->proxyPort = $proxy_port; }
			if ( !empty($proxy_user) ) { $mywd->User = $proxy_user; }
			if ( !empty($proxy_pwd) ) { $mywd->Pass = $proxy_pwd; }
		}
		$mywd->fetchData($wcdata_url, $cache_file);
		if ( IsSet($mywd->feedError) && !empty($mywd->feedError) ) {
			$failuremessage = _UNAVAIL.": $mywd->feedResponse ($mywd->feedError)\n";
		}
	}
	if ( !isSet($failuremessage) || empty($failuremessage) ) {

		$opt = array();
		$opt["keeproot"] =1;
		$opt["forcecontent"] =1;
		$xml = $simpleXML->XMLin($cache_file, $opt);
		$xmlcount = count($xml);
//		foreach ($xml as $key => $value)
//			foreach($value as $key2 => $value2)
//				$newval .= "key2 = ".$key2." value2 = ".$value2. " ";
//			$failuremessage .= "key = ".$key." value = ".$value." value2 = ".$newval." ";

//		$failuremessage = "xmlcount = ".$xmlcount;
		if ( $xmlcount == 1 ) {
			$wcidl= $xml[$_root][$_node][$_locid];
			$wcityl = $xml[$_root][$_node][$_name];

			if ($wcidl==$wcid) { $sel = "selected "; }
			echo "<option $sel value=\"$wcidl\">$wcityl</option>\n";
			$sel = "";
		} else {
			for($index=0; $index<$xmlcount; $index++) {
				$wcidl= $xml[$_root][$index][$_node][$_locid];
				$wcityl = $xml[$_root][$index][$_node][$_name];
				if ($wcidl==$wcid) { $sel = "selected "; }
				echo "<option $sel value=\"$wcidl\">$wcityl</option>\n";
				$sel = "";
			}
		}
		unlink($cache_file);
	}
	echo "</select><br>";
	echo "<input type=\"hidden\" name=\"stype\" value=\"$station_type\">";
	if ($debugurl == 1) {
		echo "</TD></form>newaccid: $newaccid<br>region: $regionid<br>city: $city_title<br>pos: $pos<br>country: $country_title<br>url: $wcdata_url<br>error: $failuremessage\n";
	} else {
		echo "</TD></form>$failuremessage\n";
	}
}

function SelRegion($utype, $wuid=0) {
	global $xoopsDB, $username, $module_name, $pnwusermod;

	if ($utype=="Anon" || $pnwusermod == 0) {

		echo "<h4><b>$module_name "._SETTINGS."</b></h4>";
		echo "<li type=\"square\"> "._WNOTLOGGEDON." &nbsp;[<a href=\"../../user.php\">"._WLOGIN."</a>]<br><br>\n";
		echo ""._IFYOU." <a href=\"../../register.php\">"._WREGISTER."</a>.<br><br>\n";
		echo "<a href=\"../../register.php\">"._WJOIN."</a><br><br>\n";
	} else {
		list($saved_accid, $saved_wcid, $station_type, $saved_tpc, $saved_tps) = $xoopsDB->fetchRow($xoopsDB->query("select accid, wcid, station_type, tpc, tps from ".$xoopsDB->prefix("userweather")." where userid='$wuid'"));

		if (!$saved_accid) {
			list($saved_accid, $saved_wcid, $station_type, $saved_tpc, $saved_tps) = $xoopsDB->fetchRow($xoopsDB->query("select accid, wcid, station_type, tpc, tps from ".$xoopsDB->prefix("userweather")." where userid='0'"));
		}
		if ($station_type == "TWC") {
			$saved_stationid = $saved_wcid;
			list($wcity, $subdivid) = $xoopsDB->fetchRow($xoopsDB->query("select city_title, subdiv_id from ".$xoopsDB->prefix("city")." where accid='$saved_accid'"));
		}
		if (!$wcity || $station_type == "WMO") {
			$saved_stationid = $saved_wcid;
			list($wcity, $subdivid) = $xoopsDB->fetchRow($xoopsDB->query("select station_name, subdiv_id from ".$xoopsDB->prefix("wmo_stations")." where icao_code='$saved_accid'"));
		}
		list($wsubdiv, $countryid) = $xoopsDB->fetchRow($xoopsDB->query("select subdiv_title, country_id from ".$xoopsDB->prefix("subdiv")." where subdiv_id='$subdivid'"));
		list($wcountry, $regionid) = $xoopsDB->fetchRow($xoopsDB->query("select country_title, region_id from ".$xoopsDB->prefix("country")." where country_id='$countryid'"));
		list($wregion) = $xoopsDB->fetchRow($xoopsDB->query("select region_title from ".$xoopsDB->prefix("region")." where region_id='$regionid'"));

		echo "<h4><b>$module_name "._SETTINGS."</b></h4>";
		echo "<li type=\"square\">"._PLSCHNGLOC."<br><br>\n";
		echo "<B><small>"._CURSETT.":</small></B>\n";
		echo "<TABLE cellspacing=\"1\" cellpadding=\"1\" class=\"head\">\n";
		echo "<TR class=\"even\"><TD><b><small>"._REG.":</small></b></TD><TD><small>$wregion</small></TD></TR><TR class=\"even\"><TD>\n";
		echo "<b><small>"._WCNTRY.":</small></b></TD><TD><small>$wcountry</small></TD></TR><TR class=\"even\"><TD>\n";
		echo "<b><small>"._WSUBDIV.":</small></b></TD><TD><small>$wsubdiv</small></TD></TR><TR class=\"even\"><TD>\n";
		echo "<b><small>"._WCITY.":</small></b></TD><TD><small>$wcity ($saved_stationid)</small></TD></TR><TR class=\"even\"><TD>\n";
		if ($saved_tpc == 0) {
			echo "<b><small>"._WTEMP.":</small></b></TD><TD><small>&deg;C </small></TD></TR><TR class=\"even\"><TD>\n";
		} else {
			echo "<b><small>"._WTEMP.":</small></b></TD><TD><small>&deg;F </small></TD></TR><TR class=\"even\"><TD>\n";
		}
		if ($saved_tps == 0) {
			echo "<b><small>"._WINDSPD.":</small></b></TD><TD><small>km/h </small></TD></TR></TABLE>\n";
		} else {
			echo "<b><small>"._WINDSPD.":</small></b></TD><TD><small>mph </small></TD></TR></TABLE>\n";
		}
		echo "<br><TABLE><TR>\n";
		frmRegion($utype, $regionid);
		echo "</TR><TR>";
		frmCountry($utype, $regionid, $countryid);
		echo "</TR><TR>";
		frmSubDiv($utype, $countryid, $subdivid);
		echo "</TR><TR>";
		frmCity($utype, $subdivid, $saved_accid, $regionid);
		echo "</TR></TABLE><br>\n";
		echo "<form action=\"change.php\" method=\"post\"><b>"._WVIEWPREF."</b><br><br>\n";
		echo ""._WTEMP." "._IN.": <br>\n";
		echo "<input type=\"radio\" ";

		if ($saved_tpc == 0) { echo "checked ";}
		echo "name=\"newtpc\" value=0> "._CELSIUS."<br>\n";
		echo "<input type=\"radio\" ";

		if ($saved_tpc == 1) { echo "checked ";}
		echo "name=\"newtpc\" value=1> "._FAHREN."<br><br>\n";
		echo ""._WINDSPD." "._IN.": <br>\n";
		echo "<input type=\"radio\" ";

		if ($saved_tps == 0) { echo "checked ";}
		echo "name=\"newtps\" value=0> km/h ("._KPH.")<br>\n";
		echo "<input type=\"radio\" ";

		if ($saved_tps == 1) { echo "checked ";}
		echo "name=\"newtps\" value=1> mph ("._MPH.")<br>\n";
		echo "<input type=\"hidden\" name=\"accid\" value=\"$saved_accid\"><br>\n";
		echo "<input type=\"hidden\" name=\"weatherop\" value=\"$utype"."Save\">\n";
		echo "<input type=\"submit\" value=\""._SAVECHGS."\">\n";
		echo "</form>\n";
	}
}

function SelCountry($utype, $regionid, $wuid = 0) {
	global $xoopsDB, $username, $module_name, $pnwusermod;

	if ($utype=="Anon" || $pnwusermod == 0) {
		echo ""._WNOTLOGGEDON."<br>\n";
		echo ""._IFYOU."<a href=\"../../user.php\">"._WMEMBER."</a>"._UCANCHNG."<br>\n";
		echo ""._SOWHATRU."<a href=\"../../user.php\">"._WJOIN."</a>"._ALREADY."<br>\n";
	} else {
		list($wregion) = $xoopsDB->fetchRow($xoopsDB->query("select region_title from ".$xoopsDB->prefix("region")." where region_id='$regionid'"));

		$result1 = $xoopsDB->query("SELECT country_id from ".$xoopsDB->prefix("country")." where region_id='$regionid'");

		if($xoopsDB->getRowsNum($result1) > 1) {
			echo "<h4><b>$module_name "._SETTINGS."</b></h4>";
			echo "<li type=\"square\"> "._WSELECT._WCNTRY."<br>\n";
			echo "<br><TABLE><TR>\n";
			frmRegion($utype, $regionid);
			echo "</TR><TR>";
			frmCountry($utype, $regionid, 0);
			echo "</TR></TABLE><br>\n";
		} else {
			list($countryid) = $xoopsDB->fetchRow($result1);
			echo "<h4><b>$module_name "._SETTINGS."</b></h4>";
			echo "<li type=\"square\">"._WSELECT._WSUBDIV."<br>\n";
			echo "<br><TABLE><TR>\n";
			frmRegion($utype, $regionid);
			echo "</TR><TR>";
			frmCountry($utype, $regionid, $countryid);
			echo "</TR><TR>";
			frmSubDiv($utype, $countryid, 0);
			echo "</TR></TABLE><br>\n";
		}
	}
}

function SelSubDiv($utype, $countryid, $wuid = 0) {
	global $xoopsDB, $username, $module_name, $pnwusermod;

	if ($utype=="Anon" || $pnwusermod == 0) {
		echo ""._WNOTLOGGEDON."<br>\n";
		echo ""._IFYOU."<a href=\"../../user.php\">"._WMEMBER."</a>"._UCANCHNG."<br>\n";
		echo ""._SOWHATRU."<a href=\"../../user.php\">"._WJOIN."</a>"._ALREADY."<br>\n";
	} else {
		list($wcountry, $regionid) = $xoopsDB->fetchRow($xoopsDB->query("select country_title, region_id from ".$xoopsDB->prefix("country")." where country_id='$countryid'"));
		list($wregion) = $xoopsDB->fetchRow($xoopsDB->query("select region_title from ".$xoopsDB->prefix("region")." where region_id='$regionid'"));

		$result1 = $xoopsDB->query("SELECT subdiv_id from ".$xoopsDB->prefix("subdiv")." where country_id='$countryid'");

		if($xoopsDB->getRowsNum($result1) > 1) {
			echo ""._WSELECT._WSUBDIV."<br>\n";
			echo "<br><TABLE><TR>\n";
			frmRegion($utype, $regionid);
			echo "</TR><TR>";
			frmCountry($utype, $regionid, $countryid);
			echo "</TR><TR>";
			frmSubDiv($utype, $countryid, 0);
			echo "</TR></TABLE><br>\n";
		} else {
			echo "<h4><b>$module_name "._SETTINGS."</b></h4>";
			echo "<li type=\"square\"> "._WSELECT._WCITY."<br>\n";
			list($subdivid) = $xoopsDB->fetchRow($result1);
			echo "<br><TABLE><TR>\n";
			frmRegion($utype, $regionid);
			echo "</TR><TR>";
			frmCountry($utype, $regionid, $countryid);
			echo "</TR><TR>";
			frmSubDiv($utype, $countryid, $subdivid);
			echo "</TR><TR>";
			frmCity($utype, $subdivid, 0, $regionid);
			echo "</TR></TABLE><br>\n";
		}
	}
}

function SelCity($utype, $subdivid, $wuid = 0) {
	global $xoopsDB, $username, $module_name, $pnwusermod;

	if ($utype=="Anon" || $pnwusermod == 0) {
		echo ""._WNOTLOGGEDON."<br>\n";
		echo ""._IFYOU."<a href=\"../../user.php\">"._WMEMBER."</a>"._UCANCHNG."<br>\n";
		echo ""._SOWHATRU."<a href=\"../../user.php\">"._WJOIN."</a>"._ALREADY."<br>\n";
	} else {
		list($wsubdiv, $countryid) = $xoopsDB->fetchRow($xoopsDB->query("select subdiv_title, country_id from ".$xoopsDB->prefix("subdiv")." where subdiv_id='$subdivid'"));
		list($wcountry, $regionid) = $xoopsDB->fetchRow($xoopsDB->query("select country_title, region_id from ".$xoopsDB->prefix("country")." where country_id='$countryid'"));
		list($wregion) = $xoopsDB->fetchRow($xoopsDB->query("select region_title from ".$xoopsDB->prefix("region")." where region_id='$regionid'"));

		echo "<h4><b>$module_name "._SETTINGS."</b></h4>";
		echo "<li type=\"square\"> "._WSELECT._WCITY."<br>\n";
		echo "<br><TABLE><TR>\n";
		frmRegion($utype, $regionid);
		echo "</TR><TR>";
		frmCountry($utype, $regionid, $countryid);
		echo "</TR><TR>";
		frmSubDiv($utype, $countryid, $subdivid);
		echo "</TR><TR>";
		frmCity($utype, $subdivid, 'NA', $regionid);
		echo "</TR></TABLE><br>\n";
	}
}

function SelStation($utype, $accid, $wuid = 0) {
	global $xoopsDB, $username, $module_name, $pnwusermod;

	if ($utype=="Anon" || $pnwusermod == 0) {
		echo ""._WNOTLOGGEDON."<br>\n";
		echo ""._IFYOU."<a href=\"../../user.php\">"._WMEMBER."</a>"._UCANCHNG."<br>\n";
		echo ""._SOWHATRU."<a href=\"../../user.php\">"._WJOIN."</a>"._ALREADY."<br>\n";
	} else {
		list($subdivid) = $xoopsDB->fetchRow($xoopsDB->query("select subdiv_id from ".$xoopsDB->prefix("city")." where accid='$accid'"));
		if ( !IsSet($subdivid) || empty($subdivid) ) {
			list($subdivid) = $xoopsDB->fetchRow($xoopsDB->query("select subdiv_id from ".$xoopsDB->prefix("wmo_stations")." where icao_code='$accid'"));
		}
		list($wsubdiv, $countryid) = $xoopsDB->fetchRow($xoopsDB->query("select subdiv_title, country_id from ".$xoopsDB->prefix("subdiv")." where subdiv_id='$subdivid'"));
		list($wcountry, $regionid) = $xoopsDB->fetchRow($xoopsDB->query("select country_title, region_id from ".$xoopsDB->prefix("country")." where country_id='$countryid'"));
		list($wregion) = $xoopsDB->fetchRow($xoopsDB->query("select region_title from ".$xoopsDB->prefix("region")." where region_id='$regionid'"));

		echo "<h4><b>$module_name "._SETTINGS."</b></h4>";
		echo "<li type=\"square\"> "._WSELECT._WSTATION."<br>\n";
		echo "<br><TABLE><TR>\n";
		frmRegion($utype, $regionid);
		echo "</TR><TR>";
		frmCountry($utype, $regionid, $countryid);
		echo "</TR><TR>";
		frmSubDiv($utype, $countryid, $subdivid);
		echo "</TR><TR>";
		frmCity($utype, $subdivid, $accid, $regionid);
		echo "</TR><TR>";
		frmStation($utype, $subdivid, $accid, "NA", $regionid);
		echo "</TR></TABLE><br>\n";
	}
}

function PreSave($utype, $accid, $wcid, $stype, $wuid=0) {
	global $xoopsDB, $xoopsUser, $username, $module_name, $pnwusermod;

	if ($utype=="Anon" || $pnwusermod == 0) {
		echo ""._WNOTLOGGEDON."<br>\n";
		echo ""._IFYOU."<a href=\"user.php\">"._WMEMBER."</a>"._UCANCHNG."<br>\n";
		echo ""._SOWHATRU."<a href=\"user.php\">"._WJOIN."</a>"._ALREADY."<br>\n";
	} elseif (!$xoopsUser->isAdmin() and ($utype=="Admin"or $pnwusermod == 0)) {
			echo ""._SORRYNOTADMIN."<br>\n";
			echo ""._ENSURELOGGEDON."<br>\n";
	} else {
		list($saved_accid, $saved_wcid, $saved_tpc, $saved_tps) = $xoopsDB->fetchRow($xoopsDB->query("select accid, wcid, tpc, tps from ".$xoopsDB->prefix("userweather")." where userid='$wuid'"));

		if (!$saved_accid) {
			list($saved_accid, $saved_wcid, $saved_tpc, $saved_tps) = $xoopsDB->fetchRow($xoopsDB->query("select accid, wcid, tpc, tps from ".$xoopsDB->prefix("userweather")." where userid='0'"));
		}
		if ($stype == "TWC" ) {
			list($wcity, $subdivid) = $xoopsDB->fetchRow($xoopsDB->query("select city_title, subdiv_id from ".$xoopsDB->prefix("city")." where accid='$saved_accid'"));
		}
		if (!$wcity || $stype == "WMO" ) {
			list($wcity, $subdivid) = $xoopsDB->fetchRow($xoopsDB->query("select station_name, subdiv_id from ".$xoopsDB->prefix("wmo_stations")." where icao_code='$saved_accid'"));
		}
		list($wsubdiv, $countryid) = $xoopsDB->fetchRow($xoopsDB->query("select subdiv_title, country_id from ".$xoopsDB->prefix("subdiv")." where subdiv_id='$subdivid'"));
		list($wcountry, $regionid) = $xoopsDB->fetchRow($xoopsDB->query("select country_title, region_id from ".$xoopsDB->prefix("country")." where country_id='$countryid'"));
		list($wregion) = $xoopsDB->fetchRow($xoopsDB->query("select region_title from ".$xoopsDB->prefix("region")." where region_id='$regionid'"));

		echo "<h4><b>$module_name "._SETTINGS."</b></h4>";
		echo "<li type=\"square\">"._PLSCHNGLOC."<br><br>\n";
		echo "<B><font color=\"#FF0000\">"._CLICKSAVE."<br><br>\n";
		echo "<B><small>"._CURSETT.":</small></B>\n";
		echo "<TABLE cellspacing=\"1\" cellpadding=\"1\" class=\"head\">\n";
		echo "<TR class=\"even\"><TD><b><small>"._REG.":</small></b></TD><TD><small>$wregion</small></TD></TR><TR class=\"even\"><TD>\n";
		echo "<b><small>"._WCNTRY.":</small></b></TD><TD><small>$wcountry</small></TD></TR><TR class=\"even\"><TD>\n";
		echo "<b><small>"._WSUBDIV.":</small></b></TD><TD><small>$wsubdiv</small></TD></TR><TR class=\"even\"><TD>\n";
		echo "<b><small>"._WCITY.":</small></b></TD><TD><small>$wcity ($saved_accid)</small></TD></TR><TR class=\"even\"><TD>\n";
		echo "<b><small>"._WSTATION.":</small></b></TD><TD><small>$saved_wcid</small></TD></TR><TR class=\"even\"><TD>\n";

		if ($saved_tpc == 0) {
			echo "<b><small>"._WTEMP.":</small></b></TD><TD><small>&deg;C </small></TD></TR><TR class=\"even\"><TD>\n";
		} else {
			echo "<b><small>"._WTEMP.":</small></b></TD><TD><small>&deg;F </small></TD></TR><TR class=\"even\"><TD>\n";
		}
		if ($saved_tps == 0) {
			echo "<b><small>"._WINDSPD.":</small></b></TD><TD><small>km/h </small></TD></TR></TABLE>\n";
		} else {
			echo "<b><small>"._WINDSPD.":</small></b></TD><TD><small>mph </small></TD></TR></TABLE>\n";
		}
		echo "<br><br>\n";
		$wcity = "";
		$subdivid = 0;
		if ($stype == "TWC" ) {
			list($wcity, $subdivid) = $xoopsDB->fetchRow($xoopsDB->query("select city_title, subdiv_id from ".$xoopsDB->prefix("city")." where accid='$accid'"));
		}
		if (!$wcity || $stype == "WMO") {
			list($wcity, $subdivid) = $xoopsDB->fetchRow($xoopsDB->query("select station_name, subdiv_id from ".$xoopsDB->prefix("wmo_stations")." where icao_code='$accid'"));
		}
		list($wsubdiv, $countryid) = $xoopsDB->fetchRow($xoopsDB->query("select subdiv_title, country_id from ".$xoopsDB->prefix("subdiv")." where subdiv_id='$subdivid'"));
		list($wcountry, $regionid) = $xoopsDB->fetchRow($xoopsDB->query("select country_title, region_id from ".$xoopsDB->prefix("country")." where country_id='$countryid'"));
		list($wregion) = $xoopsDB->fetchRow($xoopsDB->query("select region_title from ".$xoopsDB->prefix("region")." where region_id='$regionid'"));

		$station_name = "$wcity,";
		if ( !IsSet($wsubdiv) || empty($wsubdiv) ) {
			$station_name .= "$wcountry";
		} else {
			$station_name .= "$wsubdiv";
		}
		echo ""._NEWSET.":<br>\n";
		echo "<br><TABLE><TR>\n";
		frmRegion($utype, $regionid);
		echo "</TR><TR>";
		frmCountry($utype, $regionid, $countryid);
		echo "</TR><TR>";
		frmSubDiv($utype, $countryid, $subdivid);
		echo "</TR><TR>";
		frmCity($utype, $subdivid, $accid, $regionid);
		echo "</TR><TR>";
		frmStation($utype, $subdivid, $accid, $wcid, $regionid);
		echo "</TR></TABLE><br>\n";
		echo "<form action=\"change.php\" method=\"post\"><b>"._WVIEWPREF."</b><br><br>\n";
		echo ""._WTEMP." "._IN.": <br>\n";
		echo "<input type=\"radio\" ";
		if ($saved_tpc == 0) { echo "checked ";}
		echo "name=\"newtpc\" value=0> "._CELSIUS."<br>\n";
		echo "<input type=\"radio\" ";
		if ($saved_tpc == 1) { echo "checked ";}
		echo "name=\"newtpc\" value=1> "._FAHREN."<br><br>\n";
		echo ""._WINDSPD." "._IN.": <br>\n";
		echo "<input type=\"radio\" ";
		if ($saved_tps == 0) { echo "checked ";}
		echo "name=\"newtps\" value=0> km/h ("._KPH.")<br>\n";
		echo "<input type=\"radio\" ";
		if ($saved_tps == 1) { echo "checked ";}
		echo "name=\"newtps\" value=1> mph ("._MPH.")<br>\n";
		echo "<input type=\"hidden\" name=\"accid\" value=\"$accid\"><br>\n";
		echo "<input type=\"hidden\" name=\"wcid\" value=\"$wcid\"><br>\n";
		echo "<input type=\"hidden\" name=\"wstype\" value=\"$stype\"><br>\n";
		echo "<input type=\"hidden\" name=\"wstation\" value=\"$station_name\"><br>\n";
		echo "<input type=\"hidden\" name=\"weatherop\" value=\"$utype"."Save\">\n";
		echo "<input type=\"submit\" value=\""._SAVECHGS."\">\n";
		echo "</form>\n";
	}
}

function SaveSet($utype, $tpc, $tps, $accid, $wcid, $wstype, $wstation, $wuid=0) {
	global $xoopsDB, $userid, $username, $admin, $module_name, $xoopsUser, $pnwusermod;

	if ($utype=="Anon") {
		echo ""._WNOTLOGGEDON."<br>\n";
		echo ""._IFYOU."<a href=\"../../user.php\">"._WMEMBER."</a>"._UCANCHNG."<br>\n";
		echo ""._SOWHATRU."<a href=\"../../user.php\">"._WJOIN."</a>"._ALREADY."<br>\n";
	} else {
		if (!$xoopsUser->isAdmin() and ($utype=="Admin" or $pnwusermod == 0)) {
			echo ""._SORRYNOTADMIN."<br>\n";
			echo ""._ENSURELOGGEDON."<br>\n";
		} else {
			list($testaccid) = $xoopsDB->fetchRow($xoopsDB->query("select accid from ".$xoopsDB->prefix("userweather")." where userid='$wuid'"));
			if(!$testaccid) {
				$newquery = "INSERT ".$xoopsDB->prefix("userweather")." set accid='$accid', wcid='$wcid', station_type='$wstype', station_name='$wstation', tpc='$tpc', tps='$tps', userid='$wuid'";
			} else {
				$newquery = "UPDATE ".$xoopsDB->prefix("userweather")." set accid='$accid', wcid='$wcid', station_type='$wstype', station_name='$wstation', tpc='$tpc', tps='$tps' where userid='$wuid'";
			}
			if(!$xoopsDB->query($newquery)) {
				echo "<br>".$xoopsDB->error();
				echo "<br><b>"._ERRORCADMIN."</b><br>";
			} else {
				list($saved_accid, $saved_wcid, $saved_tpc, $saved_tps) = $xoopsDB->fetchRow($xoopsDB->query("select accid, wcid, tpc, tps from ".$xoopsDB->prefix("userweather")." where userid='$wuid'"));
				if ($wstype == "TWC") {
					list($wcity, $subdivid) = $xoopsDB->fetchRow($xoopsDB->query("select city_title, subdiv_id from ".$xoopsDB->prefix("city")." where accid='$saved_accid'"));
				}
				if (!$wcity || $wstype == "WMO") {
					list($wcity, $subdivid, $wregion) = $xoopsDB->fetchRow($xoopsDB->query("select station_name, subdiv_id, region_id from ".$xoopsDB->prefix("wmo_stations")." where icao_code='$saved_accid'"));
				}
				list($wsubdiv, $countryid) = $xoopsDB->fetchRow($xoopsDB->query("select subdiv_title, country_id from ".$xoopsDB->prefix("subdiv")." where subdiv_id='$subdivid'"));
				list($wcountry, $regionid) = $xoopsDB->fetchRow($xoopsDB->query("select country_title, region_id from ".$xoopsDB->prefix("country")." where country_id='$countryid'"));
				list($wregion) = $xoopsDB->fetchRow($xoopsDB->query("select region_title from ".$xoopsDB->prefix("region")." where region_id='$regionid'"));
				echo "<h4><b>$module_name "._SETTINGS."</b></h4>";
				echo ""._SETSAVED."<br><br>\n";
				echo ""._SAVEDSETR.":<br><br>\n";
				echo "<TABLE cellspacing=\"1\" cellpadding=\"1\" class=\"head\">\n";
				echo "<TR class=\"even\"><TD><b><small>"._REG.":</small></b></TD><TD><small>$wregion</small></TD></TR><TR class=\"even\"><TD>\n";
				echo "<b><small>"._WCNTRY.":</small></b></TD><TD><small>$wcountry</small></TD></TR><TR class=\"even\"><TD>\n";
				echo "<b><small>"._WSUBDIV.":</small></b></TD><TD><small>$wsubdiv</small></TD></TR class=\"even\"><TR class=\"even\"><TD>\n";
				echo "<b><small>"._WCITY.":</small></b></TD><TD><small>$wcity ($saved_accid)</small></TD></TR><TR class=\"even\"><TD>\n";
				echo "<b><small>"._WSTATION.":</small></b></TD><TD><small>$saved_wcid</small></TD></TR><TR class=\"even\"><TD>\n";
				if ($saved_tpc == 0) {
					echo "<b><small>"._WTEMP.":</small></b></TD><TD><small>&deg;C </small></TD></TR><TR class=\"even\"><TD>\n";
				} else {
					echo "<b><small>"._WTEMP.":</small></b></TD><TD><small>&deg;F </small></TD></TR><TR class=\"even\"><TD>\n";
				}
				if ($saved_tps == 0) {
					echo "<b><small>"._WINDSPD.":</small></b></TD><TD><small>km/h </small></TD></TR></TABLE>\n";
				} else {
					echo "<b><small>"._WINDSPD.":</small></b></TD><TD><small>mph </small></TD></TR></TABLE>\n";
				}
				echo "<br><br>\n";
				if ($utype=="Admin") {
					echo "<li type=\"square\"> <a href=\"index.php?func=usedefault&flushcache=1\">"._TOVIEW."</a> <br><br>\n";
				} else {
					echo "<li type=\"square\"> <a href=\"index.php?func=user&flushcache=1\">"._TOVIEW."</a> <br><br>\n";
				}
				echo "<li type=\"square\"> <a href=\"change.php\">"._TOGOBACK."</a><br><br>\n";
			}
		}
	}
}

function checkadmin() {
	global $userid, $username, $admin, $xoopsUser, $pnwusermod, $xoopsDB;

	if ( $xoopsUser ) {
		if ( $xoopsUser->isAdmin() ) {
			echo "<h4>$module_name "._WADMINMENU."</h4>";
			echo "<li type=\"square\"><font face=\"Arial\" color=\"red\" size=\"\">"._NOTESTATION."</font><br><br>";
			echo "<li type=\"square\"> "._WANT2CHNG.":<br><br>\n";
			echo "<a href=\"change.php?weatherop=AdminRegion\"><b>"._WDEFAULTSET."<br><br>"._OR."<br><br>\n";
			echo "<a href=\"change.php?weatherop=UserRegion\"><b>"._WYOURSET."<br><br>\n";
		} elseif($pnwusermod == 1) {
			SelRegion("User", $userid);
		} else {
			SelRegion("Anon");
		}
	}
	if ( !$xoopsUser ) {
		SelRegion("Anon");
	}
}

if ($xoopsUser) {
	$username = $xoopsUser->uname();
	list($userid) = $xoopsDB->fetchRow($xoopsDB->query("select uid from ".$xoopsDB->prefix("users")." where uname='$username'"));
}

$xoopsOption['show_rblock'] =0;
include(XOOPS_ROOT_PATH."/header.php");

OpenTable();
switch($weatherop) {
	case "AdminRegion":
		SelRegion("Admin", 0);
		break;
	case "UserRegion":
		SelRegion("User", $userid);
		break;
	case "AdminCountry":
		SelCountry("Admin", $Selection, 0);
		break;
	case "UserCountry":
		SelCountry("User", $Selection, $userid);
		break;
	case "AdminSubDiv":
		SelSubDiv("Admin", $Selection, 0);
		break;
	case "UserSubDiv":
		SelSubDiv("User", $Selection, $userid);
		break;
	case "AdminCity":
		SelCity("Admin", $Selection, 0);
		break;
	case "UserCity":
		SelCity("User", $Selection, $userid);
		break;
	case "AdminStation":
		SelStation("Admin", $Selection, 0);
		break;
	case "UserStation":
		SelStation("User", $Selection, $userid);
		break;
	case "AdminPreSave":
		PreSave("Admin", $accid, $Selection, $stype, $userid);
		break;
	case "UserPreSave":
		PreSave("User", $accid, $Selection, $stype, $userid);
		break;
	case "AdminSave":
		SaveSet("Admin", $newtpc, $newtps, $accid, $wcid, $wstype, $wstation, 0);
		break;
	case "UserSave":
		SaveSet("User", $newtpc, $newtps, $accid, $wcid, $wstype, $wstation, $userid);
		break;
	default:
		checkadmin();
		break;
}
CloseTable();
include(XOOPS_ROOT_PATH."/footer.php");
?>
