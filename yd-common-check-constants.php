<?php
/**
 * Утилита для тестирования API Яндекс.Директа: 
 *  - работоспособности протоколов JSON, SOAP, WDSL
 *  - работоспособности авторизации SSL, OAuth-токенами
 *  - того же, но в песочнице
 *    
 * @author V.Pshentsov <pshentsoff@yandex.ru>
 *   
 **/ 

/**
 * Константы
 **/
define('YD_LOGIN', 'yandex_login');                     // логин в Яндекс Директ
define('YD_TOKEN', 'xxxxxxxxxxxxxxxxxe8dc506da58a6c6'); //Токен или отладочный токен
define('YD_APPID', 'xxxxxxxxxxxxxxxxx1424abd2e00b76b'); //Id приложения - application_id, он же client_id
define('YD_CERTPATH', '../certs/solid-cert.crt');       // для SSL
define('YD_TRACE', 1);                                  // сохраняем последние запросы, ответы и т.п. для отладки
define('YD_EXCEPTIONS', 0);                             // Исключения обрабатываем сами
define('YD_ENCODING', 'UTF-8');                         // только utf-8
define('YD_URI', 'API');                                // 

ini_set("soap.wsdl_cache_enabled", "0");                // Отключаем кэш wsdl
ini_set("date.timezone", "Europe/Moscow");

define('YD_RECREATE_CONTEXT', 1);                       // использовать пересозданием решение бага с порчей контекста в SoapClient
?>
