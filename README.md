# Category Products Module

## Descripción
Módulo de PrestaShop para obtener productos y categorías mediante AJAX. Diseñado para facilitar la navegación dinámica y la carga de contenido sin recargar la página.

## Características Principales
- Obtención de subcategorías
- Listado de productos por categoría
- Búsqueda en descripciones de categorías
- URLs amigables para SEO
- Compatibilidad con PrestaShop 1.7+

## Requisitos
- PrestaShop 1.7 o superior
- PHP 7.1 o superior
- Módulo instalado y activado
- JavaScript habilitado en el navegador

## Instalación
1. Copiar la carpeta `categoryproducts` al directorio `modules/` de PrestaShop
2. Acceder al panel de administración > Módulos
3. Buscar "Category Products"
4. Hacer clic en "Instalar"

## Uso

### 1. Obtener Subcategorías
```javascript
// La URL está disponible globalmente como 'categoryproducts_ajax_url'
fetch(categoryproducts_ajax_url, {
    method: 'POST',
    body: new URLSearchParams({
        action: 'get_categories',
        id_category: 123 // ID de la categoría padre
    })
});

// Respuesta:
{
    "success": true,
    "subcategories": [
        {
            "id_category": "456",
            "name": "Nombre de Subcategoría",
            "url": "url-amigable-subcategoria"
        }
    ]
}
```

### 2. Obtener Productos de una Categoría
```javascript
fetch(categoryproducts_ajax_url, {
    method: 'POST',
    body: new URLSearchParams({
        id_category: 123 // ID de la categoría
    })
});

// Respuesta:
{
    "success": true,
    "products": [
        {
            "id": "789",
            "name": "Nombre del Producto",
            "url": "url-amigable-producto"
        }
    ]
}
```

### 3. Búsqueda en Descripciones
```javascript
fetch(categoryproducts_ajax_url, {
    method: 'POST',
    body: new URLSearchParams({
        action: 'search_description',
        search_term: "término a buscar" // Término a buscar en las descripciones
    })
});

// Respuesta:
{
    "success": true,
    "categories": [
        {
            "id": "456",
            "name": "Nombre de Categoría",
            "url": "url-amigable-categoria"
        }
    ],
    "total_found": 1
}
```

## Parámetros Aceptados

| Parámetro | Tipo | Descripción | Requerido |
|-----------|------|-------------|-----------|
| action | string | Tipo de acción ('get_categories', 'search_description') | No |
| id_category | int | ID de la categoría a consultar | Sí* |
| search_term | string | Término a buscar en las descripciones de categorías | No** |

*Requerido para obtener subcategorías o productos
**Requerido cuando action es 'search_description'

## Estructura del Módulo
```
categoryproducts/
├── README.md
├── categoryproducts.php       # Archivo principal del módulo
├── controllers/
│   └── front/
│       └── ajax.php          # Controlador AJAX
└── views/                    # Vistas y assets (si se necesitan)
```

## Hooks Utilizados
- `actionFrontControllerSetMedia`: Para registrar la URL del controlador AJAX

## Consideraciones Técnicas
- Las respuestas siempre incluyen un campo `success` (boolean)
- Los errores incluyen un campo `message` con la descripción
- Las URLs generadas son SEO-friendly
- Límite de 100 productos por consulta
- Los productos se ordenan por posición (ASC)
- Búsqueda en descripciones de categorías con conteo de resultados
- Manejo de errores mejorado con mensajes descriptivos

## Mantenimiento
Para modificar el módulo, considerar:
1. Mantener la estructura de respuesta consistente
2. Documentar nuevos parámetros o funcionalidades
3. Actualizar las versiones compatibles en `ps_versions_compliancy`
4. Probar las modificaciones en un entorno de desarrollo

## Soporte
Para reportar problemas o sugerir mejoras:
1. Verificar la configuración del módulo
2. Comprobar los logs de PrestaShop
3. Contactar con el equipo de desarrollo

## Changelog
### v1.2.0
- Eliminada la limpieza específica de nombres de productos
- Renombrada la funcionalidad de búsqueda a 'search_description'
- Mejorada la generalización del módulo para cualquier tipo de tienda

### v1.1.0
- Mejorada la estructura del código y manejo de errores
- Añadido contador de resultados en búsquedas
- Optimizado el rendimiento de las búsquedas

### v1.0.0
- Implementación inicial
- Soporte para obtención de categorías
- Soporte para obtención de productos 