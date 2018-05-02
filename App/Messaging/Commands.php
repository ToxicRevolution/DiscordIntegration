<?php
namespace App\Messaging;

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
        echo $this->user . " " . $this->message;
    }
}