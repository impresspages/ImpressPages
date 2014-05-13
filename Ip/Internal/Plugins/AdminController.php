<?php
namespace Ip\Internal\Plugins;


class AdminController extends \Ip\Controller{

    public function index()
    {
        ipAddJs('Ip/Internal/Core/assets/js/angular.js');
        ipAddJs('Ip/Internal/Plugins/assets/plugins.js');
        ipAddJs('Ip/Internal/Plugins/assets/jquery.pluginProperties.js');
        ipAddJs('Ip/Internal/Plugins/assets/pluginsLayout.js');

        $allPlugins = Model::getAllPlugins();

        $plugins = array();
        foreach($allPlugins as $pluginName) {
            $plugin = Helper::getPluginData($pluginName);
            $plugins[] = $plugin;
        }

        ipAddJsVariable('pluginList', $plugins);
        ipAddJsVariable('ipTranslationAreYouSure', __('Are you sure?', 'Ip-admin', false));

        $data = array ();
        $view = ipView('view/layout.php', $data);

        ipResponse()->setLayoutVariable('removeAdminContentWrapper',true);

        return $view->render();
    }

    public function pluginPropertiesForm()
    {
        $pluginName = ipRequest()->getQuery('pluginName');
        if (!$pluginName) {
            throw new \Ip\Exception(__('Missing required parameters', 'Ip-admin'));
        }

        $variables = array(
            'plugin' => Helper::getPluginData($pluginName),
        );

        if (in_array($pluginName, Model::getActivePluginNames())) {
            $variables['form'] = Helper::pluginPropertiesForm($pluginName);
        }

        $layout = ipView('view/pluginProperties.php', $variables)->render();

        $data = array (
            'html' => $layout
        );
        return new \Ip\Response\Json($data);
    }

    public function activate()
    {
        $post = ipRequest()->getPost();
        if (empty($post['params']['pluginName'])) {
            throw new \Ip\Exception(__('Missing parameter', 'Ip-admin'));
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

    public function deactivate()
    {
        $post = ipRequest()->getPost();
        if (empty($post['params']['pluginName'])) {
            throw new \Ip\Exception(__('Missing parameter', 'Ip-admin'));
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

    public function remove()
    {
        $post = ipRequest()->getPost();
        if (empty($post['params']['pluginName'])) {
            throw new \Ip\Exception(__('Missing parameter', 'Ip-admin'));
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

    public function updatePlugin()
    {
        $pluginName = ipRequest()->getPost('pluginName');
        $data = ipRequest()->getPost();

        $result = Helper::savePluginOptions($pluginName, $data);

        if ($result === true) {
            return \Ip\Response\JsonRpc::result($result);
        } else {
            return \Ip\Response\JsonRpc::error(__('Validation failed', 'Ip-admin', false));
        }
    }

}
