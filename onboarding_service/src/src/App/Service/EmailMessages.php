<?php

namespace METRIC\App\Service;

use METRIC\App\Config\AppConfig;

class EmailMessages {

	public static function emailCase0(string $firstName, string $language, string $host, string $hash) {

		// enlace hash retargeting
		$host_link = ($host == "prod") ? "https://www.bbva.ch" : "https://bbvach.openweb.bbva";

		if ($language == "en") {
			$link = $host_link . AppConfig::getInstance()->getValue("RETARGETING_URI_EN") . "/?step=3&hash=".$hash;
		} else {
			$link = $host_link . AppConfig::getInstance()->getValue("RETARGETING_URI_ES") . "/?step=3&hash=".$hash;
		}

		// enlace images
		$host = ($host == "prod") ? "https://www.bbva.ch" : "https://bbvach.openweb.bbva";

		$html_es = <<<html
			<!DOCTYPE html>
			<html lang="es">

			<head>

			    <meta http-equiv="Content-Type" content="text/html charset=UTF-8" />
			    <meta name="viewport" content="width=device-width, initial-scale=1.0">
			    <title>Document</title>
			    <link rel="noopener" target="_blank" href="https://fonts.googleapis.com/css2?family=Roboto&display=swap"
			        rel="stylesheet">
			    <style type="text/css">
			        @media only screen and (max-device-width: 576px) {
			           table, thead, tbody, tfoot {
			                width: 100vw;
			                max-width: 100vw;
			            }
			        }
			    </style>
			</head>

			<body>
			    <table border="0" width="600" height="auto"
			        style="margin: 0 auto; max-width: 600px; border: #6754b8 1px solid; font-family: Arial, Helvetica, sans-serif; border-bottom: #5a429c 25px solid;">
			        <thead width="600" height="130" style="max-width: 600px;">
			            <tr>
			                <td>
			                    <img src="$host/wp-content/themes/bbva-suiza-2021/assets/dist/img/600x130_BBVAs_New_Gen_Cabecera_Elige.gif" alt="BBVA New Gen">
			                </td>
			            </tr>
			        </thead>
			        <tbody width="600" height="auto" style="max-width: 600px; font-size: 20px;">
			            <tr style="color:#000000;">
			                <td style="padding-left: 15px; padding-bottom: 50px; padding-top: 50px; padding-right: 25px;">
			                    <p>Hola $firstName,</p>

			                    <p><b style="color:#2DCCCD;">¡Enhorabuena!</b> Estás a un paso de empezar tu experiencia 
			                    New Gen.</p>

			                    <p style="margin-bottom: 0;">Hemos recibido toda tu información, te animamos a completar
			                     el último paso, <b>identifícate a través del siguiente link:</b></p>

			                    <br><br>

			                    <p style="margin-top: 0; padding-left: 30px;"><a href="$link" target="_blank"
			                            style="color: #2DCCCD; text-decoration: none; width: 550px; word-break: break-word;">$link</a></p>

			                    <br>

			                    <p style="color: #8F7AE5;"><b>No te pierdas New Gen.</b></p>

			                    <p>Saludos, Isabel</p>

			                </td>
			            </tr>
			        </tbody>
			        <tfoot width="600" height="auto" style="max-width: 600px; font-size: 13px;">

			            <tr style="color:#000000;">
			                <td style="padding-left: 15px; padding-top: 15px;">
			                    <img width="110px" src="$host/wp-content/themes/bbva-suiza-2021/assets/dist/img/bbva-logo-newgen-blue.png" alt="Logo BBVA New Gen">
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-left: 15px;">
			                    <p style="color: #8F7AE5; margin-bottom: 0;"><b>Customer support</b></p>
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-left: 15px;">
			                    <a href="mailto:support.newgen@bbva.ch" target="_blank"
			                        style="color:#000000;"><b>support.newgen@bbva.ch</b></a>
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-left: 15px;">
			                    <a href="http://www.bbva.ch" target="_blank" style="color:#47D0BD;"><b>www.bbva.ch</b></a>
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-left: 15px;">
			                    <p style="margin-bottom: 0;">Follow us:</p>
			                </td>
			            </tr>
			            <tr style="display: flex;">
			                <td width="30" height="30" style="margin-right: 5px; padding-left: 15px;">
			                    <a href="https://twitter.com/BBVANewGen_" target="_blank">
			                        <img width="30" height="30" style="border-radius: 50%; background-color: #8f7ae5;"
			                            src="https://www.bbva.ch/wp-content/uploads/2022/05/twitter-1.png" alt="Twitter">
			                    </a>
			                </td>
			                <td width="30" height="30" style="margin-right: 5px;">
			                    <a href="https://www.linkedin.com/showcase/bbva-switzerland" target="_blank">
			                        <img width="30" height="30" style="border-radius: 50%; background-color: #8f7ae5;"
			                            src="https://www.bbva.ch/wp-content/uploads/2022/05/linkedin-2.png" alt="LinkedIn">
			                    </a>
			                </td>
			                <td width="30" height="30">
			                    <a href="https://www.youtube.com/channel/UCkf-FU_msc44FXpp456u_0w" target="_blank">
			                        <img width="30" height="30" style="border-radius: 50%; background-color: #8f7ae5;"
			                            src="https://www.bbva.ch/wp-content/uploads/2022/05/youtube-1.png" alt="Youtube">
			                    </a>
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-bottom: 15px; padding-left: 15px;">
			                    <p style="color: #388D4F; font-size: 9px;">
			                        Before you print this message please consider if its really necessary. Antes de imprimir este
			                        mensaje, por favor considera que es necesario hacerlo.
			                    </p>
			                </td>
			            </tr>
			        </tfoot>
			    </table>

			</body>

html;
		
		$html_en = <<<html
			<!DOCTYPE html>
			<html lang="es">

			<head>

			    <meta http-equiv="Content-Type" content="text/html charset=UTF-8" />
			    <meta name="viewport" content="width=device-width, initial-scale=1.0">
			    <title>Document</title>
			    <link rel="noopener" target="_blank" href="https://fonts.googleapis.com/css2?family=Roboto&display=swap"
			        rel="stylesheet">
			    <style type="text/css">
			        @media only screen and (max-device-width: 576px) {
			           table, thead, tbody, tfoot {
			                width: 100vw;
			                max-width: 100vw;
			            }
			        }
			    </style>
			</head>

			<body>
			    <table border="0" width="600" height="auto"
			        style="margin: 0 auto; max-width: 600px; border: #6754b8 1px solid; font-family: Arial, Helvetica, sans-serif; border-bottom: #5a429c 25px solid;">
			        <thead width="600" height="130" style="max-width: 600px;">
			            <tr>
			                <td>
			                    <img src="$host/wp-content/themes/bbva-suiza-2021/assets/dist/img/600x130_BBVAs_New_Gen_Cabecera_Elige_Ing.gif" alt="BBVA New Gen">
			                </td>
			            </tr>
			        </thead>
			        <tbody width="600" height="auto" style="max-width: 600px; font-size: 20px;">
			            <tr style="color:#000000;">
			                <td style="padding-left: 15px; padding-bottom: 50px; padding-top: 50px; padding-right: 25px;">
			                    <p>Hello $firstName,</p>

			                    <p><b style="color:#2DCCCD;">Congratulations!</b> You are one-step away from your New Gen experience.</p>

			                    <p style="margin-bottom: 0;">We have received all your information, we encourage you to complete the last step, <b>log in through the following link:</b></p>

			                    <br><br>

			                    <p style="margin-top: 0; padding-left: 30px;"><a href="$link" target="_blank"
			                            style="color: #2DCCCD; text-decoration: none; width: 550px; word-break: break-word;">$link</a></p>

			                    <br>

			                    <p style="color: #8F7AE5;"><b>Don't miss out New Gen.</b></p>

			                    <p>Cheers, Isabel</p>
			                </td>
			            </tr>
			        </tbody>
			        <tfoot width="600" height="auto" style="max-width: 600px; font-size: 13px;">

			            <tr style="color:#000000;">
			                <td style="padding-left: 15px; padding-top: 15px;">
			                    <img width="110px" src="$host/wp-content/themes/bbva-suiza-2021/assets/dist/img/bbva-logo-newgen-blue.png" alt="Logo BBVA New Gen">
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-left: 15px;">
			                    <p style="color: #8F7AE5; margin-bottom: 0;"><b>Customer support</b></p>
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-left: 15px;">
			                    <a href="mailto:support.newgen@bbva.ch" target="_blank"
			                        style="color:#000000;"><b>support.newgen@bbva.ch</b></a>
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-left: 15px;">
			                    <a href="http://www.bbva.ch" target="_blank" style="color:#47D0BD;"><b>www.bbva.ch</b></a>
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-left: 15px;">
			                    <p style="margin-bottom: 0;">Follow us:</p>
			                </td>
			            </tr>
			            <tr style="display: flex;">
			                <td width="30" height="30" style="margin-right: 5px; padding-left: 15px;">
			                    <a href="https://twitter.com/BBVANewGen_" target="_blank">
			                        <img width="30" height="30" style="border-radius: 50%; background-color: #8f7ae5;"
			                            src="https://www.bbva.ch/wp-content/uploads/2022/05/twitter-1.png" alt="Twitter">
			                    </a>
			                </td>
			                <td width="30" height="30" style="margin-right: 5px;">
			                    <a href="https://www.linkedin.com/showcase/bbva-switzerland" target="_blank">
			                        <img width="30" height="30" style="border-radius: 50%; background-color: #8f7ae5;"
			                            src="https://www.bbva.ch/wp-content/uploads/2022/05/linkedin-2.png" alt="LinkedIn">
			                    </a>
			                </td>
			                <td width="30" height="30">
			                    <a href="https://www.youtube.com/channel/UCkf-FU_msc44FXpp456u_0w" target="_blank">
			                        <img width="30" height="30" style="border-radius: 50%; background-color: #8f7ae5;"
			                            src="https://www.bbva.ch/wp-content/uploads/2022/05/youtube-1.png" alt="Youtube">
			                    </a>
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-bottom: 15px; padding-left: 15px;">
			                    <p style="color: #388D4F; font-size: 9px;">
			                        Before you print this message please consider if its really necessary. Antes de imprimir este
			                        mensaje, por favor considera que es necesario hacerlo.
			                    </p>
			                </td>
			            </tr>
			        </tfoot>
			    </table>

			</body>

html;

		if ($language === "es") {
			$html = $html_es;
		} else {
			$html = $html_en;
		}

		return $html;

	}

