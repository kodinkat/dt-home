<?php
/**
 * @var string $magic_link
 * @var string $data
 * @var string $app_url
 * @var string $subpage_url
 * @var WP_User $user
 * @var string $reset_apps
 */
$this->layout( 'layouts/plugin' );
?>

<header id="app-header">
    <my-tooltip>

    </my-tooltip>

    <overlay-trigger placement="right">
        <div slot="trigger">
            <sp-icon-help></sp-icon-help>
        </div>

        <sp-tooltip slot="hover-content" open placement="right" class="spl-text">
            <?php
            // phpcs:ignore
            echo wordwrap( esc_attr( __( "Copy this link and share it with people you are coaching.", "dt_home" ) ), 40, "<br />\n" ); ?>
        </sp-tooltip>
    </overlay-trigger>
    <dt-copy-text value="<?php echo esc_url( $magic_link ); ?>"></dt-copy-text>
</header>

<dt-home-app-grid id="appGrid" app-data='<?php echo esc_attr( htmlspecialchars( $data ) ); ?>'
                  app-url='<?php echo esc_url( $app_url ); ?>'>
    <!-- Add more app icons as needed -->
</dt-home-app-grid>

<div>
    <?php
    // phpcs:ignore
    echo $this->section( 'content' ) ?>
</div>

<?php $this->start( 'footer' ); ?>

<dt-home-footer id="hiddenApps"
                translations='<?php echo wp_json_encode( [
                    "hiddenAppsLabel" => __( "Hidden Apps", 'dt_home' ),
                    "buttonLabel"     => __( "Ok", 'dt_home' )
                ] ) ?>'
                hidden-data='<?php echo esc_attr( htmlspecialchars( $data ) ); ?>'
                app-url-unhide='<?php echo esc_url( $app_url ); ?>'
                reset-apps='<?php echo esc_attr( $reset_apps ); ?>'>
</dt-home-footer>

<?php $this->stop(); ?>
