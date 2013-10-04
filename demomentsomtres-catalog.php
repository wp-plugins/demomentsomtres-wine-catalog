<?php
/*
  Plugin Name: DeMomentSomTres Wine Catalog
  Plugin URI: http://demomentsomtres.com/catala
  Description: Shows your products in the web based on marks and product types. The shortcode [demomentsomtres-product-mark cols=n] shows all the products in the same mark than the current product. You also can include "echo dmst_catalog_shortcode($attr);" in your template to show the same contents.
  Version: 1.2.1
  Author: Marc Queralt
  Author URI: http://demomentsomtres.com/
  License: GPLv2
 * Change history
 * V1.1 New taxonomy Product Region. Catalan & Spanish translation.
 * v1.0 Initial version
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

//function dmst_catalog_shortcode($attr) {
//    global $post;
//    $result = '';
//    if (isset($attr['cols'])):
//        $cols = $attr['cols'];
//    else:
//        $cols = 4;
//    endif;
//    $tax_mark = wp_get_post_terms($post->ID, 'product-mark', array());
//    if (!isset($tax_mark[0])):
//        return '';
//    endif;
//    $mark = $tax_mark[0];
//    $args = array(
//        'post_type' => 'product',
//        'tax_query' => array(
//            array(
//                'taxonomy' => 'product-mark',
//                'field' => 'slug',
//                'terms' => $mark->slug
//            )
//        ),
//        'orderby' => 'name',
//        'order' => 'ASC',
//        'nopaging' => 'true'
//    );
//    $query = new WP_Query($args);
//    $products = $query->posts;
//    $i = 0;
//    $result .= '<div class="dmst_catalog_category">';
//    if ($mark->description == ''):
//        $title = $mark->title;
//    else:
//        $title = $mark->description;
//    endif;
//    $result .= '<h2>' . $title . '</h2>';
//    foreach ($products as $product):
//        $i = ($i + 1) % $cols;
//        if ($i == 0):
//            $classe = " last";
//        else:
//            $classe = "";
//        endif;
//        $result .= '<a class="product_image" href="' . get_permalink($product->ID) . '">';
//        $result .= '<div class="dmst_catalog_product' . $classe . '>';
//        if (has_post_thumbnail($product->ID)):
//            $result .= get_the_post_thumbnail($product->ID, 'medium');
//        else:
//            $result .= '<p class="no_image">' . __('No image', DMST_CATALOG_DOMAIN) . '</p>';
//        endif;
//        $result .= '</a>';
//        $result .= '</div>';
//    endforeach;
//    $result.='</div>';
//    return $result;
//}

class ProductsInCategoryWidget extends WP_Widget {

    function ProductsInCategoryWidget() {
        parent::WP_Widget(false, $name = 'Products in category');
    }

    function form($instance) {
        $title = esc_attr($instance['title']);
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
        <?php
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
                    echo get_the_post_thumbnail($product->ID, 'medium');
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
    $i=0;
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
        if(isset($products[0])):
            $p=$products[0];
            $url=get_permalink($p->ID);
            $title=$p->post_title;
        else:
            $url='#';
            $title='';
        endif;
        if (function_exists('s8_get_taxonomy_image_src')):
            $image_src = s8_get_taxonomy_image_src($mark, 'medium');
        else:
            $image_src = false;
        endif;
        $resultat .= "<a href='".$url."' title='".$title."'>";
        if (false != $image_src):
            $src = $image_src['src'];
            $width = $image_src['width'];
            $height = $image_src['height'];
            $resultat .= "<img src='$src' width=$width height=$height class='product-mark-logo alignnone'/>";
        else:
            $resultat .= "<span class='product-mark-logo'>".$mark->name."</span>";
        endif;
        $resultat .= '</a>';
        $i++;
        if($i%$cols==0):
            $resultat.='<br/>';
        endif;
    endforeach;
    return $resultat;
}
?>