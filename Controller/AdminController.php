<?php 

require_once 'Controller.php';
require_once 'Services/Collection.php';
require_once 'Services/Resource.php';
require_once 'Services/Validator.php';

class AdminController extends Controller {

  public function index()
  {
    try {
        $admins = $this->admin->all();
        return [
            'status' => 'success',
            'message' => 'admins retrieved successfully',
            'data' => Collection::returnAdmins($admins)
        ];
    } catch (Exception $e) {
       //
        return [
            'status' => 'error',
            'message' => 'An error occurred while fetching admins',
        ];
    }
  }
    
    
  public function show ($id){
    try{
      $admin= $this->admin->find($id,'id_a');
      return [
        'status' => 'success',
        'message' => 'admin retrieved successfully',
        'data' => Resource::ReturnAdmin($admin)
    ];
    }catch (Exception $e) {
      // 
      http_response_code(404);
       return [
           'status' => 'error',
           'message' => 'resource not found',
       ];
   }  
  }
    
    
  public function create($data){
    try{
  //validation
   $valid=new Validator ();
   $data=$valid->validateData($data,'admin');
  //resource
   $data=Resource::GetAdmin($data);
  //create  
   $this->admin->create($data);
  //return true 
    return [
      'status' => 'success',
      'message' =>'data created success',
      ];
    }catch (Exception $e) {
      // error message
       return [
           'status' => 'error',
           'message' =>json_decode($e->getMessage()),
       ];
   } 
}
   

public function update($id, $data)
{
    try {
         //
         $valide= new Validator();
        $data=$valide->validateData($data,'updateadmin');
        // resource 
        $data=Resource::UpdateAdmin($data);
        // Update operation
        if($data !== []){
          $this->admin->update($id, $data, 'id_a');
        }
        // Return success response
        return [
            'status' => 'success',
            'message' => 'Data updated successfully'
        ];
    } catch (Exception $e) {
        // Handle error
        return [
            'status' => 'error',
            'message' => json_decode($e->getMessage())
        ];
    }
}
    
     public function delete($id){
      try {
       $this->admin->delete($id,'id_a');
      // Return success response
      return [
          'status' => 'success',
          'message' => 'Data deleted successfully'
      ];
    } catch (Exception) {
      // Handle error
      http_response_code(500); 
      return [
          'status' => 'error',
          'message' => 'An error occurred while deleting the data'
      ];
     }
  }
 
    
     public function ShowImage($id)
     {
       $image = $this->admin->findImage($id, 'id_a');
         //
       if (isset($image['photo_a'])) {
         //
           header('Content-Type: image/jpeg');
           echo $image['photo_a'];  
       } else {
         //
           http_response_code(404);
       }
   
     }




}