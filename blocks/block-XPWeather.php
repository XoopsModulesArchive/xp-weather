<?php
/***********************************************************************************/
/* XP-Weather version 1.72                                                         */
/* 23/01/2004 - sylvainb                                                           */
/*            New adaptation from the 1.7 exoops module to xoops 2.0.5.2           */
/*            No credits to me concerning the original code                        */
/* XP-Weather version 1.5                                                          */
/*                                                                                 */
/* 1/4/2002 - davidd                                                               */
/*         module directory name is now an option                                  */
/*         added option to remove change weather link from block                   */
/* XP-Weather version 1.4                                                          */
/* 10/24/202 - davidd                                                              */
/*         added edit_module function for block settings                           */
/* XP-Weather version 1.3                                                          */
/* 8/4/2002 - davidd                                                               */
/*         added unlimited to visibility                                           */
/*         added new unavailable status                                            */
/* XP-Weather version 1.1                                                          */
/* XP-Weather version 1.0                                                          */
/* XP-Weather version 0.99                                                         */
/*         Modified again by davidd                                                */
/*                                                                                 */
/*                                                                                 */
/* 6/18/2002 - davidd                                                              */
/*         Includes cleanup                                                        */
/*         Added adjustable caching to this module                                 */
/*                                                                                 */
/* 6/13/2002 -                                                                     */
/*         added conText weather condition text                                    */
/*         re-worked table output                                                  */
/*         moved embeded French out to language/main.php file                      */
/*                                                                                 */
/***********************************************************************************/
/*                                                                                 */
/* XP-Weather version 0.71b                                                        */
/*         Converted by Bidou (bidou@lespace.org                                   */
/*         http://www.lespace.org                                                  */
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

