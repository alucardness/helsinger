<?php

/* ------------------------------------------------
 * Register the Auto Loader
 * ------------------------------------------------
 *
 * Composer is a dependency management tool for PHP.
 * It allows you to declare the libraries or packages your project
 * depends on and manages the installation and loading of those dependencies.
 */

require_once __DIR__ . "/../vendor/autoload.php";

/* ------------------------------------------------
 * Run Application
 * ------------------------------------------------
 *
 * This file serves as the entry point for the application. It initializes the
 * necessary components, sets up the environment, and bootstraps the application.
 * Any global configurations or setup tasks should be performed here.
 */

$app = require_once __DIR__ . "/../bootstrap/app.php";