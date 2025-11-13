<?php
declare(strict_types=1);

const OPERATION_EXIT = 0;
const OPERATION_ADD = 1;
const OPERATION_DELETE = 2;
const OPERATION_PRINT = 3;

$operations = [
    OPERATION_EXIT => OPERATION_EXIT . '. Завершить программу.',
    OPERATION_ADD => OPERATION_ADD . '. Добавить товар в список покупок.',
    OPERATION_DELETE => OPERATION_DELETE . '. Удалить товар из списка покупок.',
    OPERATION_PRINT => OPERATION_PRINT . '. Отобразить список покупок.',
];

$items = [];


function displayMenuAndGetOperation(array $items, array $operations): int
{
    system('clear');

    displayBasket($items);
    
    echo 'Выберите операцию для выполнения: ' . PHP_EOL;
    
    $availableOperations = $operations;
    if (empty($items)) {
        unset($availableOperations[OPERATION_DELETE]);
    }
    
    echo implode(PHP_EOL, $availableOperations) . PHP_EOL . '> ';
    $operationNumber = trim(fgets(STDIN));

    return is_numeric($operationNumber) ? (int)$operationNumber : -1;
}


function displayBasket(array $items): void
{
    if (count($items)) {
        echo 'Ваш список покупок: ' . PHP_EOL;
        echo implode("\n", $items) . "\n";
    } else {
        echo 'Ваш список покупок пуст.' . PHP_EOL;
    }
}


function addProduct(array &$items): void
{
    echo "Введите название товара для добавления в список: \n> ";
    $itemName = trim(fgets(STDIN));
    
    if (!empty($itemName)) {
        $items[] = $itemName;
        echo "Товар '{$itemName}' добавлен в список." . PHP_EOL;
    } else {
        echo "Ошибка: название товара не может быть пустым." . PHP_EOL;
    }
}


function deleteProduct(array &$items): void
{
    if (empty($items)) {
        echo "Список покупок пуст. Нечего удалять." . PHP_EOL;
        return;
    }

    echo 'Текущий список покупок:' . PHP_EOL;
    displayBasket($items);

    echo 'Введите название товара для удаления из списка:' . PHP_EOL . '> ';
    $itemName = trim(fgets(STDIN));

    if (in_array($itemName, $items, true)) {
        $initialCount = count($items);
        $items = array_filter($items, function($item) use ($itemName) {
            return $item !== $itemName;
        });
        $items = array_values($items); 
        
        $removedCount = $initialCount - count($items);
        echo "Удалено товаров '{$itemName}': {$removedCount}" . PHP_EOL;
    } else {
        echo "Товар '{$itemName}' не найден в списке." . PHP_EOL;
    }
}

function printBasket(array $items): void
{
    echo 'Ваш список покупок: ' . PHP_EOL;
    displayBasket($items);
    echo 'Всего ' . count($items) . ' позиций. '. PHP_EOL;
    echo 'Нажмите enter для продолжения';
    fgets(STDIN);
}

do {
    $operationNumber = displayMenuAndGetOperation($items, $operations);

    if (!array_key_exists($operationNumber, $operations)) {
        system('clear');
        echo '!!! Неизвестный номер операции, повторите попытку.' . PHP_EOL;
        echo "\n ----- \n";
        continue;
    }

    echo 'Выбрана операция: ' . $operations[$operationNumber] . PHP_EOL;

    switch ($operationNumber) {
        case OPERATION_ADD:
            addProduct($items);
            break;

        case OPERATION_DELETE:
            deleteProduct($items);
            break;

        case OPERATION_PRINT:
            printBasket($items);
            break;
    }

    echo "\n ----- \n";
} while ($operationNumber !== OPERATION_EXIT);

echo 'Программа завершена' . PHP_EOL;