#!/usr/bin/php
<?php

    ###############
	# ENVIRONMENT #
	###############
	define('ENVIRONMENT', 'dev');
	define('FROM_WEB', true);
	require_once(__DIR__ . '/descartes/load-environment.php');

	##############
	# INCLUSIONS #
	##############
	require_once(PWD . '/descartes/autoload.php');
	require_once(PWD . '/vendor/autoload.php');
	require_once(PWD . '/descartes/Console.php');
	require_once(PWD . '/routes.php');

	#########
	# MODEL #
	#########
	//On va appeler un modèle, est l'initialiser
	$bdd = Model::connect(DATABASE_HOST, DATABASE_NAME, DATABASE_USER, DATABASE_PASSWORD);
    
    $modelSettings = new \models\Settings($bdd);
    
    //On va ajouter les réglages globaux de RaspiSMS modifiables via l'interface
    $settings = $modelSettings->get_list(false, false);
	foreach ($settings as $setting)
	{
		define('RASPISMS_SETTINGS_' . mb_convert_case($setting['name'],  MB_CASE_UPPER), $setting['value']);
	}
    

	###########
	# ROUTAGE #
	###########
	//Partie gérant l'appel des controlleurs
    $console = new \Console($argv);
    $console->executeCommand($console->getCommand());

