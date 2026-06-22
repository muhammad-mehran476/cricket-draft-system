<?php
$path = __DIR__ . '/../app/Models/Team.php';
$lines = file($path);
for ($i = 27; $i <= 36; $i++) {
    if (!isset($lines[$i-1])) continue;
    $line = $lines[$i-1];
    echo sprintf("%02d: %s\n", $i, trim(bin2hex($line)));
}
