<?php

if (!extension_loaded('mbstring')) {
    echo "Внимание: расширение mbstring не установлено. Установите его для корректной работы с кириллицей.\n";
}

echo "Введите данные:\n";
$firstName = readline("Имя: ");
$lastName = readline("Фамилия: ");
$middleName = readline("Отчество: ");

function formatName($name) {
    $name = trim($name);
    if (mb_strlen($name, 'UTF-8') === 0) return '';
    
    // Приводим к нижнему регистру, затем первую букву к верхнему
    $name = mb_strtolower($name, 'UTF-8');
    return mb_strtoupper(mb_substr($name, 0, 1, 'UTF-8'), 'UTF-8') . 
           mb_substr($name, 1, null, 'UTF-8');
}

function getFirstLetter($name) {
    if (mb_strlen($name, 'UTF-8') === 0) return '';
    return mb_strtoupper(mb_substr($name, 0, 1, 'UTF-8'), 'UTF-8');
}

$formattedLastName = formatName($lastName);
$formattedFirstName = formatName($firstName);
$formattedMiddleName = formatName($middleName);

$fullName = $formattedLastName . ' ' . $formattedFirstName . ' ' . $formattedMiddleName;

$surnameAndInitials = $formattedLastName . ' ' . 
                     getFirstLetter($formattedFirstName) . '.' . 
                     getFirstLetter($formattedMiddleName) . '.';

$fio = getFirstLetter($formattedLastName) . 
       getFirstLetter($formattedFirstName) . 
       getFirstLetter($formattedMiddleName);

echo "\nРезультаты:\n";
echo "Полное имя: '$fullName'\n";
echo "Фамилия и инициалы: '$surnameAndInitials'\n";
echo "Аббревиатура: '$fio'\n";
?>