<?php

$menu = [


    'menu' => [[
		'icon' => 'fa fa-sitemap',
		'title' => 'Home',
		'url' => '/',
		'route-name' => 'home'
	],
        [
            'icon' => 'fa fa-download',
            'title' => 'Download Queue',
            'url' => '/downloadQueue',
            'route-name' => 'downloadQueue'
        ],
        [
            'icon' => 'fa fa-notes-medical',
            'title' => 'Log Center',
            'url' => '/logCenter',
            'route-name' => 'logCenter'
        ]
    ]
];




return $menu;