<?php
/*
  Plugin Name: DeMomentSomTres Catalog
  Plugin URI: http://demomentsomtres.com/catala
  Description: Shows your products in the web based on marks and product types. The shortcode [demomentsomtres-product-mark cols=n] shows all the products in the same mark than the current product. You also can include "echo dmst_catalog_shortcode($attr);" in your template to show the same contents.
  Version: 1.4.1
  Author: Marc Queralt
  Author URI: http://demomentsomtres.com/
  License: GPLv2
 */
define('DMST_CATALOG_DOMAIN', 'dmst-catalog');

load_plugin_textdomain(DMST_CATALOG_DOMAIN, false, dirname(plugin_basename(__FILE__)) . '/languages');
add_action('init', 'dmst_create_catalog');
add_shortcode('demomentsomtres-marks', 'dmst_catalog_mark_shortcode');

function dmst_create_catalog() {
    $labels = array(
        'name' => _x('Types', 'taxonomy general name', DMST_CATALOG_DOMAIN),
        'singular_name' => _x('Type', 'taxonomy singular name', DMST_CATALOG_DOMAIN),
        'search_items' => __('Search Type', DMST_CATALOG_DOMAIN),
        'all_items' => __('All Types', DMST_CATALOG_DOMAIN),
        'parent_item' => __('Parent Type', DMST_CATALOG_DOMAIN),
        'parent_item_colon' => __('Parent Type:', DMST_CATALOG_DOMAIN),
        'edit_item' => __('Edit Type', DMST_CATALOG_DOMAIN),
        'update_item' => __('Update Type', DMST_CATALOG_DOMAIN),
        'add_new_item' => __('Add New Type', DMST_CATALOG_DOMAIN),
        'new_item_name' => __('New Type Name', DMST_CATALOG_DOMAIN),
    );
    register_taxonomy('product-type', '', array(
        'hierarchical' => true,
        'labels' => $labels
    ));
    $labels = array(
        'name' => _x('Regions', 'taxonomy general name', DMST_CATALOG_DOMAIN),
        'singular_name' => _x('Region', 'taxonomy singular name', DMST_CATALOG_DOMAIN),
        'search_items' => __('Search Region', DMST_CATALOG_DOMAIN),
        'all_items' => __('All Regions', DMST_CATALOG_DOMAIN),
        'parent_item' => __('Parent Region', DMST_CATALOG_DOMAIN),
        'parent_item_colon' => __('Parent Region:', DMST_CATALOG_DOMAIN),
        'edit_item' => __('Edit Region', DMST_CATALOG_DOMAIN),
        'update_item' => __('Update Region', DMST_CATALOG_DOMAIN),
        'add_new_item' => __('Add New Region', DMST_CATALOG_DOMAIN),
        'new_item_name' => __('New Region Name', DMST_CATALOG_DOMAIN),
    );
    register_taxonomy('product-region', '', array(
        'hierarchical' => true,
        'labels' => $labels
    ));
    $labels = array(
        'name' => _x('Marks', 'taxonomy general name', DMST_CATALOG_DOMAIN),
        'singular_name' => _x('Mark', 'taxonomy singular name', DMST_CATALOG_DOMAIN),
        'search_items' => __('Search Mark', DMST_CATALOG_DOMAIN),
        'all_items' => __('All Marks', DMST_CATALOG_DOMAIN),
        'parent_item' => __('Parent Mark', DMST_CATALOG_DOMAIN),
        'parent_item_colon' => __('Parent Mark:', DMST_CATALOG_DOMAIN),
        'edit_item' => __('Edit Mark', DMST_CATALOG_DOMAIN),
        'update_item' => __('Update Mark', DMST_CATALOG_DOMAIN),
        'add_new_item' => __('Add New Mark', DMST_CATALOG_DOMAIN),
        'new_item_name' => __('New Mark Name', DMST_CATALOG_DOMAIN),
    );
    register_taxonomy('product-mark', '', array(
        'hierarchical' => true,
        'labels' => $labels
    ));
    register_post_type('product', array(
        'labels' => array(
            'name' => __('Products', DMST_CATALOG_DOMAIN),
            'singular_name' => __('Product', DMST_CATALOG_DOMAIN),
            'add_new' => __('Add Product', DMST_CATALOG_DOMAIN),
            'add_new_item' => __('Add New Product', DMST_CATALOG_DOMAIN),
            'edit' => __('Edit', DMST_CATALOG_DOMAIN),
            'edit_item' => __('Edit Product', DMST_CATALOG_DOMAIN),
            'new_item' => __('New Product', DMST_CATALOG_DOMAIN),
            'view' => __('View', DMST_CATALOG_DOMAIN),
            'view_item' => __('View Product', DMST_CATALOG_DOMAIN),
            'search_items' => __('Search Product', DMST_CATALOG_DOMAIN),
            'not_found' => __('No Product found', DMST_CATALOG_DOMAIN),
            'not_found_in_trash' => __('No Product found in Trash', DMST_CATALOG_DOMAIN),
            'parent' => __('Parent Product', DMST_CATALOG_DOMAIN)
        ),
        'public' => true,
        'show_in_nav_menus' => true,
        'menu_position' => 15,
        'taxonomies' => array('product-type', 'product-mark', 'product-region'),
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
            )
    );
}

