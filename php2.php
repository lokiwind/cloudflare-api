<?php
$filter_id = "84f498fc2688481bb206a134b964b672";
$new_country = "CA"; /ülke burada hocam 
$auth_email = "ipverifiedapi@gmail.com";
$auth_key = "e884ff12aa36dbb1fa95bbbd4d28bf6c46c0";
$zone_id = "90d5e2e3ad6fbfff88c64a7bf272622e";

// Get the existing filter
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.cloudflare.com/client/v4/zones/$zone_id/filters/$filter_id",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "X-Auth-Email: $auth_email",
        "X-Auth-Key: $auth_key"
    ],
]);
$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
} else {
    $filter = json_decode($response, true)["result"];
    echo "Filter ID: " . $filter["id"] . "\n";
    echo "Filter Description: " . $filter["description"] . "\n";
    echo "Filter Expression: " . $filter["expression"] . "\n";
    echo "Filter Paused: " . ($filter["paused"] ? "true" : "false") . "\n";
    echo "Filter Ref: " . $filter["ref"] . "\n";

    // Check if the new country is already in the expression
    if (strpos($filter["expression"], "ip.geoip.country eq \"$new_country\"") === false) {
        // Add the new country to the expression
        $expression = $filter["expression"] . " or (ip.geoip.country eq \"$new_country\")";
        $data = [
            "expression" => $expression,
        ];
        $data_json = json_encode($data);

        // Update the filter with the new expression
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.cloudflare.com/client/v4/zones/$zone_id/filters/$filter_id",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => $data_json,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "X-Auth-Email: $auth_email",
                "X-Auth-Key: $auth_key"
            ],
        ]);
        $response = curl_exec($curl);

    $err = curl_error($curl); }
    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        echo "Filter expression updated successfully!";
    }
}
?>
