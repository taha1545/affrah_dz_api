<?php 
   require_once 'Controller.php';
   require_once 'Services/Collection.php';
   require_once 'Services/Resource.php';
   require_once 'Services/Validator.php';

  class FavorisController extends Controller {

    //filter  

    public function index()
    {
      try {
          $data = $this->favoris->all();
          return [
              'status' => 'success',
              'message' => 'data retrieved successfully',
              'data' => Collection::returnFavorites($data)
          ];
      } catch (Exception $e) {
         //
          return [
              'status' => 'error',
              'message' => 'An error occurred while fetching favoris',
          ];
      }
    }
    
    
    public function show ($id){
      try{
        $data= $this->favoris->find($id,'id_fav');
        return [
          'status' => 'success',
          'message' => 'data retrieved successfully',
          'data' => Resource::ReturnFavorite($data)
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
     $data=$valid->validateData($data,'favorite');
    //resource
     $data=Resource::GetFavorite($data);
    //create  
     $this->favoris->create($data);
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
          $data=$valide->validateData($data,'updatefavorite');
          // resource 
          $data=Resource::UpdateFavorite($data);
          // Update operation
          if($data !== []){
            $this->favoris->update($id, $data, 'id_fav');
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
    $this->favoris->delete($id, 'id_fav');
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