add_action('widgets_init', create_function('', 'return register_widget("ProductNameWidget");'));

class ProductsInCategoryWidget extends WP_Widget {

    function ProductsInCategoryWidget() {
        parent::WP_Widget(false, $name = 'Products in category');
    }

    function form($instance) {
        $title = esc_attr($instance['title']);
        $mode = isset($instance['mode']) ? $instance['mode'] : 'image';
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
        <p>
            <label for="<?php echo $this->get_field_id('mode'); ?>"><?php _e('Mode', DMST_CATALOG_DOMAIN); ?></label> 
            <input id="<?php echo $this->get_field_id('mode'); ?>" name="<?php echo $this->get_field_name('mode'); ?>" type="radio" value="text" <?php checked("text" == $mode); ?>><?php _e('Text', DMST_CATALOG_DOMAIN); ?></radio>
        <input id="<?php echo $this->get_field_id('mode'); ?>" name="<?php echo $this->get_field_name('mode'); ?>" type="radio" value="image" <?php checked("image" == $mode); ?>><?php _e('Image', DMST_CATALOG_DOMAIN); ?></radio>
        </p>
        <?php
    }

    function update($new_instance, $old_instance) {
        $new_instance['title'] = strip_tags($new_instance['title']);
        $new_instance['mode'] = isset($new_instance['mode']) ? $new_instance['mode'] : 'image';
        return $new_instance;
    }

    function widget($args, $instance) {
        extract($args);
        global $post;
        $marks = wp_get_post_terms($post->ID, 'product-mark');
        if (is_array($marks)):
            if (isset($marks[0])):
                echo $before_widget;
                $title = apply_filters('widget_title', $instance['title']);
                if ($title)
                    echo $before_title . $title . $after_title;
                $mark = $marks[0]->term_id;
                $queryArgs = array(
                    'post_type' => 'product',
                    'orderby' => 'name',
                    'order' => 'ASC',
                    'posts_per_page' => -1,
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'product-mark',
                            'field' => 'id',
                            'terms' => $mark,
                        ),
                    ),
                );
                $newQuery = new WP_Query();
                $newQuery->query($queryArgs);
                $products = $newQuery->posts;
                foreach ($products as $product):
                    if (get_option('permalink_structure')):
                        echo '<a href="' . get_permalink($product) . '" title="' . $product->post_title . '">';
                    else:
                        echo '<a href="' . $product->guid . '" title="' . $product->post_title . '">';
                    endif;
                    if ("text" == $instance['mode']):
                        echo "<span class='demomentsomtres-catalog-product'>$product->post_title</span>";
                    else:
                        echo get_the_post_thumbnail($product->ID, 'medium');
                    endif;
                    echo '</a>';
                endforeach;
                echo $after_widget;
