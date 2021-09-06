<?php
namespace Lib\Api;

class RequestError extends \Exception {
    private $responseCode;
    protected $errorCodes = array(
        'NO_NUMBERS' => 'Нет свободных номеров для приёма смс от текущего сервиса',
        'NO_BALANCE' => 'Закончился баланс',
        'BAD_ACTION' => 'Некорректное действие (параметр action)',
        'BAD_SERVICE' => 'Некорректное наименование сервиса (параметр service)',
        'BAD_KEY' => 'Неверный API ключ доступа',
        'ERROR_SQL' => 'Один из параметров имеет недопустимое значение',
        'SQL_ERROR'=> 'Один из параметров имеет недопустимое значение',
        'NO_ACTIVATION' => 'Указанного id активации не существует',
        'BAD_STATUS' => 'Попытка установить несуществующий статус',
        'STATUS_CANCEL' => 'Текущая активация отменена и больше не доступна',
        'BANNED' => 'Аккаунт заблокирован',
        'NO_CONNECTION' => 'Нет соединения с серверами sms-activate',
        'ACCOUNT_INACTIVE' => 'Свободных номеров нет',
        'NO_ID_RENT' => 'Не указан id аренды',
        'INVALID_PHONE' => 'Номер арендован не вами (неправильный id аренды)',
        'STATUS_FINISH' => 'Аренда оплачна и завершена',
        'STATUS_CANCEL' => 'Аренда отменена с возвратом денег',
        'STATUS_WAIT_CODE' => 'Ожидание первой смс',
        'INCORECT_STATUS' => 'Отсутствует или неправильно указан статус',
        'CANT_CANCEL' => 'Невозможно отменить аренду (прошло более 20 мин.)',
        'ALREADY_FINISH' => 'Аренда уже завершена',
        'ALREADY_CANCEL' => 'Аренда уже отменена',

    );

    public function __construct($errorCode) {
        $this->responseCode = $errorCode;
        @$message = "Error in {$this->getFile()}, line: {$this->getLine()}: {$this->errorCodes[$errorCode]}";
        parent::__construct($message);
    }

    public function getResponseCode() {
        return $this->responseCode;
    }


}

class ErrorCodes extends RequestError {
    public function checkExist($errorCode){
        return array_key_exists($errorCode, $this->errorCodes);
    }
}


class SMSActivate {
    private $url = 'https://sms-activate.ru/stubs/handler_api.php';
    private $apiKey;

    public function __construct($apiKey) {
        $this->apiKey = $apiKey;
    }

    public function getBalance() {
        return $this->request(array('api_key' => $this->apiKey, 'action' => __FUNCTION__), 'GET');
    }
        public function getNumbersStatus($country = null, $operator = null){
        $requestParam = array('api_key' => $this->apiKey,'action' => __FUNCTION__);
        if($country){
            $requestParam['country']=$country;
        }
        if($operator &&($country==0 || $country == 1 || $country == 2)){
            $requestParam['service'] = $operator;
        }
        $response = array();
        $changeKeys = $this->request($requestParam, 'GET',true);
        foreach ($changeKeys as $services => $count){
            $services = trim($services,"_01");
            $response[$services] = $count;
        }
        unset($changeKeys);
        return $response;
    }
    public function getNumber($service, $country = null, $forward = 0, $operator = null, $ref = null){
        $requestParam = array('api_key' => $this->apiKey,'action' => __FUNCTION__,'service' => $service,'forward'=>$forward);
        if($country){
            $requestParam['country']=$country;
        }
        if($operator &&($country==0 || $country == 1 || $country == 2)){
            $requestParam['service'] = $operator;
        }
        if($ref){
            $requestParam['ref'] = $ref;
        }
        return $this->request($requestParam, 'POST',null,1);
    }
    public function setStatus($id, $status, $forward = 0){
        $requestParam = array('api_key' => $this->apiKey,'action' => __FUNCTION__,'id' => $id,'status' => $status);

        if($forward){
            $requestParam['forward'] = $forward;
        }

        return $this->request($requestParam,'POST',null,3);
    }

