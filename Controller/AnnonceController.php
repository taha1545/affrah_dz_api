<?php
require_once 'Controller.php';
require_once 'Services/Collection.php';
require_once 'Services/Resource.php';
require_once 'Services/Validator.php';
require_once 'Services/UploadVideo.php';
require_once 'Services/Filter.php';
class AnnonceController  extends Controller
{

  // filter
  // image problem
  // rating problem
  // search

  public function index($query = null)
  {
    try {
      if ($query == null) {
        //
        $data = $this->annonce->allannonce();
        return [
          'status' => 'success',
          'message' => 'data retrieved successfully',
          'data' => Collection::returnAnnounces($data)
        ];
      } else {
        $condition = Filter::Filterquery($query, 'annonce');
        $data = $this->annonce->whereannonce($condition);
        return [
          'status' => 'success',
          'message' => 'data retrieved successfully',
          'data' => Collection::returnAnnounces($data)
        ];
      }
    } catch (Exception $e) {
      //
      http_response_code(404);
      return [
        'status' => 'error',
        'message' => 'An error occurred while fetching data',
      ];
    }
  }


  public function show($id)
  {
    try {
      // find annonce
      $data = $this->annonce->find($id, 'id_an');
      //get boost
      try {
        $data['boost'] = $this->boost->find($id, "id_an");
      } catch (Exception) {
        $data['boost'] = [];
      }
      // get all images 
      try {
        $data['images'] = $this->images->findall($id, "id_an");
      } catch (Exception) {
        $data['images'] = [];
      }
      //return 
      return [
        'status' => 'success',
        'message' => 'data retrieved successfully',
        'data' => Resource::ReturnAnnonce($data)
      ];
    } catch (Exception $e) {
      // 
      http_response_code(404);
      return [
        'status' => 'error',
        'message' => 'resource not found',
      ];
    }
  }


  public function create($data)
  {
    try {
      //validation of data
      $valid = new Validator();
      $data = $valid->validateData($data, 'annonce');
      //validation of videos
      $valid::ValideVideo(video: $data['image']);
      if (isset($data['video'])) {
        $valid::ValideVideo(video: $data['video']);
      }
      //create vedio
      $data['image'] = UploadVideo::CreateVideo($data['image']);
      if (isset($data['video'])) {
        $data['video'] = UploadVideo::CreateVideo($data['video']);
      }
      //resource
      $data = Resource::GetAnnonce($data);
      //create  
      $this->annonce->create($data);
      //return true 
      return [
        'status' => 'success',
        'message' => 'data created success',
      ];
    } catch (Exception $e) {
      // error message
      return [
        'status' => 'error',
        'message' => json_decode($e->getMessage()),
      ];
    }
  }


  public function update($id, $data)
  {
    try {
      //
      $valide = new Validator();
      $data = $valide->validateData($data, 'updateannonce');
      // resource 
      $data = Resource::UpdateAnnonce($data);
      // Update operation
      if ($data !== []) {
        $this->annonce->update($id, $data, 'id_an');
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


  public function delete($id)
  {
    try {
      $this->annonce->delete($id, 'id_an');
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

  public function showcategorie()
  {
    try {
      // Fetch all categories
      $data = $this->annonce->allcategorie();
      // Extract only the values of 'categorie_an' into a single array
      $categories = array_map(function ($item) {
        return $item['categorie_an'];
      }, $data);
      return [
        'status' => 'success',
        'message' => 'data retrieved successfully',
        'data' => $categories
      ];
    } catch (Exception $e) {
      // Handle exceptions
      http_response_code(404);
      return [
        'status' => 'error',
        'message' => 'An error occurred while fetching data',
      ];
    }
  }

  public function showvip()
  {
    try {
      //
      $data = $this->annonce->allvip();
      return [
        'status' => 'success',
        'message' => 'data retrieved successfully',
        'data' => Collection::returnAnnounces($data)
      ];
    } catch (Exception $e) {
      //
      http_response_code(404);
      return [
        'status' => 'error',
        'message' => 'An error occurred while fetching data',
      ];
    }
  }

  public function showgold()
  {
    try {
      //
      $data = $this->annonce->allboost();
      return [
        'status' => 'success',
        'message' => 'data retrieved successfully',
        'data' => Collection::returnAnnounces($data)
      ];
    } catch (Exception $e) {
      //
      http_response_code(404);
      return [
        'status' => 'error',
        'message' => 'An error occurred while fetching data',
      ];
    }
  }
  

}
