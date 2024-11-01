<?php
/**
 * Viral Marketing Influenciadores
 *
 * @package           ViralMarketingInfluenciadores
 * @author            Viral
 * @copyright         2024 Viral.com.br
 *
 * @wordpress-plugin
 * Plugin Name:       Viral Marketing Influenciadores
 * Plugin URI:        https://wordpress.org/plugins/viral-marketing-influenciador
 * Description:       Venda mais com influenciadores! Conecte sua loja à Viral.com.br, gere cupons para influenciadores e acompanhe suas vendas.
 * Version:           1.0.0
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Author:            Viral
 * Author URI:        https://viral.com.br
 * Text Domain:       viral-marketing-influenciador
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Requires Plugins:  woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add an option upon activation to read in later when redirecting.
 */
function viral_plugin_activate(): void
{
    if ( viral_can_redirect_on_activation() ) {
        add_option( 'viral_plugin_do_activation_redirect', sanitize_text_field( __FILE__ ) );
    }
}

/**
 * Determine if a user can be redirected or not.
 *
 * @return true if the user can be redirected. false if not.
 */
function viral_can_redirect_on_activation(): bool
{
    // If plugin is activated in network admin options, skip redirect.
    if ( is_network_admin() ) {
        return false;
    }

    // Skip redirect if WP_DEBUG is enabled.
    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        return false;
    }

    // Determine if multi-activation is enabled.
    $maybe_multi = filter_input( INPUT_GET, 'activate-multi', FILTER_VALIDATE_BOOLEAN );
    if ( $maybe_multi ) {
        return false;
    }

    // All is well. Can redirect.
    return true;
}

/**
 * Redirect a user to start integration after activation.
 */
function viral_plugin_activate_redirect(): void
{
    if ( viral_can_redirect_on_activation() && is_admin() ) {
        // Read in option value.
        if ( __FILE__ === get_option( 'viral_plugin_do_activation_redirect' ) ) {
            // Delete option value so no more redirects.
            delete_option( 'viral_plugin_do_activation_redirect' );

            $shopUrl = get_site_url();

            // Get redirect URL.
            $redirect_url = "https://viral.com.br/oauth/woocommerce?shop={$shopUrl}";
            wp_redirect($redirect_url);
            exit;
        }
    }
}

register_activation_hook( __FILE__, 'viral_plugin_activate' );
add_action( 'admin_init', 'viral_plugin_activate_redirect' );