    public function getStatus($id){
        return $this->request(array('api_key' => $this->apiKey,'action' => __FUNCTION__,'id' => $id),'GET',null,2);
    }

    public function getPrices($country = null, $service = null){
        $requestParam = array('api_key' => $this->apiKey,'action' => __FUNCTION__);

        if($country !== null){
            $requestParam['country'] = $country;
        }
        if($service){
            $requestParam['service'] = $service;
        }

        return $this->request($requestParam,'GET',true);
    }
    public function getQiwiRequisites(){
        return $this->request(array('api_key' => $this->apiKey,'action' => __FUNCTION__),'GET',true);
    }

    public function getRentServicesAndCountries($time = 1,$operator="any"){
        $requestParam = array('api_key' => $this->apiKey,'action' => __FUNCTION__,'rent_time'=>$time,'operator'=>$operator);
        return $this->request($requestParam, 'POST',true);
    }

    public function getRentNumber($service, $time = 1, $country=0, $operator="any", $url=''){
        $requestParam = array('api_key' => $this->apiKey,'action' => __FUNCTION__,'service' => $service,'rent_time'=>$time,'operator'=>$operator,'country' => $country,'url'=>$url);
        return $this->requestRent($requestParam, 'POST',true);
    }

    public function getRentStatus($id){
        $requestParam = array('api_key' => $this->apiKey,'action' => __FUNCTION__,'id' => $id);
        return $this->requestRent($requestParam, 'POST',true);
    }
    public function setRentStatus($id,$status){
        $requestParam = array('api_key' => $this->apiKey,'action' => __FUNCTION__,'id' => $id,'status'=>$status);
        return $this->requestRent($requestParam, 'POST',true);
    }
    /**
     * @param $data
     * @param $method
     * @param null $parseAsJSON
     * @return mixed
     */
    private function request($data, $method, $parseAsJSON = null, $getNumber = null) {
        $method = strtoupper($method);

        if (!in_array($method, array('GET', 'POST')))
            throw new \InvalidArgumentException('Method can only be GET or POST');

        $serializedData = http_build_query($data);

        if ($method === 'GET') {
            $result = file_get_contents("$this->url?$serializedData");
        } else {
            $options = array(
                'http' => array(
                    'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method' => 'POST',
                    'content' => $serializedData
                )
            );

            $context = stream_context_create($options);
            $result = file_get_contents($this->url, false, $context);
        }

        $responsError = new ErrorCodes($result);
        $check = $responsError->checkExist($result);
        if ($check) {
            throw new RequestError($result);
        }


        if ($parseAsJSON)
            return json_decode($result,true);


        $parsedResponse = explode(':', $result);

        if ($getNumber == 1) {
            $returnNumber = array('id' => $parsedResponse[1], 'number' => $parsedResponse[2]);
            return $returnNumber;
        }
        if ($getNumber == 2) {
            $returnStatus = array('status' => $parsedResponse[0], 'code' => $parsedResponse[1]);
            return $returnStatus;
        }
        if ($getNumber == 3) {
            $returnStatus = array('status' => $parsedResponse[0]);
            return $returnStatus;
        }


        return $parsedResponse[1];
    }

    private function requestRent($data, $method, $parseAsJSON = null, $getNumber = null) {
        $method = strtoupper($method);

        if (!in_array($method, array('GET', 'POST')))
            throw new \InvalidArgumentException('Method can only be GET or POST');

        $serializedData = http_build_query($data);

        if ($method === 'GET') {
            $result = file_get_contents("$this->url?$serializedData");
        } else {
            $options = array(
                'http' => array(
                    'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method' => 'POST',
                    'content' => $serializedData
                )
            );

            $context = stream_context_create($options);
            $result = file_get_contents($this->url, false, $context);
        }


        if ($parseAsJSON) {
            $result = json_decode($result, true);
//            $responsError = new ErrorCodes($result["message"]);
//            $check = $responsError->checkExist($result["message"]);  // раскоментить если необходимо включить исключения для Аренды
//            if ($check) {
//                throw new RequestError($result["message"]);
//            }
            return $result;
        }


        return $result;
    }



}


