<?php

$pharFile = 'PocketProX.phar';

// Check if Phar extension is enabled
if (!class_exists('Phar')) {
    die("Phar extension is not enabled.\n");
}

// Remove the existing .phar file if it exists
if (file_exists($pharFile)) {
    unlink($pharFile);
}

// Create a new .phar file
try {
    $phar = new Phar($pharFile);

    // Start buffering
    $phar->startBuffering();

    // Add files to the .phar
    $phar->buildFromDirectory('./');
    
    // Add the main entry point
    $phar->setStub($phar->createDefaultStub('start.php'));

    // Stop buffering and save the .phar
    $phar->stopBuffering();

    echo "Successfully created $pharFile\n";

} catch (Exception $e) {
    echo "Could not create $pharFile: " . $e->getMessage() . "\n";
}
