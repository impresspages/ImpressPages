<?php

namespace Plugin\Test;


class System {
    public function init()
    {
        ipDispatcher()->bind('Application.sendResponse', array($this, 'replaceResponse'));
    }

    public function replaceResponse($response) {
        $response = new \Ip\Response();
        $response->setContent('TEST');
        return $response;
    }
}
