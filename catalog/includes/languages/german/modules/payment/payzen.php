<?php
/**
 * PayZen V2-Payment Module version 1.2.0 for osCommerce 2.3.x. Support contact : support@payzen.eu.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * @author    Lyra Network (http://www.lyra-network.com/)
 * @copyright 2014-2017 Lyra Network and contributors
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html  GNU General Public License (GPL v2)
 * @category  payment
 * @package   payzen
 */

// administration interface - informations
define('MODULE_PAYMENT_PAYZEN_MODULE_INFORMATION', "Modulinformationen");
define('MODULE_PAYMENT_PAYZEN_DEVELOPED_BY', "Entwickelt von : ");
define('MODULE_PAYMENT_PAYZEN_CONTACT_EMAIL', "Kontakt: ");
define('MODULE_PAYMENT_PAYZEN_CONTRIB_VERSION', "Modulversion: ");
define('MODULE_PAYMENT_PAYZEN_GATEWAY_VERSION', "Plattformversion: ");
define('MODULE_PAYMENT_PAYZEN_CMS_VERSION', "Getestet mit: ");
define('MODULE_PAYMENT_PAYZEN_SILENT_URL', "Server-URL zur Eintragung in Ihr Shopsystem: <br />");

// administration interface - module settings
define('MODULE_PAYMENT_PAYZEN_STATUS_TITLE', "PayZen-Modul aktivieren");
define('MODULE_PAYMENT_PAYZEN_STATUS_DESC', "Möchten Sie die PayZen-Zahlungsart akzeptieren?");
define('MODULE_PAYMENT_PAYZEN_SORT_ORDER_TITLE', "Anzeigereihenfolge");
define('MODULE_PAYMENT_PAYZEN_SORT_ORDER_DESC', "Anzeigereihenfolge: Von klein nach gross.");
define('MODULE_PAYMENT_PAYZEN_ZONE_TITLE', "Zahlungsraum");
define('MODULE_PAYMENT_PAYZEN_ZONE_DESC', "Ist ein Zahlungsraum ausgewählt, so wird diese Zahlungsart nur für diesen verfügbar sein.");

// administration interface - platform settings
define('MODULE_PAYMENT_PAYZEN_SITE_ID_TITLE', "Shop ID");
define('MODULE_PAYMENT_PAYZEN_SITE_ID_DESC', "Kennung, die von Ihrer Bank bereitgestellt wird.");
define('MODULE_PAYMENT_PAYZEN_KEY_TEST_TITLE', "Zertifikat im Testbetrieb");
define('MODULE_PAYMENT_PAYZEN_KEY_TEST_DESC', "Zertifikat, das von Ihrer Bank zu Testzwecken bereitgestellt wird (im PayZen-System verfügbar).");
define('MODULE_PAYMENT_PAYZEN_KEY_PROD_TITLE', "Zertifikat im Produktivbetrieb");
define('MODULE_PAYMENT_PAYZEN_KEY_PROD_DESC', "Von Ihrer Bank bereitgestelltes Zertifikat (im PayZen-System verfügbar).");
define('MODULE_PAYMENT_PAYZEN_CTX_MODE_TITLE', "Modus");
define('MODULE_PAYMENT_PAYZEN_CTX_MODE_DESC', "Kontextmodus dieses Moduls.");
define('MODULE_PAYMENT_PAYZEN_PLATFORM_URL_TITLE', "Plattform-URL");
define('MODULE_PAYMENT_PAYZEN_PLATFORM_URL_DESC', "Link zur Bezahlungsplattform.");

// administration interface - payment settings
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_TITLE', "Standardsprache");
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_DESC', "Wählen Sie bitte die Spracheinstellung der Zahlungsseiten aus.");
define('MODULE_PAYMENT_PAYZEN_AVAILABLE_LANGUAGES_TITLE', "Verfügbare Sprachen");
define('MODULE_PAYMENT_PAYZEN_AVAILABLE_LANGUAGES_DESC', "Verfügbare Sprachen der Zahlungsseite. Nichts auswählen, um die Einstellung der Zahlungsplattform zu benutzen.");
define('MODULE_PAYMENT_PAYZEN_CAPTURE_DELAY_TITLE', "Einzugsfrist");
define('MODULE_PAYMENT_PAYZEN_CAPTURE_DELAY_DESC', "Anzahl der Tage bis zum Einzug der Zahlung (Einstellung über Ihr PayZen-System).");
define('MODULE_PAYMENT_PAYZEN_VALIDATION_MODE_TITLE', "Bestätigungsmodus");
define('MODULE_PAYMENT_PAYZEN_VALIDATION_MODE_DESC', "Bei manueller Eingabe müssen Sie Zahlungen manuell in Ihrem Banksystem bestätigen.");
define('MODULE_PAYMENT_PAYZEN_PAYMENT_CARDS_TITLE', "Kartentypen");
define('MODULE_PAYMENT_PAYZEN_PAYMENT_CARDS_DESC', "Liste der/die für die Zahlung verfügbare(n) Kartentyp(en), durch Semikolon getrennt.");
define('MODULE_PAYMENT_PAYZEN_3DS_MIN_AMOUNT_TITLE', "Mindestbetrag zur Aktivierung von 3DS");
define('MODULE_PAYMENT_PAYZEN_3DS_MIN_AMOUNT_DESC', "Muss für die Option Selektives 3-D Secure freigeschaltet sein.");

// administration interface - amount restrictions settings
define('MODULE_PAYMENT_PAYZEN_AMOUNT_MIN_TITLE', "Mindestbetrag");
define('MODULE_PAYMENT_PAYZEN_AMOUNT_MIN_DESC', "Mindestbetrag für die Nutzung dieser Zahlungsweise.");
define('MODULE_PAYMENT_PAYZEN_AMOUNT_MAX_TITLE', "Höchstbetrag");
define('MODULE_PAYMENT_PAYZEN_AMOUNT_MAX_DESC', "Höchstbetrag für die Nutzung dieser Zahlungsweise.");

