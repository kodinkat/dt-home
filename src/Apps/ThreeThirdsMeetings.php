<?php

namespace DT\Home\Apps;

use function DT\Home\is_plugin_active;

class ThreeThirdsMeetings extends App {
	public function config(): array {
		return [
			"name" => "3/3 Meetings",
		    "type" => "Web View",
            'creation_type' => 'code',
            "icon" => "/wp-content/themes/disciple-tools-theme/dt-assets/images/calendar-clock.svg",
			'url' => '/3/3',
		    "sort" => 0,
		    "slug" => "three-thirds-meetings",
		    "is_hidden" => false,
            'open_in_new_tab' => false
		];
	}

	public function authorized(): bool {
		if ( !is_plugin_active( 'disciple-tools-three-thirds/disciple-tools-three-thirds.php' ) ) {
			return false;
		}

		return true;
	}
}
