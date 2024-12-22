<?php
  require_once 'Controller.php';
  require_once 'Services/Collection.php';
  require_once 'Services/Resource.php';
  require_once 'Services/Validator.php';

    class MembreController  extends Controller {

   
      public function index()
      {
        try {
            $members = $this->membre->all();
            return [
                'status' => 'success',
                'message' => 'membres retrieved successfully',
                'data' => Collection::returnMembers($members)
            ];
        } catch (Exception) {
           //
            return [
                'status' => 'error',
                'message' => 'An error occurred while fetching membres',
            ];
        }
      }
        
        
      public function show ($id){
        try{
          $membre= $this->membre->find($id,'id_m');
          return [
            'status' => 'success',
            'message' => 'membre retrieved successfully',
            'data' => Resource::ReturnMembre($membre)
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
       $data=$valid->validateData($data,'member');
      //resource
       $data=Resource::GetMembre($data);
      //create  
       $this->membre->create($data);
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
            $data=$valide->validateData($data,'updatemember');
            // resource 
            $data=Resource::UpdateMembre($data);
            // Update operation
            if($data !== []){
              $this->membre->update($id, $data, 'id_m');
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
           $this->membre->delete($id,'id_m');
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
           $image = $this->membre->findImage($id, 'id_m');
             //
           if (isset($image['photo_m'])) {
             //
               header('Content-Type: image/jpeg');
               echo $image['photo_m'];  
           } else {
             //
               http_response_code(404);
           }
       
         }
    

}