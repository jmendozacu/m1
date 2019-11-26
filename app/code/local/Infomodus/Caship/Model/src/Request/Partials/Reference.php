<?php
/**
 * @author    Vitalij Rudyuk <rvansp@gmail.com>
 * @copyright 2014
 */
class Reference extends RequestPartial
{
    protected $required = array(
        'ReferenceID' => null
    );

    public function setReferenceId($referenceId)
    {
        $this->required['ReferenceID'] = $referenceId;

        return $this;
    }
}
