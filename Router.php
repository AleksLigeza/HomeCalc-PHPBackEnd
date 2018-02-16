<?php
require_once("api.php");
require_once("Auth.php");
require_once("CustomExceptions.php");
require_once("Account.php");
require_once("Operation.php");

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, Authorization');

class Router
{
    public function matchRoute($paramsArray)
    {
        $auth = new Auth();
        $account = new Account();
        $operation = new Operation();

        self::checkRouteParamsCount($paramsArray);

        $route = $paramsArray[0] . "/" . $paramsArray[1] . ":" . (count($paramsArray) - 2);

        $type = $_SERVER['REQUEST_METHOD'];
        switch ($type) {
            case "POST":
                switch ($route) {
                    case 'auth/register:0':
                        return array("POST", false, array($auth, "register"));
                    case 'auth/login:0':
                        return array("POST", false, array($auth, "login"));
                    case 'account/changeemail:0':
                        return array("POST", true, array($account, "changeEmail"));
                    case 'account/changepassword:0':
                        return array("POST", true, array($account, "changePassword"));
                    case 'operations/create:0':
                        return array("POST", true, array($operation, "createOperation"));
                }
                break;

            case "GET":
                switch ($route) {
                    case 'operations/history:1':
                        return array("GET", true, array($operation, "history"));
                    case 'operations/historywithfilters:7':
                        return array("GET", true, array($operation, "historyWithFilters"));
                    case 'operations/details:1':
                        return array("GET", true, array($operation, "details"));
                    case 'operations/cycles:1':
                        return array("GET", true, array($operation, "cycles"));
                    case 'operations/cycle:1':
                        return array("GET", true, array($operation, "cycle"));
                    case 'operations/summary:0':
                        return array("GET", true, array($operation, "summary"));
                }
                break;

            case "DELETE":
                switch ($route) {
                    case 'operations/delete:1':
                        return array("DELETE", true, array($operation, "delete"));
                    }
                break;

            case "PUT":
                switch ($route) {
                    case 'operations/update:0':
                        return array("PUT", true, array($operation, "update"));
                    }
                break;

            default:
                $this->response('Error code 404, Page not found', 404);
                break;
        }
    }

    private function checkRouteParamsCount($paramsArray)
    {
        if (count($paramsArray) < 2) {
            throw new BadRouteException();
        }
    }

    public function processRoute()
    {
        $link = strtolower($_REQUEST['request']);
        if (substr($link, -1) == '/') {
            $link = rtrim($link, '/');
        }
        return $link_array = explode('/', $link);
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
        throw new BadRouteException();
    } else {

        array_shift($paramsArray);

        $userID = null;
        if ($func[1]) {
            $userID = Auth::checkAuthenticated();
            if ($userID == null) {
                throw new AuthException();
            }

            if ($func[0] == "GET" || $func[0] == "PUT" || "DELETE") {
                $paramsArray[0] = $userID;
                $api->response(call_user_func($func[2], $paramsArray), 200);
            } else {
                $api->response(call_user_func($func[2], $userID), 200);
            }
        } else {
            array_shift($paramsArray);

            if ($func[0] == "GET") {
                $api->response(call_user_func($func[2], $paramsArray), 200);
            } else {
                $api->response(call_user_func($func[2]), 200);
            }
        }
    }
} catch (AuthException $ex) {
    $api->response('Error code 401', 401);
} catch (BadRouteException $ex) {
    $api->response('Error code 404', 404);
}
//} catch (Exception $ex) {
//    $api->response('Error code 500', 500);
//}
