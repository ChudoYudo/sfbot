<?php

namespace App\Controller;

use App\Entity\TelegramUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\CallbackQuery;

class WebHookController extends AbstractController
{
    /**
     * @Route("/webhook", name="web_hook_in")
     */
    public function index()
    {
        $userFirstName = $this->getUser()->getUsername();
        // the template path is the relative file path from `templates/`
        return $this->render('webhook/index.html.twig', [
            // this array defines the variables passed to the template,
            // where the key is the variable name and the value is the variable value
            // (Twig recommends using snake_case variable names: 'foo_bar' instead of 'fooBar')
            'user_first_name' => $userFirstName,
        ]);
    }
    /**
     * @Route("/webhook/hk", name="web_hook_hk")
     */
    public function hk(Request $request)
    {
        $telegram = new Api($this->getParameter('api_token'));
        $sender=$telegram->getWebhookUpdates()->getMessage()->getFrom();
        $message=$telegram->getWebhookUpdates()->getMessage();
        $users = $this->getDoctrine()
            ->getRepository(TelegramUser::class)
            ->findBy(["chatId"=>$sender->getId()]);
        if ($users){
            $user=$users[0];
        } else {
            $user=null;
        }
        if (!$user){
            $user = new TelegramUser();
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
            if ($user->getLastComand()=='log'){
                if ($user->getComandDeep()==2){
                    $telegram->sendMessage(['chat_id'=>$user->getChatId(),'text'=>'pass:']);
                    $user->setUserName($message->getText());
                    $user->setComandDeep($user->getComandDeep() - 1);
                    $this->getDoctrine()->getManager()->persist($user);
                    $this->getDoctrine()->getManager()->flush();
                } elseif ($user->getComandDeep()==1){
                    $telegram->sendMessage(['chat_id'=>$user->getChatId(),'text'=>'success!']);
                    $user->setPassword($message->getText());
                    $user->setComandDeep($user->getComandDeep() - 1);
                    $this->getDoctrine()->getManager()->persist($user);
                    $this->getDoctrine()->getManager()->flush();
                }

            }
        } else {
            switch ($message->getText()) {
                case '/log':
                    $telegram->sendMessage(['chat_id'=>$user->getChatId(),'text'=>'login:']);
                    $user->setLastComand('log');
                    $user->setComandDeep(2);
                    $this->getDoctrine()->getManager()->persist($user);
                    $this->getDoctrine()->getManager()->flush();
                    break;
                case '/tickets':
                    $user->setLastComand('tickets');
                    $user->setComandDeep(0);
                    $this->getDoctrine()->getManager()->persist($user);
                    $this->getDoctrine()->getManager()->flush();
                    $tickets = $user->getTickets();
                    $telegram->sendChatAction(['chat_id' => $user->getChatId(), 'action' => 'typing']);
                    if (!$tickets){
                        $telegram->sendMessage(['chat_id'=>$user->getChatId(),'text'=>"Something went wrong. \n Try change login data with /log command"]);
                        break;
                    }
                    foreach ($tickets as $id => $info) {
                        $keyboard = [
                            [ ['text' => 'Open in Unfuddle','url'=>'https://acobby.unfuddle.com/projects/44/tickets/by_number/'.$id],],
                        ];
                        $reply_markup = $telegram->replyKeyboardMarkup([
                            'inline_keyboard'=>$keyboard,
                            'one_time_keyboard'=>false
                        ]);
                        $msg = $id."\n".$info['summary'].":\n\n".htmlspecialchars($info['description']);
                        $telegram->sendMessage(['parse_mode'=>'HTML','chat_id'=>$user->getChatId(),'text'=>$msg,'reply_markup'=>$reply_markup]);
                    }
            }
        }

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
