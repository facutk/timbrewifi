<?
  require __DIR__ . '/../../vendor/autoload.php';

  include 'Route.php';

  function getClient()
  {
      $client = new Google_Client();
      $client->setApplicationName('Google Sheets API PHP Quickstart');
      $client->setScopes(Google_Service_Sheets::SPREADSHEETS);
      $credentials_file = 'credentials.json';
      if (!file_exists($credentials_file)) {
        $credentials = $_ENV["googleapis_sheets_credentials"];
        if(empty($credentials)) {
          throw new InvalidArgumentException('credentials not found!');
        }
        file_put_contents($credentials_file, $credentials);
      }
      $client->setAuthConfig($credentials_file);

      return $client;
  }

  // Get the API client and construct the service object.
  $client = getClient();
  $service = new Google_Service_Sheets($client);

  $spreadsheetId = $_ENV["TIMBREWIFI_SPREADSHEET_ID"];
  if(empty($spreadsheetId)) {
    throw new InvalidArgumentException('spreadsheet_id not defined!');
  }
  // echo $spreadsheetId;

  Route::add('/', function() {
    echo 'Welcome :-)';
  });

  Route::add('/devices', function() {
    $range = 'device_users!A1:B';
    $spreadsheetId = $_ENV["TIMBREWIFI_SPREADSHEET_ID"];
    $client = getClient();
    $service = new Google_Service_Sheets($client);
    $response = $service->spreadsheets_values->get($spreadsheetId, $range);
    $values = $response->getValues();
    echo json_encode($values);
  });

  Route::add('/notify/(.*)', function($device) {
    $spreadsheetId = $_ENV["TIMBREWIFI_SPREADSHEET_ID"];
    $range = 'device_users!A1:B';
    $client = getClient();
    $service = new Google_Service_Sheets($client);
    $response = $service->spreadsheets_values->get($spreadsheetId, $range);

    $values = $response->getValues();

    $filtered = array_filter($values, function($row) use ($device) {
      return $row[0] == $device;
    });
    $telegramUsers = array_map(function($row) {
      return $row[1];
    }, $filtered);

    foreach ($telegramUsers as $userId) {
      $uri = "https://api.telegram.org/bot".$_ENV["TIMBREWIFI_TELEGRAM_TOKEN"];
      return file_get_contents($uri."/sendmessage?parse_mode=Markdown&chat_id=".$userId."&text=Timbre en *".$device."*");
    }

    echo 'ok';
  });

  Route::run('/api');
?>