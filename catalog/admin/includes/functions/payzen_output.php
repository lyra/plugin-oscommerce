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

/**
 * General functions to draw PayZen configuration parameters.
 */

global $payzen_supported_languages, $payzen_supported_cards;

// load PayZen payment API
$payzen_supported_languages = PayzenApi::getSupportedLanguages();
$payzen_supported_cards = PayzenApi::getSupportedCardTypes();


function payzen_get_bool_title($value)
{
    $key = 'MODULE_PAYMENT_PAYZEN_VALUE_' . $value;

    if (defined($key)) {
        return constant($key);
    } else {
        return $value;
    }
}

function payzen_get_sign_algo_title($value)
{
    $algos = array(
        'SHA-1' => 'SHA-1',
        'SHA-256' => 'HMAC-SHA-256'
    );

    return $algos[$value];
}

function payzen_get_lang_title($value)
{
    global $payzen_supported_languages;

    $key = 'MODULE_PAYMENT_PAYZEN_LANGUAGE_' . strtoupper($payzen_supported_languages[$value]);

    if (defined($key)) {
        return constant($key);
    } else {
        return $value;
    }
}

function payzen_get_multi_lang_title($value)
{
    if (! empty($value)) {
        $langs = explode(';', $value);

        $result = array();
        foreach ($langs as $lang) {
            $result[] = payzen_get_lang_title($lang);
        }

        return implode(', ', $result);
    } else {
        return '';
    }
}

function payzen_get_validation_mode_title($value)
{
    $key = 'MODULE_PAYMENT_PAYZEN_VALIDATION_' . $value;

    if (defined($key)) {
        return constant($key);
    } else {
        return MODULE_PAYMENT_PAYZEN_VALIDATION_DEFAULT;
    }
}

function payzen_get_card_title($value)
{
    global $payzen_supported_cards;

    if (! empty($value)) {
        $cards = explode(';', $value);

        $result = array();
        foreach ($cards as $card) {
            $result[] = $payzen_supported_cards[$card];
        }

        return implode(', ', $result);
    } else {
        return '';
    }
}

function payzen_get_multi_options($value)
{
    if (! $value) {
        return '';
    }

    $options = json_decode($value, true);
    if (! is_array($options) || empty($options)) {
        return '';
    }

    $field = '<table cellpadding="10" cellspacing="5" >';
    $field .= '<thead><tr>';
    $field .= '<th style="padding: 0px;">' . MODULE_PAYMENT_PAYZEN_OPTIONS_LABEL . '</th>';
    $field .= '<th style="padding: 0px;">' . MODULE_PAYMENT_PAYZEN_OPTIONS_MIN_AMOUNT . '</th>';
    $field .= '<th style="padding: 0px;">' . MODULE_PAYMENT_PAYZEN_OPTIONS_MAX_AMOUNT . '</th>';
    $field .= '<th style="padding: 0px;">' . MODULE_PAYMENT_PAYZEN_OPTIONS_CONTRACT . '</th>';
    $field .= '<th style="padding: 0px;">' . MODULE_PAYMENT_PAYZEN_OPTIONS_COUNT . '</th>';
    $field .= '<th style="padding: 0px;">' . MODULE_PAYMENT_PAYZEN_OPTIONS_PERIOD . '</th>';
    $field .= '<th style="padding: 0px;">' . MODULE_PAYMENT_PAYZEN_OPTIONS_FIRST . '</th>';
    $field .= '</tr></thead>';

    $field .= '<tbody>';
    foreach ($options as $option) {
        $field .= '<tr>';
        $field .= '<td style="padding: 0px;">' . $option['label'] . '</td>';
        $field .= '<td style="padding: 0px;">' . $option['min_amount'] . '</td>';
        $field .= '<td style="padding: 0px;">' . $option['max_amount'] . '</td>';
        $field .= '<td style="padding: 0px;">' . $option['contract'] . '</td>';
        $field .= '<td style="padding: 0px;">' . $option['count'] . '</td>';
        $field .= '<td style="padding: 0px;">' . $option['period'] . '</td>';
        $field .= '<td style="padding: 0px;">' . $option['first'] . '</td>';
        $field .= '</tr>';
    }
    $field .= '</tbody></table>';

    return $field;
}

