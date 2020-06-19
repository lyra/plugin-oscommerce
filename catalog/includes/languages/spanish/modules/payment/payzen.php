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
define('MODULE_PAYMENT_PAYZEN_MODULE_INFORMATION', "INFORMACIÓN DEL MÓDULO");
define('MODULE_PAYMENT_PAYZEN_DEVELOPED_BY', "Desarrollado por: ");
define('MODULE_PAYMENT_PAYZEN_CONTACT_EMAIL', "Contáctenos: ");
define('MODULE_PAYMENT_PAYZEN_CONTRIB_VERSION', "Versión del módulo: ");
define('MODULE_PAYMENT_PAYZEN_GATEWAY_VERSION', "Versión del portal: ");
define('MODULE_PAYMENT_PAYZEN_IPN_URL_TITLE', "URL de notificación de pago instantáneo: ");
define('MODULE_PAYMENT_PAYZEN_IPN_URL_DESC', "URL a copiar en el Back Office PayZen > Configuración > Reglas de notificación.");

// Administration interface - module settings.
define('MODULE_PAYMENT_PAYZEN_STATUS_TITLE', "Activación");
define('MODULE_PAYMENT_PAYZEN_STATUS_DESC', "Habilita/deshabilita este método de pago.");
define('MODULE_PAYMENT_PAYZEN_SORT_ORDER_TITLE', "Orden de clasificación");
define('MODULE_PAYMENT_PAYZEN_SORT_ORDER_DESC', "En la lista de métodos de pago.");
define('MODULE_PAYMENT_PAYZEN_ZONE_TITLE', "Zona de pago");
define('MODULE_PAYMENT_PAYZEN_ZONE_DESC', "Si se elige una zona, este método de pago será efectivo solo para esta.");

// Administration interface - gateway settings.
define('MODULE_PAYMENT_PAYZEN_SITE_ID_TITLE', "ID de tienda");
define('MODULE_PAYMENT_PAYZEN_SITE_ID_DESC', "El identificador proporcionado por PayZen.");
define('MODULE_PAYMENT_PAYZEN_KEY_TEST_TITLE', "Clave en modo test");
define('MODULE_PAYMENT_PAYZEN_KEY_TEST_DESC', "Clave proporcionada por PayZen para modo test (disponible en el Back Office PayZen).");
define('MODULE_PAYMENT_PAYZEN_KEY_PROD_TITLE', "Clave en modo production");
define('MODULE_PAYMENT_PAYZEN_KEY_PROD_DESC', "Clave proporcionada por PayZen (disponible en el Back Office PayZen después de habilitar el modo production).");
define('MODULE_PAYMENT_PAYZEN_CTX_MODE_TITLE', "Modo");
define('MODULE_PAYMENT_PAYZEN_CTX_MODE_DESC', "El modo de contexto de este módulo.");
define('MODULE_PAYMENT_PAYZEN_SIGN_ALGO_TITLE', "Algoritmo de firma");
define('MODULE_PAYMENT_PAYZEN_SIGN_ALGO_DESC', "Algoritmo usado para calcular la firma del formulario de pago. El algoritmo seleccionado debe ser el mismo que el configurado en el Back Office PayZen." . (! $payzen_plugin_features['shatwo'] ? "El algoritmo HMAC-SHA-256 no se debe activar si aún no está disponible el Back Office PayZen, la función estará disponible pronto." : ''));
define('MODULE_PAYMENT_PAYZEN_PLATFORM_URL_TITLE', "URL de página de pago");
define('MODULE_PAYMENT_PAYZEN_PLATFORM_URL_DESC', "Enlace a la página de pago.");

// Administration interface - payment settings.
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_TITLE', "Idioma predeterminado");
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_DESC', "Idioma predeterminado en la página de pago.");
define('MODULE_PAYMENT_PAYZEN_AVAILABLE_LANGUAGES_TITLE', "Idiomas disponibles");
define('MODULE_PAYMENT_PAYZEN_AVAILABLE_LANGUAGES_DESC', "Idiomas disponibles en la página de pago. Si no selecciona ninguno, todos los idiomas compatibles estarán disponibles.");
define('MODULE_PAYMENT_PAYZEN_CAPTURE_DELAY_TITLE', "Plazo de captura");
define('MODULE_PAYMENT_PAYZEN_CAPTURE_DELAY_DESC', "El número de días antes de la captura del pago (ajustable en su Back Office PayZen).");
define('MODULE_PAYMENT_PAYZEN_VALIDATION_MODE_TITLE', "Modo de validación");
define('MODULE_PAYMENT_PAYZEN_VALIDATION_MODE_DESC', "Si se selecciona manual, deberá confirmar los pagos manualmente en su Back Office PayZen.");
define('MODULE_PAYMENT_PAYZEN_PAYMENT_CARDS_TITLE', "Tipos de tarjeta");
define('MODULE_PAYMENT_PAYZEN_PAYMENT_CARDS_DESC', "El tipo(s) de tarjeta que se puede usar para el pago. No haga ninguna selección para usar la configuración del portal.");
define('MODULE_PAYMENT_PAYZEN_3DS_MIN_AMOUNT_TITLE', "Deshabilitar 3DS");
define('MODULE_PAYMENT_PAYZEN_3DS_MIN_AMOUNT_DESC', "Monto por debajo del cual se deshabilitará 3DS. Requiere suscripción a la opción 3DS selectivo. Para más información, consulte la documentación del módulo.");

