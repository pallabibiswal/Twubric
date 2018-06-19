<?php

namespace AppBundle\Service;

use Abraham\TwitterOAuth\TwitterOAuth;
use Psr\Container\ContainerInterface;

/**
 * Class TwrubicService
 * @package AppBundle\Servicec
 */
class TwrubicService
{
    protected $criteria = [];
    protected $scale = [];
    protected $attribute = [];
    protected $consumer;
    protected $consumer_key;
    protected $access;
    protected $access_key;
    protected $call_back;

    /**
     * TwrubicService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->consumer = $container->getParameter('consumer_key');
        $this->consumer_key = $container->getParameter('consumer_secret');
        $this->access = $container->getParameter('access_key');
        $this->access_key = $container->getParameter('access_secret');
        $this->call_back = $container->getParameter('oauth_callback');
        $this->criteria = [
            'Friends' => 2,
            'Influence' => 4,
            'Chirpy' => 4
        ];

        $this->attribute = [
            'Friends' => [
                'friends_count'
            ],
            'Influence' => [
                'followers_count',
                'friends_count'
            ],
            'Chirpy' => [
                'friends_count',
                'statuses_count'

            ]
        ];

        $this->scale = [
            'High' => [
                'up' => 'infinity',
                'low'=> 1000,
                'per' => 100
            ],

            'Average' => [
                'up' => 1000,
                'low'=> 500,
                'per' => 75
            ],

            'Low' => [
                'up' => 500,
                'low'=> 100,
                'per' => 50
            ],
        ];
    }

    /**
     * Auth check
     * @return string
     * @throws \Abraham\TwitterOAuth\TwitterOAuthException
     */
    public function twitterAuth()
    {
        $connection = new TwitterOAuth(
            $this->consumer,
            $this->consumer_key
        );

        $request_token = $connection->oauth(
            'oauth/request_token',
            array('oauth_callback' => $this->call_back)
        );

        $url = $connection->url(
            'oauth/authorize',
            array('oauth_token' => $request_token['oauth_token'])
        );

        return $url;
    }

    /**
     * Follower lists
     * @return mixed
     */
    public function getFollowersList()
    {
        $connection = new TwitterOAuth(
            $this->consumer,
            $this->consumer_key,
            $this->access,
            $this->access_key
        );
        $content = $connection->get("followers/list", ["cursor" => "-1"]);
        $data = json_encode($content);
        $arr = json_decode($data, true);
        $followers = $arr['users'];

        return $followers;
    }

    /**
     * Generate json to show followers data
     * @param $users
     * @param $id
     * @return array
     */
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

        //rubric calculation
        $rubic = $this->getCriteria($user_info);
        $follower_info['twubric'] = $rubic;

        return $follower_info;
    }

    /**
     * Function to get rubric data of user
     * @param $user
     * @return array
     */
    public function getCriteria($user)
    {
        $friends = $this->getScaleAttribute($user, 'Friends');
        $friends_wieght = $this->getScaleWeight($friends, 'Friends');
        $influence = $this->getScaleAttribute($user, 'Influence');
        $influence_wieght = $this->getScaleWeight($influence, 'Influence');
        $chirpy = $this->getScaleAttribute($user, 'Chirpy');
        $chirpy_wieght = $this->getScaleWeight($chirpy, 'Chirpy');

        $total = $friends_wieght + $influence_wieght + $chirpy_wieght;

        return array(
            'total' => $total,
            'friends' => $friends_wieght,
            'influence' => $influence_wieght,
            'chirpy' => $chirpy_wieght
        );
    }

    /**
     * Scaling the user data
     * @param $attribute
     * @param $criteria
     * @return float|string
     */
    public function getScaleWeight($attribute, $criteria)
    {
        $weight = '';
        $sum = '';
        if (array_key_exists($criteria, $this->criteria)) {
            $weight = $this->criteria[$criteria];
            $sum = array_sum($attribute);
        }
        if ($sum >= $this->scale['High']['low']) {
            return ($weight * $this->scale['High']['per']) / 100;
        } elseif ($sum >= $this->scale['Average']['low']
            && $sum < $this->scale['Average']['up']
        ) {
            return ($weight * $this->scale['Average']['per']) / 100;
        } elseif (($sum >= $this->scale['Low']['low']
                && $sum < $this->scale['Low']['up'])
            || $sum < $this->scale['Low']['low']
        ) {
            return ($weight * $this->scale['Low']['per']) / 100;
        }
        return $weight;
    }

    /**
     * attributes to scale user data
     * @param $user
     * @param $criteria
     * @return array
     */
    public function getScaleAttribute($user, $criteria)
    {
        $attributes = [];
        if (array_key_exists($criteria, $this->attribute)) {
            foreach($this->attribute[$criteria] as $attr) {
                array_push($attributes, $user[$attr]);
            }
        }

        return $attributes;
    }
}