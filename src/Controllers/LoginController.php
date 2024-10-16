<?php

namespace DT\Home\Controllers;

use DT\Home\CodeZone\WPSupport\Router\ServerRequestFactory;
use DT\Home\GuzzleHttp\Psr7\ServerRequest as Request;
use DT\Home\Psr\Http\Message\ResponseInterface;
use function DT\Home\extract_request_input;
use function DT\Home\redirect;
use function DT\Home\route_url;
use function DT\Home\template;
use function DT\Home\plugin_url;

/**
 * Class LoginController
 *
 * This class handles user login and authentication.
 */
class LoginController {

    /**
     * Display the login form.
     *
     * @param Request $request The request object.
     * @return ResponseInterface The response object containing the login form template.
     */
    public function show( Request $request ) {
        $params       = extract_request_input( $request );
        $register_url = route_url( 'register' );
        $form_action  = route_url( 'login' );
        $username     = sanitize_text_field( $params['username'] ?? '' );
        $password     = sanitize_text_field( $params['password'] ?? '' );
        $error        = sanitize_text_field( $params['error'] ?? '' );
        $logo_path    = plugin_url( 'resources/img/logo-color.png' );
        $reset_url    = wp_lostpassword_url( plugin_url() );

        return template( 'auth/login', [
            'register_url' => $register_url,
            'form_action'  => $form_action,
            'username'     => $username,
            'password'     => $password,
            'logo_path'    => $logo_path,
            'reset_url'    => $reset_url,
            'error'        => $error
        ] );
    }

    /**
     * Process the login form submission.
     *
     * @param Request $request The request object.
     * @return ResponseInterface The response object based on the login result.
     */
	public function process( Request $request ) {
		global $errors;

		$params = extract_request_input( $request );
		$user = wp_authenticate( $params['username'] ?? '', $params['password'] ?? '' );

		if ( is_wp_error( $user ) ) {
			//phpcs:ignore
			$errors = $user;
			$error  = $errors->get_error_message();
			$error  = apply_filters( 'login_errors', $error );

			//If the error links to lost password, inject the 3/3rds redirect
			$error = str_replace( '?action=lostpassword', '?action=lostpassword?&redirect_to=/', $error );

			return $this->show_error( $error );
		}

		wp_set_auth_cookie( $user->ID );

		if ( ! $user ) {
			return $this->show_error( __( 'An unexpected error has occurred.', 'dt_home' ) );
		}

		wp_set_current_user( $user->ID );

		return redirect( route_url() );
	}

    /**
     * Logout the user.
     *
     * @return ResponseInterface The response object redirecting to the login page.
     */
	public function logout() {
		wp_logout();

		return redirect( route_url( 'login' ) );
	}

    /**
     * Show the login page with an error.
     *
     * @param string $error The error message.
     * @param array $params Additional parameters for the request.
     * @param string $method The HTTP method for the request.
     * @param string $endpoint The endpoint to send the request to.
     * @param array $headers The headers for the request.
     * @return ResponseInterface The response of the request.
     */
    private function show_error( $error, $params = [], $method = "GET", $endpoint = "", $headers = [
        'Content-Type' => 'application/HTML',
    ]): ResponseInterface {
        $params = array_merge( $params, [ 'error' => $error ] );
        if ( ! empty( $endpoint ) ) {
            $endpoint = route_url( 'login' );
        }
        return $this->show( ServerRequestFactory::request( $method, $endpoint, $params, $headers ) );
    }
}
