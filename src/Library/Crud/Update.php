<?php

namespace App\Library\Crud;

use App\Library\Crud\Crud;

class Update extends Crud
{
    public function update($data, $table, $columns)
    {
        $prepare = new Prepare();
        $prepareUpdate = $prepare->prepareUpdate($data, $columns);
        $stmt = $this->pdo->prepare("
            UPDATE 
                {$table}
            SET 
                {$prepareUpdate['placeHolders']}
            WHERE 
               id = {$data['id']}
        ");

        $stmt->execute($prepareUpdate['filteredData']);

        return $stmt->rowCount() > 0 ? true : false;
    }

    public function updateByPaymentId($data, $table, $columns)
    {
        $prepare = new Prepare();
        $prepareUpdate = $prepare->prepareUpdate($data, $columns);
        $stmt = $this->pdo->prepare("
            UPDATE 
                {$table}
            SET 
                {$prepareUpdate['placeHolders']}
            WHERE 
               payment_id = {$data['payment_id']}
        ");

        $stmt->execute($prepareUpdate['filteredData']);

        return $stmt->rowCount() > 0 ? true : false;
    }

    public function updateByUUID($data, $table, $columns)
    {
        $prepare = new Prepare();
        $prepareUpdate = $prepare->prepareUpdate($data, $columns);
        $stmt = $this->pdo->prepare("
            UPDATE 
                {$table}
            SET 
                {$prepareUpdate['placeHolders']}
            WHERE 
               uuid = '{$data['uuid']}'
        ");

        $stmt->execute($prepareUpdate['filteredData']);

        return $stmt->rowCount() > 0 ? true : false;
    }
}
