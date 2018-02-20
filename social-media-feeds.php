<?php
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require 'facebook/facebook.php'; //authenticate facebook
require_once( 'twitter/TwitterAPIExchange.php' ); //authenticate twitter

/**
 * Get the social media feeds
 *
 * @param array $facebook credentials of your facebook app and page
 * @param array $twitter credentials of your twitter app
 * @param array $timezone to configure the timezone because the defaul timezone is UTC only
 * @return array data of the feeds
 */
function social_media_feeds( $facebook = array(), $twitter = array(), $timezone = 'Asia/Manila' ) {
	$array_feeds = array();

    /**
     * Facebook Feeds
     */
	if( ! empty( $facebook ) ) {
		$fb_array_feeds = array();

		define( 'APP_ID', $facebook['app_id'] );
		define( 'APP_SECRET', $facebook['app_secret'] );
		define( 'PAGE_ID', $facebook['page_id'] );

		$config = array(
			'appId'  => APP_ID,
			'secret' => APP_SECRET,      
		);
		$api = new Facebook( $config );
			
		$posts      = $api->api("/".PAGE_ID."/posts?limit=2&fields=attachments,created_time,message");
		$posts_img  = $api->api("/".PAGE_ID."/picture?redirect=false&height=48&width=48");
		$posts_name = $api->api("/".PAGE_ID."?fields=name");
	
		$fbdata = json_decode( json_encode( $posts ) );
	
		if( is_array( $fbdata->data ) && count( $fbdata->data ) > 0 ) {
			foreach ( $fbdata->data as $fb_post ) {
				$fb_post_image = null;
				if( array_key_exists( 'attachments', $fb_post ) ) {
					if ( array_key_exists( 'subattachments', $fb_post->attachments->data[0] ) ) {
						$fb_post_image = $fb_post->attachments->data[0]->subattachments->data[0]->media->image->src;
					} else {
						$fb_post_image = $fb_post->attachments->data[0]->media->image->src;
					}
				}
				
				$fb_post_msg = null;
				if( array_key_exists( 'message', $fb_post ) )
					$fb_post_msg = $fb_post->message;
				
				$fb_array_feeds = array(
					'social_media' => 'facebook',
					'page_id'      => 'https://facebook.com/'.PAGE_ID,
					'page_name'    => $posts_name['name'],
					'page_dp'      => $posts_img['data']['url'],
					'post_id'      => 'http://www.facebook.com/'.$fb_post->id,
					'post_publish' => 'Posted '. human_time_diff( strtotime( get_the_formatted_time( $fb_post->created_time, $timezone ) ), current_time('timestamp') ) . ' ago',
					'post_message' => $fb_post_msg,
					'post_image'   => $fb_post_image,
				);
	
				$array_feeds[ strtotime( $fb_post->created_time ) ] = $fb_array_feeds;
			}
		}
	}

    /**
     * Twitter Feeds
     */
	if( ! empty( $twitter ) ) {
		$twit_array_feeds = array();

		define( 'OAUTH_TOKEN', $twitter['oauth_token'] );
		define( 'OAUTH_TOKEN_SECRET', $twitter['oauth_token_secret'] );
		define( 'CONSUMER_KEY', $twitter['consumer_key'] );
		define( 'CONSUMER_SECRET', $twitter['consumer_secret'] );
		define( 'SCREEN_NAME', $twitter['screen_name'] );

		$settings = array(
			'oauth_access_token'        => OAUTH_TOKEN,
			'oauth_access_token_secret' => OAUTH_TOKEN_SECRET,
			'consumer_key'              => CONSUMER_KEY,
			'consumer_secret'           => CONSUMER_SECRET
		);
		
		$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
		$getfield = '?screen_name='.SCREEN_NAME.'&count=16';
		$requestMethod = 'GET';
	
		$twitter = new TwitterAPIExchange( $settings );
		$response = $twitter->setGetfield( $getfield )
							->buildOauth( $url, $requestMethod )
							->performRequest();

		$encode_res = json_decode( $response );
		if( is_array( $encode_res ) && count( $encode_res ) > 0 ) {
			foreach ( $encode_res as $res ) {
				if( array_key_exists( 'media', $res->entities ) ) {
					$post_image = $res->entities->media[0]->media_url_https;
				} else {
					$post_image = null;    
				}

				/**
				 * Remove link from a string
				 */
				$pattern = "/[a-zA-Z]*[:\/\/]*[A-Za-z0-9\-_]+\.+[A-Za-z0-9\.\/%&=\?\-_]+/i";
				$post_message = "";
				preg_replace( $pattern, $post_message, $res->text );

				if( $post_message == "" )
					$post_message = null;
			
				$twit_array_feeds = array(
					'social_media' => 'twitter',
					'page_id'      => 'https://twitter.com/'.$res->user->screen_name,
					'page_name'    => $res->user->name,
					'page_dp'      => $res->user->profile_image_url_https,
					'post_id'      => 'https://twitter.com/'.$res->user->screen_name.'/status/'.$res->id_str,
					'post_publish' => 'Posted '. human_time_diff( strtotime( get_the_formatted_time( $res->created_at, $timezone ) ), current_time('timestamp') ) . ' ago',
					'post_message' => $post_message,
					'post_image'   => $post_image,
				);
				$array_feeds[ strtotime( $res->created_at ) ] = $twit_array_feeds;
			}
		}
	}
    
    krsort( $array_feeds ); // sort social media feeds from recent post
    
	return $array_feeds;
}

/**
 * Format the time and set the timezone
 */
function get_the_formatted_time( $time, $timezone ) {
	$UTC = new DateTimeZone( 'UTC' );
	$newTZ = new DateTimeZone( $timezone );
	$date = new DateTime( $time, $UTC );
	$date->setTimezone( $newTZ );
	return $date->format( 'Y-m-d H:i:s' );
}
