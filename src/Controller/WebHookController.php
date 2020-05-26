<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Telegram\Bot\Api;

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
//        $message=get_object_vars(json_decode($request->getContent()));
//        var_dump($message);
        $telegram = new Api('1253793965:AAGiYA7dz7xfPwmiK0BE59ZqAxeddOLDIUI');
        $sender=$telegram->getWebhookUpdates()->getMessage()->getFrom();
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findBy(["chatId"=>$sender->getId()]);
        if (!$user){
            $user = new User();
            $user->setUserName($sender->getUsername());
            $user->setChatId($sender->getId());
            $user->setFirstName($sender->getFirstName());
            $user->setLastComand('start');
            $user->setComandDeep(1);
            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();
        }
        if ($user->getComandDeep() > 0)
        {
            if ($user->getLastComand()=='start'){
                $telegram->sendMessage(['chat_id'=>$user->getChatId(),'text'=>'how to call you']);
                $user->setComandDeep($user->getComandDeep() - 1);
                $this->getDoctrine()->getManager()->persist($user);
                $this->getDoctrine()->getManager()->flush();
            }
        }
        exit();
        file_put_contents("kek.txt",$request->getContent());
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
        $info=file_get_contents("kek.txt",$request);
        var_dump($info);
        exit();
    }
}
