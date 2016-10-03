<?php
/**
 * Copyright © 2016 Omni Partners Oy. All rights reserved.
 * See COPYING.txt for license detail
 */

namespace Omni\ConsoleImportExport\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\ImportExport\Model\Export\Adapter\Csv;
use Magento\ImportExport\Model\Export\Entity\AbstractEntity;

class Export extends AbstractImportExport
{
    protected $exportModel;

    protected function getExport()
    {
        if (null == $this->exportModel) {
            $factory = $this->getObjectManager()->create('Magento\ImportExport\Model\ExportFactory');
            $this->exportModel = $factory->create();
        }
        return $this->exportModel;
    }

    public function execute()
    {
        
    }

}