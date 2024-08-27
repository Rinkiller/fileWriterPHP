<?php
require_once('cli/code/vendor/autoload.php');

// вызов корневой функции
$result = main("cli/code/config.ini");
// вывод результата
echo $result;
// docker run --rm -v ${pwd}/:/cli php:8.2-cli php cli/code/app.php read-all