//                echo '<pre style="display:none;">' . print_r($newQuery, true) . '</pre>';
            endif;
        endif;
    }

}

add_action('widgets_init', create_function('', 'return register_widget("ProductsInCategoryWidget");'));

class ProductNameWidget extends WP_Widget {

    function ProductNameWidget() {
        parent::WP_Widget(false, $name = 'Product name');
    }

    function form($instance) {
        $title = esc_attr($instance['title']);
    }

    function update($new_instance, $old_instance) {
        $new_instance['title'] = strip_tags($new_instance['title']);
        return $new_instance;
    }

    function widget($args, $instance) {
        extract($args);
        global $post;
        echo $before_widget;
        echo '<h1>' . $post->post_title . '</h1>';
        echo $after_widget;
    }

}

add_action('widgets_init', create_function('', 'return register_widget("ProductNameWidget");'));

class MarkDescriptionWidget extends WP_Widget {

    function MarkDescriptionWidget() {
        parent::WP_Widget(false, $name = 'Mark description');
    }

    function form($instance) {
        $title = esc_attr($instance['title']);
    }

    function update($new_instance, $old_instance) {
        $new_instance['title'] = strip_tags($new_instance['title']);
        return $new_instance;
    }

    function widget($args, $instance) {
        extract($args);
        global $post;
        $marks = wp_get_post_terms($post->ID, 'product-mark');
        if (is_array($marks)):
            if (isset($marks[0])):
                echo $before_widget;
                $mark = $marks[0]->description;
                echo apply_filters('the_content', $mark);
                echo $after_widget;
            endif;
        endif;
    }

}

add_action('widgets_init', create_function('', 'return register_widget("MarkDescriptionWidget");'));

class MarkLogoWidget extends WP_Widget {

    function MarkLogoWidget() {
        parent::WP_Widget(false, $name = 'Mark Logo');
    }

    function form($instance) {
        $title = esc_attr($instance['title']);
    }

    function update($new_instance, $old_instance) {
        $new_instance['title'] = strip_tags($new_instance['title']);
        return $new_instance;
    }

    function widget($args, $instance) {
        if (function_exists('s8_get_taxonomy_image_src')):
            extract($args);
            global $post;
            $marks = wp_get_post_terms($post->ID, 'product-mark');
            if (is_array($marks)):
                if (isset($marks[0])):
                    $mark = $marks[0];
                    $image_src = s8_get_taxonomy_image_src($mark, 'medium');
                    if (false != $image_src):
                        echo $before_widget;
                        $src = $image_src['src'];
                        $width = $image_src['width'];
                        $height = $image_src['height'];
                        echo "<img src='$src' width=$width height=$height class='product-mark-logo'/>";
                        echo $after_widget;
                    endif;
                endif;
            endif;
        endif;
    }

}

add_action('widgets_init', create_function('', 'return register_widget("MarkLogoWidget");'));

