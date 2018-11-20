<?php
	/*
		Ce fichier défini les constantes modifiables et les options
	*/

	//On défini l'environment
	$environment = [
		'prod' => [
			//Si vrai, on active le cache
			'ACTIVATING_CACHE' => false,

			//On défini le nom de la session
			'SESSION_NAME' => 'raspisms',

			//Configuration de la base de données
			'DATABASE_HOST' => 'localhost',
			'DATABASE_NAME' => 'raspisms',
			'DATABASE_USER' => 'root',
            'DATABASE_PASSWORD' => 'root',

            //Réglages RaspiSMS
            'WEBSITE_TITLE' => 'RaspiSMS',
            'WEBSITE_DESCRIPTION' => '',
            'WEBSITE_AUTHOR' => 'Raspbian-France',
            'PWD_SCRIPTS' => PWD . '/scripts',
            'APP_SECRET' => 'retyuijokplmrtè34567890',

            //Types des emails
            'EMAIL_RESET_PASSWORD' => [
                'type' => 'email_reset_password',
                'subject' => 'Réinitialisation de votre mot de passe',
                'template' => 'email/reset-password',  
            ],
            'EMAIL_CREATE_USER' => [
                'type' => 'email_create_user',
                'subject' => 'Création de votre compte RaspiSMS',
                'template' => 'email/create-user',  
            ],
            
		],
		'dev' => [
			//Si vrai, on active le cache
			'ACTIVATING_CACHE' => false,

			//On défini le nom de la session
			'SESSION_NAME' => 'raspisms',

			//Configuration de la base de données
			'DATABASE_HOST' => 'localhost',
			'DATABASE_NAME' => 'raspisms',
			'DATABASE_USER' => 'root',
			'DATABASE_PASSWORD' => 'root',
            
            //Réglages RaspiSMS
            'WEBSITE_TITLE' => 'RaspiSMS',
            'WEBSITE_DESCRIPTION' => '',
            'WEBSITE_AUTHOR' => 'Raspbian-France',
            'APP_SECRET' => 'retyuijokplmrtè34567890',
            'PWD_SCRIPTS' => PWD . '/scripts',

            //Types des emails
            'EMAIL_RESET_PASSWORD' => [
                'type' => 'email_reset_password',
                'subject' => 'Réinitialisation de votre mot de passe',
                'template' => 'emails/reset-password',  
            ],
            'EMAIL_CREATE_USER' => [
                'type' => 'email_create_user',
                'subject' => 'Création de votre compte RaspiSMS',
                'template' => 'email/create-user',  
            ],
		],
		'test' => [
			//Si vrai, on active le cache
			'ACTIVATING_CACHE' => false,

			//On défini le nom de la session
			'SESSION_NAME' => 'raspisms',

			//Configuration de la base de données
			'DATABASE_HOST' => 'localhost',
			'DATABASE_NAME' => 'raspisms',
			'DATABASE_USER' => 'root',
			'DATABASE_PASSWORD' => 'root',
        
            //Réglages RaspiSMS
            'WEBSITE_TITLE' => 'RaspiSMS',
            'WEBSITE_DESCRIPTION' => '',
            'WEBSITE_AUTHOR' => 'Raspbian-France',
            'APP_SECRET' => 'retyuijokplmrtè34567890',
            'PWD_SCRIPTS' => PWD . '/scripts',

            //Types des emails
            'EMAIL_RESET_PASSWORD' => [
                'type' => 'email_reset_password',
                'subject' => 'Réinitialisation de votre mot de passe',
                'template' => 'email/reset-password',  
            ],
            'EMAIL_CREATE_USER' => [
                'type' => 'email_create_user',
                'subject' => 'Création de votre compte RaspiSMS',
                'template' => 'email/create-user',  
            ],
        ]
	];

