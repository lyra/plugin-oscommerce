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
define('MODULE_PAYMENT_PAYZEN_NULL_MIN_AMOUNT_ERROR', "Le champ « Montant minimum » est obligatoire.");
define('MODULE_PAYMENT_PAYZEN_NULL_MAX_AMOUNT_ERROR', "Le champ « Montant maximum » est obligatoire.");
define('MODULE_PAYMENT_PAYZEN_INCONSISTENT_AMOUNT_ERROR', "« Montant minimum » doit être inférieur à « Montant maximum ».");
define('MODULE_PAYMENT_PAYZEN_CHOOZEO_OPTIONS_ERROR', "Les montants min et max des options de paiement doivent être dans l'intervalle défini par les champs « Montant minimum » et « Montant maximum ».");

// administration interface - multi payment settings
define('MODULE_PAYMENT_PAYZEN_OPTIONS_TITLE', "Options de paiement");
define('MODULE_PAYMENT_PAYZEN_OPTIONS_DESC', "Définir la restriction sur le montant pour chaque carte.");
define('MODULE_PAYMENT_PAYZEN_OPTIONS_LABEL', "Libellé");
define('MODULE_PAYMENT_PAYZEN_OPTIONS_MIN_AMOUNT', "Montant min");
define('MODULE_PAYMENT_PAYZEN_OPTIONS_MAX_AMOUNT', "Montant max");

// multi payment catalog messages
define('MODULE_PAYMENT_PAYZEN_MULTI_TITLE', "PayZen - Paiement avec Choozeo");
