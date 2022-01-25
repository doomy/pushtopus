<!DOCTYPE html>
<?php

use Doomy\Pushtopus\Configuration;

error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'vendor/autoload.php';

$config = Configuration::getFromJSONFile(__DIR__ . '/config.local.json');

require "example.tpl.php";

?>

