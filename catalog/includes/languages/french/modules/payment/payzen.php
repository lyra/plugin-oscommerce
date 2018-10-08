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

global $payzen_plugin_features;

// administration interface - informations
define('MODULE_PAYMENT_PAYZEN_MODULE_INFORMATION', "INFORMATIONS SUR LE MODULE");
define('MODULE_PAYMENT_PAYZEN_DEVELOPED_BY', "Développé par : ");
define('MODULE_PAYMENT_PAYZEN_CONTACT_EMAIL', "Courriel de contact : ");
define('MODULE_PAYMENT_PAYZEN_CONTRIB_VERSION', "Version du module : ");
define('MODULE_PAYMENT_PAYZEN_GATEWAY_VERSION', "Version de la plateforme : ");
define('MODULE_PAYMENT_PAYZEN_IPN_URL_TITLE', "URL de notification");
define('MODULE_PAYMENT_PAYZEN_IPN_URL_DESC', "URL à copier dans le Back Office PayZen > Paramétrage > Règles de notifications.");

// administration interface - module settings
define('MODULE_PAYMENT_PAYZEN_STATUS_TITLE', "Activation");
define('MODULE_PAYMENT_PAYZEN_STATUS_DESC', "Activer / désactiver le module de paiement PayZen.");
define('MODULE_PAYMENT_PAYZEN_SORT_ORDER_TITLE', "Ordre d'affichage");
define('MODULE_PAYMENT_PAYZEN_SORT_ORDER_DESC', "Le plus petit indice est affiché en premier.");
define('MODULE_PAYMENT_PAYZEN_ZONE_TITLE', "Zone de paiement");
define('MODULE_PAYMENT_PAYZEN_ZONE_DESC', "Si une zone est choisie, ce mode de paiement ne sera effectif que pour celle-ci.");

// administration interface - gateway settings
define('MODULE_PAYMENT_PAYZEN_SITE_ID_TITLE', "Identifiant boutique");
define('MODULE_PAYMENT_PAYZEN_SITE_ID_DESC', "Identifiant fourni par PayZen.");
define('MODULE_PAYMENT_PAYZEN_KEY_TEST_TITLE', "Certificat en mode test");
define('MODULE_PAYMENT_PAYZEN_KEY_TEST_DESC', "Certificat fourni par PayZen pour le mode test (disponible sur le Back Office de votre boutique).");
define('MODULE_PAYMENT_PAYZEN_KEY_PROD_TITLE', "Certificat en mode production");
define('MODULE_PAYMENT_PAYZEN_KEY_PROD_DESC', "Certificat fourni par PayZen (disponible sur le Back Office de votre boutique après passage en production).");
define('MODULE_PAYMENT_PAYZEN_CTX_MODE_TITLE', "Mode");
define('MODULE_PAYMENT_PAYZEN_CTX_MODE_DESC', "Mode de fonctionnement du module.");
define('MODULE_PAYMENT_PAYZEN_SIGN_ALGO_TITLE', "Algorithme de signature");
define('MODULE_PAYMENT_PAYZEN_SIGN_ALGO_DESC', "Algorithme utilisé pour calculer la signature du formulaire de paiement. L'algorithme sélectionné doit être le même que celui configuré sur le Back Office PayZen." . (! $payzen_plugin_features['shwatwo'] ? "Le HMAC-SHA-256 ne doit pas être activé si celui-ci n'est pas encore disponible depuis le Back Office PayZen." : ''));
define('MODULE_PAYMENT_PAYZEN_PLATFORM_URL_TITLE', "URL de la page de paiement");
define('MODULE_PAYMENT_PAYZEN_PLATFORM_URL_DESC', "URL vers laquelle l'acheteur sera redirigé pour le paiement.");

// administration interface - payment settings
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_TITLE', "Langue par défaut");
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_DESC', "Sélectionner la langue par défaut à utiliser sur la page de paiement.");
define('MODULE_PAYMENT_PAYZEN_AVAILABLE_LANGUAGES_TITLE', "Langues disponibles");
define('MODULE_PAYMENT_PAYZEN_AVAILABLE_LANGUAGES_DESC', "Sélectionner les langues à proposer sur la page de paiement.");
define('MODULE_PAYMENT_PAYZEN_CAPTURE_DELAY_TITLE', "Délai avant remise en banque");
define('MODULE_PAYMENT_PAYZEN_CAPTURE_DELAY_DESC', "Le nombre de jours avant la remise en banque (paramétrable sur votre Back Office PayZen).");
define('MODULE_PAYMENT_PAYZEN_VALIDATION_MODE_TITLE', "Mode de validation");
define('MODULE_PAYMENT_PAYZEN_VALIDATION_MODE_DESC', "En mode manuel, vous devrez confirmer les paiements dans le Back Office de votre boutique.");
define('MODULE_PAYMENT_PAYZEN_PAYMENT_CARDS_TITLE', "Types de carte");
define('MODULE_PAYMENT_PAYZEN_PAYMENT_CARDS_DESC', "Le(s) type(s) de carte pouvant être utilisé(s) pour le paiement. Ne rien sélectionner pour utiliser la configuration de la plateforme.");
define('MODULE_PAYMENT_PAYZEN_3DS_MIN_AMOUNT_TITLE', "Désactiver 3DS");
define('MODULE_PAYMENT_PAYZEN_3DS_MIN_AMOUNT_DESC', "Montant en dessous duquel 3DS sera désactivé. Nécessite la souscription à l\'option 3DS sélectif. Pour plus d\'informations, reportez-vous à la documentation du module.");

