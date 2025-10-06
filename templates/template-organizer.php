<?php 
/**
 * Template Name: Organizer Template
 * Description: A custom page template for displaying event organizers.
 */
get_header(); ?>

<main class="site-main template-organizer">
  <h1><?php the_title(); ?></h1>
  <div><?php the_content(); ?></div>
</main>

<?php get_footer(); ?>