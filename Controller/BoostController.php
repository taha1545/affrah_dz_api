<?php
require_once 'Controller.php';
require_once 'Services/Collection.php';
require_once 'Services/Resource.php';
require_once 'Services/Validator.php';

class BoostController extends Controller
{

  // more validation and logic  

  public function index()
  {
    try {
      $data = $this->boost->all();
      return [
        'status' => 'success',
        'message' => 'data retrieved successfully',
        'data' => Collection::returnBoosts($data)
      ];
    } catch (Exception $e) {
      //
      return [
        'status' => 'error',
        'message' => 'An error occurred while fetching clients',
      ];
    }
  }


  public function show($id)
  {
    try {
      $data = $this->boost->find($id, 'id_b');
      return [
        'status' => 'success',
        'message' => 'data retrieved successfully',
        'data' => Resource::ReturnBoost($data)
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
      //validation
      $valid = new Validator();
      $data = $valid->validateData($data, 'boost');
      //image validation
      $valid->ValideImage($data['image']);
      //resource
      $data = Resource::GetBoost($data);
      //create  
      $this->boost->create($data);
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
      $data = $valide->validateData($data, 'updateboost');
      // resource 
      $data = Resource::UpdateBoost($data);
      // Update operation
      if ($data !== []) {
        $this->boost->update($id, $data, 'id_b');
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
      $this->boost->delete($id, 'id_b');
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



  public function ShowImage($id)
  {
    $image = $this->boost->findImage($id, 'id_b');
    //
    if (isset($image['recu_b'])) {
      //
      header('Content-Type: image/jpeg');
      echo $image['recu_b'];
    } else {
      //
      http_response_code(404);
    }
  }
}
