<?php

namespace App\Models;

use App\Library\Crud\Database;
use App\Library\Crud\Select;

class Model
{
    protected $table;
    protected $schema;
    protected $columns;


    public function getColumns(): array
    {
        $database = new Database();
        $pdo = $database->getConnection();
        $query = $pdo->prepare("
            SELECT column_name 
            FROM information_schema.columns
            WHERE table_name = :table
        ");
        $query->execute(['table' => $this->table]);
        $this->columns = $query->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($this->columns as $key => $column) {
            $this->columns[$key] = $column;
        }
        return $this->columns;
    }

    public function authentication($data)
    {
        $select = new Select();
        $query = $select->select(['id','name', 'password'])
        ->from($this->schema, ['c' => $this->table])
        ->where("c.name = '{$data['name']}'")
        ->get();
        if (!password_verify($data['password'], $query[0]['password'])) return false;
        unset($query[0]['password']);
        return $query[0];
    }
}
