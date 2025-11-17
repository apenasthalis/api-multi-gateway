<?php 

namespace App\Models;

use App\Models\Model;
use App\Repository\ClientRepository;
use App\Library\Crud\Crud;

class Client extends Model
{
    protected $table = 'client'; 
    protected $schema = 'public'; 
    protected $columns = [];
    public $client;
    public $crud;

    public function __construct() {
        $this->columns = $this->getColumns();
        $this->client = new ClientRepository();
        $this->crud = new Crud();
    }

    public function getAll() 
    {
        return $this->client->getAllClients($this->schema,$this->table);
    }

    public function getClientById($id)
    {
        return $this->client->getClientById($id, $this->table);
    }

    public function Show($table, $data)
    {
        return $this->client->getClientById($table, $data);
    }

    public function insert($data)
    {
        return $this->crud->insert($data, $this->table, $this->columns);
    }

    public function update($data)
    {
        return $this->crud->updateByUUID($data, $this->table, $this->columns);
    }

    public function delete()
    {

    }
}