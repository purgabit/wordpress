<?php
/**
 * Plugin Name:       Product Multi Currency
 * Plugin URI:        https://github.com/purgabit/wordpress/
 * Description:       WooCoommerce Product Price Multi Currency
 * Version:           1.0.0
 * Author:            Romanenko Roman aka PurgaBit
 * Author URI:        https://t.me/MyNameTheF
 */

// How to use:
// 0. input valid usd\eur rate (strings 21-22)
// 1. add tag of currency  to product (only eur/usd at now)
// 2. input correct price to product
// 3. save the product
//
// what can be better?
// auto currency rate
// more currency

if(!defined("rate_usd")) define("rate_usd", 23); // CHANGE THIS VALUE
if(!defined("rate_eur")) define("rate_eur", 29); // CHANGE IT ALSO

function get_price_multiplier($product_id){

	// get current currency rates
	// check product_tags
	// return tag rate

	if (has_term('usd', 'product_tag', $product_id)) {
		return rate_usd;
	}
	if(has_term('eur', 'product_tag', $product_id)){
		return rate_eur;
	}
	return 1;
}

// Simple, grouped and external products
add_filter('woocommerce_product_get_price', 'purgabit_product_price_fix', 99, 2 );
add_filter('woocommerce_product_get_regular_price', 'purgabit_product_price_fix', 99, 2 );

// Variations
add_filter('woocommerce_product_variation_get_regular_price', 'purgabit_product_price_fix', 99, 2 );
add_filter('woocommerce_product_variation_get_price', 'purgabit_product_price_fix', 99, 2 );
function purgabit_product_price_fix( $price, $product ) {
    return (float) $price * get_price_multiplier($product->id);
}

// Variable (price range)
add_filter('woocommerce_variation_prices_price', 'purgabit_variation_product_price_fix', 99, 3 );
add_filter('woocommerce_variation_prices_regular_price', 'purgabit_variation_product_price_fix', 99, 3 );
function purgabit_variation_product_price_fix( $price, $variation, $product ) {
    // Delete product cached price  (if needed)
    // wc_delete_product_transients($variation->get_id());

    return (float) $price * get_price_multiplier($product->id);
}

// Handling price caching (see explanations at the end)
add_filter( 'woocommerce_get_variation_prices_hash', 'purgabit_cache_fix', 99, 1 );
function purgabit_cache_fix( $hash ) {
    $hash[] = get_price_multiplier($product->id);
    return $hash;
}
