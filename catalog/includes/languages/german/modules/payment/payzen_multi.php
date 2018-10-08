<?php
/**
 * PayZen V2-Payment Module version 1.3.0 for osCommerce 2.3.x. Support contact : support@payzen.eu.
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
 * @category  Payment
 * @package   Payzen
 * @author    Lyra Network (http://www.lyra-network.com/)
 * @copyright 2014-2018 Lyra Network and contributors
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html  GNU General Public License (GPL v2)
 */

// administration interface - multi payment settings
define('MODULE_PAYMENT_PAYZEN_OPTIONS_TITLE', "Zahlungsarten");
define('MODULE_PAYMENT_PAYZEN_OPTIONS_DESC', "Auf « Hinzufügen » klicken, um eine oder mehrere Zahlungsarten zu konfigurieren. Für weitere Informationen, Sie bitte der Moduldokumentation. <b>Bitte speichern Sie Ihre Änderungen durch Klicken auf « Speichern ».</b>");
define('MODULE_PAYMENT_PAYZEN_OPTIONS_LABEL', "Kennzeichnung");
define('MODULE_PAYMENT_PAYZEN_OPTIONS_MIN_AMOUNT', "Mindestbetrag");
define('MODULE_PAYMENT_PAYZEN_OPTIONS_MAX_AMOUNT', "Höchstbetrag");
define('MODULE_PAYMENT_PAYZEN_OPTIONS_CONTRACT', "Vertrag");
define('MODULE_PAYMENT_PAYZEN_OPTIONS_COUNT', "Nummer");
define('MODULE_PAYMENT_PAYZEN_OPTIONS_PERIOD', "Zeitraum");
define('MODULE_PAYMENT_PAYZEN_OPTIONS_FIRST', "1. Zahlung");
define('MODULE_PAYMENT_PAYZEN_OPTIONS_ADD', "Hinzufügen");
define('MODULE_PAYMENT_PAYZEN_OPTIONS_DELETE', "Löschen");

// multi payment catalog messages
define('MODULE_PAYMENT_PAYZEN_MULTI_TITLE', "PayZen - Ratenzahlung mit EC-/Kreditkarte");
define('MODULE_PAYMENT_PAYZEN_MULTI_SHORT_TITLE', "PayZen - Ratenzahlung");

define('MODULE_PAYMENT_PAYZEN_MULTI_WARNING', "ATTENTION: The payment in installments feature activation is subject to the prior agreement of Société Générale.<br />If you enable this feature while you have not the associated option, an error 07 - PAYMENT_CONFIG will occur and the buyer will not be able to pay.");
