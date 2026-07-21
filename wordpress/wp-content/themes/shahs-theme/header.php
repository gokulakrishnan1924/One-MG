<?php
/**
 * The header for the SHAHS theme
 *
 * Displays the top black header bar (logo, menu toggle, search, account,
 * cart) and the breadcrumb bar below it.
 *
 * @package Shahs_Theme
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
	<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/custom.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/responsive.css">
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
	<div class="site-header__inner">

		<!-- Logo -->
		<div class="site-header__logo">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php
				$logo_id  = get_theme_mod( 'custom_logo' );
				$logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'full' ) : get_template_directory_uri() . '/assets/images/logo.svg';
				?>
				<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php bloginfo( 'name' ); ?>" class="site-header__logo-img">
			</a>
		</div>

		<!-- Menu toggle -->
		<button type="button" class="site-header__menu-toggle" aria-expanded="false" aria-controls="primary-menu">
			<span class="site-header__menu-icon">
				<span></span><span></span><span></span>
			</span>
			<span class="site-header__menu-label"><?php esc_html_e( 'MENU', 'shahs-theme' ); ?></span>
		</button>

		<!-- Primary navigation (slides out / opens on toggle) -->
		<nav id="primary-menu" class="site-header__nav" aria-label="<?php esc_attr_e( 'Primary Menu', 'shahs-theme' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'site-header__nav-list',
					'fallback_cb'    => false,
				)
			);
			?>
		</nav>

		<!-- Search -->
		<div class="site-header__search">
			<form role="search" method="get" class="site-header__search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<label for="site-search" class="screen-reader-text"><?php esc_html_e( 'Search', 'shahs-theme' ); ?></label>
				<input type="search" id="site-search" class="site-header__search-input" placeholder="<?php esc_attr_e( 'Search', 'shahs-theme' ); ?>" value="<?php echo get_search_query(); ?>" name="s">
				<button type="submit" class="site-header__search-btn" aria-label="<?php esc_attr_e( 'Search', 'shahs-theme' ); ?>">
					<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/>
						<line x1="16.65" y1="16.65" x2="21" y2="21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
					</svg>
				</button>
			</form>
		</div>

		<!-- Account + Cart -->
		<div class="site-header__actions">

			<?php
			// Fall back to the login page if WooCommerce isn't active.
			$account_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url();
			?>
			<a href="<?php echo esc_url( $account_url ); ?>" class="site-header__icon-link" aria-label="<?php esc_attr_e( 'My Account', 'shahs-theme' ); ?>">
				<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="2"/>
					<path d="M4 20c0-4 3.5-6 8-6s8 2 8 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
				</svg>
			</a>

			<?php
			// Fall back to the homepage if WooCommerce isn't active.
			$cart_url   = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/' );
			$cart_count = ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
			?>
			<a href="<?php echo esc_url( $cart_url ); ?>" class="site-header__icon-link site-header__cart" aria-label="<?php esc_attr_e( 'Cart', 'shahs-theme' ); ?>">
				<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M3 4h2l2.4 12.2a2 2 0 0 0 2 1.6h8.2a2 2 0 0 0 2-1.6L21 8H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					<circle cx="10" cy="21" r="1.5" fill="currentColor"/>
					<circle cx="18" cy="21" r="1.5" fill="currentColor"/>
				</svg>
				<?php if ( $cart_count > 0 ) : ?>
					<span class="site-header__cart-count"><?php echo esc_html( $cart_count ); ?></span>
				<?php endif; ?>
			</a>

		</div>

	</div>
	<!-- Breadcrumbs -->
	<div class="site-breadcrumbs">
        <div class="site-breadcrumbs__inner">
            <a href="<?php echo home_url(); ?>">Home</a>
            <span class="site-breadcrumbs__sep">&gt;</span>
            <a href="#">Home Appliances</a>
            <span class="site-breadcrumbs__sep">&gt;</span>
            <span class="site-breadcrumbs__current">
                Air Conditioners
            </span>
        </div>
    </div>
</header>