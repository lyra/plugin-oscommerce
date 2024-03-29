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

// Administration interface - general information.
define('MODULE_PAYMENT_PAYZEN_MODULE_INFORMATION', "INFORMATIONS SUR LE MODULE");
define('MODULE_PAYMENT_PAYZEN_DEVELOPED_BY', "Développé par : ");
define('MODULE_PAYMENT_PAYZEN_CONTACT_EMAIL', "Courriel de contact : ");
define('MODULE_PAYMENT_PAYZEN_CONTRIB_VERSION', "Version du module : ");
define('MODULE_PAYMENT_PAYZEN_GATEWAY_VERSION', "Version de la plateforme : ");
define('MODULE_PAYMENT_PAYZEN_DOCUMENTATION_URL', "Cliquer pour accéder à la documentation de configuration du module: ");

// Administration interface - module settings.
define('MODULE_PAYMENT_PAYZEN_STATUS_TITLE', "Activation");
define('MODULE_PAYMENT_PAYZEN_STATUS_DESC', "Activer / désactiver cette méthode de paiement.");
define('MODULE_PAYMENT_PAYZEN_SORT_ORDER_TITLE', "Ordre d'affichage");
define('MODULE_PAYMENT_PAYZEN_SORT_ORDER_DESC', "Dans la liste des moyens de paiement.");
define('MODULE_PAYMENT_PAYZEN_ZONE_TITLE', "Zone de paiement");
define('MODULE_PAYMENT_PAYZEN_ZONE_DESC', "Si une zone est choisie, ce mode de paiement ne sera effectif que pour celle-ci.");

// Administration interface - gateway settings.
define('MODULE_PAYMENT_PAYZEN_SITE_ID_TITLE', "Identifiant de la boutique");
define('MODULE_PAYMENT_PAYZEN_SITE_ID_DESC', "Identifiant fourni par PayZen.");
define('MODULE_PAYMENT_PAYZEN_KEY_TEST_TITLE', "Clé en mode test");
define('MODULE_PAYMENT_PAYZEN_KEY_TEST_DESC', "Clé fournie par PayZen pour le mode test (disponible sur le Back Office PayZen).");
define('MODULE_PAYMENT_PAYZEN_KEY_PROD_TITLE', "Clé en mode production");
define('MODULE_PAYMENT_PAYZEN_KEY_PROD_DESC', "Clé fournie par PayZen (disponible sur le Back Office PayZen après passage en production).");
define('MODULE_PAYMENT_PAYZEN_CTX_MODE_TITLE', "Mode");
define('MODULE_PAYMENT_PAYZEN_CTX_MODE_DESC', "Mode de fonctionnement du module.");
define('MODULE_PAYMENT_PAYZEN_SIGN_ALGO_TITLE', "Algorithme de signature");
define('MODULE_PAYMENT_PAYZEN_SIGN_ALGO_DESC', "Algorithme utilisé pour calculer la signature du formulaire de paiement. L'algorithme sélectionné doit être le même que celui configuré sur le Back Office PayZen." . (! $payzen_plugin_features['shwatwo'] ? "Le HMAC-SHA-256 ne doit pas être activé si celui-ci n'est pas encore disponible depuis le Back Office PayZen." : ''));
define('MODULE_PAYMENT_PAYZEN_PLATFORM_URL_TITLE', "URL de la page de paiement");
define('MODULE_PAYMENT_PAYZEN_IPN_URL_TITLE', "URL de notification");
define('MODULE_PAYMENT_PAYZEN_IPN_URL_DESC', "URL à copier dans le Back Office PayZen > Paramétrage > Règles de notifications.<br />En mode multi-boutique, l'URL de notification est la même pour toutes les boutiques.");
define('MODULE_PAYMENT_PAYZEN_PLATFORM_URL_DESC', "URL vers laquelle l'acheteur sera redirigé pour le paiement.");

