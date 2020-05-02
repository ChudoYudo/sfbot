<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class WebHookController extends AbstractController
{
    /**
     * @Route("/webhook", name="web_hook")
     */
    public function index()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/WebHookController.php',
        ]);
    }
    /**
     * @Route("/webhook/hk", name="web_hook")
     */
    public function hk(Request $request)
    {
        file_put_contents("kek",(string)$request);
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/WebHookController.php',
        ]);
    }
}
