<?php

namespace App\Repository;
use App\Library\Crud\Select;

class MercadoPagoRepository
{
    public function getAllClients($schema ,$table)
    {
        $librarySelect = new Select();
        $query = $librarySelect->select(['name'])
        ->from($schema, ['c' => $table])
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

    public function getClientByEmail($email)
    {
        $table = 'client';
        $librarySelect = new Select();
        $query = $librarySelect->select( ['id','email'])
        ->from('public',['c' => $table])
        ->where("email = '$email'")
        ->get();
        return $query[0];
    }

    public function getPaymentByUuid($uuid ,$table)
    {
        $librarySelect = new Select();
        $query = $librarySelect->select(['payment_id','status', 'ticket_url', 'uuid'])
        ->from('public',['m' => $table])
        ->join('public', ['c' => 'client'], 'c.id = m.client_id', ['name','email'])
        ->where("m.uuid = '{$uuid}'")
        ->get();
        return $query;
    }
}