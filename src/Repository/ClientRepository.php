<?php

namespace App\Repository;
use App\Library\Crud\Select;

class ClientRepository
{
    public function getAllClients($schema ,$table)
    {
        $librarySelect = new Select();
        $query = $librarySelect->select(['id', 'name'])
        ->from($schema, ['c' => $table])
        ->join('public', ['d' => 'docs'], 'c.id = d.id_client', ['id','cpf'])
        ->join('public', ['v' => 'vehicle'], 'c.id = v.id_client','')
        ->where('c.id = 1')
        ->orderBy('c.id DESC')
        ->orderBy('d.cpf DESC')
        ->get();
        return $query;
    }

    public function getClientById($id ,$table)
    {
        $librarySelect = new Select();
        $query = $librarySelect->select($table)
        ->where("id = $id")
        ->get();
        return $query;
    }

    public function getClientAndCpf($id ,$table)
    {
        $librarySelect = new Select();
        $query = $librarySelect->select( $table)
        ->where("id = $id")
        ->get();
        return $query;
    }
}