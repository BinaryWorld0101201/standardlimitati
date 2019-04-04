<?php
class HttpRequest {
    private $method, $url, $data = false;
    private $error, $hasError = false, $response, $status;
    private $requestInfo, $curlError, $headers = array();
    // Default arguments
    private $args = array(
        "followRedirect" => true,
    );
    function __construct($method, $url, $data = false, $args = false) {
        $method = strtolower($method);
        if ($method == "post" || $method == "get") {
            $this->method = $method;
        } else {
            $this->setError("Invalid method: $method");
            return;
        }
        $this->url  = $url;
        $this->data = $data;
        if (is_array($args)) {
            // Add arguments to the already available default arguments
            foreach($args as $key => $value) {
                $this->args[$key] = $value;
            }
        }
        $this->doRequest();
    }
    function hasError() {
        return $this->hasError;
    }
    private function setError($msg) {
        $this->error = $msg;
        $this->hasError = true;
    }
    function getError() {
        return $this->error;
    }
    function getStatus() {
        return $this->status;
    }
    function getResponse() {
        return $this->response;
    }
    function getRequestInfo() {
        return $this->requestInfo;
    }
    function toString() {
        var_dump($this);
    }
    private function doRequest() {
        $this->doCurl();
        if ($this->status != "200") {
            $this->setError("Response error: " . $this->status . " (" . $this->curlError . ")");
        }
    }
    private function doCurl() {
        $c = curl_init();
        // Maybe we want to rewrite the url for data arguments in GET requests
        if ($this->method == "get" && $this->data) {
            $this->url .= "?" . http_build_query($this->data);
        }
        // Default values
        curl_setopt($c, CURLOPT_URL, $this->url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER ,true);
        curl_setopt($c, CURLOPT_FOLLOWLOCATION, $this->args['followRedirect']);
        curl_setopt($c, CURLOPT_HTTPHEADER, array('Expect:'));
		curl_setopt($c, CURLOPT_HEADERFUNCTION, array(&$this,'readHeader'));
        if (isset($this->args['username']) && isset($this->args['password'])) {
            curl_setopt($c, CURLOPT_USERPWD, $this->args['username'] . ':' . $this->args['password']);
        }
        if($this->method == "post") {  //post
            curl_setopt($c, CURLOPT_POST, true);
            // Always escape HTTP data dammit!
            curl_setopt($c, CURLOPT_POSTFIELDS, http_build_query($this->data));
        }
        // Many servers require this to output decent HTML
        if (empty($this->args['useragent'])) {
            curl_setopt($c, CURLOPT_USERAGENT, "Mozilla/5.0");
        } else {
            curl_setopt($c, CURLOPT_USERAGENT, $this->args['useragent']);
        }
        $this->response    = curl_exec($c);
        $this->status      = curl_getinfo($c, CURLINFO_HTTP_CODE);
        $this->curlError   = curl_errno($c) . ": ". curl_error($c);
        $this->requestInfo = curl_getinfo($c);
		$this->headers     = array_merge($this->requestInfo, $this->headers);
        curl_close($c);
    }
	private function readHeader($ch, $header) {
        $key = trim(substr($header, 0, strpos($header, ":")));
        $val = trim(substr($header, strpos($header, ":") + 1));
        if (!empty($key) && !empty($val)) {
            $this->headers[$key] = $val;
        }
        return strlen($header);
	}
	
	function getHeaders($key = false) {
        if ($key) {
            return $this->headers[$key];
        } else {
    		return $this->headers;
        }
	}
}
?>