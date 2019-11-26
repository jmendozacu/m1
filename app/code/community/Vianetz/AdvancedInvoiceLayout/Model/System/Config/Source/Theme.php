<?php
/**
 * AdvancedInvoiceLayout Theme Source Model
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
class Vianetz_AdvancedInvoiceLayout_Model_System_Config_Source_Theme
{
    /**
     * Return available themes for dropdown.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $themeArray = array();

        $configNode = Mage::getConfig()->getNode('default/advancedinvoicelayout/themes');
        foreach ($configNode->children() as $themeNode) {
            $themeArray[] = array(
                'label' => $themeNode->getAttribute('title'),
                'value' => $themeNode->getName()
            );
        }

        return $themeArray;
    }
}
