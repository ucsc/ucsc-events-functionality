<?php 
/**
 * Template Name: Venues Template
 * Description: A custom page template for displaying event venues.
 */
get_header(); ?>

<main class="site-main template-venues">
  <h1><?php the_title(); ?></h1>
  <div><?php the_content(); ?></div>
</main>

<?php get_footer(); ?>