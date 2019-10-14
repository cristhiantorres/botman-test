<?php
use App\Http\Controllers\BotManController;
use BotMan\BotMan\Messages\Attachments\Image; 
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage; 

$botman = resolve('botman');

$botman->fallback(function ($bot)
{
    $message = $bot->getMessage();
    $bot->reply('Sorry, I dont\'t understand: "' . $message->getText() . '"');
}); 

$botman->hears('Weather in {location}', function ($bot, $location) {
    $url = "http://api.weatherstack.com/current?access_key=f3c5141df50f21d279cb37f44893c629&query=" . urlencode($location);
    $response = json_decode(file_get_contents($url));
    $bot->reply('Weather in ' . $response->location->name . ' is ' . $response->current->weather_descriptions[0]);
});

$botman->hears('{days} day forecast for {location}', function ($bot, $days, $location) {
    $url = "http://api.weatherstack.com/current?access_key=f3c5141df50f21d279cb37f44893c629&query=" . urlencode($location) . "&forecast_days=" . urlencode($days);
    $response = json_decode(file_get_contents($url));
    $bot->reply('Weather in ' . $response->location->name . ' is ' . $response->current->weather_descriptions[0]);
});

$botman->hears('/gif {name}', function ($bot, $name) {
    $url = "http://api.giphy.com/v1/gifs/search?q=" . urlencode($name) . "&api_key=AyjaoRH4TBp85woDqZWJgLdgHEuwvpWR&limit=1";
    $response = json_decode(file_get_contents($url));
    $image = $response->data[0]->images->downsized_large->url;

    $message = OutgoingMessage::create("This is your gif")->withAttachment(
        new Image($image)
    );

    $bot->reply($message);
});
$botman->hears('Start conversation', BotManController::class.'@startConversation');
