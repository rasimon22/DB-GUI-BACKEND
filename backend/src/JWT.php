<?php
use \Firebase\JWT\JWT;
//@param: $payload == data to encode
//@return: jwt
function encodeJWT($payload, $algo = 'HS256') {
    $key = getenv('SECRET_KEY', true) ?: getenv('SECRET_KEY');
    $header = array('typ' => 'JWT', 'alg' => $algo);
    $segments = array();
    $segments[] = JWT::urlsafeB64Encode(JWT::jsonEncode($header));
    $segments[] = JWT::urlsafeB64Encode(JWT::jsonEncode($payload));
    $signing_input = implode('.', $segments);
    $signature = JWT::sign($signing_input, $key, $algo);
    $segments[] = JWT::urlsafeB64Encode($signature);
    return implode('.', $segments);
}
//@param: $payload == jwt
//@return: userid (if valid)


function decodeJWT($jwt, $verify = true)
{
    $key = getenv('SECRET_KEY', true) ?: getenv('SECRET_KEY');
    $tks = explode('.', $jwt);
    if (count($tks) != 3) {
        throw new UnexpectedValueException('Wrong number of segments');
    }
    list($headb64, $payloadb64, $cryptob64) = $tks;
    if (null === ($header = JWT::jsonDecode(JWT::urlsafeB64Decode($headb64)))
    ) {
        throw new UnexpectedValueException('Invalid segment encoding');
    }
    if (null === $payload = JWT::jsonDecode(JWT::urlsafeB64Decode($payloadb64))
    ) {
        throw new UnexpectedValueException('Invalid segment encoding');
    }
    $sig = JWT::urlsafeB64Decode($cryptob64);
    if ($verify) {
        if (empty($header->alg)) {
            throw new DomainException('Empty algorithm');
        }
        if ($sig != JWT::sign("$headb64.$payloadb64", $key, $header->alg)) {
            throw new UnexpectedValueException('Signature verification failed');
        }
    }
    return $payload;
}

?>
