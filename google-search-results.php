<?php

require "vendor/autoload.php";

// SerpWowException
class SerpWowException extends Exception {}

/*
  Provides access to Google Search Results via the SerpWow API
  Get a free API key at https://serpwow.com/
*/
class GoogleSearchResults {
  public $options;
  public $api;
  public $api_key;
  
  public function __construct($api_key="demo") {
    $this->api_key = $api_key;
  }
  
  public function json($q) {
    return $this->search('json', 'json', $q, '/live/search');
  }

  public function html($q) {
    return $this->search('string', 'html', $q, '/live/search');
  }

  public function csv($q) {
    return $this->search('string', 'csv', $q, '/live/search');
  }

  public function locations($q) {
    return $this->search('json', 'json', $q, '/live/locations');
  }

  public function account() {
    return $this->search('json', 'json', NULL, '/live/account');
  }
    
  function search($decode_format, $output, $q, $path) {
    if($this->api_key == NULL) {
      throw new SerpWowException("api_key must be defined in the constructor");
    }
    
    $args = [];
    $api = new RestClient($args);

    $default_q = [
      'source' => 'php',
      'api_key' => $this->api_key
    ];
    if ($output != 'json') {
      $default_q['output'] = $output;
    }
    if (isset($q)) {
      $q = array_merge($default_q, $q);
    } else {
      $q = $default_q;
    }

    print_r($r);

    $result = $api->get("https://api.serpwow.com{$path}", $q);

    if($result->info->http_code == 200 || $result->info->http_code == 301 || $result->info->http_code == 302) {
      if($decode_format == 'string') {
        return $result->response;
      } else {
        return $result->decode_response();
      }
    } else {
      print_r($result);
      $error = $result->decode_response();
      $msg = $error->request_info->message;
      print_r($error);
      throw new SerpWowException($msg);
      return;
    }
    
    throw new SerpWowException("Unexpected failure, please contact hello@serpwow.com: $result");
    return;
  }
  
}
?>