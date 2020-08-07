<?php
  require __DIR__ . '/../../vendor/autoload.php';

  function getClient()
  {
      $client = new Google_Client();
      $client->setApplicationName('Google Sheets API PHP Quickstart');
      $client->setScopes(Google_Service_Sheets::SPREADSHEETS);
      $credentials_file = 'credentials.json';
      if (!file_exists($credentials_file)) {
        $credentials = $_ENV["googleapis_sheets_credentials"];
        if (empty($credentials)) {
          throw new InvalidArgumentException('credentials not found!');
        }
        file_put_contents($credentials_file, $credentials);
      }
      $client->setAuthConfig($credentials_file);

      return $client;
  }

  function getDeviceList($telegramId) {
    $client = getClient();

    $service = new Google_Service_Sheets($client);
    
    $spreadsheetId = $_ENV["TIMBREWIFI_SPREADSHEET_ID"];
    $range = 'device_users!A1:B';
    $response = $service->spreadsheets_values->get($spreadsheetId, $range);

    $values = $response->getValues();

    $filtered = array_filter($values, function($row) use ($telegramId) {
      return $row[1] == $telegramId;
    });
    $devices = array_map(function($row) {
      return $row[0];
    }, $filtered);

    return $devices;
  }

  function deleteDevice($telegramId, $deviceToDelete) {
    $devices = getDeviceList($telegramId);
    array_filter($devices, function($key, $position) use ($deviceToDelete) {
      if ($key === $deviceToDelete) {
        $requests = [
          new Google_Service_Sheets_Request([
            'deleteDimension' => [
              'range' => [
                'sheetId' => 0,
                'dimension' => 'ROWS',
                'startIndex' => $position,
                'endIndex' => $position + 1
              ]
            ]
          ])
        ];
        
        $batchUpdateRequest = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
          'requests' => $requests
        ]);
        $spreadsheetId = $_ENV["TIMBREWIFI_SPREADSHEET_ID"];
        $client = getClient();
        $service = new Google_Service_Sheets($client);
        $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequest);
      }
      return false;
    }, ARRAY_FILTER_USE_BOTH);
  }

  function addDevice($telegramId, $deviceToAdd) {
    $devices = getDeviceList($telegramId);
    
    if (in_array($deviceToAdd, $devices)) {
      return;
    }

    $newvalues = [[
      $deviceToAdd, $telegramId
    ]];

    $body = new Google_Service_Sheets_ValueRange([
      'values' => $newvalues
    ]);

    $params = [
      'valueInputOption' => 'USER_ENTERED'
    ];

    $spreadsheetId = $_ENV["TIMBREWIFI_SPREADSHEET_ID"];
    $client = getClient();
    $service = new Google_Service_Sheets($client);
    $range = "device_users!A1:B";
    $service->spreadsheets_values->append($spreadsheetId, $range, $body, $params);
  }
?>