<?php
/**
 * AdvancedInvoiceLayout Pdf Invoice block
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
 * @package     Vianetz\AdvancedInvoiceLayout
 * @author      Christoph Massmann, <C.Massmann@vianetz.com>
 * @link        http://www.vianetz.com
* @copyright   Copyright (c) 2006-18 vianetz - C. Massmann (http://www.vianetz.com)
 * @license     http://www.vianetz.com/license Commercial Software License
 * @version     2.5.3
 */
class Vianetz_AdvancedInvoiceLayout_Block_Pdf_Shipment extends Vianetz_AdvancedInvoiceLayout_Block_Pdf_Abstract
{
    /**
     * Get store config path for current block.
     *
     * @param string $configPath
     *
     * @return string
     */
    protected function _getBlockConfigPath($configPath)
    {
        return 'advancedinvoicelayout/shipment/' . $configPath;
    }

    /**
     * Init template.
     */
    protected function _construct()
    {
        $this->setTemplate($this->_getTemplateFilePath('shipment.phtml'));

        parent::_construct();
    }

    /**
     * Return html contents for single item.
     *
     * @param Mage_Core_Model_Abstract $sourceItem
     *
     * @return string
     */
    public function getItemHtml(Mage_Core_Model_Abstract $sourceItem)
    {
        return $this->getLayout()
            ->createBlock('advancedinvoicelayout/pdf_item_shipment', '', array('item' => $sourceItem))
            ->toHtml();
    }

    /**
     * Return a list of shipment tracks (if available).
     *
     * @return array
     */
    public function getTracks()
    {
        $tracks = array();
        foreach ($this->getSource()->getAllTracks() as $track) {
            $carrierCode = $track->getCarrierCode();
            if ($carrierCode != 'custom') {
                $carrier = Mage::getSingleton('shipping/config')->getCarrierInstance($carrierCode);
                $carrierTitle = $carrier->getConfigData('title') . ' ' . $track->getTitle();
            } else {
                $carrierTitle = $track->getTitle();
            }

            $tracks[$carrierCode] = array(
                'title' => $carrierTitle,
                'number' => $track->getNumber()
            );
        }

        return $tracks;
    }
}
