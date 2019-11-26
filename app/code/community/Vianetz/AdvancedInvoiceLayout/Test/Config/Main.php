<?php
/**
 * AdvancedInvoiceLayout Config Unit Tests
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
class Vianetz_AdvancedInvoiceLayout_Test_Config_Main extends EcomDev_PHPUnit_Test_Case_Config
{
    public function testCodePoolCommunity()
    {
        $this->assertModuleCodePool('community');
    }

    public function testModuleVersion()
    {
        $this->assertModuleVersion('2.0.12');
    }

    public function testModuleDependencies()
    {
        $this->assertModuleDepends('Mage_Core');
        $this->assertModuleDepends('Mage_Sales');
        $this->assertModuleDepends('Vianetz_Core');
    }

    public function testCoreEmailTemplateMailerModelRewrite()
    {
        $this->assertModelAlias('core/email_template_mailer', 'Vianetz_AdvancedInvoiceLayout_Model_Email_Template_Mailer');
    }

    public function testAdvancedInvoiceLayoutHelperAliases()
    {
        $this->assertHelperAlias('advancedinvoicelayout', 'Vianetz_AdvancedInvoiceLayout_Helper_Data');
        $this->assertHelperAlias('advancedinvoicelayout/email', 'Vianetz_AdvancedInvoiceLayout_Helper_Email');
        $this->assertHelperAlias('advancedinvoicelayout/payment', 'Vianetz_AdvancedInvoiceLayout_Helper_Payment');
    }

    public function testCustomerGroupSaveObserverDefined()
    {
        $this->assertEventObserverDefined('global', 'controller_action_postdispatch_adminhtml_customer_group_save', 'advancedinvoicelayout/observer', 'saveInvoiceFreeTextOnCustomerGroupSave');
    }

    public function testAddPrintPdfButtonToInvoiceViewObserverDefined()
    {
        $this->assertEventObserverDefined('adminhtml', 'core_block_abstract_prepare_layout_before', 'advancedinvoicelayout/observer', 'addPrintPdfButtonToInvoiceView');
    }

    public function testAddPrintPdfButtonToShipmentViewObserverDefined()
    {
        $this->assertEventObserverDefined('adminhtml', 'core_block_abstract_prepare_layout_before', 'advancedinvoicelayout/observer', 'addPrintPdfButtonToShipmentView');
    }

    public function testAddPrintPdfButtonToCreditmemoViewObserverDefined()
    {
        $this->assertEventObserverDefined('adminhtml', 'core_block_abstract_prepare_layout_before', 'advancedinvoicelayout/observer', 'addPrintPdfButtonToCreditmemoView');
    }

    public function testAddMassPrintPdfActionToInvoiceGridObserverDefined()
    {
        $this->assertEventObserverDefined('adminhtml', 'adminhtml_block_html_before', 'advancedinvoicelayout/observer', 'addMassPrintPdfActionToInvoiceGrid');
    }

    public function testAddMassPrintPdfActionToShipmentGridObserverDefined()
    {
        $this->assertEventObserverDefined('adminhtml', 'adminhtml_block_html_before', 'advancedinvoicelayout/observer', 'addMassPrintPdfActionToShipmentGrid');
    }

    public function testAddMassPrintPdfActionToCreditmemoGridObserverDefined()
    {
        $this->assertEventObserverDefined('adminhtml', 'adminhtml_block_html_before', 'advancedinvoicelayout/observer', 'addMassPrintPdfActionToCreditmemoGrid');
    }
}
