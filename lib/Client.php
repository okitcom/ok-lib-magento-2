<?php
/**
 * Created by PhpStorm.
 * Date: 7/21/17
 */

namespace OK;


use OK\Credentials\APICredentials;
use OK\Credentials\Environment\ProductionEnvironment;
use OK\Model\Network\Exception\NetworkException;

class Client {

    /**
     * @var string URL of OK service
     */
    protected $base_url;

    /**
     * @var APICredentials credentials of the OK service
     */
    protected $credentials;

    /**
     * Client constructor.
     * @param $credentials APICredentials credentials
     */
    public function __construct(APICredentials $credentials) {
        $this->credentials = $credentials;

        $this->base_url = "https://" . $credentials->getEnvironment()->getBaseUrl() . "/" . $this->credentials->path();
    }

    /**
     * Perform an authenticated JSON GET
     * @param $path string path
     * @param array $req payload
     * @return string response
     * @throws NetworkException
     */
    public function get($path, $req = array()) {
        return $this->curl($path, $req, true, "GET");
    }

    /**
     * Perform an authenticated JSON POST
     * @param $path string path
     * @param array $req payload
     * @return string response
     * @throws NetworkException
     */
    public function post($path, $req = array()) {
        return $this->curl($path, $req, true, "POST");
    }

    /**
     * Delete http request
     * @param $path
     * @param $req array parameters to be encoded in url
     * @return string response
     * @throws NetworkException
     */
    public function delete($path, $req = array()) {
        return $this->curl($path, $req, true, "DELETE");
    }

    public function getImage($path, $req = array()) {
        return $this->curl($path, $req, true, "GET", false);
    }

    /**
     * Calls a url on the OK service
     * @param $url
     * @param mixed $req Query parameters
     * @param bool $auth Should use authentication for request
     * @param string $method HTTP Method, either GET or POST
     * @param bool $json whether this request should use json
     * @return mixed|string
     * @throws NetworkException
     */
    protected function curl($url, $req = array(), $auth = TRUE, $method = "GET", $json = true) {
        $header = [];
        if ($json) {
            $header[] = 'Content-Type: application/json';
            $header[] = 'Accept: application/json';
        } else {
            $header[] = 'Accept: text/html,application/json,image/webp,image/apng,*/*';
        }
        static $ch = null;
        if (is_null($ch)) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; PHP client; '.php_uname('s').'; PHP/'.phpversion().')');
        }
        curl_setopt($ch, CURLOPT_HTTPGET, 1);

        if (Client::DEBUG) {
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);
            echo 'Request: ' . $method . "\t" . $this->base_url . $url . "\n";
        }

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, NULL);

        if ($method == "GET") {
            $post_data = http_build_query($req, '', '&');
            curl_setopt($ch, CURLOPT_URL, $this->base_url . $url .'?'.$post_data);
        } else if ($method == "POST") {
            curl_setopt($ch, CURLOPT_URL, $this->base_url . $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($req));
        } else {
            $post_data = http_build_query($req, '', '&');
            curl_setopt($ch, CURLOPT_URL, $this->base_url . $url .'?'.$post_data);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        //curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        if ($auth) {
            if ($this->credentials->getPrivateKey() == null) {
                throw new NetworkException("No credentials found.", -2);
            }
            curl_setopt($ch, CURLOPT_USERPWD, $this->credentials->getPrivateKey().':');
        }
        if(curl_errno($ch)) {
            throw new NetworkException("CURL error: " . curl_error($ch), -1);
        }
        // run the query
        $res = curl_exec($ch);

        if (Client::DEBUG) {
            $headerSent = curl_getinfo($ch, CURLINFO_HEADER_OUT ); // request headers
            echo "=== Request headers: ===\n";
            print_r($headerSent);

            if ($method == "POST") {
                echo "=== POST fields ===\n";
                echo json_encode($req) . "\n\n";
            }

            echo "=== Response: ===\n";
            echo $res;
        }

        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($code >= 400) {
            // Status code
            // Decode into error object
            throw new NetworkException($res, $code);
        }
        if ($code == 0) {
            throw new NetworkException("No network connection", -1);
        }

        //print_r(curl_getinfo($ch));

        return $res;
    }

    const DEBUG = false;
}