function disp_block_XPWeather($options) {
	global $xoopsModule, $xoopsUser, $xoopsConfig, $xoopsDB;

	include_once(XOOPS_ROOT_PATH."/modules/".$options[6]."/header.php");

	$cache_time = $options[1];
	$language = $xoopsConfig['language'];
	$moduledir = $options[6];

	$block = array();
	$block['title'] = _BLOCKTITLE;
	$block['content'] = "";
	$mywd = new WeatherData;
	unset($accid);

	if ( ($xoopsUser && $options[0] == 1) || ($xoopsUser && $xoopsUser->isAdmin()) ) {
		$username = $xoopsUser->uname();
		list($userid) = $xoopsDB->fetchRow($xoopsDB->query("select uid from ".$xoopsDB->prefix("users")." where uname= '$username'"));
		list($accid, $wcid, $statype, $station_name, $tpc, $tps) = $xoopsDB->fetchRow($xoopsDB->query("select accid, wcid, station_type, station_name, tpc, tps from ".$xoopsDB->prefix("userweather")." where userid='$userid'"));
	} else {
		list($accid, $wcid, $statype, $station_name, $tpc, $tps) = $xoopsDB->fetchRow($xoopsDB->query("select accid, wcid, station_type, station_name, tpc, tps from ".$xoopsDB->prefix("userweather")." where userid='0'"));
	}
	if (!isset($accid)) {
		list($accid, $wcid, $statype, $station_name, $tpc, $tps) = $xoopsDB->fetchRow($xoopsDB->query("select accid, wcid, station_type, station_name, tpc, tps from ".$xoopsDB->prefix("userweather")." where userid='0'"));
	}
	$cache_file = "".XOOPS_ROOT_PATH."/modules/$moduledir/cache/block_$language.$accid";
	$main_cache_file = "".XOOPS_ROOT_PATH."/modules/$moduledir/cache/main_$language.$accid";
	$time = split(" ", microtime());
	srand((double)microtime()*1000000);
	$cache_time_rnd = 300 - rand(0, 600);
	$cachemtime = file_exists($cache_file) ? filemtime($cache_file) : 0;

	// $options[2] is the timestamp of the last configuration change, $options[5] is disable caching
	if ( file_exists($cache_file) && ($cachemtime < $options[2] || $options[5] == 1) ) {
		unlink($cache_file);
		if (file_exists($main_cache_file)) { unlink($main_cache_file); }
	}
	if ( (!(file_exists($cache_file))) || ((($cachemtime + $cache_time - $time[1]) + $cache_time_rnd < 0) || (!(filesize($cache_file))) && $cache_time != -1) || $options[5] == 1 ) {
		if ( IsSet($wcid) && ($statype == "TWC" || $statype == "WMO") ) {
			$weather_url .="?acid=$wcid";
		} else {
			$weather_url .= "?acid=$accid";
		}
		$mywd->maxlength = $response_maxlength;

		if ( !empty($proxy_host) ) {
			$mywd->proxyHost = $proxy_host;
			if ( !empty($proxy_port) ) { $mywd->proxyPort = $proxy_port; }
			if ( !empty($proxy_user) ) { $mywd->User = $proxy_user; }
			if ( !empty($proxy_pwd) ) { $mywd->Pass = $proxy_pwd; }
		}

		$mywd->fetchData($weather_url);
		if ( IsSet($mywd->feedError) && !empty($mywd->feedError) ) {
			$failuremessage = _UNAVAIL.": $mywd->feedResponse ($mywd->feedError)\n";
			if($debugurl == 1) {
				$failuremessage .= "url: $weather_url\n";
			}
		} else {
			$mywd->processData();
			$v_City    = $mywd->v_City;
			$v_SubDiv  = $mywd->v_SubDiv;
			$v_Country = $mywd->v_Country;
			$v_Region  = $mywd->v_Region;
			$v_Temp    = $mywd->v_Temp;
			$v_CIcon   = $mywd->getIcon($mywd->v_CIcon);
			$v_WindS   = $mywd->v_WindS;
			$v_WindD   = $mywd->v_WindD;
			$v_Baro    = $mywd->v_Baro;
			$v_Humid   = $mywd->v_Humid;
			$v_Real    = $mywd->v_Real;
			$v_UV      = $mywd->v_UV;
			$v_Vis     = $mywd->getVisibility();
			$v_LastUp  = $mywd->formatLastUpdate();
//			$v_LastUp  = formatTimestamp($cachemtime);
			$v_ConText = $mywd->v_ConText;
			$v_Fore    = explode("|", $mywd->v_Fore);
			$v_Acid    = $mywd->v_Acid;

			if ( ($v_Temp == "" && $v_CIcon == "") && ( is_object($xoopsUser ) && ( $options[0] == 1 || $xoopsUser->isAdmin() ))) {
				$failuremessage = "<center><a href=\"".XOOPS_URL."/modules/$moduledir/change.php\">";
				$failuremessage .= "<small>"._CHNGSET."</small></A></center><br />\n";
				if ( $v_City != "" ) {
					$failuremessage .= "<center><b>$v_City</b></center>\n<center>"._UNAVAIL."</center>\n";
				} else {
					$failuremessage .= "<center><b>$v_City</b></center>\n<center>"._UNAVAIL."</center> \n<center>"._NODATA."</center>\n";
				}
			} else {
				if ($v_Temp == "" || $v_Temp == "0") {
					$v_Temp = ConvTemp($v_Fore[20],$tpc);
					$v_CIcon = $mywd->getIcon($v_Fore[10]);
					$v_WindS = "n/a";
					$v_WindD = "n/a";
					$v_Baro = "n/a";
					$v_Humid = "n/a";
					$v_Real = $v_Temp;
					$v_UV = "n/a";
					$v_Vis = "n/a";
				}
				$fpwrite = fopen($cache_file, 'w');
				if(!$fpwrite) {
					$failuremessage = _WRITEFAIL." ".$cache_file;
				} else {
					if ( $v_SubDiv != "" ) {
						$v_SubDiv = ", ".$v_SubDiv;
					}
					// show region/subdiv with city name in block
					if ($options[3] == 1) {
						$block['content'] .= "<center><b>$station_name</b></center>\n";
					} else {
						$block['content'] .= "<center><b>$v_City</b></center>\n";
					}
					// users can change weather settings from block
					if ( ( $options[7] == 1 && $options[0] == 1 ) ) {
						$block['content'] .= "<center><a href=\"".XOOPS_URL."/modules/$moduledir/change.php\">";
						$block['content'] .= "<small>"._CHNGSET."</small></A></center>\n";
					}
					$block['content'] .= "<center><table><tr><td align=\"center\">\n";
					$block['content'] .="<small><b>"._CURCOND."</b></small>\n";
					$block['content'] .="<table cellspacing=\"2\" cellpadding=\"2\">\n";
					$block['content'] .="<tr><td align=\"center\" valign=\"top\"><small>" .$v_ConText. "</small>\n";
					$block['content'] .="<br/><br/><small>" ._WIND. ":</small>\n";
					$block['content'] .="</td><td align=\"center\" valign=\"top\"><a href=\"".XOOPS_URL."/modules/$moduledir/index.php\"><img src=\"".XOOPS_URL."/modules/$moduledir/images/$bgifdir/".$v_CIcon."\" border=\"0\"></a>\n";
					$block['content'] .="</td><td align=\"center\" valign=\"top\"><small> " . ConvTemp($v_Temp, $tpc) ."</small>\n";
//					if ( strlen($v_ConText) > 6 ) {
//						$block['content'] .= "<br/>";
//					}
					$block['content'] .= "<br/><br/><small> $v_WindD </small>";
					if ($v_WindS > 0) {
						$block['content'] .= "<small>" . ConvSpeed($v_WindS, $tps) . "</small>";
					}
					$block['content'] .="\n</td></tr><tr><td align=\"center\" colspan=\"3\">\n";
					$block['content'] .="<small>" ._REFE. " " . ConvReal($v_Real, $tpc) . "</small>\n";
					$block['content'] .="</td></tr></table>\n";
					$block['content'] .="</td></tr><tr>\n";
					$block['content'] .="<td align=\"center\"><a href=\"".XOOPS_URL."/modules/$moduledir/index.php\">" ._WDETAILED. "...</a>\n";
					$block['content'] .="</td></tr><tr><td align=\"center\"><small>" ._LASTUP. " " .$v_LastUp. "</small>\n";
					$block['content'] .="</td></tr></table>\n";
					$block['content'] .="</center>\n";
					fputs($fpwrite, $block['content']);
					// show block cache debug hit/miss message
					if ($options[4] == 1) {
						$block['content'] .="<small>Cache-Miss tpc = $tpc tps = $tps</small>";
					}
				}
				fclose($fpwrite);
			}
		}
	} else {
		if (file_exists($cache_file)) {
			$wfread = fopen($cache_file, 'r');
			if(!$wfread) {
				$failuremessage = _READFAIL ." ". $cache_file;
			} else {
				$block['content'] .= fread($wfread,filesize($cache_file));
				fclose($wfread);
				// show block cache debug hit/miss message
				if ($options[4] == 1) {
					$block['content'] .= "<small>Cache-Hit tpc = $tpc tps = $tps</small>";
				}
			}
		}
	}
	if (isset($failuremessage)) {
		$block['content'] = $failuremessage;
	}
	return $block;
}

