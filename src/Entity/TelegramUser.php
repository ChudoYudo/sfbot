<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class TelegramUser
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $userName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $chatId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $first_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $last_comand;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $comand_deep;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $username;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserName(): ?string
    {
        return $this->username;
    }

    public function setUserName(?string $userName): self
    {
        $this->username = $userName;

        return $this;
    }

    public function getChatId(): ?string
    {
        return $this->chatId;
    }

    public function setChatId(string $chatId): self
    {
        $this->chatId = $chatId;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(?string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastComand(): ?string
    {
        return $this->last_comand;
    }

    public function setLastComand(?string $last_comand): self
    {
        $this->last_comand = $last_comand;

        return $this;
    }

    public function getComandDeep(): ?int
    {
        return $this->comand_deep;
    }

    public function setComandDeep(?int $comand_deep): self
    {
        $this->comand_deep = $comand_deep;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }
    public function getTickets(){
        $url = 'https://acobby.unfuddle.com/api/v1/projects/44/tickets';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $i=$this->getUserName().":".$this->getPassword());
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        if ($info['http_code']!=200){
            return false;
        }

        $xml=simplexml_load_string($result);
        $resp = array();
        foreach ($xml->xpath('//ticket') as $item) {
            $row = simplexml_load_string($item->asXML());
            $v = $row->xpath('//version-id[. =""]');
            if(isset($v[0])){
                if ((string)$item->status!=='closed') {
                    $resp+=array(
                        (string)$item->number=>
                            array(
                                'description' => (string)$item->description,
                                'summary' => (string)$item->summary,
                            ));
                }
            }
        }
        return $resp;
    }
}
