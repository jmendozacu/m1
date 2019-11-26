<?php
/**
 * AutomaticInvoice Action Abstract Class
 *
 * @section LICENSE
 * This file is created by vianetz <info@vianetz.com>.
 * The Magento module is distributed under a commercial license.
 * Any redistribution, copy or direct modification is explicitly not allowed.
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@vianetz.com so we can send you a copy immediately.
 *
 * @category    Vianetz
 * @package     Vianetz_AutomaticInvoice
 * @author      Christoph Massmann, <C.Massmann@vianetz.com>
 * @link        http://www.vianetz.com
 * @copyright   Copyright (c) 2006-17 vianetz - C. Massmann (http://www.vianetz.com)
 * @license     http://www.vianetz.com/license Commercial Software License
 * @version     1.4.4
 */
abstract class Vianetz_AutomaticInvoice_Model_Action_Abstract implements Vianetz_AutomaticInvoice_Model_Action_ActionInterface
{
    /**
     * @var Mage_Sales_Model_Abstract
     */
    protected $sourceModel;

    /**
     * @var Mage_Sales_Model_Order
     */
    protected $order;

    /**
     * @var mixed
     */
    protected $result;

    /**
     * @return null|string
     */
    protected function getSourceType()
    {
        if ($this->sourceModel instanceof Mage_Sales_Model_Order_Invoice) {
            return 'invoice';
        }

        if ($this->sourceModel instanceof Mage_Sales_Model_Order_Shipment) {
            return 'shipment';
        }

        return null;
    }

    /**
     * @param Mage_Sales_Model_Abstract $sourceModel
     *
     * @return $this
     */
    public function setSourceModel(Mage_Sales_Model_Abstract $sourceModel)
    {
        $this->sourceModel = $sourceModel;

        return $this;
    }

    /**
     * @param Mage_Sales_Model_Order $order
     *
     * @return $this
     */
    public function setOrder(Mage_Sales_Model_Order $order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }
}