	public static function emailCase1(string $firstName, string $language, string $host, string $hash) {

		// enlace hash retargeting
		$host_link = ($host == "prod") ? "https://www.bbva.ch" : "https://bbvach.openweb.bbva";

		if ($language == "en") {
			$link = $host_link . AppConfig::getInstance()->getValue("RETARGETING_URI_EN") . "/?step=3&hash=".$hash;
		} else {
			$link = $host_link . AppConfig::getInstance()->getValue("RETARGETING_URI_ES") . "/?step=3&hash=".$hash;
		}

		// enlace images
		$host = ($host == "prod") ? "https://www.bbva.ch" : "https://bbvach.openweb.bbva";

		$html_es = <<<html
			<!DOCTYPE html>
			<html lang="es">

			<head>

			    <meta http-equiv="Content-Type" content="text/html charset=UTF-8" />
			    <meta name="viewport" content="width=device-width, initial-scale=1.0">
			    <title>Document</title>
			    <link rel="noopener" target="_blank" href="https://fonts.googleapis.com/css2?family=Roboto&display=swap"
			        rel="stylesheet">
			    <style type="text/css">
			        @media only screen and (max-device-width: 576px) {
			           table, thead, tbody, tfoot {
			                width: 100vw;
			                max-width: 100vw;
			            }
			        }
			    </style>
			</head>

			<body>
			    <table border="0" width="600" height="auto"
			        style="margin: 0 auto; max-width: 600px; border: #6754b8 1px solid; font-family: Arial, Helvetica, sans-serif; border-bottom: #5a429c 25px solid;">
			        <thead width="600" height="130" style="max-width: 600px;">
			            <tr>
			                <td>
			                    <img src="$host/wp-content/themes/bbva-suiza-2021/assets/dist/img/600x130_BBVAs_New_Gen_Cabecera_Elige.gif" alt="BBVA New Gen">
			                </td>
			            </tr>
			        </thead>
			        <tbody width="600" height="auto" style="max-width: 600px; font-size: 20px;">
			            <tr style="color:#000000;">
			                <td style="padding-left: 15px; padding-bottom: 50px; padding-top: 50px; padding-right: 25px;">
			                    <p>Hola $firstName,</p>

			                    <p style="color: #2DCCCD;"><b>No te pierdas New Gen.</b></p>

			                    <p style="margin-bottom: 0;">Hemos recibido toda tu información y sólo te falta completar el último paso. <b>Identifícate a través
			                            del
			                            siguiente link:</b></p>

			                    <br><br>

			                    <p style="margin-top: 0; padding-left: 30px;"><a href="$link" target="_blank"
			                            style="color: #2DCCCD; text-decoration: none; width: 550px; word-break: break-word;">$link</a></p>

			                    <br>

			                    <p style="color: #8F7AE5;"><b>New Gen está en tus manos.</b></p>

			                    <p>Saludos, Isabel</p>
			                </td>
			            </tr>
			        </tbody>
			        <tfoot width="600" height="auto" style="max-width: 600px; font-size: 13px;">
			            <tr style="color:#000000;">
			                <td style="padding-left: 15px; padding-top: 15px;">
			                    <img width="110px" src="$host/wp-content/themes/bbva-suiza-2021/assets/dist/img/bbva-logo-newgen-blue.png" alt="Logo BBVA New Gen">
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-left: 15px;">
			                    <p style="color: #6754b8; margin-bottom: 0;"><b>Customer support</b></p>
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-left: 15px;">
			                    <a href="mailto:support.newgen@bbva.ch" target="_blank"
			                        style="color:#000000;"><b>support.newgen@bbva.ch</b></a>
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-left: 15px;">
			                    <a href="http://www.bbva.ch" target="_blank" style="color:#47D0BD;"><b>www.bbva.ch</b></a>
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-left: 15px;">
			                    <p style="margin-bottom: 0;">Follow us:</p>
			                </td>
			            </tr>
			            <tr style="display: flex;">
			                <td width="30" height="30" style="margin-right: 5px; padding-left: 15px;">
			                    <a href="https://twitter.com/BBVANewGen_" target="_blank">
			                        <img width="30" height="30" style="border-radius: 50%; background-color: #8f7ae5;"
			                            src="https://www.bbva.ch/wp-content/uploads/2022/05/twitter-1.png" alt="Twitter">
			                    </a>
			                </td>
			                <td width="30" height="30" style="margin-right: 5px;">
			                    <a href="https://www.linkedin.com/showcase/bbva-switzerland" target="_blank">
			                        <img width="30" height="30" style="border-radius: 50%; background-color: #8f7ae5;"
			                            src="https://www.bbva.ch/wp-content/uploads/2022/05/linkedin-2.png" alt="LinkedIn">
			                    </a>
			                </td>
			                <td width="30" height="30">
			                    <a href="https://www.youtube.com/channel/UCkf-FU_msc44FXpp456u_0w" target="_blank">
			                        <img width="30" height="30" style="border-radius: 50%; background-color: #8f7ae5;"
			                            src="https://www.bbva.ch/wp-content/uploads/2022/05/youtube-1.png" alt="Youtube">
			                    </a>
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-bottom: 15px; padding-left: 15px;">
			                    <p style="color: #388D4F; font-size: 9px;">
			                        Before you print this message please consider if its really necessary. Antes de imprimir este
			                        mensaje, por favor considera que es necesario hacerlo.
			                    </p>
			                </td>
			            </tr>
			        </tfoot>
			    </table>

			</body>

html;
		
		$html_en = <<<html
			<!DOCTYPE html>
			<html lang="es">

			<head>

			    <meta http-equiv="Content-Type" content="text/html charset=UTF-8" />
			    <meta name="viewport" content="width=device-width, initial-scale=1.0">
			    <title>Document</title>
			    <link rel="noopener" target="_blank" href="https://fonts.googleapis.com/css2?family=Roboto&display=swap"
			        rel="stylesheet">
			    <style type="text/css">
			        @media only screen and (max-device-width: 576px) {
			           table, thead, tbody, tfoot {
			                width: 100vw;
			                max-width: 100vw;
			            }
			        }
			    </style>
			</head>

			<body>
			    <table border="0" width="600" height="auto"
			        style="margin: 0 auto; max-width: 600px; border: #6754b8 1px solid; font-family: Arial, Helvetica, sans-serif; border-bottom: #5a429c 25px solid;">
			        <thead width="600" height="130" style="max-width: 600px;">
			            <tr>
			                <td>
			                    <img src="$host/wp-content/themes/bbva-suiza-2021/assets/dist/img/600x130_BBVAs_New_Gen_Cabecera_Elige_Ing.gif" alt="BBVA New Gen">
			                </td>
			            </tr>
			        </thead>
			        <tbody width="600" height="auto" style="max-width: 600px; font-size: 20px;">
			            <tr style="color:#000000;">
			                <td style="padding-left: 15px; padding-bottom: 50px; padding-top: 50px; padding-right: 25px;">
			                    <p>Hello $firstName,</p>

			                    <p><b style="color: #2DCCCD;">Don't miss New Gen.</b> You are one-step away from your New Gen experience.</p>

			                    <p style="margin-bottom: 0;">We have received all your information and you just need to complete the last step.  <b>Please identify yourself by clicking on the following link:</b></p>

			                    <br><br>

			                    <p style="margin-top: 0; padding-left: 30px;"><a href="$link" target="_blank"
			                            style="color: #2DCCCD; text-decoration: none; width: 550px; word-break: break-word;">$link</a></p>
			                    <br>

			                    <p style="color: #8F7AE5;"><b>New Gen is in your hands.</b></p>

			                    <p>Regards, Isabel</p>
			                </td>
			            </tr>
			        </tbody>
			        <tfoot width="600" height="auto" style="max-width: 600px; font-size: 13px;">

			            <tr style="color:#000000;">
			                <td style="padding-left: 15px; padding-top: 15px;">
			                    <img width="110px" src="$host/wp-content/themes/bbva-suiza-2021/assets/dist/img/bbva-logo-newgen-blue.png" alt="Logo BBVA New Gen">
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-left: 15px;">
			                    <p style="color: #8F7AE5; margin-bottom: 0;"><b>Customer support</b></p>
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-left: 15px;">
			                    <a href="mailto:support.newgen@bbva.ch" target="_blank"
			                        style="color:#000000;"><b>support.newgen@bbva.ch</b></a>
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-left: 15px;">
			                    <a href="http://www.bbva.ch" target="_blank" style="color:#47D0BD;"><b>www.bbva.ch</b></a>
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-left: 15px;">
			                    <p style="margin-bottom: 0;">Follow us:</p>
			                </td>
			            </tr>
			            <tr style="display: flex;">
			                <td width="30" height="30" style="margin-right: 5px; padding-left: 15px;">
			                    <a href="https://twitter.com/BBVANewGen_" target="_blank">
			                        <img width="30" height="30" style="border-radius: 50%; background-color: #8f7ae5;"
			                            src="https://www.bbva.ch/wp-content/uploads/2022/05/twitter-1.png" alt="Twitter">
			                    </a>
			                </td>
			                <td width="30" height="30" style="margin-right: 5px;">
			                    <a href="https://www.linkedin.com/showcase/bbva-switzerland" target="_blank">
			                        <img width="30" height="30" style="border-radius: 50%; background-color: #8f7ae5;"
			                            src="https://www.bbva.ch/wp-content/uploads/2022/05/linkedin-2.png" alt="LinkedIn">
			                    </a>
			                </td>
			                <td width="30" height="30">
			                    <a href="https://www.youtube.com/channel/UCkf-FU_msc44FXpp456u_0w" target="_blank">
			                        <img width="30" height="30" style="border-radius: 50%; background-color: #8f7ae5;"
			                            src="https://www.bbva.ch/wp-content/uploads/2022/05/youtube-1.png" alt="Youtube">
			                    </a>
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-bottom: 15px; padding-left: 15px;">
			                    <p style="color: #388D4F; font-size: 9px;">
			                        Before you print this message please consider if its really necessary. Antes de imprimir este
			                        mensaje, por favor considera que es necesario hacerlo.
			                    </p>
			                </td>
			            </tr>
			        </tfoot>
			    </table>

			</body>

html;

		if ($language === "es") {
			$html = $html_es;
		} else {
			$html = $html_en;
		}

		return $html;

	}

