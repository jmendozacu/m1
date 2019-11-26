<?php
/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */
?>
<?php

class Infomodus_Caship_Block_Adminhtml_Errorlog_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('errorlogCashipGrid');
        $this->setDefaultSort('created_time');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('caship/errorlog')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('err_id', array(
            'header' => Mage::helper('caship')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'err_id',
        ));

        $this->addColumn('error_message', array(
            'header' => Mage::helper('caship')->__('Message'),
            'align' => 'left',
            'index' => 'error_message',
            'type'  => 'text',
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('caship')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('caship')->__('XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('err_id');
        $this->getMassactionBlock()->setFormFieldName('errorlog');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('caship')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('caship')->__('Are you sure?')
        ));
        return $this;
    }

    public function getRowUrl($row)
    {
        return false;
    }
}