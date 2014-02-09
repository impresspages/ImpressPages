<?php
namespace Ip\Internal\Plugins;


class AdminController extends \Ip\Controller{

    public function index()
    {
        $activePlugins = Service::getActivePluginNames();
        $allPlugins = Model::getAllPlugins();

        $plugins = array();
        foreach($allPlugins as $pluginName)
        {
            $pluginRecord = array(
                'description' => '',
                'title' => $pluginName,
                'name' => $pluginName,
                'version' => '',
                'author' => ''
            );
            $pluginRecord['active'] = in_array($pluginName, $activePlugins);
            $config = Model::getPluginConfig($pluginName);
            if (isset($config['description'])) {
                $pluginRecord['description'] = $config['description'];
            }
            if (isset($config['version'])) {
                $pluginRecord['version'] = $config['version'];
            }
            if (isset($config['title'])) {
                $pluginRecord['title'] = $config['title'];
            }
            if (isset($config['author'])) {
                $pluginRecord['author'] = $config['author'];
            }
            if (isset($config['name'])) {
                $pluginRecord['name'] = $config['name'];
            }
            $plugins[] = $pluginRecord;
        }

        $data = array (
            'plugins' => $plugins
        );

        $view = ipView('view/admin/index.php', $data);

        ipAddJs('Ip/Internal/Plugins/assets/admin.js');

        return $view->render();
    }

    public function activate ()
    {
        $post = ipRequest()->getPost();
        if (empty($post['params']['pluginName'])) {
            throw new \Ip\Exception("Missing parameter");
        }
        $pluginName = $post['params']['pluginName'];

        try {
            Service::activatePlugin($pluginName);
        } catch (\Ip\Exception $e){
            $answer = array(
                'jsonrpc' => '2.0',
                'error' => array(
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                ),
                'id' => null,
            );

            return new \Ip\Response\Json($answer);
            return;
        }

        $answer = array(
            'jsonrpc' => '2.0',
            'result' => array(
                1
            ),
            'id' => null,
        );

        return new \Ip\Response\Json($answer);
    }

    public function deactivate ()
    {
        $post = ipRequest()->getPost();
        if (empty($post['params']['pluginName'])) {
            throw new \Ip\Exception("Missing parameter");
        }
        $pluginName = $post['params']['pluginName'];

        try {
            Service::deactivatePlugin($pluginName);
        } catch (\Ip\Exception $e){
            $answer = array(
                'jsonrpc' => '2.0',
                'error' => array(
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                ),
                'id' => null,
            );

            return new \Ip\Response\Json($answer);
            return;
        }

        $answer = array(
            'jsonrpc' => '2.0',
            'result' => array(
                1
            ),
            'id' => null,
        );

        return new \Ip\Response\Json($answer);
    }

    public function remove ()
    {
        $post = ipRequest()->getPost();
        if (empty($post['params']['pluginName'])) {
            throw new \Ip\Exception("Missing parameter");
        }
        $pluginName = $post['params']['pluginName'];

        try {
            Service::removePlugin($pluginName);
        } catch (\Ip\Exception $e){
            $answer = array(
                'jsonrpc' => '2.0',
                'error' => array(
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                ),
                'id' => null,
            );

            return new \Ip\Response\Json($answer);
            return;
        }

        $answer = array(
            'jsonrpc' => '2.0',
            'result' => array(
                1
            ),
            'id' => null,
        );

        return new \Ip\Response\Json($answer);
    }

}

