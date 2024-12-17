<?php 
 

 require_once 'Controller.php';

  class ContactController extends Controller {
   
    public function index (){
 
        return $this->contact->all();
    
     }
    
    
     public function show ($id){
           
         return $this->contact->find($id,'id');
    
     }
    
    
     public function create($data){
        //validation 
    
       //    
        $this->contact->create($data);
    
        //return true 
       return "data created secssefly";
    
     }
        
    
     public function update($id,$data){
      //validation
        
      //
      $this->contact->update($id,$data,'id');
      
      //return
       return "data updated secessefly";
     }
    
    
     public function delete($id) {
         
        $this->contact->delete($id,'id');
       
       //
       return "data deleted secc";
    
     }










  }