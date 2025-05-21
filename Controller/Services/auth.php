<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Dotenv\Dotenv;

// 

class Auth
{
    private $secret;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
        $dotenv->load();
        $this->secret = $_ENV['SECRET'];
    }

    // 
    public function generateToken($userId, $role)
    {
        $payload = [
            'sub' => $userId,
            'role' => $role,
            'iat' => time(),
            'exp' => time() + (3600 * 24 * 30)
        ];

        return JWT::encode($payload, $this->secret, 'HS256');
    }

    // 
    public function validateToken($token)
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
            return (array) $decoded;
        } catch (Exception) {
            http_response_code(401);
            throw new Exception("User Not Found Try Login");
        }
    }

    // 
    public function authMiddleware()
    {
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            http_response_code(403);
            throw new Exception('This Action Require login First');
        }
        //
        $token = str_replace('Bearer ', '', $headers['Authorization']);
        try {
            return $this->validateToken($token);
        } catch (Exception $e) {
            http_response_code(401);
            throw new Exception('User Not Found Try Login');
        }
    }

    // 
    public function checkRole($requiredRole = [])
    {
        $decoded = $this->authMiddleware();
        $userRole = $decoded['role'];
        if (!in_array($userRole, $requiredRole)) {
            throw new Exception("you Don't Have Permission");
        }
        return $decoded;
    }

    // 
    public function getUserIdFromToken()
    {
        $decoded = $this->authMiddleware();
        return $decoded['sub'];
    }


    //
    public function getRoleFromToken()
    {
        $decoded = $this->authMiddleware();
        return $decoded['role'];
    }

    public function getuser()
    {
        try {
            $headers = getallheaders();
            if (!isset($headers['Authorization'])) {
                return null;
            }
            //
            $token = str_replace('Bearer ', '', $headers['Authorization']);
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
            return (array) $decoded;
            //
        } catch (Exception) {
            return null;
        }
    }
}
