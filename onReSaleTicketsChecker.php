<?php
require 'credentials.php';

function sendSms(string $message) {
    $postData = [
        "Body" => $message,
        "From" => SENDER_PHONE,
        "To"   => RECIPIENT_PHONE,
    ];
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL  => TWILIO_API_URL,
        CURLOPT_POST =>  true,
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERPWD => TWILIO_CREDENTIALS
    ]);
    curl_exec($curl);

    curl_close($curl);
}

$stream = curl_init();

$headers = [
    'Host: exchange.stadefrance.com',
    'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/112.0',
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
    'Accept-Language: fr,fr-FR;q=0.8,en-US;q=0.5,en;q=0.3',
    'Accept-Encoding: gzip, deflate, br',
    'Connection: keep-alive',
    'Upgrade-Insecure-Requests: 1',
    'Sec-Fetch-Dest: document',
    'Sec-Fetch-Mode: navigate',
    'Sec-Fetch-Site: none',
    'Sec-Fetch-User: ?1',
];
curl_setopt_array($stream, [
    CURLOPT_URL            => PERFORMANCE_LINK,
    CURLOPT_HTTPHEADER     => $headers,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING       => 'gzip',
]);
$response = curl_exec($stream);
$status   = curl_getinfo($stream, CURLINFO_HTTP_CODE);
echo $status.PHP_EOL;
// If error, save response in file and send alert
if ($status !== 200) {
    $date = new DateTimeImmutable();
    file_put_contents($date->format('Y-m-d_H:i:s').'error.html', $response);
    sendSms('Error '.$status.' on http response');
}
// Else parse response to retrieve html and hint
$array          = explode('<!DOCTYPE html>', $response);
$html           = $array[1];
$dom            = new DOMDocument();
$internalErrors = libxml_use_internal_errors(true);
$dom->loadHTML($html);
libxml_use_internal_errors($internalErrors);
$div = $dom->getElementById('controls');

curl_close($stream);

// If it's true, tickets are in sale \o/, send a sms to go buy
if (strlen($div->textContent) > 173 === true) {
    echo 'Tickets Available'.PHP_EOL;
    sendSms('Ya des places poto !!! '.PERFORMANCE_LINK);
} else {
    echo 'No Tickets available'.PHP_EOL;
}
