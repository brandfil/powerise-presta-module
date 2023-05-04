<?php
/**
* 2007-2023 PrestaShop
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
*  @copyright 2007-2023 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
if (!defined('_PS_VERSION_')) {
    exit;
}

class Powerise extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'powerise';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Powerise';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Powerise');
        $this->description = $this->l('Powerise integration module. Get the power of AI!');

        $this->ps_versions_compliancy = ['min' => '1.6', 'max' => _PS_VERSION_];
        $this->module_key = '3eb2d2d5ae451228223dbabe64825971';
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        include __DIR__ . '/sql/install.php';

        return parent::install() && $this->registerHook('displayBackOfficeHeader');
    }

    public function uninstall()
    {
        include __DIR__ . '/sql/uninstall.php';

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        if (((bool) Tools::isSubmit('submitPoweriseModule')) == true) {
            $this->postProcess();
        }

        $redirectUrl = ($this->isSecure() ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $baseUrl = ($this->isSecure() ? 'https://' : 'http://') . \Tools::getShopDomain(false);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!empty(\Tools::getValue('userId')) && !empty(\Tools::getValue('apiKey'))) {
                \Configuration::updateValue('POWERISE_USER_ID', \Tools::getValue('userId'));
                \Configuration::updateValue('POWERISE_API_KEY', \Tools::getValue('apiKey'));
                if (!empty(\Tools::getValue('firstName'))) {
                    \Configuration::updateValue('POWERISE_AUTH_FIRSTNAME', \Tools::getValue('firstName'));
                }
                if (!empty(\Tools::getValue('lastName'))) {
                    \Configuration::updateValue('POWERISE_AUTH_LASTNAME', \Tools::getValue('lastName'));
                }
                \Configuration::updateValue('POWERISE_AUTH_EMAIL', \Tools::getValue('email'));
            }
        }

        $this->context->smarty->assign('auth_user_email', \Configuration::get('POWERISE_AUTH_EMAIL'));
        $this->context->smarty->assign('auth_user_firstname', \Configuration::get('POWERISE_AUTH_FIRSTNAME'));
        $this->context->smarty->assign('auth_user_lastname', \Configuration::get('POWERISE_AUTH_LASTNAME'));
        $this->context->smarty->assign('module_dir', $this->_path);
        $this->context->smarty->assign('redirect_url', $redirectUrl);
        $this->context->smarty->assign('shop_url', $baseUrl);

        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');

        if ($disabled = empty(\Configuration::get('POWERISE_API_KEY'))) {
            return $output . '<div class="pw-section pw-section--disabled">' . $this->renderForm() . '<div class="pw-section__overlay">Connect your account first</div></div>';
        }

        return $output . $this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitPoweriseModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$this->getConfigForm()]);
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return [
            'form' => [
                'legend' => [
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'col' => 3,
                        'prefix' => '<i class="icon-key"></i>',
                        'type' => 'text',
                        'name' => 'POWERISE_API_KEY',
                        'label' => $this->l('API Key'),
                        'desc' => $this->l('API Key will be fetched automatically when you will connect your shop with Powerise.'),
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return [
            'POWERISE_API_KEY' => Configuration::get('POWERISE_API_KEY'),
        ];
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
        }
    }

    private function isSecure()
    {
        if (isset($_SERVER['HTTP_CF_VISITOR'])) {
            $cfVisitor = json_decode($_SERVER['HTTP_CF_VISITOR']);
            if ($cfVisitor->scheme === 'https') {
                return true;
            } else {
                return false;
            }
        }

        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
    }
}
