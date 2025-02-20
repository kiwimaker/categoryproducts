<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class CategoryProducts extends Module
{
    public function __construct()
    {
        $this->name = 'categoryproducts';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Kiwimaker';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Category Products AJAX');
        $this->description = $this->l('Módulo para obtener productos y categorías vía AJAX');
    }

    public function install()
    {
        return parent::install() &&
            $this->registerHook('actionFrontControllerSetMedia');
    }

    public function uninstall()
    {
        return parent::uninstall() &&
            $this->unregisterHook('actionFrontControllerSetMedia');
    }

    public function hookActionFrontControllerSetMedia()
    {
        // Registrar la URL del controlador AJAX para uso en JavaScript
        Media::addJsDef([
            'categoryproducts_ajax_url' => $this->context->link->getModuleLink('categoryproducts', 'ajax')
        ]);
    }
} 