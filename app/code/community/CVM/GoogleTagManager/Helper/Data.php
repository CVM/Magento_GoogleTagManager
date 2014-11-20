<?php
/**
 * Google Tag Manager Data Helper
 *
 * @category    CVM
 * @package     CVM_GoogleTagManager
 * @author      Chris Martin <chris@chris-martin.net>
 * @copyright   Copyright (c) 2013 Chris Martin
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License 3.0 (OSL-3.0)
 */
class CVM_GoogleTagManager_Helper_Data extends Mage_Core_Helper_Abstract
{
	const XML_PATH_ACTIVE = 'google/googletagmanager/active';
	const XML_PATH_CONTAINER = 'google/googletagmanager/containerid';

	const XML_PATH_DATALAYER_TRANSACTIONS  = 'google/googletagmanager/datalayertransactions';
	const XML_PATH_DATALAYER_TRANSACTIONTYPE = 'google/googletagmanager/datalayertransactiontype';
	const XML_PATH_DATALAYER_TRANSACTIONAFFILIATION = 'google/googletagmanager/datalayertransactionaffiliation';

	const XML_PATH_DATALAYER_VISITORS = 'google/googletagmanager/datalayervisitors';
	const XML_PATH_DATALAYER_ECOMM = 'google/googletagmanager/datalayerecomm';

	/**
	 * Determine if GTM is ready to use.
	 *
	 * @return bool
	 */
	public function isGoogleTagManagerAvailable()
	{
		return Mage::getStoreConfig(self::XML_PATH_CONTAINER) && Mage::getStoreConfigFlag(self::XML_PATH_ACTIVE);
	}

	/**
	 * Get the GTM container ID.
	 *
	 * @return string
	 */
	public function getContainerId() {
		return Mage::getStoreConfig(self::XML_PATH_CONTAINER);
	}

	/**
	 * Add transaction data to the data layer?
	 *
	 * @return bool
	 */
	public function isDataLayerTransactionsEnabled()
	{
		return Mage::getStoreConfig(self::XML_PATH_DATALAYER_TRANSACTIONS);
	}

	/**
	 * Get the transaction type.
	 *
	 * @return string
	 */
	public function getTransactionType() {
		if (!Mage::getStoreConfig(self::XML_PATH_DATALAYER_TRANSACTIONTYPE)) return '';
		return Mage::getStoreConfig(self::XML_PATH_DATALAYER_TRANSACTIONTYPE);
	}

	/**
	 * Get the transaction affiliation.
	 *
	 * @return string
	 */
	public function getTransactionAffiliation() {
		if (!Mage::getStoreConfig(self::XML_PATH_DATALAYER_TRANSACTIONAFFILIATION)) return '';
		return Mage::getStoreConfig(self::XML_PATH_DATALAYER_TRANSACTIONAFFILIATION);
	}

	/**
	 * Add visitor data to the data layer?
	 *
	 * @return bool
	 */
	public function isDataLayerVisitorsEnabled()
	{
		return Mage::getStoreConfig(self::XML_PATH_DATALAYER_VISITORS);
	}

	/**
	 * Add ecomm data to the data layer?
	 *
	 * @return bool
	 */
	public function isDataLayerDynEcomEnabled()
	{
		return Mage::getStoreConfig(self::XML_PATH_DATALAYER_ECOMM);
	}
}
