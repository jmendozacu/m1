<?php
class Magecomp_Recaptcha_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	public function saveAction()
	{
	    try {
            $nam = $this->getRequest()->getParam('name');
            $emails = $this->getRequest()->getParam('email');
            $telephone = $this->getRequest()->getParam('telephone');
            $cmt = $this->getRequest()->getParam('comment');
            $receiveEmail = Mage::getStoreConfig('trans_email/ident_general/email');
            $g_response = $this->getRequest()->getParam('g-recaptcha-response');

            if (isset($g_response) && !empty($g_response)):
                if (Mage::helper('recaptcha')->Validate_captcha($g_response)):
                    if ($receiveEmail != '') {
                        try {
                            $contact = Mage::getModel('recaptcha/recaptcha')
                                ->setCname($nam)
                                ->setCemail($emails)
                                ->setCmobno($telephone)
                                ->setCcomment($cmt)
                                ->save();
                            Mage::helper('recaptcha')->contactmailsent($receiveEmail, $nam, $emails, $telephone, $cmt);
                            Mage::getSingleton('core/session')->addSuccess('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.');
                            return $this->_redirectReferer();
                        } catch (Exception $e) {
                            echo $e->getMessage();
                        }
                    }
                    else {
						/**
						 * 2019-11-27 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
						 * «Object of class Mage_Core_Model_Session could not be converted to string
						 * in app/code/local/Magecomp/Recaptcha/controllers/IndexController.php on line 44»:
						 * https://github.com/repairzoom/m1/issues/2
						 */
						return $this->error('Please Configure Admin Email.');
                    }
                else:
					/**
					 * 2019-11-27 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
					 * «Object of class Mage_Core_Model_Session could not be converted to string
					 * in app/code/local/Magecomp/Recaptcha/controllers/IndexController.php on line 44»:
					 * https://github.com/repairzoom/m1/issues/2
					 */
					return $this->error();
                endif;
            else:
				/**
				 * 2019-11-27 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
				 * «Object of class Mage_Core_Model_Session could not be converted to string
				 * in app/code/local/Magecomp/Recaptcha/controllers/IndexController.php on line 44»:
				 * https://github.com/repairzoom/m1/issues/2
				 */
                return $this->error();
            endif;
        }
        catch (Exception $e){
	        Mage::log("Captcha Error :".$e->getMessage(),null,"recaptcha.log");
        }
	}

	/**
	 * 2019-11-27 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	 * «Object of class Mage_Core_Model_Session could not be converted to string
	 * in app/code/local/Magecomp/Recaptcha/controllers/IndexController.php on line 44»:
	 * https://github.com/repairzoom/m1/issues/2
	 * @param string $s [optional]
	 * @return Mage_Core_Controller_Varien_Action
	 */
	private function error($s = 'Please click on the reCAPTCHA box.') {
		$sess = Mage::getSingleton('core/session'); /** @var Mage_Core_Model_Session $sess */
		$sess->addError($s);
		return $this->_redirectReferer();
	}
}
