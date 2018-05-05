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

    private $steamReg;
    private $storeDB;
    private $storeTable;

    /**
     * Inits command check from user message
     * @param string $user
     * @param string $message
     */
    function __construct($user, $message)
    {
        $this->user = $user;
        $this->message = $message;
        $this->storeDB = getenv("DATABASE_DB_STORE");
        $this->storeTable = "store_players";
    }
    function MessageCheck()
    {
        // echo $this->user . " " . $this->message.PHP_EOL; // Using for error checking.
        if($this->message == "ping"){
            return $message_answer = ["true", "pong"];
        }
        if(preg_match("/^!credits STEAM_[0-5]:[01]:\d+$/", $this->message, $match, PREG_OFFSET_CAPTURE, 0)){
            $steam = preg_replace("/^STEAM_[0-5]:/","", $this->SteamCheck($this->message));
            $DB = new MySQL($this->storeDB, $this->storeTable);
            $data = $DB->row("SELECT * FROM {$this->storeTable} WHERE authid=:steam", array('steam' => $steam));
            if($data){
                return $message_answer = [true, "Player " . $data['name'] . " has " . $data['credits'] . " credits"];
            }
            return $message_answer = [true, "Player not found"];
        }
        if(preg_match("/^!check STEAM_[0-5]:[01]:\d+$/", $this->message, $match, PREG_OFFSET_CAPTURE, 0)){
            $steam = preg_replace("/^STEAM_[0-5]:/","", $this->SteamCheck($this->message));
            $DB = new MySQL($this->storeDB, $this->storeTable);
            $data = $DB->row("SELECT * FROM {$this->storeTable} WHERE authid=:steam", array('steam' => $steam));
            $items = $DB->query("SELECT * FROM store_equipment WHERE player_id=:playerid", array("playerid" => $data['id']));
            if($data){
                if(count($items) > 0){
                    $response_string = $data['name'] . " has " . $data['credits'] . " credits and owns";
                    foreach($items as $key => $item){
                        if(count($items)-1 == $key){
                            $response_string = $response_string." , and ".$item['type'];
                        } else {
                            $response_string = $response_string." , ".$item['type'];
                        }
                    }
                    return $message_answer = [true, $response_string];
                }
                return $message_answer = [true, $data['name'] . "has no items."];
            }
            return $message_answer = [true, "Player not found"];
        }
        return $message_answer = [false];
    }
    private function SteamCheck($steamID){
        if(preg_match("/STEAM_[0-5]:[01]:\d+$/", $steamID, $match, PREG_OFFSET_CAPTURE, 0)){
            return $match[0][0];
        }
        return "Oi that isn't a Steam ID";
    }
}