// Administration interface - amount restrictions settings.
define('MODULE_PAYMENT_PAYZEN_MIN_AMOUNT_TITLE', "Monto mínimo");
define('MODULE_PAYMENT_PAYZEN_MIN_AMOUNT_DESC', "Monto mínimo para activar este método de pago.");
define('MODULE_PAYMENT_PAYZEN_MAX_AMOUNT_TITLE', "Monto máximo");
define('MODULE_PAYMENT_PAYZEN_MAX_AMOUNT_DESC', "Monto máximo para activar este método de pago.");

// Administration interface - back to store settings.
define('MODULE_PAYMENT_PAYZEN_REDIRECT_ENABLED_TITLE', "Redirección automática");
define('MODULE_PAYMENT_PAYZEN_REDIRECT_ENABLED_DESC', "Si está habilitada, el comprador es redirigido automáticamente a su sitio al final del pago.");
define('MODULE_PAYMENT_PAYZEN_REDIRECT_SUCCESS_TIMEOUT_TITLE', "Tiempo de espera de la redirección en pago exitoso");
define('MODULE_PAYMENT_PAYZEN_REDIRECT_SUCCESS_TIMEOUT_DESC', "Tiempo en segundos (0-300) antes de que el comprador sea redirigido automáticamente a su sitio web después de un pago exitoso.");
define('MODULE_PAYMENT_PAYZEN_REDIRECT_SUCCESS_MESSAGE_TITLE', "Mensaje de redirección en pago exitoso");
define('MODULE_PAYMENT_PAYZEN_REDIRECT_SUCCESS_MESSAGE_DESC', "Mensaje mostrado en la página de pago antes de la redirección después de un pago exitoso.");
define('MODULE_PAYMENT_PAYZEN_REDIRECT_ERROR_TIMEOUT_TITLE', "Tiempo de espera de la redirección en pago rechazado");
define('MODULE_PAYMENT_PAYZEN_REDIRECT_ERROR_TIMEOUT_DESC', "Tiempo en segundos (0-300) antes de que el comprador sea redirigido automáticamente a su sitio web después de un pago rechazado.");
define('MODULE_PAYMENT_PAYZEN_REDIRECT_ERROR_MESSAGE_TITLE', "Mensaje de redirección en pago rechazado");
define('MODULE_PAYMENT_PAYZEN_REDIRECT_ERROR_MESSAGE_DESC', "Mensaje mostrado en la página de pago antes de la redirección después de un pago rechazado.");
define('MODULE_PAYMENT_PAYZEN_RETURN_MODE_TITLE', "Modo de retorno");
define('MODULE_PAYMENT_PAYZEN_RETURN_MODE_DESC', "Método que se usará para transmitir el resultado del pago de la página de pago a su tienda.");
define('MODULE_PAYMENT_PAYZEN_ORDER_STATUS_TITLE', "Estado de pedidos");
define('MODULE_PAYMENT_PAYZEN_ORDER_STATUS_DESC', "Estado de los pedidos cuando el pago es exitoso.");

// Administration interface - misc constants.
define('MODULE_PAYMENT_PAYZEN_VALUE_0', "Deshabilitado");
define('MODULE_PAYMENT_PAYZEN_VALUE_1', "Habilitado");

define('MODULE_PAYMENT_PAYZEN_VALIDATION_DEFAULT', "Configuración de Back Office PayZen");
define('MODULE_PAYMENT_PAYZEN_VALIDATION_0', "Automático");
define('MODULE_PAYMENT_PAYZEN_VALIDATION_1', "Manual");

define('MODULE_PAYMENT_PAYZEN_LANGUAGE_FRENCH', "Francés");
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_GERMAN', "Alemán");
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_ENGLISH', "Inglés");
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_SPANISH', "Español");
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_CHINESE', "Chino");
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_ITALIAN', "Italiano");
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_JAPANESE', "Japonés");
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_PORTUGUESE', "Portugués");
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_DUTCH', "Holandés");
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_SWEDISH', "Sueco");
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_RUSSIAN', "Ruso");
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_POLISH', "Polaco");
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_TURKISH', "Turco");

define('MODULE_PAYMENT_PAYZEN_REDIRECT_SUCCESS_MESSAGE', "Redirección a la tienda en unos momentos...");
define('MODULE_PAYMENT_PAYZEN_REDIRECT_ERROR_MESSAGE', "Redirección a la tienda en unos momentos...");

// Catalog messages.
define('MODULE_PAYMENT_PAYZEN_TECHNICAL_ERROR', "Ocurrió un error durante el proceso de pago.");
define('MODULE_PAYMENT_PAYZEN_PAYMENT_ERROR', "Su pago no fue aceptado. Intente realizar de nuevo el pedido.");
define('MODULE_PAYMENT_PAYZEN_CHECK_URL_WARN', "La validación automática no ha funcionado. ¿Configuró correctamente la URL de notificación en su Back Office PayZen?");
define('MODULE_PAYMENT_PAYZEN_CHECK_URL_WARN_DETAIL', "Para entender el problema, lea la documentación del módulo:<br />&nbsp;&nbsp;&nbsp;- Capítulo « Leer detenidamente antes de continuar »<br />&nbsp;&nbsp;&nbsp;- Capítulo « Configuración de la URL de notificación »");
define('MODULE_PAYMENT_PAYZEN_GOING_INTO_PROD_INFO', "<b>IR A PRODUCTION:</b> Si desea saber cómo poner su tienda en modo production, lea los capítulos « Proceder a la fase de prueba » y « Paso de una tienda al modo producción » en la documentación del módulo.");

// Single payment catalog messages.
define('MODULE_PAYMENT_PAYZEN_STD_TITLE', "PayZen - Pago con tarjeta de crédito");
