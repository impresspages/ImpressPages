<?php

namespace Plugin\Test;


class System {
    public function init()
    {
        //ipDispatcher()->addEventListener('site.urlChanged', array($this, 'catchLanguageChange'));
        //ipDispatcher()->addEventListener('Application.sendResponse', array($this, 'replaceResponse'));
    }


    public function catchLanguageChange($data){
        var_dump($data);exit;
    }
    public function replaceResponse($response) {
        $response = new \Ip\Response();
        $response->setContent('TEST');
        return $response;
    }
}
