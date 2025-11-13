<?php
declare(strict_types=1);

const OPERATION_EXIT = 0;
const OPERATION_ADD = 1;
const OPERATION_DELETE = 2;
const OPERATION_EDIT = 3;
const OPERATION_PRINT = 4;

$operations = [
    OPERATION_EXIT => OPERATION_EXIT . '. Завершить программу.',
    OPERATION_ADD => OPERATION_ADD . '. Добавить товар в список покупок.',
    OPERATION_DELETE => OPERATION_DELETE . '. Удалить товар из списка покупок.',
    OPERATION_EDIT => OPERATION_EDIT . '. Изменить товар в списке покупок.',
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
        unset($availableOperations[OPERATION_EDIT]);
    }
    
    echo implode(PHP_EOL, $availableOperations) . PHP_EOL . '> ';
    $operationNumber = trim(fgets(STDIN));

    return is_numeric($operationNumber) ? (int)$operationNumber : -1;
}

function displayBasket(array $items): void
{
    if (count($items)) {
        echo 'Ваш список покупок: ' . PHP_EOL;
        foreach ($items as $index => $item) {
            $quantity = isset($item['quantity']) ? " ({$item['quantity']} шт.)" : '';
            echo ($index + 1) . ". {$item['name']}{$quantity}" . PHP_EOL;
        }
    } else {
        echo 'Ваш список покупок пуст.' . PHP_EOL;
    }
}


function getUserInput(string $prompt): string
{
    echo $prompt . PHP_EOL . '> ';
    return trim(fgets(STDIN));
}


function getNumericInput(string $prompt): int
{
    $input = getUserInput($prompt);
    return is_numeric($input) ? (int)$input : 0;
}


function addProduct(array &$items): void
{
    $itemName = getUserInput("Введите название товара для добавления в список:");
    
    if (empty($itemName)) {
        echo "Ошибка: название товара не может быть пустым." . PHP_EOL;
        return;
    }
    
    $quantity = getNumericInput("Введите количество товара:");
    
    if ($quantity <= 0) {
        echo "Ошибка: количество должно быть положительным числом." . PHP_EOL;
        return;
    }
    
    $existingIndex = findProductIndex($items, $itemName);
    if ($existingIndex !== -1) {
        echo "Товар '{$itemName}' уже есть в списке. Обновляем количество." . PHP_EOL;
        $items[$existingIndex]['quantity'] += $quantity;
    } else {
        $items[] = [
            'name' => $itemName,
            'quantity' => $quantity
        ];
    }
    
    echo "Товар '{$itemName}' в количестве {$quantity} шт. добавлен в список." . PHP_EOL;
}


function findProductIndex(array $items, string $productName): int
{
    foreach ($items as $index => $item) {
        if ($item['name'] === $productName) {
            return $index;
        }
    }
    return -1;
}


function deleteProduct(array &$items): void
{
    if (empty($items)) {
        echo "Список покупок пуст. Нечего удалять." . PHP_EOL;
        return;
    }

    echo 'Текущий список покупок:' . PHP_EOL;
    displayBasket($items);

    $itemName = getUserInput("Введите название товара для удаления из списка:");
    $index = findProductIndex($items, $itemName);

    if ($index !== -1) {
        $removedItem = $items[$index];
        unset($items[$index]);
        $items = array_values($items); 
        
        $quantityText = $removedItem['quantity'] > 1 ? " в количестве {$removedItem['quantity']} шт." : "";
        echo "Товар '{$removedItem['name']}'{$quantityText} удален из списка." . PHP_EOL;
    } else {
        echo "Товар '{$itemName}' не найден в списке." . PHP_EOL;
    }
}

function editProduct(array &$items): void
{
    if (empty($items)) {
        echo "Список покупок пуст. Нечего редактировать." . PHP_EOL;
        return;
    }

    echo 'Текущий список покупок:' . PHP_EOL;
    displayBasket($items);

    $itemName = getUserInput("Введите название товара для изменения:");
    $index = findProductIndex($items, $itemName);

    if ($index === -1) {
        echo "Товар '{$itemName}' не найден в списке." . PHP_EOL;
        return;
    }

    echo "Редактирование товара: {$items[$index]['name']} ({$items[$index]['quantity']} шт.)" . PHP_EOL;
    
    $newName = getUserInput("Введите новое название товара (или оставьте пустым чтобы не менять):");
    $newQuantity = getNumericInput("Введите новое количество товара (или 0 чтобы не менять):");

    $changes = [];
    
    if (!empty($newName)) {
        $existingIndex = findProductIndex($items, $newName);
        if ($existingIndex !== -1 && $existingIndex !== $index) {
            echo "Ошибка: товар с названием '{$newName}' уже существует в списке." . PHP_EOL;
            return;
        }
        $items[$index]['name'] = $newName;
        $changes[] = "название изменено на '{$newName}'";
    }
    
    if ($newQuantity > 0) {
        $items[$index]['quantity'] = $newQuantity;
        $changes[] = "количество изменено на {$newQuantity} шт.";
    }
    
    if (empty($changes)) {
        echo "Изменения не произведены." . PHP_EOL;
    } else {
        echo "Товар успешно обновлен: " . implode(', ', $changes) . "." . PHP_EOL;
    }
}

function printBasket(array $items): void
{
    echo 'Ваш список покупок: ' . PHP_EOL;
    displayBasket($items);
    
    $totalItems = array_sum(array_column($items, 'quantity'));
    echo 'Всего ' . count($items) . ' позиций, ' . $totalItems . ' товаров. '. PHP_EOL;
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

        case OPERATION_EDIT:
            editProduct($items);
            break;

        case OPERATION_PRINT:
            printBasket($items);
            break;
    }

    echo "\n ----- \n";
} while ($operationNumber !== OPERATION_EXIT);

echo 'Программа завершена' . PHP_EOL;