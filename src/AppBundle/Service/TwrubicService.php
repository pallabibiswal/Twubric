<?php

namespace AppBundle\Service;

use Abraham\TwitterOAuth\TwitterOAuth;

class TwrubicService
{
    public $followers;

    public function twitterAuth()
    {
        $connection = new TwitterOAuth(
            'iDj1Kd5xZB4dmTuKbJCxRrxDI',
            'Le7uZhNTjUpTNu3C2tRbLioQdejK6hcjsFlMiqCpD9dxPrrkq2'
        );

        $request_token = $connection->oauth(
            'oauth/request_token',
            array('oauth_callback' => 'http://localhost:8000/app/followers')
        );

        $url = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));

        return $url;
    }

    public function getFollowersList()
    {
        $connection = new TwitterOAuth(
            'iDj1Kd5xZB4dmTuKbJCxRrxDI',
            'Le7uZhNTjUpTNu3C2tRbLioQdejK6hcjsFlMiqCpD9dxPrrkq2',
            '4919593574-czTwto2iW2dUD9m3cr2imlH7btiOOAF9dwhJAsf',
            '3o1ibBRbvGw8USD3KxFVQDVIKhtdOZnUFAIPIQhaWkOFS'
        );
        $content = $connection->get("followers/list", ["cursor" => "-1"]);
        $data = json_encode($content);
        $arr = json_decode($data, true);
        $this->followers = $arr['users'];

        return  $this->followers;
    }
    public function getFollwerDeatils($users, $id)
    {
        $follower_info = [];
        $user_info = [];
        $info = array(
            "id",
            "id_str",
            "name",
            "screen_name",
            "followers_count",
            "friends_count",
            "listed_count",
            "favourites_count",
            "statuses_count"
        );
        foreach($users as $user) {
            if($id == $user['id']) {
                $user_info = $user;
            }
        }
        if (count($user_info) > 0) {
            foreach($info as $key){
                $follower_info[$key] = $user_info[$key];
            }
        }

        return $follower_info;
    }
}