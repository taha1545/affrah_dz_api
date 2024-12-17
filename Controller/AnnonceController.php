<?php 
 

 require_once 'Controller.php';

 class AnnonceController  extends Controller{
   



  public function index (){
 
    return $this->annonce->all();

 }


 public function show ($id){
       
     return $this->annonce->find($id,'id_an');

 }


 public function create($data){
    //validation 

   //    
    $this->annonce->create($data);

    //return true 
   return "data created secssefly";

 }
    

 public function update($id,$data){
  //validation
    
  //
  $this->annonce->update($id,$data,'id_an');
  
  //return
   return "data updated secessefly";
 }


 public function delete($id) {
     
    $this->annonce->delete($id,'id_an');
   
   //
   return "data deleted secc";

 }

 }