<?php

namespace DT\Home\Controllers\Admin;

use DT\Home\GuzzleHttp\Psr7\ServerRequest as Request;
use DT\Home\Psr\Http\Message\ResponseInterface;
use function DT\Home\extract_request_input;
use function DT\Home\get_plugin_option;
use function DT\Home\redirect;
use function DT\Home\set_plugin_option;
use function DT\Home\view;

class TrainingSettingsController {

    /**
     * Show the training settings page
     *
     * @return ResponseInterface
     */
	public function show() {

		$tab        = "training";
		$link       = 'admin.php?page=dt_home&tab=';
		$page_title = "Home Settings";

		$data = $this->get_all_trainings_data();

		return view( "settings/training", compact( 'tab', 'link', 'page_title', 'data' ) );
	}

    /**
     * Retrieves all training data from the plugin option and sorts it based on the 'sort' key.
     *
     * @return array The sorted training data.
     */
    protected function get_all_trainings_data() {
		// Get the apps array from the option
		$trainings_array = get_plugin_option( 'trainings' );

		// Sort the array based on the 'sort' key
		usort( $trainings_array, function ( $a, $b ) {
			return ( !empty( $a['sort'] ) && !empty( $b['sort'] ) ) ? ( $a['sort'] - $b['sort'] ) : -1;
		} );

		return $trainings_array;
	}


    /**
     * Creates a new training and returns a view with the necessary data.
     *
     * @return ResponseInterface The view with the necessary data.
     */
    public function create() {
		$tab        = "training";
		$link       = 'admin.php?page=dt_home&tab=';
		$page_title = "Home Settings";

		return view( "settings/training/create", compact( 'tab', 'link', 'page_title' ) );
	}

    /**
     * Edit method for the TrainingController.
     *
     * Retrieves the existing training data based on the provided ID and displays the edit page.
     * If the ID is null or no existing data is found, it redirects back to the training tab in the admin panel.
     *
     * @param Request $request The request object.
     * @param array $params The parameters (including the ID) passed to the route.
     *
     * @return ResponseInterface The response object.
     */
    public function edit( Request $request, $params ) {
        $id = $params['id'] ?? null;
        $edit_id = isset( $id ) ? intval( $id ) : 0;

        if ( !$edit_id ) {
            return redirect( 'admin.php?page=dt_home&tab=training' );
        }

        // Retrieve the existing data based on $edit_id
        $existing_data = $this->get_data_by_id( $edit_id );

        if ( !$existing_data ) {
            return redirect( 'admin.php?page=dt_home&tab=training' );
        }

        $tab        = "training";
        $link       = 'admin.php?page=dt_home&tab=';
        $page_title = "Home Settings";

        return view( "settings/training/edit", compact( 'existing_data', 'link', 'tab', 'page_title' ) );
    }

    /**
     * Updates a training entry based on the input data.
     *
     * @param Request $request The HTTP request object.
     * @return ResponseInterface The HTTP response object.
     */
    public function update( Request $request, $params ) {
        $input       = extract_request_input( $request );
        $name        = sanitize_text_field( $input['name'] ?? '' );
        $embed_video = sanitize_text_field( $input['embed_video'] ?? '' );
        $anchor      = sanitize_text_field( $input['anchor'] ?? '' );
        $sort        = intval( $input['sort'] ?? 0 );
        $edit_id     = intval( $params['id'] ?? 0 );
        $trainings_array = get_plugin_option( 'trainings' );

        // Find and update the app in the array
        foreach ( $trainings_array as $key => $training ) {
            if ( $training['id'] == $edit_id ) {
                $trainings_array[ $key ] = [
                    'id'          => $edit_id,
                    'name'        => $name,
                    'embed_video' => $embed_video,
                    'anchor'      => $anchor,
                    'sort'        => $sort,
                ];
                break; // Stop the loop once the app is found and updated
            }
        }

        // Save the updated array back to the option
        set_plugin_option( 'trainings', $trainings_array );

        return redirect( 'admin.php?page=dt_home&tab=training&updated=true' );
    }


    /**
     * Stores a new training record in the database.
     *
     * @param Request $request The HTTP request.
     *
     * @return ResponseInterface
     */
    public function store( Request $request ) {
		// Retrieve form data
        $input       = extract_request_input( $request );
		$name        = sanitize_text_field( $input['name'] ?? '' );
		$embed_video = sanitize_text_field( $input['embed_video'] ?? '' );
		$anchor      = sanitize_text_field( $input['anchor'] ?? '' );
		$sort        = intval( $input['sort'] ?? 0 );

		// Prepare the data to be stored
		$training_data = [
			'name'        => $name,
			'embed_video' => $embed_video,
			'anchor'      => $anchor,
			'sort'        => $sort,
		];

		// Get the existing apps array
		$trainings_array = get_option( 'dt_home_trainings', [] );

		// Generate a unique ID for the new app
		$next_id = 1;
		foreach ( $trainings_array as $training ) {
			if ( isset( $training['id'] ) && $training['id'] >= $next_id ) {
				$next_id = $training['id'] + 1;
			}
		}

		$training_data['id'] = $next_id; // Add the ID to the new app data

		// Append new app data to the array
		$trainings_array[] = $training_data;

		// Save the updated array back to the option
		set_plugin_option( 'trainings', $trainings_array );

		return redirect( 'admin.php?page=dt_home&tab=training&updated=true' );
	}


