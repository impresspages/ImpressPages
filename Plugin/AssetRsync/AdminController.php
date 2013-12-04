<?php

namespace Plugin\AssetRsync;


use Ip\Response\JsonRpc;
use Symfony\Bridge\Twig\Tests\NodeVisitor\TranslationDefaultDomainNodeVisitorTest;

class AdminController extends \Ip\Controller
{
    public function index()
    {
        ipAddJavascript(ipFileUrl('Plugin/AssetRsync/assets/admin.js'));

        $form = new \Ip\Form();
        $form->addClass('ipsAssetRsyncOptions');
        $field = new \Ip\Form\Field\Hidden(array(
            'name' => 'aa',
            'defaultValue' => 'AssetRsync.saveOptions'
        ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Checkbox(array(
            'name' => 'syncOnCacheClear',
            'label' => __('Sync on cache clear', 'plugin-AssetRsync'),
            'checked' => ipGetOption('AssetRsync.syncOnCacheClear') ? true : false,
        ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Text(array(
            'name' => 'pluginAssetDirectory',
            'label' => __('Plugin asset directory', 'plugin-AssetRsync'),
            'defaultValue' => ipGetOption('AssetRsync.pluginAssetDirectory'),
        ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Text(array(
            'name' => 'pluginAssetUrl',
            'label' => __('Plugin asset url', 'plugin-AssetRsync'),
            'defaultValue' => ipGetOption('AssetRsync.pluginAssetUrl'),
        ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Text(array(
            'name' => 'moduleAssetDirectory',
            'label' => __('Module asset directory', 'plugin-AssetRsync'),
            'defaultValue' => ipGetOption('AssetRsync.moduleAssetDirectory'),
        ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Text(array(
            'name' => 'moduleAssetUrl',
            'label' => __('Module asset url', 'plugin-AssetRsync'),
            'defaultValue' => ipGetOption('AssetRsync.moduleAssetUrl'),
        ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Submit(array(
            'name' => 'submit',
            'defaultValue' => __('Submit', 'plugin-AssetRsync')
        ));
        $form->addField($field);

        $data = array(
            'form' => $form,
        );

        return \Ip\View::create('view/options.php', $data);
    }

    public function saveOptions()
    {
        $request = ipRequest();

        // TODOX validate form

        $syncOnCacheClear = $request->getPost('syncOnCacheClear', false) ? true : false;

        ipSetOption('AssetRsync.syncOnCacheClear', $syncOnCacheClear);

        ipSetOption('AssetRsync.pluginAssetDirectory', $request->getPost('pluginAssetDirectory'));
        ipSetOption('AssetRsync.pluginAssetUrl', $request->getPost('pluginAssetUrl'));
        ipSetOption('AssetRsync.moduleAssetDirectory', $request->getPost('moduleAssetDirectory'));
        ipSetOption('AssetRsync.moduleAssetUrl', $request->getPost('moduleAssetUrl'));

        return JsonRpc::result(true);
    }

    public function syncAssets()
    {
        Model::syncAssets();

        return JsonRpc::result(true);
    }
}