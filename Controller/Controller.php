<?php

require_once 'Services/Models.php';

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
      // Create a single shared database connection
      $this->conn = new mysqli("db", "root", "rootpassword", "affrah");

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
