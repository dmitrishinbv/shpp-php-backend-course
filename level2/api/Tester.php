<?php

$tester = new Tester(3);
echo json_encode($tester->getResult());

class Tester
{

    private $version;
    private $linkToApi;

    private $text;
    private $newText;
    private $checked;
    private $id = 0;
    private $user;

    private $result = [];

    public function __construct($version, $linkToApi = 'http://localhost/api/', $text = 'test', $newText = 'test change', $checked = true, $user = ['login' => 'tester', 'pass' => 'testerPass'])
    {
        $this->version = $version;
        $this->linkToApi = $linkToApi;
        $this->text = $text;
        $this->newText = $newText;
        $this->checked = $checked;
        $this->user = $user;
    }

    public function getResult()
    {
        if ($this->version === 2 || $this->version === 3) {
            $this->checkRegister();
            $this->checkLogout();
            $this->checkLogin();
        }
        $this->checkAddItem();
        $this->checkGetItems();
//
        $this->checkChange();
        $this->checkDelete();

        return $this->result;
    }

    private function checkLogin(){
        $action = 'login';
        $result = $this->query($this->getLink($action), json_encode($this->user));
        if($result['code'] === 200){
            if(isset($result['body']['ok']) && $result['body']['ok']){
                $this->result[$action]['status'] = 'successful';
            } else{
                $this->result[$action] = ['status' => 'fail', 'message' => 'answer !== {"ok":true}'];
            }
            return;
        }
        $this->result[$action] = ['status' => 'fail', 'message' => 'code !== 200'];
    }

    private function checkLogout(){
        $action = 'logout';
        $result = $this->query($this->getLink($action));
        $this->checkStatusOk($action, $result['code'], $result['body']);
    }

    function checkStatusOk($action, $code, $body){
        if($code !== 200){
            $this->result[$action] = ['status' => 'fail', 'message' => 'code !== 200'];
            return;
        }
        $this->result[$action] = isset($body['ok']) && $body['ok'] ? ['status' => 'successful'] : ['status' => 'fail', 'message' => 'answer !== {"ok":true}'];
    }

    private function checkRegister()
    {
        $action = 'register';
        $result = $this->query($this->getLink($action), json_encode($this->user));
        $this->checkStatusOk($action, $result['code'], $result['body']);
    }

    private function checkAddItem()
    {
        $result = $this->query($this->getLink('addItem'), json_encode(['text' => $this->text]));
        $this->result['addItem']['status'] = ($result['code'] === 200 && isset($result['body']['id'])) ? 'successful' : 'fail';
    }

    private function checkGetItems()
    {
        $action = 'getItems';
        $result = $this->query($this->getLink($action));
        if ($result['code'] === 200) {
            $items = $result['body']['items'] ?? [];
            $last = end($items);
            $this->id = $last['id'];
            if ($last['text'] === $this->text) {
                $this->result[$action]['status'] = 'successful';
            } else {
                $this->result['addItem'] = ['status' => 'fail', 'message' => "no entry with text: {$this->text}"];
                $this->result[$action] = $this->result['addItem'];
            }
            return;
        }
        $this->result[$action] = ['status' => 'fail', 'message' => 'code !== 200'];
    }

    private function checkChange()
    {
        $data = json_encode([
            'id' => $this->id,
            'text' => $this->newText,
            'checked' => $this->checked
        ]);
        $action = 'changeItem';
        $result = $this->query($this->getLink($action), $data);
        if ($result['code'] === 200) {
            $getItems = $this->query($this->getLink('getItems'));
            if ($getItems['code'] === 200) {
                $lastItem = end($getItems['body']['items']);
                            if ($lastItem['text'] === $this->newText && $lastItem['checked'] == $this->checked) {
                    $this->result[$action]['status'] = 'successful';
                } else {
                    $this->result[$action] = ['status' => 'fail', 'message' => 'the field has not changed or has changed incorrectly'];
                }
            } else {
                $this->result[$action] = ['status' => 'fail', 'message' => 'failed to get list of entries'];
            }
        } else {
            $this->result[$action] = ['status' => 'fail', 'message' => 'code !== 200'];
        }
    }

    private function checkDelete()
    {
        $action = 'deleteItem';
        $result = $this->query($this->getLink($action), json_encode(['id' => $this->id]));

        if ($result['code'] === 200) {
            $getItems = $this->query($this->getLink('getItems'));
            if ($getItems['code'] === 200) {
                if ($getItems['body']['items'] === [] || end($getItems['body']['items'])['id'] !== $this->id) {
                    $this->result[$action]['status'] = 'successful';
                } else {
                    $this->result[$action] = ['status' => 'fail', 'message' => 'item not deleted'];
                }
            } else {
                $this->result[$action] = ['status' => 'fail', 'message' => 'failed to get list of entries'];
            }
        } else {
            $this->result[$action] = ['status' => 'fail', 'message' => 'code !== 200'];
        }
    }

    private function query($link, $data = '')
    {
        $cookieFileName = 'cookie.txt';
        $this->checkCookieFile($cookieFileName);
        $query = curl_init();
        curl_setopt($query, CURLOPT_URL, $link);
        if ($data !== '') {
            curl_setopt($query, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data)
            ]);
            curl_setopt($query, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($query, CURLOPT_HEADER, false);
        curl_setopt($query, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($query, CURLOPT_COOKIEFILE, $cookieFileName);
        curl_setopt($query, CURLOPT_COOKIEJAR, $cookieFileName);
        $body = curl_exec($query);
        $code = curl_getinfo($query, CURLINFO_RESPONSE_CODE);
        curl_close($query);
        return ['body' => json_decode($body, true), 'code' => $code];
    }

    private function checkCookieFile($cookieFileName)
    {
        if (!is_writable(__DIR__)) {
            header('500 Internal Server Error');
            exit('no permissions to write to ' . $cookieFileName);
        }
    }

    private function getLink($action)
    {
        switch ($this->version) {
            case 1 :
                return $this->linkToApi . 'v1/' . $action . '.php';
            case 2 :
                return $this->linkToApi . 'v2/' . $action . '.php';
            case 3 :
                return $this->linkToApi . 'v3/' . 'router.php?action=' . $action;
            default :
                header('400 Bad Request');
                exit('Version must be 1, 2 or 3');
        }
    }
}