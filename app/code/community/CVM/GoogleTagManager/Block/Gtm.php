<?php
/**
 * Google Tag Manager Block
 *
 * @category    CVM
 * @package     CVM_GoogleTagManager
 * @author      Chris Martin <chris@chris-martin.net>
 * @copyright   Copyright (c) 2013 Chris Martin
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License 3.0 (OSL-3.0)
 */
class CVM_GoogleTagManager_Block_Gtm extends Mage_Core_Block_Template
{
	/**
	 * Generate JavaScript for the container snippet.
	 *
	 * @return string
	 */
	protected function _getContainerSnippet()
	{
		// Get the container ID.
		$containerId = Mage::helper('googletagmanager')->getContainerId();

		// Render the container snippet JavaScript.
		return "<noscript><iframe src=\"//www.googletagmanager.com/ns.html?id=".$containerId."\"
height=\"0\" width=\"0\" style=\"display:none;visibility:hidden\"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','".$containerId."');</script>\n";
	}

	/**
	 * Generate JavaScript for the data layer.
	 *
	 * @return string|null
	 */
	protected function _getDataLayer()
	{
		// Initialise our data source.
		$data = array();
        $remark_tags = '';

        // Get transaction and visitor data, if desired.
		if (Mage::helper('googletagmanager')->isDataLayerTransactionsEnabled()) $data = $data + $this->_getTransactionData();
		if (Mage::helper('googletagmanager')->isDataLayerVisitorsEnabled()) $data = $data + $this->_getVisitorData();
        if (Mage::helper('googletagmanager')->isDataLayerDynamicRemarketingEnabled()) $remark_tags = $this->_getDynamicRemarketingData();

        // Generate the data layer JavaScript.
        if (!empty($data)) return "<script>dataLayer = [" . json_encode($data) . "];</script>\n\n" . $remark_tags;
        else return '';
	}

