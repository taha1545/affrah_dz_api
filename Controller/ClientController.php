<?php
    
    require_once 'Controller.php';
    require_once 'Services/Collection.php';

  class ClientController extends Controller {    

      public function index (){
 
         return Collection::returnClients($this->client->all());

      }


      public function show ($id){
            
          return $this->client->find($id,'id_c');

      }


      public function create($data){
         //validation 

        //    
         $this->client->create($data);

         //return true 
        return "data created secssefly";

      }
         

      public function update($id,$data){
       //validation
         
       //
       $this->client->update($id,$data,'id_c');
       
       //return
        return "data updated secessefly";
      }


      public function delete($id) {
          
         $this->client->delete($id,'id_c');
        
        //
        return "data deleted secc";

      }

    

    public function ShowImage($id)
  {
    $image = $this->client->findImage($id, 'id_c');
      //
    if (isset($image['photo_c'])) {
      //
        header('Content-Type: image/jpeg');
        echo $image['photo_c'];  
    } else {
      //
        http_response_code(404);
    }

  }

      
      



  }







