<?php
/**
 * Copyright © Lyra Network.
 * This file is part of PayZen plugin for osCommerce. See COPYING.md for license details.
 *
 * @author    Lyra Network (https://www.lyra-network.com/)
 * @copyright Lyra Network
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL v2)
 */

global $payzen_plugin_features;

// Administration interface - informations.
define('MODULE_PAYMENT_PAYZEN_MODULE_INFORMATION', "MODULINFORMATIONEN");
define('MODULE_PAYMENT_PAYZEN_DEVELOPED_BY', "Entwickelt von: ");
define('MODULE_PAYMENT_PAYZEN_CONTACT_EMAIL', "Kontakt: ");
define('MODULE_PAYMENT_PAYZEN_CONTRIB_VERSION', "Modulversion: ");
define('MODULE_PAYMENT_PAYZEN_GATEWAY_VERSION', "Plattformversion: ");
define('MODULE_PAYMENT_PAYZEN_IPN_URL_TITLE', "Benachrichtigung-URL");
define('MODULE_PAYMENT_PAYZEN_IPN_URL_DESC', "URL, die Sie in Ihre PayZen Back Office kopieren sollen > Einstellung > Regeln der Benachrichtigungen.");

// Administration interface - module settings.
define('MODULE_PAYMENT_PAYZEN_STATUS_TITLE', "Aktiviert");
define('MODULE_PAYMENT_PAYZEN_STATUS_DESC', "Aktiviert / Deaktiviert dieses Zahlungsmodus.");
define('MODULE_PAYMENT_PAYZEN_SORT_ORDER_TITLE', "Reihenfolge");
define('MODULE_PAYMENT_PAYZEN_SORT_ORDER_DESC', "In der Liste der Zahlungsmittel.");
define('MODULE_PAYMENT_PAYZEN_ZONE_TITLE', "Zahlungsraum");
define('MODULE_PAYMENT_PAYZEN_ZONE_DESC', "Ist ein Zahlungsraum ausgewählt, so wird diese Zahlungsart nur für diesen verfügbar sein.");

// Administration interface - gateway settings.
define('MODULE_PAYMENT_PAYZEN_SITE_ID_TITLE', "Shop ID");
define('MODULE_PAYMENT_PAYZEN_SITE_ID_DESC', "Die Kennung von PayZen bereitgestellt.");
define('MODULE_PAYMENT_PAYZEN_KEY_TEST_TITLE', "Schlüssel im Testbetrieb");
define('MODULE_PAYMENT_PAYZEN_KEY_TEST_DESC', "Schlüssel, das von PayZen zu Testzwecken bereitgestellt wird (im PayZen Back Office verfügbar).");
define('MODULE_PAYMENT_PAYZEN_KEY_PROD_TITLE', "Schlüssel im Produktivbetrieb");
define('MODULE_PAYMENT_PAYZEN_KEY_PROD_DESC', "Von PayZen bereitgestelltes Schlüssel (im PayZen Back Office verfügbar, nachdem der Produktionsmodus aktiviert wurde).");
define('MODULE_PAYMENT_PAYZEN_CTX_MODE_TITLE', "Modus");
define('MODULE_PAYMENT_PAYZEN_CTX_MODE_DESC', "Kontextmodus dieses Moduls.");
define('MODULE_PAYMENT_PAYZEN_SIGN_ALGO_TITLE', "Signaturalgorithmus");
define('MODULE_PAYMENT_PAYZEN_SIGN_ALGO_DESC', "Algorithmus zur Berechnung der Zahlungsformsignatur. Der ausgewählte Algorithmus muss derselbe sein, wie er im PayZen Back Office." . (! $payzen_plugin_features['shatwo'] ? "Der HMAC-SHA-256-Algorithmus sollte nicht aktiviert werden, wenn er noch nicht im PayZen Back Office verfügbar ist." : ''));
define('MODULE_PAYMENT_PAYZEN_PLATFORM_URL_TITLE', "Plattform-URL");
define('MODULE_PAYMENT_PAYZEN_PLATFORM_URL_DESC', "Link zur Bezahlungsplattform.");

