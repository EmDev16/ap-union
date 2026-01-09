<?php

echo "Setting up AP Union project...\n";

// Create symbolic link for storage (needed for avatar/images)
passthru('php artisan storage:link');

echo "Setup complete!\n";
?>