function dmst_catalog_mark_shortcode($attr) {
    if (isset($attr['cols'])):
        $cols = $attr['cols'];
    else:
        $cols = 4;
    endif;
    $args = array(
        'orderby' => 'slug',
        'order' => 'ASC',
        'hide_empty' => false,
    );
    $resultat = '';
    $marks = get_terms('product-mark', $args);
    $i = 0;
    foreach ($marks as $mark):
        $queryArgs = array(
            'post_type' => 'product',
            'orderby' => 'name',
            'order' => 'ASC',
            'posts_per_page' => 1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'product-mark',
                    'field' => 'id',
                    'terms' => $mark->term_id,
                ),
            ),
        );
        $newQuery = new WP_Query();
        $newQuery->query($queryArgs);
        $products = $newQuery->posts;
        if (isset($products[0])):
            $p = $products[0];
            $url = get_permalink($p->ID);
            $title = $p->post_title;
        else:
            $url = '#';
            $title = '';
        endif;
        if (function_exists('s8_get_taxonomy_image_src')):
            $image_src = s8_get_taxonomy_image_src($mark, 'medium');
        else:
            $image_src = false;
        endif;
        $resultat .= "<a href='" . $url . "' title='" . $title . "'>";
        if (false != $image_src):
            $src = $image_src['src'];
            $width = $image_src['width'];
            $height = $image_src['height'];
            $resultat .= "<img src='$src' width=$width height=$height class='product-mark-logo alignnone'/>";
        else:
            $resultat .= "<span class='product-mark-logo'>" . $mark->name . "</span>";
        endif;
        $resultat .= '</a>';
        $i++;
        if ($i % $cols == 0):
            $resultat.='<br/>';
        endif;
    endforeach;
    return $resultat;
}

// v1.4
define('DMST_CATALOG_SALES_URL', 'dms3_catalog_sales_url');
add_action('add_meta_boxes', 'demomentsomtres_catalog_add_metaboxes');

function demomentsomtres_catalog_add_metaboxes() {
    add_meta_box('dms3-catalog-url', __('Sales URL', DMST_CATALOG_DOMAIN), 'demomentsomtres_catalog_url_metabox', 'product', 'normal', 'high');
}

function demomentsomtres_catalog_url_metabox($post) {
    $sales_url = get_post_meta($post->ID, DMST_CATALOG_SALES_URL, true);

    echo '<p>';
    echo __('URL', DMST_CATALOG_DOMAIN) . ': ';
    echo '<input type="text" size="100" name="' . DMST_CATALOG_SALES_URL . '" value="' . esc_attr($sales_url) . '" />';
    echo'</p>';
}

add_action('save_post', 'demomentsomtres_catalog_save_post');

function demomentsomtres_catalog_save_post($post_id) {
    if (isset($_POST[DMST_CATALOG_SALES_URL])):
        update_post_meta($post_id, DMST_CATALOG_SALES_URL, strip_tags($_POST[DMST_CATALOG_SALES_URL]));
    endif;
}

class ProductSalesURLWidget extends WP_Widget {

    function ProductSalesURLWidget() {
        parent::WP_Widget(false, $name = 'Product Sales URL');
    }

    function form($instance) {
        $title = esc_attr($instance['title']);
        $label = esc_attr($instance['label']);
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
        <p><label for="<?php echo $this->get_field_id('lable'); ?>"><?php _e('URL Label:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('label'); ?>" name="<?php echo $this->get_field_name('label'); ?>" type="text" value="<?php echo $label; ?>" /></label></p>
        <?php
    }

    function update($new_instance, $old_instance) {
        $new_instance['title'] = strip_tags($new_instance['title']);
        $new_instance['label'] = strip_tags($new_instance['label']);
        return $new_instance;
    }

    function widget($args, $instance) {
        extract($args);
        global $post;
        $sales_url = get_post_meta($post->ID, DMST_CATALOG_SALES_URL, true);
        $title = apply_filters('widget_title', $instance['title']);
        $label = $instance['label'];
        //echo '<pre class="invisible">' . print_r($instance, true) . '</pre>';
        if ($sales_url != ''):
            echo $before_widget;
            if ($title)
                echo $before_title . $title . $after_title;
            echo '<a href="' . $sales_url . '" target="_blank" class="dms3_catalog_sales_url">';
            if ($label)
                echo $label;
            else
                echo $post->post_title;
            echo '</a>';
            echo $after_widget;
        endif;
    }

}

add_action('widgets_init', create_function('', 'return register_widget("ProductSalesURLWidget");'));
?>