<?php
function modify_books_archive_query( $query ) {
    if( is_post_type_archive( 'book' ) && !is_admin() && $query->is_main_query() ) {
        // Check if the 'genre' parameter is set in the URL query string and is not empty
        if ( isset( $_GET['genre'] ) && !empty( $_GET['genre']) ) {
            // Sanitize the genre slug to prevent any potential security issues
            $genre_slug = sanitize_text_field( $_GET['genre']);
            // Create a tax query to filter books by the selected genre (instead of hardcoding to 'science-fiction')
            $tax_query = array(
                array(
                    'taxonomy' => 'genre', // 
                    'field'    => 'slug', // 
                    'terms'    => $genre_slug, // The term to filter by is dynamic (the sanitized genre slug)
                ),
            );
            $query->set( 'tax_query', $tax_query );
        }
    }
}
add_action( 'pre_get_posts', 'modify_books_archive_query' );
?>