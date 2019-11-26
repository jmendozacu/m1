<?php
/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */
?>
<?php

class Infomodus_Caship_Block_Adminhtml_Method_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('methodGrid');
        $this->setDefaultSort('caship_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('caship/method')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('caship_id', array(
            'header' => Mage::helper('caship')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'caship_id',
        ));

        $this->addColumn('title', array(
            'header' => Mage::helper('caship')->__('Title'),
            'align' => 'left',
            'index' => 'title',
            'type'  => 'text',
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('caship')->__('Method Name'),
            'align' => 'left',
            'index' => 'name',
            'type'  => 'text',
        ));

        $this->addColumn('price', array(
            'header' => Mage::helper('caship')->__('Price'),
            'align' => 'left',
            'index' => 'price',
            'type'  => 'text',
        ));

        $this->addColumn('country_ids', array(
            'header' => Mage::helper('caship')->__('Countries'),
            'align' => 'left',
            'width' => '200px',
            'index' => 'country_ids',
            'type'  => 'text',
            'frame_callback' => array($this, 'callback_countries'),
        ));

        $this->addColumn('amount_min', array(
            'header' => Mage::helper('caship')->__('Min Order Amount'),
            'align' => 'left',
            'index' => 'amount_min',
            'type'  => 'text',
        ));

        $this->addColumn('amount_max', array(
            'header' => Mage::helper('caship')->__('Max Order Amount'),
            'align' => 'left',
            'index' => 'amount_max',
            'type'  => 'text',
        ));

        $this->addColumn('weight_min', array(
            'header' => Mage::helper('caship')->__('Min Weight'),
            'align' => 'left',
            'index' => 'weight_min',
            'type'  => 'text',
        ));

        $this->addColumn('weight_max', array(
            'header' => Mage::helper('caship')->__('Max Weight'),
            'align' => 'left',
            'index' => 'weight_max',
            'type'  => 'text',
        ));

        $this->addColumn('qty_min', array(
            'header' => Mage::helper('caship')->__('Min Qty'),
            'align' => 'left',
            'index' => 'qty_min',
            'type'  => 'text',
        ));

        $this->addColumn('qty_max', array(
            'header' => Mage::helper('caship')->__('Max Qty'),
            'align' => 'left',
            'index' => 'qty_max',
            'type'  => 'text',
        ));

        
        $this->addColumn('status', array(
            'header' => Mage::helper('caship')->__('Status'),
            'align' => 'left',
            'width' => '50px',
            'index' => 'status',
            'type'  => 'options',
            'options' => array('1' => Mage::helper('adminhtml')->__('Enabled'), '0' => Mage::helper('adminhtml')->__('Disabled'))
        ));

        $this->addColumn('action',
            array(
                'header' => Mage::helper('caship')->__('Action'),
                'width' => '100',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('caship')->__('Edit'),
                        'url' => array('base' => '*/*/edit'),
                        'field' => 'id'
                    )
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true,
            ));

        $this->addExportType('*/*/exportCsv', Mage::helper('caship')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('caship')->__('XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('caship_id');
        $this->getMassactionBlock()->setFormFieldName('method');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('caship')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('caship')->__('Are you sure?')
        ));
        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    public function callback_countries($value, $row, $column, $isExport)
    {
        if($row->is_country_all == 0){
            return 'All';
        }
        return str_replace(',', ', ', $value);
    }
}