<?php

namespace METRIC\App\Service;

use METRIC\App\Config\AppConfig;

class EmailProspect {

	public static function getTemplate(array $prospectEmail, string $host)
	{

		$template = "";

		switch ($prospectEmail["template"]) {
			case 'template1':
				$template = self::getTemplate1($prospectEmail, $host);
				break;

			case 'template2':
				$template = self::getTemplate2($prospectEmail, $host);
				break;

			case 'template3':
				$template = self::getTemplate3($prospectEmail, $host);
				break;

			case 'template4':
				$template = self::getTemplate4($prospectEmail, $host);
				break;

			case 'template5':
				$template = self::getTemplate5($prospectEmail, $host);
				break;

			case 'template6':
				$template = self::getTemplate6($prospectEmail, $host);
				break;

			case 'template7':
				$template = self::getTemplate7($prospectEmail, $host);
				break;

			case 'template8':
				$template = self::getTemplate8($prospectEmail, $host);
				break;

			case 'template9':
				$template = self::getTemplate9($prospectEmail, $host);
				break;

			case 'template10':
				$template = self::getTemplate10($prospectEmail, $host);
				break;

			case 'template11':
				$template = self::getTemplate11($prospectEmail, $host);
				break;

			case 'template12':
				$template = self::getTemplate12($prospectEmail, $host);
				break;
			
			default:
				break;
		}

		return $template;

	}

	public static function getTemplate1(array $prospectEmail, string $host) {
		// enlace images
		$host = ($host == "prod") ? "https://www.bbva.ch" : "https://www.bbva.ch";
		$language = $prospectEmail['language'];
		$first_name = $prospectEmail['first_name'];
		$ng_tax_country1 = $prospectEmail['ng_tax_country1'];

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
			            <tr style="color:#6754b8;">
			                <td style="padding-left: 15px; padding-bottom: 50px; padding-top: 50px; padding-right: 25px;">
			                    <p>Hola $first_name,</p>

			                    <p>Gracias por tu interés en <span style="color: #6754b8;">BBVA New Gen</span>.</p>

			                    <p>Te confirmamos que hemos recibido tu solicitud de apertura con éxito.</p>

			                    <p style="margin-bottom: 0;">Para continuar con el proceso nuestro departamento de cumplimiento requiere la siguiente información adicional:</p>

			                    <br><br>

			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>País de residencia fiscal:</u></b></p>

			                    <br>

			                    <p>En los documentos recibidos nos indicas que tu país de residencia fiscal es <span style="">$ng_tax_country1</span>. Al tratarse de un país distinto de tu nacionalidad y/o domicilio, necesitamos tu permiso de residencia en este país.</p>

			                    <br><br>

			                    <p>Gracias por tu colaboración,</p>

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
			            <tr style="color:#6754b8;">
			                <td style="padding-left: 15px; padding-bottom: 50px; padding-top: 50px; padding-right: 25px;">
			                    <p>Hello $first_name,</p>

			                    <p>Thank you for your interest in <span style="color: #6754b8;">BBVA New Gen</span>.</p>

			                    <p>We confirm that we have successfully received your opening request.</p>

			                    <p style="margin-bottom: 0;">To continue with the process our compliance team requires the following additional information:</p>

			                    <br><br>

			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>Country of tax residence:</u></b></p>

			                    <br>

			                    <p>In the documents received you indicate that your country of tax residence is <span style="">$ng_tax_country1</span>. As this is a country other than your country of nationality and/or domicile, we require your residence permit from this country.</p>

			                    <br><br>

			                    <p>Thank you for your understanding,</p>

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

		if ($language === "es") {
			$html = $html_es;
		} else {
			$html = $html_en;
		}

		return $html;
	}

