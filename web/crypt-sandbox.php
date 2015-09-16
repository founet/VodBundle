
<h1>payload cryptée</h1>

<?php

// exemple {"code":200,"message":"ok","payload":{"prestataire_id":2,"prestataire_type":"1"}}

$iv = substr(md5(rand(1,10000)), 0, openssl_cipher_iv_length('aes-256-cbc'));
$json = json_encode([
    'code' => 200,
    'message' => 'ok',
    'payload' => openssl_encrypt(json_encode([
        'prestataire_id' => 99,
        'prestataire_type' => 99,
    ]), 'aes-256-cbc', md5('868ff34788fccade13a9'), 0, $iv) . ':' . $iv,
]);

echo $json;

?>


<h1>payload décryptée</h1>

<?php


$api_response = json_decode($json, true);
$api_response['payload'] = json_decode(openssl_decrypt(explode(':', $api_response['payload'])[0], 'aes-256-cbc', md5('868ff34788fccade13a9'), 0, explode(':', $api_response['payload'])[1]), true);

echo '<pre>';
print_r($api_response);
echo '</pre>';