function payzen_get_choozeo_options($value)
{
    if (! $value) {
        return '';
    }

    $options = json_decode($value, true);
    if (! is_array($options) || empty($options)) {
        return '';
    }

    $field = '<table cellpadding="10" cellspacing="5" >';

    $field .= '<thead><tr>';
    $field .= '<th style="padding: 0px;">' . MODULE_PAYMENT_PAYZEN_OPTIONS_LABEL . '</th>';
    $field .= '<th style="padding: 0px;">' . MODULE_PAYMENT_PAYZEN_OPTIONS_MIN_AMOUNT . '</th>';
    $field .= '<th style="padding: 0px;">' . MODULE_PAYMENT_PAYZEN_OPTIONS_MAX_AMOUNT . '</th>';
    $field .= '</tr></thead>';

    $field .= '<tbody>';

    $choozeo_options = array (
        'EPNF_3X' => 'Choozeo 3x CB',
        'EPNF_4X' => 'Choozeo 4x CB'
    );

    foreach ($choozeo_options as $code => $option) {
        $field .= '<tr>';
        $field .= '<td style="padding: 0px;">' . $option . '</td>';
        $field .= '<td style="padding: 0px;">' . $options[$code]['min_amount'] . '</td>';
        $field .= '<td style="padding: 0px;">' . $options[$code]['max_amount'] . '</td>';
        $field .= '</tr>';
    }

    $field .= '</tbody></table>';

    return $field;
}

function payzen_cfg_draw_pull_down_bools($value='', $name)
{
    $name = 'configuration[' . tep_output_string($name) . ']';
    if (empty($value) && isset($GLOBALS[$name])) $value = stripslashes($GLOBALS[$name]);

    $bools = array('1', '0');

    $field = '';
    foreach ($bools as $bool) {
        $field .= '<input type="radio" name="' . $name . '" value="' . $bool . '"';
        if ($value == $bool) {
            $field .= ' checked="checked"';
        }

        $field .= '> ' . tep_output_string(payzen_get_bool_title($bool)) . '<br />';
    }

    return $field;
}

function payzen_cfg_draw_pull_down_sign_algos($value='', $name)
{
    $name = 'configuration[' . tep_output_string($name) . ']';

    if (empty($value) && isset($GLOBALS[$name])) {
        $value = stripslashes($GLOBALS[$name]);
    }

    $algos = array(
        'SHA-1' => 'SHA-1',
        'SHA-256' => 'HMAC-SHA-256'
    );

    $field = '<select name="' . $name . '">';
    foreach ($algos as $code => $algo) {
        $field .= '<option value="' . $code . '"';
        if ($value == $code) {
            $field .= ' selected="selected"';
        }

        $field .= '>' . tep_output_string($algo) . '</option>';
    }

    $field .= '</select>';

    return $field;
}

function payzen_cfg_draw_pull_down_validation_modes($value='', $name)
{
    $name = 'configuration[' . tep_output_string($name) . ']';

    if (empty($value) && isset($GLOBALS[$name])) $value = stripslashes($GLOBALS[$name]);
    $modes = array('', '0', '1');

    $field = '<select name="' . $name . '">';
    foreach ($modes as $mode) {
        $field .= '<option value="' . $mode . '"';
        if ($value == $mode) {
            $field .= ' selected="selected"';
        }

        $field .= '>' . tep_output_string(payzen_get_validation_mode_title($mode)) . '</option>';
    }

    $field .= '</select>';

    return $field;
}

function payzen_cfg_draw_pull_down_langs($value='', $name)
{
    global $payzen_supported_languages;

    $name = 'configuration[' . tep_output_string($name) . ']';
    if (empty($value) && isset($GLOBALS[$name])) $value = stripslashes($GLOBALS[$name]);

    $field = '<select name="' . $name . '">';
    foreach (array_keys($payzen_supported_languages) as $key) {
        $field .= '<option value="' . $key . '"';
        if ($value == $key) {
            $field .= ' selected="selected"';
        }

        $field .= '>' . tep_output_string(payzen_get_lang_title($key)) . '</option>';
    }

    $field .= '</select>';

    return $field;
}

