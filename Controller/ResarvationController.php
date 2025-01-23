<?php
require_once 'Controller.php';
require_once 'Services/Collection.php';
require_once 'Services/Resource.php';
require_once 'Services/Validator.php';

class ResarvationController extends Controller
{

  //update prblm
  // more validation 
  // more auth 

  public function index($query = null)
  {
    try {
      if ($query == null) {
        $data = $this->resarvation->all();
      } else {
        $condition = Filter::Filterquery($query, 'resarvation');
        $data = $this->resarvation->where($condition);
      }
      return [
        'status' => 'success',
        'message' => 'data retrieved successfully',
        'data' => Collection::returnReservations($data)
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


  public function show($id)
  {
    try {
      $data = $this->resarvation->find($id, 'id_r');
      return [
        'status' => 'success',
        'message' => 'data retrieved successfully',
        'data' => Resource::ReturnReservation($data)
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
      //authentication role 
      $auth = new Auth();
      $user = $auth->checkRole(['client']);
      //validation
      $valid = new Validator();
      $data = $valid->validateData($data, 'reservation');
      //resource
      $data = Resource::GetReservation($data);
      $data['id_c'] = $user['sub'];
      //create  
      $this->resarvation->create($data);
      //return true 
      return [
        'status' => 'success',
        'message' => 'data created success',
      ];
    } catch (Exception $e) {
      http_response_code(404);
      $errorms = json_decode($e->getMessage()) ?? $e->getMessage();
      return [
        'status' => 'error',
        'message' => $errorms
      ];
    }
  }


  public function update($id, $data)
  {
    try {
      //authentication role 
      $auth = new Auth();
      $user = $auth->checkRole(['client', 'membre']);
      //
      $valide = new Validator();
      $data = $valide->validateData($data, 'updatereservation');
      // resource 
      $data = Resource::UpdateReservation($data);
      // Update operation
      if ($data !== []) {
        $this->resarvation->update($id, $data, 'id_r');
      }
      // Return success response
      return [
        'status' => 'success',
        'message' => 'Data updated successfully'
      ];
    } catch (Exception $e) {
      http_response_code(404);
      $errorms = json_decode($e->getMessage()) ?? $e->getMessage();
      return [
        'status' => 'error',
        'message' => $errorms
      ];
    }
  }


  public function delete($id)
  {
    try {
      //authentication role 
      $auth = new Auth();
      $user = $auth->checkRole(['client', 'membre']);
      //
      $this->resarvation->delete($id, 'id_r');
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


  public function myresarvation()
  {
    try {
      //authentication role 
      $auth = new Auth();
      $user = $auth->checkRole(['client', 'membre']);
      // find resarvation
      if ($user['role'] == 'client') {
        $data = $this->resarvation->findall($user['sub'], 'id_c');
      } else {
        $data = $this->resarvation->findall($user['sub'], 'id_m');
      }
      //
      return [
        'status' => 'success',
        'message' => 'data retrieved successfully',
        'data' => Collection::returnReservations($data)
      ];
    } catch (Exception $e) {
      http_response_code(404);
      return [
        'status' => 'error',
        'message' => "no resarvation found"
      ];
    }
  }
}
