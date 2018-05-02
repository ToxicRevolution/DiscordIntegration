<?php
require __DIR__ . '/vendor/autoload.php';
use App\Messaging\Commands;

// Config file loader
$dotenv = new \Dotenv\Dotenv(__DIR__);
$dotenv->load();

// Yasmin Discord API Wrapper
$loop = \React\EventLoop\Factory::create();
$client = new \CharlotteDunois\Yasmin\Client(array(), $loop);

// On Ready Message
$client->on('ready', function () use ($client){
    echo 'Logged in as '.$client->user->tag.PHP_EOL;
});

// When a message is sent in discord
$client->on('message', function($message) use ($client){
    if(!($message->author->tag == $client->user->tag)){
        $MessageHandler = new App\Messaging\Commands($message->author->tag, $message->content);
        $MessageHandler->MessageCheck();
    }    
});

$client->login(getenv('DISCORD_BOT_KEY'));
$loop->run();