function payzen_cfg_draw_pull_down_multi_langs($value='', $name)
{
    global $payzen_supported_languages;

    $fieldName = 'configuration[' . tep_output_string($name) . ']';
    if (empty($value) && isset($GLOBALS[$fieldName])) $value = stripslashes($GLOBALS[$fieldName]);

    $langs = empty($value) ? array() : explode(';', $value);

    $field = '<select name="' . tep_output_string($name) . '" multiple="multiple" onChange="JavaScript:payzenProcessLangs()">';
    foreach (array_keys($payzen_supported_languages) as $key) {
        $field .= '<option value="' . $key . '"';
        if (in_array($key, $langs)) {
            $field .= ' selected="selected"';
        }

        $field .= '>' . tep_output_string(payzen_get_lang_title($key)) . '</option>';
    }
    $field .= '</select> <br />';

    $field .= <<<JSCODE
    <script type="text/javascript">
        function payzenProcessLangs() {
            var elt = document.forms['modules'].elements['$name'];

            var result = '';
            for (var i=0; i < elt.length; i++) {
                if (elt[i].selected) {
                    if (result != '') result += ';';
                    result += elt[i].value;
                }
            }

            document.forms['modules'].elements['$fieldName'].value = result;
        }
    </script>
JSCODE;

    $field .= '<input type="hidden" name="' . tep_output_string($fieldName) . '" value="' . $value . '">';

    return $field;
}

function payzen_cfg_draw_pull_down_cards($value='', $name)
{
    global $payzen_supported_cards;

    $fieldName = 'configuration[' . tep_output_string($name) . ']';
    if (empty($value) && isset($GLOBALS[$fieldName])) $value = stripslashes($GLOBALS[$fieldName]);

    $cards = empty($value) ? array() : explode(';', $value);

    $field = '<select name="' . tep_output_string($name) . '" multiple="multiple" onChange="JavaScript:payzenProcessCards()">';
    foreach ($payzen_supported_cards as $key => $label) {
        $field .= '<option value="' . $key . '"';
        if (in_array($key, $cards)) {
            $field .= ' selected="selected"';
        }

        $field .= '>' . tep_output_string($label) . '</option>';
    }
    $field .= '</select> <br />';

    $field .= <<<JSCODE
    <script type="text/javascript">
        function payzenProcessCards() {
            var elt = document.forms['modules'].elements['$name'];

            var result = '';
            for (var i=0; i < elt.length; i++) {
                if (elt[i].selected) {
                    if (result != '') result += ';';
                    result += elt[i].value;
                }
            }

            document.forms['modules'].elements['$fieldName'].value = result;
        }
    </script>
JSCODE;

    $field .= '<input type="hidden" name="' . tep_output_string($fieldName) . '" value="' . $value . '">';

    return $field;
}

