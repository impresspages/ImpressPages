<?php
namespace Plugin\ImportExport;

class Model
{

    public static function getForm()
    {
        $form = new \Ip\Form();
        $form->addClass('ipsImportExportForm');


        $field = new \Ip\Form\Field\File(
            array(
                'name' => 'siteFile', //html "name" attribute
                'label' => 'ZIP file:', //field label that will be displayed next to input field
            ));
        $form->addField($field);


        $field = new \Ip\Form\Field\Submit(
            array(
                'value' => 'Import site widget content from file'
            ));
        $field->addClass('ipsImportExportSubmit');
        $form->addField($field);

        $field = new \Ip\Form\Field\Hidden(
            array(
                'name' => 'action',
                'defaultValue' => 'import'
            )
        );

        $form->addField($field);

        $field = new \Ip\Form\Field\Hidden(
            array(
                'name' => 'aa',
                'value' => 'ImportExport.import'
            ));
        $form->addField($field);

        return $form;
    }

    public static function getLanguageIdByUrl($url)
    {

        $ra = ipContent()->getLanguages();

        foreach ($ra as $language){
            if ($language->getUrl() == $url){
                 return $language->getId();
            }
        }

        return false;

    }

    public static function languageExists($url)
    {

//        $ra =  \Ip\Module\Languages\Db::getLanguageByUrl($url);
        if (self::getLanguageIdByUrl($url)){
            return true;
        }else{
            return false;
        }

    }


}