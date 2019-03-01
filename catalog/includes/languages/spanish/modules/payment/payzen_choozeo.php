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
define('MODULE_PAYMENT_PAYZEN_NULL_MIN_AMOUNT_ERROR', "El campo « Monto mínimo » es obligatorio.");
define('MODULE_PAYMENT_PAYZEN_NULL_MAX_AMOUNT_ERROR', "El campo « Monto máximo » es obligatorio.");
define('MODULE_PAYMENT_PAYZEN_INCONSISTENT_AMOUNT_ERROR', "« Monto mínimo » debe ser inferior a « Monto máximo ».");
define('MODULE_PAYMENT_PAYZEN_CHOOZEO_OPTIONS_ERROR', "Los montos mínimos y máximos de optiones de pago deben estar dentro del rango definido por los campos « Monto mínimo » y « Monto máximo ».");

// administration interface - choozeo payment options
define('MODULE_PAYMENT_PAYZEN_OPTIONS_TITLE', "Opciones de pago");
define('MODULE_PAYMENT_PAYZEN_OPTIONS_DESC', "Defina la restricción del monto para cada tarjeta.");
define('MODULE_PAYMENT_PAYZEN_OPTIONS_LABEL', "Etiqueta");
define('MODULE_PAYMENT_PAYZEN_OPTIONS_MIN_AMOUNT', "Monto mínimo");
define('MODULE_PAYMENT_PAYZEN_OPTIONS_MAX_AMOUNT', "Monto máximo");

// choozeo payment catalog messages
define('MODULE_PAYMENT_PAYZEN_CHOOZEO_TITLE', "PayZen - Pago con Choozeo");
