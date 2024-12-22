<?php
    require_once 'Controller.php';
    require_once 'Services/Collection.php';
    require_once 'Services/Resource.php';
    require_once 'Services/Validator.php';

  class ClientController extends Controller {    

    public function index()
  {
    try {
        $clients = $this->client->all();
        return [
            'status' => 'success',
            'message' => 'Clients retrieved successfully',
            'data' => Collection::returnClients($clients)
        ];
    } catch (Exception $e) {
       //
        return [
            'status' => 'error',
            'message' => 'An error occurred while fetching clients',
        ];
    }
  }

    public function show ($id){
      try{
        $client= $this->client->find($id,'id_c');
        return [
          'status' => 'success',
          'message' => 'Client retrieved successfully',
          'data' => Resource::ReturnClient($client)
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

       // image upload 
      public function create($data){
          try{
        //validation
         $valid=new Validator ();
         $data=$valid->validateData($data,'client');
        //resource
         $data=Resource::GetClient($data);
        //create  
         $this->client->create($data);
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
              $data=$valide->validateData($data,'updateclient');
              // resource 
              $data=Resource::UpdateClient($data);
              // Update operation
              if($data !== []){
                $this->client->update($id, $data, 'id_c');
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
        $this->client->delete($id, 'id_c');
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
    $image = $this->client->findImage($id, 'id_c');
      //
    if (isset($image['photo_c'])) {
      //
        header('Content-Type: image/jpeg');
        echo $image['photo_c'];  
    } else {
      //
        http_response_code(404);
    }

  }
  }







