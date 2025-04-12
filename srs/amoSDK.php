<?php

class AmoSDK {
    private $url;
    private $method;
    private $headers;
    private $data;
    private $timeout = 30;
    private $cache_enabled = false;
    private $cache_duration = 3600; // 1 hour
    private $cache_dir = 'cache/';
    private $log_enabled = false;
    private $log_file = 'logs/api_logs.txt';
    private $retry_attempts = 3;
    private $retry_delay = 1; // seconds

    public function __construct($url, $method = 'GET', $headers = [], $data = null) {
        $this->url = $url;
        $this->method = strtoupper($method);
        $this->headers = array_merge([
            'Content-Type: application/json',
            'Accept: application/json'
        ], $headers);
        $this->data = $data;
        
        // Create cache and log directories if they don't exist
        if (!file_exists($this->cache_dir)) mkdir($this->cache_dir, 0777, true);
        if (!file_exists(dirname($this->log_file))) mkdir(dirname($this->log_file), 0777, true);
    }

    // Setter methods for configuration
    public function setTimeout($seconds) {
        $this->timeout = $seconds;
        return $this;
    }

    public function enableCache($duration = 3600) {
        $this->cache_enabled = true;
        $this->cache_duration = $duration;
        return $this;
    }

    public function enableLogging() {
        $this->log_enabled = true;
        return $this;
    }

    public function setRetry($attempts, $delay = 1) {
        $this->retry_attempts = $attempts;
        $this->retry_delay = $delay;
        return $this;
    }

    private function log($message) {
        if ($this->log_enabled) {
            $timestamp = date('Y-m-d H:i:s');
            file_put_contents($this->log_file, "[$timestamp] $message\n", FILE_APPEND);
        }
    }

    private function getCacheKey() {
        return md5($this->url . serialize($this->data) . serialize($this->headers));
    }

    private function getFromCache() {
        if (!$this->cache_enabled) return null;
        
        $cache_file = $this->cache_dir . $this->getCacheKey();
        if (file_exists($cache_file)) {
            $content = file_get_contents($cache_file);
            $cache = json_decode($content, true);
            
            if (time() - $cache['timestamp'] < $this->cache_duration) {
                $this->log("Cache hit for: {$this->url}");
                return $cache['data'];
            }
        }
        return null;
    }

    private function saveToCache($data) {
        if (!$this->cache_enabled) return;
        
        $cache_file = $this->cache_dir . $this->getCacheKey();
        $cache_data = [
            'timestamp' => time(),
            'data' => $data
        ];
        file_put_contents($cache_file, json_encode($cache_data));
    }

    private function validateRequest() {
        if (empty($this->url)) {
            throw new Exception('URL cannot be empty');
        }
        
        if (!filter_var($this->url, FILTER_VALIDATE_URL)) {
            throw new Exception('Invalid URL format');
        }
        
        if (!in_array($this->method, ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'])) {
            throw new Exception('Unsupported HTTP method');
        }
    }

    public function pull() {
        try {
            $this->validateRequest();
            
            // Check cache first
            $cached_response = $this->getFromCache();
            if ($cached_response !== null) {
                return $cached_response;
            }

            $attempt = 0;
            do {
                $attempt++;
                $response = $this->executeRequest();
                
                if ($response !== null) {
                    $this->saveToCache($response);
                    return $response;
                }
                
                if ($attempt < $this->retry_attempts) {
                    $this->log("Retrying request (attempt $attempt of {$this->retry_attempts})");
                    sleep($this->retry_delay);
                }
            } while ($attempt < $this->retry_attempts);

            throw new Exception("Request failed after {$this->retry_attempts} attempts");

        } catch (Exception $e) {
            $this->log("Error: " . $e->getMessage());
            throw $e;
        }
    }

    private function executeRequest() {
        $ch = curl_init();

        $curl_options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $this->url,
            CURLOPT_HTTPHEADER => $this->headers,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2
        ];

        switch ($this->method) {
            case 'POST':
                $curl_options[CURLOPT_POST] = true;
                $curl_options[CURLOPT_POSTFIELDS] = json_encode($this->data);
                break;
            case 'PUT':
                $curl_options[CURLOPT_CUSTOMREQUEST] = 'PUT';
                $curl_options[CURLOPT_POSTFIELDS] = json_encode($this->data);
                break;
            case 'DELETE':
                $curl_options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
                break;
            case 'PATCH':
                $curl_options[CURLOPT_CUSTOMREQUEST] = 'PATCH';
                $curl_options[CURLOPT_POSTFIELDS] = json_encode($this->data);
                break;
        }

        curl_setopt_array($ch, $curl_options);

        $this->log("Sending {$this->method} request to: {$this->url}");
        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if(curl_errno($ch)) {
            $this->log("cURL Error: " . curl_error($ch));
            curl_close($ch);
            return null;
        }

        curl_close($ch);

        if ($status_code >= 200 && $status_code < 300) {
            return json_decode($response, true);
        } else {
            $this->log("Request failed with status code: $status_code");
            return null;
        }
    }

    // Utility methods
    public static function get($url, $headers = []) {
        return new self($url, 'GET', $headers);
    }

    public static function post($url, $data, $headers = []) {
        return new self($url, 'POST', $headers, $data);
    }

    public static function put($url, $data, $headers = []) {
        return new self($url, 'PUT', $headers, $data);
    }

    public static function delete($url, $headers = []) {
        return new self($url, 'DELETE', $headers);
    }
}

?>
