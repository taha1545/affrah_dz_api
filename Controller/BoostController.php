<?php
   require_once 'Models.php';
   require_once 'Controller.php';

   class BoostController extends Controller {

    public function index (){
 
        return $this->boost->all();
    
     }
    
    
     public function show ($id){
           
         return $this->boost->find($id,'id_b');
    
     }
    
    
     public function create($data){
        //validation 
    
       //    
        $this->boost->create($data);
    
        //return true 
       return "data created secssefly";
    
     }
        
    
     public function update($id,$data){
      //validation
        
      //
      $this->boost->update($id,$data,'id_b');
      
      //return
       return "data updated secessefly";
     }
    
    
     public function delete($id) {
         
        $this->boost->delete($id,'id_b');
       
       //
       return "data deleted secc";
    
     }










   }