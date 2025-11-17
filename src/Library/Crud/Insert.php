<?php

namespace App\Library\Crud;

use App\Library\Crud\Crud;
use App\Library\Crud\Prepare;

class Insert extends Crud
{
    public function Insert($data, $table, $columns)
    {
        $prepare = new Prepare();
        $prepareData = $prepare->prepareInsert($data, $columns);

        $stmt = $this->pdo->prepare("
            INSERT 
            INTO 
                {$table} ({$prepareData['finalColumns']})
            VALUES
                ({$prepareData['placeHolders']})
            RETURNING *
        ");

        if ($stmt->execute($prepareData['filteredData'])) {

            $insertedRow = $stmt->fetch($this->pdo::FETCH_ASSOC);

            if ($insertedRow) {
                return $insertedRow;
            }

            return false;
        }
        return false;
    }
}
