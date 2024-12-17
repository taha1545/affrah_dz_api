<?php 
  
 
  require_once 'Controller.php';

  class FavorisController extends Controller {

    public function index (){
 
        return $this->favoris->all();
    
     }
    
    
     public function show ($id){
           
         return $this->favoris->find($id,'id_fav');
    
     }
    
    
     public function create($data){
        //validation 
    
       //    
        $this->favoris->create($data);
    
        //return true 
       return "data created secssefly";
    
     }
        
    
     public function update($id,$data){
      //validation
        
      //
      $this->favoris->update($id,$data,'id_fav');
      
      //return
       return "data updated secessefly";
     }
    
    
     public function delete($id) {
         
        $this->favoris->delete($id,'id_fav');
       
       //
       return "data deleted secc";
    
     }




  }