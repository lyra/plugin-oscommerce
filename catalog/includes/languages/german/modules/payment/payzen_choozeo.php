<?php
/**
 * Copyright © Lyra Network.
 * This file is part of PayZen plugin for osCommerce. See COPYING.md for license details.
 *
 * @author    Lyra Network (https://www.lyra-network.com/)
 * @copyright Lyra Network
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL v2)
 */

// administration interface - amount restriction error message
define('MODULE_PAYMENT_PAYZEN_NULL_MIN_AMOUNT_ERROR', "Das Feld « Mindestbetrag » ist Pflicht.");
define('MODULE_PAYMENT_PAYZEN_NULL_MAX_AMOUNT_ERROR', "Das Feld « Höchstbetrag » ist Pflicht.");
define('MODULE_PAYMENT_PAYZEN_INCONSISTENT_AMOUNT_ERROR', "« Mindestbetrag » muss unter « Höchstbetrag » liegen.");
define('MODULE_PAYMENT_PAYZEN_CHOOZEO_OPTIONS_ERROR', "Min and max amounts of payment options must fall within the interval defined by fields « Minimum amount » and « Maximum amount ».");

// administration interface - choozeo payment options
define('MODULE_PAYMENT_PAYZEN_OPTIONS_TITLE', "Zahlungsoptionen");
define('MODULE_PAYMENT_PAYZEN_OPTIONS_DESC', "Definieren Menge Beschränkung für jede Karte.");
define('MODULE_PAYMENT_PAYZEN_OPTIONS_LABEL', "Kennzeichnung");
define('MODULE_PAYMENT_PAYZEN_OPTIONS_MIN_AMOUNT', "Mindestbetrag");
define('MODULE_PAYMENT_PAYZEN_OPTIONS_MAX_AMOUNT', "Höchstbetrag");

// multi payment catalog messages
define('MODULE_PAYMENT_PAYZEN_CHOOZEO_TITLE', "PayZen - Zahlung mit Choozeo");
