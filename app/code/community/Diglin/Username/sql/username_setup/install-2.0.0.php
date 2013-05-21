<?php
/**
 * Diglin
 *
 * @category    Diglin
 * @package     Diglin_Username
 * @copyright   Copyright (c) 2011-2013 Diglin (http://www.diglin.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Diglin_Username_Model_Entity_Setup */
$installer = $this;

/* @var $eavConfig Mage_Eav_Model_Config */
$eavConfig = Mage::getSingleton('eav/config');

$store = Mage::app()->getStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$attributes = $installer->getAdditionalAttributes();

foreach ($attributes as $attributeCode => $data) {
    $installer->addAttribute('customer', $attributeCode, $data);

    $attribute = $eavConfig->getAttribute('customer', $attributeCode);
    $attribute->setWebsite( (($store->getWebsite()) ? $store->getWebsite() : 0));

    if (false === ($attribute->getIsSystem() == 1 && $attribute->getIsVisible() == 0)) {
        $usedInForms = array(
            'customer_account_create',
            'customer_account_edit',
            'checkout_register',
        );
        if (!empty($data['adminhtml_only'])) {
            $usedInForms = array('adminhtml_customer');
        } else {
            $usedInForms[] = 'adminhtml_customer';
        }
        if (!empty($data['adminhtml_checkout'])) {
            $usedInForms[] = 'adminhtml_checkout';
        }

        $attribute->setData('used_in_forms', $usedInForms);
    }
    $attribute->save();
}

$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('sales/quote'), 'customer_username', array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => '255',
    'nullable' => true,
    'comment' => 'Customer Username'
));

$installer->endSetup();