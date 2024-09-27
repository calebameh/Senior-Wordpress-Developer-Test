<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">

        <!-- Genre Filter Dropdown -->
        <form method="get" id="genre-filter-form">
            <label for="genre">Filter by Genre: </label>
            <select name="genre" id="genre" onchange="this.form.submit()">
                <option value="">All Genres</option>
                <?php
                $genres = get_terms( array(
                    'taxonomy' => 'genre',
                    'hide_empty' => true,
                ));

                foreach ( $genres as $genre ) {
                    $selected = ( isset( $_GET['genre'] ) && $_GET['genre'] == $genre->slug ) ? 'selected' : '';
                    echo '<option value="' . esc_attr( $genre->slug ) . '" ' . $selected . '>' . esc_html( $genre->name ) . '</option>';
                }
                ?>
            </select>
        </form>

        <?php
        // Modify query based on genre filter
        if ( isset( $_GET['genre'] ) && ! empty( $_GET['genre'] ) ) {
            $genre_slug = sanitize_text_field( $_GET['genre'] );

            $args = array(
                'post_type' => 'book',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'genre',
                        'field'    => 'slug',
                        'terms'    => $genre_slug,
                    ),
                ),
            );
        } else {
            $args = array( 'post_type' => 'book' );
        }

        $book_query = new WP_Query( $args );

        if ( $book_query->have_posts() ) : ?>

            <header class="page-header">
                <h1 class="page-title"><?php post_type_archive_title(); ?></h1>
            </header><!-- .page-header -->

            <div class="book-archive-list">
                <?php
                // Start the loop
                while ( $book_query->have_posts() ) :
                    $book_query->the_post(); ?>

                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                        <header class="entry-header">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <div class="book-thumbnail">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail( 'medium' ); ?>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <h2 class="entry-title">
                                <a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
                            </h2>
                        </header><!-- .entry-header -->

                        <div class="entry-content">
                            <?php the_excerpt(); ?>
                        </div><!-- .entry-content -->

                        <footer class="entry-footer">
                            <!-- Display the Author Name if it has a value -->
                            <?php 
                            $author_name = get_post_meta( get_the_ID(), '_book_author_name', true ); // Retrieve the author name
                            if ( !empty( $author_name ) && !is_wp_error( $author_name ) ) : 
                                if ( is_array( $author_name ) ) {
                                    $author_name = implode(', ', $author_name);
                                }
                            ?>
                                <div class="book-author">
                                    <strong>Author:</strong> 
                                    <?php echo esc_html( $author_name ); ?>
                                </div> 
                            <?php endif; ?>

                        </footer><!-- .entry-footer -->
                    </article><!-- #post-<?php the_ID(); ?> -->

                <?php endwhile; ?>
            </div><!-- .book-archive-list -->

            <?php
            // Pagination
            echo paginate_links();

        else :

            get_template_part( 'template-parts/content', 'none' );

        endif; ?>
    </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>