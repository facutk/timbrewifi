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

  Route::add('/register', function() {
    $newvalues = [
      [
          "foo2", "bar2"
      ],
    ];
    $body = new Google_Service_Sheets_ValueRange([
      'values' => $newvalues
    ]);
    $params = [
      'valueInputOption' => 'USER_ENTERED'
    ];
    $result = $service->spreadsheets_values->append($spreadsheetId, $range, $body, $params);
    printf("%d cells appended.", $result->getUpdates()->getUpdatedCells());
  });

  Route::add('/delete', function() {
    $requests = [
      // delete 1st row
      new Google_Service_Sheets_Request([
          'deleteDimension' => [
              'range' => [
                'sheetId' => 0,
                'dimension' => 'ROWS',
                'startIndex' => 7,
                'endIndex' => 8
              ]
          ]
      ])
    ];
    
    // Add additional requests (operations) ...
    $batchUpdateRequest = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
        'requests' => $requests
    ]);
    
    $response = $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequest);
    // printf("%d cells updated.", $result->getTotalUpdatedCells());
  });

  Route::run('/api');
?>