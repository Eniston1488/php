<?php
declare(strict_types=1);

function generateSchedule(int $year, int $month, int $monthsCount = 1): array
{
    $schedule = [];
    $currentWorkDay = null;
    
    for ($m = 0; $m < $monthsCount; $m++) {
        $currentMonth = $month + $m;
        $currentYear = $year;
        
        if ($currentMonth > 12) {
            $currentMonth -= 12;
            $currentYear++;
        }
        
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);
        $monthSchedule = [];
        
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDate = DateTime::createFromFormat('Y-m-d', "{$currentYear}-{$currentMonth}-{$day}");
            $isWeekend = in_array($currentDate->format('N'), [6, 7]); 
            
            if ($day === 1 && $m === 0) {
                $isWorkDay = true;
                $currentWorkDay = $currentDate;
            } 
            elseif ($currentWorkDay !== null) {
                $daysDiff = $currentDate->diff($currentWorkDay)->days;
                
                $isWorkDay = ($daysDiff % 3 === 0);
                
                if ($isWorkDay && $isWeekend) {
                    $isWorkDay = false;
                }
            } else {
                $isWorkDay = false;
            }
            
            if ($isWorkDay) {
                $currentWorkDay = $currentDate;
            }
            
            $monthSchedule[$day] = [
                'date' => $currentDate,
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
    }
    
    return $schedule;
}


function displaySchedule(array $schedule): void
{
    foreach ($schedule as $monthData) {
        echo "\n\033[1;36m" . str_repeat('=', 50) . "\033[0m\n";
        echo "\033[1;36mРасписание на: {$monthData['month_name']} {$monthData['year']}\033[0m\n";
        echo "\033[1;36m" . str_repeat('=', 50) . "\033[0m\n";
        
        $weekLine = '';
        foreach ($monthData['schedule'] as $day => $dayInfo) {
            $date = $dayInfo['date'];
            $dayOfWeek = $date->format('D');
            $isWeekend = $dayInfo['is_weekend'];
            $isWorkDay = $dayInfo['is_work_day'];
            
            $dayDisplay = sprintf("%2d(%s)", $day, $dayOfWeek);
            
            if ($isWorkDay) {
                $weekLine .= "\033[1;32m" . $dayDisplay . " +\033[0m ";
            } elseif ($isWeekend) {
                $weekLine .= "\033[1;31m" . $dayDisplay . "  \033[0m ";
            } else {
                $weekLine .= $dayDisplay . "   ";
            }
            
            if ($date->format('N') == 7 || $day == count($monthData['schedule'])) {
                echo $weekLine . "\n";
                $weekLine = '';
            }
        }
        
        $workDays = array_filter($monthData['schedule'], function($day) {
            return $day['is_work_day'];
        });
        
        $weekendWorkDays = array_filter($workDays, function($day) {
            return $day['is_weekend'];
        });
        
        echo "\n\033[33mСтатистика:\033[0m\n";
        echo "Всего рабочих дней: " . count($workDays) . "\n";
        echo "Рабочих дней в выходные: " . count($weekendWorkDays) . "\n";
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
    echo "Первый день первого месяца считается рабочим днем\n";
    echo "Рабочие дни, выпадающие на выходные, переносятся на понедельник\n";
    
    $schedule = generateSchedule($args['year'], $args['month'], $args['months_count']);
    displaySchedule($schedule);
    
} catch (Exception $e) {
    echo "\033[1;31mОшибка: " . $e->getMessage() . "\033[0m\n";
    echo "Использование: php schedule.php [год] [месяц] [количество_месяцев]\n";
    echo "Пример: php schedule.php 2024 1 3\n";
}