// administration interface - amount restrictions settings
define('MODULE_PAYMENT_PAYZEN_MIN_AMOUNT_TITLE', "Montant minimum");
define('MODULE_PAYMENT_PAYZEN_MIN_AMOUNT_DESC', "Montant minimum pour lequel cette méthode de paiement est disponible.");
define('MODULE_PAYMENT_PAYZEN_MAX_AMOUNT_TITLE', "Montant maximum");
define('MODULE_PAYMENT_PAYZEN_MAX_AMOUNT_DESC', "Montant maximum pour lequel cette méthode de paiement est disponible.");

// administration interface - back to store settings
define('MODULE_PAYMENT_PAYZEN_REDIRECT_ENABLED_TITLE', "Redirection automatique");
define('MODULE_PAYMENT_PAYZEN_REDIRECT_ENABLED_DESC', "Si activée, l'acheteur sera redirigé automatiquement vers votre site à la fin du paiement.");
define('MODULE_PAYMENT_PAYZEN_REDIRECT_SUCCESS_TIMEOUT_TITLE', "Temps avant redirection (succès)");
define('MODULE_PAYMENT_PAYZEN_REDIRECT_SUCCESS_TIMEOUT_DESC', "Temps en secondes (0-300) avant que l'acheteur ne soit redirigé automatiquement vers votre site lorsque le paiement a réussi.");
define('MODULE_PAYMENT_PAYZEN_REDIRECT_SUCCESS_MESSAGE_TITLE', "Message avant redirection (succès)");
define('MODULE_PAYMENT_PAYZEN_REDIRECT_SUCCESS_MESSAGE_DESC', "Message affiché sur la page de paiement avant redirection lorsque le paiement a réussi.");
define('MODULE_PAYMENT_PAYZEN_REDIRECT_ERROR_TIMEOUT_TITLE', "Temps avant redirection (échec)");
define('MODULE_PAYMENT_PAYZEN_REDIRECT_ERROR_TIMEOUT_DESC', "Temps en secondes (0-300) avant que l'acheteur ne soit redirigé automatiquement vers votre site lorsque le paiement a échoué.");
define('MODULE_PAYMENT_PAYZEN_REDIRECT_ERROR_MESSAGE_TITLE', "Message avant redirection (échec)");
define('MODULE_PAYMENT_PAYZEN_REDIRECT_ERROR_MESSAGE_DESC', "Message affiché sur la page de paiement avant redirection, lorsque le paiement a échoué.");
define('MODULE_PAYMENT_PAYZEN_RETURN_MODE_TITLE', "Mode de retour");
define('MODULE_PAYMENT_PAYZEN_RETURN_MODE_DESC', "Façon dont l'acheteur transmettra le résultat du paiement lors de son retour à la boutique.");
define('MODULE_PAYMENT_PAYZEN_ORDER_STATUS_TITLE', "Statut des commandes");
define('MODULE_PAYMENT_PAYZEN_ORDER_STATUS_DESC', "Definir le statut des commandes payées par ce mode de paiement.");

// administration interface - misc constants
define('MODULE_PAYMENT_PAYZEN_VALUE_0', "Désactivé");
define('MODULE_PAYMENT_PAYZEN_VALUE_1', "Activé");

define('MODULE_PAYMENT_PAYZEN_VALIDATION_DEFAULT', "Configuration Back Office");
define('MODULE_PAYMENT_PAYZEN_VALIDATION_0', "Automatique");
define('MODULE_PAYMENT_PAYZEN_VALIDATION_1', "Manuel");

define('MODULE_PAYMENT_PAYZEN_LANGUAGE_FRENCH', "Français");
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_GERMAN', "Allemand");
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_ENGLISH', "Anglais");
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_SPANISH', "Espagnol");
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_CHINESE', "Chinois");
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_ITALIAN', "Italien");
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_JAPANESE', "Japonais");
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_PORTUGUESE', "Portugais");
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_DUTCH', "Néerlandais");
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_SWEDISH', "Suédois");
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_RUSSIAN', "Russe");
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_POLISH', "Polonais");
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_TURKISH', "Turc");

// catalog messages
define('MODULE_PAYMENT_PAYZEN_TECHNICAL_ERROR', "Une erreur est survenue dans le processus de paiement.");
define('MODULE_PAYMENT_PAYZEN_PAYMENT_ERROR', "Votre paiement n'a pas été accepté. Veuillez repasser votre commande.");
define('MODULE_PAYMENT_PAYZEN_CHECK_URL_WARN', "La validation automatique n'a pas fonctionné. Avez-vous configuré correctement l'URL de notification dans le Back Office PayZen ?");
define('MODULE_PAYMENT_PAYZEN_CHECK_URL_WARN_DETAIL', "Afin de comprendre la problématique, reportez vous à la documentation du module :<br />&nbsp;&nbsp;&nbsp;- Chapitre « A lire attentivement avant d'aller loin »<br />&nbsp;&nbsp;&nbsp;- Chapitre « Paramétrage de l'URL de notification ».");
define('MODULE_PAYMENT_PAYZEN_GOING_INTO_PROD_INFO', "<b>PASSAGE EN PRODUCTION :</b> Vous souhaitez savoir comment passer votre boutique en production, merci de consulter les chapitres « Procéder à la phase des tests » et « Passage d'une boutique en mode production » de la documentation du module.");

// single payment catalog messages
define('MODULE_PAYMENT_PAYZEN_STD_TITLE', "PayZen - Paiement par carte bancaire");
