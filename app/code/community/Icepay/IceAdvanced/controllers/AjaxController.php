<?php

/**
 *  ICEPAY Advanced - AJAX controller
 *  @version 1.0.0
 *  @author Olaf Abbenhuis
 *  @copyright ICEPAY <www.icepay.com>
 *  
 *  Disclaimer:
 *  The merchant is entitled to change de ICEPAY plug-in code,
 *  any changes will be at merchant's own risk.
 *  Requesting ICEPAY support for a modified plug-in will be
 *  charged in accordance with the standard ICEPAY tariffs.
 * 
 */
class Icepay_IceAdvanced_AjaxController extends Mage_Adminhtml_Controller_Action
{

    protected $webservice = null;

    public function iceWebservice()
    {
        if ($this->webservice == null)
            $this->webservice = new Icepay_IceAdvanced_Model_Paymentmethods();
        return $this->webservice;
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function get_paymentmethodsAction()
    {
        $this->getResponse()->setBody(Zend_Json::encode($this->iceWebservice()->retrieveAdminGrid($this->getRequest()->get("store"))));
    }

    public function save_paymentmethodAction()
    {
        $adv_sql = Mage::getSingleton('iceadvanced/mysql4_iceAdvanced');

        $reference = $this->getRequest()->getPost("reference");
        $scopeID = $this->getRequest()->getPost("store");

        if (!isset($reference))
            return;

        $adv_sql->setScope($scopeID);

        $settings = Mage::helper("iceadvanced")->getPaymentmethodExtraSettings();

        if ($this->getRequest()->getPost("active_issuers")) {
            $issuers = explode(",", $this->getRequest()->getPost("active_issuers"));
            if (count($issuers) >= 1)
                array_push($settings, "active_issuers"); //At least 1 issuer active is required
        }

        foreach ($settings as $setting) {
            $adv_sql->saveConfigValue($reference, $setting, $this->getRequest()->getPost($setting));
        }

        $this->getResponse()->setBody(sprintf($this->__("%s settings have been saved."), $this->getRequest()->getPost("name")));
    }

}
