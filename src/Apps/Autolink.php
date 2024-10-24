<?php

namespace DT\Home\Apps;

use function DT\Home\is_plugin_active;

class Autolink extends App
{

    public function config(): array
    {
        return [
            "name" => "Autolink",
            "type" => "Web View",
            'creation_type' => 'code',
            "icon" => "/wp-content/themes/disciple-tools-theme/dt-assets/images/link.svg",
            'url' => '/autolink',
            "sort" => 0,
            "slug" => "disciple-tools-autolink",
            "is_hidden" => false,
            'open_in_new_tab' => false
        ];
    }

    public function authorized(): bool
    {
        if ( !is_plugin_active( 'disciple-tools-autolink/disciple-tools-autolink.php' ) ) {
            return false;
        }

        return true;
    }
}
