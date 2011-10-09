<?php

/**
 * Copyright 2011 Facebook, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

// localhost ?
if (in_array(@$_SERVER['REMOTE_ADDR'], array(
    '127.0.0.1',
    '::1',
))) {
	define('FB_APP_ID','171883476226709');
	define('FB_APP_SECRET','2b7123f2d917d3b0fb4f94c149661fcd');

}else{
	define('FB_APP_ID','194718703877738');
	define('FB_APP_SECRET','5cf1a93366a4738ad2130ea605e3e446');
}

require __DIR__ . '/../src/facebook.php';

// Create our Application instance (replace this with your appId and secret).
$facebook = new Facebook(array(
  'appId'  => FB_APP_ID,
  'secret' => FB_APP_SECRET,
));

// Get User ID
$user = $facebook->getUser();

// We may or may not have this data based on whether the user is logged in.
//
// If we have a $user id here, it means we know the user is logged into
// Facebook, but we don't know if the access token is valid. An access
// token is invalid if the user logged out of Facebook.

$user_profile = false;

if ($user) {
  try {
    // Proceed knowing you have a logged in user who's authenticated.
    $user_profile = $facebook->api('/me');
  } catch (FacebookApiException $e) {
    error_log($e);
    $user = null;
  }
}



// Login or logout url will be needed depending on current user state.
if ( $user_profile ) {
  $logoutUrl = $facebook->getLogoutUrl();
} else {

	$par['scope'] = "email, user_about_me, user_location, user_website";
		$loginUrl = $facebook->getLoginUrl($par);

}

// This call will always work since we are fetching public data.
//$naitik = $facebook->api('/naitik');

?>