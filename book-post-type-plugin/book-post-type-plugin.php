<?php
/*
Plugin Name: Book Post Type Plugin
Description: A custom plugin to manage a Book post type, genre taxonomy, and book details.
Version: 1.0
Author: Caleb Ameh
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


class BookPostTypePlugin {

    public function __construct() {
        // Register the custom post type and taxonomy
        add_action( 'init', array( $this, 'register_book_post_type' ) );
        add_action( 'init', array( $this, 'register_genre_taxonomy' ) );

        // Add custom meta box
        add_action( 'add_meta_boxes', array( $this, 'add_book_meta_box' ) );
        add_action( 'save_post', array( $this, 'save_book_meta' ) );

        // Display author name on the front end
        add_filter( 'the_content', array( $this, 'display_author_name' ) );

        // Register archive template
        add_filter( 'archive_template', array( $this, 'load_custom_archive_template' ) );
       
    }

    // Register Book Custom Post Type
    public function register_book_post_type() {
        $labels = array(
            'name'               => _x( 'Books', 'post type general name' ),
            'singular_name'      => _x( 'Book', 'post type singular name' ),
            'menu_name'          => _x( 'Books', 'admin menu' ),
            'name_admin_bar'     => _x( 'Book', 'add new on admin bar' ),
            'add_new'            => _x( 'Add New', 'book' ),
            'add_new_item'       => __( 'Add New Book' ),
            'new_item'           => __( 'New Book' ),
            'edit_item'          => __( 'Edit Book' ),
            'view_item'          => __( 'View Book' ),
            'all_items'          => __( 'All Books' ),
            'search_items'       => __( 'Search Books' ),
            'not_found'          => __( 'No books found.' ),
            'not_found_in_trash' => __( 'No books found in Trash.' )
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'books' ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'supports'           => array( 'title', 'editor', 'thumbnail' ),
            'menu_icon'          => 'dashicons-book-alt',
            'show_in_rest'       => true, // Support Gutenberg
        );

        register_post_type( 'book', $args );
    }

    // Register Genre Taxonomy
    public function register_genre_taxonomy() {
        $labels = array(
            'name'              => _x( 'Genres', 'taxonomy general name' ),
            'singular_name'     => _x( 'Genre', 'taxonomy singular name' ),
            'search_items'      => __( 'Search Genres' ),
            'all_items'         => __( 'All Genres' ),
            'edit_item'         => __( 'Edit Genre' ),
            'update_item'       => __( 'Update Genre' ),
            'add_new_item'      => __( 'Add New Genre' ),
            'new_item_name'     => __( 'New Genre Name' ),
            'menu_name'         => __( 'Genres' ),
        );

        $args = array(
            'hierarchical'      => false, // Non-hierarchical like tags
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'genre' ),
            'show_in_rest'      => true,
        );

        register_taxonomy( 'genre', 'book', $args );
    }

    // Add Meta Box for "Book Details"
    public function add_book_meta_box() {
        add_meta_box(
            'book_details',
            __( 'Book Details', 'textdomain' ),
            array( $this, 'render_book_meta_box' ),
            'book',
            'side',
            'default'
        );
    }

    // Render the Meta Box HTML
    public function render_book_meta_box( $post ) {
        // Retrieve current author name based on post ID
        $author_name = get_post_meta( $post->ID, '_book_author_name', true );

        ?>
        <label for="book_author_name"><?php _e( 'Author Name:', 'textdomain' ); ?></label>
        <input type="text" id="book_author_name" name="book_author_name" value="<?php echo esc_attr( $author_name ); ?>" style="width:100%;" />
        <?php
    }

    // Save Meta Box Data
    public function save_book_meta( $post_id ) {
        // Check if the field is set
        if ( isset( $_POST['book_author_name'] ) ) {
            update_post_meta( $post_id, '_book_author_name', sanitize_text_field( $_POST['book_author_name'] ) );
        }
    }

    // Display Author Name Below Book Content on the Frontend
    public function display_author_name( $content ) {
        if ( is_singular( 'book' ) ) {
            $author_name = get_post_meta( get_the_ID(), '_book_author_name', true );
            if ( $author_name ) {
                $content .= '<p><strong>Author:</strong> ' . esc_html( $author_name ) . '</p>';
            }
        }
        return $content;
    }

    // Load Custom Archive Template for "Book" Post Type
    public function load_custom_archive_template( $archive_template ) {
        if ( is_post_type_archive( 'book' ) ) {
            $archive_template = plugin_dir_path( __FILE__ ) . 'templates/archive-book.php';
        }
        return $archive_template;
    }

}

// Instantiate the plugin class
new BookPostTypePlugin();