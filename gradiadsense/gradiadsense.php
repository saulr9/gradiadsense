<?php
/**
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2020 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Gradiadsense extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'gradiadsense';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Saul Ramos';
        $this->need_instance = 1;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Gradi Adsense');
        $this->description = $this->l('
Display an ad banner');

        $this->confirmUninstall = $this->l('Are you sure to want to uninstall Gradi Adsense?');

        $this->ps_versions_compliancy = array('min' => '1.7.1.0', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {

        
        $languages = Language::getLanguages(false);

        foreach ($languages as $lang)
        {


            $values['GRADIADSENSE_AD_TITLE'][(int)$id_lang] = '10 % OFF';
            $values['GRADIADSENSE_AD_DESCRIPTION'][(int)$id_lang] = 'Get up to a 10 % discount on all our products';
            $values['GRADIADSENSE_AD_TEXT_CTA'][(int)$id_lang] = 'I want it!';
            $values['GRADIADSENSE_AD_URL_CTA'][(int)$id_lang] = '/';

            
            Configuration::updateValue('GRADIADSENSE_AD_TITLE', $values['GRADIADSENSE_AD_TITLE']);
            Configuration::updateValue('GRADIADSENSE_AD_DESCRIPTION', $values['GRADIADSENSE_AD_DESCRIPTION']);
            Configuration::updateValue('GRADIADSENSE_AD_TEXT_CTA', $values['GRADIADSENSE_AD_TEXT_CTA']);
            Configuration::updateValue('GRADIADSENSE_AD_URL_CTA', $values['GRADIADSENSE_AD_URL_CTA']);
            
        }

        Configuration::updateValue('GRADIADSENSE_ENABLE', true);
        Configuration::updateValue('GRADIADSENSE_OVERLAY', NULL);
        Configuration::updateValue('GRADIADSENSE_AD_IMG', null);
        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayHome');
    }
    

    public function uninstall()
    {

        
        Configuration::deleteByName('GRADIADSENSE_ENABLE');
        Configuration::deleteByName('GRADIADSENSE_AD_TITLE');
        Configuration::deleteByName('GRADIADSENSE_AD_DESCRIPTION');
        Configuration::deleteByName('GRADIADSENSE_AD_TEXT_CTA');
        Configuration::deleteByName('GRADIADSENSE_AD_URL_CTA');
        Configuration::deleteByName('GRADIADSENSE_OVERLAY');
        Configuration::deleteByName('GRADIADSENSE_AD_IMG');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        //$html='';
       // $this->_html .= $this->headerHTML();
        if (((bool)Tools::isSubmit('submitGradiadsenseModule')) == true) {
           if($this->_postValidation()) {
                if($this->postProcess()){
                    $this->_html .= $this->displayConfirmation($this->l('The settings have been updated.'));
                }
            }

        }
        
        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');
        
        return $this->_html.$output.$this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitGradiadsenseModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }



    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        

        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable'),
                        'name' => 'GRADIADSENSE_ENABLE',
                        'is_bool' => true,
                        'lang' => true,
                        'desc' => $this->l('Enable this module'),
                        'required' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'file',
                        'label' => $this->trans('Ad Image'),
                        'name' => 'GRADIADSENSE_AD_IMG',
                        'desc' => $this->trans('Upload an image for your ad. JPG, JPEG, PNG or WEBP format.'),
                        'lang' => true,
                        'required' => true
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Enter the ad title'),
                        'name' => 'GRADIADSENSE_AD_TITLE',
                        'label' => $this->l('Ad Title'),
                        'lang' => true,
                        'required' => true,
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Enter the ad descrption'),
                        'name' => 'GRADIADSENSE_AD_DESCRIPTION',
                        'label' => $this->l('Ad Description'),
                        'lang' => true,
                        'required' => true
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Enter the Call to Action Label'),
                        'name' => 'GRADIADSENSE_AD_TEXT_CTA',
                        'label' => $this->l('Call to Action Label'),
                        'lang' => true,
                        'required' => true
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Enter the Call to Action URL'),
                        'name' => 'GRADIADSENSE_AD_URL_CTA',
                        'label' => $this->l('Call to Action URL'),
                        'lang' => true,
                        'required' => true
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Overlay'),
                        'name' => 'GRADIADSENSE_OVERLAY',
                        'is_bool' => true,
                        'desc' => $this->l('Enable ad overlay'),
                        'required' => true,
                        /*'lang' => true,*/
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        $languages = Language::getLanguages(false);
        $fields = array();


        foreach ($languages as $lang) {
            $fields['GRADIADSENSE_AD_TITLE'][$lang['id_lang']] = Tools::getValue('GRADIADSENSE_AD_TITLE_'.$lang['id_lang'], Configuration::get('GRADIADSENSE_AD_TITLE', $lang['id_lang']));
            $fields['GRADIADSENSE_AD_DESCRIPTION'][$lang['id_lang']] = Tools::getValue('GRADIADSENSE_AD_DESCRIPTION_'.$lang['id_lang'], Configuration::get('GRADIADSENSE_AD_DESCRIPTION', $lang['id_lang']));
            $fields['GRADIADSENSE_AD_TEXT_CTA'][$lang['id_lang']] = Tools::getValue('GRADIADSENSE_AD_TEXT_CTA_'.$lang['id_lang'], Configuration::get('GRADIADSENSE_AD_TEXT_CTA', $lang['id_lang']));
            $fields['GRADIADSENSE_AD_URL_CTA'][$lang['id_lang']] = Tools::getValue('GRADIADSENSE_AD_URL_CTA_'.$lang['id_lang'], Configuration::get('GRADIADSENSE_AD_URL_CTA', $lang['id_lang']));
            $fields['GRADIADSENSE_AD_IMG'][$lang['id_lang']] = Tools::getValue('GRADIADSENSE_AD_IMG_'.$lang['id_lang'], Configuration::get('GRADIADSENSE_AD_IMG', $lang['id_lang']));

           
            
        }
        $fields['GRADIADSENSE_ENABLE'][$lang['id_lang']] = Tools::getValue('GRADIADSENSE_ENABLE', Configuration::get('GRADIADSENSE_ENABLE'));
        $fields['GRADIADSENSE_OVERLAY'] = Tools::getValue('GRADIADSENSE_OVERLAY', Configuration::get('GRADIADSENSE_OVERLAY'));
        return $fields;

    }
    protected function _postValidation() {
       
        $form_values = $this->getConfigFormValues();
        $errors = array();
        $allowedImgExt = array('image/webp', 'image/jpeg', 'image/jpg', 'image/png');
        $gradiadsense_image =  $_FILES['GRADIADSENSE_AD_IMG'];

        $languages = Language::getLanguages(false);
        
            foreach ($languages as $language) {
                if(empty(Tools::getValue('GRADIADSENSE_AD_TITLE_' . $language['id_lang']))) {
                    $errors[] = $this->l('GRADIADSENSE_AD_TITLE') . $this->l(' is not set in language ') .$language['iso_code'];
                }
                if(empty(Tools::getValue('GRADIADSENSE_AD_DESCRIPTION_' . $language['id_lang']))) {
                    $errors[] = $this->l('GRADIADSENSE_AD_DESCRIPTION'). $this->l(' is not set in language ') .$language['iso_code'];
                }
                if(empty(Tools::getValue('GRADIADSENSE_AD_TEXT_CTA_' . $language['id_lang']))) {
                    $errors[] = $this->l('GRADIADSENSE_AD_TEXT_CTA'). $this->l(' is not set in language ') .$language['iso_code'];
                }
                if(empty(Tools::getValue('GRADIADSENSE_AD_URL_CTA_' . $language['id_lang']))) {
                    $errors[] = $this->l('GRADIADSENSE_AD_URL_CTA'). $this->l(' is not set in language ') .$language['iso_code'];
                }
            }


        if(file_exists($gradiadsense_image['tmp_name'])){
            if(!in_array($gradiadsense_image['type'], $allowedImgExt)) {
                $errors[] = $this->l('GRADIADSENSE_AD_IMG') . $this->l(' must be in jpg, jpeg, png or webp format');
            }
        }


        if (count($errors)) {
            $this->_html .= $this->displayError(implode('<br />', $errors));
            return false;
        }
        return true;
    }
    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();
        $values = array();
        $gradiadsense_image =  $_FILES['GRADIADSENSE_AD_IMG'];
        $path = _PS_MODULE_DIR_ . 'gradiadsense/views/img/';
        $image_ext = pathinfo($gradiadsense_image['name'], PATHINFO_EXTENSION);
        $languages = Language::getLanguages(false);

        if(file_exists($gradiadsense_image['tmp_name'])){
            $uploaded = move_uploaded_file($gradiadsense_image['tmp_name'], $path.$gradiadsense_image['name'] = 'gradiadsense'.'.'.$image_ext);
        
            if(!$uploaded){
                $this->_html .= $this->displayError($this->l('Error uploading the image. Please try again.'));
                return false;
            }
            Configuration::updateValue('GRADIADSENSE_AD_IMG', 'gradiadsense'.'.'.$image_ext);

        }
        foreach ($languages as $lang) {
            $values['GRADIADSENSE_AD_TITLE'][$lang['id_lang']] = Tools::getValue('GRADIADSENSE_AD_TITLE_'.$lang['id_lang']);
            $values['GRADIADSENSE_AD_DESCRIPTION'][$lang['id_lang']] = Tools::getValue('GRADIADSENSE_AD_DESCRIPTION_'.$lang['id_lang']);
            $values['GRADIADSENSE_AD_TEXT_CTA'][$lang['id_lang']] = Tools::getValue('GRADIADSENSE_AD_TEXT_CTA_'.$lang['id_lang']);
            $values['GRADIADSENSE_AD_URL_CTA'][$lang['id_lang']] = Tools::getValue('GRADIADSENSE_AD_URL_CTA_'.$lang['id_lang']);
        }
        Configuration::updateValue('GRADIADSENSE_AD_TITLE', $values['GRADIADSENSE_AD_TITLE']);
        Configuration::updateValue('GRADIADSENSE_AD_DESCRIPTION', $values['GRADIADSENSE_AD_DESCRIPTION']);
        Configuration::updateValue('GRADIADSENSE_AD_TEXT_CTA', $values['GRADIADSENSE_AD_TEXT_CTA']);
        Configuration::updateValue('GRADIADSENSE_AD_URL_CTA', $values['GRADIADSENSE_AD_URL_CTA']);


        Configuration::updateValue('GRADIADSENSE_OVERLAY', Tools::getValue('GRADIADSENSE_OVERLAY'));
        Configuration::updateValue('GRADIADSENSE_ENABLE', Tools::getValue('GRADIADSENSE_ENABLE'));
        $this->_clearCache($this->templateFile);
        return true;
        
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/gradiadsense.js');
        $this->context->controller->addCSS($this->_path.'/views/css/gradiadsense.css');
    }

    public function hookDisplayHome()
    {
        if(Configuration::get('GRADIADSENSE_ENABLE', $this->context->language->id)) {
            $overlay = Configuration::get('GRADIADSENSE_OVERLAY') ? Configuration::get('GRADIADSENSE_OVERLAY') : null;
            $image = Configuration::get('GRADIADSENSE_AD_IMG') ? $this->_path.'views/img/'.Configuration::get('GRADIADSENSE_AD_IMG') : $this->_path.'views/img/ad_default.jpg';
            $this->context->smarty->assign([
                'GRADIADSENSE_AD_TITLE' => Configuration::get('GRADIADSENSE_AD_TITLE', $this->context->language->id),
                'GRADIADSENSE_AD_DESCRIPTION' => Configuration::get('GRADIADSENSE_AD_DESCRIPTION',$this->context->language->id),
                'GRADIADSENSE_AD_TEXT_CTA' => Configuration::get('GRADIADSENSE_AD_TEXT_CTA', $this->context->language->id),
                'GRADIADSENSE_AD_URL_CTA' => Configuration::get('GRADIADSENSE_AD_URL_CTA', $this->context->language->id),
                'GRADIADSENSE_OVERLAY' => $overlay,
                'GRADIADSENSE_AD_IMG' => $image,
            ]);

            return $this->display(__FILE__, 'gradiadsense.tpl');
        }

    }
}
