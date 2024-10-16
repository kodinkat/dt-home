<?php

use function DT\Home\magic_url;

$user = wp_get_current_user();
$home = DT\Home\route_url();
$dashboard = '/';
$menu_items = [];

// Adding default menu items
$menu_items[] = [ 'label' => __( 'Apps', 'dt_home' ), 'href' => $home ];
$menu_items[] = [ 'label' => __( 'Training', 'dt_home' ), 'href' => magic_url( 'training' ) ];
//if ( get_option( 'dt_home_require_login', true ) ) {
$menu_items[] = [ 'label' => __( 'Log Out', 'dt_home' ), 'href' => magic_url( 'logout' ) ];
//}


$menu_items_json = wp_json_encode( $menu_items );
?>

<sp-theme
    color="light"
    scale="medium"
>
    <div class="plugin cloak">
        <div class="plugin__main">
            <div class="container non-selectable">

                <dt-home-menu menuItems='<?php echo esc_js( $menu_items_json ); ?>'></dt-home-menu>

                <?php
                // phpcs:ignore
                echo $this->section('content') ?>
            </div>
        </div>

        <div class="plugin__footer">
            <div class="container">
                <?php
                // phpcs:ignore
                echo $this->section('footer') ?>
            </div>
        </div>
    </div>
</sp-theme>
