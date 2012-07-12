<?php


define('IP_PHP_VERSION', 'Version PHP >= 5.3');
define('IP_MOD_REWRITE', 'Module Apache "mod_rewrite"');
define('IP_GD_LIB', 'Librairie Graphique GD');
define('IP_MAGIC_QUOTES', '"Magic Quotes" désactivés (recommandé)');

define('IP_OK', 'OK');
define('IP_ERROR', 'Non');
define('IP_WRITABLE', 'inscriptible');
define('IP_CHECK_AGAIN', 'Revérifier');
define('IP_BACK', 'Précédent');
define('IP_NEXT', 'Suivant');
define('IP_ACCEPT', 'Accepter');
define('IP_INSTALLATION', 'Installateur du CMS/GED ImpressPages');
define('IP_VERSION', 'Version '.TARGET_VERSION);
define('IP_SUBDIRECTORIES', '(y compris sous-dossiers et fichiers)');
define('IP_OPTIONAL', '(optionnel)');



define('IP_STEP_LANGUAGE', 'Choix de la langue');
define('IP_STEP_CHECK', 'Vérification du Système');
define('IP_STEP_LICENSE', 'Licence');
define('IP_STEP_DB', 'Base de Données');
define('IP_STEP_CONFIGURATION', 'Configuration');
define('IP_STEP_COMPLETED', 'Fin');
define('IP_STEP_LANGUAGE_LONG', 'Choix de la langue de l\'interface');
define('IP_STEP_CHECK_LONG', 'Vérification du Système');
define('IP_STEP_LICENSE_LONG', 'Informations Juridiques - ImpressPages');
define('IP_STEP_DB_LONG', 'Configuration de la Base de Données');
define('IP_STEP_CONFIGURATION_LONG', 'Configuration Système');
define('IP_STEP_COMPLETED_LONG', 'ImpressPages a été installé avec succès.');





define('IP_DB_SERVER', 'Base de données (e.g. localhost)');
define('IP_DB_USER', 'Nom de l\'utilisateur');
define('IP_DB_PASS', 'Mot de Passe de l\'utilisateur');
define('IP_DB_DB', 'Nom de la Base de Données');
define('IP_DB_PREFIX', 'Préfixe des tables (utilisez le _ pour séparer le préfixe).');
define('IP_DB_DATA_WARNING', 'Attention !!! Toutes les anciennes tables avec le même préfixe seront supprimées !');
define('IP_DB_ERROR_ALL_FIELDS', 'Remplissez tous les champs');
define('IP_DB_ERROR_CONNECT', 'Impossible de se connecter à la Base de Données');
define('IP_DB_ERROR_DB', 'Cette Base de Données n\'existe pas');
define('IP_DB_ERROR_QUERY', 'Erreur SQL inconnue');
define('IP_DB_ERROR_LONG_PREFIX', 'Le préfixe doit comporter au maximum 7 caractères');
define('IP_DB_ERROR_INCORRECT_PREFIX', 'Caractères spéciaux non autorisés dans le préfixe et il doit commencer par une lettre');
define('IP_DB_ERROR_EMAIL', 'Courriel/e-mail incorrect'); 
define('IP_CONFIG_ERROR_CONFIG', 'Impossible de créer le fichier "/ip_config.php"');
define('IP_CONFIG_ERROR_ROBOTS', 'Impossible de créer le fichier "/robots.txt"');
define('IP_CONFIG_ERROR_EMAIL', 'Entrez un courriel/e-mail valide pour l\'administrateur.');
define('IP_CONFIG_ERROR_SITE_NAME', 'Entrez le nom du site.');
define('IP_CONFIG_ERROR_SITE_EMAIL', 'Entrez un courriel/e-mail correct pour le site.');
define('IP_CONFIG_ERROR_LOGIN', 'Entrez le nom de l\'Administrateur et son Mot de Passe.');
define('IP_CONFIG_ERROR_TIME_ZONE', 'Choisissez le fuseau horaire du site.');


define('IP_CONFIG_SITE_NAME', 'Nom du site');
define('IP_CONFIG_SITE_EMAIL', 'Courriel/e-mail du site');
define('IP_CONFIG_EMAIL', 'Courriel/e-mail où envoyer les erreurs (optionnel)');
define('IP_CONFIG_LOGIN', 'Nom de l\'Administrateur');
define('IP_CONFIG_PASS', 'Mot de Passe de l\'Administrateur');
define('IP_CONFIG_TIMEZONE', 'Fuseau horaire du site');
define('IP_CONFIG_SELECT_TIMEZONE', 'Choisissez un fuseau horaire pour le site');

define('IP_FINISH_MESSAGE', '
<p>
<a href="../">Page d\'accueil</a>
</p>
<p>
<a href="../admin.php">Page Administrateur</a><br /><br />
</p>
<p>
Si vous deviez refaire l\'installation, nettoyer le fichier de configuration "ip_config.php".
</p>
');
