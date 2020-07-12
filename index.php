<?php
/***********************************************************************************/
/* XP-Weather version 1.72                                                         */
/* 23/01/2004 - sylvainb                                                           */
/*            New adaptation from the 1.7 exoops module to xoops 2.0.5.2           */
/*            No credits to me concerning the original code                        */
/*                                                                                 */
/* XP-Weather version 1.5                                                          */
/*                                                                                 */
/* 12/28/2002 - davidd                                                             */
/* - added cache debug                                                             */
/* - removed module_name for xoopsModule->dirname()                                */
/*                                                                                 */
/* XP-Weather version 1.3                                                          */
/*                                                                                 */
/* 8/4/2002 - davidd                                                               */
/* - moved weather data collection to Weather class for supporting new feeds       */
/* - added proxy server support using snoopy class                                 */
/* - added unlimited value to visibility                                           */
/* - fixed flushcache test                                                         */
/* - removed hard-coded font tags for better display with dark themes              */
/* - added new unavailable messages when data is not available                     */
/*                                                                                 */
/* XP-Weather version 1.2                                                          */
/*                                                                                 */
/* 7/31/2002 - davidd                                                              */
/* msnbc.com changed weather sources from Accuweather to weather.com               */
/* - fixed forcast parsing offsets were changed                                    */
/* - added humidity and precipitation to detailed forecast                         */
/*                                                                                 */
/*                                                                                 */
/* XP-Weather version 1.1                                                          */
/* XP-Weather version 1.0                                                          */
/* XP-Weather version 0.98d                                                        */
/*         Modified again by davidd                                                */
/*                                                                                 */
/*                                                                                 */
/* 6/18/2002 - davidd                                                              */
/* - Added adjustable persistent cache to block and main module                    */
/* - Code Cleanup                                                                  */
/*                                                                                 */
/* 6/13/2002 - davidd                                                              */
/* - added conText weather condition text                                          */
/* - re-worked table output                                                        */
/* - added radar and percipitation map links                                       */
/* - fixed header and footer includes for template main/cblock                     */
/* - moved embeded French out to language/main.php file                            */
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

include_once("../../mainfile.php");
include_once("header.php");

