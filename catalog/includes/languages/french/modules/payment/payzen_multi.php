<?php
/**
 * PayZen V2-Payment Module version 1.1.3 (revision 66007) for osCommerce 2.3.
 *
 * Copyright (C) 2014-2015 Lyra Network and contributors
 * Support contact : support@payzen.eu
 * Author link : http://www.lyra-network.com/
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
 * @category  payment
 * @package   payzen
 * @author    Lyra Network <supportvad@lyra-network.com>
 * @copyright 2014-2015 Lyra Network and contributors
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html  GNU General Public License (GPL v2)
 * @version   1.1.3 (revision 66007)
*/

include_once 'payzen_common.php';

## CATALOG MESSAGES ##

define('MODULE_PAYMENT_PAYZEN_MULTI_TITLE', "PayZen - paiement par carte bancaire en plusieurs fois");
define('MODULE_PAYMENT_PAYZEN_MULTI_SHORT_TITLE', "PayZen - paiement en plusieurs fois");

## ADMINISTRATION INTERFACE - MULTI PAYMENT SETTINGS ##
define('MODULE_PAYMENT_PAYZEN_OPTIONS_TITLE', "Options de paiement");
define('MODULE_PAYMENT_PAYZEN_OPTIONS_DESC', "Cliquer sur le bouton \"Ajouter\" pour configurer une ou plusieurs options de paiement. Pour plus d'informations, merci de consulter la documentation. <b>N'oubliez pas de cliquer sur le bouton \"Sauvegarder\" afin de sauvegarder vos modifications.</b>");
define('MODULE_PAYMENT_PAYZEN_OPTIONS_LABEL', "Libellé");
define('MODULE_PAYMENT_PAYZEN_OPTIONS_MIN_AMOUNT', "Montant min");
define('MODULE_PAYMENT_PAYZEN_OPTIONS_MAX_AMOUNT', "Montant max");
define('MODULE_PAYMENT_PAYZEN_OPTIONS_CONTRACT', "Contrat");
define('MODULE_PAYMENT_PAYZEN_OPTIONS_COUNT', "Nombre");
define('MODULE_PAYMENT_PAYZEN_OPTIONS_PERIOD', "Période");
define('MODULE_PAYMENT_PAYZEN_OPTIONS_FIRST', "1er paiement");
define('MODULE_PAYMENT_PAYZEN_OPTIONS_ADD', "Ajouter");
define('MODULE_PAYMENT_PAYZEN_OPTIONS_DELETE', "Supprimer");
?>