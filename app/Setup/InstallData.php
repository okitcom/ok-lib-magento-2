<?php
/**
 * Created by PhpStorm.
 * Date: 8/14/17
 */

namespace Okitcom\OkLibMagento\Setup;


use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Config;

class InstallData implements InstallDataInterface
{

    const OK_TOKEN = "oktoken";
    const OK_SESSION_TOKEN = "oktokensession";

    private $eavSetupFactory;
    private $eavConfig;

    public function __construct(EavSetupFactory $eavSetupFactory, Config $eavConfig)
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            InstallData::OK_TOKEN,
            [
                'type' => 'varchar',
                'label' => 'OK Token',
                'input' => 'text',
                'required' => false,
                'visible' => false,
                'user_defined' => false,
                'sort_order' => 0,
                'position' => 1000,
                'system' => 0,
            ]
        );
        $sampleAttribute = $this->eavConfig->getAttribute(\Magento\Customer\Model\Customer::ENTITY, InstallData::OK_TOKEN);
        $sampleAttribute->save();
    }

}