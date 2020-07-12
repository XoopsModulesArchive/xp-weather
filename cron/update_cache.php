#!/usr/bin/php -q
<?php
/*******************************************************************************
 *                                                                             *
 *                                                                             *
 *                                                                             *
 *                                                                             *
 *                                                                             *
 *                                                                             *
 *                                                                             *
 ******************************************************************************/
/**
 *  Update XP-Weather Cache
*/
	include "header.php"

	$cache_file = "".XOOPS_ROOT_PATH."/modules/".xoopsModule->dirname()."/cache/main_$language.$accid";
	$fa = fsockopen("www.msnbc.com", 80, $num_error, $str_error, 30);
	if(!$fa) {
		$failuremessage = "<h4><b>$module_name</b></h4>\n"._UNAVAIL.": $str_error ($num_error)\n";
	} else {
		fputs($fa,"GET /m/chnk/d/weather_d_src.asp?acid=$accid HTTP/1.0\n\n");
		$answer=fgets($fa,128);
		$v_City    = "";
		$v_SubDiv  = "";
		$v_Country = "";
		$v_Region  = "";
		$v_Temp    = "";
		$v_CIcon   = "";
		$v_WindS   = "";
		$v_WindD   = "";
		$v_Baro    = "";
		$v_Humid   = "";
		$v_Real    = "";
		$v_UV      = "";
		$v_Vis     = "";
		$v_LastUp  = "";
		$v_Fore    = "";
		$v_Acid    = "";
		while (!feof($fa)) {
			$grabline = fgets($fa, 4096);
			$grabline= trim($grabline) . "\n";
			if (substr($grabline,7,4) == "City")    { $v_City    = substr($grabline,15,20); }
			if (substr($grabline,7,6) == "SubDiv")  { $v_SubDiv  = substr($grabline,17,20); }
			if (substr($grabline,7,7) == "Country") { $v_Country = substr($grabline,18,20); }
			if (substr($grabline,7,6) == "Region")  { $v_Region  = substr($grabline,17,20); }
			if (substr($grabline,7,5) == "Temp ")   { $v_Temp    = substr($grabline,15,20); }
			if (substr($grabline,7,5) == "CIcon")   { $v_CIcon   = substr($grabline,16,20); }
			if (substr($grabline,7,5) == "WindS")   { $v_WindS   = substr($grabline,16,20); }
			if (substr($grabline,7,5) == "WindD")   { $v_WindD   = substr($grabline,16,20); }
			if (substr($grabline,7,4) == "Baro")    { $v_Baro    = substr($grabline,15,20); }
			if (substr($grabline,7,5) == "Humid")   { $v_Humid   = substr($grabline,16,20); }
			if (substr($grabline,7,4) == "Real")    { $v_Real    = substr($grabline,15,20); }
			if (substr($grabline,7,2) == "UV")      { $v_UV      = substr($grabline,13,20); }
			if (substr($grabline,7,3) == "Vis")     { $v_Vis     = substr($grabline,14,20); }
			if (substr($grabline,7,5) == "LastUp")  { $v_LastUp  = substr($grabline,16,20); }
			if (substr($grabline,7,7) == "ConText") { $v_ConText = substr($grabline,18,20); }
			if (substr($grabline,7,4) == "Fore")    { $v_Fore    = substr($grabline,15,200); }
			if (substr($grabline,7,4) == "Acid")    { $v_Acid    = substr($grabline,15,20); }
		}
		fclose($fa);
		$v_City    = substr($v_City,0,strlen($v_City)-3);
		$v_SubDiv  = substr($v_SubDiv,0,strlen($v_SubDiv)-3);
		$v_Country = substr($v_Country,0,strlen($v_Country)-3);
		$v_Region  = substr($v_Region,0,strlen($v_Region)-3);
		$v_Temp    = substr($v_Temp,0,strlen($v_Temp)-3);
		$v_CIcon   = substr($v_CIcon,0,strlen($v_CIcon)-3);
		$v_WindS   = substr($v_WindS,0,strlen($v_WindS)-3);
		$v_WindD   = substr($v_WindD,0,strlen($v_WindD)-3);
		$v_Baro    = substr($v_Baro,0,strlen($v_Baro)-3);
		$v_Humid   = substr($v_Humid,0,strlen($v_Humid)-3);
		$v_Real    = substr($v_Real,0,strlen($v_Real)-3);
		$v_UV      = substr($v_UV,0,strlen($v_UV)-3);
		$v_Vis     = substr($v_Vis,0,strlen($v_Vis)-3);
		$v_LastUp  = substr($v_LastUp,0,strlen($v_LastUp)-3);
		$v_ConText = substr($v_ConText,0,strlen($v_ConText)-3);
		$v_Fore    = substr($v_Fore,0,strlen($v_Fore)-3);
		$v_Acid    = substr($v_Acid,0,strlen($v_Acid)-3);
		$v_Fore = explode("|", $v_Fore);
		$fpwrite = fopen($cache_file, 'w');
		if(!$fpwrite) {
			$failuremessage = _WRITEFAIL;
		} else {
			$outbuf = "<h4><b>"._WDETAILED."</b></h4>\n"
			._REG.": $v_Region<br>\n"
			."<b>$v_City, $v_SubDiv, $v_Country</b> "
			."[<a href=\"change.php\"><small>"._CHNGSET."</small></a>]\n"
			."<hr><table cellpadding=\"4\" cellspacing=\"0\" border=\"0\"><tr><td valign=\"top\">\n"
			."<img src=\"images/$gifdir/current_cond.gif\" align=\"top\" alt=\""._CURCOND."\">\n"
			."<font face=\"Arial\" color=\"red\" size=\"\">".ConvTemp($v_Temp, $tpc)."</font> \n"
			."<img src=\"images/$gifdir/".$v_CIcon.".gif\" align=\"center\"><br>\n"
			."</td><td><table cellpadding=\"4\" cellspacing=\"0\" border=\"0\"><tr><td>\n"
			."<font face=\"Arial\" color=\"#000000\" size=\"1\">"._WIND.": ".$v_WindD." ".ConvSpeed($v_WindS,$tps)."</font></td><td>\n"
			."<font face=\"Arial\" color=\"#000000\" size=\"1\">"._BARO.": ".$v_Baro." </font></td><td>\n"
			."<font face=\"Arial\" color=\"#000000\" size=\"1\">"._HUMID.": ".$v_Humid."%</font></td></tr><tr><td>".$v_ConText."</td><td>\n"
			."<font face=\"Arial\" color=\"#000000\" size=\"1\">"._BARO_PRES.": ".ConvPress($v_Baro,$tpc)." </font></td></tr><tr><td>\n"
			."<font face=\"Arial\" color=\"#000000\" size=\"1\">"._UV.": ".$v_UV."</font></td><td>\n"
			."<font face=\"Arial\" color=\"#000000\" size=\"1\">"._REFE.": ".ConvTemp($v_Real,$tpc)."</font></td><td>\n"
			."<font face=\"Arial\" color=\"#000000\" size=\"1\">"._VIS.": ".ConvLength($v_Vis,$tps)."</font></td></tr>\n"
			."</table></td></tr></table><br><table cellpadding=\"4\" cellspacing=\"0\" border=\"0\">\n"
			."<tr><td valign=\"top\" align=\"center\"><img src=\"images/$gifdir/forecast.gif\" alt=\""._FOREC."\"></td><td>&nbsp;</td><td align=\"center\">\n"
			.Fore($v_Fore[0])."<br><img src=\"images/$gifdir/".$v_Fore[10].".gif\"></td><td align=\"center\">\n"
			.Fore($v_Fore[1])."<br><img src=\"images/$gifdir/".$v_Fore[11].".gif\"></td><td align=\"center\">\n"
			.Fore($v_Fore[2])."<br><img src=\"images/$gifdir/".$v_Fore[12].".gif\"></td><td align=\"center\">\n"
			.Fore($v_Fore[3])."<br><img src=\"images/$gifdir/".$v_Fore[13].".gif\"></td><td align=\"center\">\n"
			.Fore($v_Fore[4])."<br><img src=\"images/$gifdir/".$v_Fore[14].".gif\"></td></tr>\n"
			."<tr><td>&nbsp;</td><td colspan=\"6\"><hr></td></tr>\n"
			."<tr><td>&nbsp;</td><td><font face=\"Arial\" color=\"red\" size=\"\">"._WHIGH.":</font></td><td align=\"center\">\n"
			."<font face=\"Arial\" color=\"red\" size=\"\">".ConvTemp($v_Fore[15],$tpc)."</font></td><td align=\"center\">\n"
			."<font face=\"Arial\" color=\"red\" size=\"\">".ConvTemp($v_Fore[16],$tpc)."</font></td><td align=\"center\">\n"
			."<font face=\"Arial\" color=\"red\" size=\"\">".ConvTemp($v_Fore[17],$tpc)."</font></td><td align=\"center\">\n"
			."<font face=\"Arial\" color=\"red\" size=\"\">".ConvTemp($v_Fore[18],$tpc)."</font></td><td align=\"center\">\n"
			."<font face=\"Arial\" color=\"red\" size=\"\">".ConvTemp($v_Fore[19],$tpc)."</font></td></tr>\n"
			."<tr><td>&nbsp;</td><td colspan=\"6\"><hr></td></tr>\n"
			."<tr><td>&nbsp;</td><td><font face=\"Arial\" color=\"#000000\" size=\"\">"._WLOW.":</td><td align=\"center\">\n"
			."<font face=\"Arial\" color=\"#000000\" size=\"\">".ConvTemp($v_Fore[25],$tpc)."</font></td><td align=\"center\">\n"
			."<font face=\"Arial\" color=\"#000000\" size=\"\">".ConvTemp($v_Fore[26],$tpc)."</font></td><td align=\"center\">\n"
			."<font face=\"Arial\" color=\"#000000\" size=\"\">".ConvTemp($v_Fore[27],$tpc)."</font></td><td align=\"center\">\n"
			."<font face=\"Arial\" color=\"#000000\" size=\"\">".ConvTemp($v_Fore[28],$tpc)."</font></td><td align=\"center\">\n"
			."<font face=\"Arial\" color=\"#000000\" size=\"\">".ConvTemp($v_Fore[29],$tpc)."</font>\n"
			."</td></tr></table><hr><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td align=\"center\">\n"
			."<TABLE cellspacing=\"1\" cellpadding=\"1\"  class=\"head\"><TR class=\"even\"><TD>\n"
			."<B><small>"._CURSETT.":</small></B> \n"
			."<TD><a href=\"change.php\"><small>"._CHNGSET."</small></a>\n"
			."</TD><TR class=\"even\"><TD><b><small>"._REG.":</small></b></TD><TD><small>$v_Region</small></TD></TR><TR class=\"even\"><TD>\n"
			."<b><small>"._WCNTRY.":</small></b></TD><TD><small>$v_Country</small></TD></TR><TR class=\"even\"><TD>\n"
			."<b><small>"._WSUBDIV.":</small></b></TD><TD><small>$v_SubDiv</small></TD></TR><TR class=\"even\"><TD>\n"
			."<b><small>"._WCITY.":</small></b></TD><TD><small>$v_City ($accid)</small></TD></TR><TR class=\"even\"><TD>\n";
			$outbuf .= "<b><small>"._WTEMP.":</small></b></TD><TD><small>".ConvTemp($v_Temp,$tpc)."</small></TD></TR><TR class=\"even\"><TD>\n";
			$outbuf .= "<b><small>"._WINDSPD.":</small></b></TD><TD><small>".ConvSpeed($v_WindS,$tps)."</small></TD></TR></TABLE>\n";
			$outbuf .= "</TD>\n"
			."<TD WIDTH=72 VALIGN=top><center><b>"._RADAR."</b><a href=\"http://msnbc.accuweather.com/msnbc/msnbc_qx01/$accid/radar/?accuid=$accid\" target=\"_blank\"><img src=\"images/radar.gif\" width=62 height=64 ALT="._RADAR."></a></center></TD>\n"
			."<TD WIDTH=72 VALIGN=top><center><b>"._SATELL."</b><a href=\"http://msnbc.accuweather.com/msnbc/msnbc_qx01/$accid/sat/?accuid=$accid\" target=\"_blank\"><img src=\"images/satellite.gif\" width=62 height=64 ALT="._SATELL."></a></center></TD>\n"
			."<TD WIDTH=72 VALIGN=top><center><b>"._PRECIP."</b><a href=\"http://msnbc.accuweather.com/msnbc/msnbc_qx01/$accid/precip/?accuid=$accid\" target=\"_blank\"><img src=\"images/precipitation.gif\" width=62 height=64 ALT="._PRECIP."></a></center></TD>\n"
			."</TR></TABLE>\n";
			fputs($fpwrite, $outbuf);
//			$outbuf .= "Cache-Miss";
		}
		fclose($fpwrite);
	}
	include "footer.php";
?>
