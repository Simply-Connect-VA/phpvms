<?php

namespace Modules\DisposableAirlines\Listeners;

use App\Events\PirepFiled;
use App\Models\Enums\AircraftState;
use Illuminate\Support\Facades\Log;

class Pirep_Filed
{
  // Listen PirepFiled Event
  // To Change Aircraft State : PARKED
  public function handle(PirepFiled $event)
  {
    $pirep = $event->pirep;
    // Send Out Discord Message
    if (Dispo_Settings('dairlines.discord_pirepmsg')) {
      $this->SendPirepMessage($pirep);
    }
    // Change Aircraft State
    if (Dispo_Settings('dairlines.acstate_control') && $pirep->aircraft) {
      $aircraft = $pirep->aircraft;
      $aircraft->state = AircraftState::PARKED;
      $aircraft->save();
      Log::debug('Disposable Airlines, Pirep '.$event->pirep->id.' FILED, Change STATE of '.$aircraft->registration.' to ON GROUND');
    }
  }

  public function SendPirepMessage($pirep)
  {
    // Create new webhook in your Discord Server settings and copy&paste its URL here
    $webhookurl = Dispo_Settings('dairlines.discord_pirep_webhook');
    $msgposter = !empty(Dispo_Settings('dairlines.discord_pirep_msgposter')) ? Dispo_Settings('dairlines.discord_pirep_msgposter') : config('app.name');
    $avatar = !empty($pirep->user->avatar) ? $pirep->user->avatar->url : $pirep->user->gravatar(256);
    $pirep_aircraft = !empty($pirep->aircraft) ? $pirep->aircraft->registration." (".$pirep->aircraft->icao.")" : "Not Reported";
    $json_data = json_encode([
      // Plain Text Message
      "content" => "New Flight Report Received",
      "username" => $msgposter,
      "tts" => false,
      // Embeds Array
      "embeds" => [
        // Main Content
        [
          "title" => "**Flight Details**",
          "image" => ["url" => $pirep->airline->logo],
          "type" => "rich",
          "timestamp" => date("c", strtotime($pirep->submitted_at)),
          "color" => hexdec( "FF0000" ),
          "thumbnail" => ["url" => $avatar],
          "author" => ["name" => "Pilot In Command: ".$pirep->user->name_private, "url" => route('frontend.profile.show', [$pirep->user->id])],
          // Additional Fields (Discord displays max 3 items per row)
          "fields" => [
            [
              "name"   => "__Flight #__",
              "value"  => $pirep->airline->code.$pirep->flight_number,
              "inline" => true
            ],[
              "name"   => "__Origin__",
              "value"  => $pirep->dpt_airport_id,
              "inline" => true
            ],[
              "name"   => "__Destination__",
              "value"  => $pirep->arr_airport_id,
              "inline" => true
            ],[
              "name"   => "__Distance__",
              "value"  => $pirep->distance." nm",
              "inline" => true
            ],[
              "name"   => "__Block Time__",
              "value"  => Dispo_TimeConvert($pirep->flight_time),
              "inline" => true
            ],[
              "name"   => "__Equipment__",
              "value"  => $pirep_aircraft,
              "inline" => true
            ],
          ],
        ],
      ]
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

    $ch = curl_init($webhookurl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    if ($response) { Log::debug('Disposable Airlines, Discord WebHook Pirep Msg Response: '.$response); }
    curl_close($ch);
  }
}
