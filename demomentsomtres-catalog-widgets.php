<?php
    /**
     * @deprecated
     */
    class ProductsInCategoryWidget extends WP_Widget {

        function ProductsInCategoryWidget() {
            parent::WP_Widget(false, $name = 'Products in category');
        }

        function form($instance) {
            $title = esc_attr($instance['title']);
            $mode = isset($instance['mode']) ? $instance['mode'] : 'image';
            echo "<p><label for='" . $this -> get_field_id('title') . "'>" . __('Title:', DeMomentSomTresWineAndCheese::TEXT_DOMAIN) . "<input class='widefat' id='" . $this -> get_field_id('title') . "' name='" . $this -> get_field_name('title') . "' type='text' value='" . $title . "' /></label></p>";
            echo "<p><label for='" . $this -> get_field_id('mode') . "'>" . __('Mode', DeMomentSomTresWineAndCheese::TEXT_DOMAIN) . "</label>";
            echo "<input id='" . $this -> get_field_id('mode') . "' name='" . $this -> get_field_name('mode') . "' type='radio' value='text' " . checked("text" == $mode) . ">" . __('Text', DeMomentSomTresWineAndCheese::TEXT_DOMAIN) . "</radio>";
            echo "<input id='" . $this -> get_field_id('mode') . "' name='" . $this -> get_field_name('mode') . "' type='radio' value='image' " . checked("image" == $mode) . ">" . __('Image', DeMomentSomTresWineAndCheese::TEXT_DOMAIN) . "</radio>";
            echo "</p>";
        }

        function update($new_instance, $old_instance) {
            $new_instance['title'] = strip_tags($new_instance['title']);
            $new_instance['mode'] = isset($new_instance['mode']) ? $new_instance['mode'] : 'image';
            return $new_instance;
        }

        function widget($args, $instance) {
            extract($args);
            global $post;
            $marks = wp_get_post_terms($post -> ID, 'product-mark');
            if (is_array($marks)) :
                if (isset($marks[0])) :
                    echo $before_widget;
                    $title = apply_filters('widget_title', $instance['title']);
                    if ($title)
                        echo $before_title . $title . $after_title;
                    $mark = $marks[0] -> term_id;
                    $queryArgs = array(
                        'post_type' => 'product',
                        'orderby' => 'name',
                        'order' => 'ASC',
                        'posts_per_page' => -1,
                        'tax_query' => array( array(
                                'taxonomy' => 'product-mark',
                                'field' => 'id',
                                'terms' => $mark,
                            ), ),
                    );
                    $newQuery = new WP_Query();
                    $newQuery -> query($queryArgs);
                    $products = $newQuery -> posts;
                    foreach ($products as $product) :
                        if (get_option('permalink_structure')) :
                            echo '<a href="' . get_permalink($product) . '" title="' . $product -> post_title . '">';
                        else :
                            echo '<a href="' . $product -> guid . '" title="' . $product -> post_title . '">';
                        endif;
                        if ("text" == $instance['mode']) :
                            echo "<span class='demomentsomtres-catalog-product'>$product->post_title</span>";
                        else :
                            echo get_the_post_thumbnail($product -> ID, 'medium');
                        endif;
                        echo '</a>';
                    endforeach;
                    echo $after_widget;
                endif;
            endif;
        }

    }

    /**
     * @deprecated
     */
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
            echo '<h1>' . $post -> post_title . '</h1>';
            echo $after_widget;
        }

    }

    /**
     * @deprecated
     */
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
            $marks = wp_get_post_terms($post -> ID, 'product-mark');
            if (is_array($marks)) :
                if (isset($marks[0])) :
                    echo $before_widget;
                    $mark = $marks[0] -> description;
                    echo apply_filters('the_content', $mark);
                    echo $after_widget;
                endif;
            endif;
        }

    }

    /**
     * @deprecated
     */
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
            if (function_exists('s8_get_taxonomy_image_src')) :
                extract($args);
                global $post;
                $marks = wp_get_post_terms($post -> ID, 'product-mark');
                if (is_array($marks)) :
                    if (isset($marks[0])) :
                        $mark = $marks[0];
                        $image_src = s8_get_taxonomy_image_src($mark, 'medium');
                        if (false != $image_src) :
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

    class ProductSalesURLWidget extends WP_Widget {

        function ProductSalesURLWidget() {
            parent::WP_Widget(false, $name = 'Product Sales URL');
        }

        function form($instance) {
            $title = esc_attr($instance['title']);
            $label = esc_attr($instance['label']);
            echo "<p><label for='" . $this -> get_field_id('title') . "'>" . __("Title:", DeMomentSomTresWineAndCheese::TEXT_DOMAIN) . "<input class='widefat' id='" . $this -> get_field_id("title") . "' name='" . $this -> get_field_name('title') . "' type='text' value='" . $title . "' /></label></p>";
            echo "<p><label for='" . $this -> get_field_id('label') . "'>" . __("URL Label:", DeMomentSomTresWineAndCheese::TEXT_DOMAIN) . "<input class='widefat' id='" . $this -> get_field_id("label") . "' name='" . $this -> get_field_name('label') . "' type='text' value='" . $label . "' /></label></p>";
        }

        function update($new_instance, $old_instance) {
            $new_instance['title'] = strip_tags($new_instance['title']);
            $new_instance['label'] = strip_tags($new_instance['label']);
            return $new_instance;
        }

        function widget($args, $instance) {
            extract($args);
            global $post;
            $sales_url = get_post_meta($post -> ID, DeMomentSomTresWineAndCheese::OPTION_SALES_URL, true);
            $title = apply_filters('widget_title', $instance['title']);
            $label = $instance['label'];
            if ($sales_url != '') :
                echo $before_widget;
                if ($title) :
                    echo $before_title . $title . $after_title;
                endif;
                echo '<a href="' . $sales_url . '" target="_blank" class="dms3_catalog_sales_url">';
                if ($label) :
                    echo $label;
                else :
                    echo $post -> post_title;
                endif;
                echo '</a>';
                echo $after_widget;
            endif;
        }

    }
