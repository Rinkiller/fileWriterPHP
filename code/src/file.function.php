<?php


function readAllFunction(array $config) : string {
    $address = $config['storage']['address'];
    if (file_exists($address) && is_readable($address)) {
        $file = fopen($address, "rb");
        $contents = '';
        while (!feof($file)) {
            $contents .= fread($file, 100);
        }
        fclose($file);
        return $contents;
        }
    else {
        return handleError("Файл не существует");
    }
}
function isGoodDate(string $strDate):bool{
    $date_array = explode("-", $strDate);
    if(count($date_array) != 3){
        echo "False\n";
        return FALSE;
    }
    $now_date_array = explode(":",date("d:m:Y"));
    if (($date_array[0] > 31)or($date_array[1] > 12)or($date_array[2] > $now_date_array[2])){
        return FALSE;
    }
    if($date_array[2] == $now_date_array[2]){
        if($date_array[1] > $now_date_array[1]){
            return FALSE; 
        }else if($date_array[1] == $now_date_array[1]){
            if ($date_array[0] > $now_date_array[0]){
                return FALSE; 
            }
        }

    }
    return TRUE;
}

function addFunction(array $config) : string {
    $address = $config['storage']['address'];
    $name = readline("Введите имя: ");
    $date = readline("Введите дату рождения в формате ДД-ММ-ГГГГ: ");
    if (!isGoodDate($date)){
        return handleError("Введены некоректные данные даты рождения :({$date})");
    }
    $data = $name . "; " . $date . "\r\n";
    $fileHandler = fopen($address, 'a');
    if(fwrite($fileHandler, $data)){
        return "Запись $data добавлена в файл $address";
    }
    else {
        return handleError("Произошла ошибка записи. Данные не сохранены");
    }
    fclose($fileHandler);
}

function clearFunction(array $config) : string {
    $address = $config['storage']['address'];
    if (file_exists($address) && is_readable($address)) {
        $file = fopen($address, "w");
        fwrite($file, '');
        fclose($file);
        return "Файл очищен";
    }
    else {
        return handleError("Файл не существует");
    }
}

function helpFunction() : string {
    return handleHelp();
}
    
function readConfig(string $configAddress): array|false{
    return parse_ini_file($configAddress, true);
    }

function readProfilesDirectory(array $config): string {
    $profilesDirectoryAddress = $config['profiles']['address'];
    if(!is_dir($profilesDirectoryAddress)){
        mkdir($profilesDirectoryAddress);
        }
    $files = scandir($profilesDirectoryAddress);
    $result = "";
    if(count($files) > 2){
        foreach($files as $file){
            if(in_array($file, ['.', '..']))
                continue;
                $result .= $file . "\r\n";
            }
        }
        else {
            $result .= "Директория пуста \r\n";
        }
    return $result;
}
function readProfile(array $config):string {
    if(!isset($_SERVER['argv'][2])){
        return handleError("Не указан файл профиля");
        }
    $profilesDirectoryAddress = $config['profiles']['address'];
    $profileFileName = $profilesDirectoryAddress . $_SERVER['argv'][2] . ".json";
    if(!file_exists($profileFileName)){
        return handleError("Файл $profileFileName не существует");
    }
    $contentJson = file_get_contents($profileFileName);
    $contentArray = json_decode($contentJson, true);
    $info = "Имя: " . $contentArray['name'] . "\r\n";
    $info .= "Фамилия: " . $contentArray['lastname'] . "\r\n";
    return $info;
}
function get_now_birthday(array $config) : string {
    $address = $config['storage']['address'];
    if (file_exists($address) && is_readable($address)) {
        $now_date = date("d-m");
        $file = fopen($address, "rb");
        $result = "";
        while (!feof($file)) {
            $fline = fgets($file);
            if (strpos($fline ,$now_date)!==false){
                $result .= explode("; ", $fline)[0]."\n";
            }
        }
        fclose($file);
        if(strlen($result))return "Сегодня празднуют свой день рожденья:\n".$result; else return "Сегодня нет именинников";
        
        }
    else {
        return "Файл не существует";
    }
}
function dell_this_line(array $config):string{
    $address = $config['storage']['address'];
    if (file_exists($address) && is_readable($address)) {
        $find_str = readline("Введите данные удаляемого именинника: ");
        if(strlen($find_str) < 5) return handleError("Введенных данных недостаточно!");
        $new_cont = "";
        $file_bith = file_get_contents($address);
        if(strpos($file_bith,$find_str)!==false){
            $file_bith_array = explode(PHP_EOL,$file_bith);
            foreach($file_bith_array as $record){
                if(strpos($record, $find_str)!==false){
                    continue;
                }else{
                    $new_cont .= $record;
                }
            }
            file_put_contents($address,$new_cont);
            return "Данные имениника {$find_str} успешно удалены.";
        }    
        return handleError("Данные не найдены");
    }    
    else {
        return handleError("Файл не существует");
    }
}