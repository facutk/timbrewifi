<?
  // app.get('/autosuggest/:country/:currency/:locale', async (req, res) => {
  //   const { country, currency, locale } = req.params;
  //   const { query } = req.query;

  //   const response = await fetch(`${SKYSCANNER_URL}/autosuggest/v1.0/${country}/${currency}/${locale}?query=${query}&apiKey=${SKYSCANNER_API_KEY}`);
  //   const { Places = [] } = await response.json();

  //   return res.status(200).json(Places);
  // });

  include 'Route.php';

  function callAPI($method, $url, $data){
    $curl = curl_init();
    switch ($method){
       case "POST":
          curl_setopt($curl, CURLOPT_POST, 1);
          if ($data)
             curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
          break;
       case "PUT":
          curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
          if ($data)
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
          break;
       default:
          if ($data)
             $url = sprintf("%s?%s", $url, http_build_query($data));
    }
    // OPTIONS:
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
      //  'APIKEY: 111111111111111111111',
       'Content-Type: application/json',
    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    // EXECUTE:
    $result = curl_exec($curl);
    if(!$result){die("Connection Failure");}
    curl_close($curl);
    return $result;
 }

  Route::add('/', function() {
    echo 'Welcome :-)';
  });

  Route::add('/autosuggest/(.*)/(.*)/(.*)/(.*)', function($country,$currency,$locale,$query) {
    $skyscanner_url = $_ENV["SKYSCANNER_URL"];
    $skyscanner_api_key = $_ENV["SKYSCANNER_API_KEY"];

    $url = "$skyscanner_url/autosuggest/v1.0/$country/$currency/$locale?query=$query&apiKey=$skyscanner_api_key";
    $get_data = callAPI('GET', $url, false);
    $response = json_decode($get_data, true);

    header('Content-type: application/json');
    echo json_encode($response["Places"]);
  });

  Route::add('/session/(.*)/(.*)/(.*)/(.*)/(.*)/(.*)/(.*)', function($country, $currency, $locale, $originPlace, $destinationPlace, $outboundDate, $inboundDate) {
    $adults = '1';
    $children = '0';
    $infants = '0';
    $cabinClass = 'economy';
    $locationSchema = 'iata';
    $groupPricing = 'false';
    $skyscanner_url = $_ENV["SKYSCANNER_URL"];
    $skyscanner_api_key = $_ENV["SKYSCANNER_API_KEY"];

    $url = "$skyscanner_url/pricing/v1.0";

    $data = [
      'country' => $country,
      'currency' => $currency,
      'locale' => $locale,
      'adults' => $adults,
      'children' => $children,
      'infants' => $infants,
      'locationSchema' => $locationSchema,
      'originPlace' => $originPlace,
      'cabinClass' => $cabinClass,
      'destinationPlace' => $destinationPlace,
      'outboundDate' => $outboundDate,
      'inboundDate' => $inboundDate,
      'groupPricing' => $groupPricing,
      'apikey' => $skyscanner_api_key
    ];

    $ch = curl_init();
    $http_header = ["Content_type : application/json"];
    $options = [
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_POST => 1,
      CURLOPT_POSTFIELDS => $data,
      CURLOPT_HTTPHEADER => $http_header
    ];
    curl_setopt_array($ch, $options);
    // $response = curl_exec($ch);
    

    // this function is called by curl for each header received
    curl_setopt($ch, CURLOPT_HEADERFUNCTION,
      function($curl, $header) use (&$headers) {
        $len = strlen($header);
        $header = explode(':', $header, 2);
        if (count($header) < 2) // ignore invalid headers
          return $len;

        $headers[strtolower(trim($header[0]))][] = trim($header[1]);

        return $len;
      }
    );

    $data = curl_exec($ch);
    curl_close($ch);
    // print_r($headers["location"][0]);
    $location = $headers["location"][0];
    echo $location;
    // var_dump($data);
    // $init_session_response = callAPI('POST', $url, $data);

    // echo $init_session_response;
    //  const r = await fetch(`${SKYSCANNER_URL}/pricing/v1.0`, {
    //    method: 'POST',
    //    headers: {
    //      'Content-Type': 'application/x-www-form-urlencoded'
    //    },
    //    body: qs.stringify(params)
    //  });

    //  const location = r.headers.get('location');
    //  const pollingUrl = `${location}/?apiKey=${SKYSCANNER_API_KEY}&pageIndex=0&pageSize=1"`;
    //  const rPolling = await fetch(pollingUrl);
    //  const responseJson = await rPolling.json();
      
    //  return res.status(200).json(responseJson);
 });
 
//  app.get('/poll', async (req, res) => {
//    const { sessionKey } = req.query;
   
//    const r = await fetch(`${SKYSCANNER_URL}/pricing/uk1/v1.0/${sessionKey}?apiKey=${SKYSCANNER_API_KEY}`);
//    const responseJson = await r.json();
 
//    return res.status(200).json(responseJson);
//  });

  Route::add('/vuelos', function() {
    $strJsonFileContents = file_get_contents("mockSession.json");
    $jsonResponse = json_decode($strJsonFileContents, true);
    $sid = md5(strval(rand()));

    $itineraries = $jsonResponse["Itineraries"];

    echo '
      <form autocomplete="false" action="" method="post">
        <input type="text" name="from" />
        <input type="submit" value="buscar" />
        <pre>'. count($jsonResponse["Itineraries"]) .'</pre>
        <textarea>'. $strJsonFileContents .'</textarea>
      </form>
    ';
    $sid = md5(strval(rand()));
    echo '<input type="hidden" name="sid" value="' . $sid . '" />';

    // Works if session cookie was accepted
    echo '<br /><a href="">page 2</a>';

    // Or maybe pass along the session id, if needed
    echo '<br /><a href="?' . SID . '">page 2</a>';
  });

  Route::add('/vuelos', function() {

    echo "posted";
  }, 'post');

  Route::run('/api');
?>