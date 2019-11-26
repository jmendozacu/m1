<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Blog
 * @version    tip
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Blog_IndexController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();
        if (!Mage::helper('blog')->getEnabled()) {
            $this->_redirectUrl(Mage::helper('core/url')->getHomeUrl());
        }
        Mage::helper('blog')->ifStoreChangedRedirect();
    }

    public function indexAction()
    {
        $this->_forward('list');
    }

    public function listAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('root')->setTemplate(Mage::helper('blog')->getLayout());
        $this->renderLayout();
    }

    public function ajaxAction()
    {
        $last   = $_POST['last'];
        $block  = $this->getLayout()->createBlock('blog/blog')->setTemplate('aw_blog/ajax.phtml')->assign('last', $last);
        $html   = $block->toHtml();
        echo $html;
        /*
        $this->loadLayout();
        $this->renderLayout();

        /*
        
        $sortOrder = 'created_time';
        $sortDirection = Mage::helper('blog')->defaultPostSort(Mage::app()->getStore()->getId());
        $posts = Mage::getModel('blog/blog')->getCollection()
            ->addPresentFilter()
            ->addEnableFilter(AW_Blog_Model_Status::STATUS_ENABLED)
            ->addStoreFilter()
            ->joinComments();
        $posts->setOrder($posts->getConnection()->quote($sortOrder), $sortDirection);
        $html = '';
        $i=1;
        foreach ($posts as $post):
            if($last == $i+1 || $last == $i+2 || $last == $i+3 || $last == $i+4){
                if($i==1 || $i==3){
                    $html .= '<div class="row">';
                }
                echo "<pre>";
                print_r($post->getShortContent());
                echo "</pre>";
                break;
                $html .='
                    <div class="postWrapper col-sm-12 col-md-6">
                        <div class="postBanner">
                            <div class="item" style="padding:0;">
                                <a class="single-image" href="/ipad-repair.html">
                                    '.$post->getShortContent().'
                                </a>
                            </div>
                        </div>';
                        
                            $orig_data = $post->getData('created_time');
                            $oldLocale = setlocale(LC_COLLATE, "0");
                            setlocale(LC_ALL, Mage::app()->getLocale()->getLocaleCode().'.UTF-8');
                            $post_day = strftime("%d", strtotime($orig_data) + Mage::getSingleton('core/date')->getGmtOffset());
                            $post_month = strftime("%b", strtotime($orig_data) + Mage::getSingleton('core/date')->getGmtOffset());
                            setlocale(LC_ALL, $oldLocale);
                    $html .='    
                        <div class="postTitle">
                            <h2><a href="'.$post->getAddress().'" >'.$post->getTitle().'</a></h2>
                        </div>';
                        $postCats = $post->getCats(); 
                        if(!empty($postCats)): 
                            foreach ($postCats as $data): 
                                $className = $data['title']; 
                                $className = str_replace(" ", "", $className);
                            endforeach; 
                        endif;
                        $html .='
                        <div class="postDetails '.$className.'">
                            '.$post->getReadMore();
                            $postCats = $post->getCats();
                            if (!empty($postCats)):
                                    foreach ($postCats as $data):
                                        $html .='<a class="cat" href="'.$data['url'].'">'.$data['title'].'</a>';
                                    endforeach;
                            endif;
                        $html .='
                        </div>
                        <div class="postContent">'.$post->getPostContent().'</div>
                    </div>';
                if($i==2 || $i==4){
                    $html .='
                        </div>';
                }
            }
            $i++;
        endforeach;
        echo $html;
        */
    }
    public function ajaxCatAction()
    {
        $last   = $_POST['last'];
        $block  = $this->getLayout()->createBlock('blog/blog')->setTemplate('aw_blog/ajaxCat.phtml')->assign('last', $last);
        $html   = $block->toHtml();
        echo $html;
    }
}