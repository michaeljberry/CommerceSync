<?php

namespace models\channels;


use controllers\channels\tax\TaxController;
use controllers\channels\tax\TaxXMLController;
use models\channels\order\Order;
use models\ModelDB as MDB;
use PDO;

class Tax
{

    private $taxableStates = [];
    private $tax;
    private $taxXML;

    public function __construct($tax, $companyID, Order $order)
    {
        $this->setTaxableStates($companyID);
        $this->setTax($tax, $order);
        $this->setTaxItem($order);
    }

    private function setTaxableStates($companyID)
    {
        $this->taxableStates = Tax::getCompanyInfo($companyID);
    }

    private function setTax($totalTax, Order $order)
    {
        $state = $order->getBuyer()->getState()->get();

        if (TaxController::stateIsTaxable($this->taxableStates, $state) && $totalTax == 0) {
            echo 'Should be taxed<br>';
            $totalTax = TaxController::calculate($this->taxableStates[$state], $order->getTotalNoTax(), $order->getShippingPrice());
        }
        $this->tax = $totalTax;
    }

    private function setTaxItem(Order $order)
    {
        $this->taxXML = TaxXMLController::getItemXml($this->taxableStates[$order->getBuyer()->getState()->get()], $order);
    }

    public function get()
    {
        return $this->tax;
    }

    public function getTaxXml()
    {
        return $this->taxXML;
    }

    public static function getCompanyInfo($companyID)
    {
        $sql = "SELECT s.abbr, t.tax_rate, t.tax_line_name, t.shipping_taxed 
                FROM taxes t 
                INNER JOIN state s ON s.id = t.state_id 
                WHERE company_id = :company_id";
        $queryParams = [
            ':company_id' => $companyID
        ];
        return MDB::query($sql, $queryParams, 'fetchAll', PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);
    }

}