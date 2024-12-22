<?php 
   require_once 'Controller.php';
   require_once 'Services/Collection.php';
   require_once 'Services/Resource.php';
   require_once 'Services/Validator.php';


    class ModerateurController  extends Controller {



      public function index()
      {
        try {
            $moderateur = $this->moderateur->all();
            return [
                'status' => 'success',
                'message' => 'moderateurs retrieved successfully',
                'data' => Collection::returnModerators($moderateur)
            ];
        } catch (Exception) {
           //
            return [
                'status' => 'error',
                'message' => 'An error occurred while fetching moderateur',
            ];
        }
      }


      public function show ($id){
        try{
          $moderateur= $this->moderateur->find($id,'id_mo');
          return [
            'status' => 'success',
            'message' => 'moderateur retrieved successfully',
            'data' => Resource::ReturnModerateur($moderateur)
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
       $data=$valid->validateData($data,'moderateur');
      //resource
       $data=Resource::GetModerateur($data);
      //create  
       $this->moderateur->create($data);
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
            $data=$valide->validateData($data,'updatemoderator');
            // resource 
            $data=Resource::UpdateModerateur($data);
            // Update operation
            if($data !== []){
              $this->moderateur->update($id, $data, 'id_mo');
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
      $this->moderateur->delete($id, 'id_mo');
      // Return success response
      return [
          'status' => 'success',
          'message' => 'Data deleted successfully'
      ];
    } catch (Exception $e) {
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
       $image = $this->moderateur->findImage($id, 'id_mo');
         //
       if (isset($image['photo_mo'])) {
         //
           header('Content-Type: image/jpeg');
           echo $image['photo_mo'];  
       } else {
         //
           http_response_code(404);
       }
   
     }


    }