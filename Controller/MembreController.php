<?php

   
   require_once 'Controller.php';

    class MembreController  extends Controller {

   
      public function index (){
 
         return $this->membre->all();

      }


      public function show ($id){
            
          return $this->membre->find($id,'id_m');

      }


      public function create($data){
         //validation 

        //    
         $this->membre->create($data);

         //return true 
        return "data created secssefly";

      }
         

      public function update($id,$data){
       //validation
         
       //
       $this->membre->update($id,$data,'id_m');
       
       //return
        return "data updated secessefly";
      }


      public function delete($id) {
         //
         $this->membre->delete($id,'id_m');
        //
        return "data deleted secc";
      }


      public function ShowImage($id)
      {
        $image = $this->membre->findImage($id, 'id_m');
          //
        if (isset($image['photo_m'])) {
          //
            header('Content-Type: image/jpeg');
            echo $image['photo_m'];  
        } else {
          //
            http_response_code(404);
        }
    
      }

}