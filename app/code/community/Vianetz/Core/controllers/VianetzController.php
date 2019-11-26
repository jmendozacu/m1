<?php
/**
 * Core License Controller Class
 *
 * This class is required for compatibility reasons.
 *
 * @deprecated
 *
 * @section LICENSE
 * This file is created by vianetz <info@vianetz.com>.
 * The Magento module is distributed under a commercial license.
 * Any redistribution, copy or direct modification is explicitly not allowed.
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@vianetz.com so we can send you a copy immediately.
 *
 * @category    Vianetz
 * @package     Vianetz_Core
 * @author      Christoph Massmann, <C.Massmann@vianetz.com>
 * @link        http://www.vianetz.com
 * @copyright   Copyright (c) since 2006 vianetz - Dipl.-Ing. C. Massmann (http://www.vianetz.com)
 * @license     http://www.vianetz.com/license Commercial Software License
 */
class Vianetz_Core_VianetzController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @deprecated
     *
     * @return $this
     */
    public function checkLicenseAction()
    {
    	return $this;
    }

    /**
     * @deprecated
     *
     * @return $this
     */
    public function downloadSetupAction()
    {
        return $this;
    }

    /**
     * @deprecated
     *
     * @return $this
     */
    protected function installUpgradeAction()
    {
    	return $this;
    }

    /**
     * Check if user is allowed to do current action.
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return true;
    }
}
