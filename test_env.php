<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>GymForge Environment Test</h1>";

// Test PHP version
echo "<h2>PHP Version:</h2>";
echo PHP_VERSION;

// Server Information
echo "<h2>Server Information:</h2>";
echo "<pre>";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Script Filename: " . $_SERVER['SCRIPT_FILENAME'] . "\n";
echo "HTTP Host: " . $_SERVER['HTTP_HOST'] . "\n";
echo "Server Name: " . $_SERVER['SERVER_NAME'] . "\n";
echo "Request URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "</pre>";

// Apache Modules (if available)
if (function_exists('apache_get_modules')) {
    echo "<h2>Apache Modules:</h2>";
    echo "<pre>";
    print_r(apache_get_modules());
    echo "</pre>";
}

// PHP loaded extensions
echo "<h2>PHP Extensions:</h2>";
echo "<pre>";
print_r(get_loaded_extensions());
echo "</pre>";

// File permissions
echo "<h2>File Permissions:</h2>";
echo "<pre>";
echo "Current file: " . __FILE__ . " - Permissions: " . substr(sprintf('%o', fileperms(__FILE__)), -4) . "\n";
echo "Parent directory: " . dirname(__FILE__) . " - Permissions: " . substr(sprintf('%o', fileperms(dirname(__FILE__))), -4) . "\n";
echo "</pre>";
?> 