<?php
/**
 * Copyright © 2016 Omni Partners Oy. All rights reserved.
 * See COPYING.txt for license detail
 */

namespace Omni\ConsoleImportExport\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Omni\ConsoleImportExport\Model\Import;
use Omni\ConsoleImportExport\Model\Export;

class Dataflow extends Command
{
    const INPUT_KEY_MODE = 'mode';
    const INPUT_MODE_IMPORT = 'import';
    const INPUT_MODE_EXPORT = 'export';

    const INPUT_KEY_ENTITY = 'entity';
    const INPUT_ENTITY_PRODUCT = 'catalog_product';
    const INPUT_ENTITY_CUSTOMER = 'customer';
    const INPUT_ENTITY_ADDRESS = 'customer_address';
    const INPUT_ENTITY_ADV_PRICE = 'advanced_pricing';

    private $log;
    protected $importModel;
    protected $exportModel;

    public function __construct(
        Import $importModel,
        Export $exportModel,
        \Psr\Log\LoggerInterface $log
    ) {
        $this->importModel = $importModel;
        $this->exportModel = $exportModel;
        $this->log = $log;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('omni:dataflow')
                ->setDescription('Trigger the import/export job with defined arguments.')
                ->setDefinition($this->getInputList());
    }

    public function getInputList()
    {
        $modeOptions[] = new InputArgument(
            self::INPUT_KEY_MODE,
            InputArgument::REQUIRED,
            'Data direction mode ['. self::INPUT_MODE_IMPORT . '|' . self::INPUT_MODE_EXPORT .']'
        );
        $modeOptions[] = new InputArgument(
            self::INPUT_KEY_ENTITY,
            InputArgument::REQUIRED,
            'Entity Type ['. self::INPUT_ENTITY_PRODUCT . '|' . self::INPUT_ENTITY_CUSTOMER . '|' . self::INPUT_ENTITY_ADDRESS . '|' . self::INPUT_ENTITY_ADV_PRICE . '|' . '.....' .']'
        );
        return $modeOptions;
    }

    public function validate(InputInterface $input)
    {
        $errors = [];
        $acceptedValues = ' Accepted values for ' . self::INPUT_KEY_MODE . ' are \''
            . self::INPUT_MODE_IMPORT . '\' or \'' . self::INPUT_MODE_EXPORT . '\'';

        $inputMode = $input->getArgument(self::INPUT_KEY_MODE);
        if (!$inputMode) {
            $errors[] = 'Missing argument \'' . self::INPUT_KEY_MODE .'\'.' . $acceptedValues;
        } elseif (!in_array($inputMode, [self::INPUT_MODE_IMPORT, self::INPUT_MODE_EXPORT])) {
            $errors[] = $acceptedValues;
        }

        
        $inputEnity = $input->getArgument(self::INPUT_KEY_ENTITY);
        if (!$inputEnity) {
            $errors[] = 'Missing argument \'' . self::INPUT_KEY_ENTITY .'\'.';
        }

        return $errors;
    }
 
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $errors = $this->validate($input);
        if ($errors) {
            throw new \InvalidArgumentException(implode("\n", $errors));
        }

        $returnValue = \Magento\Framework\Console\Cli::RETURN_SUCCESS;
        try {
            switch ($input->getArgument(self::INPUT_KEY_MODE)) {
                case self::INPUT_MODE_IMPORT:
                    $this->importModel
                        ->setEntityType($input->getArgument(self::INPUT_KEY_ENTITY))
                        ->execute();
                    break;
                case self::INPUT_MODE_EXPORT:
                    $this->exportModel
                        ->setEntityType($input->getArgument(self::INPUT_KEY_ENTITY))
                        ->execute();
                    break;
                default:
                    
                    break;
            }  
        } catch (\Exception $e) {
            $output->writeln($e->getMessage() . PHP_EOL);
            $returnValue = \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }
        return $returnValue;
    }
}