// Administration interface - payment settings.
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_TITLE', "Standardsprache");
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_DESC', "Wählen Sie bitte die Spracheinstellung der Zahlungsseiten aus.");
define('MODULE_PAYMENT_PAYZEN_AVAILABLE_LANGUAGES_TITLE', "Verfügbare Sprachen");
define('MODULE_PAYMENT_PAYZEN_AVAILABLE_LANGUAGES_DESC', "Verfügbare Sprachen der Zahlungsseite. Nichts auswählen, um die Einstellung der Zahlungsplattform zu benutzen.");
define('MODULE_PAYMENT_PAYZEN_CAPTURE_DELAY_TITLE', "Einzugsfrist");
define('MODULE_PAYMENT_PAYZEN_CAPTURE_DELAY_DESC', "Anzahl der Tage bis zum Einzug der Zahlung (Einstellung über Ihr PayZen Back Office).");
define('MODULE_PAYMENT_PAYZEN_VALIDATION_MODE_TITLE', "Bestätigungsmodus");
define('MODULE_PAYMENT_PAYZEN_VALIDATION_MODE_DESC', "Bei manueller Eingabe müssen Sie Zahlungen manuell in Ihr PayZen Back Office bestätigen.");
define('MODULE_PAYMENT_PAYZEN_PAYMENT_CARDS_TITLE', "Kartentypen");
define('MODULE_PAYMENT_PAYZEN_PAYMENT_CARDS_DESC', "Wählen Sie die zur Zahlung verfügbaren Kartentypen aus. Nichts auswählen, um die Einstellungen der Plattform zu verwenden.");
define('MODULE_PAYMENT_PAYZEN_3DS_MIN_AMOUNT_TITLE', "3DS deaktivieren");
define('MODULE_PAYMENT_PAYZEN_3DS_MIN_AMOUNT_DESC', "Betrag, unter dem 3DS deaktiviert wird. Muss für die Option Selektives 3DS freigeschaltet sein. Weitere Informationen finden Sie in der Moduldokumentation.");

// Administration interface - amount restrictions settings.
define('MODULE_PAYMENT_PAYZEN_MIN_AMOUNT_TITLE', "Mindestbetrag");
define('MODULE_PAYMENT_PAYZEN_MIN_AMOUNT_DESC', "Mindestbetrag für die Nutzung dieser Zahlungsweise.");
define('MODULE_PAYMENT_PAYZEN_MAX_AMOUNT_TITLE', "Höchstbetrag");
define('MODULE_PAYMENT_PAYZEN_MAX_AMOUNT_DESC', "Höchstbetrag für die Nutzung dieser Zahlungsweise.");

// Administration interface - back to store settings.
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
define('MODULE_PAYMENT_PAYZEN_ORDER_STATUS_DESC', "Status der Bestellungen bei erfolgreicher Zahlung");

// Administration interface - misc constants.
define('MODULE_PAYMENT_PAYZEN_VALUE_0', "Deaktiviert");
define('MODULE_PAYMENT_PAYZEN_VALUE_1', "Aktiviert");

define('MODULE_PAYMENT_PAYZEN_VALIDATION_DEFAULT', "PayZen Back Office Konfiguration");
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

define('MODULE_PAYMENT_PAYZEN_REDIRECT_SUCCESS_MESSAGE', "Weiterleitung zum Shop in Kürze...");
define('MODULE_PAYMENT_PAYZEN_REDIRECT_ERROR_MESSAGE', "Weiterleitung zum Shop in Kürze...");

// Catalog messages.
define('MODULE_PAYMENT_PAYZEN_TECHNICAL_ERROR', "Ein Fehler ist bei dem Zahlungsvorgang unterlaufen.");
define('MODULE_PAYMENT_PAYZEN_PAYMENT_ERROR', "Ihre Zahlung wurde abgelehnt. Bitte führen Sie den Bestellvorgang erneut durch.");
define('MODULE_PAYMENT_PAYZEN_CHECK_URL_WARN', "Die automatische Bestätigung hat nicht funktioniert. Haben Sie die Server URL im PayZen Back Office richtig eingestellt?");
define('MODULE_PAYMENT_PAYZEN_CHECK_URL_WARN_DETAIL', "Um die Problematif zu verstehen, benutzen Sie bitte die Benutzerhilfe des Moduls:<br />&nbsp;&nbsp;&nbsp;- Kapitel « Aufmerksam lesen »<br />&nbsp;&nbsp;&nbsp;- Kapitel « Einstellung der Server URL ».");
define('MODULE_PAYMENT_PAYZEN_GOING_INTO_PROD_INFO', "<b>UMSTELLUNG AUF PRODUKTIONSUMFELD:</b> Sie möchten wissen, wie Sie auf Produktionsumfeld umstellen können, bitte lesen Sie die Kapitel « Weiter zur Testphase » und « Verschieben des Shops in den Produktionsumfeld » in der Dokumentation des Moduls.");

// Single payment catalog messages.
define('MODULE_PAYMENT_PAYZEN_STD_TITLE', "PayZen - Zahlung mit EC-/Kreditkarte");
