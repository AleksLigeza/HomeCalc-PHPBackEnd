<?php
require_once("api.php");
require_once("Auth.php");
require_once("CustomExceptions.php");
require_once("Account.php");
require_once("Operation.php");

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
                    case 'account/getallusers:0':
                        return array("GET", true, array($account, "getAllUsers"));
                }
                break;

            case "DELETE":
                switch ($route) {
                    case 'operations/delete:1':
                        return array("DELETE", true, array($operation, "delete"));
                    case 'account/delete:1':
                        return array("DELETE", true, array($account, "delete"));
                }
                break;

            case "PUT":
                switch ($route) {
                    case 'operations/update:0':
                        return array("PUT", true, array($operation, "update"));
                    case 'account/changeemail:0':
                        return array("PUT", true, array($account, "changeEmail"));
                    case 'account/changepassword:0':
                        return array("PUT", true, array($account, "changePassword"));
                    case 'account/updateuser:0':
                        return array("PUT", true, array($account, "updateUser"));
                }
                break;
            default:
                throw new BadRouteException();
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

try {
    cors();
    $router = new Router();
    $api = new API();

    $paramsArray = $router->processRoute();
    $func = $router->matchRoute($paramsArray);
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
                $result = call_user_func($func[2], $paramsArray);
                $api->response($result, 200);
            } else {
                $result = call_user_func($func[2], $userID);
                $api->response($result, 200);
            }
        } else {
            array_shift($paramsArray);

            if ($func[0] == "GET") {
                $result = call_user_func($func[2], $paramsArray);
                $api->response($result, 200);
            } else {
                $result = call_user_func($func[2]);
                $api->response($result, 200);
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


function cors() {

    // Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
        // you want to allow, and if so:
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }

    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            // may also be using PUT, PATCH, HEAD etc
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        exit(0);
    }
}