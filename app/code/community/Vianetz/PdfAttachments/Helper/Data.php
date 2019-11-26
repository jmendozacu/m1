<?php
/**
 * PdfAttachments email helper class
 *
 * @section LICENSE
 * This file is created by vianetz <info@vianetz.com>.
 * The code is distributed under the GPL license.
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@vianetz.com so we can send you a copy immediately.
 *
 * @category    Vianetz
 * @package     Vianetz_PdfAttachments
 * @author      Christoph Massmann, <C.Massmann@vianetz.com>
 * @link        http://www.vianetz.com
 * @copyright   Copyright (c) since 2006 vianetz - Dipl.-Ing. C. Massmann (http://www.vianetz.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.txt GNU GENERAL PUBLIC LICENSE
 */
class Vianetz_PdfAttachments_Helper_Data extends Mage_Core_Helper_Abstract
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
