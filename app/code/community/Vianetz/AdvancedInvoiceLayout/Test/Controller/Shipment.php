<?php
/**
 * AdvancedInvoiceLayout Shipment Controller Tests
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
class Vianetz_AdvancedInvoiceLayout_Test_Controller_Shipment extends EcomDev_PHPUnit_Test_Case_Controller
{
    public function setUp()
    {
        parent::setUp();

        // For some reason the notifications block makes trouble if you execute the whole testsuite.
        // As we do not need it we replace it by mock here.
        $notificationsResourceModelMock = $this->getModelMock('index/process_collection', array('addEventsStats'));
        $notificationsResourceModelMock->expects($this->any())->method('addEventsStats')->will($this->returnSelf());
        $this->replaceByMock('resource_model', 'index/process_collection', $notificationsResourceModelMock);

        $this->mockAdminUserSession();
    }

    /**
     * @test
     */
    public function isLoggedIn()
    {
        $this->assertTrue(Mage::getSingleton('admin/session')->isLoggedIn(), "User is not logged in");
    }

    /**
     * @test
     * @loadFixture shipment.yaml
     */
    public function testControllerDispatchedIfShipmentPrinted()
    {
        $this->dispatch('advancedinvoicelayout/adminhtml_shipment/printpdf', array('shipment_id' => 9999));
        $this->assertRequestControllerModule('Vianetz_AdvancedInvoiceLayout');
    }

    /**
     * @test
     * @loadFixture shipment.yaml
     * @loadFixture config.yaml
     */
    public function testShipmentControllerReturnsPdfHeader()
    {
        $this->dispatch('advancedinvoicelayout/adminhtml_shipment/printpdf', array('shipment_id' => 9999));
        $this->assertResponseHeaderContains('Content-type', 'application/pdf');
    }

    /**
     * @test
     * @loadFixture disabledModuleOutputConfig.yaml
     */
    public function testShipmentControllerForwardsToOriginalMagentoControllerIfModuleOutputDisabled()
    {
        $this->dispatch('advancedinvoicelayout/adminhtml_shipment/printpdf', array('shipment_id' => 1234));
        $this->assertRequestForwarded();
    }

    /**
     * @test
     * @loadFixture disabledModuleDocumentType.yaml
     */
    public function testShipmentControllerForwardsToOriginalMagentoControllerIfDocumentTypeDisabled()
    {
        $this->dispatch('advancedinvoicelayout/adminhtml_shipment/printpdf', array('shipment_id' => 1234));
        $this->assertRequestForwarded();
    }

    /**
     * @test
     */
    public function testShipmentControllerLoadsDeniedLayoutHandleIfAclActionNotAllowed()
    {
        // Remove all acls.
        $this->mockAdminUserSession(array());

        $this->dispatch('advancedinvoicelayout/adminhtml_shipment/printpdf', array('shipment_id' => 1234));
        $this->assertLayoutHandleLoaded('adminhtml_denied');
    }
}
