<?php
include './vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\FileCookieJar;


class amoCrmAdapter
{

    private $subdomain;
    private $cookieJar;
    private $httpClient;
    /**
     * amoCrmAdapter constructor.
     * @param string $subdomain
     */
    public function __construct(string $subdomain)
    {
        $this->subdomain = $subdomain;
        $cookieFile = __DIR__ . '/../cookies/'.$this->subdomain.'.txt';
        $this->cookieJar = new FileCookieJar($cookieFile, true);

        $this->httpClient = new Client([
            'base_uri' => 'https://' . $this->subdomain . '.amocrm.ru/',
            'cookies' => $this->cookieJar,
        ]);
    }

    /**
     * @param string $login
     * @param string $hash
     * @return int
     * Авторизация через GuzzleHTTP с сохранением куков
     */
    public function autorization(string $login, string $hash) : int
    {
        if (isset($this->httpClient)) {
            try {
                $r = $this->httpClient->request('POST', 'private/api/auth.php', [
                    'json' => [
                        'USER_LOGIN' => $login,
                        'USER_HASH' => $hash,
                    ]
                ]);
                return $r->getStatusCode();
            } catch (Exception $e) {
                return $e->getCode();
            }
        } else {
            return null;
        }
    }

    /**
     * @param $query - запрос
     * @return mixed
     */
    public function contactQuery($query)
    {
        $r = $this->httpClient->request('GET', 'api/v2/contacts/?query='.$query);
        return json_decode($r->getBody()->getContents());
    }

    /**
     * @param $query - запрос
     * @return mixed
     */
    public function leadQuery($query)
    {
        $r = $this->httpClient->request('GET', 'api/v2/leads?query='.$query);
        return json_decode($r->getBody()->getContents());
    }

    /**
     * @param $query - запрос
     * @return mixed
     */
    public function companyQuery($query)
    {
        $r = $this->httpClient->request('GET', 'api/v2/companies?query='.$query);
        return json_decode($r->getBody()->getContents());
    }

    /**
     * @param $query - запрос
     * @param $type - используеться при выборки примечаний (contact|lead)
     * @return mixed
     */
    public function noteQuery($query, $type)
    {
        $r = $this->httpClient->request('GET', 'api/v2/notes?type='.$type.'&query='.$query);
        return json_decode($r->getBody()->getContents());


    }

    /**
     * @param $entity - выборку  какой сущности делать(текст): contact - контакт, lead - сделка, company- компания, note - примечания
     * @param $query - запрос
     * @param null $type используеться при выборки примечаний (contact|lead)
     * @return mixed
     */
    public function Query($entity, $query, $type = null)
    {
        switch ($entity) {
            case 'contact':
                return $this->contactQuery($query);
                break;
            case 'lead':
                return $this->leadQuery($query);
                break;
            case 'company':
                return $this->companyQuery($query);
                break;
            case 'note':
                return $this->noteQuery($query,$type);
                break;
            default:
                return null;
                break;
        }
    }

}