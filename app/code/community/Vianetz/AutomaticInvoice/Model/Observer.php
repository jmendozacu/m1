<?php
/**
 * AutomaticInvoice Observer Class
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
 * @package     Vianetz_AutomaticInvoice
 * @author      Christoph Massmann, <C.Massmann@vianetz.com>
 * @link        http://www.vianetz.com
 * @copyright   Copyright (c) 2006-17 vianetz - C. Massmann (http://www.vianetz.com)
 * @license     http://www.vianetz.com/license Commercial Software License
 * @version     1.4.4
 */

class Vianetz_AutomaticInvoice_Model_Observer
{
    /**
     * @param Mage_Cron_Model_Schedule $schedule
     *
     * @return void
     */
    public function processAsyncQueue(Mage_Cron_Model_Schedule $schedule)
    {
        Mage::helper('automaticinvoice')->log('Processing queue..');

        $queue = Mage::getModel('automaticinvoice/queue');
        Mage::getSingleton('automaticinvoice/queue')->processQueue($queue);
    }
}