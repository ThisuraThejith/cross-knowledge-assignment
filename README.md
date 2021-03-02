
# Cross Knowledge Technical challenge

You will be able to find the solutions for the Cross Knowledge [technical challenge](https://gist.github.com/pxotox/e6f2190685d70f91a2439c9f5b5b482e) in this repository.

# Solutions

## 1. Cache function
The implementation (request.php) can be found inside the 'php-assignment' directory.
#### Steps to execute the cache functionality
1. Install the [phpredis](https://github.com/phpredis/phpredis) extension
2. Place the 'request.php' file inside the local php server. (I've used the LAMP stack and localhost).
3. Inside the file there will be a place where an instance of the 'SimpleJsonRequest' class is created and the functionality is executed as follows.
```php
$simpleJsonRequest = new SimpleJsonRequest();
$requestMethod = $_SERVER['REQUEST_METHOD'];
$url = 'https://postman-echo.com/get';
$parameters = array('test' => 123);
$result = $simpleJsonRequest::handleRequest($requestMethod, $url, $parameters);
echo json_encode($result);
```
4. There you can change the value of the $url and $parameters according to your choice. If there are no parameters related to the $url, $parameters should be set to null.
5. Then inside the class 'SimpleJsonRequest' you'll find two variables named $redisHost and $redisPort as below which are the credentials used to establish a connection with the redis server. The default values are given here. You have to change these values if anything is different in your case than the default values. 

```php
    private static $redisHost = 'localhost';
    private static $redisPort = 6379;
```
6. Send a get request by either of the following methods. (As I've used the LAMP stack and the 'request.php' file is hosted in localhost, my get url will be http://localhost/request.php. ) 
- Call the get url from the web browser
- Call the get url from an api testing tool (like 'Postman') by specifying the HTTP method as 'GET'.
- Use the curl command. (curl http://localhost/request.php).

7. By using either one of above ways you can verify the difference of time taken for a particular request to a url in the first time and the time taken for the requests made for the same url within five minutes
## 2. Date formatting
The implementation of the solution for this part of the challenge can be found inside the 'javascript-assignment' directory.

#### Steps to execute the date formatting functionality
1. Open the 'date-format.html' file with the web browser.
- **Important**: The 'date-format.js' javascript file must be at the same level as the 'date-format.html'. (i.e. They both should be inside a same directory).

## 3. Apply style
The implementation of the solution for this part of the challenge can be found inside the 'css-assignment' directory.

#### Steps to execute the style functionality
1. Open the 'component.html' file with the web browser.
- **Important**: The 'component.css' css file must be at the same level as the 'component.html'. (i.e. They both should be inside a same directory).
