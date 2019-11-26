<?php
class Godogi_Request_Block_Adminhtml_Request_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('entity_id');
        $this->setId('godogi_request_request_grid');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }
     
    protected function _getCollectionClass()
    {
        return 'godogi_request/request_collection';
    }
     
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
     
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id',
            array(
                'header'=> $this->__('ID'),
                'align' =>'right',
                'width' => '50px',
                'index' => 'entity_id'
            )
        );
        $this->addColumn('firstname',
            array(
                'header'=> $this->__('First Name'),
                'index' => 'firstname'
            )
        );
        $this->addColumn('lastname',
            array(
                'header'=> $this->__('Last Name'),
                'index' => 'lastname'
            )
        );
        $this->addColumn('email',
            array(
                'header'=> $this->__('Email'),
                'index' => 'email'
            )
        );
        $this->addColumn('phone',
            array(
                'header'=> $this->__('Phone'),
                'index' => 'phone'
            )
        );
        $this->addColumn('school',
            array(
                'header'=> $this->__('School or School District'),
                'index' => 'school'
            )
        );
        $this->addColumn('position',
            array(
                'header'=> $this->__('Position'),
                'index' => 'position'
            )
        );
        $this->addColumn('interest',
            array(
                'header'=> $this->__('Interest'),
                'index' => 'interest',
                'type'  => 'options',
                'options'   => array(
                    0 => 'Not Specified',
                    1 => 'iPad epair',
                    2 => 'iPhone Repair',
                    3 => 'Mac Repair',
                    4 => 'Other',
                    5 => 'Multiple',
                )
            )
        );
        return parent::_prepareColumns();
    }
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('entity_id' => $row->getId()));
    }
}