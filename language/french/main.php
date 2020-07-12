<?php
/***********************************************************************************/
/*                                                                                 */
/* MyWeather version 1.0                                                           */
/*                                                                                 */
/* Richard Benfield aka PcProphet                                                  */
/* http://www.pc-prophet.com                                                       */
/* http://www.benfield.ws                                                          */
/*                                                                                 */
/* Chris Myden                                                                     */
/* http://www.chrismyden.com/                                                      */
/*                                                                                 */
/***********************************************************************************/
/*                                                                                 */
/* Latest Translation to "French" including City/Subdiv/Country sql data           */
/* Olivier Blazy                                                                   */
/* olivier.blazy@wanadoo.fr                                                        */
/* http://www.oblazy.com                                                           */
/*                                                                                 */
/* Old Translations:                                                               */
/* Translation to "French"                                                         */
/* Translated by "Jean-Francois Zahnen"                                            */
/* and by "Christophe Boulord"                                                     */
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

define("_UNLIMITED", "illimité");
define("_ALREADY", " dej&agrave;!");
define("_BARO", "Barom&egrave;tre");
define("_BARO_PRES", "Pression");
define("_BLOCKTITLE", "Infos m&eacute;t&eacute;o");
define("_CELSIUS", "Celsius");
define("_CHNGSET", "Changer les param&egrave;tres");
define("_CLICKSAVE", "Note : N'oubliez pas de cliquer 'Enregistrer changements' en-dessous.");
define("_CURCOND", "Conditions actuelles");
define("_CURSETT", "Param&egrave;tres actuels");
define("_EMPTY", "");
define("_ENSURELOGGEDON", "Assurez-vous d'&ecirc;tre loggu&eacute; avec les droits administrateur et r&eacute;essayer de nouveau!");
define("_ERRORCADMIN", "Impossible d'enregistrer les changements. Contactez l'administrateur du site.");
define("_ERRORTRYAGAIN", " Base de donn&eacute;es : Pas d'installation ou non disponible!<br><br>\n");
define("_FAHREN", "Fahrenheit");
define("_FOREC", "Pr&eacute;visions &agrave; cinq jours");
define("_HUMID", "Humidit&eacute;");
define("_IFYOU", "Afin de pouvoir personnaliser et sauver votre profil vous devez d'abord");
define("_IN", "en");
define("_KPH", "kilom&egrave;tres par heure");
define("_MPH", "miles par heure");
define("_NEWSET", "Param&egrave;tres actuels <b><font color=\"#FF0000\">non enregistr&eacute;s</font></b>");
define("_OR", "Ou");
define("_PLSCHNGLOC", "Ici, vous pouvez s&eacute;lectionner votre r&eacute;gion, pays, subdivision, et ville. <br><br> <li type=\"square\">  Vous pouvez &eacute;galement choisir vos unit&eacute;s de mesures.");
define("_PRECIP", "Pr&eacute;cipitations");
define("_RADAR", "Radar");
define("_REFE", "Sensation");
define("_REG", "R&eacute;gion");
define("_SAVECHGS", "Enregistrer changements");
define("_SAVEDSETR", "Nouveaux param&egrave;tres<b><font color=\"#FF0000\">enregistr&eacute;s</font></b>");
define("_SETTINGS", "Param&egrave;tres");
define("_SETSAVED", "<em>Vos changements ont &eacute;t&eacute; enregistr&eacute;s!</em>");
define("_SORRYNOTADMIN", "D&eacute;sol&eacute;, mais vous n'&ecirc;tes pas loggu&eacute; comme administrateur!");
define("_SOWHATRU", "Les membres profitent de nombreux avantages, alors");
define("_TOGOBACK", "Revenez en arri&egrave;re et changez vos param&egrave;tres de nouveau");
define("_TOVIEW", "Visualisez le r&eacute;sultat avec vos nouveaux param&egrave;tres");
define("_UCANCHNG", ", vous pouvez changer vos param&egrave;tres!");
define("_UNAVAIL", "La m&eacute;t&eacute;o n'est pas disponible");
define("_NODATA", "Il n'y a aucunes données fournies par cette station m&eacute;t&eacute;orologique, veuillez en choisir une autre.");
define("_NOTESTATION", "Note : Toutes les stations ne sont pas actives, si vous en choisissez une qui ne fonctionne pas, essayez-en une autre proche du m&ecirc;me endroit");
define("_UV", "UV");
define("_VIS", "Visibilit&eacute;");
define("_WADMINMENU", "Menu Administration");
define("_WANT2CHNG", "Voulez-vous changer de");
define("_WCITY", "Ville");
define("_WCNTRY", "Pays");
define("_WDEFAULTSET", "Param&egrave;tres par d&eacute;faut (pour tous les utilisateurs anonymes et les utilisateurs qui n'ont pas encore configur&eacute; XP-Weather)");
define("_WDETAILED", "Pr&eacute;visions détaill&eacute;es");
define("_WFRI", "Ven");
define("_WHIGH", "Haute");
define("_WIND", "Vent");
define("_WINDSPD", "Vitesse du vent");
define("_WJOIN", "Les membres profitent de nombreux avantages, cliquez ici pour vous enregistrer!");
define("_WLOGIN", "Login");
define("_WLOW", "Basse");
define("_WMEMBER", "membre");
define("_WMON", "Lun");
define("_WNOTLOGGEDON", "D&eacute;sol&eacute;, vous n'&ecirc;tes pas loggu&eacute;");
define("_WREGISTER", "Vous enregistrer");
define("_WRITEFAIL", "Le syst&egrave;me n'a pas r&eacute;ussi &agrave; sauvegarder les donn&eacute;es. Veuillez contr&ocirc;lez les droits de votre r&eacute;pertoire cache");
define("_READFAIL", "Le syst&egrave;me ne peut lire les fichiers du cache. Veuillez v&eacute;rifier les permissions sur ce fichier : ");
define("_WSAT", "Sam");
define("_WSELECT", "S&eacute;lectionnez un(e) ");
define("_WSTATION", "Station");
define("_WSUBDIV", "Sous-division");
define("_WSUN", "Dim");
define("_WTEMP", "Temp&eacute;rature");
define("_WTHU", "Jeu");
define("_WTUE", "Mar");
define("_WVIEWPREF", "Unit&eacute;s de mesures");
define("_WWED", "Mer");
define("_WYOURSET", "Vos param&egrave;tres actuels (Pour votre compte seulement)");
define("_SATELL", "Satellite");
define("_PRECIP", "Pr&eacute;cipitations");
define("_RAISON", ". Base de donn&eacute;es : Pas d'installation ou non disponible!");
define("_INSTALL", "Pour Une premi&egrave;re installation, cliquez");
define("_ICI", "ici");
?>