// Administration interface - rest api keys.
define('MODULE_PAYMENT_PAYZEN_REST_API_KEYS_TITLE', "CLÉS D'API REST");
define('MODULE_PAYMENT_PAYZEN_REST_API_KEYS_DESC', "Les clés de l'API REST sont disponibles dans votre Back Office PayZen (menu: Paramétrage > Boutiques > Clés d'API REST).");
define('MODULE_PAYMENT_PAYZEN_PRIV_TEST_KEY_TITLE', "Mot de passe de test");
define('MODULE_PAYMENT_PAYZEN_PRIV_PROD_KEY_TITLE', "Mot de passe de production");
define('MODULE_PAYMENT_PAYZEN_REST_URL_TITLE', "URL du serveur de l'API REST");
define('MODULE_PAYMENT_PAYZEN_PUB_TEST_KEY_TITLE', "Clé publique de test");
define('MODULE_PAYMENT_PAYZEN_PUB_PROD_KEY_TITLE', "Clé publique de production");
define('MODULE_PAYMENT_PAYZEN_HMAC_TEST_KEY_TITLE', "Clé HMAC-SHA-256 de test");
define('MODULE_PAYMENT_PAYZEN_HMAC_PROD_KEY_TITLE', "Clé HMAC-SHA-256 de production");
define('MODULE_PAYMENT_PAYZEN_REST_IPN_URL_TITLE', "URL de notification de l'API REST");
define('MODULE_PAYMENT_PAYZEN_REST_IPN_URL_DESC', "URL à copier dans le Back Office PayZen > Paramétrage > Règles de notifications.<br />En mode multi-boutique, l'URL de notification est la même pour toutes les boutiques.");
define('MODULE_PAYMENT_PAYZEN_STATIC_URL_TITLE', "URL du client JavaScript");

// Administration interface - advanced options
define('MODULE_PAYMENT_PAYZEN_ADVANCED_OPTIONS_TITLE', "OPTIONS AVANCÉES");
define('MODULE_PAYMENT_PAYZEN_CARD_DATA_ENTRY_MODE_TITLE', "Mode de saisie des données de paiement");
define('MODULE_PAYMENT_PAYZEN_CARD_DATA_ENTRY_MODE_DESC', "Sélectionner la manière dont seront saisies les données de paiement. Attention, pour utiliser le Smartform, vous devez vous assurer d'avoir souscrit à cette option auprès de PayZen.");
define('MODULE_PAYMENT_PAYZEN_REST_POPIN_MODE_TITLE', "Afficher dans une pop-in");
define('MODULE_PAYMENT_PAYZEN_REST_POPIN_MODE_DESC', "Cette option permet d'afficher le Smartform dans une pop-in.");
define('MODULE_PAYMENT_PAYZEN_REST_THEME_TITLE', "Thème");
define('MODULE_PAYMENT_PAYZEN_REST_THEME_DESC', "Sélectionnez un thème à utiliser pour afficher le Smartform.");
define('MODULE_PAYMENT_PAYZEN_REST_COMPACT_MODE_TITLE', "Mode compact");
define('MODULE_PAYMENT_PAYZEN_REST_COMPACT_MODE_DESC', "Cette option permet d'afficher le Smartform en mode compact.");
define('MODULE_PAYMENT_PAYZEN_REST_THRESHOLD_TITLE', "Seuil de regroupement des moyens de paiement");
define('MODULE_PAYMENT_PAYZEN_REST_THRESHOLD_DESC', "Nombre de moyens de paiement à partir duquel ils vont être regroupés.");
define('MODULE_PAYMENT_PAYZEN_REST_ATTEMPTS_TITLE', "Nombre de tentatives de paiement par cartes");
define('MODULE_PAYMENT_PAYZEN_REST_ATTEMPTS_DESC', "Nombre maximum de tentatives de paiement par cartes après un paiement en échec (entre 0 et 2). Si vide, la valeur par défaut de la plateforme est 2.");

// Administration interface - payment settings.
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_TITLE', "Langue par défaut");
define('MODULE_PAYMENT_PAYZEN_LANGUAGE_DESC', "Sélectionner la langue par défaut à utiliser sur la page de paiement.");
define('MODULE_PAYMENT_PAYZEN_AVAILABLE_LANGUAGES_TITLE', "Langues disponibles");
define('MODULE_PAYMENT_PAYZEN_AVAILABLE_LANGUAGES_DESC', "Sélectionner les langues à proposer sur la page de paiement. Ne rien sélectionner pour utiliser la configuration de la plateforme.");
define('MODULE_PAYMENT_PAYZEN_CAPTURE_DELAY_TITLE', "Délai avant remise en banque");
define('MODULE_PAYMENT_PAYZEN_CAPTURE_DELAY_DESC', "Le nombre de jours avant la remise en banque (paramétrable sur votre Back Office PayZen).");
define('MODULE_PAYMENT_PAYZEN_VALIDATION_MODE_TITLE', "Mode de validation");
define('MODULE_PAYMENT_PAYZEN_VALIDATION_MODE_DESC', "En mode manuel, vous devrez confirmer les paiements dans le Back Office PayZen.");
define('MODULE_PAYMENT_PAYZEN_PAYMENT_CARDS_TITLE', "Types de carte");
define('MODULE_PAYMENT_PAYZEN_PAYMENT_CARDS_DESC', "Le(s) type(s) de carte pouvant être utilisé(s) pour le paiement. Ne rien sélectionner pour utiliser la configuration de la plateforme.");
define('MODULE_PAYMENT_PAYZEN_3DS_MIN_AMOUNT_TITLE', "Gérer le 3DS");
define('MODULE_PAYMENT_PAYZEN_3DS_MIN_AMOUNT_DESC', "Montant en dessous duquel l'acheteur pourrait être exempté de l'authentification forte. Nécessite la souscription à l'option «Selective 3DS1» ou l'option  «Frictionless 3DS2». Pour plus d'informations, reportez-vous à la documentation du module.");

