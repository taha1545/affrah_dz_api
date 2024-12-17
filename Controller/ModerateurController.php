<?php 
 
    require_once 'Controller.php';


    class ModerateurController  extends Controller {



      public function index (){
 
        return $this->moderateur->all();

     }


     public function show ($id){
           
         return $this->moderateur->find($id,'id_mo');

     }


     public function create($data){
        //validation 

       //    
        $this->moderateur->create($data);

        //return true 
       return "data created secssefly";

     }
        

     public function update($id,$data){
      //validation
        
      //
      $this->moderateur->update($id,$data,'id_mo');
      
      //return
       return "data updated secessefly";
     }


     public function delete($id) {
        //
        $this->moderateur->delete($id,'id_mo');
       //
       return "data deleted secc";
     }
 

     public function ShowImage($id)
     {
       $image = $this->moderateur->findImage($id, 'id_mo');
         //
       if (isset($image['photo_mo'])) {
         //
           header('Content-Type: image/jpeg');
           echo $image['photo_mo'];  
       } else {
         //
           http_response_code(404);
       }
   
     }


    }