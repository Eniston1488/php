<?php

function divisionScript() {
    fwrite(STDOUT, "Введите первое число: ");
    $num1_str = trim(fgets(STDIN));
    fwrite(STDOUT, "Введите второе число: ");
    $num2_str = trim(fgets(STDIN));
    if (!is_numeric($num1_str) || !is_numeric($num2_str)) {
        fwrite(STDERR, "Введите, пожалуйста, число" . PHP_EOL);
        return 1;
    }
    $num1 = (int)$num1_str;
    $num2 = (int)$num2_str;
    if ($num2 == 0) {
        fwrite(STDERR, "Делить на 0 нельзя" . PHP_EOL);
        return 1;
    }
    
    $result = $num1 / $num2;
    fwrite(STDOUT, "Результат деления: " . $result . PHP_EOL);
    return 0;
}
exit(divisionScript());

?>
