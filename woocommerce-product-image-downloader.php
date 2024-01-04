<?php
/*
Plugin Name: Product Image Downloader
Description: A simple plugin to download all WooCommerce product images.
Version: 1.0
Author: Sameet
*/


function add_menu_item() {
    add_menu_page('Image Downloader', 'Image Downloader', 'manage_options', 'image-downloader', 'download_images');
}
add_action('admin_menu', 'add_menu_item');

function download_images() {
    
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    if (is_plugin_active('woocommerce/woocommerce.php')) {
        require_once(ABSPATH . 'wp-load.php');
        global $woocommerce;

     
        $downloadFolder = ABSPATH . 'wp-content/uploads/downloaded_images/';

       
        if (!file_exists($downloadFolder)) {
            mkdir($downloadFolder, 0755, true);
        }

        
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
        );

        $products = new WP_Query($args);

        if ($products->have_posts()) {
            while ($products->have_posts()) {
                $products->the_post();
                $product_id = get_the_ID();
                $product = wc_get_product($product_id);

                
                $product_gallery_ids = $product->get_gallery_image_ids();
                foreach ($product_gallery_ids as $image_id) {
                    $image_path = get_attached_file($image_id);
                    copy($image_path, $downloadFolder . basename($image_path));
                }

               
                if ($product->is_type('variable')) {
                    
                    $variations = $product->get_available_variations();
                    foreach ($variations as $variation) {
                        $variation_image_id = $variation['image_id'];
                        $variation_image_path = get_attached_file($variation_image_id);
                        copy($variation_image_path, $downloadFolder . basename($variation_image_path));
                    }
                }
            }

            echo 'All product images downloaded successfully!';
        } else {
            echo 'No products found.';
        }
    } else {
        echo 'WooCommerce plugin is not active.';
    }
}
