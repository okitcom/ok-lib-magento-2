# OK Lib Magento

This is a Magento 2 plugin to implement OK checkout and authentication functionality.

## Installation

Copy the required files to you magento installation

```bash
CP app/ $MAGE_HOME/app/code/Okitcom/OkLibMagento/
CP lib/ $MAGE_HOME/app/code/OK
```

Clear the caches for the app in order to active the plugin.

## Usage
After the installation was successful, the plugin's functionality is disabled by default. In order 
 to set it up login to the magento admin console navigate to:
 
```
'Stores' > 'Configuration' > 'SERVICES' > 'OK'
```

Choose the production environment and fill in the 'Open key' and 'Cash key' provided by OK support.

## Using the OK Cash webhook
The OK Cash service provides a functionality to notify the website when a transaction has reached a 
 final state in the form of a webhook.

The plugin automatically provides an endpoint in the website to listen for callbacks from OK. 
 However, the url of this endpoint must be provided in the Cash service in OK Works. The url must 
 use `https` and looks as follows:
```
Template: {base_url_secure}/oklib/callback/cash
Example:  https://magento2.ok.app/oklib/callback/cash
```