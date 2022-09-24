<?php
$value = 'something from somewhere';

setcookie("teste", $value, time()+3600);

echo $_COOKIE['teste'];

