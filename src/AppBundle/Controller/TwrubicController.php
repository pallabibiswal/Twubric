<?php

namespace AppBundle\Controller;

use AppBundle\Service\TwrubicService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


class TwrubicController extends Controller
{
    protected $twitter;

    /**
     * TwrubicController constructor.
     * @param TwrubicService $twitter
     */
    public function __construct(TwrubicService $twitter)
    {
        $this->twitter = $twitter;
    }

    /**
     * Home Page redirect to the app route
     * @Route("/", name="home_page")
     */
    public function indexAction()
    {
        return $this->redirect("/app");
    }

    /**
     * Home Page with twitter Login
     * @Route("/app", name="app_page")
     * @return Response
     */
    public function appHomePage()
    {
        $url = $this->twitter->twitterAuth();
        return $this->render("Twrubic/login.html.twig",
            array(
                'url' => $url
            )
        );
    }

    /**
     * @Route("/app/followers", name="list_page")
     */
    public function showList()
    {
        return $this->redirect("/app/follower");
    }

    /**
     * @Route("/app/follower", name="list_followers_page")
     * @return Response
     */
    public function showFollersList()
    {
        $followers = $this->twitter->getFollowersList();

        return $this->render(
            'Twrubic/followers.html.twig',
            array(
                'contents' =>  $followers
            )
        );
    }

    /**
     * @Route("/app/follower/{id}/twubric.json", name="show_page")
     * @param $id
     * @return Response
     */
    public function show($id)
    {
        $followers = $this->twitter->getFollowersList();
        $user_detail = $this->twitter->getFollwerDeatils($followers, $id);
        return $this->render("/Twrubic/json.html.twig",
            array(
                'details' => json_encode($user_detail, JSON_PRETTY_PRINT)
            )
        );
    }
}
