# Issues
The following issues pertains **ONLY** to the chrome extension.

## v1.0.1

### No popup dialog after installing the extension and clicking the browser button

![no-popup](../../images/issues/chrome/v1.0.1/1-nopopup.png)

**Temporary Fix**
1. right-click to inspect

![inspect](../../images/issues/chrome/v1.0.1/2-right-click-inspect.png)

2. view th HTML element and expand the body tag

![expand body tag](../../images/issues/chrome/v1.0.1/3-view-elements-expand-body.png)

3. change the **display: none;** to **display: block;** for the id="settings" block

![change display](../../images/issues/chrome/v1.0.1/4-change-display-from-none-to-block.png)

4. the popup will show

![popup show](../../images/issues/chrome/v1.0.1/5-popup-showing.png)

or download the [latest release](https://github.com/VerifiedPress/press-id-badge/releases), unarchive, switch to developer mode in the browser and install unpack version

**How you can help**
1. fork this repository
2. review the source code and look for the cause
3. identity the fix
4. test the fix
5. check in your changes to your fork
6. submit a pull request

**FIX**
Version: 1.0.2

in the popup.js, the settings block is being set to block and none,

```
document.getElementById("settings").style.display = "block";
document.getElementById("settings").style.display = "none";
```
but really should be
```
document.getElementById("settings").style.display = "block";
document.getElementById("signing").style.display = "none";
```