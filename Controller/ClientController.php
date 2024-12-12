<?php
    require_once 'Models.php';
    require_once 'Controller.php';

  class ClientController extends Controller {    

      public function index (){
 
         return $this->client->all();

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



  }







