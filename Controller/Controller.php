<?php

require_once 'Services/Models.php';

class Controller
{

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

      $this->client = new Models('client');

      $this->membre = new Models('membre');


      $this->resarvation = new Models('reservation');

      $this->annonce = new Models('annonce');


      $this->favoris = new Models('favoris');

      $this->boost = new Models('boost');

      $this->contact = new Models('contact');

      $this->images = new Models('images');
   }
}
