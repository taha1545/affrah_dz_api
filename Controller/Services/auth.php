<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Dotenv\Dotenv;

// to use token and maniplate users and securite

class Auth
{
    private $secret;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
        $dotenv->load();
        $this->secret = $_ENV['SECRET'];
    }

    // Generate JWT Token
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

    // Validate JWT Token
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

    // Middleware to Validate Auth Token
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

    // Check Role
    public function checkRole($requiredRole = [])
    {
        $decoded = $this->authMiddleware();
        $userRole = $decoded['role'];
        if (!in_array($userRole, $requiredRole)) {
            throw new Exception("you Don't Have Permission");
        }
        return $decoded;
    }

    // Get User ID from Token
    public function getUserIdFromToken()
    {
        $decoded = $this->authMiddleware();
        return $decoded['sub'];
    }
    // Get Role from Token
    public function getRoleFromToken()
    {
        $decoded = $this->authMiddleware();
        return $decoded['role'];
    }
}
