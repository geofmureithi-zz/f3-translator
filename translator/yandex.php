<?php

namespace Translator;
class Yandex extends \Prefab
{
    private $web;
    protected $endpoint = 'https://translate.yandex.net/api/v1.5/tr.json/';

    function __construct()
    {
        $this->web = \Web::instance();
        $this->apiKey = \Base::instance()->get('TRANSLATE.YANDEX.APIKEY');
    }

    public function translate($val, $lang = null)
    {
        $fw = \Base::instance();
        if (empty($lang))
            $lang = $fw->get('TRANSLATE.LANG');
        $_lang = $fw->get('LANGUAGE');

        $__lang = explode(',', $_lang);
        $lang = $__lang[1] . '-' . $lang;
        if ($fw->get('CACHE')) {
            $cache = \Cache::instance();
            if ($cache->exists(md5('translate.yandex.' . $lang . '.' . $val)))
                return $cache->get(md5('translate.yandex.' . $lang . '.' . $val));
        }
        $url = $this->endpoint . 'translate?key=' . $this->apiKey . '&lang=' . $lang . '&text=' . urlencode($val);
        $response = $this->web->request($url);
        $translation = json_decode($response['body'], true);
        if ($translation['code'] == 200) {
            if ($fw->get('CACHE')) {
                $cache = \Cache::instance();
                $cache->set(md5('translate.yandex.' . $lang . '.' . $val), $translation['text'][0]);
            }
            return $translation['text'][0];
        } else {
            $error_handler = $fw->get('TRANSLATE.ONERROR');
            if (!empty($error_handler))
                $fw->call($error_handler, array($translation['code'], $translation['message']));
        }
        return $val; //Fallback


    }

} 
