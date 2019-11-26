<?php
class Godogi_Request_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction (){
        $this->loadLayout();
        $this->renderLayout();
    }
    public function registerAction(){
    	$firstname = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $this->getRequest()->getPost('firstname'));
    	$lastname = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $this->getRequest()->getPost('lastname'));
    	$email = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $this->getRequest()->getPost('email'));
    	$phone = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $this->getRequest()->getPost('phone'));
    	$school = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $this->getRequest()->getPost('school'));
    	$position = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $this->getRequest()->getPost('position'));
    	$interest = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $this->getRequest()->getPost('interest'));

        /*$data     =     $this->getRequest()->getParams();
        */
        $data = [
			    'firstname' => $firstname,
			    'lastname' => $lastname,
			    'email' => $email,
			    'phone' => $phone,
			    'school' => $school,
			    'position' => $position,
			    'interest' => $interest
		];
    	$model     =     Mage::getModel('godogi_request/request')->setData($data);
        $model     ->    save();
        Mage::getSingleton("core/session")->addSuccess("Your request has been submitted successfully!");
        $this->_redirect('ipad-repair/school-ipad-repair-college-university-education-accounts.html');
    }
}