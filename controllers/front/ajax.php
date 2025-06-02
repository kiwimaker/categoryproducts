<?php
class CategoryProductsAjaxModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        
        $response = ['success' => false];
        $context = Context::getContext();
        
        try {
            $action = Tools::getValue('action');
            
            switch ($action) {
                case 'get_categories':
                    $response = $this->getCategories($context);
                    break;
                case 'search_description':
                    $response = $this->searchInDescription($context);
                    break;
                default:
                    $response = $this->getProducts($context);
            }
        } catch (Exception $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }
        
        header('Content-Type: application/json');
        die(json_encode($response));
    }
    
    protected function getCategories($context)
    {
        $id_category = (int)Tools::getValue('id_category');
        if ($id_category <= 0) {
            throw new Exception('Invalid category ID');
        }
        
        $category = new Category($id_category);
        if (!Validate::isLoadedObject($category)) {
            throw new Exception('Category not found');
        }
        
        $subcategories = $category->getSubCategories($context->language->id, false);
        $formattedSubcategories = array_map(function($subcat) use ($context) {
            return [
                'id_category' => $subcat['id_category'],
                'name' => $subcat['name'],
                'active' => (bool)$subcat['active'],
                'url' => $context->link->getCategoryLink(
                    $subcat['id_category'],
                    $subcat['link_rewrite']
                )
            ];
        }, $subcategories);
        
        return [
            'success' => true,
            'subcategories' => $formattedSubcategories
        ];
    }
    
    protected function searchInDescription($context)
    {
        $searchTerm = Tools::getValue('search_term');
        if (empty($searchTerm)) {
            throw new Exception('Search term is required');
        }
        
        $categories = Category::getCategories($context->language->id, false, false, '', 'position');
        $matches = [];
        
        foreach ($categories as $category) {
            $cat = new Category($category['id_category']);
            $description = isset($cat->description[$context->language->id]) ? $cat->description[$context->language->id] : '';
            
            if (stripos($description, $searchTerm) !== false) {
                $matches[] = [
                    'id' => $category['id_category'],
                    'name' => $category['name'],
                    'active' => (bool)$category['active'],
                    'url' => $context->link->getCategoryLink(
                        $category['id_category'],
                        $category['link_rewrite']
                    )
                ];
            }
        }
        
        return [
            'success' => true,
            'categories' => $matches,
            'total_found' => count($matches)
        ];
    }
    
    protected function getProducts($context)
    {
        $id_category = (int)Tools::getValue('id_category');
        if ($id_category <= 0) {
            throw new Exception('Invalid category ID');
        }
        
        $category = new Category($id_category);
        if (!Validate::isLoadedObject($category)) {
            throw new Exception('Category not found');
        }
        
        $products = $category->getProducts(
            $context->language->id,
            1,
            100,
            'position',
            'ASC',
            false,
            true,
            false,
            1,
            $context
        );
        
        $formattedProducts = array_map(function($product) use ($context) {
            return [
                'id' => $product['id_product'],
                'name' => $product['name'],
                'active' => isset($product['active']) ? (bool)$product['active'] : true,
                'url' => $context->link->getProductLink(
                    $product['id_product'],
                    $product['link_rewrite'],
                    $product['category'],
                    $product['ean13']
                )
            ];
        }, $products);
        
        return [
            'success' => true,
            'products' => $formattedProducts,
            'category_active' => (bool)$category->active
        ];
    }
} 