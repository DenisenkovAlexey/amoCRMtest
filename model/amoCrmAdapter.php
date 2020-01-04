<?php
include './vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\FileCookieJar;


class amoCrmAdapter
{

    private $subdomain;
    private $cookieJar;
    private $httpClient;
    private $amo;
    private $arrayEntity = ['contact', 'lead', 'company', 'note'];
    /**
     * amoCrmAdapter constructor.
     * @param string $subdomain
     */
    public function __construct(string $subdomain)
    {
        $this->subdomain = $subdomain;
        $cookieFile = __DIR__ . '/../cookies/'.$this->subdomain.'.txt';
        $this->cookieJar = new FileCookieJar($cookieFile, true);

        $this->httpClient = new Client(['cookies' => $this->cookieJar]);
    }

    /**
     * @param string $login
     * @param $hash
     * @return int
     * Авторизация через GuzzleHTTP с сохранением куков
     */
    public function autorizationFromGuzzleHttp(string $login, string $hash)
    {
        if (isset($this->httpClient)) {
            $r = $this->httpClient->request('POST', 'https://' . $this->Subdomain . '.amocrm.ru/private/api/auth.php',
                [
                    'form_params' => [
                        'USER_LOGIN'    =>  $login,
                        'USER_HASH'     =>  $hash,
                        'type'          =>  'json',
                    ]
                ]
            );
            return $r->getBody(Sub);
        } else {
            return null;
        }
    }

    public function autorization(string $login, string $hash)
    {
        $this->amo = new \AmoCRM\Client($this->subdomain,$login,$hash);
        return true;
    }


    /**
     * @param $entity - выборку  какой сущности делать(текст): contact - контакт, lead - сделка, company- компания, note - примечания
     * @param $query - запрос
     * @param null $type используеться при выборки примечаний (contact|lead)
     * @return mixed
     */
    public function Query($entity, $query, $type = null)
    {
        if (in_array($entity,$this->arrayEntity)) {
            $result = $this->amo->$entity->apiList([ 'type' => $type,'query' => $query]);
        } else {
            $result = null;
        }
        return $result;
    }

}