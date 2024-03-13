# Installation

We recommend installing the extension using [Composer](https://getcomposer.org/). 
You can also install the module locally by downloading the ZIP of the repository
and uploading the files to the `app/code/JcElectronics/ExactOrders` folder of your
website.

Next, install the new module in Magento itself.

```bash
composer require jc-electronics/magento2-exact-orders
bin/magento module:enable JcElectronics_ExactOrders
bin/magento setup:upgrade
```

## Removing the extension

If installed using Composer, just run the following command:

```bash
composer remove jc-electronics/magento2-exact-orders
bin/magento setup:upgrade
```

If you installed the module manually, make sure you remove all the files from the
`app/code/JcElectronics/ExactOrders` folder.