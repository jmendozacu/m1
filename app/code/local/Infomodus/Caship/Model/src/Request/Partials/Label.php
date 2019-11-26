<?php
/**
 * @author    Danail Kyosev <ddkyosev@gmail.com>
 * @copyright 2014, Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
class Label extends RequestPartial
{
    protected $required = array(
        'LabelTemplate' => '8X4_A4_PDF',
    );

    public function setLabelTemplate($labelTemplate)
    {
        $this->required['LabelTemplate'] = $labelTemplate;

        return $this;
    }
}
