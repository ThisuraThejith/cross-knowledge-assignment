<?php

//Creating an instance of SimpleJsonRequest and invoking the cache functionality
$simpleJsonRequest = new SimpleJsonRequest();
$requestMethod = $_SERVER['REQUEST_METHOD'];
$url = 'https://postman-echo.com/get';
$parameters = array('test' => 123);
$result = $simpleJsonRequest::handleRequest($requestMethod, $url, $parameters);
echo json_encode($result);

class SimpleJsonRequest
{
    private static $redis = null;
    private static $redisHost = 'localhost';
    private static $redisPort = 6379;

    private static function makeRequest(string $method, string $url, array $parameters = null, array $data = null)
    {
        $opts = [
            'http' => [
                'method' => $method,
                'header' => 'Content-type: application/json',
                'content' => $data ? json_encode($data) : null
            ]
        ];

        $url .= ($parameters ? '?' . http_build_query($parameters) : '');
        return file_get_contents($url, false, stream_context_create($opts));
    }

    //Has implemented the core cache functionality inside get method rather than in a separate function as generally
    //caching is used for getting/retrieving information
    public static function get(string $url, array $parameters = null)
    {
        $method = 'GET';
        $timePeriodToInvalidateCache = 300;
        $cacheKey = self::generateCacheKey($url, $method, $parameters);
        $isKeyAvailableInCache = self::isKeyAvailableInCache($cacheKey);
        $redisInstance = self::getRedisInstanceWithConnection();
        if ($isKeyAvailableInCache)
        {
            return json_decode($redisInstance->get($cacheKey));
        }
        $value = self::makeRequest($method, $url, $parameters);
        $redisInstance->set($cacheKey, $value);
        //Will invalidate the cache record with the particular key after 300 seconds/ 5 minutes
        $redisInstance->expire($cacheKey, $timePeriodToInvalidateCache);
        return json_decode($value);
    }

    public static function post(string $url, array $parameters = null, array $data)
    {
        return json_decode(self::makeRequest('POST', $url, $parameters, $data));
    }

    public static function put(string $url, array $parameters = null, array $data)
    {
        return json_decode(self::makeRequest('PUT', $url, $parameters, $data));
    }

    public static function patch(string $url, array $parameters = null, array $data)
    {
        return json_decode(self::makeRequest('PATCH', $url, $parameters, $data));
    }

    public static function delete(string $url, array $parameters = null, array $data = null)
    {
        return json_decode(self::makeRequest('DELETE', $url, $parameters, $data));
    }

    //Will redirect the request to the appropriate http method based on the request method
    public static function handleRequest(string $method, string $url, array $parameters = null, array $data = null)
    {

        switch ($method)
        {
            case 'GET':
                $response = self::get($url, $parameters);
                break;
            case 'POST':
                $response = self::post($url, $parameters, $data);
                break;
            case 'PATCH':
                $response = self::patch($url, $parameters, $data);
                break;
            case 'PUT':
                $response = self::put($url, $parameters, $data);
                break;
            case 'DELETE':
                $response = self::delete($url, $parameters);
                break;
            default:
                $response = null;
                break;
        }

        return $response;
    }

    //Will create a unique hash key to be used as the key in the cache record which is going to be stored in the cache
    private static function generateCacheKey(string $url, string $method ,array $parameters = null)
    {
        $uniqueUrl = $url . ($parameters ? '?' . http_build_query($parameters) : '') . $method;
        return hash('md5', $uniqueUrl);
    }

    //Creates and returns a redis instance with the connection to the redis server. When called, will only return a single
    //redis instance with connection (based on singleton pattern) without creating multiple instances
    private static function getRedisInstanceWithConnection()
    {
        try {
            if (self::$redis == null)
            {
                self::$redis = new Redis();
                self::$redis->connect(self::$redisHost, self::$redisPort);
            }
            return self::$redis;
        } catch (Exception $e)
        {
            print_r(array('error'=> $e->getMessage()));
        }
    }

    //Checks whether a particular key is available in the cache
    private static function isKeyAvailableInCache(string $cacheKey)
    {
        $redisInstance = self::getRedisInstanceWithConnection();
        if (!$redisInstance->get($cacheKey))
        {
            return false;
        }
        return true;
    }
}
