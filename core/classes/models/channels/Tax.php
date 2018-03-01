<?php

namespace models\channels;


use controllers\channels\tax\TaxController;
use controllers\channels\tax\TaxXMLController;
use Ecommerce\Ecommerce;
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
        $this->setTax($tax);
    }

    private function setTaxableStates($companyID)
    {
        $this->taxableStates = Tax::getCompanyInfo($companyID);
    }

    private function setTax($totalTax)
    {
        $this->tax = Ecommerce::formatMoney($totalTax); //Still need to format to two decimal places
    }

    private function setTaxXml(Order $order)
    {
        $this->taxXML = TaxXMLController::create($this->taxableStates[$order->getBuyer()->getState()->get()]['tax_line_name'], $order);
    }

    public function get()
    {
        return $this->tax;
    }

    public function getTaxXml()
    {
        return $this->taxXML;
    }

    public function updateTax($tax)
    {
        $this->tax += Ecommerce::formatMoney($tax);
    }

    public function settleTax(Order $order)
    {
        if ($this->isTaxable($order)) {
            echo 'Should be taxed<br>';
            if($order->getTax()->get() == 0) {
                $totalTax = TaxController::calculate(
                    $this->taxableStates[$order->getBuyer()->getState()->get()],
                    $order->getTotalNoTax(),
                    $order->getShippingPrice()
                );
                $this->updateTax($totalTax);
            }
            $this->setTaxXml($order);
        }
    }

    public function isTaxable(Order $order)
    {
        return TaxController::stateIsTaxable($this->taxableStates, $order->getBuyer()->getState()->get());
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