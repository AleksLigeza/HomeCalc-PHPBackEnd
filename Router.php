<?php
require_once("api.php");
require_once("\MongoDB\Driver\Exception\AuthenticationException");
require_once("\http\Exception\BadUrlException");
require_once("\http\Exception\InvalidArgumentException");

class Router
{

    //init -------------------------
    //new operations
    //new auth
    //new account

    public function matchRoute($paramsArray)
    {
        if (count($paramsArray) < 2) {
            return "ERROR";
        }
        $route = $paramsArray[0] . "/" . $paramsArray[1] . ":" . (count($paramsArray) - 2);

        $type = $_SERVER['REQUEST_METHOD'];
        switch ($type) {
            case "POST":
                switch ($route) {
                    case 'test/test:0':
                        return array("POST", false, array($this, "test"));
                    case 'test/test:0':
                        return array("POST", false, array($this, "test"));
                    case 'test/test:0':
                        return array("POST", false, array($this, "test"));
                }
                break;

            case "GET":
                switch ($route) {
                    case 'test/test:0':
                        return array("GET", false, array($this, "test"));
                    case 'test/test:1':
                        return array("GET", false, array($this, "test"));
                    case 'test/test:2':
                        return array("GET", false, array($this, "test"));
                    case 'test/test:3':
                        return array("GET", false, array($this, "test"));
                }
                break;

            case "DELETE":
                switch ($route) {
                    case 'test/test:0':
                        return array("DELETE", false, array($this, "test"));
                    case 'test/test:0':
                        return array("DELETE", false, array($this, "test"));
                    case 'test/test:0':
                        return array("DELETE", false, array($this, "test"));
                }
                break;

            case "PUT":
                switch ($route) {
                    case 'test/test:0':
                        return array("PUT", false, array($this, "test"));
                    case 'test/test:0':
                        return array("PUT", false, array($this, "test"));
                    case 'test/test:0':
                        return array("PUT", false, array($this, "test"));
                }
                break;

            default:
                $this->response('Error code 404, Page not found', 404);
                break;
        }


//            case 'auth':
//                //auth.getRouteFunction
//            case 'account':
//                //check auth Token
//                //account.getRouteFunction
//                break;
//            case 'operations':
//                //check auth Token
//                //operations.getRouteFunction
//                break;
//            default:
//                processApi();
//                break;
//        }

    }

    public function processRoute()
    {
        return $link_array = explode('/', strtolower($_REQUEST['request']));

//        if ((int)method_exists($this, $func) > 0)
//                $this->$func();
//        else

    }


    public function test($paramsArray)
    {
        return $paramsArray;
    }
}

$router = new Router();
$api = new API();

$paramsArray = $router->processRoute();
$func = $router->matchRoute($paramsArray);

try {
    if ($func == null) {
        throw new BadUrlException();
    } else {

        array_shift($paramsArray);
        array_shift($paramsArray);

        if ($func[1]) {
            //check auth
            //if auth return userID
            //else response error
        }
        if ($func[0] == "GET") {
            $api->response(call_user_func($func[2], $paramsArray), 200);
        } else {
            $api->response(call_user_func($func[2]), 200);
        }
    }
} catch (AuthenticationException $ex) {
    $api->response('Error code 401', 401);
} catch (BadUrlException $ex) {
    $api->response('Error code 404, Page not found', 404);
} catch (InvalidArgumentException $ex) {
    $api->response('Error code 500', 500);
} catch (Exception $ex) {
    $api->response('Error code 500', 500);
}
