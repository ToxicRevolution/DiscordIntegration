<?php
namespace App\Messaging;
use App\Database\MySQL;

class Commands
{
    /**
     * User that sent the message
     * @var string
     */
    private $user;
    /**
     * Message Sent by the user
     * @var string
     */
    private $message;
    /**
     * Inits command check from user message
     * @param string $user
     * @param string $message
     */
    function __construct($user, $message)
    {
        $this->user = $user;
        $this->message = $message;
    }
    function MessageCheck()
    {
        if($this->message == "ping"){
            return $message_answer = ["true", "pong"];
        }
        if(preg_match("/^!credits STEAM_[0-5]:[01]:\d+$/", $this->message, $match, PREG_OFFSET_CAPTURE, 0)){
            echo $this->user . " " . $this->message.PHP_EOL; // Using for error checking.
            $steam = preg_replace("/!credits STEAM_[0-5]:/","",$this->message);
            $DB = new App\Database\MySQL("store", "store_players");
            $data = $DB->row("SELECT * FROM store WHERE authid=:steam", array('steam' => $steam));
            $DB->ConnectionClose();
            return $message_answer = [true, "Player " . $data->name . "has " . $data->credits];
        }
        return $message_answer = [false];
    }
}