	public static function emailCase2(string $firstName, string $language, string $host, string $hash) {

		// enlace hash retargeting
		$host_link = ($host == "prod") ? "https://www.bbva.ch" : "https://bbvach.openweb.bbva";

		if ($language == "en") {
			$link = $host_link . AppConfig::getInstance()->getValue("RETARGETING_URI_EN") . "/?step=3&hash=".$hash;
		} else {
			$link = $host_link . AppConfig::getInstance()->getValue("RETARGETING_URI_ES") . "/?step=3&hash=".$hash;
		}

		// enlace images
		$host = ($host == "prod") ? "https://www.bbva.ch" : "https://bbvach.openweb.bbva";

		$html_es = <<<html
			<!DOCTYPE html>
			<html lang="es">

			<head>

			    <meta http-equiv="Content-Type" content="text/html charset=UTF-8" />
			    <meta name="viewport" content="width=device-width, initial-scale=1.0">
			    <title>Document</title>
			    <link rel="noopener" target="_blank" href="https://fonts.googleapis.com/css2?family=Roboto&display=swap"
			        rel="stylesheet">
			    <style type="text/css">
			        @media only screen and (max-device-width: 576px) {
			           table, thead, tbody, tfoot {
			                width: 100vw;
			                max-width: 100vw;
			            }
			        }
			    </style>
			</head>

			<body>
			    <table border="0" width="600" height="auto"
			        style="margin: 0 auto; max-width: 600px; border: #6754b8 1px solid; font-family: Arial, Helvetica, sans-serif; border-bottom: #5a429c 25px solid;">
			        <thead width="600" height="130" style="max-width: 600px;">
			            <tr>
			                <td>
			                    <img src="$host/wp-content/themes/bbva-suiza-2021/assets/dist/img/600x130_BBVAs_New_Gen_Cabecera_Elige.gif" alt="BBVA New Gen">
			                </td>
			            </tr>
			        </thead>
			        <tbody width="600" height="auto" style="max-width: 600px; font-size: 20px;">
			            <tr style="color:#000000;">
			                <td style="padding-left: 15px; padding-bottom: 50px; padding-top: 50px; padding-right: 25px;">
			                    <p>Hola $firstName,</p>

			                  	<p><b style="color:#2DCCCD;">El 98% de tu cuenta New Gen ya está lista.</b></p>

			                    <p style="margin-bottom: 0;">Completa tu identifícación y comienza tu experiencia New Gen. <b>Accede al siguiente link y ya estarás al 100%:</b></p>

			                    <br><br>

			                    <p style="margin-top: 0; padding-left: 30px;"><a href="$link" target="_blank"
			                            style="color: #2DCCCD; text-decoration: none; width: 550px; word-break: break-word;">$link</a></p>

			                    <br>

			                    <p style="color: #8F7AE5;"><b>Invierte en ti con New Gen.</b></p>

			                    <p>Saludos, Isabel</p>
			                </td>
			            </tr>
			        </tbody>
			        <tfoot width="600" height="auto" style="max-width: 600px; font-size: 13px;">
			            <tr style="color:#000000;">
			                <td style="padding-left: 15px; padding-top: 15px;">
			                    <img width="110px" src="$host/wp-content/themes/bbva-suiza-2021/assets/dist/img/bbva-logo-newgen-blue.png" alt="Logo BBVA New Gen">
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-left: 15px;">
			                    <p style="color: #6754b8; margin-bottom: 0;"><b>Customer support</b></p>
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-left: 15px;">
			                    <a href="mailto:support.newgen@bbva.ch" target="_blank"
			                        style="color:#000000;"><b>support.newgen@bbva.ch</b></a>
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-left: 15px;">
			                    <a href="http://www.bbva.ch" target="_blank" style="color:#47D0BD;"><b>www.bbva.ch</b></a>
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-left: 15px;">
			                    <p style="margin-bottom: 0;">Follow us:</p>
			                </td>
			            </tr>
			            <tr style="display: flex;">
			                <td width="30" height="30" style="margin-right: 5px; padding-left: 15px;">
			                    <a href="https://twitter.com/BBVANewGen_" target="_blank">
			                        <img width="30" height="30" style="border-radius: 50%; background-color: #8f7ae5;"
			                            src="https://www.bbva.ch/wp-content/uploads/2022/05/twitter-1.png" alt="Twitter">
			                    </a>
			                </td>
			                <td width="30" height="30" style="margin-right: 5px;">
			                    <a href="https://www.linkedin.com/showcase/bbva-switzerland" target="_blank">
			                        <img width="30" height="30" style="border-radius: 50%; background-color: #8f7ae5;"
			                            src="https://www.bbva.ch/wp-content/uploads/2022/05/linkedin-2.png" alt="LinkedIn">
			                    </a>
			                </td>
			                <td width="30" height="30">
			                    <a href="https://www.youtube.com/channel/UCkf-FU_msc44FXpp456u_0w" target="_blank">
			                        <img width="30" height="30" style="border-radius: 50%; background-color: #8f7ae5;"
			                            src="https://www.bbva.ch/wp-content/uploads/2022/05/youtube-1.png" alt="Youtube">
			                    </a>
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-bottom: 15px; padding-left: 15px;">
			                    <p style="color: #32de84; font-size: 9px;">
			                        Before you print this message please consider if its really necessary. Antes de imprimir este
			                        mensaje, por favor considera que es necesario hacerlo.
			                    </p>
			                </td>
			            </tr>
			        </tfoot>
			    </table>

			</body>

html;
		
		$html_en = <<<html
			<!DOCTYPE html>
			<html lang="es">

			<head>

			    <meta http-equiv="Content-Type" content="text/html charset=UTF-8" />
			    <meta name="viewport" content="width=device-width, initial-scale=1.0">
			    <title>Document</title>
			    <link rel="noopener" target="_blank" href="https://fonts.googleapis.com/css2?family=Roboto&display=swap"
			        rel="stylesheet">
			    <style type="text/css">
			        @media only screen and (max-device-width: 576px) {
			           table, thead, tbody, tfoot {
			                width: 100vw;
			                max-width: 100vw;
			            }
			        }
			    </style>
			</head>

			<body>
			    <table border="0" width="600" height="auto"
			        style="margin: 0 auto; max-width: 600px; border: #6754b8 1px solid; font-family: Arial, Helvetica, sans-serif; border-bottom: #5a429c 25px solid;">
			        <thead width="600" height="130" style="max-width: 600px;">
			            <tr>
			                <td>
			                    <img src="$host/wp-content/themes/bbva-suiza-2021/assets/dist/img/600x130_BBVAs_New_Gen_Cabecera_Elige_Ing.gif" alt="BBVA New Gen">
			                </td>
			            </tr>
			        </thead>
			        <tbody width="600" height="auto" style="max-width: 600px; font-size: 20px;">
			            <tr style="color:#000000;">
			                <td style="padding-left: 15px; padding-bottom: 50px; padding-top: 50px; padding-right: 25px;">
			                    <p>Hello $firstName,</p>

			                  	<p><b style="color:#2DCCCD;">El 98% of your New Gen account is ready.</b></p>

			                    <p style="margin-bottom: 0;">Complete your identification and start your New Gen experience. <b>Please access the following link and you will be 100% ready:</b></p>

			                    <br><br>

			                    <p style="margin-top: 0; padding-left: 30px;"><a href="$link" target="_blank"
			                            style="color: #2DCCCD; text-decoration: none; width: 550px; word-break: break-word;">$link</a></p>

			                    <br>

			                    <p style="color: #8F7AE5;"><b>Invest in yourself with New Gen. </b></p>

			                    <p>Regards, Isabel</p>
			                </td>
			            </tr>
			        </tbody>
			        <tfoot width="600" height="auto" style="max-width: 600px; font-size: 13px;">
			            <tr style="color:#000000;">
			                <td style="padding-left: 15px; padding-top: 15px;">
			                    <img width="110px" src="$host/wp-content/themes/bbva-suiza-2021/assets/dist/img/bbva-logo-newgen-blue.png" alt="Logo BBVA New Gen">
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-left: 15px;">
			                    <p style="color: #6754b8; margin-bottom: 0;"><b>Customer support</b></p>
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-left: 15px;">
			                    <a href="mailto:support.newgen@bbva.ch" target="_blank"
			                        style="color:#000000;"><b>support.newgen@bbva.ch</b></a>
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-left: 15px;">
			                    <a href="http://www.bbva.ch" target="_blank" style="color:#47D0BD;"><b>www.bbva.ch</b></a>
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-left: 15px;">
			                    <p style="margin-bottom: 0;">Follow us:</p>
			                </td>
			            </tr>
			            <tr style="display: flex;">
			                <td width="30" height="30" style="margin-right: 5px; padding-left: 15px;">
			                    <a href="https://twitter.com/BBVANewGen_" target="_blank">
			                        <img width="30" height="30" style="border-radius: 50%; background-color: #8f7ae5;"
			                            src="https://www.bbva.ch/wp-content/uploads/2022/05/twitter-1.png" alt="Twitter">
			                    </a>
			                </td>
			                <td width="30" height="30" style="margin-right: 5px;">
			                    <a href="https://www.linkedin.com/showcase/bbva-switzerland" target="_blank">
			                        <img width="30" height="30" style="border-radius: 50%; background-color: #8f7ae5;"
			                            src="https://www.bbva.ch/wp-content/uploads/2022/05/linkedin-2.png" alt="LinkedIn">
			                    </a>
			                </td>
			                <td width="30" height="30">
			                    <a href="https://www.youtube.com/channel/UCkf-FU_msc44FXpp456u_0w" target="_blank">
			                        <img width="30" height="30" style="border-radius: 50%; background-color: #8f7ae5;"
			                            src="https://www.bbva.ch/wp-content/uploads/2022/05/youtube-1.png" alt="Youtube">
			                    </a>
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-bottom: 15px; padding-left: 15px;">
			                    <p style="color: #32de84; font-size: 9px;">
			                        Before you print this message please consider if its really necessary. Antes de imprimir este
			                        mensaje, por favor considera que es necesario hacerlo.
			                    </p>
			                </td>
			            </tr>
			        </tfoot>
			    </table>

			</body>

html;

		if ($language === "es") {
			$html = $html_es;
		} else {
			$html = $html_en;
		}

		return $html;

	}

