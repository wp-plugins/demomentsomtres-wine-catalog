<?php
    /**
     Plugin Name: DeMomentSomTres Wine and Cheese Catalog
     Plugin URI: http://demomentsomtres.com/english/wordpress-plugins/demomentsomtres-wine-catalog/
     Description: Shows your products in the web based on marks and product types. The shortcode [demomentsomtres-product-mark cols=n] shows all the products in the same mark than the current product. You also can include "echo dmst_catalog_shortcode($attr);" in your template to show the same contents.
     Version: 2.0
     Author: Marc Queralt
     Author URI: http://demomentsomtres.com/
     License: GPLv2
     */

    require_once (dirname(__FILE__) . '/lib/class-tgm-plugin-activation.php');
    require_once (dirname(__FILE__) . '/demomentsomtres-catalog-widgets.php');

    define('DMST_CATALOG_DOMAIN', 'dmst-catalog');

    $dms3_cheeseAndWine = new DeMomentSomTresWineAndCheese();

    class DeMomentSomTresWineAndCheese {
        const TEXT_DOMAIN = DMST_CATALOG_DOMAIN;
        const POSTTYPE_PRODUCT = 'product';
        const TAX_PRODUCTTYPE = 'product-type';
        const TAX_PRODUCTREGION = 'product-region';
        const TAX_PRODUCER = 'product-mark';
        const TAX_CHEESE_MILKANIMAL = 'product-cheese-milk-animal';
        const TAX_CHEESE_MILKTYPE = 'product-cheese-milk-type';
        const TAX_CHEESE_TEXTURE = 'product-cheese-texture';
        const TAX_ALLERGENS_INFO = 'product-allergens-info';
        const TAX_WINE_TYPE = 'product-wine-type';
        const OPTION_THUMBNAILID = '_thumbnail_id';
        const OPTION_SALES_URL = 'dms3_catalog_sales_url';
        const OPTION_RELATEDPRODUCTS = "dms3_catalog_relatedProducts";
        const IMAGESIZENAME = "dms3_catalog";
        const PARAMETER_IMAGEWIDTH = 200;
        const PARAMETER_IMAGEHEIGHT = 200;

        private $pluginURL;
        private $pluginPath;
        private $langDir;

        /**
         * @since 2.0
         */
        function __construct() {
            $this -> pluginURL = plugin_dir_url(__FILE__);
            $this -> pluginPath = plugin_dir_path(__FILE__);
            $this -> langDir = dirname(plugin_basename(__FILE__)) . '/languages';

            add_action('plugins_loaded', array(
                $this,
                'plugin_init'
            ));
            add_action('tgmpa_register', array(
                $this,
                'required_plugins'
            ));
            add_action('init', array(
                $this,
                'posttypes'
            ), 0);
            add_action('widgets_init', create_function('', 'return register_widget("ProductNameWidget");'));
            add_action('widgets_init', create_function('', 'return register_widget("MarkDescriptionWidget");'));
            add_action('widgets_init', create_function('', 'return register_widget("ProductNameWidget");'));
            add_action('widgets_init', create_function('', 'return register_widget("ProductsInCategoryWidget");'));
            add_action('widgets_init', create_function('', 'return register_widget("ProductSalesURLWidget");'));
            add_action('widgets_init', create_function('', 'return register_widget("MarkLogoWidget");'));
            add_filter('rwmb_meta_boxes', array(
                $this,
                'metaboxes'
            ));
            add_action('add_meta_boxes', array(
                $this,
                "add_metaboxes"
            ));
            add_action('do_meta_boxes', array(
                $this,
                'do_meta_boxes'
            ));
            add_action('edit_form_after_title', array(
                $this,
                'excerpt_metabox'
            ));

            add_shortcode('dms3-catalog-relatedProducts', array(
                $this,
                'shortcode_related_products'
            ));
            // Kept for compatibility
            add_shortcode('demomentsomtres-marks', array(
                $this,
                'shortcode_marks'
            ));
            add_shortcode('dms3-catalog-producers', array(
                $this,
                'shortcode_marks'
            ));
            add_shortcode('dms3-catalog-wine-type', array(
                $this,
                'shortcode_wine_type'
            ));
            add_shortcode('dms3-catalog-cheese-milk-origin', array(
                $this,
                'shortcode_cheese_milk_origin'
            ));
            add_shortcode('dms3-catalog-cheese-milk-type', array(
                $this,
                'shortcode_cheese_milk_type'
            ));
            add_shortcode('dms3-catalog-cheese-texture', array(
                $this,
                'shortcode_cheese_texture'
            ));
            add_shortcode('dms3-catalog-product-region', array(
                $this,
                'shortcode_product_region'
            ));
            add_shortcode('dms3-catalog-product-producer', array(
                $this,
                'shortcode_product_producer'
            ));
            add_shortcode('dms3-catalog-allergens', array(
                $this,
                'shortcode_product_allergens'
            ));
            add_shortcode('dms3-catalog-product-slogan', array(
                $this,
                'shortcode_product_slogan'
            ));
            add_shortcode('dms3-catalog-products-in-mark', array(
                $this,
                'shortcode_products_in_mark'
            ));
            add_shortcode('dms3-catalog-product-name', array(
                $this,
                'shortcode_product_name'
            ));

            add_image_size(self::IMAGESIZENAME, self::PARAMETER_IMAGEWIDTH, self::PARAMETER_IMAGEHEIGHT, false);
        }

        /**
         * @since 2.0
         */
        function plugin_init() {
            load_plugin_textdomain(DMST_CATALOG_DOMAIN, false, $this -> langDir);
        }

        /**
         * @since 2.0
         */
        function required_plugins() {
            $plugins = array(
                array(
                    'name' => 'Meta Box',
                    'slug' => 'meta-box',
                    'required' => true
                ),
                array(
                    'name' => 'Events Manager',
                    'slug' => 'events-manager',
                    'required' => false
                ),
                array(
                    'name' => 'Categories Images',
                    'slug' => 'categories-images',
                    'required' => false
                ),
            );
            $config = array(
                'default_path' => '', // Default absolute path to pre-packaged plugins.
                'menu' => 'tgmpa-install-plugins', // Menu slug.
                'has_notices' => true, // Show admin notices or not.
                'dismissable' => false, // If false, a user cannot dismiss the nag message.
                'dismiss_msg' => __('Some plugins are missing!', self::TEXT_DOMAIN), // If 'dismissable' is false, this message will be output at top of nag.
                'is_automatic' => false, // Automatically activate plugins after installation or not.
                'message' => __('This are the required plugins', self::TEXT_DOMAIN), // Message to output right before the plugins table.
                'strings' => array(
                    'page_title' => __('Install Required Plugins', self::TEXT_DOMAIN),
                    'menu_title' => __('Install Plugins', self::TEXT_DOMAIN),
                    'installing' => __('Installing Plugin: %s', self::TEXT_DOMAIN), // %s = plugin name.
                    'oops' => __('Something went wrong with the plugin API.', self::TEXT_DOMAIN),
                    'notice_can_install_required' => _n_noop('This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', self::TEXT_DOMAIN), // %1$s = plugin name(s).
                    'notice_can_install_recommended' => _n_noop('This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', self::TEXT_DOMAIN), // %1$s = plugin name(s).
                    'notice_cannot_install' => _n_noop('Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', self::TEXT_DOMAIN), // %1$s = plugin name(s).
                    'notice_can_activate_required' => _n_noop('The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', self::TEXT_DOMAIN), // %1$s = plugin name(s).
                    'notice_can_activate_recommended' => _n_noop('The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', self::TEXT_DOMAIN), // %1$s = plugin name(s).
                    'notice_cannot_activate' => _n_noop('Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', self::TEXT_DOMAIN), // %1$s = plugin name(s).
                    'notice_ask_to_update' => _n_noop('The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', self::TEXT_DOMAIN), // %1$s = plugin name(s).
                    'notice_cannot_update' => _n_noop('Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', self::TEXT_DOMAIN), // %1$s = plugin name(s).
                    'install_link' => _n_noop('Begin installing plugin', 'Begin installing plugins', self::TEXT_DOMAIN),
                    'activate_link' => _n_noop('Begin activating plugin', 'Begin activating plugins', self::TEXT_DOMAIN),
                    'return' => __('Return to Required Plugins Installer', self::TEXT_DOMAIN),
                    'plugin_activated' => __('Plugin activated successfully.', self::TEXT_DOMAIN),
                    'complete' => __('All plugins installed and activated successfully. %s', self::TEXT_DOMAIN), // %s = dashboard link.
                    'nag_type' => 'error' // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
                )
            );
            tgmpa($plugins, $config);
        }

        function posttypes() {
            $labels = array(
                'name' => _x('Product Types', 'taxonomy general name', self::TEXT_DOMAIN),
                'singular_name' => _x('Product Type', 'taxonomy singular name', self::TEXT_DOMAIN),
                'search_items' => __('Search Type', self::TEXT_DOMAIN),
                'all_items' => __('All Product Types', self::TEXT_DOMAIN),
                'parent_item' => __('Parent Product Type', self::TEXT_DOMAIN),
                'parent_item_colon' => __('Parent Product Type:', self::TEXT_DOMAIN),
                'edit_item' => __('Edit Product Type', self::TEXT_DOMAIN),
                'update_item' => __('Update Product Type', self::TEXT_DOMAIN),
                'add_new_item' => __('Add New Product Type', self::TEXT_DOMAIN),
                'new_item_name' => __('New Product Type Name', self::TEXT_DOMAIN),
            );
            register_taxonomy(self::TAX_PRODUCTTYPE, '', array(
                'hierarchical' => true,
                'labels' => $labels
            ));

            $labels = array(
                'name' => _x('Regions', 'taxonomy general name', self::TEXT_DOMAIN),
                'singular_name' => _x('Region', 'taxonomy singular name', self::TEXT_DOMAIN),
                'search_items' => __('Search Region', self::TEXT_DOMAIN),
                'all_items' => __('All Regions', self::TEXT_DOMAIN),
                'parent_item' => __('Parent Region', self::TEXT_DOMAIN),
                'parent_item_colon' => __('Parent Region:', self::TEXT_DOMAIN),
                'edit_item' => __('Edit Region', self::TEXT_DOMAIN),
                'update_item' => __('Update Region', self::TEXT_DOMAIN),
                'add_new_item' => __('Add New Region', self::TEXT_DOMAIN),
                'new_item_name' => __('New Region Name', self::TEXT_DOMAIN),
            );
            register_taxonomy(self::TAX_PRODUCTREGION, '', array(
                'hierarchical' => true,
                'labels' => $labels
            ));

            $labels = array(
                'name' => _x('Producers', 'taxonomy general name', self::TEXT_DOMAIN),
                'singular_name' => _x('Producer', 'taxonomy singular name', self::TEXT_DOMAIN),
                'search_items' => __('Search Producer', self::TEXT_DOMAIN),
                'all_items' => __('All Producers', self::TEXT_DOMAIN),
                'parent_item' => __('Parent Producer', self::TEXT_DOMAIN),
                'parent_item_colon' => __('Parent Producer:', self::TEXT_DOMAIN),
                'edit_item' => __('Edit Producer', self::TEXT_DOMAIN),
                'update_item' => __('Update Producer', self::TEXT_DOMAIN),
                'add_new_item' => __('Add New Producer', self::TEXT_DOMAIN),
                'new_item_name' => __('New Producer Name', self::TEXT_DOMAIN),
            );
            register_taxonomy(self::TAX_PRODUCER, '', array(
                'hierarchical' => true,
                'labels' => $labels
            ));

            $labels = array(
                'name' => _x('Milk origins', 'Taxonomy General Name', self::TEXT_DOMAIN),
                'singular_name' => _x('Milk origin', 'Taxonomy Singular Name', self::TEXT_DOMAIN),
                'menu_name' => __('Milk origins', self::TEXT_DOMAIN),
                'all_items' => __('All Milk Origins', self::TEXT_DOMAIN),
                'parent_item' => __('Parent Origin', self::TEXT_DOMAIN),
                'parent_item_colon' => __('Parent Origin:', self::TEXT_DOMAIN),
                'new_item_name' => __('New Milk Origin', self::TEXT_DOMAIN),
                'add_new_item' => __('Add New Milk Origin', self::TEXT_DOMAIN),
                'edit_item' => __('Edit Milk Origin', self::TEXT_DOMAIN),
                'update_item' => __('Update Milk Origin', self::TEXT_DOMAIN),
                'view_item' => __('View Milk Origin', self::TEXT_DOMAIN),
                'separate_items_with_commas' => __('Separate items with commas', self::TEXT_DOMAIN),
                'add_or_remove_items' => __('Add or remove items', self::TEXT_DOMAIN),
                'choose_from_most_used' => __('Choose from the most used', self::TEXT_DOMAIN),
                'popular_items' => __('Popular Milk Origins', self::TEXT_DOMAIN),
                'search_items' => __('Search Milk Origins', self::TEXT_DOMAIN),
                'not_found' => __('Not Found', self::TEXT_DOMAIN),
            );
            $args = array(
                'labels' => $labels,
                'hierarchical' => true,
                'public' => false,
                'show_ui' => true,
                'show_admin_column' => false,
                'show_in_nav_menus' => true,
                'show_tagcloud' => true,
            );
            register_taxonomy(self::TAX_CHEESE_MILKANIMAL, '', $args);

            $labels = array(
                'name' => _x('Milk Types', 'Taxonomy General Name', self::TEXT_DOMAIN),
                'singular_name' => _x('Milk Yype', 'Taxonomy Singular Name', self::TEXT_DOMAIN),
                'menu_name' => __('Milk Types', self::TEXT_DOMAIN),
                'all_items' => __('All Milk Types', self::TEXT_DOMAIN),
                'parent_item' => __('Parent Type', self::TEXT_DOMAIN),
                'parent_item_colon' => __('Parent Type:', self::TEXT_DOMAIN),
                'new_item_name' => __('New Milk Type', self::TEXT_DOMAIN),
                'add_new_item' => __('Add New Milk Type', self::TEXT_DOMAIN),
                'edit_item' => __('Edit Milk Type', self::TEXT_DOMAIN),
                'update_item' => __('Update Milk Type', self::TEXT_DOMAIN),
                'view_item' => __('View Milk Type', self::TEXT_DOMAIN),
                'separate_items_with_commas' => __('Separate items with commas', self::TEXT_DOMAIN),
                'add_or_remove_items' => __('Add or remove items', self::TEXT_DOMAIN),
                'choose_from_most_used' => __('Choose from the most used', self::TEXT_DOMAIN),
                'popular_items' => __('Popular Milk Types', self::TEXT_DOMAIN),
                'search_items' => __('Search Milk Types', self::TEXT_DOMAIN),
                'not_found' => __('Not Found', self::TEXT_DOMAIN),
            );
            $args = array(
                'labels' => $labels,
                'hierarchical' => true,
                'public' => false,
                'show_ui' => true,
                'show_admin_column' => false,
                'show_in_nav_menus' => true,
                'show_tagcloud' => true,
            );
            register_taxonomy(self::TAX_CHEESE_MILKTYPE, '', $args);

            $labels = array(
                'name' => _x('Cheese Textures', 'Taxonomy General Name', self::TEXT_DOMAIN),
                'singular_name' => _x('Cheese Texture', 'Taxonomy Singular Name', self::TEXT_DOMAIN),
                'menu_name' => __('Cheese Textures', self::TEXT_DOMAIN),
                'all_items' => __('All Cheese Textures', self::TEXT_DOMAIN),
                'parent_item' => __('Parent Cheese Texture', self::TEXT_DOMAIN),
                'parent_item_colon' => __('Parent Texture:', self::TEXT_DOMAIN),
                'new_item_name' => __('New Cheese Texture', self::TEXT_DOMAIN),
                'add_new_item' => __('Add New Cheese Texture', self::TEXT_DOMAIN),
                'edit_item' => __('Edit Cheese Texture', self::TEXT_DOMAIN),
                'update_item' => __('Update Cheese Texture', self::TEXT_DOMAIN),
                'view_item' => __('View Cheese Texture', self::TEXT_DOMAIN),
                'separate_items_with_commas' => __('Separate items with commas', self::TEXT_DOMAIN),
                'add_or_remove_items' => __('Add or remove items', self::TEXT_DOMAIN),
                'choose_from_most_used' => __('Choose from the most used', self::TEXT_DOMAIN),
                'popular_items' => __('Popular Cheese Textures', self::TEXT_DOMAIN),
                'search_items' => __('Search Cheese Textures', self::TEXT_DOMAIN),
                'not_found' => __('Not Found', self::TEXT_DOMAIN),
            );
            $args = array(
                'labels' => $labels,
                'hierarchical' => true,
                'public' => false,
                'show_ui' => true,
                'show_admin_column' => false,
                'show_in_nav_menus' => true,
                'show_tagcloud' => true,
            );
            register_taxonomy(self::TAX_CHEESE_TEXTURE, '', $args);

            $labels = array(
                'name' => _x('Wine Types', 'Taxonomy General Name', self::TEXT_DOMAIN),
                'singular_name' => _x('Wine Type', 'Taxonomy Singular Name', self::TEXT_DOMAIN),
                'menu_name' => __('Wine Types', self::TEXT_DOMAIN),
                'all_items' => __('All Wine Types', self::TEXT_DOMAIN),
                'parent_item' => __('Parent Wine Type', self::TEXT_DOMAIN),
                'parent_item_colon' => __('Parent Type:', self::TEXT_DOMAIN),
                'new_item_name' => __('New Wine Type', self::TEXT_DOMAIN),
                'add_new_item' => __('Add New Wine Type', self::TEXT_DOMAIN),
                'edit_item' => __('Edit Wine Type', self::TEXT_DOMAIN),
                'update_item' => __('Update Wine Type', self::TEXT_DOMAIN),
                'view_item' => __('View Wine Type', self::TEXT_DOMAIN),
                'separate_items_with_commas' => __('Separate items with commas', self::TEXT_DOMAIN),
                'add_or_remove_items' => __('Add or remove items', self::TEXT_DOMAIN),
                'choose_from_most_used' => __('Choose from the most used', self::TEXT_DOMAIN),
                'popular_items' => __('Popular Wine Types', self::TEXT_DOMAIN),
                'search_items' => __('Search Wine Types', self::TEXT_DOMAIN),
                'not_found' => __('Not Found', self::TEXT_DOMAIN),
            );
            $args = array(
                'labels' => $labels,
                'hierarchical' => true,
                'public' => false,
                'show_ui' => true,
                'show_admin_column' => false,
                'show_in_nav_menus' => true,
                'show_tagcloud' => true,
            );
            register_taxonomy(self::TAX_WINE_TYPE, '', $args);

            $labels = array(
                'name' => _x('Allergens', 'Taxonomy General Name', self::TEXT_DOMAIN),
                'singular_name' => _x('Allergen', 'Taxonomy Singular Name', self::TEXT_DOMAIN),
                'menu_name' => __('Allergens', self::TEXT_DOMAIN),
                'all_items' => __('All Allergens', self::TEXT_DOMAIN),
                'parent_item' => __('Parent Allergen', self::TEXT_DOMAIN),
                'parent_item_colon' => __('Parent Allergen:', self::TEXT_DOMAIN),
                'new_item_name' => __('New Allergen', self::TEXT_DOMAIN),
                'add_new_item' => __('Add New Allergen', self::TEXT_DOMAIN),
                'edit_item' => __('Edit Allergen', self::TEXT_DOMAIN),
                'update_item' => __('Update Allergen', self::TEXT_DOMAIN),
                'view_item' => __('View Allergen', self::TEXT_DOMAIN),
                'separate_items_with_commas' => __('Separate items with commas', self::TEXT_DOMAIN),
                'add_or_remove_items' => __('Add or remove items', self::TEXT_DOMAIN),
                'choose_from_most_used' => __('Choose from the most used', self::TEXT_DOMAIN),
                'popular_items' => __('Popular Allergens', self::TEXT_DOMAIN),
                'search_items' => __('Search Allergens', self::TEXT_DOMAIN),
                'not_found' => __('Not Found', self::TEXT_DOMAIN),
            );
            $args = array(
                'labels' => $labels,
                'hierarchical' => true,
                'public' => false,
                'show_ui' => true,
                'show_admin_column' => false,
                'show_in_nav_menus' => true,
                'show_tagcloud' => true,
            );
            register_taxonomy(self::TAX_ALLERGENS_INFO, '', $args);

            register_post_type(self::POSTTYPE_PRODUCT, array(
                'labels' => array(
                    'name' => __('Products', self::TEXT_DOMAIN),
                    'singular_name' => __('Product', self::TEXT_DOMAIN),
                    'add_new' => __('Add Product', self::TEXT_DOMAIN),
                    'add_new_item' => __('Add New Product', self::TEXT_DOMAIN),
                    'edit' => __('Edit', self::TEXT_DOMAIN),
                    'edit_item' => __('Edit Product', self::TEXT_DOMAIN),
                    'new_item' => __('New Product', self::TEXT_DOMAIN),
                    'view' => __('View', self::TEXT_DOMAIN),
                    'view_item' => __('View Product', self::TEXT_DOMAIN),
                    'search_items' => __('Search Product', self::TEXT_DOMAIN),
                    'not_found' => __('No Product found', self::TEXT_DOMAIN),
                    'not_found_in_trash' => __('No Product found in Trash', self::TEXT_DOMAIN),
                    'parent' => __('Parent Product', self::TEXT_DOMAIN)
                ),
                'public' => true,
                'show_in_nav_menus' => true,
                'menu_position' => 26,
                'menu_icon' => 'dashicons-products',
                'taxonomies' => array(
                    self::TAX_PRODUCTTYPE,
                    self::TAX_PRODUCER,
                    self::TAX_PRODUCTREGION,
                    self::TAX_CHEESE_MILKANIMAL,
                    self::TAX_CHEESE_MILKTYPE,
                    self::TAX_CHEESE_TEXTURE,
                    self::TAX_WINE_TYPE,
                    self::TAX_ALLERGENS_INFO,
                ),
                'rewrite' => array('slug' => 'product-catalog'),
                'query_var' => true,
                'has_archive' => true,
                'supports' => array(
                    'title',
                    'editor',
                    'excerpt',
                    'trackbacks',
                    'custom-fields',
                    'comments',
                    'revisions',
                    'thumbnail',
                    'author',
                    'page-attributes'
                )
            ));
        }

        function metaboxes($metaboxes) {
            $metaboxes[] = array(
                'id' => 'dms3-product-main',
                'title' => __('Product Common Information', self::TEXT_DOMAIN),
                'pages' => array(self::POSTTYPE_PRODUCT),
                'context' => 'normal',
                'priority' => 'high',
                'fields' => array(
                    array(
                        'name' => __('Product Type', self::TEXT_DOMAIN),
                        'id' => self::TAX_PRODUCTTYPE,
                        'type' => 'taxonomy',
                        // 'multiple' => true,
                        // 'js_options' => array("width" => "100%"),
                        'placeholder' => __("Select a product type", self::TEXT_DOMAIN),
                        'options' => array(
                            'taxonomy' => self::TAX_PRODUCTTYPE,
                            // How to show taxonomy: 'checkbox_list' (default) or 'checkbox_tree', 'select_tree', select_advanced or 'select'. Optional
                            'type' => 'select_advanced',
                            // Additional arguments for get_terms() function. Optional
                            'args' => array()
                        ),
                    ),
                    array(
                        'name' => __('Featured Image', self::TEXT_DOMAIN),
                        'type' => 'image_advanced',
                        'max_file_uploads' => 1,
                        'id' => self::OPTION_THUMBNAILID,
                    ),
                    array(
                        'name' => __('Region', self::TEXT_DOMAIN),
                        'id' => self::TAX_PRODUCTREGION,
                        'type' => 'taxonomy',
                        'placeholder' => __("Select a region", self::TEXT_DOMAIN),
                        // 'multiple' => true,
                        // 'js_options' => array("width" => "100%"),
                        'options' => array(
                            'taxonomy' => self::TAX_PRODUCTREGION,
                            // How to show taxonomy: 'checkbox_list' (default) or 'checkbox_tree', 'select_tree', select_advanced or 'select'. Optional
                            'type' => 'select_advanced',
                            // Additional arguments for get_terms() function. Optional
                            'args' => array()
                        ),
                    ),
                    array(
                        'name' => __('Producer', self::TEXT_DOMAIN),
                        'id' => self::TAX_PRODUCER,
                        'type' => 'taxonomy',
                        'multiple' => true,
                        'js_options' => array("width" => "50%"),
                        'placeholder' => __("Select the producer", self::TEXT_DOMAIN),
                        'options' => array(
                            'taxonomy' => self::TAX_PRODUCER,
                            // How to show taxonomy: 'checkbox_list' (default) or 'checkbox_tree', 'select_tree', select_advanced or 'select'. Optional
                            'type' => 'select_advanced',
                            // Additional arguments for get_terms() function. Optional
                            'args' => array()
                        ),
                    ),
                    array(
                        'name' => __('Allergens information', self::TEXT_DOMAIN),
                        'id' => self::TAX_ALLERGENS_INFO,
                        'type' => 'taxonomy',
                        'multiple' => true,
                        'js_options' => array("width" => "100%"),
                        'placeholder' => __("Select the needed Allergen Information", self::TEXT_DOMAIN),
                        'options' => array(
                            'taxonomy' => self::TAX_ALLERGENS_INFO,
                            // How to show taxonomy: 'checkbox_list' (default) or 'checkbox_tree', 'select_tree', select_advanced or 'select'. Optional
                            'type' => 'select_advanced',
                            // Additional arguments for get_terms() function. Optional
                            'args' => array(),
                        ),
                    ),
                )
            );
            $metaboxes[] = array(
                'id' => 'dms3-product-wine',
                'title' => __('Wine Specific Information', self::TEXT_DOMAIN),
                'pages' => array(self::POSTTYPE_PRODUCT),
                'context' => 'normal',
                'priority' => 'high',
                'fields' => array( array(
                        'name' => __('Wine Types', self::TEXT_DOMAIN),
                        'id' => self::TAX_WINE_TYPE,
                        'type' => 'taxonomy',
                        'multiple' => true,
                        'placeholder' => __("Select the needed Wine Types", self::TEXT_DOMAIN),
                        'js_options' => array("width" => "100%"),
                        'options' => array(
                            'taxonomy' => self::TAX_WINE_TYPE,
                            // How to show taxonomy: 'checkbox_list' (default) or 'checkbox_tree', 'select_tree', select_advanced or 'select'. Optional
                            'type' => 'select_advanced',
                            // Additional arguments for get_terms() function. Optional
                            'args' => array(),
                        ),
                    ), )
            );
            $metaboxes[] = array(
                'id' => 'dms3-product-cheese',
                'title' => __('Cheese Specific Information', self::TEXT_DOMAIN),
                'pages' => array(self::POSTTYPE_PRODUCT),
                'context' => 'normal',
                'priority' => 'high',
                'fields' => array(
                    array(
                        'name' => __('Milk Origin', self::TEXT_DOMAIN),
                        'id' => self::TAX_CHEESE_MILKANIMAL,
                        'type' => 'taxonomy',
                        'multiple' => true,
                        'js_options' => array("width" => "100%"),
                        'placeholder' => __("Select the animals whose milk this cheese is made", self::TEXT_DOMAIN),
                        'options' => array(
                            'taxonomy' => self::TAX_CHEESE_MILKANIMAL,
                            // How to show taxonomy: 'checkbox_list' (default) or 'checkbox_tree', 'select_tree', select_advanced or 'select'. Optional
                            'type' => 'select_advanced',
                            // Additional arguments for get_terms() function. Optional
                            'args' => array(),
                        )
                    ),
                    array(
                        'name' => __('Milk Type', self::TEXT_DOMAIN),
                        'id' => self::TAX_CHEESE_MILKTYPE,
                        'type' => 'taxonomy',
                        'multiple' => true,
                        'placeholder' => __("Select the milk characteristics", self::TEXT_DOMAIN),
                        'js_options' => array("width" => "100%"),
                        'options' => array(
                            'taxonomy' => self::TAX_CHEESE_MILKTYPE,
                            // How to show taxonomy: 'checkbox_list' (default) or 'checkbox_tree', 'select_tree', select_advanced or 'select'. Optional
                            'type' => 'select_advanced',
                            // Additional arguments for get_terms() function. Optional
                            'args' => array(),
                        )
                    ),
                    array(
                        'name' => __('Cheese Texture', self::TEXT_DOMAIN),
                        'id' => self::TAX_CHEESE_TEXTURE,
                        'type' => 'taxonomy',
                        'multiple' => true,
                        'js_options' => array("width" => "100%"),
                        'placeholder' => __("Select Textures of this cheese", self::TEXT_DOMAIN),
                        'options' => array(
                            'taxonomy' => self::TAX_CHEESE_TEXTURE,
                            // How to show taxonomy: 'checkbox_list' (default) or 'checkbox_tree', 'select_tree', select_advanced or 'select'. Optional
                            'type' => 'select_advanced',
                            // Additional arguments for get_terms() function. Optional
                            'args' => array(),
                        )
                    )
                )
            );
            $metaboxes[] = array(
                'id' => 'dms3-product-shopping',
                'title' => __('Product Shopping Information', self::TEXT_DOMAIN),
                'pages' => array(self::POSTTYPE_PRODUCT),
                'context' => 'normal',
                'priority' => 'high',
                'fields' => array( array(
                        'name' => __('Shopping URL', self::TEXT_DOMAIN),
                        'type' => 'url',
                        'id' => self::OPTION_SALES_URL,
                        'size' => 100,
                    ), )
            );
            $metaboxes[] = array(
                'id' => 'dms3-related-products',
                'title' => __('Related Products', self::TEXT_DOMAIN),
                'pages' => array(
                    'post',
                    'event'
                ),
                'context' => 'normal',
                'priority' => 'high',
                'fields' => array( array(
                        'name' => __('Related Products', self::TEXT_DOMAIN),
                        'id' => self::OPTION_RELATEDPRODUCTS,
                        'type' => 'post',
                        // Post type
                        'post_type' => self::POSTTYPE_PRODUCT,
                        // Field type, either 'select' or 'select_advanced' (default)
                        'field_type' => 'select_advanced',
                        'placeholder' => __('Select an Item', 'your-prefix'),
                        'multiple' => true,
                        // Query arguments (optional). No settings means get all published posts
                        'query_args' => array(
                            'post_status' => 'publish',
                            'posts_per_page' => -1,
                        ),
                        'js_options' => array("width" => "100%"),
                    ), )
            );
            return $metaboxes;
        }

        /**
         * @since 2.0
         */
        function do_meta_boxes() {
            remove_meta_box('postimagediv', self::POSTTYPE_PRODUCT, 'side');
            remove_meta_box('product-typediv', self::POSTTYPE_PRODUCT, 'side');
            remove_meta_box('product-markdiv', self::POSTTYPE_PRODUCT, 'side');
            remove_meta_box('product-regiondiv', self::POSTTYPE_PRODUCT, 'side');
            remove_meta_box('postexcerpt', self::POSTTYPE_PRODUCT, 'normal');
            remove_meta_box('product-cheese-milk-animaldiv', self::POSTTYPE_PRODUCT, 'side');
            remove_meta_box('product-cheese-milk-typediv', self::POSTTYPE_PRODUCT, 'side');
            remove_meta_box('product-cheese-texturediv', self::POSTTYPE_PRODUCT, 'side');
            remove_meta_box('product-wine-typediv', self::POSTTYPE_PRODUCT, 'side');
            remove_meta_box('product-allergens-infodiv', self::POSTTYPE_PRODUCT, 'side');
        }

        /**
         * since 2.0
         */
        function excerpt_metabox() {
            global $post;
            $scr = get_current_screen();
            if ($scr -> post_type == self::POSTTYPE_PRODUCT) :
                echo "<div class='postbox'>";
                echo "<div class='handlediv' title='" . __("Clic to toggle", self::TEXT_DOMAIN) . "'><br></div>";
                echo "<h3 class='hndle ui-sortable-handle'>" . __("Product slogan", self::TEXT_DOMAIN) . "</h3>";
                echo "<div class='inside'>";
                post_excerpt_meta_box($post);
                echo "</div>";
                echo "</div>";
            endif;
        }

        /**
         * @since 2.0
         */
        function add_metaboxes() {
            add_meta_box('dms3_catalog_relatedPosts', __("Related Posts (and events)", self::TEXT_DOMAIN), array(
                $this,
                "metabox_relatedPosts"
            ), self::POSTTYPE_PRODUCT, 'normal', 'high');
        }

        /**
         * @since 2.0
         */
        function metabox_relatedPosts($post) {
            if ($post -> post_status == 'auto-draft') :
                echo "<p>" . __('If the post is not saved, information cannot be displayed', self::TEXT_DOMAIN) . "</p>";
            else :
                $rposts = $this -> getRelatedPosts($post -> ID);
                if (count($rposts) > 0) :
                    echo "<table width='100%'><tr><td width='60%'>";
                    echo "<h4>" . __("Related Posts", self::TEXT_DOMAIN) . "</h4>";
                    echo "<table class='table'>";
                    echo "<tr><th>" . __('Post Title', self::TEXT_DOMAIN) . "</th><th>" . __("Post Type", self::TEXT_DOMAIN) . "</th><th>" . __("Actions", self::TEXT_DOMAIN) . "</th></tr>";
                    foreach ($rposts as $r) :
                        echo "<tr><td>" . $r -> post_title . "</td><td>" . $r -> post_type . "</td><td><a href='" . get_permalink($r -> ID) . "' target='_blank' class='button button-primary'>" . __("Open in a new window", self::TEXT_DOMAIN) . "</a></td></tr>";
                    endforeach;
                    echo "</table>";
                    echo "</td><td>";
                    $rprods = $this -> getProductRelatedProducts($post -> ID);
                    echo "<h4>" . __("Related Products", self::TEXT_DOMAIN) . "</h4>";
                    echo "<table class='table'>";
                    echo "<tr><th>" . __('Product', self::TEXT_DOMAIN) . "</th><th>" . __("Actions", self::TEXT_DOMAIN) . "</th></tr>";
                    foreach ($rprods as $r) :
                        echo "<tr><td>" . $r -> post_title . "</td><td><a href='" . get_permalink($r -> ID) . "' target='_blank' class='button button-primary'>" . __("Open in a new window", self::TEXT_DOMAIN) . "</a></td></tr>";
                    endforeach;
                    echo "</table>";
                    echo "</td></tr></table>";
                endif;
            endif;
        }

        /**
         * @since 2.0
         */
        function getRelatedPosts($productID) {
            global $wpdb;
            $sql = "SELECT post_id" . " FROM " . $wpdb -> prefix . "postmeta pm" . " WHERE meta_key='" . self::OPTION_RELATEDPRODUCTS . "'" . " AND meta_value=$productID";
            $query = $wpdb -> get_results($sql);
            $in = array();
            foreach ($query as $r) :
                $in[] = $r -> post_id;
            endforeach;
            if (count($in) == 0) :
                return array();
            endif;
            $posts = get_posts(array(
                'post__in' => $in,
                'posts_per_page' => -1
            ));
            $relatedPosts = $posts;
            return $relatedPosts;
        }

        /**
         * @since 2.0
         */
        function getProductRelatedProducts($productID) {
            $relatedPosts = $this -> getRelatedPosts($productID);
            $relatedProductsID = array();
            foreach ($relatedPosts as $post) :
                $rp = $this -> getRelatedProductsIDs($post -> ID);
                foreach ($rp as $r) :
                    if ($r <> $productID) :
                        $relatedProductsID[$r] = (int)$r;
                    endif;
                endforeach;
            endforeach;
            if (count($relatedProductsID) == 0) :
                return array();
            endif;
            $relatedProducts = get_posts(array(
                'post__in' => $relatedProductsID,
                'post_type' => self::POSTTYPE_PRODUCT,
                'posts_per_page' => -1
            ));
            return $relatedProducts;
        }

        /**
         * @since 2.0
         */
        function getRelatedProductsIDs($postID) {
            return get_post_meta($postID, self::OPTION_RELATEDPRODUCTS);
        }

        function getRelatedProducts($postID) {
            $ids = $this -> getRelatedProductsIDs($postID);
            if (count($ids) == 0)
                return array();
            $relatedProducts = get_posts(array(
                'post__in' => $ids,
                'post_type' => self::POSTTYPE_PRODUCT,
                'posts_per_page' => -1
            ));
            return $relatedProducts;
        }

        /**
         * Adds field imagesrc to each one of the products in the array of products 
         * @since 2.0
         */
         function add_product_imagesrc($products,$size) {
             $result=$products;
             if(is_array($products)):
                 foreach($result as $k=>$d):
                     $url=wp_get_attachment_url( get_post_thumbnail_id($d->ID) ,$size);
                     if($url):
                         $d->imagesrc=$url;
                         $result[$k]=$d;
                     endif;
                 endforeach;
             endif;
             return $result;
         }

        /**
         * Adds field imagesrc to each one of the terms in the array of terms 
         * @since 2.0
         */
         function add_terms_imagesrc($terms,$size) {
             $result=$terms;
             if (is_array($result)):
                if (function_exists('z_taxonomy_image_url')) :                         
                    foreach($result as $k=>$d):
                        $url = z_taxonomy_image_url($d->term_id, $size);
                        if($url):
                            $d->imagesrc=$url;
                            $result[$k]=$d;
                        endif;
                    endforeach;
                endif;
             endif;
             return $result;
         }
         
         
         /**
          * @since 2.0
          */
        function print_columns($posts,$columns,$imageProperty="imagesrc") {
            $output="";
            if(is_array($posts)):
                $class="span".((int) (12 / $columns));
                $output.="<div class='row-fluid'>";
                $i=1;
                foreach($posts as $p):
                    $url=get_permalink($p->ID);
                    $title=$p->post_title;
                    $output.="<div class='$class'>";
                    if(property_exists($p, $imageProperty)):
                        $imagesrc=$p->{$imageProperty};
                        $output.="<a href='$url' title='$title' class='image'>";
                        $output.="<img src='$imagesrc' />";
                        $output.="</a>";
                    endif;
                    $output .="<a href='$url' title='$title' class='text'>$title</a>";
                    $output .= "</div>";
                    if($i%$columns==0):
                        $output.="</div><div class='row-fluid'>";
                    endif;
                    $i++;
                endforeach;
                $output.="</div>";
            endif;
            return $output;    
        }

         /**
          * @since 2.0
          */
        function print_terms_columns($terms,$columns,$attr=array(),$imageProperty="imagesrc") {
            $withLinks=isset($attr['links'])?$attr['links']:false;
            $withName=isset($attr['name'])?$attr['name']:true;
            $withDescription=isset($attr['description'])?$attr['description']:true;
            $output="";
            if(is_array($terms)):
                $class="span".((int) (12 / $columns));
                $output.="<div class='row-fluid'>";
                $i=1;
                foreach($terms as $p):
                    $url=get_term_link($p->term_id);
                    $title=$p->name;
                    $description=$p->description;
                    $output.="<div class='$class'>";
                    if(property_exists($p, $imageProperty)):
                        $imagesrc=$p->{$imageProperty};
                        if($withLinks):
                            $output.="<a href='$url' title='$title' class='image'>";
                        endif;
                        $output.="<img src='$imagesrc' />";
                        if($withLinks):
                            $output.="</a>";
                        endif;
                    endif;
                    if($withName):
                        if($withLinks):
                            $output .="<a href='$url' title='$title' class='text'>$title</a>";
                        else:
                            $output .="<span class='text'>$title</span>";
                        endif;
                    endif;
                    if($withDescription):
                        $output.="<span class='description'>$description</span>";
                    endif;
                    $output .= "</div>";
                    if($i%$columns==0):
                        $output.="</div><div class='row-fluid'>";
                    endif;
                    $i++;
                endforeach;
                $output.="</div>";
            endif;
            return $output;    
        }
          
        /**
         * @since 2.0
         */
        function shortcode_related_products($attr) {
            global $post;
            extract(shortcode_atts(array(
                'post_id' => $post -> ID,
                'cols' => 4,
                'size' => self::IMAGESIZENAME,
            ), $atts));
            $relatedProducts = $this -> getRelatedProducts($post_id);
            if($size<>''):
                $relatedProducts=$this->add_product_imagesrc($relatedProducts,$size);
            endif;
            $result = $this->print_columns($relatedProducts,$cols);
            return $result;
        }

        /**
         * @since 2.0
         */
        function shortcode_mark($attr) {
            if (isset($attr['cols'])) :
                $cols = $attr['cols'];
            else :
                $cols = 4;
            endif;
            $spanClass = 'span' . ((int) (12 / $cols));
            $args = array(
                'orderby' => 'slug',
                'order' => 'ASC',
                'hide_empty' => false,
            );
            $resultat = "<div class='dms3-marks'><div class='row-fluid'>";
            $marks = get_terms('product-mark', $args);
            $i = 0;
            foreach ($marks as $mark) :
                $queryArgs = array(
                    'post_type' => 'product',
                    'orderby' => 'name',
                    'order' => 'ASC',
                    'posts_per_page' => 1,
                    'tax_query' => array( array(
                            'taxonomy' => 'product-mark',
                            'field' => 'id',
                            'terms' => $mark -> term_id,
                        ), ),
                );
                $newQuery = new WP_Query();
                $newQuery -> query($queryArgs);
                $products = $newQuery -> posts;
                if (isset($products[0])) :
                    $p = $products[0];
                    $url = get_permalink($p -> ID);
                    $title = $p -> post_title;
                else :
                    $url = '#';
                    $title = '';
                endif;
                if (function_exists('z_taxonomy_image_url')) :
                    $image_src = z_taxonomy_image_url($mark, 'medium');
                else :
                    if (function_exists('s8_get_taxonomy_image_src')) :
                        $image_src = s8_get_taxonomy_image_src($mark, 'medium');
                    else :
                        $image_src = false;
                    endif;
                endif;
                $resultat .= "<a href='$url' title='$title' class='$spanClass'>";
                if (false != $image_src) :
                    $src = $image_src['src'];
                    $width = $image_src['width'];
                    $height = $image_src['height'];
                    $resultat .= "<img src='$src' width=$width height=$height class='product-mark-logo alignnone'/>";
                else :
                    $resultat .= "<span class='product-mark-logo'>" . $mark -> name . "</span>";
                endif;
                $resultat .= '</a>';
                $i++;
                if ($i % $cols == 0) :
                    $resultat .= "</div><div class='row-fluid'>";
                endif;
                $resultat .= "</div></div>";
            endforeach;
            return $resultat;
        }

        function shortcode_wine_type($atts) {
            global $post;
            extract(shortcode_atts(array(
                'post_id' => $post -> ID,
                'cols' => 4,
                'size' => self::IMAGESIZENAME,
            ), $atts));
            $arr=get_the_terms($post_id,self::TAX_WINE_TYPE);
            if($size<>''):
                $arr=$this->add_terms_imagesrc($arr,$size);
            endif;
            $result = $this->print_terms_columns($arr,$cols);
            return $result;
        }
        
        function shortcode_cheese_milk_origin($atts) {
            global $post;
            extract(shortcode_atts(array(
                'post_id' => $post -> ID,
                'cols' => 4,
                'size' => self::IMAGESIZENAME,
            ), $atts));
            $arr=get_the_terms($post_id,self::TAX_CHEESE_MILKANIMAL);
            if($size<>''):
                $arr=$this->add_terms_imagesrc($arr,$size);
            endif;
            $result = $this->print_terms_columns($arr,$cols);
            return $result;            
        }

        function shortcode_cheese_milk_type($atts) {
            global $post;
            extract(shortcode_atts(array(
                'post_id' => $post -> ID,
                'cols' => 4,
                'size' => self::IMAGESIZENAME,
            ), $atts));
            $arr=get_the_terms($post_id,self::TAX_CHEESE_MILKTYPE);
            if($size<>''):
                $arr=$this->add_terms_imagesrc($arr,$size);
            endif;
            $result = $this->print_terms_columns($arr,$cols);
            return $result;            
        }

        function shortcode_cheese_texture($atts) {
            global $post;
            extract(shortcode_atts(array(
                'post_id' => $post -> ID,
                'cols' => 4,
                'size' => self::IMAGESIZENAME,
            ), $atts));
            $arr=get_the_terms($post_id,self::TAX_CHEESE_TEXTURE);
            if($size<>''):
                $arr=$this->add_terms_imagesrc($arr,$size);
            endif;
            $result = $this->print_terms_columns($arr,$cols);
            return $result;            
        }

        function shortcode_product_region($atts) {
            global $post;
            extract(shortcode_atts(array(
                'post_id' => $post -> ID,
                'cols' => 1,
                'size' => self::IMAGESIZENAME,
            ), $atts));
            $arr=get_the_terms($post_id,self::TAX_PRODUCTREGION);
            if($size<>''):
                $arr=$this->add_terms_imagesrc($arr,$size);
            endif;
            $result = $this->print_terms_columns($arr,$cols);
            return $result;            
        }
        
        function shortcode_product_producer($atts) {
            global $post;
            extract(shortcode_atts(array(
                'post_id' => $post -> ID,
                'cols' => 1,
                'size' => self::IMAGESIZENAME,
                'description'=>true,
                'name'=>true,
                'withoutlinks'=>true
            ), $atts));
            $arr=get_the_terms($post_id,self::TAX_PRODUCER);
            if($size<>''):
                $arr=$this->add_terms_imagesrc($arr,$size);
            endif;
            $result = $this->print_terms_columns($arr,$cols,array(
                'links'=>!$withoutlinks,
                'description'=>$description,
                'name'=>$name,
            ) );
            return $result;            
        }
        
        function shortcode_product_allergens($atts) {
            global $post;
            extract(shortcode_atts(array(
                'post_id' => $post -> ID,
                'cols' => 4,
                'size' => self::IMAGESIZENAME,
            ), $atts));
            $arr=get_the_terms($post_id,self::TAX_ALLERGENS_INFO);
            if($size<>''):
                $arr=$this->add_terms_imagesrc($arr,$size);
            endif;
            $result = $this->print_terms_columns($arr,$cols);
            return $result;            
        }

        function shortcode_product_slogan($atts) {
            global $post;
            extract(shortcode_atts(array(
                'post_id' => $post -> ID,
            ), $atts));
            if($post_id==$post->ID):
                $p=$post;
            else:
                $p=get_post($post_id,self::POSTTYPE_PRODUCT);
            endif;
            if($p->post_excerpt):
                $result = "<span class='dms3-product-slogan'>".$p->post_excerpt."</span>";
            else:
                $result='';
            endif;
            return $result;            
        }
        
        function shortcode_products_in_mark($atts) {
            global $post;
            extract(shortcode_atts(array(
                'post_id' => $post -> ID,
                'cols' => 4,
                'size' => self::IMAGESIZENAME,
                'mode'=> 'image',
                'index'=>-1,
            ), $atts));
            $terms=get_the_terms($post_id,self::TAX_PRODUCER);
            if(is_array($terms)):
                $theTerms=array();
                if($index==-1):
                    foreach($terms as $t):
                        $theTerms[]=$t->term_id;
                    endforeach;
                else:
                    $theTerms=$terms[$index];
                endif;
                $theTerms=implode(",",$theTerms);
                $queryArgs = array(
                        'post_type' => 'product',
                        'orderby' => 'name',
                        'order' => 'ASC',
                        'posts_per_page' => -1,
                        'tax_query' => array( array(
                                'taxonomy' => 'product-mark',
                                'field' => 'id',
                                'terms' => $theTerms,
                            ), ),
                    );
                    $newQuery = new WP_Query();
                    $newQuery -> query($queryArgs);
                    $products = $newQuery -> posts;
                    foreach ($products as $product) :
                        echo '<a href="' . get_permalink($product) . '" title="' . $product -> post_title . '">';
                        if ("text" == $instance['mode']) :
                            echo "<span class='demomentsomtres-catalog-product'>$product->post_title</span>";
                        else :
                            echo get_the_post_thumbnail($product -> ID, $size);
                        endif;
                        echo '</a>';
                    endforeach;
                
                if($size<>''):
                    $arr=$this->add_terms_imagesrc($arr,$size);
                endif;
                $result = $this->print_terms_columns($arr,$cols);
            else:
                $result="";
            endif;
            return $result;            
        }

      function shortcode_product_name($atts) {
            global $post;
            extract(shortcode_atts(array(
                'post_id' => $post -> ID,
                'tag'=> 'h1',
            ), $atts));
            if($post_id==$post->ID):
                $p=$post;
            else:
                $p=get_post($post_id,self::POSTTYPE_PRODUCT);
            endif;
            $result = "<$tag class='dms3-product-name'>".$p->post_title."</$tag>";
            return $result;            
        }
    }
?>