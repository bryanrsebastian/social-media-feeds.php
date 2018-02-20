# social-media-feeds.php
Fetching the feeds of your social media page ( Facebook and Twitter ).

### How to use ?
Include first the social-media-feeds.php
```sh
include_once( 'social-media-feeds/social-media-feeds.php' );
```

Next, call the function and supply the arguments
```sh
/**
 * To satisfy this configuration visit 
 * https://developers.facebook.com and your page
 */
$facebook = array(
    'app_id'     => 'YOUR_FACEBOOK_APP_ID',
    'app_secret' => 'YOUR_FACEBOOK_APP_SECRET',
    'page_id'    => 'YOUR_FACEBOOK_PAGE_ID'
);

/**
 * To satisfy this configuration visit 
 * https://apps.twitter.com
 */
$twitter = array(
    'oauth_token'        => 'YOUR_TWIITER_OAUTH_TOKEN',
    'oauth_token_secret' => 'YOUR_TWITTER_OAUTH_TOKEN_SECRET',
    'consumer_key'       => 'YOUR_TWITTER_CONSUMER_KEY',
    'consumer_secret'    => 'YOUR_TWITTER_CONSUMER_SECRET',
    'screen_name'        => 'YOUR_TWITTER_@NAME'
);

$social_media_feeds = social_media_feeds( $facebook, $twitter );
```

But if you want to show only the feeds in your facebook
```sh
$social_media_feeds = social_media_feeds( $facebook );
```

Or twitter
```sh
$social_media_feeds = social_media_feeds( array(), $twitter );
```

The default timezone of this module is "Asia/Manila",
to change that timezone you just need to supply the 3rh argument
List of timezone : http://php.net/manual/en/timezones.php
```sh
$social_media_feeds = social_media_feeds( $facebook, $twitter, 'YourTimezone' );
```

### Returns

| Key | Description |
| ------ | ------ |
| social_media | To determine what kind of social media |
| page_id | Link of the page |
| page_name | Name of the page |
| page_dp | Link of the page display picture |
| post_id | Link of the post |
| post_publish | Datetime when post has been published |
| post_message | Message of the post. `string` `null` |
| post_image | Image of the post. `string` `null` |


### Contributing
For a pull request to be considered it must resolve a bug, or add a feature which is beneficial to a large audience.

Requests must be made against the develop branch. Pull requests submitted against the master branch will not be considered.

All pull requests are subject to approval by the repository owners, who have sole discretion over acceptance or denial.

### License
social-media-feeds.php is under MIT license - http://www.opensource.org/licenses/mit-license.php

> If you find this module useful please don't forget to follow me and star this repository. Thank You!