	public static function emailCase3(string $firstName, string $language, string $host, string $hash) {

		// enlace hash retargeting
		$host_link = ($host == "prod") ? "https://www.bbva.ch" : "https://bbvach.openweb.bbva";

		if ($language == "en") {
			$link = $host_link . AppConfig::getInstance()->getValue("RETARGETING_URI_EN") . "/?step=3&hash=".$hash;
		} else {
			$link = $host_link . AppConfig::getInstance()->getValue("RETARGETING_URI_ES") . "/?step=3&hash=".$hash;
		}

		// enlace images
		$host = ($host == "prod") ? "https://www.bbva.ch" : "https://bbvach.openweb.bbva";

		$html_es = <<<html
			<!DOCTYPE html>
			<html lang="es">

			<head>

			    <meta http-equiv="Content-Type" content="text/html charset=UTF-8" />
			    <meta name="viewport" content="width=device-width, initial-scale=1.0">
			    <title>Document</title>
			    <link rel="noopener" target="_blank" href="https://fonts.googleapis.com/css2?family=Roboto&display=swap"
			        rel="stylesheet">
			    <style type="text/css">
			        @media only screen and (max-device-width: 576px) {
			           table, thead, tbody, tfoot {
			                width: 100vw;
			                max-width: 100vw;
			            }
			        }
			    </style>
			</head>

			<body>
			    <table border="0" width="600" height="auto"
			        style="margin: 0 auto; max-width: 600px; border: #6754b8 1px solid; font-family: Arial, Helvetica, sans-serif; border-bottom: #5a429c 25px solid;">
			        <thead width="600" height="130" style="max-width: 600px;">
			            <tr>
			                <td>
			                    <img src="$host/wp-content/themes/bbva-suiza-2021/assets/dist/img/600x130_BBVAs_New_Gen_Cabecera_Elige.gif" alt="BBVA New Gen">
			                </td>
			            </tr>
			        </thead>
			        <tbody width="600" height="auto" style="max-width: 600px; font-size: 20px;">
			            <tr style="color:#000000;">
			                <td style="padding-left: 15px; padding-bottom: 50px; padding-top: 50px; padding-right: 25px;">
			                    <p>Hola $firstName,</p>

			                  	<p>Ahora que puedes elegir <b style="color:#2DCCCD;">invierte el futuro, activa tu cuenta New Gen ahora.</b></p>

			                    <p style="margin-bottom: 0;"><b>¡Te animamos a seguir con la solicitud de tu apertura de cuenta!</b></p>

			                    <br><br>

			                    <p style="margin-top: 0; padding-left: 30px;"><a href="$link" target="_blank"
			                            style="color: #2DCCCD; text-decoration: none; width: 550px; word-break: break-word;">$link</a></p>

			                    <br>

			                    <p style="color: #8F7AE5;"><b>No te pierdas New Gen.</b></p>

			                    <p>Saludos, Isabel</p>
			                </td>
			            </tr>
			        </tbody>
			        <tfoot width="600" height="auto" style="max-width: 600px; font-size: 13px;">
			            <tr style="color:#000000;">
			                <td style="padding-left: 15px; padding-top: 15px;">
			                    <img width="110px" src="$host/wp-content/themes/bbva-suiza-2021/assets/dist/img/bbva-logo-newgen-blue.png" alt="Logo BBVA New Gen">
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-left: 15px;">
			                    <p style="color: #6754b8; margin-bottom: 0;"><b>Customer support</b></p>
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-left: 15px;">
			                    <a href="mailto:support.newgen@bbva.ch" target="_blank"
			                        style="color:#000000;"><b>support.newgen@bbva.ch</b></a>
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-left: 15px;">
			                    <a href="http://www.bbva.ch" target="_blank" style="color:#47D0BD;"><b>www.bbva.ch</b></a>
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-left: 15px;">
			                    <p style="margin-bottom: 0;">Follow us:</p>
			                </td>
			            </tr>
			            <tr style="display: flex;">
			                <td width="30" height="30" style="margin-right: 5px; padding-left: 15px;">
			                    <a href="https://twitter.com/BBVANewGen_" target="_blank">
			                        <img width="30" height="30" style="border-radius: 50%; background-color: #8f7ae5;"
			                            src="https://www.bbva.ch/wp-content/uploads/2022/05/twitter-1.png" alt="Twitter">
			                    </a>
			                </td>
			                <td width="30" height="30" style="margin-right: 5px;">
			                    <a href="https://www.linkedin.com/showcase/bbva-switzerland" target="_blank">
			                        <img width="30" height="30" style="border-radius: 50%; background-color: #8f7ae5;"
			                            src="https://www.bbva.ch/wp-content/uploads/2022/05/linkedin-2.png" alt="LinkedIn">
			                    </a>
			                </td>
			                <td width="30" height="30">
			                    <a href="https://www.youtube.com/channel/UCkf-FU_msc44FXpp456u_0w" target="_blank">
			                        <img width="30" height="30" style="border-radius: 50%; background-color: #8f7ae5;"
			                            src="https://www.bbva.ch/wp-content/uploads/2022/05/youtube-1.png" alt="Youtube">
			                    </a>
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-bottom: 15px; padding-left: 15px;">
			                    <p style="color: #32de84; font-size: 9px;">
			                        Before you print this message please consider if its really necessary. Antes de imprimir este
			                        mensaje, por favor considera que es necesario hacerlo.
			                    </p>
			                </td>
			            </tr>
			        </tfoot>
			    </table>

			</body>

html;
		
		$html_en = <<<html
			<!DOCTYPE html>
			<html lang="es">

			<head>

			    <meta http-equiv="Content-Type" content="text/html charset=UTF-8" />
			    <meta name="viewport" content="width=device-width, initial-scale=1.0">
			    <title>Document</title>
			    <link rel="noopener" target="_blank" href="https://fonts.googleapis.com/css2?family=Roboto&display=swap"
			        rel="stylesheet">
			    <style type="text/css">
			        @media only screen and (max-device-width: 576px) {
			           table, thead, tbody, tfoot {
			                width: 100vw;
			                max-width: 100vw;
			            }
			        }
			    </style>
			</head>

			<body>
			    <table border="0" width="600" height="auto"
			        style="margin: 0 auto; max-width: 600px; border: #6754b8 1px solid; font-family: Arial, Helvetica, sans-serif; border-bottom: #5a429c 25px solid;">
			        <thead width="600" height="130" style="max-width: 600px;">
			            <tr>
			                <td>
			                    <img src="$host/wp-content/themes/bbva-suiza-2021/assets/dist/img/600x130_BBVAs_New_Gen_Cabecera_Elige_Ing.gif" alt="BBVA New Gen">
			                </td>
			            </tr>
			        </thead>
			        <tbody width="600" height="auto" style="max-width: 600px; font-size: 20px;">
			            <tr style="color:#000000;">
			                <td style="padding-left: 15px; padding-bottom: 50px; padding-top: 50px; padding-right: 25px;">
			                    <p>Hello $firstName,</p>

			                  	<p>Now that you can choose to <b style="color:#2DCCCD;">invest the future, activate your New Gen account now.</b></p>

			                    <p style="margin-bottom: 0;"><b>We encourage you to follow up with your account opening application!</b></p>

			                    <br><br>

			                    <p style="margin-top: 0; padding-left: 30px;"><a href="$link" target="_blank"
			                            style="color: #2DCCCD; text-decoration: none; width: 550px; word-break: break-word;">$link</a></p>

			                    <br>

			                    <p style="color: #8F7AE5;"><b>Don't miss out on New Gen.</b></p>

			                    <p>Regards, Isabel</p>
			                </td>
			            </tr>
			        </tbody>
			        <tfoot width="600" height="auto" style="max-width: 600px; font-size: 13px;">
			            <tr style="color:#000000;">
			                <td style="padding-left: 15px; padding-top: 15px;">
			                    <img width="110px" src="$host/wp-content/themes/bbva-suiza-2021/assets/dist/img/bbva-logo-newgen-blue.png" alt="Logo BBVA New Gen">
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-left: 15px;">
			                    <p style="color: #6754b8; margin-bottom: 0;"><b>Customer support</b></p>
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-left: 15px;">
			                    <a href="mailto:support.newgen@bbva.ch" target="_blank"
			                        style="color:#000000;"><b>support.newgen@bbva.ch</b></a>
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-left: 15px;">
			                    <a href="http://www.bbva.ch" target="_blank" style="color:#47D0BD;"><b>www.bbva.ch</b></a>
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-left: 15px;">
			                    <p style="margin-bottom: 0;">Follow us:</p>
			                </td>
			            </tr>
			            <tr style="display: flex;">
			                <td width="30" height="30" style="margin-right: 5px; padding-left: 15px;">
			                    <a href="https://twitter.com/BBVANewGen_" target="_blank">
			                        <img width="30" height="30" style="border-radius: 50%; background-color: #8f7ae5;"
			                            src="https://www.bbva.ch/wp-content/uploads/2022/05/twitter-1.png" alt="Twitter">
			                    </a>
			                </td>
			                <td width="30" height="30" style="margin-right: 5px;">
			                    <a href="https://www.linkedin.com/showcase/bbva-switzerland" target="_blank">
			                        <img width="30" height="30" style="border-radius: 50%; background-color: #8f7ae5;"
			                            src="https://www.bbva.ch/wp-content/uploads/2022/05/linkedin-2.png" alt="LinkedIn">
			                    </a>
			                </td>
			                <td width="30" height="30">
			                    <a href="https://www.youtube.com/channel/UCkf-FU_msc44FXpp456u_0w" target="_blank">
			                        <img width="30" height="30" style="border-radius: 50%; background-color: #8f7ae5;"
			                            src="https://www.bbva.ch/wp-content/uploads/2022/05/youtube-1.png" alt="Youtube">
			                    </a>
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-bottom: 15px; padding-left: 15px;">
			                    <p style="color: #32de84; font-size: 9px;">
			                        Before you print this message please consider if its really necessary. Antes de imprimir este
			                        mensaje, por favor considera que es necesario hacerlo.
			                    </p>
			                </td>
			            </tr>
			        </tfoot>
			    </table>

			</body>

html;

		if ($language === "es") {
			$html = $html_es;
		} else {
			$html = $html_en;
		}

		return $html;

	}

