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
    return $this->httpGet('json', 'json', $q, '/live/search');
  }

  public function html($q) {
    return $this->httpGet('string', 'html', $q, '/live/search');
  }

  public function csv($q) {
    return $this->httpGet('string', 'csv', $q, '/live/search');
  }

  public function locations($q) {
    return $this->httpGet('json', 'json', $q, '/live/locations');
  }

  public function account() {
    return $this->httpGet('json', 'json', NULL, '/live/account');
  }


  public function listBatches() {
    return $this->httpGet('json', 'json', NULL, '/live/batches');
  }

  public function getBatch($batchId) {
    return $this->httpGet('json', 'json', NULL, "/live/batches/{$batchId}");
  }

  public function startBatch($batchId) {
    return $this->httpGet('json', 'json', NULL, "/live/batches/{$batchId}/start");
  }

  public function deleteBatch($batchId) {
    return $this->httpDelete("/live/batches/{$batchId}");
  }

  public function deleteBatchSearch($batchId, $searchId) {
    return $this->httpDelete("/live/batches/{$batchId}/{$searchId}");
  }

  public function listBatchSearches($batchId, $page) {
    return $this->httpGet("json", "json", NULL, "/live/batches/{$batchId}/searches/{$page}");
  }

  public function listAllBatchSearchesAsJSON($batchId) {
    return $this->httpGet("json", "json", NULL, "/live/batches/{$batchId}/searches/json");
  }

  public function listAllBatchSearchesAsCSV($batchId) {
    return $this->httpGet("json", "json", NULL, "/live/batches/{$batchId}/searches/csv");
  }

  public function listBatchResults($batchId) {
    return $this->httpGet('json', 'json', NULL, "/live/batches/{$batchId}/results");
  }

  public function getBatchResultSet($batchId, $resultSetId) {
    return $this->httpGet("json", "json", NULL, "/live/batches/{$batchId}/results/{$resultSetId}");
  }

  public function getBatchResultSetAsCSV($batchId, $resultSetId) {
    return $this->httpGet("json", "json", NULL, "/live/batches/{$batchId}/results/{$resultSetId}/csv");
  }

  public function createBatch($params) {
    return $this->httpPost("/live/batches", $params);
  }

  public function updateBatch($batchId, $params) {
    return $this->httpPut("/live/batches/{$batchId}", $params);
  }

  public function updateBatchSearch($batchId, $searchId, $params) {
    return $this->httpPut("/live/batches/{$batchId}/{$searchId}", $params);
  }
    
  function httpGet($decode_format, $output, $q, $path) {
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

    $result = $api->get("https://api.serpwow.com{$path}", $q);

    if($result->info->http_code == 200 || $result->info->http_code == 301 || $result->info->http_code == 302) {
      if($decode_format == 'string') {
        return $result->response;
      } else {
        return $result->decode_response();
      }
    } else {
      $error = $result->decode_response();
      $msg = $error->request_info->message;
      throw new SerpWowException($msg);
      return;
    }
    
    throw new SerpWowException("Unexpected failure, please contact hello@serpwow.com: $result");
    return;
  }

  function httpDelete($path) {
    if($this->api_key == NULL) {
      throw new SerpWowException("api_key must be defined in the constructor");
    }
    
    $args = [];
    $api = new RestClient($args);

    $path = $path.'?api_key='.$this->api_key.'&source=php';

    $result = $api->delete("https://api.serpwow.com{$path}");

    if($result->info->http_code == 200 || $result->info->http_code == 301 || $result->info->http_code == 302) {
      return $result->decode_response();
      
    } else {
      $error = $result->decode_response();
      $msg = $error->request_info->message;
      throw new SerpWowException($msg);
      return;
    }
    
    throw new SerpWowException("Unexpected failure, please contact hello@serpwow.com: $result");
    return;
  }

  function httpPost($path, $data) {
    if($this->api_key == NULL) {
      throw new SerpWowException("api_key must be defined in the constructor");
    }
    
    $args = [];
    $api = new RestClient($args);

    $path = $path.'?api_key='.$this->api_key.'&source=php';

    $result = $api->post("https://api.serpwow.com{$path}", json_encode($data), array('Content-Type' => 'application/json'));

    if($result->info->http_code == 200 || $result->info->http_code == 301 || $result->info->http_code == 302) {
      return $result->decode_response();
      
    } else {
      $error = $result->decode_response();
      $msg = $error->request_info->message;
      throw new SerpWowException($msg);
      return;
    }
    
    throw new SerpWowException("Unexpected failure, please contact hello@serpwow.com: $result");
    return;
  }

  function httpPut($path, $data) {
    if($this->api_key == NULL) {
      throw new SerpWowException("api_key must be defined in the constructor");
    }
    
    $args = [];
    $api = new RestClient($args);

    $path = $path.'?api_key='.$this->api_key.'&source=php';

    $result = $api->put("https://api.serpwow.com{$path}", json_encode($data), array('Content-Type' => 'application/json'));

    if($result->info->http_code == 200 || $result->info->http_code == 301 || $result->info->http_code == 302) {
      return $result->decode_response();
      
    } else {
      $error = $result->decode_response();
      $msg = $error->request_info->message;
      throw new SerpWowException($msg);
      return;
    }
    
    throw new SerpWowException("Unexpected failure, please contact hello@serpwow.com: $result");
    return;
  }
  
}
?>