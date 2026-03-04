<?php
/**
 * Image Helper Functions
 * Helper functions for handling product images
 */

/**
 * Get primary image for a product
 * Returns the first image from images array, or single image, or placeholder
 * 
 * @param array $product Product data with 'image' and 'images' fields
 * @return string Image path
 */
function getProductPrimaryImage($product) {
    // Check if product has multiple images
    if (!empty($product['images'])) {
        $images = json_decode($product['images'], true);
        if (is_array($images) && count($images) > 0) {
            return BASE_URL . '/public/' . $images[0]; // Return first image
        }
    }
    
    // Check if product has single image
    if (!empty($product['image'])) {
        return BASE_URL . '/public/' . $product['image'];
    }
    
    // Return placeholder
    return BASE_URL . '/public/assets/images/placeholder-product.svg';
}

/**
 * Get all images for a product
 * Returns array of all product images
 * 
 * @param array $product Product data with 'image' and 'images' fields
 * @return array Array of image paths
 */
function getProductImages($product) {
    $images = [];
    
    // Check if product has multiple images
    if (!empty($product['images'])) {
        $decoded = json_decode($product['images'], true);
        if (is_array($decoded) && count($decoded) > 0) {
            foreach ($decoded as $img) {
                $images[] = BASE_URL . '/public/' . $img;
            }
            return $images;
        }
    }
    
    // Check if product has single image
    if (!empty($product['image'])) {
        $images[] = BASE_URL . '/public/' . $product['image'];
        return $images;
    }
    
    // Return placeholder
    $images[] = BASE_URL . '/public/assets/images/placeholder-product.svg';
    return $images;
}

/**
 * Check if product has multiple images
 * 
 * @param array $product Product data
 * @return bool True if product has more than one image
 */
function hasMultipleImages($product) {
    if (!empty($product['images'])) {
        $images = json_decode($product['images'], true);
        return is_array($images) && count($images) > 1;
    }
    return false;
}

/**
 * Resolve a settings image value to a full URL.
 * Handles both old absolute URLs (http://localhost/...) and new relative paths (/uploads/settings/...).
 *
 * @param string|null $storedValue  Value from site_settings table
 * @param string      $fallback     Fallback image path (relative to BASE_URL/public)
 * @return string Full image URL
 */
function resolveSettingsImage($storedValue, $fallback = '') {
    if (empty($storedValue)) {
        return $fallback ? BASE_URL . '/public/' . ltrim($fallback, '/') : '';
    }
    // Already a full URL (old format) — extract relative part so it works on any host
    if (strpos($storedValue, 'http') === 0) {
        $pos = strpos($storedValue, '/uploads/settings/');
        if ($pos !== false) {
            return BASE_URL . '/public' . substr($storedValue, $pos);
        }
        return $storedValue; // can't parse, return as-is
    }
    // Relative path (new format)
    return BASE_URL . '/public' . $storedValue;
}
?>
