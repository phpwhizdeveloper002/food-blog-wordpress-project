<?php
class WPForms_Views_Services {

	function __construct() {
		add_action( 'admin_menu', array( $this, 'add_page' ) );
		if ( isset( $_GET['page'] ) && ( $_GET['page'] == 'wpf-views-services' ) ) {
			add_filter( 'wpforms_admin_header', '__return_false' );
			add_filter( 'wpforms_admin_flyoutmenu', '__return_false' );
		}
	}

	function add_page() {
		add_submenu_page(
			'edit.php?post_type=wpforms-views',
			__( 'Our Services', 'views-for-wpforms-lite' ),
			__( '<span class="dashicons dashicons-businessman" style="font-size: 16px; margin-right: 5px;"></span>Our Services', 'views-for-wpforms-lite' ),
			'manage_options',
			'wpf-views-services',
			array( $this, 'services_page' )
		);
	}

	function services_page() {
		?>
		<style>
		#wpf-views-services-section {
			margin: 32px;
			font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
		}

		.services-header {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			padding: 40px 30px;
			border-radius: 12px;
			color: white;
			margin-bottom: 30px;
			text-align: center;
		}

		.services-header h1 {
			color: white;
			font-size: 2.5em;
			margin: 0 0 15px 0;
			font-weight: 600;
		}

		.services-header p {
			font-size: 1.2em;
			margin: 0;
			opacity: 0.9;
		}

		.services-grid {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
			gap: 30px;
			margin-bottom: 40px;
		}

		.service-card {
			background: white;
			border: 1px solid #e2e8f0;
			border-radius: 12px;
			padding: 30px;
			box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
			transition: transform 0.3s ease, box-shadow 0.3s ease;
			position: relative;
			overflow: hidden;
		}

		.service-card:hover {
			transform: translateY(-5px);
			box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
		}

		.service-card::before {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			height: 4px;
			background: linear-gradient(90deg, #667eea, #764ba2);
		}

		.service-icon {
			width: 60px;
			height: 60px;
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			margin-bottom: 20px;
			font-size: 24px;
			color: white;
		}

		.service-card h3 {
			color: #2d3748;
			font-size: 1.5em;
			margin: 0 0 15px 0;
			font-weight: 600;
		}

		.service-card p {
			color: #4a5568;
			line-height: 1.6;
			margin-bottom: 20px;
		}

		.service-features {
			list-style: none;
			padding: 0;
			margin: 0 0 25px 0;
		}

		.service-features li {
			color: #4a5568;
			padding: 8px 0;
			position: relative;
			padding-left: 25px;
		}

		.service-features li:before {
			content: "✓";
			position: absolute;
			left: 0;
			color: #48bb78;
			font-weight: bold;
		}

		.service-price {
			font-size: 1.1em;
			font-weight: 600;
			color: #667eea;
			margin-bottom: 20px;
		}

		.cta-section {
			background: #f8fafc;
			padding: 40px;
			border-radius: 12px;
			text-align: center;
			border: 2px solid #e2e8f0;
		}

		.cta-section h2 {
			color: #2d3748;
			font-size: 2em;
			margin: 0 0 15px 0;
		}

		.cta-section p {
			color: #4a5568;
			font-size: 1.1em;
			margin-bottom: 25px;
		}

		.contact-button {
			display: inline-block;
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			color: white;
			padding: 15px 30px;
			border-radius: 8px;
			text-decoration: none;
			font-weight: 600;
			font-size: 1.1em;
			transition: transform 0.3s ease, box-shadow 0.3s ease;
		}

		.contact-button:hover {
			color: white;
			transform: translateY(-2px);
			box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
		}

		.testimonial {
			background: #f7fafc;
			padding: 25px;
			border-radius: 8px;
			border-left: 4px solid #667eea;
			margin: 30px 0;
		}

		.testimonial p {
			font-style: italic;
			margin: 0 0 10px 0;
			color: #4a5568;
		}

		.testimonial .author {
			font-weight: 600;
			color: #2d3748;
		}

		@media (max-width: 768px) {
			.services-grid {
				grid-template-columns: 1fr;
			}

			.services-header h1 {
				font-size: 2em;
			}

			#wpf-views-services-section {
				margin: 20px;
			}
		}
		</style>

		<div id="wpf-views-services-section">
			<div class="services-header">
				<h1>Professional WordPress Development Services</h1>
				<p>Take your WordPress projects to the next level with our expert development services</p>
			</div>

			<div class="services-grid">
				<div class="service-card">
					<div class="service-icon">
						<span class="dashicons dashicons-forms"></span>
					</div>
					<h3>Custom WPForms Plugin Development</h3>
					<p>Need custom functionality for WPForms? We specialize in creating tailored solutions that extend WPForms capabilities to meet your specific business requirements.</p>
					<ul class="service-features">
						<li>Custom form field types</li>
						<li>Advanced form processing</li>
						<li>Third-party integrations</li>
						<li>Custom form layouts & styling</li>
						<li>Advanced validation rules</li>
						<li>Custom notification systems</li>
					</ul>
					<div class="service-price">Starting from $299</div>
				</div>

				<div class="service-card">
					<div class="service-icon">
						<span class="dashicons dashicons-admin-plugins"></span>
					</div>
					<h3>Custom WordPress Plugin Creation</h3>
					<p>Transform your unique ideas into powerful WordPress plugins. We develop custom plugins from scratch, ensuring clean code, security best practices, and seamless WordPress integration.</p>
					<ul class="service-features">
						<li>Custom plugin architecture</li>
						<li>Admin dashboard integration</li>
						<li>Database optimization</li>
						<li>Security implementation</li>
						<li>WordPress coding standards</li>
						<li>Plugin documentation</li>
					</ul>
					<div class="service-price">Starting from $499</div>
				</div>

				<div class="service-card">
					<div class="service-icon">
						<span class="dashicons dashicons-wordpress"></span>
					</div>
					<h3>Complete WordPress Development</h3>
					<p>From theme customization to complex web applications, we provide comprehensive WordPress development services to bring your vision to life.</p>
					<ul class="service-features">
						<li>Theme development & customization</li>
						<li>WooCommerce development</li>
						<li>Performance optimization</li>
						<li>Security hardening</li>
						<li>API integrations</li>
						<li>Maintenance & support</li>
					</ul>
					<div class="service-price">Starting from $199</div>
				</div>
			</div>

			<div class="testimonial">
				<p>"The team at FormViewsWP delivered exactly what we needed. Their expertise in WPForms development saved us months of work and the final product exceeded our expectations."</p>
				<div class="author">- Sarah Johnson, Marketing Director</div>
			</div>

			<div class="cta-section">
				<h2>Ready to Get Started?</h2>
				<p>Let's discuss your project requirements and create a solution that perfectly fits your needs. We're here to help you succeed!</p>
				<a href="https://formviewswp.com/contact" target="_blank" class="contact-button">
					<span class="dashicons dashicons-email-alt" style="margin-right: 8px; font-size: 16px;"></span>
					Contact Us Today
				</a>

				<div style="margin-top: 30px; padding-top: 30px; border-top: 1px solid #e2e8f0;">
					<p style="color: #718096; margin: 0;">
						<strong>Why choose us?</strong> 12+ years of WordPress experience • 800+ successful projects • Ongoing support • Clean, documented code
					</p>
				</div>
			</div>
		</div>
		<?php
	}
}

new WPForms_Views_Services();
