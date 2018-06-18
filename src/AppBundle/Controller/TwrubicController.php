<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Abraham\TwitterOAuth\TwitterOAuth;
use Symfony\Component\HttpFoundation\Response;


class DefaultController extends Controller
{

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $connection = new TwitterOAuth(
            'iDj1Kd5xZB4dmTuKbJCxRrxDI',
            'Le7uZhNTjUpTNu3C2tRbLioQdejK6hcjsFlMiqCpD9dxPrrkq2'
//            '4919593574-czTwto2iW2dUD9m3cr2imlH7btiOOAF9dwhJAsf',
//            '3o1ibBRbvGw8USD3KxFVQDVIKhtdOZnUFAIPIQhaWkOFS'
        );

        $request_token = $connection->oauth(
            'oauth/request_token',
            array('oauth_callback' => 'http://localhost:8000/app/followers')
        );

        $url = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));
        return $this->render("default/index.html.twig", array(
            'url' => $url
        ));
    }

    /**
     * @Route("/app/followers", name="list_page")
     */
    public function showList()
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
        $users = $arr['users'];
//        echo '<pre>';
//        print_r($arr['users']);
//        exit;

        return $this->render(
            'default/followers.html.twig',
            array(
                'contents' =>  $users
            )
        );
    }

    /**
     * @Route("/app/follower/{id}/twubric.json", name="show_page")
     *
     */
    public function show($id)
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
        $users = $arr['users'];


        foreach($users as $user) {
            if ($id == $user['id']) {
                return new Response(json_encode($user));
            }
        }
    }
}
