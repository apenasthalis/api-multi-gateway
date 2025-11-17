<?php 

namespace App\Models;

use App\Models\Model;
use App\Library\Crud\Crud;
use App\Repository\MercadoPagoRepository;

class MercadoPago extends Model
{
    protected $table = 'mercado_pago'; 
    protected $schema = 'public'; 
    protected $columns = [];
    public $pixPayment;
    public $crud;

    public function __construct() {
        $this->columns = $this->getColumns();
        $this->pixPayment = new MercadoPagoRepository();
        $this->crud = new Crud();
    }

    public function getAll() 
    {
        return $this->pixPayment->getAllClients($this->schema,$this->table);
    }

    public function getClientById($id)
    {
        return $this->pixPayment->getClientById($id, $this->table);
    }

    public function getClientByEmail($data)
    {
        return $this->pixPayment->getClientByEmail($data['email']);
    }

    public function getPaymentById($uuid)
    {
        return $this->pixPayment->getPaymentByUuid($uuid,$this->table);
    }

    public function Show($table, $data)
    {
        return $this->pixPayment->getClientById($table, $data);
    }

    public function insert($data)
    {
        return $this->crud->insert($data, $this->table, $this->columns);
    }

    public function update($data)
    {
        return $this->crud->updateByPaymentId($data, $this->table, $this->columns);
    }

    public function delete()
    {

    }
}