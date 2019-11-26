<?php
/**
 * AdvancedInvoiceLayout Pdf Creditmemo Item block
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
/**
 * Class Vianetz_AdvancedInvoiceLayout_Block_Pdf_Creditmemo_Item
 *
 */
class Vianetz_AdvancedInvoiceLayout_Block_Pdf_Item_Creditmemo extends Vianetz_AdvancedInvoiceLayout_Block_Pdf_Item_Abstract
{
    /**
     * Init template.
     */
    protected function _construct()
    {
        $this->setTemplate($this->_getTemplateFilePath('item' . DS . 'creditmemo.phtml'));

        parent::_construct();
    }

    /**
     * Get store config path for current block.
     *
     * @param string $configPath
     *
     * @return string
     */
    protected function _getBlockConfigPath($configPath)
    {
        return 'advancedinvoicelayout/creditmemo/' . $configPath;
    }
}
