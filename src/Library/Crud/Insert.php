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
        ");

        if ($stmt->execute($prepareData['filteredData'])) {
            return $this->pdo->lastInsertId() > 0 ? true : false;
        }
    }
}