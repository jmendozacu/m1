<?php
/**
 * AdvancedInvoiceLayout Pdf Invoice Footer block
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
 * Class Vianetz_AdvancedInvoiceLayout_Block_Pdf_Footer
 * @method Mage_Sales_Model_Abstract getSource()
 */
class Vianetz_AdvancedInvoiceLayout_Block_Pdf_Footer extends Vianetz_AdvancedInvoiceLayout_Block_Pdf_Abstract
{
    /**
     * Init template.
     */
    protected function _construct()
    {
        $this->setTemplate($this->_getTemplateFilePath('footer.phtml'));

        parent::_construct();
    }

    /**
     * Get first footer column content.
     *
     * @return string
     */
    public function getFooterFirstColumnContent()
    {
        return $this->getFooterColumnContent(1);
    }

    /**
     * Get second footer column content.
     *
     * @return string
     */
    public function getFooterSecondColumnContent()
    {
        return $this->getFooterColumnContent(2);
    }

    /**
     * Get third footer column content.
     * @return string
     */
    public function getFooterThirdColumnContent()
    {
        return $this->getFooterColumnContent(3);
    }

    /**
     * Get content for footer column with specified index.
     *
     * @param int $footerColumnIndex
     *
     * @return string
     */
    public function getFooterColumnContent($footerColumnIndex)
    {
        return Mage::getModel('advancedinvoicelayout/pdf_container_footertext')
            ->setInstance($this)
            ->getFooterTextForColumn($footerColumnIndex);
    }
}
