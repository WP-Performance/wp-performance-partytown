## Partytown in your WordPress

### 🚨 Be careful, the proxy php is a experimental code. You can use it, but i'm not responsible of the security of your website


This plugin add [Partytown](https://partytown.builder.io/) available for your website


If you use proxy, set the cache lifetime to max 8h for avoid nonce bug.


## For use Proxy PHP, add this line in your wp-config.php.

☝🏻 use it only if you know what you do

```
define('PR_PROXY', true);
```

The proxy is only necessary when service don't have ```Access-Control-Allow-Origin: *``` in response.
For example, Google Analytics, has this CORS headers.
More informations, [here](https://partytown.builder.io/proxying-requests)


## Define proxy key for connected users. In your wp-config.php

```
define('PR_PROXY_KEY', 'key of your choice');
```

## Debug => In your wp-config.php

```
define('PR_DEBUG', true);
```


## Add script for partytown

Juste add type "text/partytown" in ```<script>``` tag

Example :
```
<!-- Google Tag Manager -->
<script type="text/partytown">(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','');</script>
<!-- End Google Tag Manager -->
```


## Launch partytown parsing after cookie acceptation

Dispatch load event for trigger partytown parsing.
```
const load = new Event('load');
window.dispatchEvent(load)
```
