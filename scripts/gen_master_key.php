<?php
/**
 * Generates a random 32-byte key and outputs it as a base64 string.
 * This should be used for APP_MASTER_KEY in .env.
 */

$key = random_bytes(32);
$base64Key = base64_encode($key);

echo "--- BMIKollen Master Key Generator ---\n";
echo "Copy the following line to your .env file:\n\n";
echo "APP_MASTER_KEY=base64:{$base64Key}\n\n";
echo "KEEP THIS KEY SECRET! If you lose it, you cannot decrypt weight data.\n";
