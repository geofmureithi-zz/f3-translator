<?php

namespace Translator;
class Microsoft extends \Prefab
{
    private $web;
    protected $endpoint = 'http://api.microsofttranslator.com/v2/Http.svc/Translate';
    protected $authUrl = 'https://datamarket.accesscontrol.windows.net/v2/OAuth2-13/';
    //Application Scope Url
    protected $scopeUrl = "http://api.microsofttranslator.com";
    //Application grant type
    protected $grantType = "client_credentials";

    function __construct()
    {
        //Client ID of the application.
        $this->clientID = \Base::instance()->get('TRANSLATE.MICROSOFT.CLIENTID');
        //Client Secret key of the application.
        $this->clientSecret = \Base::instance()->get('TRANSLATE.MICROSOFT.CLIENTSECRET');
        $this->web = \Web::instance();
    }

    private function getTokens($force = false)
    {
        if (!$force && \Base::instance()->get('CACHE')) {
            $cache = \Cache::instance();
            if ($cache->exists('translate.microsoft.token'))
                return $cache->get('translate.microsoft.token');
        }
        $paramArr = array(
            'grant_type' => $this->grantType,
            'scope' => $this->scopeUrl,
            'client_id' => $this->clientID,
            'client_secret' => $this->clientSecret
        );
        $paramArr = http_build_query($paramArr);
        $opts =
            array(
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => $paramArr
            );

        $response = $this->web->request($this->authUrl, $opts);
        $objResponse = json_decode($response['body']);
        if (\Base::instance()->get('CACHE')) {
            $cache = \Cache::instance();
            $cache->set('translate.microsoft.token', $objResponse->access_token,5);
        }
        return $objResponse->access_token;

    }

    public function translate($val, $lang = null)
    {
        $fw = \Base::instance();
        if (empty($lang))
            $lang = $fw->get('TRANSLATE.LANG');
        $_lang = $fw->get('LANGUAGE');

        $__lang = explode(',', $_lang);
        $lang_code = $__lang[1] . '-' . $lang;
        if ($fw->get('CACHE')) {
            $cache = \Cache::instance();
            if ($cache->exists(md5('translate.microsoft.' . $lang_code . '.' . $val)))
                return $cache->get(md5('translate.microsoft.' . $lang_code . '.' . $val));
        }


        $params = "?text=" . urlencode($val) . "&to=" . $lang . "&from=" . $$__lang[1];
        $url = $this->endpoint . $params;

        $authHeader = "Authorization: Bearer " . $this->getTokens(true);
        $opts =
            array(
                'header' => array($authHeader, "Content-Type: text/xml"),

            );
        $response = $this->web->request($url, $opts);
        $xmlObj = simplexml_load_string($response['body']);
        foreach ((array)$xmlObj[0] as $v) {
            $translatedStr = $v;
        }
        if ((string) $translatedStr) {
            if ($fw->get('CACHE')) {
                $cache = \Cache::instance();
                $cache->set(md5('translate.microsoft.' . $lang_code . '.' . $val), $translatedStr);
            }
            return $translatedStr;
        } else {
            $error_handler = $fw->get('TRANSLATE.ONERROR');
            if (!empty($error_handler))
                $this->getTokens(true); //Renew Token
            $e = (array)$xmlObj->body->p;
            $fw->call($error_handler, array($e[3], $e[2]));
        }
        return $val; //Fallback


    }

} 
