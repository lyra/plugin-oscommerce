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

/**
 * This file is an access point for the PayZen payment gateway to validate an order.
 */

// restore session if this is a server call.
if(key_exists('vads_hash', $_POST) && isset($_POST['vads_hash']) && key_exists('vads_result', $_POST) && isset($_POST['vads_result'])) {
	$osCsid = substr($_POST['vads_order_info'], strlen('session_id='));
	$_POST['osCsid'] = $osCsid;
	$_GET['osCsid'] = $osCsid;

	// for cookie based sessions ...
	$_COOKIE['osCsid'] = $osCsid;
	$_COOKIE['cookie_test'] = 'please_accept_for_session';
}

require_once 'checkout_process.php';
?>