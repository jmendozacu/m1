<?php
/**
 * @author    Vitalij Rudyuk <rvansp@gmail.com>
 * @copyright 2015
 */
class SpecialService extends RequestPartial
{
    protected $required = array(
        'SpecialServiceType' => null
    );

    public function setSpecialServiceType($specialServiceType)
    {
        $this->required['SpecialServiceType'] = $specialServiceType;

        return $this;
    }
}