// Administration interface - amount restrictions settings.
define('MODULE_PAYMENT_PAYZEN_MIN_AMOUNT_TITLE', "Montant minimum");
define('MODULE_PAYMENT_PAYZEN_MIN_AMOUNT_DESC', "Montant minimum pour lequel cette méthode de paiement est disponible.");
define('MODULE_PAYMENT_PAYZEN_MAX_AMOUNT_TITLE', "Montant maximum");
define('MODULE_PAYMENT_PAYZEN_MAX_AMOUNT_DESC', "Montant maximum pour lequel cette méthode de paiement est disponible.");

// Administration interface - return to shop settings.
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
define('MODULE_PAYMENT_PAYZEN_ORDER_STATUS_DESC', "Statut des commandes dont le paiement a réussi.");

// Administration interface - misc constants.
define('MODULE_PAYMENT_PAYZEN_VALUE_0', "Désactivé");
define('MODULE_PAYMENT_PAYZEN_VALUE_1', "Activé");

define('MODULE_PAYMENT_PAYZEN_VALIDATION_DEFAULT', "Configuration Back Office PayZen");
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

define('MODULE_PAYMENT_PAYZEN_REDIRECT_SUCCESS_MESSAGE', "Redirection vers la boutique dans quelques instants...");
define('MODULE_PAYMENT_PAYZEN_REDIRECT_ERROR_MESSAGE', "Redirection vers la boutique dans quelques instants...");

define('MODULE_PAYMENT_PAYZEN_CARD_DATA_ENTRY_MODE_FORM', "Acquisition des données sur la plateforme de paiement");
define('MODULE_PAYMENT_PAYZEN_CARD_DATA_ENTRY_MODE_SMARTFORM', "Smartform embarqué sur le site marchand (API REST)");
define('MODULE_PAYMENT_PAYZEN_CARD_DATA_ENTRY_MODE_SMARTFORM_EXT_WITH_LOGOS', "Smartform étendu embarqué sur le site marchand avec les logos (API REST)");
define('MODULE_PAYMENT_PAYZEN_CARD_DATA_ENTRY_MODE_SMARTFORM_EXT_WITHOUT_LOGOS', "Smartform étendu embarqué sur le site marchand sans les logos (API REST)");

// Catalog messages.
define('MODULE_PAYMENT_PAYZEN_TECHNICAL_ERROR', "Une erreur est survenue dans le processus de paiement.");
define('MODULE_PAYMENT_PAYZEN_PAYMENT_ERROR', "Votre paiement n'a pas été accepté. Veuillez repasser votre commande.");
define('MODULE_PAYMENT_PAYZEN_CHECK_URL_WARN', "La validation automatique n'a pas fonctionné. Avez-vous configuré correctement l'URL de notification dans le Back Office PayZen ?");
define('MODULE_PAYMENT_PAYZEN_CHECK_URL_WARN_DETAIL', "Afin de comprendre la problématique, reportez vous à la documentation du module :<br />&nbsp;&nbsp;&nbsp;- Chapitre « A lire attentivement avant d'aller loin »<br />&nbsp;&nbsp;&nbsp;- Chapitre « Paramétrage de l'URL de notification ».");
define('MODULE_PAYMENT_PAYZEN_GOING_INTO_PROD_INFO', "<b>PASSAGE EN PRODUCTION :</b> Vous souhaitez savoir comment passer votre boutique en production, merci de consulter les chapitres « Procéder à la phase des tests » et « Passage d'une boutique en mode production » de la documentation du module.");

// Single payment catalog messages.
define('MODULE_PAYMENT_PAYZEN_STD_TITLE', "PayZen - Paiement par carte bancaire");