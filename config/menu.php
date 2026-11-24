<?php

$dashboard = ['label' => 'Dashboard', 'icon' => 'fa-solid fa-chart-pie fa-fw', 'route' => 'dashboard'];
$filemanager = ['label' => 'File Manager', 'icon' => 'fa-solid fa-folder-tree fa-fw', 'route' => 'filemanager.index'];
$setting = ['label' => 'Settings', 'icon' => 'fa-solid fa-gear fa-fw', 'route' => 'setting.index', 'roles' => ['admin']];
$user = ['label' => 'Users', 'icon' => 'fa-solid fa-users fa-fw', 'route' => 'user.index', 'roles' => ['admin']];

return [
    'sidebar' => [
        'workspace' => ['items' => [$dashboard, $filemanager]],
        'administration' => ['items' => [$setting, $user], 'roles' => ['admin']],
    ],
];
