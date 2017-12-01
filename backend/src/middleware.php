<?php

require_once("JWT.php");
require __DIR__ . '/../vendor/autoload.php';
class AuthMiddleware
{
    public function __invoke($request, $response, $next)
    {
        $token = $request->getParsedBody();
        if(isset($token['jwt']) && isValid($token['jwt'])) {
            return $next($request, $response);
        } else {
            return $response->withStatus(403);
        }
    }
    private function isValid($tkn)
    {
        try {
            $payload = decodeJWT($tkn);
            $now = time();
            return !($now > $payload['exp']);            
        } catch (Exception $e) {
            return false;
        }
    }
}
/*
$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});*/
// e.g: $app->add(new \Slim\Csrf\Guard);
