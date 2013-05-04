# Google Tag Manager

A standard implementation of [Google Tag Manager][1] for [Magento][2], with data layer support for transaction and visitor data.

## Features

### Built in data layer support

Use the data layer to enable Google Analytics e-commerce tracking using Google Tag Manager's native tags. You can also build more flexible macros in GTM using visitor data.

### The future

Additional data layer work is planned for future releases, starting with site search data.

This extension is made available for free. Any donations toward maintaining it and adding new features as Google Tag Manager evolves are greatly appreciated.

[![Donate via PayPal](https://www.paypalobjects.com/en_GB/i/btn/btn_donate_LG.gif)][3]

## Installation

The latest version of this extension can be downloaded via [Magento Connect][4] and installed via Magento Connect Manager.

Alternatively, you can also clone or download this repository and copy the app directory into the root of your Magento installation (not recommended unless you're sure you know what you're doing).

## Usage

Log in to your Magento installation's admin and navigate to `System > Configuration > Sales > Google API`. If installed correctly, you should see a new section below Google Analytics for Google Tag Manager.

### Deploying the tag container

To deploy the tag container on all your pages, switch the `Enable` option to `Yes` and enter your `Container Public ID`. This can be found in the Google Tag Manager interface, and takes the form `GTM-XXXX`.

### Enabling the data layer

The data layer provides Google Tag Manager with additional data that can be used to form macros which can in turn be used for creating rules that dictate when specific tags will fire. It is also populated with transaction data when orders are placed, facilitating Google Analytics e-commerce tracking when using the appropriate tag in Google Tag Manager.

The data layer is disabled by default and can be enabled independently for both transaction and visitor data. To do this, change the `Data layer: Transactions` or `Data layer: Visitors` options to `Enable`.

For transactions, there are additional fields for `transactionType` and `transactionAffiliation` that can take custom values, depending on the store view. These can be left blank if desired, or populated to allow Google Tag Manager to fire different tags for different stores.

## Support

Please report bugs and feature requests using the [Github issue tracker][6].

[1]:http://www.google.com/tagmanager
[2]:http://www.magentocommerce.com/
[3]:https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=XZMM6SFDTPCAA
[4]:http://www.magentocommerce.com/magento-connect/
[5]:https://support.google.com/tagmanager/
[6]:https://github.com/CVM/Magento_GoogleTagManager/issues