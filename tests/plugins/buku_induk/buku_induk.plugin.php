<?php
/**
 * Plugin Name: buku_induk
 * Plugin URI: https://github.com/drajathasan/buku_induk
 * Description: Dummy plugin
 * Version: 1.0.0
 * Author: Drajat Hasan
 * Author URI: https://github.com/drajathasan/
 */

// get plugin instance
$plugin = \SLiMS\Plugins::getInstance();

// registering menus
$plugin->registerMenu('bibliography', 'Buku Induk', __DIR__ . '/index.php');
