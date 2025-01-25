<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Dotenv\Dotenv;

class Auth
{
    private $secret;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
        $dotenv->load();
        $this->secret = $_ENV['SECRET'] ;
    }

    // Generate JWT Token
    public function generateToken($userId, $role)
    {
        $payload = [
            'sub' => $userId,
            'role' => $role,
            'iat' => time(),
            'exp' => time() + (3600 * 240)
        ];

        return JWT::encode($payload, $this->secret, 'HS256');
    }

    // Validate JWT Token
    public function validateToken($token)
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
            return (array) $decoded;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    // Middleware to Validate Auth Token
    public function authMiddleware()
    {
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            throw new Exception('u need to login first');
        }
        //
        $token = str_replace('Bearer ', '', $headers['Authorization']);
        try {
            return $this->validateToken($token);
        } catch (Exception $e) {
            http_response_code(401);
            throw new Exception('token is not valid');
        }
    }

    // Check Role
    public function checkRole($requiredRole = [])
    {
        $decoded = $this->authMiddleware();
        $userRole = $decoded['role'];
        if (!in_array($userRole, $requiredRole)) {
            throw new Exception("This role is not allowed for this user");
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