function payzen_cfg_draw_table_multi_options($value='', $name)
{
    $name = tep_output_string($name);

    $fieldName = 'configuration[' . $name . ']';
    if (empty($value) && isset($GLOBALS[$fieldName])) $value = stripslashes($GLOBALS[$fieldName]);

    $options = empty($value) ? array() : json_decode($value, true);

    $field = '<input id="' . $name . '_btn" class="' . $name . '_btn"' . (! empty($options) ? ' style="display: none;"' : '') . ' type="button" value="' . MODULE_PAYMENT_PAYZEN_OPTIONS_ADD . '" />';
    $field .= '<table id="' . $name . '_table"' . (empty($options) ? ' style="display: none;"' : '') . ' cellpadding="10" cellspacing="0" >';

    $field .= '<thead><tr>';
    $field .= '<th style="padding: 0px;" class="label">' . MODULE_PAYMENT_PAYZEN_OPTIONS_LABEL . '</th>';
    $field .= '<th style="padding: 0px;" class="min_amount">' . MODULE_PAYMENT_PAYZEN_OPTIONS_MIN_AMOUNT . '</th>';
    $field .= '<th style="padding: 0px;" class="max_amount">' . MODULE_PAYMENT_PAYZEN_OPTIONS_MAX_AMOUNT . '</th>';
    $field .= '<th style="padding: 0px;" class="contract">' . MODULE_PAYMENT_PAYZEN_OPTIONS_CONTRACT . '</th>';
    $field .= '<th style="padding: 0px;" class="count">' . MODULE_PAYMENT_PAYZEN_OPTIONS_COUNT . '</th>';
    $field .= '<th style="padding: 0px;" class="period">' . MODULE_PAYMENT_PAYZEN_OPTIONS_PERIOD . '</th>';
    $field .= '<th style="padding: 0px;" class="first">' . MODULE_PAYMENT_PAYZEN_OPTIONS_FIRST . '</th>';
    $field .= '<th style="padding: 0px;"></th>';
    $field .= '</tr></thead>';

    $field .= '<tbody>';
    $field .= '<tr id="' . $name . '_add">
                <td colspan="7"></td>
                <td style="padding: 0px;"><input class="' . $name . '_btn" type="button" value="' . MODULE_PAYMENT_PAYZEN_OPTIONS_ADD . '" /></td>
               </tr>';
    $field .= '</tbody></table>';

    $field .= "\n" . '<script type="text/javascript">';

    $field .= "\n" . 'jQuery(".' . $name . '_btn").click(function() {
                        payzenAddOption("' . $name . '");
                     });';

    // add already inserted lines
    if (! empty($options)) {
        foreach ($options as $code => $option) {
            $field .= "\n" . 'payzenAddOption("' . $name . '", "' . $code . '", ' . json_encode($option) . ');' . "\n";
        }
    }


    $deleteTxt = MODULE_PAYMENT_PAYZEN_OPTIONS_DELETE;

    $js_serialize = payzen_js_serialize($name);

    $field .= <<<JSCODE
        $js_serialize

        function payzenAddOption(name, key, record) {
            if (jQuery('#' + name + '_table tbody tr').length == 1) {
                jQuery('#' + name + '_btn').css('display', 'none');
                jQuery('#' + name + '_table').css('display', '');
            }

            if (! key && ! record) {
                // new line, generate key and use empty record
                key = new Date().getTime();
                record = { label: "", min_amount: "", max_amount: "", contract: "", count: "", period: "", first: "" };
            }

            var inputPrefix = name + '[' + key + ']';

            var optionLine = '<tr id="' + name + '_line_' + key + '">';
            optionLine += '<td style="padding: 0px;"><input style="width: 150px;" name="' + inputPrefix + '[label]" type="text" value="' + record['label'] + '" /></td>';
            optionLine += '<td style="padding: 0px;"><input style="width: 75px;" name="' + inputPrefix + '[min_amount]" type="text" value="' + record['min_amount'] + '" /></td>';
            optionLine += '<td style="padding: 0px;"><input style="width: 75px;" name="' + inputPrefix + '[max_amount]" type="text" value="' + record['max_amount'] + '" /></td>';
            optionLine += '<td style="padding: 0px;"><input style="width: 65px;" name="' + inputPrefix + '[contract]" type="text" value="' + record['contract'] + '" /></td>';
            optionLine += '<td style="padding: 0px;"><input style="width: 65px;" name="' + inputPrefix + '[count]" type="text" value="' + record['count'] + '" /></td>';
            optionLine += '<td style="padding: 0px;"><input style="width: 65px;" name="' + inputPrefix + '[period]" type="text" value="' + record['period'] + '" /></td>';
            optionLine += '<td style="padding: 0px;"><input style="width: 75px;" name="' + inputPrefix + '[first]" type="text" value="' + record['first'] + '" /></td>';
            optionLine += '<td style="padding: 0px;"><input type="button" value="$deleteTxt" onclick="javascript: payzenDeleteOption(\'' + name + '\', \'' + key + '\');" /></td>';
            optionLine += '</tr>';

            jQuery(optionLine).insertBefore('#' + name + '_add');
        }

        function payzenDeleteOption(name, key) {
            jQuery('#' + name + '_line_' + key).remove();

            if (jQuery('#' + name + '_table tbody tr').length == 1) {
                jQuery('#' + name + '_btn').css('display', '');
                jQuery('#' + name + '_table').css('display', 'none');
            }
        }
    </script>
