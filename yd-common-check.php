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

# поскольку тестим - хотим смотреть ошибки
# а для копипаста внутрь чего-либо запоминаем прежнее значение
$old_errrep = error_reporting(E_STRICT | E_ALL);

include('yd-common-check-constants.php');
include('yd-common-check-urls.php');

#echo YD_APPID.':'.YD_TOKEN."<br/>\n";

/**
 * Допускаемые функции
 * строчными для удобства сравнения 
 **/ 
$methods_allow = array(
  'pingapi', 
  'getversion', 
  'getavailableversions', 
#  'getclientinfo', 
  'gettimezones', 
  'getregions',
  'getrubrics',
#  'getreportlist',
  ); 

$methods = array();

if(array_key_exists('method', $_GET)&&$_GET['method']) {
  //TODO (pshentsoff): security checking
  $method = htmlspecialchars($_GET['method']);
  if(in_array(strtolower($method), $methods_allow)) {
    $methods[] = $method;
    } else {
    echo '<h3>This method is not allowed, sorry.</h3>';
    exit;
    }
  } else {
  // не указано ничего - список по-умолчанию
  $methods = array('PingAPI', 'GetClientInfo');
  }


foreach($api_urls as $protocol => $urls) {

  unset($options);
  unset($params);
  if(!count($urls)) continue;
  switch($protocol) {
    case 'JSON.OAUTH':
      $params = array(YD_LOGIN);
      $options = array(
        'token'=> YD_TOKEN,           //Токен или отладочный токен
        'application_id'=> YD_APPID,  //Id приложения - application_id, он же client_id
        'login'=> YD_LOGIN,
        'locale'=> 'ru',              //Установка языка ответных сообщений
        'param'=> $params,
        );
      CheckJSON($urls, $options, $methods, $protocol);
      break;
    case 'JSON.SSL':
      $params = array(YD_LOGIN);
      $options = array(
        'login'=> YD_LOGIN,
        'local_cert' => YD_CERTPATH,
        'locale'=> 'ru',              //Установка языка ответных сообщений
        'param'=> $params,
        );
      CheckJSON($urls, $options, $methods, $protocol);
      break;
    case 'SOAP.OAUTH':
      $options = array(
        'token'=> YD_TOKEN,           //Токен или отладочный токен
        'application_id'=> YD_APPID,  //Id приложения - application_id, он же client_id
        'login'=> YD_LOGIN,
              'trace'=> YD_TRACE,
              'exceptions' => YD_EXCEPTIONS,
              'encoding' => YD_ENCODING,
              'passphrase' => '',
              'uri' => YD_URI,
          );
      CheckSOAP($urls, $options, $methods, $protocol);
      break;
    case 'SOAP.SSL':
      $options = array(
              'trace'=> YD_TRACE,
              'exceptions' => YD_EXCEPTIONS,
              'encoding' => YD_ENCODING,
              'local_cert' => YD_CERTPATH,
              'passphrase' => '',
              'uri' => YD_URI,
          );
      CheckSOAP($urls, $options, $methods, $protocol);
      break;
    case 'WSDL.OAUTH':
      $options = array(
              'trace'=> YD_TRACE,
              'exceptions' => YD_EXCEPTIONS,
              'encoding' => YD_ENCODING,
              'passphrase' => ''
          );
      CheckWSDL($urls, $options, $methods, $protocol);
      break;
    case 'WSDL.SSL':
      $options = array(
              'trace'=> YD_TRACE,
              'exceptions' => YD_EXCEPTIONS,
              'encoding' => YD_ENCODING,
              'local_cert' => YD_CERTPATH,
              'passphrase' => ''
          );
      CheckWSDL($urls, $options, $methods, $protocol);
      break;
    }
  }

# вернем на родину
error_reporting($old_errrep);

/**
 * Непосредственно функции 
 **/ 