    /**
     * Get tags for dynamic remarketing based on page type
     *
     * @link   https://developers.google.com/tag-manager/reference
     * @author Martin Beukman <martbeuk@gmail.com>
     * @return string
     */
    protected function _getDynamicRemarketingData() {
        $return_data = array();
        if ($this->getRequest()->getModuleName() == 'catalog' &&
            $this->getRequest()->getControllerName() == 'product'
        ) {
            $_product = Mage::registry('current_product');
            //load the categories of this product
            $cats      = $_product->getCategoryIds();
            $_use_cats = array();
            foreach ($cats as $category_id) {
                $_cat        = Mage::getModel('catalog/category')->load($category_id);
                $_use_cats[] = "'" . $_cat->getName() . "'";
            }

            /**
             * Get children products (all associated children products data)
             *
             */
            $_use_childs_ids    = array();
            $_use_childs_names  = array();
            $_use_childs_prices = array();

            //assign current product sku to array
            $_use_childs_ids[]    = "'" . $_product->getSku() . "'";
            $_use_childs_names[]  = "'" . $_product->getName() . "'";
            $_use_childs_prices[] = "'" . $_product->getFinalPrice() . "'";
            if ($_product->isConfigurable()) {
                $childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null, $_product);
                if (count($childProducts) > 0) {
                    foreach ($childProducts as $child) {
                        $_use_childs_ids[]   = "'" . $child->getSku() . "'";
                        $_use_childs_names[] = "'" . $child->getName() . "'";
                        //$_use_childs_prices[] = "'".$child->getFinalPrice()."'";
                    }
                }
            }
            $return_data[] = "ecomm_prodid: [" . implode(',', $_use_childs_ids) . "]";
            $return_data[] = "ecomm_pagetype: 'product'";
            $return_data[] = "pname: [" . implode(',', $_use_childs_names) . "]";
            $return_data[] = "pcat: [" . implode(',', $_use_cats) . "]";
            $return_data[] = "ecomm_totalvalue: [" . implode(',', $_use_childs_prices) . "]";
        } elseif ($this->getRequest()->getModuleName() == 'catalog' &&
            $this->getRequest()->getControllerName() == 'category'
        ) {
            //if on a category page
            $return_data[] = "ecomm_prodid: []";
            $return_data[] = "ecomm_pagetype: 'category'";
            $return_data[] = "ecomm_totalvalue: []";
        } elseif ($this->getRequest()->getModuleName() == 'checkout' &&
            $this->getRequest()->getControllerName() == 'cart'
        ) {
            //if on a cart page

            /*gather all product ids and total value*/

            $session          = Mage::getSingleton('checkout/session');
            $_use_childs_ids  = array();
            $_use_total_price = 0.00;

            foreach ($session->getQuote()->getAllItems() as $item) {
                $_use_childs_ids[$item->getSku()] = "'" . $item->getSku() . "'";
                $_use_total_price                 = $_use_total_price + $item->getQty() * $item->getBaseCalculationPrice();
            }

            $return_data[] = "ecomm_prodid: [" . implode(',', $_use_childs_ids) . "]";
            $return_data[] = "ecomm_pagetype: 'cart'";
            $return_data[] = "ecomm_totalvalue: [" . $_use_total_price . "]";
        } elseif ($this->getRequest()->getModuleName() == 'checkout' &&
            $this->getRequest()->getControllerName() == 'onepage' &&
            $this->getRequest()->getActionName() == 'success'
        ) {
            //if on a succes page

            /*gather all product ids and total value*/

            $lastOrderId      = Mage::getSingleton('checkout/session')->getLastOrderId();
            $_order           = Mage::getModel('sales/order')->load($lastOrderId);
            $_use_childs_ids  = array();
            $_totalData       = $_order->getData();
            $_use_total_price = $_totalData['base_subtotal'];

            foreach ($_order->getAllItems() as $item) {
                $_use_childs_ids[$item->getSku()] = "'" . $item->getSku() . "'";
            }

            $return_data[] = "ecomm_prodid: [" . implode(',', $_use_childs_ids) . "]";
            $return_data[] = "ecomm_pagetype: 'purchase'";
            $return_data[] = "ecomm_totalvalue: [" . $_use_total_price . "]";
        } elseif ($this->getRequest()->getModuleName() == 'cms' &&
            $this->getRequest()->getControllerName() == 'index'
        ) {
            //if on home page
            $return_data[] = "ecomm_prodid: []";
            $return_data[] = "ecomm_pagetype: 'home'";
            $return_data[] = "ecomm_totalvalue: []";
        } else {
            //any other page
            $return_data[] = "ecomm_prodid: []";
            $return_data[] = "ecomm_pagetype: 'siteview'";
            $return_data[] = "ecomm_totalvalue: []";
        }
        /*prepare the return array*/
        $remark_tags = "<script> var google_tag_params = {";
        $remark_tags .= implode(",\n", $return_data);
        $remark_tags .= "} </script>";
        $remark_tags .= "<script> dataLayer = [{ google_tag_params: window.google_tag_params }]; </script>";
        return $remark_tags;
    }


    /**
	 * Get transaction data for use in the data layer.
	 *
	 * @link https://developers.google.com/tag-manager/reference
	 * @return array
	 */
	protected function _getTransactionData()
	{
		$data = array();

		$orderIds = $this->getOrderIds();
		if (empty($orderIds) || !is_array($orderIds)) return array();

		$collection = Mage::getResourceModel('sales/order_collection')->addFieldToFilter('entity_id', array('in' => $orderIds));

		$i = 0;
		$products = array();

		foreach ($collection as $order) {
			if ($i == 0) {
				// Build all fields for first order.
				$data = array(
					'transactionId' => $order->getIncrementId(),
					'transactionDate' => date("Y-m-d"),
					'transactionType' => Mage::helper('googletagmanager')->getTransactionType(),
					'transactionAffiliation' => Mage::helper('googletagmanager')->getTransactionAffiliation(),
					'transactionTotal' => round($order->getBaseGrandTotal(),2),
					'transactionShipping' => round($order->getBaseShippingAmount(),2),
					'transactionTax' => round($order->getBaseTaxAmount(),2),
					'transactionPaymentType' => $order->getPayment()->getMethodInstance()->getTitle(),
					'transactionCurrency' => $order->getOrderCurrencyCode(),
					'transactionShippingMethod' => $order->getShippingCarrier()->getCarrierCode(),
					'transactionPromoCode' => $order->getCouponCode(),
					'transactionProducts' => array()
				);
			} else {
				// For subsequent orders, append to order ID, totals and shipping method.
				$data['transactionId'] .= '|'.$order->getIncrementId();
				$data['transactionTotal'] += $order->getBaseGrandTotal();
				$data['transactionShipping'] += $order->getBaseShippingAmount();
				$data['transactionTax'] += $order->getBaseTaxAmount();
				$data['transactionShippingMethod'] .= '|'.$order->getShippingCarrier()->getCarrierCode();
			}

			// Build products array.
			foreach ($order->getAllVisibleItems() as $item) {
				$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$item->getSku());
				$product_categories = $product->getCategoryIds();
				$categories = array();
				foreach ($product_categories as $category) {
					$categories[] = Mage::getModel('catalog/category')->load($category)->getName();
				}
				if (empty($products[$item->getSku()])) {
					// Build all fields the first time we encounter this item.
					$products[$item->getSku()] = array(
						'name' => $this->jsQuoteEscape(Mage::helper('core')->escapeHtml($item->getName())),
						'sku' => $this->jsQuoteEscape(Mage::helper('core')->escapeHtml($item->getSku())),
						'category' => implode('|',$categories),
						'price' => (double)number_format($item->getBasePrice(),2,'.',''),
						'quantity' => (int)$item->getQtyOrdered()
					);
				} else {
					// If we already have the item, update quantity.
					$products[$item->getSku()]['quantity'] += (int)$item->getQtyOrdered();
				}
			}

			$i++;
		}

		// Push products into main data array.
		foreach ($products as $product) {
			$data['transactionProducts'][] = $product;
		}

		// Trim empty fields from the final output.
		foreach ($data as $key => $value) {
			if (!is_numeric($value) && empty($value)) unset($data[$key]);
		}

		return $data;
	}

	/**
	 * Get visitor data for use in the data layer.
	 *
	 * @link https://developers.google.com/tag-manager/reference
	 * @return array
	 */
	protected function _getVisitorData()
	{
		$data = array();
		$customer = Mage::getSingleton('customer/session');

		// visitorId
		if ($customer->getCustomerId()) $data['visitorId'] = (string)$customer->getCustomerId();

		// visitorLoginState
		$data['visitorLoginState'] = ($customer->isLoggedIn()) ? 'Logged in' : 'Logged out';

		// visitorType
		$data['visitorType'] = (string)Mage::getModel('customer/group')->load($customer->getCustomerGroupId())->getCode();

		// visitorExistingCustomer / visitorLifetimeValue
		$orders = Mage::getResourceModel('sales/order_collection')->addFieldToSelect('*')->addFieldToFilter('customer_id',$customer->getId());
		$ordersTotal = 0;
		foreach ($orders as $order) {
			$ordersTotal += $order->getGrandTotal();
		}
		$data['visitorLifetimeValue'] = round($ordersTotal,2);
		$data['visitorExistingCustomer'] = ($ordersTotal > 0) ? 'Yes' : 'No';

		return $data;
	}

	/**
	 * Render Google Tag Manager code
	 *
	 * @return string
	 */
	protected function _toHtml()
	{
		if (!Mage::helper('googletagmanager')->isGoogleTagManagerAvailable()) return '';
		return parent::_toHtml();
	}
}