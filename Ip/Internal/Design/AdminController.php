<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal\Design;


use Ip\ServiceLocator;
use Ip\Response\JsonRpc;

class AdminController extends \Ip\Controller
{


    public function index()
    {

        ipAddJs('Ip/Internal/Core/assets/js/jquery-ui/jquery-ui.js');
        ipAddCss('Ip/Internal/Core/assets/js/jquery-ui/jquery-ui.css');
        ipAddJs('Ip/Internal/Core/assets/js/easyXDM/easyXDM.min.js');
        ipAddJs('Ip/Internal/Design/assets/options.js');
        ipAddJs('Ip/Internal/Design/assets/market.js');
        ipAddJs('Ip/Internal/Design/assets/design.js');
        ipAddJs('Ip/Internal/Design/assets/pluginInstall.js');
        ipAddJs('Ip/Internal/System/assets/market.js');


        $model = Model::instance();

        $themes = $model->getAvailableThemes();

        $model = Model::instance();
        $theme = $model->getTheme(ipConfig()->theme());
        $options = $theme->getOptionsAsArray();

        $themePlugins = $model->getThemePlugins();
        $installedPlugins = \Ip\Internal\Plugins\Service::getActivePluginNames();
        $notInstalledPlugins = [];

        //filter plugins that are already installed
        foreach ($themePlugins as $plugin) {
            if (!empty($plugin['name']) && (!in_array($plugin['name'], $installedPlugins) || !is_dir(
                        ipFile('Plugin/' . $plugin['name'])
                    ))
            ) {
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
            'theme' => $model->getTheme(ipConfig()->theme()),
            'plugins' => $notInstalledPlugins,
            'availableThemes' => $themes,
            'marketUrl' => $model->getMarketUrl(),
            'showConfiguration' => !empty($options),
            'contentManagementUrl' => ipConfig()->baseUrl() . '?aa=Content.index',
            'contentManagementText' => __('Manage content', 'Ip-admin', false)
        );
        $contentView = ipView('view/layout.php', $data);

        ipResponse()->setLayoutVariable('removeAdminContentWrapper', true);

        return $contentView->render();
    }

    public function installPlugin()
    {
        ipRequest()->mustBePost();
        $postData = ipRequest()->getPost();

        if (empty($postData['params']['pluginName'])) {
            throw new \Exception("Missing required parameters");
        }
        $pluginName = $postData['params']['pluginName'];

        $model = Model::instance();
        try {
            $model->installThemePlugin($pluginName);

            $_SESSION['module']['design']['pluginNote'] = __('Plugin has been successfully installed.', 'Ip-admin');

            return JsonRpc::result(1);
        } catch (\Exception $e) {
            return JsonRpc::error($e->getMessage(), $e->getCode());
        }

    }


    public function downloadThemes()
    {

        ipRequest()->mustBePost();
        $themes = ipRequest()->getPost('themes');

        if (!is_writable(Model::instance()->getThemeInstallDir())) {
            return JsonRpc::error(
                __(
                    'Directory is not writable. Please check your email and install the theme manually.',
                    'Ip-admin',
                    false
                ),
                777
            );
        }

        try {
            if (!is_array($themes)) {
                return JsonRpc::error(__('Download failed: invalid parameters', 'Ip-admin', false), 101);
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
        } catch (\Ip\Exception $e) {
            return JsonRpc::error($e->getMessage(), $e->getCode());
        } catch (\Exception $e) {
            return JsonRpc::error(__('Unknown error. Please see logs.', 'Ip-admin', false), 987);
        }

        return JsonRpc::result(array('themes' => $themes));
    }

    /**
     * @throws \Ip\Exception
     */
    public function installTheme()
    {
        $request = ServiceLocator::request();
        $request->mustBePost();

        $themeName = $request->getPost('themeName');
        if (empty($themeName)) {
            throw new \Ip\Exception('Invalid arguments.');
        }

        $model = Model::instance();

        try {
            $model->installTheme($themeName);
        } catch (\Ip\Exception $e) {
            return JsonRpc::error($e->getMessage());
        }

        // TODO jsonrpc
        return new \Ip\Response\Json(array('status' => 'success'));
    }

    public function updateConfig()
    {
        ipRequest()->mustBePost();

        $configModel = ConfigModel::instance();

        $form = $configModel->getThemeConfigForm(ipConfig()->theme());

        $post = ipRequest()->getPost();

        $errors = $form->validate($post);

        if ($errors) {
            return JsonRpc::error('Invalid form');
        }

        $configModel = ConfigModel::instance();
        $model = Model::instance();
        $theme = $model->getTheme(ipConfig()->theme());
        if (!$theme) {
            throw new \Ip\Exception("Theme doesn't exist");
        }

        $options = $theme->getOptionsAsArray();

        $valuesToStore = [];
        foreach ($options as $option) {
            if (empty($option['name'])) {
                continue;
            }

            $field = $form->getField($option['name']);
            if (!$field) {
                continue;
            }

            switch ($option['type']) {
                case 'checkbox':
                    /**
                     * @var \Ip\Form\Field\Checkbox $field
                     */
                    $value = $field->isChecked($post, $option['name']);
                    break;
                case 'RepositoryFile':
                    $value = '';
                    if (!empty($post[$option['name']][0])) {
                        $value = $post[$option['name']][0];
                    }
                    break;
                default:
                    $value = $field->getValueAsString($post, $option['name']);
            }
            $valuesToStore[$option['name']] = $value;
        }


        $valuesToStore = ipFilter('ipDesignOptionsSave', $valuesToStore);

        foreach($valuesToStore as $key => $value) {
            $configModel->setConfigValue(ipConfig()->theme(), $key, $value);
        }




        $lessCompiler = LessCompiler::instance();
        $lessCompiler->rebuild(ipConfig()->theme());
        \Ip\Internal\Core\Service::invalidateAssetCache();


        ipAddJsVariable('ipModuleDesignConfiguration', Helper::getConfigurationBoxHtml());

        return JsonRpc::result(true);
    }


    /**
     * Compile LESS CSS in real time and output the content
     */
    public function realTimeLess()
    {

        $file = ipRequest()->getRequest('file');
        if (empty($file)) {
            throw new \Ip\Exception("Required parameter missing");
        }

        $file = basename($file);

        $lessCompiler = LessCompiler::instance();
        $css = $lessCompiler->compileFile(ipConfig()->theme(), $file);

        return new \Ip\Response($css, 'Content-type: text/css');
    }
}
