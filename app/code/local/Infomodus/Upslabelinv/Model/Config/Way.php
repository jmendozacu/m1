<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Owner
 * Date: 16.12.11
 * Time: 10:55
 * To change this template use File | Settings | File Templates.
 */
class Infomodus_Upslabelinv_Model_Config_Way
{
    public function toOptionArray()
    {
        /*return array(
          array('value' => 0, 'label' => 'First item'),
        );*/
        $c = array();
        if ($model1 = Mage::getResourceModel('upstablerates_shipping/carrier_upstablerates_collection')) {
            $model = $model1->load();
            $items = $model->getItems();
            $items2 = $items;
            $i = 0;
            $arrFlag = array();
            foreach ($items AS $k => $v) {
                if (!in_array($v['method_name'], $arrFlag)) {
                    $c[$i]['value'] = '';
                    $c[$i]['label'] = '';
                    foreach ($items2 AS $f => $d) {
                        if ($v['method_name'] == $d['method_name']) {
                            if ($c[$i]['value'] != '') {
                                $c[$i]['value'] = implode('.', array($d['pk'], $c[$i]['value']));
                            }
                            else {
                                $c[$i]['value'] = $d['pk'];
                            }
                            $c[$i]['label'] = $d['method_name'];
                            $arrFlag[] = $d['method_name'];
                        }
                    }
                }
                $i += 1;
            }
        }
        return $c;
    }
}