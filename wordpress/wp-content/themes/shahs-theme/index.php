<?php get_header(); ?>

<div class="container">

<h1>Welcome to Shahs Theme</h1>

<?php

if(have_posts()):

while(have_posts()):

the_post();

the_content();

endwhile;

endif;

?>

</div>

<?php get_footer(); ?>