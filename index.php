<?php

    ###############
	# ENVIRONMENT #
	###############
	define('ENVIRONMENT', 'dev');
	define('FROM_WEB', true);
	require_once(__DIR__ . '/descartes/load-environment.php');

	############
	# SESSIONS #
	############
	session_name(SESSION_NAME);
	session_start();

	//On creé le csrf token si il n'existe pas
	if (!isset($_SESSION['csrf']))
	{
		$_SESSION['csrf'] = str_shuffle(uniqid().uniqid());
	}

	##############
	# INCLUSIONS #
	##############
	require_once(PWD . '/descartes/autoload.php');
	require_once(PWD . '/vendor/autoload.php');
	require_once(PWD . '/routes.php');

	#########
	# MODEL #
    #########
   
    $bdd = Model::connect(DATABASE_HOST, DATABASE_NAME, DATABASE_USER, DATABASE_PASSWORD);
    $modelSetting = new \models\Setting($bdd);
    
    //On va ajouter les réglages globaux de RaspiSMS modifiables via l'interface
    $settings = $modelSetting->get_list(false, false);
	foreach ($settings as $setting)
	{
		define('RASPISMS_SETTINGS_' . mb_convert_case($setting['name'],  MB_CASE_UPPER), $setting['value']);
	}
    

	###########
	# ROUTAGE #
	###########
	//Partie gérant l'appel des controlleurs
	$router = new Router($_SERVER['REQUEST_URI'], $descartesRoutes);
	$router->callRouterForUrl($router->getCallUrl(), $router->getRoutes());

