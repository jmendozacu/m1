<?php
/**
 * Created by PhpStorm.
 * User: Vitalij
 * Date: 19.06.15
 * Time: 0:21
 */

class Infomodus_Caship_Model_Config_DinamicPrice
{
    public function toOptionArray()
    {
        return array(
            array('label' => 'Static', 'value' => 0),
            array('label' => 'Dynamic', 'value' => 1),
        );
    }
}