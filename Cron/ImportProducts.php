<?php
/**
 * Copyright © 2016 Omni Partners Oy. All rights reserved.
 * See COPYING.txt for license detail
 */

namespace Omni\ConsoleImportExport\Cron;

use Omni\ConsoleImportExport\Model\Import;

class ImportProducts
{   
    protected $importModel;
    private $log;

    public function __construct(
        Import $importModel,
        \Psr\Log\LoggerInterface $log
    ) {
        $this->importModel = $importModel;
        $this->log = $log;
    }

    public function execute()
    { 
        $this->importModel
                ->setEntityType(\Omni\ConsoleImportExport\Console\Command\Dataflow::INPUT_ENTITY_PRODUCT))
                ->execute();
    }
}