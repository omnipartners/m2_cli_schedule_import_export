<?php
/**
 * Copyright © 2016 Omni Partners Oy. All rights reserved.
 * See COPYING.txt for license detail
 */

namespace Omni\ConsoleImportExport\Model;


use Psr\Log\LoggerInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\App\ObjectManagerFactory;
use Magento\Backend\App\Area\FrontNameResolver;

class AbstractImportExport
{
    protected $_filesystem;
    protected $log;
    private $objectManagerFactory;
    private $objectManager;

    protected $_entityType;

    public function __construct(
        ObjectManagerFactory $objectManagerFactory,
        Filesystem $filesystem,
        LoggerInterface $log
    ) {
        $this->objectManagerFactory = $objectManagerFactory;
        $this->_filesystem = $filesystem;
        $this->log = $log;
    }

    protected function getObjectManager()
    {
        if (null == $this->objectManager) {
            $area = FrontNameResolver::AREA_CODE; // adminhtml
            $this->objectManager = $this->objectManagerFactory->create($_SERVER);
            /** @var \Magento\Framework\App\State $appState */
            $appState = $this->objectManager->get('Magento\Framework\App\State');
            $appState->setAreaCode($area);
            $configLoader = $this->objectManager->get('Magento\Framework\ObjectManager\ConfigLoaderInterface');
            $this->objectManager->configure($configLoader->load($area));
        }
        return $this->objectManager;
    }

    public function setEntityType(string $entity) 
    {
        $this->_entityType = $entity;
        return $this;
    }

    public function getEntityType() 
    {
        return $this->_entityType;
    }
}