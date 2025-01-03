<?php

/**
 * Save meta for product.
 *
 * @param int $post_id Post ID.
 * @param object $post Post object.
 *
 * @since 2.1.0
 */
do_action( "woocommerce_process_{$post_type}_meta", $post_id, $post );

/**
 * Save meta for product.
 *
 * @param int $post_id Post ID.
 * @param object $post Post object.
 *
 * @since 2.1.0
 */
do_action( "woocommerce_process_product_meta", $post_id, $post );

