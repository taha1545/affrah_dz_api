<?php 
   
    require_once 'Controller.php';

 class ResarvationController extends Controller {


  public function index (){
 
    return $this->resarvation->all();

 }


 public function show ($id){
       
     return $this->resarvation->find($id,'id_r');

 }


 public function create($data){
    //validation 

   //    
    $this->resarvation->create($data);

    //return true 
   return "data created secssefly";

 }
    

 public function update($id,$data){
  //validation
    
  //
  $this->resarvation->update($id,$data,'id_r');
  
  //return
   return "data updated secessefly";
 }


 public function delete($id) {
     
    $this->resarvation->delete($id,'id_r');
   
   //
   return "data deleted secc";

 }



 }