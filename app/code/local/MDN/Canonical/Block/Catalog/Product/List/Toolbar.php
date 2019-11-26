<?php

/**
 * Product list toolbar
 *
 * @category    MDN
 * @package     MDN_Canonical
 * @author      MDN
 */
class MDN_Canonical_Block_Catalog_Product_List_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar
{

    public function getPagerUrl($params=array())
    {

        if (isset($params['p']) && $params['p'] == 1)
               $params['p'] = NULL;

        $urlParams = array();
        $urlParams['_current']  = true;
        $urlParams['_escape']   = true;
        $urlParams['_use_rewrite']   = true;
        $urlParams['_query']    = $params;

        return $this->getUrl('*/*/*', $urlParams);
    }

}