	public static function emailCase4(string $firstName, string $language, string $host, string $hash) {

		// enlace hash retargeting
		$host_link = ($host == "prod") ? "https://www.bbva.ch" : "https://bbvach.openweb.bbva";

		if ($language == "en") {
			$link = $host_link . AppConfig::getInstance()->getValue("RETARGETING_URI_EN") . "/?step=3&hash=".$hash;
		} else {
			$link = $host_link . AppConfig::getInstance()->getValue("RETARGETING_URI_ES") . "/?step=3&hash=".$hash;
		}

		// enlace images
		$host = ($host == "prod") ? "https://www.bbva.ch" : "https://bbvach.openweb.bbva";

		$html_es = <<<html
			<!DOCTYPE html>
			<html lang="es">

			<head>

			    <meta http-equiv="Content-Type" content="text/html charset=UTF-8" />
			    <meta name="viewport" content="width=device-width, initial-scale=1.0">
			    <title>Document</title>
			    <link rel="noopener" target="_blank" href="https://fonts.googleapis.com/css2?family=Roboto&display=swap"
			        rel="stylesheet">
			    <style type="text/css">
			        @media only screen and (max-device-width: 576px) {
			           table, thead, tbody, tfoot {
			                width: 100vw;
			                max-width: 100vw;
			            }
			        }
			    </style>
			</head>

			<body>
			    <table border="0" width="600" height="auto"
			        style="margin: 0 auto; max-width: 600px; border: #6754b8 1px solid; font-family: Arial, Helvetica, sans-serif; border-bottom: #5a429c 25px solid;">
			        <thead width="600" height="130" style="max-width: 600px;">
			            <tr>
			                <td>
			                    <img src="$host/wp-content/themes/bbva-suiza-2021/assets/dist/img/600x130_BBVAs_New_Gen_Cabecera_Elige.gif" alt="BBVA New Gen">
			                </td>
			            </tr>
			        </thead>
			        <tbody width="600" height="auto" style="max-width: 600px; font-size: 20px;">
			            <tr style="color:#000000;">
			                <td style="padding-left: 15px; padding-bottom: 50px; padding-top: 50px; padding-right: 25px;">
			                    <p>Hola $firstName,</p>

			                  	<p>El tiempo se acaba y estás a un paso de poder <b style="color:#2DCCCD;">invertir en las ideas que están cambiando el mundo.</b></p>

			                    <p style="margin-bottom: 0;"><b>Completa tu identificación</b> e invierte en lo que más crees.</p>

			                    <br><br>

			                    <p style="margin-top: 0; padding-left: 30px;"><a href="$link" target="_blank"
			                            style="color: #2DCCCD; text-decoration: none; width: 550px; word-break: break-word;">$link</a></p>

			                    <br>

			                    <p style="color: #8F7AE5;"><b>New Gen te está esperando.</b></p>

			                    <p>Saludos, Isabel</p>
			                </td>
			            </tr>
			        </tbody>
			        <tfoot width="600" height="auto" style="max-width: 600px; font-size: 13px;">
			            <tr style="color:#000000;">
			                <td style="padding-left: 15px; padding-top: 15px;">
			                    <img width="110px" src="$host/wp-content/themes/bbva-suiza-2021/assets/dist/img/bbva-logo-newgen-blue.png" alt="Logo BBVA New Gen">
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-left: 15px;">
			                    <p style="color: #6754b8; margin-bottom: 0;"><b>Customer support</b></p>
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-left: 15px;">
			                    <a href="mailto:support.newgen@bbva.ch" target="_blank"
			                        style="color:#000000;"><b>support.newgen@bbva.ch</b></a>
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-left: 15px;">
			                    <a href="http://www.bbva.ch" target="_blank" style="color:#47D0BD;"><b>www.bbva.ch</b></a>
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-left: 15px;">
			                    <p style="margin-bottom: 0;">Follow us:</p>
			                </td>
			            </tr>
			            <tr style="display: flex;">
			                <td width="30" height="30" style="margin-right: 5px; padding-left: 15px;">
			                    <a href="https://twitter.com/BBVANewGen_" target="_blank">
			                        <img width="30" height="30" style="border-radius: 50%; background-color: #8f7ae5;"
			                            src="https://www.bbva.ch/wp-content/uploads/2022/05/twitter-1.png" alt="Twitter">
			                    </a>
			                </td>
			                <td width="30" height="30" style="margin-right: 5px;">
			                    <a href="https://www.linkedin.com/showcase/bbva-switzerland" target="_blank">
			                        <img width="30" height="30" style="border-radius: 50%; background-color: #8f7ae5;"
			                            src="https://www.bbva.ch/wp-content/uploads/2022/05/linkedin-2.png" alt="LinkedIn">
			                    </a>
			                </td>
			                <td width="30" height="30">
			                    <a href="https://www.youtube.com/channel/UCkf-FU_msc44FXpp456u_0w" target="_blank">
			                        <img width="30" height="30" style="border-radius: 50%; background-color: #8f7ae5;"
			                            src="https://www.bbva.ch/wp-content/uploads/2022/05/youtube-1.png" alt="Youtube">
			                    </a>
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-bottom: 15px; padding-left: 15px;">
			                    <p style="color: #32de84; font-size: 9px;">
			                        Before you print this message please consider if its really necessary. Antes de imprimir este
			                        mensaje, por favor considera que es necesario hacerlo.
			                    </p>
			                </td>
			            </tr>
			        </tfoot>
			    </table>

			</body>

html;
		
		$html_en = <<<html
			<!DOCTYPE html>
			<html lang="es">

			<head>

			    <meta http-equiv="Content-Type" content="text/html charset=UTF-8" />
			    <meta name="viewport" content="width=device-width, initial-scale=1.0">
			    <title>Document</title>
			    <link rel="noopener" target="_blank" href="https://fonts.googleapis.com/css2?family=Roboto&display=swap"
			        rel="stylesheet">
			    <style type="text/css">
			        @media only screen and (max-device-width: 576px) {
			           table, thead, tbody, tfoot {
			                width: 100vw;
			                max-width: 100vw;
			            }
			        }
			    </style>
			</head>

			<body>
			    <table border="0" width="600" height="auto"
			        style="margin: 0 auto; max-width: 600px; border: #6754b8 1px solid; font-family: Arial, Helvetica, sans-serif; border-bottom: #5a429c 25px solid;">
			        <thead width="600" height="130" style="max-width: 600px;">
			            <tr>
			                <td>
			                    <img src="$host/wp-content/themes/bbva-suiza-2021/assets/dist/img/600x130_BBVAs_New_Gen_Cabecera_Elige_Ing.gif" alt="BBVA New Gen">
			                </td>
			            </tr>
			        </thead>
			        <tbody width="600" height="auto" style="max-width: 600px; font-size: 20px;">
			            <tr style="color:#000000;">
			                <td style="padding-left: 15px; padding-bottom: 50px; padding-top: 50px; padding-right: 25px;">
			                    <p>Hello $firstName,</p>

			                  	<p>Time is running out and you are one step away from <b style="color:#2DCCCD;"><b>investing in the ideas that are changing the world.</b></p>

			                    <p style="margin-bottom: 0;"><b>Complete your identification</b> and invest in what you believe in most.</p>

			                    <br><br>

			                    <p style="margin-top: 0; padding-left: 30px;"><a href="$link" target="_blank"
			                            style="color: #2DCCCD; text-decoration: none; width: 550px; word-break: break-word;">$link</a></p>

			                    <br>

			                    <p style="color: #6754b8;"><b>New Gen is waiting for you.</b></p>

			                    <p>Regards, Isabel</p>
			                </td>
			            </tr>
			        </tbody>
			        <tfoot width="600" height="auto" style="max-width: 600px; font-size: 13px;">
			            <tr style="color:#000000;">
			                <td style="padding-left: 15px; padding-top: 15px;">
			                    <img width="110px" src="$host/wp-content/themes/bbva-suiza-2021/assets/dist/img/bbva-logo-newgen-blue.png" alt="Logo BBVA New Gen">
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-left: 15px;">
			                    <p style="color: #6754b8; margin-bottom: 0;"><b>Customer support</b></p>
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-left: 15px;">
			                    <a href="mailto:support.newgen@bbva.ch" target="_blank"
			                        style="color:#000000;"><b>support.newgen@bbva.ch</b></a>
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-left: 15px;">
			                    <a href="http://www.bbva.ch" target="_blank" style="color:#47D0BD;"><b>www.bbva.ch</b></a>
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-left: 15px;">
			                    <p style="margin-bottom: 0;">Follow us:</p>
			                </td>
			            </tr>
			            <tr style="display: flex;">
			                <td width="30" height="30" style="margin-right: 5px; padding-left: 15px;">
			                    <a href="https://twitter.com/BBVANewGen_" target="_blank">
			                        <img width="30" height="30" style="border-radius: 50%; background-color: #8f7ae5;"
			                            src="https://www.bbva.ch/wp-content/uploads/2022/05/twitter-1.png" alt="Twitter">
			                    </a>
			                </td>
			                <td width="30" height="30" style="margin-right: 5px;">
			                    <a href="https://www.linkedin.com/showcase/bbva-switzerland" target="_blank">
			                        <img width="30" height="30" style="border-radius: 50%; background-color: #8f7ae5;"
			                            src="https://www.bbva.ch/wp-content/uploads/2022/05/linkedin-2.png" alt="LinkedIn">
			                    </a>
			                </td>
			                <td width="30" height="30">
			                    <a href="https://www.youtube.com/channel/UCkf-FU_msc44FXpp456u_0w" target="_blank">
			                        <img width="30" height="30" style="border-radius: 50%; background-color: #8f7ae5;"
			                            src="https://www.bbva.ch/wp-content/uploads/2022/05/youtube-1.png" alt="Youtube">
			                    </a>
			                </td>
			            </tr>
			            <tr>
			                <td style="padding-bottom: 15px; padding-left: 15px;">
			                    <p style="color: #32de84; font-size: 9px;">
			                        Before you print this message please consider if its really necessary. Antes de imprimir este
			                        mensaje, por favor considera que es necesario hacerlo.
			                    </p>
			                </td>
			            </tr>
			        </tfoot>
			    </table>

			</body>

html;

		if ($language === "es") {
			$html = $html_es;
		} else {
			$html = $html_en;
		}

		return $html;

	}
}