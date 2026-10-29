<?php

$context = stream_context_create([
    'ssl' => [
        'verify_peer' => true,
'verify_peer_name' => true,
'cafile' => 'C:/php/cacert.pem',
    ],
]);

$socket = stream_socket_client(
    'tcp://smtp.gmail.com:587',
    $errno,
    $error,
    15,
    STREAM_CLIENT_CONNECT,
    $context
);

if (!$socket) {
    die("Connection failed: $error\n");
}

stream_set_timeout($socket, 10);

function readSmtp($socket): string
{
    $response = '';

    while (($line = fgets($socket)) !== false) {
        $response .= $line;

        if (isset($line[3]) && $line[3] === ' ') {
            break;
        }
    }

    return $response;
}

readSmtp($socket);

fwrite($socket, "EHLO localhost\r\n");
readSmtp($socket);

fwrite($socket, "STARTTLS\r\n");
echo readSmtp($socket);

if (stream_socket_enable_crypto(
    $socket,
    true,
    STREAM_CRYPTO_METHOD_TLS_CLIENT
)) {
    echo "TLS handshake completed (verification disabled for diagnosis only).\n";

    $params = stream_context_get_params($socket);
    $cert = $params['options']['ssl']['peer_certificate'] ?? null;

    if ($cert) {
        $details = openssl_x509_parse($cert);

        echo "Subject:\n";
        print_r($details['subject'] ?? []);

        echo "Issuer:\n";
        print_r($details['issuer'] ?? []);

        echo "Valid from: " . ($details['validFrom_time_t'] ?? 'unknown') . PHP_EOL;
        echo "Valid until: " . ($details['validTo_time_t'] ?? 'unknown') . PHP_EOL;
    }
} else {
    echo "TLS handshake failed.\n";
}

fclose($socket);