<?php

require_once("Rest.inc.php");

class API extends REST
{
    public $data = "";

    public function __construct()
    {
        parent::__construct();
    }

    private function json($data)
    {
        if (is_array($data)) {
            return json_encode($data);
        }
    }
}
?>