php
<?php

echo "Введите данные:\n";
$firstName = readline("Имя: ");
$lastName = readline("Фамилия: ");
$middleName = readline("Отчество: ");


function formatName($name) {
    $name = trim($name);
    if (strlen($name) === 0) return '';
    
    $firstChar = substr($name, 0, 2); 
    $rest = substr($name, 2);
    
    $upperFirst = '';
    if (preg_match('/[а-я]/iu', $firstChar)) {
        $upperFirst = strtoupper($firstChar);
    } else {
        $upperFirst = strtoupper(substr($name, 0, 1));
        $rest = substr($name, 1);
    }
    
    return $upperFirst . strtolower($rest);
}

function getFirstLetter($name) {
    if (strlen($name) === 0) return '';
    
    $firstChar = substr($name, 0, 2);
    if (preg_match('/[а-я]/iu', $firstChar)) {
        return strtoupper($firstChar);
    } else {
        return strtoupper(substr($name, 0, 1));
    }
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