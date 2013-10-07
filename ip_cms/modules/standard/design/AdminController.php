<?php
/**
 * @package ImpressPages
 *
 */

namespace Modules\standard\design;




use Ip\ServiceLocator;

class AdminController extends \Ip\Controller
{


    public function index()
    {
        $site = \Ip\ServiceLocator::getSite();

        $site->addCss(BASE_URL.LIBRARY_DIR.'css/bootstrap/bootstrap.css');
        $site->addJavascript(BASE_URL.LIBRARY_DIR.'css/bootstrap/bootstrap.js');
        $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/jquery-ui/jquery-ui.js');
        $site->addCss(BASE_URL.LIBRARY_DIR.'js/jquery-ui/jquery-ui.css');
        $site->addCss(BASE_URL.LIBRARY_DIR.'fonts/font-awesome/font-awesome.css');
        $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/easyXDM/easyXDM.min.js');
        $site->addJavascript(BASE_URL.MODULE_DIR.'standard/design/public/options.js');
        $site->addJavascript(BASE_URL.MODULE_DIR.'standard/design/public/market.js');
        $site->addJavascript(BASE_URL.MODULE_DIR.'standard/design/public/design.js');
        $site->addJavascript(BASE_URL.MODULE_DIR.'standard/design/public/pluginInstall.js');
        $site->addCss(BASE_URL.MODULE_DIR.'standard/design/public/design.css');
        $site->addJavascript(BASE_URL.MODULE_DIR.'administrator/system/public/market.js');

        $model = Model::instance();

        $themes = $model->getAvailableThemes();

        $model = Model::instance();
        $theme = $model->getTheme(THEME_DIR, THEME);
        $options = $theme->getOptionsAsArray();

        if (!defined('BACKEND')) {
            define('BACKEND', 1);
        }

        $helper = Helper::instance();
        $contentManagementModule = \Db::getModule(null, 'standard', 'content_management');
        $contentManagementUrl = $helper->generateAdminUrl($contentManagementModule['id']);

        $themePlugins = $model->getThemePlugins();
        $notInstalledPlugins = array();

        //filter plugins that are already installed
        foreach ($themePlugins as $key => $plugin) {
            if (!is_dir(BASE_DIR . PLUGIN_DIR . $plugin->getModuleGroupKey() . '/' . $plugin->getModuleKey())) { //if plugin has been already installed
                $notInstalledPlugins[] = $plugin;
            }
        }


        if (isset($_SESSION['module']['design']['pluginNote'])) {
            $pluginNote = $_SESSION['module']['design']['pluginNote'];
            unset($_SESSION['module']['design']['pluginNote']);
        } else {
            $pluginNote = '';
        }

        $data = array(
            'pluginNote' => $pluginNote,
            'theme' => $model->getTheme(THEME_DIR, THEME),
            'plugins' => $notInstalledPlugins,
            'availableThemes' => $themes,
            'marketUrl' => $model->getMarketUrl(),
            'showConfiguration' => !empty($options),
            'contentManagementUrl' => $contentManagementUrl,
            'contentManagementText' => $contentManagementModule['m_translation']
        );

        $contentView = \Ip\View::create('view/index.php', $data);
        $layout = $this->createAdminView($contentView);
        $site->setOutput($layout->render());
    }

    public function installPlugin()
    {
        $parametersMod = \Ip\ServiceLocator::getParametersMod();
        \Ip\ServiceLocator::getRequest()->mustBePost();
        $postData = \Ip\ServiceLocator::getRequest()->getPost();

        if (empty($postData['params']['pluginGroup']) || empty($postData['params']['pluginName'])) {
            throw new \Exception("Missing required parameters");
        }
        $pluginGroup = $postData['params']['pluginGroup'];
        $pluginName = $postData['params']['pluginName'];

        $model = Model::instance();
        try {
            $model->installThemePlugin($pluginGroup, $pluginName);
            $this->rpcSuccess(1, 1);
            $_SESSION['module']['design']['pluginNote'] = $parametersMod->getValue('standard', 'design', 'admin_translations', 'plugin_installed');
        } catch (\Exception $e) {
            $this->rpcError($e->getCode(), $e->getMessage());
        }

    }


