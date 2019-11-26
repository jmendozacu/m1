<?php
/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */
?>
<?php
class Infomodus_Upslabelinv_LabelController extends Mage_Core_Controller_Front_Action
{

    public function preDispatch()
    {
        if(Mage::getStoreConfig('upslabelinv/access/guest')==0){
            if (!Mage::getSingleton('customer/session')->authenticate($this)) {
                parent::preDispatch();
                $this->setFlag('', 'no-dispatch', true);
            }
        }
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function viewAction()
    {
        echo $this->getLayout()->createBlock('upslabelinv/label_view')->setTemplate('upslabelinv/label/view.phtml')->toHtml();
    }

}