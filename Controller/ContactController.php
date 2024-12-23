<?php 
  require_once 'Controller.php';
  require_once 'Services/Collection.php';
  require_once 'Services/Resource.php';
  require_once 'Services/Validator.php';

  class ContactController extends Controller {
   
    public function index()
    {
      try {
          $data = $this->contact->all();
          return [
              'status' => 'success',
              'message' => 'data retrieved successfully',
              'data' => Collection::returnContacts($data)
          ];
      } catch (Exception $e) {
         //
          return [
              'status' => 'error',
              'message' => 'An error occurred while fetching data',
          ];
      }
    }
    
    
    public function show ($id){
      try{
        $data= $this->contact->find($id,'id');
        return [
          'status' => 'success',
          'message' => 'data retrieved successfully',
          'data' => Resource::ReturnContact($data)
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
     $data=$valid->validateData($data,'contact');
    //resource
     $data=Resource::GetContact($data);
    //create  
     $this->contact->create($data);
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
          $data=$valide->validateData($data,'updatecontact');
          // resource 
          $data=Resource::UpdateContact($data);
          // Update operation
          if($data !== []){
            $this->contact->update($id, $data, 'id');
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
    $this->contact->delete($id, 'id');
    // Return success response
     return [
        'status' => 'success',
        'message' => 'Data deleted successfully'
    ];
  } catch (Exception  $e) {
    // Handle error
    http_response_code(500); 
    return [
        'status' => 'error',
        'message' => $e->getMessage()
    ];
   }
}

  }