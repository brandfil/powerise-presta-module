<?php

class PoweriseGatewayModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        parent::__construct();
        $this->template = 'module:powerise/views/templates/front/gateway.tpl';
    }

    public function postProcess()
    {
        $this->context->smarty->assign([
            'data' => $_POST ?: [],
            'action' => Tools::getValue('q'),
        ]);
        $this->setTemplate($this->template);
    }
}
