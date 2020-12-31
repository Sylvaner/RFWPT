<?php
/**
 * Fonctions accessibles à l'ensemble du thème
 */
require_once 'libs/PostManager.php';
require_once 'libs/ThemeManager.php';
require_once 'libs/Menus.php';

function start_init()
{
    ThemeManager::getInstance();
}

add_action('init', 'start_init');
