<?php
/**
 * PdfAttachments email template model class
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
class Vianetz_PdfAttachments_Model_Email_Template_Mailer extends Mage_Core_Model_Email_Template_Mailer
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'vianetz_pdfattachments_email';

    /**
     * Send all emails from email list.
     *
     * Unfortunately adding the attachments to invoice/shipments/creditmemo mails is no easy task so instead
     * of rewriting all models additionally we do all the logic here.
     *
     * @see self::$_emailInfos
     *
     * @return Mage_Core_Model_Email_Template_Mailer
     */
    public function send()
    {
        /** @var Mage_Core_Model_Email_Template $emailTemplate */
        $emailTemplate = Mage::getModel('core/email_template');

        Mage::dispatchEvent($this->_eventPrefix . '_template_init', array('email_template' => $emailTemplate, 'mailer' => $this));

        // Send all emails from corresponding list
        while (!empty($this->_emailInfos)) {
            $emailInfo = array_pop($this->_emailInfos);
            // Handle "Bcc" recipients of the current email
            $emailTemplate->addBcc($emailInfo->getBccEmails());

            Mage::dispatchEvent($this->_eventPrefix . '_send_before', array('email_template' => $emailTemplate, 'email_info' => $emailInfo, 'mailer' => $this));

            // Set required design parameters and delegate email sending to Mage_Core_Model_Email_Template
            if ($emailTemplate->getMail()->hasAttachments === false) {
                // We do not use the Magento queue feature because then attachments do not work.
                $emailTemplate->setQueue($this->getQueue());
            }
            $emailTemplate->setDesignConfig(array('area' => Mage_Core_Model_App_Area::AREA_FRONTEND, 'store' => $this->getStoreId()))
                ->sendTransactional(
                    $this->getTemplateId(),
                    $this->getSender(),
                    $emailInfo->getToEmails(),
                    $emailInfo->getToNames(),
                    $this->getTemplateParams(),
                    $this->getStoreId()
                );

            Mage::dispatchEvent($this->_eventPrefix . '_send_after', array('email_template' => $emailTemplate, 'email_info' => $emailInfo, 'mailer' => $this));
        }

        return $this;
    }
}
