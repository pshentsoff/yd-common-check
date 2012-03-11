<?php
/**
 * Утилита для тестирования API Яндекс.Директа: 
 *  - работоспособности протоколов JSON, SOAP, WDSL
 *  - работоспособности авторизации SSL, OAuth-токенами
 *  - того же, но в песочнице
 *    
 * @author V.Pshentsov <pshentsoff@yandex.ru>
 * @file Адреса сервисов Яндекс.Директ 
 *   
 **/ 

/**
 * Массив адресов API Яндекс.Директа
 * начиная с v4: 
 *  на момент когда писалась утилита Live4 еще работала не всегда, а v3 уже не работала 
 * URL приведены просто полностью для удобства копипаста и наглядности   
 * просто раскоменчиваем что нужно проверить и закомменчиваем что ненужно 
 **/  
$api_urls = array(
  # JSON авторизация будет по OAuth
  'JSON.OAUTH' => array(
#    'v4'    => 'https://soap.direct.yandex.ru/v4/json/',
#    'live4' => 'https://soap.direct.yandex.ru/live/v4/json/',
#    'v4.sandbox'    => 'https://api-sandbox.direct.yandex.ru/json-api/v4/',
#    'live4.sandbox' => '',                                                    # нет - значит не проверяем
    ),
  # JSON с авторизацией по SSL сертификату
  'JSON.SSL' => array(
#    'v4'    => 'https://soap.direct.yandex.ru/v4/json/',
#    'live4' => 'https://soap.direct.yandex.ru/live/v4/json/',
#    'v4.sandbox'    => 'https://api-sandbox.direct.yandex.ru/json-api/v4/',
#    'live4.sandbox' => '',                                                    # нет - значит не проверяем
    ),
  # SOAP non-WSDL с сертификацией по OAuth
  'SOAP.OAUTH' => array(
#    'v4'    => 'https://soap.direct.yandex.ru/v4/soap/',
#    'live4' => 'https://soap.direct.yandex.ru/live/v4/soap/',
#    'v4.sandbox'    => 'https://api-sandbox.direct.yandex.ru/api/v4/',
#    'live4.sandbox' => '',                                                    # нет - значит не проверяем
    ),
  # SOAP non-WSDL с сертификацией по сертификату SSL
  'SOAP.SSL' => array(
    'v4'    => 'https://soap.direct.yandex.ru/v4/soap/',
#    'live4' => 'https://soap.direct.yandex.ru/live/v4/soap/',
#    'v4.sandbox'    => 'https://api-sandbox.direct.yandex.ru/api/v4/',
#    'live4.sandbox' => '',                                                    # нет - значит не проверяем
    ),
  'WSDL.OAUTH' => array(
#    'v4'    => 'http://soap.direct.yandex.ru/v4/wsdl/',
#    'live4' => 'http://soap.direct.yandex.ru/live/v4/wsdl/',
#    'v4.sanbox'    => 'https://api-sandbox.direct.yandex.ru/wsdl/v4/',
#    'live4.sandbox' => '',                                                    # нет - значит не проверяем
    ),
  'WSDL.SSL' => array(
#    'v4'    => 'http://soap.direct.yandex.ru/v4/wsdl/',
#    'live4' => 'http://soap.direct.yandex.ru/live/v4/wsdl/',
#    'v4.sanbox'    => 'https://api-sandbox.direct.yandex.ru/wsdl/v4/',
#    'live4.sandbox' => '',                                                    # нет - значит не проверяем
    ),
  );


?>
