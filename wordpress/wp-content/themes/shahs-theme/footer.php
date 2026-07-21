<?php
/**
 * The footer for the SHAHS theme
 *
 * Displays the newsletter/social block, stores list, customer care
 * number, and products list.
 *
 * @package Shahs_Theme
 */

// Store locations — list of [ name, phone ] pairs.
$shahs_stores = array(
	array( 'Adyar', '91762 02397' ),
	array( 'Ambattur', '98415 07039' ),
	array( 'Anna Nagar', '99400 84927' ),
	array( 'Chrompet', '91766 53177' ),
	array( 'Mount Road', '91762 06973' ),
	array( 'Mylapore', '98841 96166' ),
	array( 'Navalur', '98849 33901' ),
	array( 'OMR', '99402 88561' ),
	array( 'Porur', '97898 97539' ),
	array( 'T Nagar', '98840 00257' ),
	array( 'Valasaravakkam', '98845 90433' ),
	array( 'Velachery', '98841 32910' ),
);

// Split the stores into two visual columns of six.
$shahs_store_columns = array_chunk( $shahs_stores, 6 );

// Product categories — list of [ name, url ] pairs.
$shahs_products = array(
	array( 'Televisions', '#' ),
	array( 'Air Conditioner', '#' ),
	array( 'Refrigerator', '#' ),
	array( 'Washing Machine', '#' ),
	array( 'Air Cooler', '#' ),
	array( 'Mobile', '#' ),
	array( 'Home Theater', '#' ),
	array( 'Microwave Oven', '#' ),
	array( 'Juicer Mixer Grinders', '#' ),
	array( 'Gas Stove', '#' ),
	array( 'Rice Cookers', '#' ),
	array( 'Wet Grinders', '#' ),
);

$shahs_product_columns = array_chunk( $shahs_products, 9 );
?>

	<footer class="site-footer">
		<div class="site-footer__inner">

			<!-- Connect with us -->
			<div class="site-footer__col site-footer__connect">
				<h3 class="site-footer__heading"><?php esc_html_e( 'Connect with us', 'shahs-theme' ); ?></h3>

				<form class="site-footer__newsletter" method="post" action="">
					<label for="footer-email" class="screen-reader-text"><?php esc_html_e( 'Enter Email ID', 'shahs-theme' ); ?></label>
					<input type="email" id="footer-email" name="footer_email" class="site-footer__newsletter-input" placeholder="<?php esc_attr_e( 'Enter Email ID', 'shahs-theme' ); ?>" required>
					<button type="submit" class="site-footer__newsletter-btn" aria-label="<?php esc_attr_e( 'Subscribe', 'shahs-theme' ); ?>">
						<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<line x1="4" y1="12" x2="20" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
							<polyline points="13 5 20 12 13 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
						</svg>
					</button>
				</form>

				<div class="site-footer__social">
					<a href="<?php echo esc_url( get_theme_mod( 'shahs_facebook_url', '#' ) ); ?>" class="site-footer__social-icon" aria-label="Facebook">
						<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M14 9h2V6h-2c-1.66 0-3 1.34-3 3v2H9v3h2v6h3v-6h2.2l.8-3H14V9z" fill="currentColor"/>
						</svg>
					</a>
					<a href="<?php echo esc_url( get_theme_mod( 'shahs_instagram_url', '#' ) ); ?>" class="site-footer__social-icon" aria-label="Instagram">
						<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<rect x="4" y="4" width="16" height="16" rx="4" stroke="currentColor" stroke-width="2"/>
							<circle cx="12" cy="12" r="3.5" stroke="currentColor" stroke-width="2"/>
							<circle cx="16.2" cy="7.8" r="1" fill="currentColor"/>
						</svg>
					</a>
					<a href="<?php echo esc_url( get_theme_mod( 'shahs_youtube_url', '#' ) ); ?>" class="site-footer__social-icon" aria-label="YouTube">
						<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<rect x="3" y="6" width="18" height="12" rx="3" stroke="currentColor" stroke-width="2"/>
							<polygon points="10.5 9.5 15 12 10.5 14.5" fill="currentColor"/>
						</svg>
					</a>
				</div>
			</div>

			<!-- Stores -->
			<div class="site-footer__col site-footer__stores">
				<h3 class="site-footer__heading"><?php esc_html_e( 'Stores', 'shahs-theme' ); ?></h3>

				<div class="site-footer__stores-columns">
					<?php foreach ( $shahs_store_columns as $column ) : ?>
						<ul class="site-footer__stores-list">
							<?php foreach ( $column as $store ) : ?>
								<li>
									<span class="site-footer__store-name"><?php echo esc_html( $store[0] ); ?></span>
									<span class="site-footer__store-sep">|</span>
									<span class="site-footer__store-phone"><?php echo esc_html( $store[1] ); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endforeach; ?>
				</div>

				<div class="site-footer__customer-care">
					<h3 class="site-footer__heading"><?php esc_html_e( 'Customer Care', 'shahs-theme' ); ?></h3>
					<p class="site-footer__care-number">
						<a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', '78240 10000' ) ); ?>">78240 10000</a>
					</p>
				</div>
			</div>

			<!-- Products -->
			<div class="site-footer__col site-footer__products">
				<h3 class="site-footer__heading"><?php esc_html_e( 'Products', 'shahs-theme' ); ?></h3>

				<div class="site-footer__products-columns">
					<?php foreach ( $shahs_product_columns as $column ) : ?>
						<ul class="site-footer__products-list">
							<?php foreach ( $column as $product ) : ?>
								<li><a href="<?php echo esc_url( $product[1] ); ?>"><?php echo esc_html( $product[0] ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					<?php endforeach; ?>
				</div>
			</div>

		</div>

		<div class="site-footer__bottom">
			<div class="site-footer__bottom-inner">
				<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'shahs-theme' ); ?></p>
			</div>
		</div>
	</footer>

<?php wp_footer(); ?>
</body>
</html>