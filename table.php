<?php
declare(strict_types=1);

function generateSchedule(int $year, int $month, int $monthsCount = 1): array
{
    $schedule = [];
    $nonWorkingDays = 2; 
    $currentDate = DateTime::createFromFormat('Y-m-d', "{$year}-{$month}-1");
    
    for ($m = 0; $m < $monthsCount; $m++) {
        $currentMonth = (int)$currentDate->format('m');
        $currentYear = (int)$currentDate->format('Y');
        $daysInMonth = (int)$currentDate->format('t');
        
        $monthSchedule = [];
        
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dateString = "{$currentYear}-{$currentMonth}-{$day}";
            $date = DateTime::createFromFormat('Y-m-d', $dateString);
            $isWeekend = in_array($date->format('N'), [6, 7]); 
            $isWorkDay = false;
            
            if ($isWeekend) {
                $nonWorkingDays++;
                $isWorkDay = false;
            }
            
            elseif ($nonWorkingDays < 2) {
                $nonWorkingDays++;
                $isWorkDay = false;
            }
            
            else {
                $isWorkDay = true;
                $nonWorkingDays = 0; 
            }
            
            $monthSchedule[$day] = [
                'date' => $date,
                'is_work_day' => $isWorkDay,
                'is_weekend' => $isWeekend
            ];
        }
        
        $schedule[] = [
            'year' => $currentYear,
            'month' => $currentMonth,
            'month_name' => $currentDate->format('F'),
            'schedule' => $monthSchedule
        ];
        
        
        $currentDate->modify('first day of next month');
    }
    
    return $schedule;
}


function displaySchedule(array $schedule): void
{
    foreach ($schedule as $monthData) {
        echo "\n\033[1;36m" . str_repeat('=', 60) . "\033[0m\n";
        echo "\033[1;36mРасписание на: {$monthData['month_name']} {$monthData['year']}\033[0m\n";
        echo "\033[1;36m" . str_repeat('=', 60) . "\033[0m\n";
        
        echo "Пн    Вт    Ср    Чт    Пт    \033[1;31mСб    Вс\033[0m\n";
        
        $weekLine = '';
        $dayCounter = 0;
        
        foreach ($monthData['schedule'] as $day => $dayInfo) {
            $date = $dayInfo['date'];
            $dayOfWeek = $date->format('N'); 
            $isWeekend = $dayInfo['is_weekend'];
            $isWorkDay = $dayInfo['is_work_day'];
            
            if ($day === 1 && $dayOfWeek > 1) {
                $weekLine .= str_repeat('     ', $dayOfWeek - 1);
                $dayCounter = $dayOfWeek - 1;
            }
            
            $dayDisplay = sprintf("%2d", $day);
            
            if ($isWorkDay) {
                $weekLine .= "\033[1;32m" . $dayDisplay . " +  \033[0m";
            } elseif ($isWeekend) {
                $weekLine .= "\033[1;31m" . $dayDisplay . "    \033[0m";
            } else {
                $weekLine .= $dayDisplay . "    ";
            }
            
            $dayCounter++;
            
            if ($dayCounter % 7 === 0 || $day == count($monthData['schedule'])) {
                echo $weekLine . "\n";
                $weekLine = '';
                $dayCounter = 0;
            }
        }
        
        $workDays = array_filter($monthData['schedule'], function($day) {
            return $day['is_work_day'];
        });
        
        $workDayNumbers = array_keys($workDays);
        
        echo "\n\033[33mСтатистика:\033[0m\n";
        echo "Рабочие дни: " . implode(', ', $workDayNumbers) . "\n";
        echo "Всего рабочих дней: " . count($workDays) . "\n";
        echo "Легенда: \033[1;32mзелёный+\033[0m - рабочий день, \033[1;31mкрасный\033[0m - выходной\n";
    }
}


function parseCommandLineArgs(): array
{
    $options = [
        'year' => (int)date('Y'),
        'month' => (int)date('m'),
        'months_count' => 1
    ];
    
    global $argv;
    
    if (isset($argv[1]) && is_numeric($argv[1])) {
        $options['year'] = (int)$argv[1];
    }
    
    if (isset($argv[2]) && is_numeric($argv[2])) {
        $options['month'] = (int)$argv[2];
    }
    
    if (isset($argv[3]) && is_numeric($argv[3])) {
        $options['months_count'] = (int)$argv[3];
    }
    
    return $options;
}

try {
    $args = parseCommandLineArgs();
    
    echo "\033[1;35mГенератор расписания работы (сутки через двое)\033[0m\n";
    echo "Параметры: год={$args['year']}, месяц={$args['month']}, месяцев={$args['months_count']}\n";
    echo "Алгоритм: рабочий день → 2 выходных дня → рабочий день\n";
    echo "Выходные (суббота, воскресенье) всегда считаются выходными\n";
    
    $schedule = generateSchedule($args['year'], $args['month'], $args['months_count']);
    displaySchedule($schedule);
    
} catch (Exception $e) {
    echo "\033[1;31mОшибка: " . $e->getMessage() . "\033[0m\n";
    echo "Использование: php schedule.php [год] [месяц] [количество_месяцев]\n";
    echo "Пример: php schedule.php 2024 1 3\n";
    echo "Пример: php schedule.php 2025 11 1 (для проверки ноября 2025)\n";
}