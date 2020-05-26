<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class WebHookController extends AbstractController
{
    /**
     * @Route("/webhook", name="web_hook_in")
     */
    public function index()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/WebHookController.php',
        ]);
    }
    /**
     * @Route("/webhook/hk", name="web_hook_hk")
     */
    public function hk(Request $request)
    {
        file_put_contents("kek.txt",json_encode($request));
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/WebHookController.php',
        ]);
    }
    /**
     * @Route("/webhook/sh", name="web_hook_sh")
     */
    public function sh(Request $request)
    {
        $info=file_get_contents("kek.txt",json_decode($request));
        var_dump($info);
        exit();
    }
}
