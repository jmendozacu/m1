<?php
/**
 * @author    Vitalij Rudyuk <rvansp@gmail.com>
 * @copyright 2014
 */
class DutiableQuot extends RequestPartial
{
    protected $required = array(
        'DeclaredCurrency' => null,
        'DeclaredValue' => null
    );

    public function setDeclaredValue($declaredValue)
    {
        $this->required['DeclaredValue'] = $declaredValue;

        return $this;
    }

    public function setDeclaredCurrency($declaredCurrency)
    {
        $this->required['DeclaredCurrency'] = $declaredCurrency;

        return $this;
    }
}
