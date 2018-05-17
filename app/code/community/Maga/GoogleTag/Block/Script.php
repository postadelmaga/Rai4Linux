<?php

class Maga_GoogleTag_Block_Script extends Maga_GoogleTag_Block_Abstract
{
    CONST PAGE_HOME = 'home';
    CONST PAGE_CMS = 'cms';
    CONST PAGE_SEARCH_RESULT = 'search';

    CONST PAGE_CHANNEL = 'channel';
    CONST PAGE_PROGRAM = 'program';

    protected function _getAnalyticsJsonOptions()
    {
        /** @var BurnOut_Ultimo_Helper_GoogleAnalytics_Data $_helper */
        $_helper = $this->helper('googleanalytics');
        $params = array();

        $pageType = $_helper->getCurrentPageType();
        switch ($pageType) {
            case self::PAGE_HOME:
                $params['page_title'] = 'Page: HomePage';
                break;
            case self::PAGE_CMS:
                if ($cms = $_helper->getCurrentCms()) {
                    $params['page_title'] = 'Page: ' . $cms->getTitle();
                }
                break;
            case self::PAGE_SEARCH_RESULT:
                $params['page_title'] = 'Search Result for: ' . $this->jsQuoteEscape($_helper->getCurrentSearchTerm());
                break;
            case self::PAGE_PROGRAM:
                if ($product = $_helper->getCurrentProgram()) {
                    $params['page_title'] = 'Program: ' . $this->jsQuoteEscape($product->getName());
                }
                break;
            case self::PAGE_CHANNEL:
                if ($category = $_helper->getCurrentChannel()) {
                    $params['page_title'] = 'Channel: ' . $this->jsQuoteEscape($category->getName());
                }
                break;
        }

        if ($_helper->isIpAnonymizationEnabled()) {
            $params['anonymize_ip'] = true;
        }

        if (count($params)) {
            return Mage::helper('core')->jsonEncode($params);
        }
        return false;
    }

    /**
     * @see https://developers.google.com/analytics/devguides/collection/gtagjs/enhanced-ecommerce
     * @return string
     */
    protected function _getAnalyticsExtraEventSnippet()
    {
        /** @var BurnOut_Ultimo_Helper_GoogleAnalytics_Data $_helper */
        $_helper = $this->helper('googleanalytics');

        if (empty($this->getOrderIds())) {
            // OTHER PAGES
            $pageType = $_helper->getCurrentPageType();
        } else {
            // Checkout Success Page
            $pageType = self::PAGE_CHECKOUT_SUCCESS;
        }

        $event_type = null;
        $params = array();
        switch ($pageType) {
            case self::PAGE_HOME:
                $event_type = 'page_list';
                $params['event_category'] = 'engagement';
                $params['event_label'] = 'CMS: HomePage';
                break;
            case self::PAGE_CMS:
                $event_type = 'page_list';
                $params['event_category'] = 'engagement';
                if ($cms = $_helper->getCurrentCms()) {
                    $params['event_label'] = 'CMS: ' . $cms->getTitle();
                }
                break;
            case self::PAGE_SEARCH_RESULT:
                $event_type = 'view_search_results';
                $params = array('search_term' => $_helper->getCurrentSearchTerm());
                break;
            case self::PAGE_PROGRAM:
                $event_type = 'view_item';
                $product = $_helper->getCurrentProgram();
                $params = array('items' => $this->_getProgramParams($product));
                $params['event_label'] = $this->jsQuoteEscape($product->getName());
                break;
            case self::PAGE_CHANNEL:
                $event_type = 'view_item_list';
                if ($category = $_helper->getCurrentChannel()) {
                    $params['event_label'] = 'Channel: ' . $this->jsQuoteEscape($category->getName());
                }
                break;
        }

        if ($event_type) {
            $paramsJson = Mage::helper('core')->jsonEncode($params);
            return "gtag('event','{$event_type}',{$paramsJson})";
        }

        return '';
    }
}