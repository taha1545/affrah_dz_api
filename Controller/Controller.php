<?php 
 
class Controller {

   protected $client; 
   
   protected $membre; 

   protected $annonce;  

   protected $moderateur;  

   protected $resarvation;  

   protected $admin;   

   protected $favoris;  

   protected $boost; 

   protected $contact; 

   protected $images;  
   

   public function __construct(){

      $this->client= new Models('client',[],['photo_c']);

      $this->membre=new Models('membre',[],['photo_m']);
        
      $this->moderateur=new Models('moderateur',[],['photo_mo']) ;

      $this->resarvation= new Models('reservation',[],[]);

      $this->annonce= new Models('annonce',[],[]);

      $this->admin= new Models('admin',[],['photo_a']);

      $this->favoris= new Models('favoris',[],[]);

      $this->boost = new Models('boost',[],[]);

      $this->contact = new Models('contact',[],[]);

      $this ->images = new Models('images',[],[]);
   }
      

}

