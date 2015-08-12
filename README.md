# f3-translator
Translation Filters for FatFree Framework (Supports Yandex and Microsoft Translator), Allows dynamic translations.

### Version
0.1


### Installation

Copy the `<translator>` folder to an auto-loaded path eg `lib` or path from (`$f3->set('AUTOLOAD',<paths>);`)

Set config for either Yandex or Microsoft

```ini
[TRANSLATE]
YANDEX.APIKEY = <YOUR YANDEX API KEY>
MICROSOFT.CLIENTID = <YOUR MICROSOFT CLIENT ID>
MICROSOFT.CLIENTSECRET = <YOUR MICROSOFT CLIENT SECRET>
# ONERROR = 
```

Get Yandex API KEY [HERE](https://tech.yandex.com/keys/?service=trnsl).

Get Microsoft Client Details  [HERE](https://datamarket.azure.com/developer/applications/).

A guide to setting up Microsoft translator  [HERE](http://blogs.msdn.com/b/translation/p/gettingstarted1.aspx).

### Usage
```php
$f3->set('TRANSLATE.ONERROR',function($code,$message){
    // var_dump($code, $message); //Handle your errors
});
$f3->set('TRANSLATE.LANG','ru'); // Default Language to translate to, can be gotten from $_GET[] or $_SESSION[]
\Template::instance()->filter('translate','\Translator\Microsoft::instance()->translate');
//\Template::instance()->filter('translate','\Translator\Yandex::instance()->translate'); //Choose one.
```
Now you can use `translate` filter in templates.
```html
<p><strong>{{"Book Details"| translate}}</strong></p>
```
Outputs
```html
<strong>Информации о книге</strong>
```
You dont need to specify the `TRANSLATE.LANG` parameter: E.g. to translate to Spanish
```html
<p><strong>{{ @var, "es" | translate }}</strong></p>
```
### Notes
Language codes are different for Yandex and Microsoft

* [Microsoft Codes](https://msdn.microsoft.com/en-us/library/hh456380.aspx)
* [Yandex Codes] (https://tech.yandex.com/translate/doc/dg/concepts/langs-docpage/)

### To-Do
*  Google Translate API