function WeatherIndex($usedefault=0, $flushcache=0) {
	global $xoopsConfig, $xoopsModule, $cache_time, $gifdir, $pnwusermod, $xoopsUser, $xoopsDB;
	global $weather_url, $response_maxlength, $proxy_host, $proxy_port, $proxy_user, $proxy_pwd, $debugcache;

	$language = $xoopsConfig['language'];
	$moduledir = $xoopsModule->dirname();
	$outbuf = "";
	$mywd = new WeatherData;

	if ($usedefault == 0 && $xoopsUser) {
		$username = $xoopsUser->uname();
		list($userid) = $xoopsDB->fetchRow($xoopsDB->query("select uid from ".$xoopsDB->prefix("users")." where uname= '$username'"));
		list($accid, $wcid, $statype, $tpc, $tps) = $xoopsDB->fetchRow($xoopsDB->query("select accid, wcid, station_type, tpc, tps from ".$xoopsDB->prefix("userweather")." where userid='$userid'"));
	} else {
		list($accid, $wcid, $statype, $tpc, $tps) = $xoopsDB->fetchRow($xoopsDB->query("select accid, wcid, station_type, tpc, tps from ".$xoopsDB->prefix("userweather")." where userid='0'"));
	}
	if (!IsSet($accid)) {
		list($accid, $wcid, $statype, $tpc, $tps) = $xoopsDB->fetchRow($xoopsDB->query("select accid, wcid, station_type, tpc, tps from ".$xoopsDB->prefix("userweather")." where userid='0'"));
	}
	if ( !IsSet($accid) ) {
		if ( $xoopsUser->isAdmin() ) {
			echo "<br>"._UNAVAIL.""._RAISON."<br><br>\n";
			echo ""._INSTALL." <a href=\"install.php\">"._ICI."</a>.";
		} else {
			echo "<br>"._UNAVAIL._ERRORTRYAGAIN;
		}
	} else {
		$cache_file = "".XOOPS_ROOT_PATH."/modules/".$xoopsModule->dirname()."/cache/main_$language.$accid";
		$block_cache_file = "".XOOPS_ROOT_PATH."/modules/".$xoopsModule->dirname()."/cache/block_$language.$accid";
		$time = split(" ", microtime());
		srand((double)microtime()*1000000);
		$cache_time_rnd = 300 - rand(0, 600);

		if ( $flushcache == 1 ) {
			if (file_exists($cache_file)) { unlink( $cache_file ); }
			if (file_exists($block_cache_file)) { unlink( $block_cache_file ); }
		}
		if ( !(file_exists($cache_file)) || (((filectime($cache_file) + $cache_time - $time[1]) + $cache_time_rnd < 0) || (!(filesize($cache_file))) && $cache_time != -1) ) {
			if ( IsSet($wcid) && ($statype == "TWC" || $statype == "WMO") ) {
				$saved_stationid = $wcid;
				$weather_url .="?acid=$wcid";
			} else {
				$saved_stationid = $accid;
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
			if ( IsSet($mywd->feedError) && !empty($mywd->feedResponse) && empty($mywd->results) ) {
				$failuremessage = "<h4><b>$moduledir</b></h4>\n"._UNAVAIL.": ".$mywd->feedResponse." (".$mywd->feedError.")\n"."url: ".$weather_url."\n";
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
//				$v_Vis     = $mywd->v_Vis;
				$v_Vis     = $mywd->getVisibility();
				$v_LastUp  = $mywd->v_LastUp;
				$v_ConText = $mywd->v_ConText;
				$v_Fore    = explode("|", $mywd->v_Fore);
				$v_Acid    = $mywd->v_Acid;

				if ($v_City != "" && ($v_Temp == "" && $v_CIcon == "")) {
					$failuremessage = "<h4><b>$moduledir</b></h4>\n"._UNAVAIL." City: $v_City, Temp: $v_Temp, Icon: $v_CIcon\n";
				} else if ($v_City == "") {
					$failuremessage = "<h4><b>$moduledir</b></h4>\n<center>"._UNAVAIL." </center>\n<center>"._NODATA."</center>\n";
				} else {
					if ( $v_SubDiv == "" ) {
						if ( $statype == "WMO" ) {
							list($wsubdiv) = $xoopsDB->fetchRow($xoopsDB->query("select subdiv.subdiv_title from ".$xoopsDB->prefix("subdiv")." subdiv inner join ".$xoopsDB->prefix("wmo_stations")." wmo on subdiv.subdiv_id = wmo.subdiv_id and wmo.icao_code ='$accid'"));
							$mywd->setSubDiv($wsubdiv);
						} else {
							list($wsubdiv) = $xoopsDB->fetchRow($xoopsDB->query("select subdiv.subdiv_title from ".$xoopsDB->prefix("subdiv")." subdiv inner join ".$xoopsDB->prefix("city")." city on subdiv.subdiv_id = city.subdiv_id and city.accid ='$accid'"));
							$mywd->setSubDiv($wsubdiv);
						}
					}
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
						$failuremessage = _WRITEFAIL ." ".$cache_file;
					} else {
						$outbuf .= "<h4><b>"._WDETAILED."</b></h4>\n"
						._REG.": $v_Region<br>\n"
						."<b>".$mywd->getLocation()."</b> ";
						if ( $pnwusermod == 1 || ( is_object($xoopsUser) && $xoopsUser->isAdmin() ) ) {
							$outbuf .= "[<a href=\"change.php\"><small>"._CHNGSET."</small></a>]\n";
						}
						$outbuf .= "<hr><table cellpadding=\"4\" cellspacing=\"0\" border=\"0\"><tr><td valign=\"top\">\n"
						."<img src=\"images/$gifdir/current_cond.gif\" align=\"top\" alt=\""._CURCOND."\">\n"
						."<font face=\"Arial\" color=\"red\" size=\"\">".ConvTemp($v_Temp, $tpc)."</font> \n"
						."<img src=\"images/$gifdir/".$v_CIcon."\" align=\"center\"><br>\n"
						."</td><td><table cellpadding=\"4\" cellspacing=\"0\" border=\"0\"><tr><td>\n"
						._WIND.": ".$v_WindD." ".ConvSpeed($v_WindS,$tps)."</td><td>\n"
						._BARO.": ".ConvPress($v_Baro,$tpc)." </td><td>\n"
						._HUMID.": ".$v_Humid."%</td></tr><tr><td>".$v_ConText."</td><td>\n"
						._BARO_PRES.": ".ConvPress($v_Baro,0)." </td></tr><tr><td>\n"
						._UV.": ".$v_UV."</td><td>\n"
						._REFE.": ".ConvTemp($v_Real,$tpc)."</td><td>\n"
						._VIS.": ".ConvLength($v_Vis,$tps)."</td></tr>\n"
						."</table></td></tr></table><br><table cellpadding=\"4\" cellspacing=\"0\" border=\"0\">\n"
						."<tr><td valign=\"top\" align=\"center\"><img src=\"images/$gifdir/forecast.gif\" alt=\""._FOREC."\"></td><td>&nbsp;</td><td align=\"center\">\n"
						.Fore($v_Fore[0])."<br><img src=\"images/$gifdir/".$mywd->getIcon($v_Fore[10])."\"></td><td align=\"center\">\n"
						.Fore($v_Fore[1])."<br><img src=\"images/$gifdir/".$mywd->getIcon($v_Fore[11])."\"></td><td align=\"center\">\n"
						.Fore($v_Fore[2])."<br><img src=\"images/$gifdir/".$mywd->getIcon($v_Fore[12])."\"></td><td align=\"center\">\n"
						.Fore($v_Fore[3])."<br><img src=\"images/$gifdir/".$mywd->getIcon($v_Fore[13])."\"></td><td align=\"center\">\n"
						.Fore($v_Fore[4])."<br><img src=\"images/$gifdir/".$mywd->getIcon($v_Fore[14])."\"></td></tr>\n"
						."<tr><td>&nbsp;</td><td colspan=\"6\"><hr></td></tr>\n"
						."<tr><td>&nbsp;</td><td><font face=\"Arial\" color=\"red\" size=\"\">"._WHIGH.":</font></td><td align=\"center\">\n"
						."<font face=\"Arial\" color=\"red\" size=\"\">".ConvTemp($v_Fore[20],$tpc)."</font></td><td align=\"center\">\n"
						."<font face=\"Arial\" color=\"red\" size=\"\">".ConvTemp($v_Fore[21],$tpc)."</font></td><td align=\"center\">\n"
						."<font face=\"Arial\" color=\"red\" size=\"\">".ConvTemp($v_Fore[22],$tpc)."</font></td><td align=\"center\">\n"
						."<font face=\"Arial\" color=\"red\" size=\"\">".ConvTemp($v_Fore[23],$tpc)."</font></td><td align=\"center\">\n"
						."<font face=\"Arial\" color=\"red\" size=\"\">".ConvTemp($v_Fore[24],$tpc)."</font></td></tr>\n"
						."<tr><td>&nbsp;</td><td colspan=\"6\"><hr></td></tr>\n"
						."<tr><td>&nbsp;</td><td>"._WLOW.":</td><td align=\"center\">\n"
						.ConvTemp($v_Fore[40],$tpc)."</td><td align=\"center\">\n"
						.ConvTemp($v_Fore[41],$tpc)."</td><td align=\"center\">\n"
						.ConvTemp($v_Fore[42],$tpc)."</td><td align=\"center\">\n"
						.ConvTemp($v_Fore[43],$tpc)."</td><td align=\"center\">\n"
						.ConvTemp($v_Fore[44],$tpc)."</td</tr>\n"
						."<tr><td>&nbsp;</td><td colspan=\"6\"><hr></td></tr>\n"
						."<tr><td>&nbsp;</td><td>"._PRECIP.":</td><td align=\"center\">\n"
						."$v_Fore[25]%</td><td align=\"center\">\n"
						."$v_Fore[26]%</td><td align=\"center\">\n"
						."$v_Fore[27]%</td><td align=\"center\">\n"
						."$v_Fore[28]%</td><td align=\"center\">\n"
						."$v_Fore[29]%\n"
						."</td></tr></table><hr><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td align=\"center\">\n";
						if (is_object($xoopsUser)) {
							$outbuf .= "<TABLE cellspacing=\"1\" cellpadding=\"1\"  class=\"head\"><TR class=\"even\"><TD>\n"
							."<B><small>"._CURSETT.":</small></B> \n"
							."<TD><a href=\"change.php\"><small>"._CHNGSET."</small></a>\n"
							."</TD><TR class=\"even\"><TD><b><small>"._REG.":</small></b></TD><TD><small>$v_Region</small></TD></TR><TR class=\"even\"><TD>\n"
							."<b><small>"._WCNTRY.":</small></b></TD><TD><small>$v_Country</small></TD></TR><TR class=\"even\"><TD>\n"
							."<b><small>"._WSUBDIV.":</small></b></TD><TD><small>".$mywd->getLocation()."</small></TD></TR><TR class=\"even\"><TD>\n"
							."<b><small>"._WCITY.":</small></b></TD><TD><small>$v_City ($saved_stationid)</small></TD></TR><TR class=\"even\"><TD>\n";
							$outbuf .= "<b><small>"._WTEMP.":</small></b></TD><TD><small>".ConvTemp($v_Temp,$tpc)."</small></TD></TR><TR class=\"even\"><TD>\n";
							$outbuf .= "<b><small>"._WINDSPD.":</small></b></TD><TD><small>".ConvSpeed($v_WindS,$tps)."</small></TD></TR></TABLE>\n";
						}
						$outbuf .= "</TD>\n"
						."<TD WIDTH=\"72\" VALIGN=top><center><b>"._RADAR."</b><a href=\"http://msnbc.accuweather.com/msnbc/msnbc_qx01/$accid/radar/?accuid=$accid\" target=\"_blank\"><img src=\"images/radar.gif\" width=\"62\" height=\"64\" ALT=\""._RADAR."\"></a></center></TD>\n"
						."<TD WIDTH=\"72\" VALIGN=top><center><b>"._SATELL."</b><a href=\"http://msnbc.accuweather.com/msnbc/msnbc_qx01/$accid/sat/?accuid=$accid\" target=\"_blank\"><img src=\"images/satellite.gif\" width=\"62\" height=\"64\" ALT=\""._SATELL."\"></a></center></TD>\n"
						."<TD WIDTH=\"72\" VALIGN=top><center><b>"._PRECIP."</b><a href=\"http://msnbc.accuweather.com/msnbc/msnbc_qx01/$accid/precip/?accuid=$accid\" target=\"_blank\"><img src=\"images/precipitation.gif\" width=\"62\" height=\"64\" ALT=\""._PRECIP."\"></a></center></TD>\n"
						."</TR></TABLE>\n";
						fputs($fpwrite, $outbuf);
						if ($debugcache == 1) {
							$outbuf .= "Cache-Miss";
						}
					}
					fclose($fpwrite);
				}
			}
		} else {
			if (file_exists($cache_file)) {
				$wfread	= fopen($cache_file, 'r');
				if(!$wfread) {
					$failuremessage = _READFAIL ." ". $cache_file;
				} else {
					$outbuf = fread($wfread,filesize($cache_file));
					fclose($wfread);
					if ($debugcache == 1) {
						$outbuf .= "<small>From Cache</small>";
					}
				}
			}
		}
		if (isset($failuremessage)) {
			$outbuf = $failuremessage;
			if ($pnwusermod == 1 || $xoopsUser->isAdmin()) {
				$outbuf .= "<br /><center>[<a href=\"change.php\"><small>"._CHNGSET."</small></a>]</center>\n";
			}
		}
	}
	echo $outbuf;
}
/*******************************************************************************\
| MAIN                                                                          |
| START                                                                         |
\*******************************************************************************/
if ( $xoopsConfig['startpage'] == $xoopsModule->dirname() ) {
	$xoopsOption['show_rblock'] =1;
	include(XOOPS_ROOT_PATH."/header.php");
	if ( empty($HTTP_GET_VARS['start']) ) {
		make_cblock();
		echo "<br />";
	}
} else {
	$xoopsOption['show_rblock'] =0;
	include(XOOPS_ROOT_PATH."/header.php");
}
$func = $HTTP_GET_VARS['func'];
$flushcache = $HTTP_GET_VARS['flushcache'];

OpenTable();
switch($func) {
	case "usedefault":
		WeatherIndex(1, $flushcache);
		break;
	default:
		WeatherIndex(0, $flushcache);
		break;
}
CloseTable();
include_once(XOOPS_ROOT_PATH."/footer.php");
?>