function CheckJSON($urls, $options, $methods, $title) {
  echo "<hr /><h3>$title</h3>\n";
  foreach($urls as $key => $url) {
    echo "<hr />API version: <b>'.$key.'</b><br />\n";
    if(!$url) {
      echo "no url<br/>\n";
      continue;
      }
    foreach($methods as $method) {
      $options['method'] = $method;
      $encoded = json_encode($options);
      $request = array(
        'http' => array (
          'method' => 'POST',
          'content' => $encoded,
          ),
        );
      $context = stream_context_create($request);
      if(array_key_exists('local_cert', $options)&&file_exists($options['local_cert'])) {
        stream_context_set_option($context, 'ssl', 'local_cert', $options['local_cert']);
        }
      
      $dt = date(DATE_RFC822);
      //Отправка запроса и получение результата 
      $result = @file_get_contents($url, 0, $context);
      
      echo "Function: <b>'.$method.'</b><br />\n";
      echo "$dt<br />\n";
      echo "JSON: $result\n";
      $result = json_decode($result, true);
      echo '<pre>'.print_r($result, true).'</pre>';
      }
    }
  }

/**
 *  Фиксим баг
 *  "Warning: SoapClient::__doRequest(): XX is not a valid Stream-Context resource in [...]"
 *  https://bugs.php.net/bug.php?id=46427  
 **/ 
function newSoapClient($options) {
  return new SoapClient(NULL, $options);
}

function CheckSOAP($urls, $options, $methods, $title) {
  echo "<hr /><h3>$title</h3>\n";
  foreach($urls as $key => $url) {
    echo "<hr />API version: <b>$key</b><br />\n";
    if(!$url) {
      echo "no url\n";
      continue;
      }
    if(isset($client)) {
      # меняем url
      $client->__SetLocation($url);
      # Использовать заплатку для бага пересозданием контекста
      if(YD_RECREATE_CONTEXT) {
        $client->_stream_context = stream_context_create();
        } 
      } else {
      $options['location'] = $url;
      # попытка решить баг другим рецептом
      if(defined('YD_RECREATE_CONTEXT') && YD_RECREATE_CONTEXT) {
        $options['stream_context'] = stream_context_create();      
#        function newSoapClient($options) {
#          return new SoapClient(NULL, $options);
#          }
        $client = newSoapClient($options);
        } else {
        $client = new SoapClient(NULL, $options);
        }  
      }
      
    $ns = 'SOAP-ENV';
    $name = '"http://schemas.xmlsoap.org/soap/envelope/"';
    $headers = array();
    if(isset($options['login'])) $headers['login'] = $options['login'];
    if(isset($options['token'])) $headers['token'] = $options['token'];
    if(isset($options['application_id'])) $headers['application_id'] = $options['application_id'];
    if(isset($options['locale'])) $headers['locale'] = $options['locale'];
    echo '<pre>'.print_r($headers, true).'</pre>';
    if($headers) {
      $soap_header = new SOAPHeader($ns, $name, $headers); 
      echo '<pre>'.print_r($soap_header, true).'</pre>';
      $client->__setSoapHeaders($soap_header);
      }
    echo '<pre>'.print_r($client, true).'</pre>';

    foreach($methods as $method) {
      $dt = date(DATE_RFC822);
      $result = $client->__soapCall($method, array());
      echo "Function: <b>$method</b><br />\n";
      echo "$dt<br />\n";
      echo '<pre>'.print_r($result, true).'</pre>';
      }
    
    }
  }
  
function CheckWSDL($urls, $options, $methods, $title) {
  echo "<hr /><h3>$title</h3>\n";
  foreach($urls as $key => $url) {
    echo "<hr />Protocol: <b>$key</b><br />\n";
    if(!$url) {
      echo "no url<br>\n";
      continue;
      }
    if(isset($client)) {
      # меняем url
      $client->__SetLocation($url);
      } else {
      $options['location'] = $url;
      # или инициализируем объект SOAP
      $client = new SoapClient(NULL, $options);
      }
    foreach($methods as $method) {

      $dt = date(DATE_RFC822);
      $result = $client->__soapCall($method, array());
      echo "Function: <b>$method</b><br />\n";
      echo "$dt<br />\n";
      echo '<pre>'.print_r($result, true).'</pre>';
      }
    
    }
  }
  
?>