    public function downloadThemes()
    {
        $parametersMod = \Ip\ServiceLocator::getParametersMod();

        $request = ServiceLocator::getRequest();
        $request->mustBePost();

        $themes = $request->getPost('themes');

        if (!is_writable(BASE_DIR.THEME_DIR)) {
            $error = array('jsonrpc' => '2.0', 'error' => array('code' => 777, 'message' => $parametersMod->getValue('standard', 'design', 'admin_translations', 'theme_write_error')), 'id' => null);
            $this->returnJson($error);
            return;
        }

        try {
            if (!is_array($themes)) {
                $error = array('jsonrpc' => '2.0', 'error' => array('code' => 101, 'message' => 'Download failed: invalid parameters'), 'id' => null);
                $this->returnJson($error);
                return;
            }

            if (function_exists('set_time_limit')) {
                set_time_limit(count($themes) * 180 + 30);
            }

            $themeDownloader = new ThemeDownloader();

            foreach ($themes as $theme) {
                if (!empty($theme['url']) && !empty($theme['name']) && !empty($theme['signature'])) {
                    $themeDownloader->downloadTheme($theme['name'], $theme['url'], $theme['signature']);
                }
            }
        } catch (\Ip\CoreException $e) {
            $error = array('jsonrpc' => '2.0', 'error' => array('code' => 234, 'message' => $e->getMessage()), 'id' => null);
            $this->returnJson($error);
            return;
        } catch (\Exception $e) {
            $error = array('jsonrpc' => '2.0', 'error' => array('code' => 987, 'message' => 'Unknown error. Please see logs.'), 'id' => null);
            $this->returnJson($error);
            return;
        }

        $response = array(
            "jsonrpc" => "2.0",
            "result" => array(
                "themes" => $themes,
            ),
            "id" => null,
        );

        $this->returnJson($response);
    }

    /**
     * @throws \Ip\CoreException
     */
    public function installTheme()
    {
        $request = ServiceLocator::getRequest();
        $request->mustBePost();

        $themeName = $request->getPost('themeName');
        if (empty($themeName)) {
            throw new \Ip\CoreException('Invalid arguments.');
        }

        $model = Model::instance();

        try {
            $model->installTheme($themeName);
        } catch (\Ip\CoreException $e) {
            $this->returnJson(array('status' => 'error', 'error' => $e->getMessage()));
            return;
        }

        $this->returnJson(array('status' => 'success'));
    }

    public function updateConfig()
    {
        $request = \Ip\ServiceLocator::getRequest();
        $request->mustBePost();

        $configModel = ConfigModel::instance();

        $form = $configModel->getThemeConfigForm(THEME);


        $errors = $form->validate($request->getPost());

        if ($errors) {
            $data = array(
                'status' => 'error',
                'errors' => $errors
            );
        } else {
            $configModel = ConfigModel::instance();
            $model = Model::instance();
            $theme = $model->getTheme(THEME_DIR, THEME);
            if (!$theme) {
                throw new \Ip\CoreException("Theme doesn't exist");
            }

            $options = $theme->getOptionsAsArray();

            foreach($options as $option) {
                if (empty($option['name'])) {
                    continue;
                }

                $field = $form->getField($option['name']);
                if (!$field) {
                    continue;
                }

                switch($option['type']) {
                    case 'check':
                        $value = $field->isChecked($request->getPost(), $option['name']);
                        break;
                    default:
                        $value = $field->getValueAsString($request->getPost(), $option['name']);
                }
                $configModel->setConfigValue(THEME, $option['name'], $value);
            }

            $lessCompiler = LessCompiler::instance();
            $lessCompiler->rebuild(THEME);

        }




    }


    /**
     * Compile LESS CSS in real time and output the content
     */
    public function realTimeLess()
    {
        $site = \Ip\ServiceLocator::getSite();

        $request = \Ip\ServiceLocator::getRequest();
        $params = $request->getRequest();
        if (!isset($params['file'])) {
            throw new \Ip\CoreException("Required parameter missing");
        }

        $file = basename($params['file']);

        $lessCompiler = LessCompiler::instance();
        $css = $lessCompiler->compileFile(THEME, $file);

        header("Content-type: text/css", null, 200);
        $site->setOutput($css);
    }


    protected function rpcError($code, $message)
    {
        $answer = array(
            'jsonrpc' => '2.0',
            'error' => array(
                'code' => $code,
                'message' => $message,
                'id' => 'id'
            )
        );
        $this->returnJson($answer);
        return;
    }

    protected function rpcSuccess($id, $result)
    {
        // Return JSON-RPC response
        $answerArray = array(
            "jsonrpc" => "2.0",
            "result" => $result,
            "id" => $id
        );
        $this->returnJson($answerArray);
        return;
    }
}