=== Payer for WooCommerce ===
Contributors: payertech, krokedil, NiklasHogefjord
Tags: ecommerce, e-commerce, woocommerce, payer, checkout
Requires at least: 4.7
Tested up to: 4.9.4
Requires PHP: 5.6
Stable tag: trunk
WC requires at least: 3.0.0
WC tested up to: 3.3.1
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html


== DESCRIPTION ==
Payer for WooCommerce is a plugin that extends WooCommerce, allowing you to take payments via Payers payment methods.

= Get started =
To get started with Payer you need to [sign up](https://www.payer.se/) for an account.

More information on how to get started can be found in the [plugin documentation](http://docs.krokedil.com/documentation/payer-for-woocommerce/).


== INSTALLATION	 ==
1. Download the latest release zip file or install it directly via the plugins menu in WordPress Administration.
2. If you use the WordPress plugin uploader to install this plugin skip to step 4.
3. Unzip and upload the entire plugin directory to your /wp-content/plugins/ directory.
4. Activate the plugin through the 'Plugins' menu in WordPress Administration.
5. Go WooCommerce Settings --> Payment Gateways and configure your Payer settings.
6. Read more about the configuration process in the [plugin documentation](http://docs.krokedil.com/documentation/payer-for-woocommerce/).


== Frequently Asked Questions ==

= Where can I find Payer for WooCommerce documentation? =
For help setting up and configuring Payer for WooCommerce please refer to our [documentation](http://docs.krokedil.com/documentation/payer-for-woocommerce/).



== CHANGELOG ==
= 2018.05.08  	- version 0.1.2 =
* Fix - Fixed free shipping error.
* Fix - Added Swedish translation for Get Address.
* Fix - Added prefixes to function that register gateways to prevent error with old plugin.
* Fix - We now send the used charset with the order to prevent malformed order details.
* Fix - Added function to show settings fields on direct invoice to prevent an error.
* Fix - Fixed a spelling error on Percentage.

= 2018.02.28  	- version 0.1.1 =
* Fix - Fixed Installment to only show if enabled.
* Fix - Fixed so masking is removed on order completion without payer being the gateway.
* Enhancement - Showing messages on get address success or failure.
* Enhancement - Fixed some translations.
* Enhancement - Added setting for enabling order management and get address function.

= 2018.02.16  	- version 0.1.0 =
* First release on wordpress.org.