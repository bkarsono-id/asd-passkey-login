<?php

namespace Asd\Classes;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWK;
use UnexpectedValueException;

if (!defined('ABSPATH')) exit;

class JwtToken
{
    public function checkToken($token)
    {
        $secretKey = get_option('asd_key2');
        try {
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
            return $decoded;
        } catch (ExpiredException $e) {
            wp_send_json_error("Token is expired: " . $e->getMessage());
            return;
        } catch (SignatureInvalidException $e) {
            wp_send_json_error("Signature is not valid : " . $e->getMessage());
            return;
        } catch (BeforeValidException $e) {
            wp_send_json_error("Token is early decoded : " . $e->getMessage());
            return;
        } catch (UnexpectedValueException $e) {
            wp_send_json_error("General Error : " . $e->getMessage());
            return;
        }
    }

    public function checkGoogleToken($token)
    {
        $verificationUrl = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . $token;

        $response = file_get_contents($verificationUrl);
        if ($response === false) {
            die('Gagal menghubungi server verifikasi Google.');
        }

        // Decode respons JSON
        $data = json_decode($response, true);

        // Periksa hasil verifikasi
        if (isset($data['error_description'])) {
            wp_send_json_error("Verifikasi gagal: " . $data['error_description']);
            exit;
        }

        // Verifikasi klaim penting
        $clientId = '398460535296-i921bef9eq8eljn7ok11kkhbt8r1qvu2.apps.googleusercontent.com';
        if ($data['aud'] !== $clientId) {
            wp_send_json_error("Audience tidak cocok.");
            exit;
        }
        if ($data['iss'] !== 'accounts.google.com' && $data['iss'] !== 'https://accounts.google.com') {
            die('Issuer tidak valid.');
            wp_send_json_error("Issuer tidak valid.");
            exit;
        }
        if ($data['exp'] < time()) {
            wp_send_json_error("Token telah kedaluwarsa ");
            exit;
        }
        return $data;
    }
}