JSCODE;

    $field .= '<input type="hidden" name="' . $fieldName . '" value="' . $value . '">';

    return $field;
}

function payzen_cfg_draw_table_choozeo_options($value='', $name)
{
    $name = tep_output_string($name);

    $fieldName = 'configuration[' . $name . ']';

    if (empty($value) && isset($GLOBALS[$fieldName])) $value = stripslashes($GLOBALS[$fieldName]);

    $value = empty($value) ? array() : json_decode($value, true);

    $field = '<table id="' . $name . '_table" cellpadding="10" cellspacing="0" >';

    $field .= '<thead><tr>';
    $field .= '<th style="padding: 0px;" class="label">' . MODULE_PAYMENT_PAYZEN_OPTIONS_LABEL . '</th>';
    $field .= '<th style="padding: 0px;" class="min_amount">' . MODULE_PAYMENT_PAYZEN_OPTIONS_MIN_AMOUNT . '</th>';
    $field .= '<th style="padding: 0px;" class="max_amount">' . MODULE_PAYMENT_PAYZEN_OPTIONS_MAX_AMOUNT . '</th>';
    $field .= '</tr></thead>';

    $field .= '<tbody>';

    $choozeo_options = array (
        'EPNF_3X' => 'Choozeo 3x CB',
        'EPNF_4X' => 'Choozeo 4x CB'
    );

    foreach ($choozeo_options as $code => $option) {
        $field .= '<tr>';
        $field .= '<td style="padding: 0px; width:150px;"><input name="' . $name . '[' . $code . '][label]" value="' . $option . '" type="text" readonly ></td>';
        $field .= '<td style="padding: 0px; width:150px;"><input name="' . $name . '[' . $code . '][min_amount]" value="' . $value[$code]["min_amount"] . '" type="text"></td>';
        $field .= '<td style="padding: 0px; width:150px;"><input name="' . $name . '[' . $code . '][max_amount]" value="' . $value[$code]["max_amount"] . '" type="text"></td>';
        $field .= '</tr>';
    }

    $field .= '</tbody></table>';

    $js_serialize = payzen_js_serialize($name);

    $field .= <<<JSCODE
    <script type="text/javascript">
        $js_serialize
    </script>
JSCODE;

    $field .= '<input type="hidden" name="' . tep_output_string($fieldName) . '" value="' . $value . '">';

    return $field;
}

function payzen_js_serialize($name)
{
    $fieldName = 'configuration[' . $name . ']';

    $js_code = <<<JSCODE
    var JSON = JSON || {};

    // implement JSON.stringify serialization
    JSON.stringify || function(obj) {
        var t = typeof (obj);
        if (t != "object" || obj === null) {
            // simple data type
            if (t == "string") obj = '"' + obj + '"';
            return String(obj);
        } else {
            // recurse array or object
            var n, v, json = [], arr = (obj && obj.constructor == Array);

            for (n in obj) {
                v = obj[n]; t = typeof(v);

                if (t == "string") v = '"'+v+'"';
                else if (t == "object" && v !== null) v = JSON.stringify(v);

                json.push((arr ? "" : '"' + n + '":') + String(v));
            }

            return (arr ? "[" : "{") + String(json) + (arr ? "]" : "}");
        }
    };

    jQuery(document.forms['modules']).submit(function(event) {
        var options = {};

        jQuery('#$name' + '_table tbody tr td input[type=text]').each(function() {
            var name = jQuery(this).attr('name');
            name = name.replace(/\]/g, '');

            var keys = name.split('[');
            keys.shift();

            options = payzenFillArray(options, keys, jQuery(this).val());
        });

            document.forms['modules'].elements['$fieldName'].value = JSON.stringify(options);
            return true;
    });

    function payzenFillArray(arr, keys, val) {
        if (keys.length > 0) {
            var key = keys[0];

            if (keys.length == 1) {
                // it's a leaf, let's set the value
                arr[key] = val;
            } else {
                keys.shift();

                if (! arr[key]) {
                    arr[key] = {};
                }
                arr[key] = payzenFillArray(arr[key], keys, val);
            }
        }

        return arr;
    }
JSCODE;

    return $js_code;
}