function edit_block_xpweather($options) {
	global $xoopsUser;

	if ( is_object($xoopsUser) && $xoopsUser->isAdmin() ) {
		$tabletag1='<tr><td>';
		$tabletag2='</td><td>';
		$time = split(" ", microtime());
		$time = $time[1];

		$form = "<table border='0'>";
		$form .= $tabletag1._USERS_CAN_SET_WEATHER.$tabletag2;
		$form .= mk_xpwchkbox($options,0);
		$form .= $tabletag1._MAX_CACHE_TIME.$tabletag2;
		$form .= "<input type='text' name='options[1]' value='".$options[1]."' size='4'>"._SECONDS."</td></tr>";
		$form .= $tabletag1.$tabletag2."<input type='hidden' name='options[2]' value='".$time."' size='12'></td></tr>";
		$form .= $tabletag1._SHOW_REGION_IN_BLOCK.$tabletag2;
		$form .= mk_xpwchkbox($options,3);
		$form .= $tabletag1._SHOW_CACHE_DEBUG_MESSAGE.$tabletag2;
		$form .= mk_xpwchkbox($options,4);
		$form .= $tabletag1._DISABLE_CACHING.$tabletag2;
		$form .= mk_xpwchkbox($options,5);
		$form .= $tabletag1._MODULE_DIR_NAME.$tabletag2;
		$form .= "<input type='hidden' name='options[6]' value='".$options[6]."'>".$options[6]."</td></tr>";
		$form .= $tabletag1._SHOW_EDIT_WEATHER_LINK.$tabletag2;
		$form .= mk_xpwchkbox($options,7);
		$form .= "</table>";
		return $form;
	}
}

function mk_xpwchkbox($options, $number) {
	$chk   = "";
	if ($options[$number] == 0) {
		$chk = " checked='checked'";
	}
	$chkbox= "<input type='radio' name='options[$number]' value='0'".$chk." />"._NO."";
	$chk   = "";
	if ($options[$number] == 1) {
		$chk = " checked='checked'";
	}
	$chkbox .= "<input type='radio' name='options[$number]' value='1'".$chk." />"._YES."</td></tr>";
	RETURN $chkbox;
}
?>

