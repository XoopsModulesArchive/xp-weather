<?php
/***********************************************************************************/
/* XP-Weather version 1.72                                                         */
/* 23/01/2004 - sylvainb                                                           */
/*            New adaptation from the 1.7 exoops module to xoops 2.0.5.2           */
/*            No credits to me concerning the original code                        */
/* XP-Weather version 1.3                                                          */
/*    davidd - added proxy_* variables to configure snoopy proxy server support    */
/*                                                                                 */
/* XP-Weather version 1.1                                                          */
/*    removed $pnwconfig variable it broke the block rendering (don't know why)    */
/*                                                                                 */
/* XP-Weather version 1.0                                                          */
/* XP-Weather version 0.98d                                                        */
/*         Modified again by davidd                                                */
/*                                                                                 */
/*                                                                                 */
/* 6/18/2002 - davidd                                                              */
/*         Added adjustable persistent cache to block and main module              */
/*         Code Cleanup                                                            */
/*                                                                                 */
/* 6/13/2002 - davidd                                                              */
/*         added conText weather condition text                                    */
/*         re-worked table output                                                  */
/*         added radar and percipitation map links                                 */
/*         fixed header and footer includes for template main/cblock               */
/*         moved embeded French out to language/main.php file                      */
/*                                                                                 */
/***********************************************************************************/
/*                                                                                 */
/* PNWeather version 0.71b                                                         */
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

/*********************************************************************************************************/
/* General Module Configuration                                                                          */
/*                                                                                                       */
/* $bgifdir:        Directory name /modules/$module_name/images/ which contains weather images for block */
/* $gifdir:         Directory under /modules/$module_name/images/ which contains weather images          */
/* $cache_time:     Cache Time                                                                           */
/*                  This allows seamless compatibility with both PHP-Nuke 5.2 & 5.3                      */
/* $pnwusermod:     1 -- Allow Users to Change Settings, 0 -- Disallow Settings Changes                  */
/*                                                                                                       */
/*                                                                                                       */
/*********************************************************************************************************/
$weather_url = "http://www.msnbc.com/m/chnk/d/weather_d_src.asp";
$wcdata_url="http://desktop3.weather.com/search/search?what=weather";
$response_maxlength=4224;
$proxy_host="";
$proxy_port="";
$proxy_user="";
$proxy_pwd="";
$bgifdir = $xoopsConfig['language'];
$gifdir = $xoopsConfig['language'];
$cache_time=3600;
$pnwusermod=1;
$debugurl=0;
$debugcache=0;
?>
