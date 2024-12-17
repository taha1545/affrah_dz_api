<?php
     
     require_once 'Controller.php';

   class ImageController extends Controller {

    public function index (){
 
        return $this->images->all();
    
     }
    
    
     public function show ($id){
           
         return $this->images->find($id,'id_img');
    
     }
    
    
     public function create($data){
        //validation 
    
       //    
        $this->images->create($data);
    
        //return true 
       return "data created secssefly";
    
     }
        
    
     public function update($id,$data){
      //validation
        
      //
      $this->images->update($id,$data,'id_img');
      
      //return
       return "data updated secessefly";
     }
    
    
     public function delete($id) {
         
        $this->images->delete($id,'id_img');
       
       //
       return "data deleted secc";
    
     }





   }  