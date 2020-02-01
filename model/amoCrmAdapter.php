<?php
include './vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\FileCookieJar;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;


class amoCrmAdapter
{

    private $subdomain;
    private  $cookieJar;
    private  $httpClient;
    private  $log;

    private  $login;
    private  $hash;
    /**
     * amoCrmAdapter constructor.
     * @param string $subdomain
     */
    public function __construct(string $subdomain, string $login, string $hash)
    {
        $this->subdomain = $subdomain;
        $this->login = $login;
        $this->hash = $hash;
        $cookieFile = __DIR__ . '/../cookies/'.$this->subdomain.'.txt';
        $this->cookieJar = new FileCookieJar($cookieFile, true);

        $this->httpClient = new Client([
            'base_uri' => 'https://' . $this->subdomain . '.amocrm.ru/',
            'cookies' => $this->cookieJar,
        ]);

        $this->log = new Logger('test');
        $hendler = new StreamHandler(__DIR__.'/../Log/logFile.txt',Logger::ERROR);
        $formatter = new LineFormatter("[%datetime%]: %level_name%: %message%\n", "Y-m-d H:i:s");
        $hendler->setFormatter($formatter);
        $this->log->pushHandler($hendler);
    }

    /**
     * @param string $login
     * @param string $hash
     * @return int
     * Авторизация через GuzzleHTTP с сохранением куков
     */
    public function autorization() : int
    {
        if (isset($this->httpClient)) {
            try {
                $r = $this->httpClient->request('POST', 'private/api/auth.php', [
                    'json' => [
                        'USER_LOGIN' => $this->login,
                        'USER_HASH' => $this->hash,
                    ]
                ]);
                return $r->getStatusCode();
            } catch (Exception $e) {
                $error = $e->getMessage();
                $user = $this->login;
                $subdomain = $this->subdomain;
                $this->log->error("Ошибка авторизации. Код ответа: $error, пользователь: $user, субдомен: $subdomain");
                return false;
            }
        } else {
            return false;
        }
    }

    private function errorHandling(Exception $e,string $query)
    {            switch ($e->getCode()) {
        case 401:
            $this->autorization();
            try {
                $r = $this->httpClient->request('GET', $query);
                return json_decode($r->getBody()->getContents());
            } catch (Exception $e) {
                $queryText = "$query";
                $error = $e->getMessage();
                $user = $this->login;
                $subdomain = $this->subdomain;
                $this->log->error("Запрос: $queryText, ответ: $error, пользователь: $user, субдомен: $subdomain");
                return false;
            }
            break;
        default:
            $queryText = $query;
            $error = $e->getMessage();
            $user = $this->login;
            $subdomain = $this->subdomain;
            $this->log->error("Запрос: $queryText, ответ: $error, пользователь: $user, субдомен: $subdomain");
            return false;
            break;
    }
    }


    /**
     * @param $query - запрос
     * @return mixed
     */
    public function contactQuery($query)
    {
        try {
            $r = $this->httpClient->request('GET', 'api/v2/contacts/?query=' . $query);
            return json_decode($r->getBody()->getContents());
        } catch (Exception $e) {
            $this->errorHandling($e,'api/v2/contacts/?query=' . $query);
            return false;
        }
    }

    /**
     * @param $query - запрос
     * @return mixed
     */
    public function leadQuery($query)
    {
        try {
        $r = $this->httpClient->request('GET', 'api/v2/leads?query=' . $query);
        return json_decode($r->getBody()->getContents());
        } catch (Exception $e) {
            $this->errorHandling($e,'api/v2/leads?query=' . $query);
            return false;
        }
    }

    /**
     * @param $query - запрос
     * @return mixed
     */
    public function companyQuery($query)
    {
        try {
            $r = $this->httpClient->request('GET', 'api/v2/companies?query=' . $query);
            return json_decode($r->getBody()->getContents());
        } catch (Exception $e) {
            $this->errorHandling($e,'api/v2/companies?query=' . $query);
            return false;
        }
    }

    /**
     * @param $query - запрос
     * @param $type - используеться при выборки примечаний (contact|lead)
     * @return mixed
     */
    public function noteQuery($query, $type)
    {
        try {
            $r = $this->httpClient->request('GET', 'api/v2/notes?type=' . $type . '&query=' . $query);
            return json_decode($r->getBody()->getContents());
        } catch (Exception $e) {
            $this->errorHandling($e,'api/v2/notes?type=' . $type . '&query=' . $query);
            return false;
        }


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