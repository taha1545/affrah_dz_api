<?php
     require_once 'Controller.php';
     require_once 'Services/Collection.php';
     require_once 'Services/Resource.php';
     require_once 'Services/Validator.php';
     require_once 'Services/UploadVideo.php';

   class ImageController extends Controller {

    // filter  

    public function index()
    {
      try {
          $data = $this->images->all();
          return [
              'status' => 'success',
              'message' => 'data retrieved successfully',
              'data' => Collection::returnImages($data)
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
        $data= $this->images->find($id,'id_img');
        return [
          'status' => 'success',
          'message' => 'data retrieved successfully',
          'data' => Resource::ReturnImages($data)
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
     $data=$valid->validateData($data,'images');
     //validate image
      $valid::ValideImage($data['image']);
      //create image
      $data['image']=UploadVideo::CreateImage($data['image']);
    //resource
     $data=Resource::GetImages($data);
    //create  
     $this->images->create($data);
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
          $data=$valide->validateData($data,'updateimages');
          //validate image
              $valide::ValideImage($data['image']);
          //create image 
           $data['image']=UploadVideo::CreateImage($data['image']);
          // resource 
          $data=Resource::UpdateImages($data);
          // Update operation
          if($data !== []){
            $this->images->update($id, $data, 'id_img');
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
    $this->images->delete($id, 'id_img');
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