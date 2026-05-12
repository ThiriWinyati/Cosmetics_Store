<?php

$hash_code = password_hash("Thiri@2004!", PASSWORD_BCRYPT);
echo $hash_code;
echo "<br>" . strlen($hash_code);

$hash_code1 = password_hash("ChawNadi@2003!", PASSWORD_BCRYPT);
echo "<br>" . $hash_code1;
echo "<br>" . strlen($hash_code1);

$hash_code2 = password_hash("LynnPa@2003!", PASSWORD_BCRYPT);
echo "<br>" . $hash_code2;
echo "<br>" . strlen($hash_code2);


?>