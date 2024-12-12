<?php 
   require_once 'Models.php';
   require_once 'Controller.php';

class AdminController extends Controller {

    public function index (){
 
        return $this->admin->all();
    
     }
    
    
     public function show ($id){
           
         return $this->admin->find($id,'id_a');
    
     }
    
    
     public function create($data){
        //validation 
    
       //    
        $this->admin->create($data);
    
        //return true 
       return "data created secssefly";
    
     }
        
    
     public function update($id,$data){
      //validation
        
      //
      $this->admin->update($id,$data,'id_a');
      
      //return
       return "data updated secessefly";
     }
    
    
     public function delete($id) {
         
        $this->admin->delete($id,'id_a');
       
       //
       return "data deleted secc";
    
     }
 










}