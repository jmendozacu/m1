<?php
/**
 * AdvancedInvoiceLayout Footer PDF model
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
 * Class Vianetz_AdvancedInvoiceLayout_Model_Pdf_Container_Footertext
 *
 * @method Vianetz_AdvancedInvoiceLayout_Model_Pdf_Document_Abstract getInstance()
 */
class Vianetz_AdvancedInvoiceLayout_Model_Pdf_Container_Footertext extends Mage_Core_Model_Abstract
{
    /**
     * @var Vianetz_AdvancedInvoiceLayout_Model_Template_Filter
     */
    protected $_variableTemplateFilter;

    /**
     * Set variables that should be available via {{var ...}}.
     *
     * @return Vianetz_AdvancedInvoiceLayout_Model_Template_Filter
     */
    protected function _getVariableTemplateFilter()
    {
        if (!$this->_variableTemplateFilter) {
            $this->_variableTemplateFilter = Mage::getModel('advancedinvoicelayout/template_filter')
                ->setSource($this->getInstance()->getSource())
                ->init();
        }

        return $this->_variableTemplateFilter;
    }

    /**
     * Get column text from admin configuration.
     *
     * @param int $columnId
     *
     * @return string
     */
    protected function _getRawFooterTextForColumn($columnId)
    {
        return Mage::getStoreConfig(sprintf('advancedinvoicelayout/layout_footer/footertext_%dcolumn', $columnId), $this->getInstance()->getStore());
    }

    /**
     * Get footer text for given column id. Therefore the variables are replaced via the default Magento templating engine.
     *
     * @param int $columnId
     *
     * @return string
     */
    public function getFooterTextForColumn($columnId)
    {
        $columnText = $this->_getRawFooterTextForColumn($columnId);
        $columnText = nl2br($columnText);

        $columnText = $this->_getVariableTemplateFilter()->filter($columnText);

        return $columnText;
    }
}
