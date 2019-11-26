<?php
/**
 * AdvancedInvoiceLayout email helper class
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
 * @package     Vianetz\AdvancedInvoiceLayout
 * @author      Christoph Massmann, <C.Massmann@vianetz.com>
 * @link        http://www.vianetz.com
* @copyright   Copyright (c) 2006-18 vianetz - C. Massmann (http://www.vianetz.com)
 * @license     http://www.vianetz.com/license Commercial Software License
 * @version     2.5.3
 */
class Vianetz_AdvancedInvoiceLayout_Helper_Email extends Mage_Core_Helper_Abstract
{
    /**
     * Attaches the specified file contents to the email template.
     *
     * @param Mage_Core_Model_Email_Template $emailTemplate
     * @param Varien_Object $fileContents
     * @param string $filename
     *
     * @return \Zend_Mime_Part
     */
    public function addAttachmentToEmail(Mage_Core_Model_Email_Template $emailTemplate, $fileContents, $filename)
    {
        return $emailTemplate->getMail()->createAttachment($fileContents, 'application/pdf', Zend_Mime::DISPOSITION_ATTACHMENT, Zend_Mime::ENCODING_BASE64, $filename);
    }
}
