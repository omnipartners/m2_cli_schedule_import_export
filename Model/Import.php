<?php
/**
 * Copyright © 2016 Omni Partners Oy. All rights reserved.
 * See COPYING.txt for license detail
 */

namespace Omni\ConsoleImportExport\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\ImportExport\Model\Import\Adapter;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregator;
use Magento\ImportExport\Model\Import\Entity\AbstractEntity;

class Import extends AbstractImportExport
{
    protected $importModel;

    protected function getImport()
    {
        if (null == $this->importModel) {
            $importFactory = $this->getObjectManager()->create('Magento\ImportExport\Model\ImportFactory');
            $this->importModel = $importFactory->create();
        }
        return $this->importModel;
    }

    public function execute()
    {
        $sourceFile = $this->getImport()->getWorkingDir() . 'omni_import.csv';
        $source = Adapter::findAdapterFor(
            $sourceFile,
            $this->_filesystem->getDirectoryWrite(DirectoryList::ROOT),
            ','
        );
        $this->getImport()->setData([
            'entity' => $this->getEntityType(),
            'behavior'  => 'append',
            'validation_strategy'   => 'validation-stop-on-errors',
            'allowed_error_count' => '10',
            '_import_field_separator' => ',',
            '_import_multiple_value_separator' => ',',
            'import_images_file_dir' => ''
        ]);
 
        $validationResult = $this->getImport()->validateSource($source);
        $errorAggregator = $this->getImport()->getErrorAggregator();
        if ($errorAggregator->getErrorsCount()) {
            foreach ($this->getErrorMessages($errorAggregator) as $error) {
                $this->log->debug($error);
                throw new \Exception($error);
            }
        }

        $this->getImport()->importSource();
        $errorAggregator = $this->getImport()->getErrorAggregator();
        if ($errorAggregator->getErrorsCount()) {
            foreach ($this->getErrorMessages($errorAggregator) as $error) {
                $this->log->debug($error);
                throw new \Exception($error);
            }
        }
    }

    protected function getErrorMessages(ProcessingErrorAggregator $errorAggregator)
    {
        $messages = [];
        $rowMessages = $errorAggregator->getRowsGroupedByErrorCode([], [AbstractEntity::ERROR_CODE_SYSTEM_EXCEPTION]);
        foreach ($rowMessages as $errorCode => $rows) {
            $messages[] = $errorCode . ' ' . __('in row(s):') . ' ' . implode(', ', $rows);
        }
        return $messages;
    }
}