    /**
     * Retrieves the training data with the specified ID from the plugin option array.
     *
     * @param int $id The ID of the training data to retrieve.
     *
     * @return array|null The training data with the specified ID, or null if not found.
     */
    public function get_data_by_id( $id ) {
		$trainings_array = get_option( 'dt_home_trainings', [] );
		foreach ( $trainings_array as $training ) {
			if ( isset( $training['id'] ) && $training['id'] == $id ) {
				return $training;
			}
		}

		return null; // Return null if no app is found with the given ID
	}

    /**
     * Deletes a training from the plugin option based on the specified ID.
     *
     * @param Request $request The server request object.
     * @param array $params An array of parameters passed to the method.
     * @return ResponseInterface The redirect response.
     */
    public function delete( Request $request, $params ) {
        $id = $params['id'] ?? null;

        if ( !$id ) {
            return redirect( 'admin.php?page=dt_home&tab=training' );
        }

		// Retrieve the existing array of trainings
		$trainings_array = get_plugin_option( 'trainings' );

		// Find the app with the specified ID and remove it from the array
		foreach ( $trainings_array as $key => $training ) {
			if ( isset( $training['id'] ) && $training['id'] == $id ) {
				unset( $trainings_array[ $key ] ); // Remove the app from the array
				break; // Exit the loop once the app is found and removed
			}
		}

		// Save the updated array back to the option
		set_plugin_option( 'trainings', $trainings_array );

		// Redirect to the page with a success message
		return redirect( 'admin.php?page=dt_home&tab=training&updated=true' );
	}

    /**
     * Retrieves the training data from the plugin option, finds the specified training by its ID, and moves it up in the sort order.
     * If successful, it saves the updated array to the plugin option and redirects to the training page.
     * If the specified ID is not found or the training is already at the top, it redirects back to the training page without making any changes.
     *
     * @param Request $request The current server request object.
     * @param array $params An array of parameters passed to the method.
     * @return ResponseInterface A redirect response to the training page with an "updated" parameter set to "true".
     */
    public function up( Request $request, $params ) {
        $id = $params['id'] ?? null;

        if ( !$id ) {
            return redirect( 'admin.php?page=dt_home&tab=training' );
        }

		// Retrieve the existing array of apps
		$trainings_array = get_plugin_option( 'trainings' );

		// Find the index of the app and its current sort value
		$current_index = null;
		$current_sort  = null;
		foreach ( $trainings_array as $key => $training ) {
			if ( $training['id'] == $id ) {
				$current_index = $key;
				$current_sort  = $training['sort'];
				break;
			}
		}

		// Only proceed if the app was found and it's not already at the top
		if ( $current_index !== null && $current_sort > 1 ) {
			// Adjust the sort values
			foreach ( $trainings_array as $key => &$training ) {
				if ( $training['sort'] == $current_sort - 1 ) {
					// Increment the sort value of the app that's currently one position above
					$training['sort']++;
				}
			}
			// Decrement the sort value of the current app
			$trainings_array[ $current_index ]['sort']--;

			// Re-sort the array
			usort( $trainings_array, function ( $a, $b ) {
				return $a['sort'] - $b['sort'];
			} );

			// Save the updated array back to the option
			set_plugin_option( 'trainings', $trainings_array );
		}

		return redirect( 'admin.php?page=dt_home&tab=training&updated=true' );
	}

    /**
     * Handles the request to move the training down in the list.
     *
     * @param Request $request The HTTP Request object.
     * @param array $params The request parameters.
     *
     * @return ResponseInterface The redirect response to the admin page.
     */
    public function down( Request $request, $params ) {
        $id = $params['id'] ?? null;

		// Retrieve the existing array of apps
		$trainings_array = get_plugin_option( 'trainings' );

		// Find the index of the app and its current sort value
		$current_index = null;
		$current_sort  = null;
		foreach ( $trainings_array as $key => $app ) {
			if ( $app['id'] == $id ) {
				$current_index = $key;
				$current_sort  = $app['sort'];
				break;
			}
		}

		// Determine the maximum sort value
		$max_sort = count( $trainings_array );

		// Only proceed if the app was found and it's not already at the bottom
		if ( $current_index !== null && $current_sort < $max_sort ) {
			// Adjust the sort values
			foreach ( $trainings_array as $key => &$app ) {
				if ( $app['sort'] == $current_sort + 1 ) {
					// Decrement the sort value of the app that's currently one position below
					$app['sort']--;
				}
			}
			// Increment the sort value of the current app
			$trainings_array[ $current_index ]['sort']++;

			// Re-sort the array
			usort( $trainings_array, function ( $a, $b ) {
				return $a['sort'] - $b['sort'];
			} );

			// Save the updated array back to the option
			set_plugin_option( 'trainings', $trainings_array );
		}

		return redirect( 'admin.php?page=dt_home&tab=training&updated=true' );
	}
}
