<?php
require_once("OperationModel.php");
require_once("OperationDbModel.php");

class Operation
{

    // POST -- /operations/create
    public function CreateOperation($userID)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $operation = new OperationModel($userID, $data);
        return OperationDbModel::AddOperation($operation);
    }

    // GET -- /operations/history/:skip
    public function history($paramsArray)
    {
        $userID = $paramsArray[0];
        $skip = (int)$paramsArray[1];
        $history = OperationDbModel::GetOperations($userID, $skip);

        return $history;
    }

    // GET -- /operations/historyWithFilters/:skip/:amountFrom/:amountTo/:description/:dateSince/:dateTo/:type
    public function historyWithFilters($paramsArray)
    {
        return OperationDbModel::GetOperationsWithFilters($paramsArray);
    }

    // GET -- /operations/details/:id
    public function details($paramsArray)
    {
        $userID = $paramsArray[0];
        $id = (int)$paramsArray[1];
        return OperationDbModel::FindOperationById($id, $userID);
    }

    // DELETE -- /operations/delete/:id
    public function delete($paramsArray)
    {
        $userID = $paramsArray[0];
        $id = $paramsArray[1];
        return OperationDbModel::DeleteOperationById($id, $userID);
    }

    // PUT -- /operations/update
    public function update($paramsArray)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $operation = new OperationModel($paramsArray[0], $data);

        $oldOperation = OperationDbModel::FindOperationById($operation->id, $paramsArray[0]);

        $oldOperation['description'] = $operation->description;
        $oldOperation['date'] = $operation->date;
        $oldOperation['createDate'] = $operation->createDate;
        $oldOperation['amount'] = $operation->amount;
        $oldOperation['income'] = $operation->income;

        return OperationDbModel::UpdateOperation($oldOperation);
    }

    // GET -- /operations/cycles/:skip
    public function cycles($paramsArray)
    {
        $userID = $paramsArray[0];
        $skip = (int)$paramsArray[1];
        $history = OperationDbModel::GetCycles($userID, $skip);

        return $history;
    }

    // GET -- /operations/cycle/:id
    public function cycle($paramsArray)
    {
        $userID = $paramsArray[0];
        $cycleId = (int)$paramsArray[1];
        $history = OperationDbModel::GetCycles($userID, $cycleId);

        return $history;
    }

    // GET -- /operations/summary
    public function summary($paramsArray)
    {
        $userId = $paramsArray[0];
        $history = OperationDbModel::GetAllOperations($userId);

        $currentMonthBillsHistory = OperationDbModel::GetMonthOperations($userId, date('Y-m-d'), FALSE);
        $currentMonthIncomeHistory = OperationDbModel::GetMonthOperations($userId, date('Y-m-d'), TRUE);
        $lastMonth = new DateTime("last day of last month");
        $lastMonthBillsHistory = OperationDbModel::GetMonthOperations($userId, $lastMonth->format('Y-m-d'), FALSE);
        $lastMonthIncomeHistory = OperationDbModel::GetMonthOperations($userId, $lastMonth->format('Y-m-d'), TRUE);

        $keys = array('amount', 'bills', 'income', 'lastMonthBills', 'lastMonthIncome',);
        $values = array(
            $this->calculateAmount($history, TRUE),
            $this->calculateAmount($currentMonthBillsHistory, FALSE),
            $this->calculateAmount($currentMonthIncomeHistory, FALSE),
            $this->calculateAmount($lastMonthBillsHistory, FALSE),
            $this->calculateAmount($lastMonthIncomeHistory, FALSE)
        );

        return array_combine($keys, $values);
    }

    private function calculateAmount($history, $checkIncome)
    {
        $amount = 0;

        foreach ($history as &$value) {
            if (!$checkIncome) {
                $amount += $value['amount'];
            } else {
                if ($value['income']) {
                    $amount += $value['amount'];
                } else {
                    $amount -= $value['amount'];
                }
            }
        }
        return $amount;
    }
}