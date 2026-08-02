<?php
/**
 * Main Index Template
 */

get_header();

if ( have_posts() ) :

    while ( have_posts() ) :

        the_post();

        if ( is_singular() ) {

            get_template_part(
                'template-parts/content',
                get_post_type()
            );

        } else {

            get_template_part(
                'template-parts/content',
                get_post_type()
            );

        }

    endwhile;

else :

    get_template_part(
        'template-parts/content',
        'none'
    );

endif;

get_footer();