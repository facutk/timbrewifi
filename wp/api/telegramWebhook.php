<?php
  require __DIR__ . '/gsheets.php';

  $payload = file_get_contents("php://input");
  $update = json_decode($payload, TRUE);

  // just for debugging
  // $fe = fopen('php://stderr', 'w');
  // fprintf($fe, '%s', json_encode($update, JSON_PRETTY_PRINT));

  $chatId = $update["message"]["chat"]["id"];
  $message = $update["message"]["text"];

  function reply($msg) {
    global $chatId;
   
    $uri = "https://api.telegram.org/bot".$_ENV["TIMBREWIFI_TELEGRAM_TOKEN"];
    
    return file_get_contents($uri."/sendmessage?parse_mode=Markdown&chat_id=".$chatId."&text=".$msg);
  }

  if (strpos($message, "/join") === 0) {
    $room = substr($message, 6);

    if (strlen($room) === 0) {
      return reply("/join _<nombre del timbre>_");
    }

    addDevice($chatId, strtolower($room));
    return reply("Te uniste a *".$room."*");
  }

  if (strpos($message, "/exit") === 0) {
    $room = substr($message, 6);
    $devices = getDeviceList($chatId);

    if (strlen($room) === 0) {
      if (count($devices) === 0) {
        return reply("No se encontraron timbres");
      }
      
      return reply("/exit _<nombre del timbre>_%0A```%0A".implode("%0A", $devices)."```");
    }
    
    deleteDevice($chatId, $room);
    return reply("Saliste de *".$room."*");
  }
?>
