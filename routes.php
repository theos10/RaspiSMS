<?php
	$descartesRoutes = array(
		'Connect' => [
            'login' => '/',
            'connection' => '/connection/',
        ],

        'Dashboard' => [
            'show' => '/dashboard/',
        ],

        'Account' => [
            'list' => '/account/',
        ],

        'Commands' => [
            'list' => '/commands/',
        ],

        'Contacts' => [
            'list' => '/contacts/',
            'json_list' => '/contacts.json/',
        ],

        'Discussions' => [
            'list' => '/discussions/',
        ],

        'Events' => [
            'list' => '/events/',
        ],

        'Groups' => [
            'list' => '/groups/',
            'json_list' => '/groups.json/',
        ],

        'Receiveds' => [
            'list' => '/receiveds/',
        ],

        'Scheduleds' => [
            'list' => '/scheduleds/',
            'add' => '/scheduleds/add/',
            'create' => '/scheduleds/create/{csrf}/',
            'edit' => '/scheduleds/edit/',
            'update' => '/scheduleds/update/{csrf}/',
            'delete' => '/scheduleds/delete/{csrf}/',
        ],

        'Sendeds' => [
            'list' => '/sendeds/',
        ],

        'Settings' => [
            'show' => '/settings/',
        ],

        'SMSStops' => [
            'list' => '/smsstops/',
        ],

        'Users' => [
            'list' => '/users/',
        ],
	);
