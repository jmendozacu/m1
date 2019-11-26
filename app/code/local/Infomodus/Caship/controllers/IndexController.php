<?php
/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */
?>
<?php

class Infomodus_Caship_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function frameAction()
    {
        header('Access-Control-Allow-Origin: http://www.ups.com, http://ups.com');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT');
        header("Access-Control-Allow-Headers: Authorization, X-Requested-With");
        header('Access-Control-Allow-Credentials: true');
        header('P3P: CP="NON DSP LAW CUR ADM DEV TAI PSA PSD HIS OUR DEL IND UNI PUR COM NAV INT DEM CNT STA POL HEA PRE LOC IVD SAM IVA OTC"');
        header('Access-Control-Max-Age: 1');
        $url = str_replace('&', '&amp;', str_replace('&amp;', '&', $_SERVER['QUERY_STRING']));
        ?>
        <!DOCTYPE html>
        <head>
            <meta http-equiv="X-UA-Compatible" content="IE=8">
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        </head>
        <body>
        <style type="text/css">
            body {
                margin: 0;
                padding: 0;
            }
        </style>
        <script type="text/javascript">
            window.onload = function () {
                var el = document.querySelector("iframe");
                el.style.width = window.innerWidth + 'px';
                el.style.height = window.innerHeight + 'px';
            }
        </script>
        <iframe src="//www.ups.com/lsw/invoke?<?php echo $url; ?>" frameborder="0" width="1080px" height="750px"
                name="dialog_caship_access_points2"></iframe>
        </body>
        </html>
    <?php
    }

    public function accesspointcallbackAction()
    {
        $url = $this->getRequest()->getParams();
        ?>
        <!DOCTYPE html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        </head>
        <body>
        <script type="text/javascript">
            window.onload = function () {
                <?php
                if($url['action'] == "cancel"):
                ?>
                window.top.closePopapMapRVA();
                <?php endif; ?>
                <?php
            if($url['action'] == "select"):
            ?>
                <?php $arrUrl= array(); foreach($url AS $k => $v): ?>
                <?php $arrUrl[$k] = $v; ?>
                <?php endforeach; ?>
                <?php /*print_r($arrUrl);*/ ?>
                window.top.setAccessPointToCheckout(<?php echo json_encode($arrUrl); ?>);
                <?php endif; ?>
            }
        </script>
        </body>
        </html>
    <?php
    }

    public function setSessionAddressAPAction()
    {
        $address = Mage::app()->getRequest()->getParams();
        $session = Mage::getSingleton('customer/session');
        if (isset($address['caship_addLine1'])) {
            $session->setCashipAddLine1($address['caship_addLine1']);

            if (isset($address['caship_addLine2'])) {
                $session->setCashipAddLine2($address['caship_addLine2']);
            }
            if (isset($address['caship_addLine3'])) {
                $session->setCashipAddLine3($address['caship_addLine3']);
            }
            if (isset($address['caship_city'])) {
                $session->setCashipCity($address['caship_city']);
            }
            if (isset($address['caship_country'])) {
                $session->setCashipCountry($address['caship_country']);
            }
            if (isset($address['caship_fax'])) {
                $session->setCashipFax($address['caship_fax']);
            }
            if (isset($address['caship_state'])) {
                $session->setCashipState($address['caship_state']);
            }
            if (isset($address['caship_postal'])) {
                $session->setCashipPostal($address['caship_postal']);
            }
            if (isset($address['caship_appuId'])) {
                $session->setCashipAppuId($address['caship_appuId']);
            }
            if (isset($address['caship_name'])) {
                $session->setCashipName($address['caship_name']);
            }
        }
        echo json_encode($address);
    }

    public function getSessionAddressAPAction()
    {
        $session = Mage::getSingleton('customer/session');
        $address = array();
        if ($session->getCashipAddLine1()) {
            $address['addLine1'] = $session->getCashipAddLine1();

            if ($session->getCashipAddLine2()) {
                $address['addLine2'] = $session->getCashipAddLine2();
            }
            if ($session->getCashipAddLine3()) {
                $address['addLine3'] = $session->getCashipAddLine3();
            }
            if ($session->getCashipCity()) {
                $address['city'] = $session->getCashipCity();
            }
            if ($session->getCashipCountry()) {
                $address['country'] = $session->getCashipCountry();
            }
            if ($session->getCashipFax()) {
                $address['fax'] = $session->getCashipFax();
            }
            if ($session->getCashipState()) {
                $address['state'] = $session->getCashipState();
            }
            if ($session->getCashipPostal()) {
                $address['postal'] = $session->getCashipPostal();
            }
            if ($session->getCashipAppuId()) {
                $address['appuId'] = $session->getCashipAppuId();
            }
            if ($session->getCashipName()) {
                $address['name'] = $session->getCashipName();
            }
            echo json_encode($address);
        } else {
            echo json_encode(array("error" => "empty"));
        }
    }

    public function customerAddressAction()
    {
        $address = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress();
        /*$addressId = Mage::app()->getRequest()->getParam('id');
        $address = Mage::getModel('customer/address')->load((int)$addressId);*/
        if(!$address){
            $address = Mage::getSingleton('checkout/session')->getQuote()->getBillingAddress();
        }
        $address->explodeStreetAddress();
        if ($address->getRegionId()) {
            $region = Mage::getModel('directory/region')->load($address->getRegionId());
            $state_code = $region->getCode();
            $address->setRegion($state_code);
        }
        echo json_encode($address->getData());
    }

    public function getShippingMethodsAction()
    {
        $storeId = 1;
        
        if (Mage::getStoreConfig('carriers/caship/active') == 1) {
            echo json_encode(array(
                /*'methods' => Mage::getStoreConfig('carriers/caship/shipping_method'),*/
                'countries' => Mage::getStoreConfig('carriers/caship/specificcountry')
            ));
        } else {
            echo '{}';
        }
    }
}
