=== Payer for WooCommerce ===
Contributors: payertech, krokedil, NiklasHogefjord
Tags: ecommerce, e-commerce, woocommerce, payer, checkout
Requires at least: 4.7
Tested up to: 5.2.1
Requires PHP: 5.6
Stable tag: trunk
WC requires at least: 3.0.0
WC tested up to: 3.6.4
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
= 2019.09.25  	- version 1.1.4 =
* Fix	        - Only add order note for order completed with Payer if that is the case.

= 2019.07.08  	- version 1.1.3 =
* Enhancement	- Changed when invoices get sent to Payer for direct invoices.

= 2019.07.08  	- version 1.1.2 =
* Fix			- Fixed gateway icons to be loaded from the correct path.

= 2019.07.08  	- version 1.1.1 =
* Fix			- Changed subscription card payments to be charged on completion of order in WooCommerce. Prevents issue due to making the debit call to fast.

= 2019.06.27  	- version 1.1.0 =
* Feature		- Added rental payments as an option for those that have this allowed by Payer.
* Enhancement   - Changed how to get the tax rate.
* Enhancement   - Added filters for PNO fields to change the field used and how it is saved to the order.
* Enhancement   - All gateways now have proper checks to see if they are enabled or not.
* Enhancement   - Updated SDK and added functionality for IP validation skipping and support for Proxys.
* Enhancement   - Direct invoice now supports free trial subscriptions.

= 2018.11.19  	- version 1.0.2 =
* Fix           - Fixed is_available for Direct Invoice. Should not longer show if you dont have it enabled.
* Enhancement   - Removed masking of address data on organizations.
* Enhancement   - Added filter to Personalnummber label.

= 2018.10.23  	- version 1.0.1 =
* Fix           - Fixed a 500 error caused when lacking WooCommerce Subscription.

= 2018.10.08  	- version 1.0.0 =
* Enhancement 	- Added support for WooCommerce subscriptions for Direct invoice and Card payments.
* Enhancement   - Updated the Payer SDK.

= 2018.06.25  	- version 0.2.0 =
* Enhancement 	- Added setting to be able to partake in MasterPass campaigns.
* Enhancement 	- Added setting to add text to the instant purchase buttons.
* Feature		- Added support for wp_add_privacy_policy_content (for GDPR compliance). More info: https://core.trac.wordpress.org/attachment/ticket/43473/PRIVACY-POLICY-CONTENT-HOOK.md.

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