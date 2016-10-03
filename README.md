Technical feature
------------------------

This extension is providing the cli and crontab interface for triggering Import/Export jobs in Magento2.


Installation
------------------------

Enter following commands to install module:

```bash
cd MAGE2_ROOT_DIR
# install
composer config repositories.omni_console-import-export git https://github.com/omnipartners/m2_cli_schedule_import_export.git
composer require omni/console-import-export:dev-master
# enable
php bin/magento module:enable Omni_ConsoleImportExport --clear-static-content
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```

Uninstall
------------------------

Enter following commands to disable and uninstall module:

```bash
cd MAGE2_ROOT_DIR
# disable
php bin/magento module:disable Omni_ConsoleImportExport --clear-static-content    
# uninstall
php bin/magento module:uninstall Omni_ConsoleImportExport --clear-static-content
php bin/magento setup:static-content:deploy
```

Usage
------------------------

```bash
bin/magento omni:dataflow --mode=import|export --entity=catalog_product|customer|customer_address|CUSTOM 
```


