<?php
$string = 'Hành Ð?ng';
$encoded = json_encode($string);
$escaped = trim($encoded, '"');
echo "Original: $string\n";
echo "Encoded: $encoded\n";
echo "Escaped: $escaped\n";
