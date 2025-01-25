<?php

require_once 'Services/Models.php';

use Dotenv\Dotenv;

class Controller
{
   protected $conn;
   protected $client;
   protected $membre;
   protected $annonce;
   protected $resarvation;
   protected $favoris;
   protected $boost;
   protected $contact;
   protected $images;

   public function __construct()
   {
      //
      $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
      $dotenv->load();

      $dbHost = $_ENV['DB_HOST'] ?? 'db';     
      $dbUser = $_ENV['DB_USER'] ?? 'root';            
      $dbPassword = $_ENV['DB_PASSWORD'] ?? 'rootpassword';       
      $dbName = $_ENV['DB_NAME'] ?? 'affrah';           
      
      // Create a MySQL connection
      $this->conn = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);

      if ($this->conn->connect_error) {
         die("Database connection failed: " . $this->conn->connect_error);
      }

      // Initialize Models with the shared connection
      $this->client = new Models('client', $this->conn);
      $this->membre = new Models('membre', $this->conn);
      $this->resarvation = new Models('reservation', $this->conn);
      $this->annonce = new Models('annonce', $this->conn);
      $this->favoris = new Models('favoris', $this->conn);
      $this->boost = new Models('boost', $this->conn);
      $this->contact = new Models('contact', $this->conn);
      $this->images = new Models('images', $this->conn);
   }

   public function __destruct()
   {
      if ($this->conn !== null) {
         $this->conn->close();
      }
   }
}
