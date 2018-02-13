<?php

require_once("Rest.inc.php");

class API extends REST
{

    public $data = "";
    //Enter details of your database
    const DB_SERVER = "localhost";
    const DB_USER = "root";
    const DB_PASSWORD = "";
    const DB = "homecalc";

    private $db = NULL;

    public function __construct()
    {
        parent::__construct();
        $this->dbConnect();
    }

    private function dbConnect()
    {
        $this->db = mysqli_connect(self::DB_SERVER, self::DB_USER, self::DB_PASSWORD, self::DB);
    }

    public function processApi()
    {
        $link_array = explode('/', $_REQUEST['request']);
        $func = strtolower($link_array[0]);
        array_shift($link_array);

        if ((int)method_exists($this, $func) > 0)
            if ($_SERVER['REQUEST_METHOD'] == "GET") {
                $this->$func($link_array);
            } else {
                $this->$func();
            }
        else
            $this->response('Error code 404, Page not found', 404);   // If the method not exist with in this class, response would be "Page not found".
    }

    private function test($test)
    {
        print_r($test);

    }


    private function test1()
    {
        // Cross validation if the request method is GET else it will return "Not Acceptable" status
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        $myDatabase = $this->db;// variable to access your database
        $param = $this->_request['var'];
        // If success everythig is good send header as "OK" return param
        $this->response($param, 200);
    }


    private function json($data)
    {
        if (is_array($data)) {
            return json_encode($data);
        }
    }
}

$api = new API;
$api->processApi();
?>