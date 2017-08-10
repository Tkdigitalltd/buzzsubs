<?php namespace Tkdigital\Buzzsubs\Setup;

use Magento\Framework\Filesystem\Io\File;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Integration\Model\ConfigBasedIntegrationManager;

class InstallData implements InstallDataInterface
{
    /**
     * @var ConfigBasedIntegrationManager
     */
    private $integrationManager;

    /**
     * @var File
     */
    private $io;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param ConfigBasedIntegrationManager $integrationManager
     * @param File $io
     * @param StoreManagerInterface $storeManager
     */

    public function __construct(
        ConfigBasedIntegrationManager $integrationManager,
        File $io,
        StoreManagerInterface $storeManager
    ) {
        $this->integrationManager = $integrationManager;
        $this->io = $io;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->integrationManager->processIntegrationConfig(['Tkdigital_Buzzsubs']);

        $this->createScript();
    }

    public function createScript()
    {
        $domain = $this->storeManager->getStore()->getBaseUrl();
        $scriptName = $this->strSlug($domain);
        $filePath = __DIR__ . '/../view/frontend/layout/';
        $file = 'default_head_blocks.xml';
        $content = $this->getContent($scriptName);
        $fileMetaData = $this->io->getPathInfo($filePath . '/' . $file);
        $this->io->mkdir($fileMetaData['dirname']);
        $this->io->write($filePath . '/' . $file, $content, 0777);
    }

    private function strSlug($title, $separator = '-')
    {
        $flip = $separator == '-' ? '_' : '-';
        $title = preg_replace('![' . preg_quote($flip) . ']+!u', $separator, $title);
        $title = preg_replace('![^' . preg_quote($separator) . '\pL\pN\s]+!u', '', mb_strtolower($title));
        $title = preg_replace('![' . preg_quote($separator) . '\s]+!u', $separator, $title);

        return trim($title, $separator);
    }

    /**
     * @param $scriptName
     * @return string
     */
    public function getContent($scriptName)
    {
        $content = '<page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
xsi:noNamespaceSchemaLocation="urn:magento:framework:View/Layout/etc/page_configuration.xsd">
<head>
    <script src="https://mage.buzzsubs.com/buzz/vendor/app.js" src_type="url" defer="defer"/>
    <script src="https://mage.buzzsubs.com/buzz/scripts/buzz-' . $scriptName . '-subs.js" src_type="url" defer="defer"/>
</head>
</page>';
        return $content;
    }
}
