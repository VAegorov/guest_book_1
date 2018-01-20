<?php
$a1 = range(1,20);
$a2 = array_chunk($a1, 3, true);
$a3 = array_chunk($a2, 2, true);

echo "<pre>";
var_dump($a3);
echo "</pre>";

echo "<hr>";
echo $a3[1][2][8];

