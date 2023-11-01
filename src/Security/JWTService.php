<?php

namespace App\Security;

class JWTService
{
//     // Creation of the token
//     public function generate(array $header, array $payload, string $secret, int $validity = 10800): string
//     {
//         if($validity <= 0)
//         {
//             return "";
//         }
//         $now = new DateTimeImmutable();
//         $exp = $now->getTimeStam() + $validity;

//         $payload['iat'] = $now->getTimeStamp();
//         $payload['exp'] = $exp;

//         // Encode base64
//         $base64Header = base64_encode(json_encode($header));
//         $base64Payload = base64_encode(json_encode($payload));

//         // Let's clean the base64
//         $base64Header = str_replace(['+', '/', '='], ['-','_', ''], $base64Header);
//         $base64Payload = str_replace(['+', '/', '='], ['-','_', ''], $base64Payload);

//         // Set the signature
//         $secret = bin2hex(random_bytes(32));
//         $secret = base64_encode($secret);
//         $signature = hash_hmac('sha256', $base64Header . '.' . $base64Payload, $secret, true);

//         $base64Signature = base64_encode($signature);
//         $base64Signature = str_replace(['+', '/', '='], ['-','_', ''], $base64Signature);

//         $jwt = $base64Header . '.' . $base64Payload . '.' . $base64Signature;
//         return $jwt;
//    }
}