	public static function getTemplate2(array $prospectEmail, string $host) {
		// enlace images
		$host = ($host == "prod") ? "https://www.bbva.ch" : "https://www.bbva.ch";
		$language = $prospectEmail['language'];
		$first_name = $prospectEmail['first_name'];
		$ng_worth = $prospectEmail['ng_worth'];

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
			            <tr style="color:#6754b8;">
			                <td style="padding-left: 15px; padding-bottom: 50px; padding-top: 50px; padding-right: 25px;">
			                    <p>Hola $first_name,</p>

			                    <p>Gracias por tu interés en <span style="color: #6754b8;">BBVA New Gen</span>.</p>

			                    <p>Te confirmamos que hemos recibido tu solicitud de apertura con éxito.</p>

			                    <p style="margin-bottom: 0;">Para continuar con el proceso nuestro departamento de cumplimiento requiere la siguiente información adicional:</p>

			                    <br><br>

			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>Origen del patrimonio:</u></b></p>

			                    <br>

			                    <p>En la documentación que recibimos nos indicas que tu patrimonio actual es de <span style="">$ng_worth</span>.  ¿Podrías indicarnos el % de distribución de dicho patrimonio en las siguientes 3 categorías?</p>

			                    <br>

			                    <span style="margin-top: 0; padding-left: 30px; color: #072146;"><b>• Financiero:</b></span><br>
			                    <span style="margin-top: 0; padding-left: 30px; color: #072146;"><b>• Mobiliario:</b></span><br>
			                    <span style="margin-top: 0; padding-left: 30px; color: #072146;"><b>• Inmobiliario:</b></span>

			                    <br><br>

			                    <p>Gracias por tu colaboración,</p>

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
			            <tr style="color:#6754b8;">
			                <td style="padding-left: 15px; padding-bottom: 50px; padding-top: 50px; padding-right: 25px;">
			                    <p>Hello $first_name,</p>

			                    <p>Thank you for your interest in <span style="color: #6754b8;">BBVA New Gen</span>.</p>

			                    <p>We confirm that we have successfully received your opening request.</p>

			                    <p style="margin-bottom: 0;">To continue with the process our compliance team requires the following additional information:</p>

			                    <br><br>

			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>Distribution of your wealth:</u></b></p>

			                    <br>

			                    <p>In the documentation we received, you indicate that your current networth is <span style="">$ng_worth</span>. Could you please tell us the percentage distribution of that net worth in the following three categories?</p>

			                    <br>

			                    <span style="margin-top: 0; padding-left: 30px; color: #072146;"><b>• Financial:</b></span><br>
			                    <span style="margin-top: 0; padding-left: 30px; color: #072146;"><b>• Assets (Cars, ships, airplanes, etc):</b></span><br>
			                    <span style="margin-top: 0; padding-left: 30px; color: #072146;"><b>• Real Estate:</b></span>
			                    
			                    <br><br>

			                    <p>Thank you for your understanding,</p>

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

	public static function getTemplate3(array $prospectEmail, string $host) {
		// enlace images
		$host = ($host == "prod") ? "https://www.bbva.ch" : "https://www.bbva.ch";
		$language = $prospectEmail['language'];
		$first_name = $prospectEmail['first_name'];
		$company = $prospectEmail['company'];

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
			            <tr style="color:#6754b8;">
			                <td style="padding-left: 15px; padding-bottom: 50px; padding-top: 50px; padding-right: 25px;">
			                    <p>Hola $first_name,</p>

			                    <p>Gracias por tu interés en <span style="color: #6754b8;">BBVA New Gen</span>.</p>

			                    <p>Te confirmamos que hemos recibido tu solicitud de apertura con éxito.</p>

			                    <p style="margin-bottom: 0;">Para continuar con el proceso nuestro departamento de cumplimiento requiere la siguiente información adicional:</p>

			                    <br><br>

			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>Actividad profesional:</u></b></p>

			                    <br>

			                    <p>En la documentación recibida nos indicas que eres empleado en la compañía <span style="">$company</span>. Para esta sección necesitaríamos una copia de tu nomina donde podamos corroborar el nivel de ingreso indicado en el formulario, por favor, <u>¿Podrías enviarnos por este medio una copia de tu nómina?</u></p>

			                    <br><br>

			                    <p>Gracias por tu colaboración,</p>

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
			            <tr style="color:#6754b8;">
			                <td style="padding-left: 15px; padding-bottom: 50px; padding-top: 50px; padding-right: 25px;">
			                    <p>Hello $first_name,</p>

			                    <p>Thank you for your interest in <span style="color: #6754b8;">BBVA New Gen</span>.</p>

			                    <p>We confirm that we have successfully received your opening request.</p>

			                    <p style="margin-bottom: 0;">To continue with the process our compliance team requires the following additional information:</p>

			                    <br><br>

			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>Professional activity:</u></b></p>

			                    <br>

			                    <p>In the documentation received, you indicate that currently you are an employee of the company <span style="">$company</span>. For this section, we would need a copy of your payslip where we can verify the level of income indicated in the online form, please, <u> Could you send us a copy of your payslip?</u></p>

			                    <br><br>

			                    <p>Thank you for your understanding,</p>

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

	public static function getTemplate4(array $prospectEmail, string $host) {
		// enlace images
		$host = ($host == "prod") ? "https://www.bbva.ch" : "https://www.bbva.ch";
		$language = $prospectEmail['language'];
		$first_name = $prospectEmail['first_name'];
		$company = $prospectEmail['company'];

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
			            <tr style="color:#6754b8;">
			                <td style="padding-left: 15px; padding-bottom: 50px; padding-top: 50px; padding-right: 25px;">
			                    <p>Hola $first_name,</p>

			                    <p>Gracias por tu interés en <span style="color: #6754b8;">BBVA New Gen</span>.</p>

			                    <p>Te confirmamos que hemos recibido tu solicitud de apertura con éxito.</p>

			                    <p style="margin-bottom: 0;">Para continuar con el proceso nuestro departamento de cumplimiento requiere la siguiente información adicional:</p>

			                    <br><br>

			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>Actividad profesional:</u></b></p>

			                    <br>

			                    <p>En la documentación recibida nos indicas que eres autónomo en la compañía <span style="">$company</span>. Para esta sección necesitaríamos una prueba de tu actividad profesional como autónomo donde podamos verificar tus ingresos anuales aproximados. Por favor, <u>¿Podrías enviarnos por este medio una copia la última declaración jurada presentada o el alta como autónomo?</u></p>

			                    <br><br>

			                    <p>Gracias por tu colaboración,</p>

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
			            <tr style="color:#6754b8;">
			                <td style="padding-left: 15px; padding-bottom: 50px; padding-top: 50px; padding-right: 25px;">
			                    <p>Hello $first_name,</p>

			                    <p>Thank you for your interest in <span style="color: #6754b8;">BBVA New Gen</span>.</p>

			                    <p>We confirm that we have successfully received your opening request.</p>

			                    <p style="margin-bottom: 0;">To continue with the process our compliance team requires the following additional information:</p>

			                    <br><br>

			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>Professional activity:</u></b></p>

			                    <br>

			                    <p>In the documentation received, you indicate that you are selfemployed in the company <span style="">$company</span>. For this section, we request you to provide proof of your professional activity as a selfemployed person where we can verify your approximate annual income. Please, <u> could you send us a copy of the last tax declaration submitted or the registration as self-employed?</u></p>

			                    <br><br>

			                    <p>Thank you for your understanding,</p>

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

	public static function getTemplate5(array $prospectEmail, string $host) {
		// enlace images
		$host = ($host == "prod") ? "https://www.bbva.ch" : "https://www.bbva.ch";
		$language = $prospectEmail['language'];
		$first_name = $prospectEmail['first_name'];
		$ng_tax_country1 = $prospectEmail['ng_tax_country1'];
		$ng_worth = $prospectEmail['ng_worth'];

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
			            <tr style="color:#6754b8;">
			                <td style="padding-left: 15px; padding-bottom: 50px; padding-top: 50px; padding-right: 25px;">
			                    <p>Hola $first_name,</p>

			                    <p>Gracias por tu interés en <span style="color: #6754b8;">BBVA New Gen</span>.</p>

			                    <p>Te confirmamos que hemos recibido tu solicitud de apertura con éxito.</p>

			                    <p style="margin-bottom: 0;">Para continuar con el proceso nuestro departamento de cumplimiento requiere la siguiente información adicional:</p>

			                    <br>

			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>País de residencia fiscal:</u></b></p>

			                    <br>

			                    <p>En los documentos recibidos nos indicas que tu país de residencia fiscal es <span style="">$ng_tax_country1</span>. Al tratarse de un país distinto de tu nacionalidad y/o domicilio necesitamos tu permiso de residencia en este país.</p>

			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>Origen del patrimonio:</u></b></p>

			                    <br>

			                    <p>En la documentación que recibimos nos indicas que tu patrimonio actual es de <span style="">$ng_worth</span>.   ¿Podrías indicarnos el % de distribución de dicho patrimonio en las siguientes 3 categorías?</p>

			                    <br>

			                    <span style="margin-top: 0; padding-left: 30px; color: #072146;"><b>• Financiero:</b></span><br>
			                    <span style="margin-top: 0; padding-left: 30px; color: #072146;"><b>• Mobiliario:</b></span><br>
			                    <span style="margin-top: 0; padding-left: 30px; color: #072146;"><b>• Inmobiliario:</b></span>

			                    <br><br>

			                    <p>Gracias por tu colaboración,</p>

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
			            <tr style="color:#6754b8;">
			                <td style="padding-left: 15px; padding-bottom: 50px; padding-top: 50px; padding-right: 25px;">
			                    <p>Hello $first_name,</p>

			                    <p>Thank you for your interest in <span style="color: #6754b8;">BBVA New Gen</span>.</p>

			                    <p>We confirm that we have successfully received your opening request.</p>

			                    <p style="margin-bottom: 0;">To continue with the process our compliance team requires the following additional information:</p>

			                    <br>

			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>Country of tax residence:</u></b></p>

			                    <br>

			                    <p>In the documents received, you indicate that your country of tax residence is <span style="">$ng_tax_country1</span>.  As this is a country other than your country of nationality and/or domicile, we require your residence permit from this country.</p>

			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>Distribution of your wealth:</u></b></p>

			                    <br>

			                    <p>In the documentation we received, you indicate that your current net worth is <span style="">$ng_worth</span>. Could you please tell us the percentage distribution of that net worth in the following three categories?</p>

			                    <br>

			                    <span style="margin-top: 0; padding-left: 30px; color: #072146;"><b>• Financial:</b></span><br>
			                    <span style="margin-top: 0; padding-left: 30px; color: #072146;"><b>• Assets (Cars, ships, airplanes, etc):</b></span><br>
			                    <span style="margin-top: 0; padding-left: 30px; color: #072146;"><b>• Real Estate:</b></span>

			                    <br><br>

			                    <p>Thank you for your understanding,</p>

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

	public static function getTemplate6(array $prospectEmail, string $host) {
		// enlace images
		$host = ($host == "prod") ? "https://www.bbva.ch" : "https://www.bbva.ch";
		$language = $prospectEmail['language'];
		$first_name = $prospectEmail['first_name'];
		$ng_tax_country1 = $prospectEmail['ng_tax_country1'];
		$ng_worth = $prospectEmail['ng_worth'];
		$company = $prospectEmail['company'];

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
			            <tr style="color:#6754b8;">
			                <td style="padding-left: 15px; padding-bottom: 50px; padding-top: 50px; padding-right: 25px;">
			                    <p>Hola $first_name,</p>

			                    <p>Gracias por tu interés en <span style="color: #6754b8;">BBVA New Gen</span>.</p>

			                    <p>Te confirmamos que hemos recibido tu solicitud de apertura con éxito.</p>

			                    <p style="margin-bottom: 0;">Para continuar con el proceso nuestro departamento de cumplimiento requiere la siguiente información adicional:</p>

			                    <br>

			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>País de residencia fiscal:</u></b></p>

			                    <p>En los documentos recibidos nos indicas que tu país de residencia fiscal es <span style="">$ng_tax_country1</span>. Al tratarse de un país distinto de tu nacionalidad y/o domicilio necesitamos tu permiso de residencia en este país.</p>

			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>Origen del patrimonio:</u></b></p>

			                    <p>En la documentación que recibimos nos indicas que tu patrimonio actual es de <span style="">$ng_worth</span>.   ¿Podrías indicarnos el % de distribución de dicho patrimonio en las siguientes 3 categorías?</p>


			                    <span style="margin-top: 0; padding-left: 30px; color: #072146;"><b>• Financiero:</b></span><br>
			                    <span style="margin-top: 0; padding-left: 30px; color: #072146;"><b>• Mobiliario:</b></span><br>
			                    <span style="margin-top: 0; padding-left: 30px; color: #072146;"><b>• Inmobiliario:</b></span>

			                    <br><br>

			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>Actividad profesional:</u></b></p>

			                    <p>En la documentación recibida nos indicas que eres empleado en la compañía <span style="">$company</span>. Para esta sección necesitaríamos una copia de tu nomina donde podamos corroborar el nivel de ingreso indicado en el formulario, por favor, <u>¿Podrías enviarnos por este medio una copia de tu nómina?</u></p>

			                    <br><br>

			                    <p>Gracias por tu colaboración,</p>

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
			            <tr style="color:#6754b8;">
			                <td style="padding-left: 15px; padding-bottom: 50px; padding-top: 50px; padding-right: 25px;">
			                    <p>Hello $first_name,</p>

			                    <p>Thank you for your interest in <span style="color: #6754b8;">BBVA New Gen</span>.</p>

			                    <p>We confirm that we have successfully received your opening request.</p>

			                    <p style="margin-bottom: 0;">To continue with the process our compliance team requires the following additional information:</p>

			                    <br>

			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>Country of tax residence:</u></b></p>

			                    <p>In the documentation we received, you indicate that your current net worth is <span style="">$ng_tax_country1</span>. As this is a country other than your country of nationality and/or domicile, we require your residence permit from this country.</p>

			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>Distribution of your wealth:</u></b></p>


			                    <p>In the documentation we received, you indicate that your current net worth is <span style="">$ng_worth</span>. Could you please tell us the percentage distribution of that net worth in the following three categories?</p>


			                    <span style="margin-top: 0; padding-left: 30px; color: #072146;"><b>• Financial:</b></span><br>
			                    <span style="margin-top: 0; padding-left: 30px; color: #072146;"><b>• Assets (Cars, ships, airplanes, etc):</b></span><br>
			                    <span style="margin-top: 0; padding-left: 30px; color: #072146;"><b>• Real Estate:</b></span>

			                    <br><br>

			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>Professional activity:</u></b></p>

			                    <p>In the documentation received, you indicate that currently you are an employee of the company <span style="">$company</span>.  For this section, we would need a copy of your payslip where we can verify the level of income indicated in the online form, please, <u>Could you send us a copy of your payslip?</u></p>

			                    <br><br>

			                    <p>Thank you for your understanding,</p>

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

	public static function getTemplate7(array $prospectEmail, string $host) {
		// enlace images
		$host = ($host == "prod") ? "https://www.bbva.ch" : "https://www.bbva.ch";
		$language = $prospectEmail['language'];
		$first_name = $prospectEmail['first_name'];
		$ng_tax_country1 = $prospectEmail['ng_tax_country1'];
		$ng_worth = $prospectEmail['ng_worth'];
		$company = $prospectEmail['company'];

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
			            <tr style="color:#6754b8;">
			                <td style="padding-left: 15px; padding-bottom: 50px; padding-top: 50px; padding-right: 25px;">
			                    <p>Hola $first_name,</p>

			                    <p>Gracias por tu interés en <span style="color: #6754b8;">BBVA New Gen</span>.</p>

			                    <p>Te confirmamos que hemos recibido tu solicitud de apertura con éxito.</p>

			                    <p style="margin-bottom: 0;">Para continuar con el proceso nuestro departamento de cumplimiento requiere la siguiente información adicional:</p>

			                    <br>

			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>País de residencia fiscal:</u></b></p>

			                    <p>En los documentos recibidos nos indicas que tu país de residencia fiscal es <span style="">$ng_tax_country1</span>. Al tratarse de un país distinto de tu nacionalidad y/o domicilio necesitamos tu permiso de residencia en este país.</p>

			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>Origen del patrimonio:</u></b></p>

			                    <p>En la documentación que recibimos nos indicas que tu patrimonio actual es de <span style="">$ng_worth</span>.   ¿Podrías indicarnos el % de distribución de dicho patrimonio en las siguientes 3 categorías?</p>


			                    <span style="margin-top: 0; padding-left: 30px; color: #072146;"><b>• Financiero:</b></span><br>
			                    <span style="margin-top: 0; padding-left: 30px; color: #072146;"><b>• Mobiliario:</b></span><br>
			                    <span style="margin-top: 0; padding-left: 30px; color: #072146;"><b>• Inmobiliario:</b></span>

			                    <br><br>

			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>Actividad profesional:</u></b></p>

			                    <p>En la documentación recibida nos indicas que eres autónomo en la compañía <span style="">$company</span>. Para esta sección necesitaríamos una prueba de tu actividad profesional como autónomo donde podamos verificar tus ingresos anuales aproximados. Por favor, <u>¿Podrías enviarnos por este medio una copia la última declaración jurada presentada o el alta como autónomo?</u></p>

			                    <br><br>

			                    <p>Gracias por tu colaboración,</p>

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
			            <tr style="color:#6754b8;">
			                <td style="padding-left: 15px; padding-bottom: 50px; padding-top: 50px; padding-right: 25px;">
			                    <p>Hello $first_name,</p>

			                    <p>Thank you for your interest in <span style="color: #6754b8;">BBVA New Gen</span>.</p>

			                    <p>We confirm that we have successfully received your opening request.</p>

			                    <p style="margin-bottom: 0;">To continue with the process our compliance team requires the following additional information:</p>

			                    <br>

			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>Country of tax residence:</u></b></p>

			                    <p>In the documents received, you indicate that your country of tax residence is <span style="">$ng_tax_country1</span>. As this is a country other than your country of nationality and/or domicile, we require your residence permit from this country.</p>

			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>Distribution of your wealth:</u></b></p>


			                    <p>In the documentation we received, you indicate that your current net worth is <span style="">$ng_worth</span>. Could you please tell us the percentage distribution of that net worth in the following three categories?</p>


			                    <span style="margin-top: 0; padding-left: 30px; color: #072146;"><b>• Financial:</b></span><br>
			                    <span style="margin-top: 0; padding-left: 30px; color: #072146;"><b>• Assets (Cars, ships, airplanes, etc):</b></span><br>
			                    <span style="margin-top: 0; padding-left: 30px; color: #072146;"><b>• Real Estate:</b></span>

			                    <br><br>
			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>Professional activity:</u></b></p>

			                    <p>In the documentation received, you indicate that you are self-employed in the company <span style="">$company</span>.  For this section, we request you to provide proof of your professional activity as a self-employed person where we can verify your approximate annual income. Please, <u>could you send us a copy of the last tax declaration submitted or the registration as self-employed?</u></p>

			                    <br><br>

			                    <p>Thank you for your understanding,</p>

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

	public static function getTemplate8(array $prospectEmail, string $host) {
		// enlace images
		$host = ($host == "prod") ? "https://www.bbva.ch" : "https://www.bbva.ch";
		$language = $prospectEmail['language'];
		$first_name = $prospectEmail['first_name'];
		$ng_tax_country1 = $prospectEmail['ng_tax_country1'];
		$ng_worth = $prospectEmail['ng_worth'];
		$company = $prospectEmail['company'];

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
			            <tr style="color:#6754b8;">
			                <td style="padding-left: 15px; padding-bottom: 50px; padding-top: 50px; padding-right: 25px;">
			                    <p>Hola $first_name,</p>

			                    <p>Gracias por tu interés en <span style="color: #6754b8;">BBVA New Gen</span>.</p>

			                    <p>Te confirmamos que hemos recibido tu solicitud de apertura con éxito.</p>

			                    <p style="margin-bottom: 0;">Para continuar con el proceso nuestro departamento de cumplimiento requiere la siguiente información adicional:</p>

			                    <br>

			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>País de residencia fiscal:</u></b></p>

			                    <p>En los documentos recibidos nos indicas que tu país de residencia fiscal es <span style="">$ng_tax_country1</span>. Al tratarse de un país distinto de tu nacionalidad y/o domicilio necesitamos tu permiso de residencia en este país.</p>

			                    <br>

			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>Actividad profesional:</u></b></p>

			                    <p>En la documentación recibida nos indicas que eres empleado en la compañía <span style="">$company</span>.  Para esta sección necesitaríamos una copia de tu nomina donde podamos corroborar el nivel de ingreso indicado en el formulario, por favor, <u>¿Podrías enviarnos por este medio una copia de tu nómina?</u></p>

			                    <br><br>

			                    <p>Gracias por tu colaboración,</p>

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
			            <tr style="color:#6754b8;">
			                <td style="padding-left: 15px; padding-bottom: 50px; padding-top: 50px; padding-right: 25px;">
			                    <p>Hello $first_name,</p>

			                    <p>Thank you for your interest in <span style="color: #6754b8;">BBVA New Gen</span>.</p>

			                    <p>We confirm that we have successfully received your opening request.</p>

			                    <p style="margin-bottom: 0;">To continue with the process our compliance team requires the following additional information:</p>

			                    <br>

			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>Country of tax residence:</u></b></p>

			                    <p>In the documents received, you indicate that your country of tax residence is <span style="">$ng_tax_country1</span>. As this is a country other than your country of nationality and/or domicile, we require your residence permit from this country. </p>

			                    <br>

			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>Professional activity:</u></b></p>

			                    <p>In the documentation received, you indicate that currently you are an employee of the company <span style="">$company</span>.  For this section, we would need a copy of your payslip where we can verify the level of income indicated in the online form, please, <u>Could you send us a copy of your payslip?</u></p>

			                    <br><br>

			                    <p>Thank you for your understanding,</p>

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

	public static function getTemplate9(array $prospectEmail, string $host) {
		// enlace images
		$host = ($host == "prod") ? "https://www.bbva.ch" : "https://www.bbva.ch";
		$language = $prospectEmail['language'];
		$first_name = $prospectEmail['first_name'];
		$ng_tax_country1 = $prospectEmail['ng_tax_country1'];
		$ng_worth = $prospectEmail['ng_worth'];
		$company = $prospectEmail['company'];

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
			            <tr style="color:#6754b8;">
			                <td style="padding-left: 15px; padding-bottom: 50px; padding-top: 50px; padding-right: 25px;">
			                    <p>Hola $first_name,</p>

			                    <p>Gracias por tu interés en <span style="color: #6754b8;">BBVA New Gen</span>.</p>

			                    <p>Te confirmamos que hemos recibido tu solicitud de apertura con éxito.</p>

			                    <p style="margin-bottom: 0;">Para continuar con el proceso nuestro departamento de cumplimiento requiere la siguiente información adicional:</p>

			                    <br>

			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>País de residencia fiscal:</u></b></p>

			                    <p>En los documentos recibidos nos indicas que tu país de residencia fiscal es <span style="">$ng_tax_country1</span>. Al tratarse de un país distinto de tu nacionalidad y/o domicilio necesitamos tu permiso de residencia en este país.</p>

			                    <br>

			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>Actividad profesional:</u></b></p>

			                    <p>En la documentación recibida nos indicas que eres autónomo en la compañía <span style="">$company</span>. Para esta sección necesitaríamos una prueba de tu actividad profesional como autónomo donde podamos verificar tus ingresos anuales aproximados. Por favor, <u> ¿Podrías enviarnos por este medio una copia la última declaración jurada presentada o el alta como autónomo?</u></p>

			                    <br><br>

			                    <p>Gracias por tu colaboración,</p>

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
			            <tr style="color:#6754b8;">
			                <td style="padding-left: 15px; padding-bottom: 50px; padding-top: 50px; padding-right: 25px;">
			                    <p>Hello $first_name,</p>

			                    <p>Thank you for your interest in <span style="color: #6754b8;">BBVA New Gen</span>.</p>

			                    <p>We confirm that we have successfully received your opening request.</p>

			                    <p style="margin-bottom: 0;">To continue with the process our compliance team requires the following additional information:</p>

			                    <br>

			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>Country of tax residence:</u></b></p>

			                    <p>In the documents received, you indicate that your country of tax residence is <span style="">$ng_tax_country1</span>. As this is a country other than your country of nationality and/or domicile, we require your residence permit from this country. </p>

			                    <br>

			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>Professional activity:</u></b></p>

			                    <p>In the documentation received, you indicate that you are self-employed in the company <span style="">$company</span>. For this section, we request you to provide proof of your professional activity as a self-employed person where we can verify your approximate annual income. Please, <u>could you send us a copy of the last tax declaration submitted or the registration as selfemployed?</u></p>

			                    <br><br>

			                    <p>Thank you for your understanding,</p>

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

	public static function getTemplate10(array $prospectEmail, string $host) {
		// enlace images
		$host = ($host == "prod") ? "https://www.bbva.ch" : "https://www.bbva.ch";
		$language = $prospectEmail['language'];
		$first_name = $prospectEmail['first_name'];
		$ng_tax_country1 = $prospectEmail['ng_tax_country1'];
		$ng_worth = $prospectEmail['ng_worth'];
		$company = $prospectEmail['company'];

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
			            <tr style="color:#6754b8;">
			                <td style="padding-left: 15px; padding-bottom: 50px; padding-top: 50px; padding-right: 25px;">
			                    <p>Hola $first_name,</p>

			                    <p>Gracias por tu interés en <span style="color: #6754b8;">BBVA New Gen</span>.</p>

			                    <p>Te confirmamos que hemos recibido tu solicitud de apertura con éxito.</p>

			                    <p style="margin-bottom: 0;">Para continuar con el proceso nuestro departamento de cumplimiento requiere la siguiente información adicional:</p>

			                    <br>

			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>Origen del patrimonio:</u></b></p>

			                    <p>En la documentación que recibimos nos indicas que tu patrimonio actual es de <span style="">$ng_worth</span>. ¿Podrías indicarnos el % de distribución de dicho patrimonio en las siguientes 3 categorías?</p>


			                    <span style="margin-top: 0; padding-left: 30px; color: #072146;"><b>• Financiero:</b></span><br>
			                    <span style="margin-top: 0; padding-left: 30px; color: #072146;"><b>• Mobiliario:</b></span><br>
			                    <span style="margin-top: 0; padding-left: 30px; color: #072146;"><b>• Inmobiliario:</b></span>

			                    <br><br>

			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>Actividad profesional:</u></b></p>

			                    <p>En la documentación recibida nos indicas que eres empleado en la compañía <span style="">$company</span>. Para esta sección necesitaríamos una copia de tu nomina donde podamos corroborar el nivel de ingreso indicado en el formulario, por favor, <u>¿Podrías enviarnos por este medio una copia de tu nómina?</u></p>

			                    <br><br>

			                    <p>Gracias por tu colaboración,</p>

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
			            <tr style="color:#6754b8;">
			                <td style="padding-left: 15px; padding-bottom: 50px; padding-top: 50px; padding-right: 25px;">
			                    <p>Hello $first_name,</p>

			                    <p>Thank you for your interest in <span style="color: #6754b8;">BBVA New Gen</span>.</p>

			                    <p>We confirm that we have successfully received your opening request.</p>

			                    <p style="margin-bottom: 0;">To continue with the process our compliance team requires the following additional information:</p>

			                    <br>

			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>Distribution of your wealth:</u></b></p>


			                    <p>In the documentation we received, you indicate that your current net worth is  <span style="">$ng_worth</span>.  Could you please tell us the percentage distribution of that net worth in the following three categories?</p>


			                    <span style="margin-top: 0; padding-left: 30px; color: #072146;"><b>• Financial:</b></span><br>
			                    <span style="margin-top: 0; padding-left: 30px; color: #072146;"><b>• Assets (Cars, ships, airplanes, etc):</b></span><br>
			                    <span style="margin-top: 0; padding-left: 30px; color: #072146;"><b>• Real Estate:</b></span>

			                    <br><br>

			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>Professional activity:</u></b></p>

			                    <p>In the documentation received, you indicate that currently you are an employee of the company <span style="">$company</span>. For this section, we would need a copy of your payslip where we can verify the level of income indicated in the online form, please,  <u>Could you send us a copy of your payslip?</u></p>

			                    <br><br>

			                    <p>Thank you for your understanding,</p>

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

	public static function getTemplate11(array $prospectEmail, string $host) {
		// enlace images
		$host = ($host == "prod") ? "https://www.bbva.ch" : "https://www.bbva.ch";
		$language = $prospectEmail['language'];
		$first_name = $prospectEmail['first_name'];
		$ng_tax_country1 = $prospectEmail['ng_tax_country1'];
		$ng_worth = $prospectEmail['ng_worth'];
		$company = $prospectEmail['company'];

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
			            <tr style="color:#6754b8;">
			                <td style="padding-left: 15px; padding-bottom: 50px; padding-top: 50px; padding-right: 25px;">
			                    <p>Hola $first_name,</p>

			                    <p>Gracias por tu interés en <span style="color: #6754b8;">BBVA New Gen</span>.</p>

			                    <p>Te confirmamos que hemos recibido tu solicitud de apertura con éxito.</p>

			                    <p style="margin-bottom: 0;">Para continuar con el proceso nuestro departamento de cumplimiento requiere la siguiente información adicional:</p>

			                    <br>

			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>Origen del patrimonio:</u></b></p>

			                    <p>En la documentación que recibimos nos indicas que tu patrimonio actual es de <span style="">$ng_worth</span>. ¿Podrías indicarnos el % de distribución de dicho patrimonio en las siguientes 3 categorías?</p>


			                    <span style="margin-top: 0; padding-left: 30px; color: #072146;"><b>• Financiero:</b></span><br>
			                    <span style="margin-top: 0; padding-left: 30px; color: #072146;"><b>• Mobiliario:</b></span><br>
			                    <span style="margin-top: 0; padding-left: 30px; color: #072146;"><b>• Inmobiliario:</b></span>

			                    <br><br>

			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>Actividad profesional:</u></b></p>

			                    <p>En la documentación recibida nos indicas que eres autónomo en la compañía <span style="">$company</span>.  Para esta sección necesitaríamos una prueba de tu actividad profesional como autónomo donde podamos verificar tus ingresos anuales aproximados. Por favor, <u>¿Podrías enviarnos por este medio una copia la última declaración jurada presentada o el alta como autónomo?</u></p>

			                    <br><br>

			                    <p>Gracias por tu colaboración,</p>

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
			            <tr style="color:#6754b8;">
			                <td style="padding-left: 15px; padding-bottom: 50px; padding-top: 50px; padding-right: 25px;">
			                    <p>Hello $first_name,</p>

			                    <p>Thank you for your interest in <span style="color: #6754b8;">BBVA New Gen</span>.</p>

			                    <p>We confirm that we have successfully received your opening request.</p>

			                    <p style="margin-bottom: 0;">To continue with the process our compliance team requires the following additional information:</p>

			                    <br>

			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>Distribution of your wealth:</u></b></p>


			                    <p>In the documentation we received, you indicate that your current net worth is  <span style="">$ng_worth</span>.  Could you please tell us the percentage distribution of that net worth in the following three categories?</p>


			                    <span style="margin-top: 0; padding-left: 30px; color: #072146;"><b>• Financial:</b></span><br>
			                    <span style="margin-top: 0; padding-left: 30px; color: #072146;"><b>• Assets (Cars, ships, airplanes, etc):</b></span><br>
			                    <span style="margin-top: 0; padding-left: 30px; color: #072146;"><b>• Real Estate:</b></span>

			                    <br><br>

			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>Professional activity:</u></b></p>

			                    <p>In the documentation received, you indicate that you are self-employed in the company <span style="">$company</span>. For this section, we request you to provide proof of your professional activity as a self-employed person where we can verify your approximate annual income. Please, <u> could you send us a copy of the last tax declaration submitted or the registration as selfemployed?</u></p>

			                    <br><br>

			                    <p>Thank you for your understanding,</p>

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

	public static function getTemplate12(array $prospectEmail, string $host) {
		// enlace images
		$host = ($host == "prod") ? "https://www.bbva.ch" : "https://www.bbva.ch";
		$language = $prospectEmail['language'];
		$first_name = $prospectEmail['first_name'];
		$ng_tax_country1 = $prospectEmail['ng_tax_country1'];
		$ng_worth = $prospectEmail['ng_worth'];
		$company = $prospectEmail['company'];

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
			            <tr style="color:#6754b8;">
			                <td style="padding-left: 15px; padding-bottom: 50px; padding-top: 50px; padding-right: 25px;">
			                    <p>Hola $first_name,</p>

			                    <p>Gracias por tu interés en <span style="color: #6754b8;">BBVA New Gen</span>.</p>

			                    <p>Te confirmamos que hemos recibido tu solicitud de apertura con éxito.</p>

			                    <p style="margin-bottom: 0;">Para continuar con el proceso nuestro departamento de cumplimiento requiere la siguiente información adicional:</p>

			                    <br>

			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>Actividad profesional:</u></b></p>

			                    <p>En la documentación recibida nos indicas que actualmente estas jubilado. Para esta sección necesitaríamos una prueba donde podamos verificar tus ingresos anuales aproximados. Por favor, <u>¿Podrías enviarnos por este medio una copia la última declaración jurada presentada?</u></p>

			                    <br><br>

			                    <p>Gracias por tu colaboración,</p>

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
			            <tr style="color:#6754b8;">
			                <td style="padding-left: 15px; padding-bottom: 50px; padding-top: 50px; padding-right: 25px;">
			                    <p>Hello $first_name,</p>

			                    <p>Thank you for your interest in <span style="color: #6754b8;">BBVA New Gen</span>.</p>

			                    <p>We confirm that we have successfully received your opening request.</p>

			                    <p style="margin-bottom: 0;">To continue with the process our compliance team requires the following additional information:</p>

			                    <br>

			                    <p style="margin-top: 0; padding-left: 30px; color: #072146;"><b>✔ <u>Profesional activity:</u></b></p>

			                    <p>In the documentation received, you indicate that you are retired. For this section, we request you to provide proof of where we can verify your approximate annual income. Please, <u>could you send us a copy of the last tax declaration submitted?</u></p>

			                    <br><br>

			                    <p>Thank you for your understanding,</p>

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

}