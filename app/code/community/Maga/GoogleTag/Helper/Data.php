<?php

class Maga_GoogleTag_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Config paths for using throughout the code
     */
    const XML_PATH_ACTIVE = 'google/analytics/active';
    const XML_PATH_TYPE = 'google/analytics/type';
    const XML_PATH_ACCOUNT = 'google/analytics/account';
    const XML_PATH_ANONYMIZATION = 'google/analytics/anonymization';

    /**
     * Whether GA is ready to use
     *
     * @param mixed $store
     * @return bool
     */
    public function isGoogleAnalyticsAvailable($store = null)
    {
        $accountId = Mage::getStoreConfig(self::XML_PATH_ACCOUNT, $store);
        return $accountId && Mage::getStoreConfigFlag(self::XML_PATH_ACTIVE, $store);
    }

    /**
     * Whether GA IP Anonymization is enabled
     *
     * @param null $store
     * @return bool
     */
    public function isIpAnonymizationEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ANONYMIZATION, $store);
    }

    /**
     * Get GA account id
     *
     * @param string $store
     * @return string
     */
    public function getAccountId($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_ACCOUNT, $store);
    }

    public function getConversionActionId($store = null)
    {
        $label = Mage::getStoreConfig(self::XML_PATH_ADWORDS_CONVERSION_LABEL, $store);
        if ($label != '') {
            return $this->getAdwordsAccountId() . '/' . $label;
        }
        return $this->getAdwordsAccountId();
    }

    public function getCurrentPageType()
    {
        $route_name = Mage::app()->getFrontController()->getRequest()->getRouteName();

        if ($route_name == 'cms') {
            if (Mage::getBlockSingleton('page/html_header')->getIsHomePage()) {
                return Maga_GoogleTag_Block_Script::PAGE_HOME;
            } else {
                return Maga_GoogleTag_Block_Script::PAGE_CMS;
            }
        } else if ($route_name == 'catalogsearch') {
            return Maga_GoogleTag_Block_Script::PAGE_SEARCH_RESULT;
        }

        return false;
    }

    public function getCurrentSearchTerm()
    {
        return Mage::app()->getFrontController()->getRequest()->getParam('q');
    }

    public function getCurrentProduct()
    {
        $product = Mage::registry('current_product');
        if (!$product) {
            $product = Mage::registry('current');
        }

        return $product;
    }

    public function getCurrentCategory()
    {
        return Mage::registry('current_category');
    }

    public function getCurrentCms()
    {
        return Mage::getSingleton('cms/page');
    }
}