// administration interface - back to store settings
define('MODULE_PAYMENT_PAYZEN_REDIRECT_ENABLED_TITLE', "Automatische Weiterleitung");
define('MODULE_PAYMENT_PAYZEN_REDIRECT_ENABLED_DESC', "Ist diese Einstellung aktiviert, wird der Kunde am Ende des Bezahlvorgangs automatisch auf Ihre Seite weitergeleitet.");
define('MODULE_PAYMENT_PAYZEN_REDIRECT_SUCCESS_TIMEOUT_TITLE', "Zeitbeschränkung Weiterleitung im Erfolgsfall");
define('MODULE_PAYMENT_PAYZEN_REDIRECT_SUCCESS_TIMEOUT_DESC', "Zeitspanne in Sekunden (0-300) bis zur automatischen Weiterleitung des Kunden auf Ihre Seite nach erfolgter Zahlung.");
define('MODULE_PAYMENT_PAYZEN_REDIRECT_SUCCESS_MESSAGE_TITLE', "Weiterleitungs-Nachricht im Erfolgsfall");
define('MODULE_PAYMENT_PAYZEN_REDIRECT_SUCCESS_MESSAGE_DESC', "Nachricht, die nach erfolgter Zahlung und vor der Weiterleitung auf der Plattform angezeigt wird.");
define('MODULE_PAYMENT_PAYZEN_REDIRECT_ERROR_TIMEOUT_TITLE', "Zeitbeschränkung Weiterleitung nach Ablehnung");
define('MODULE_PAYMENT_PAYZEN_REDIRECT_ERROR_TIMEOUT_DESC', "Zeitspanne in Sekunden (0-300) bis zur automatischen Weiterleitung des Kunden auf Ihre Seite nach fehlgeschlagener Zahlung.");
define('MODULE_PAYMENT_PAYZEN_REDIRECT_ERROR_MESSAGE_TITLE', "Weiterleitungs-Nachricht nach Ablehnung");
define('MODULE_PAYMENT_PAYZEN_REDIRECT_ERROR_MESSAGE_DESC', "Nachricht, die nach fehlgeschlagener Zahlung und vor der Weiterleitung auf der Plattform angezeigt wird.");
define('MODULE_PAYMENT_PAYZEN_RETURN_MODE_TITLE', 'Übermittlungs-Modus');
define('MODULE_PAYMENT_PAYZEN_RETURN_MODE_DESC', 'Methode, die zur Übermittlung des Zahlungsergebnisses von der Zahlungsschnittstelle an Ihren Shop verwendet wird.');
define('MODULE_PAYMENT_PAYZEN_ORDER_STATUS_TITLE', "Bestellstatus");
define('MODULE_PAYMENT_PAYZEN_ORDER_STATUS_DESC', "Definiert den Status von Bestellungen, die über die PayZen-Zahlungsart bezahlt wurden.");

// administration interface - misc constants
define('MODULE_PAYMENT_PAYZEN_VALUE_0', "Deaktiviert");
define('MODULE_PAYMENT_PAYZEN_VALUE_1', "Aktiviert");

define('MODULE_PAYMENT_PAYZEN_VALIDATION_DEFAULT', "Einstellung über das PayZen-System");
define('MODULE_PAYMENT_PAYZEN_VALIDATION_0', "Auto");
define('MODULE_PAYMENT_PAYZEN_VALIDATION_1', "Manuell");

define('MODULE_PAYMENT_PAYZEN_LANGUAGE_FRENCH', "Französisch");
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_GERMAN', "Deutsch");
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_ENGLISH', "Englisch");
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_SPANISH', "Spanisch");
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_CHINESE', "Chinesisch");
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_ITALIAN', "Italienisch");
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_JAPANESE', "Japanisch");
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_PORTUGUESE', "Portugiesisch");
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_DUTCH', "Niederländisch");
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_SWEDISH', "Schwedisch");
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_RUSSIAN', "Russisch");
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_POLISH', "Polnisch");
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_TURKISH', "Türkisch");

// catalog messages
define('MODULE_PAYMENT_PAYZEN_TECHNICAL_ERROR', "Ein Fehler ist bei dem Zahlungsvorgang unterlaufen.");
define('MODULE_PAYMENT_PAYZEN_PAYMENT_ERROR', "Ihre Bestellung konnte nicht bestätigt werden.  Die Zahlung wurde nicht angenommen.");
define('MODULE_PAYMENT_PAYZEN_CHECK_URL_WARN', "Die automatische Bestätigung hat nicht funktioniert. Haben Sie die Server URL im Backoffice PayZen richtig eingestellt?");
define('MODULE_PAYMENT_PAYZEN_CHECK_URL_WARN_DETAIL', "Um die Problematif zu verstehen, benutzen Sie bitte die Benutzerhilfe des Moduls:<br />&nbsp;&nbsp;&nbsp;- Kapitel « Aufmerksam lesen »<br />&nbsp;&nbsp;&nbsp;- Kapitel « Einstellung der Server URL ».");
define('MODULE_PAYMENT_PAYZEN_GOING_INTO_PROD_INFO', "<b>UMSTELLUNG AUF PRODUKTIONSUMFELD:</b> Sie möchten wissen, wie Sie auf Produktionsumfeld umstellen können, bitte lesen Sie folgende URL ");

// single payment catalog messages
define('MODULE_PAYMENT_PAYZEN_STD_TITLE', "PayZen - Zahlung mit EC-/Kreditkarte");
