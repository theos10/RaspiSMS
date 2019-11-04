<?php
	/*
		Ce fichier défini les constantes du MVC
	*/

	//On définit les chemins
    define('PWD', '/var/www/html/RaspiSMS/'); //On définit le chemin de base du site
	define('HTTP_ROOT', '/'); //On définit la racine d'accès (selon nore vhost)
	define('HTTP_PORT', '443'); //On définit le port sur lequel est raspisms
	define('HTTP_PWD', (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost') . ( ((HTTP_PORT != 443 AND isset($_SERVER['HTTPS'])) OR (HTTP_PORT != 80 AND !isset($_SERVER['HTTPS']))) ? ':'. HTTP_PORT : '') . HTTP_ROOT); //On défini l'adresse url du site

	define('PWD_IMG', PWD . 'img/'); //Chemin dossier des images
	define('HTTP_PWD_IMG', HTTP_PWD . 'img/'); //URL dossier des images

	define('PWD_CSS', PWD . 'css/'); //Chemin dossier des css
	define('HTTP_PWD_CSS', HTTP_PWD . 'css/'); //URL dossier des css

	define('PWD_JS', PWD . 'js/'); //Chemin dossier des js
	define('HTTP_PWD_JS', HTTP_PWD . 'js/'); //URL dossier des js

	define('PWD_CONTROLLER', PWD . 'controllers/'); //Dossier des controllers
	define('PWD_MODEL', PWD . 'model/'); //Dossier des models
	define('PWD_TEMPLATES', PWD . 'templates/'); //Dossier des templates
	
	define('PWD_SCRIPTS', PWD . 'scripts/'); //URL dossier des scripts appelables via les commandes
	define('PWD_RECEIVEDS', PWD . 'receiveds/'); //URL dossier des sms reçus via les commandes


	//On défini les controlleurs et methodes par défaut
	define('DEFAULT_CONTROLLER', 'dashboard'); //Nom controller appelé par défaut
	define('DEFAULT_METHOD', 'byDefault'); //Nom méthode appelée par défaut
	define('DEFAULT_BEFORE', 'before'); //Nom méthode before par défaut

	// Commande shell
	define('CMD_SIGNAL',"/usr/bin/gammu-smsd-monitor -n 1 -d 0 | grep NetworkSignal | awk '{ print $2 }'");//Commande de recuperation du NetworkSignal

	//Réglages des logs
	define('LOG_ACTIVATED', 1); //On active les logs

	//Réglages du cache
	define('ACTIVATING_CACHE', false); //On desactive le cache

	//Réglages divers
	define('WEBSITE_TITLE', 'RaspiSMS'); //Le titre du site
	define('WEBSITE_DESCRIPTION', ''); //Description du site
	define('WEBSITE_KEYWORDS', ''); //Mots clefs du site
	define('WEBSITE_AUTHOR', 'Raspbian-France'); //Auteur du site

	//Réglages des identifiants de base de données
	define('DATABASE_HOST', 'localhost'); //Hote de la bdd
	define('DATABASE_NAME', 'raspisms'); //Nom de la bdd
	define('DATABASE_USER', 'raspisms'); //Utilisateur de la bdd
	define('DATABASE_PASSWORD', 'raspisms'); //Password de l'utilisateur
