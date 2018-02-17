<?php

class OperationModel
{
    public $id;
    public $date;
    public $createDate;
    public $userId;
    public $income;
    public $amount;
    public $description;
    public $cycleId;

    public function __construct($userId, $data)
    {
        $this->id = date($data["id"]);
        $this->date = date("Y-m-d H:i:s", strtotime($data["date"]));
        $this->createDate = date("Y-m-d H:i:s");
        $this->userId = $userId;
        $this->income = $data["income"];
        $this->amount = $data["amount"];
        $this->description = $data["description"];
        if (array_key_exists("cycleId", $data)) {
            $this->cycleId = $data["cycleId"];
        }

    }
}