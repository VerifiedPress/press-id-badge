# Firefox Browser Extension
For testing the extension, see [public/](../../public/README.md)

[Porting Chrome extenstion to Firefox](https://extensionworkshop.com/documentation/develop/porting-a-google-chrome-extension/)

# Releases
- 1.0.2 submitted to [https://addons.mozilla.org](https://addons.mozilla.org) on August 1, 2025
- 1.0.1 not released
- 1.0.0 not released

# Testing Firefox Extensions
Navigate to [about:debugging](about:debugging), 
[this firefox](images/testing.png)
then choose **This Firefox**, followed by pressing **Load Temporary Add-on...**, and select the manifest.json,
[manifest](images/manifest.png)
and the extension is loaded and ready to use.
[extension](images/extension.png)
then select the extensions management from the browser toolbar,
[toolbar](images/toolbar.png)
and select the settings for the extension.
[settings](images/extension-settings.png)
and select **Pin to Toolbar**

then start the PHP server in the public/ directory

```sh
cd public
php -S localhost:8082 -t .
```

and navigate to [http://localhost:8082](http://localhost:8082),

then perform the tests in [public/](../../public/README.md)

