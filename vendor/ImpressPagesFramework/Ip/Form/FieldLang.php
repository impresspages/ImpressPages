<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Form;


/**
 * Multilingual form field.
 * All Multilingual form fields have to extend this class so that any plugin could check if
 * input field object is multilingual or not using following code: $object instanceof \Ip\Form\FieldLang
 *
 * @package Ip\Form
 */
abstract class FieldLang extends Field
{

}
