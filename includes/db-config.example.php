<?php
return [
    'host' => 'localhost',
    'name' => 'cpaneluser_database',
    'user' => 'cpaneluser_dbuser',
    'password' => 'change-this-password',
    'charset' => 'utf8mb4',
    // Activar solo durante una instalación o migración; evita DDL en cada request.
    'auto_migrate' => false,
];
