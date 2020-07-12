<?php
// ------------------------------------------------------------------------- //
//                XOOPS - PHP Content Management System                      //
//                       <http://www.xoops.org/>                             //
// ------------------------------------------------------------------------- //
// Based on:								     //
// myPHPNUKE Web Portal System - http://myphpnuke.com/	  		     //
// PHP-NUKE Web Portal System - http://phpnuke.org/	  		     //
// Thatware - http://thatware.org/					     //
// ------------------------------------------------------------------------- //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
// ------------------------------------------------------------------------- //
include_once(XOOPS_ROOT_PATH."/modules/xp-weather/cache/config.php");
include_once(XOOPS_ROOT_PATH."/modules/xp-weather/include/functions.php");
include_once(XOOPS_ROOT_PATH."/modules/xp-weather/class/weather.class.php");

// Include the appropriate language file.
if(file_exists(XOOPS_ROOT_PATH.'/modules/xp-weather/language/'.$xoopsConfig['language'].'/main.php')) {
	include_once(XOOPS_ROOT_PATH.'/modules/xp-weather/language/'.$xoopsConfig['language'].'/main.php');
	if (!file_exists(XOOPS_ROOT_PATH.'/modules/xp-weather/images/'.$xoopsConfig['language'])) {
		$bgifdir = "english";
		$gifdir = "english";
	}
} else {
	$bgifdir = "english";
	$gifdir = "english";
	include_once(XOOPS_ROOT_PATH.'/modules/xp-weather/language/english/main.php');
}
?>
