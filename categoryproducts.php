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
        $this->version = '1.3.0';
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

    private function checkRobotsTxt()
    {
        $robots_path = _PS_ROOT_DIR_ . '/robots.txt';
        $disallow_line = 'Disallow: /module/categoryproducts/';
        
        if (!file_exists($robots_path)) {
            return [
                'exists' => false,
                'configured' => false,
                'message' => $this->l('El archivo robots.txt no existe en la raíz del sitio web.')
            ];
        }
        
        $robots_content = file_get_contents($robots_path);
        $has_disallow = strpos($robots_content, $disallow_line) !== false;
        
        return [
            'exists' => true,
            'configured' => $has_disallow,
            'message' => $has_disallow 
                ? $this->l('La configuración de robots.txt está correctamente implementada.')
                : $this->l('La línea de Disallow no está presente en robots.txt.')
        ];
    }

    public function getContent()
    {
        $output = '';
        
        // Verificar estado de robots.txt
        $robots_status = $this->checkRobotsTxt();
        
        // Generar las URLs de los archivos de documentación
        $module_path = $this->getPathUri();
        $readme_url = Tools::getShopDomainSsl(true) . $module_path . 'README.md';
        $llm_url = Tools::getShopDomainSsl(true) . $module_path . 'llm.txt';
        
        $output .= '
        <div class="panel">
            <div class="panel-heading">
                <i class="icon-cogs"></i> ' . $this->l('Configuración - Category Products AJAX') . '
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="alert alert-info">
                            <h4><i class="icon-info-circle"></i> ' . $this->l('Información del Módulo') . '</h4>
                            <p>' . $this->l('Este módulo proporciona una API AJAX para obtener dinámicamente información de categorías y productos sin recargar la página.') . '</p>
                            <p><strong>' . $this->l('Versión:') . '</strong> ' . $this->version . '</p>
                            <p><strong>' . $this->l('Variable JavaScript:') . '</strong> <code>categoryproducts_ajax_url</code></p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-lg-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title"><i class="icon-book"></i> ' . $this->l('Documentación') . '</h3>
                            </div>
                            <div class="panel-body">
                                <p>' . $this->l('Consulta la documentación completa para implementar el módulo:') . '</p>
                                <div class="form-group">
                                    <a href="' . $readme_url . '" target="_blank" class="btn btn-default">
                                        <i class="icon-external-link"></i> ' . $this->l('Ver README.md') . '
                                    </a>
                                </div>
                                <div class="form-group">
                                    <a href="' . $llm_url . '" target="_blank" class="btn btn-info">
                                        <i class="icon-file-text"></i> ' . $this->l('Ver llm.txt (Documentación LLM)') . '
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title"><i class="icon-search"></i> ' . $this->l('Configuración SEO') . '</h3>
                            </div>
                            <div class="panel-body">';
        
        // Mostrar estado de robots.txt
        if (!$robots_status['exists']) {
            $output .= '
                                <div class="alert alert-danger">
                                    <h4><i class="icon-exclamation-triangle"></i> ' . $this->l('Archivo robots.txt no encontrado') . '</h4>
                                    <p>' . $robots_status['message'] . '</p>
                                </div>';
        } elseif ($robots_status['configured']) {
            $output .= '
                                <div class="alert alert-success">
                                    <h4><i class="icon-check"></i> ' . $this->l('robots.txt configurado correctamente') . '</h4>
                                    <p>' . $robots_status['message'] . '</p>
                                </div>';
        } else {
            $output .= '
                                <div class="alert alert-warning">
                                    <h4><i class="icon-exclamation-triangle"></i> ' . $this->l('Configuración de robots.txt pendiente') . '</h4>
                                    <p>' . $robots_status['message'] . '</p>
                                </div>';
        }
        
        $output .= '
                                <div class="alert alert-info">
                                    <h4><i class="icon-info"></i> ' . $this->l('Instrucciones') . '</h4>
                                    <p>' . $this->l('Para evitar que los motores de búsqueda indexen las URLs del módulo, añade esta línea a tu archivo robots.txt:') . '</p>
                                    <pre style="background: #f5f5f5; padding: 10px; border-radius: 4px; margin: 10px 0;">Disallow: /module/categoryproducts/</pre>
                                    <p><small>' . $this->l('Esta configuración evitará que Google y otros buscadores indexen las respuestas AJAX del módulo.') . '</small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title"><i class="icon-code"></i> ' . $this->l('Ejemplo de Uso') . '</h3>
                            </div>
                            <div class="panel-body">
                                <p>' . $this->l('Ejemplo básico de JavaScript para usar el módulo:') . '</p>
                                <pre style="background: #f8f8f8; padding: 15px; border-radius: 4px; border-left: 4px solid #007cba; overflow-x: auto;">
// Obtener productos de una categoría
fetch(categoryproducts_ajax_url, {
    method: \'POST\',
    body: new URLSearchParams({
        id_category: 123
    })
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        console.log(data.products);
    }
});

// Obtener subcategorías
fetch(categoryproducts_ajax_url, {
    method: \'POST\',
    body: new URLSearchParams({
        action: \'get_categories\',
        id_category: 123
    })
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        console.log(data.subcategories);
    }
});
                                </pre>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-lg-12">
                        <div class="alert alert-success">
                            <h4><i class="icon-check"></i> ' . $this->l('Módulo Configurado Correctamente') . '</h4>
                            <p>' . $this->l('El módulo está instalado y configurado. La variable JavaScript <code>categoryproducts_ajax_url</code> está disponible en el frontend.') . '</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
        
        return $output;
    }
} 