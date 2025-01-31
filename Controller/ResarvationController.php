<?php
require_once 'Controller.php';
require_once 'Services/Collection.php';
require_once 'Services/Resource.php';
require_once 'Services/Validator.php';

class ResarvationController extends Controller
{
  // more logic and validation for resarvation
  //update

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
      http_response_code(500);
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
      http_response_code(500);
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
      $reserv = $this->resarvation->find($id, 'id_r');
      if ($user['role'] == 'client') {
        if ($user['sub'] !== $reserv['id_c']) {
          http_response_code(403);
          throw new Exception('this client is not allowed for edite');
        }
      }
      if ($user['role'] == 'membre') {
        if ($user['sub'] !== $reserv['id_m']) {
          throw new Exception('this membre is not allowed for edite');
        }
      }
      // validation
      $valide = new Validator();
      $data = $valide->validateData($data, 'updatereservation');
      // resource 
      $data = Resource::UpdateReservation($data);
      // Update operation
      if (empty($data)) {
        throw new Exception('emty data given');
      }
      $this->resarvation->update($id, $data, 'id_r');
      // Return success response
      return [
        'status' => 'success',
        'message' => 'Data updated successfully'
      ];
    } catch (Exception $e) {
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
      $reserv = $this->resarvation->find($id, 'id_r');
      if ($user['role'] == 'client') {
        if ($user['sub'] !== $reserv['id_c']) {
          throw new Exception('this client is not allowed for edite');
        }
      }
      if ($user['role'] == 'membre') {
        if ($user['sub'] !== $reserv['id_m']) {
          throw new Exception('this membre is not allowed for edite');
        }
      }
      //
      $this->resarvation->delete($id, 'id_r');
      // Return success response
      return [
        'status' => 'success',
        'message' => 'Data deleted successfully'
      ];
    } catch (Exception  $e) {
      // Handle error
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
      http_response_code(500);
      return [
        'status' => 'error',
        'message' => "no resarvation found"
      ];
    }
  }

  public function myPlanning($data)
  {
    try {
      // Date handling and validation
      $start = $data['StartDate'] ?? "2024-1-1 00:00:00";
      $final = $data['FinalDate'] ?? date("Y-m-d H:i:s");
      //
      if (!DateTime::createFromFormat('Y-m-d', $start) || !DateTime::createFromFormat('Y-m-d', $final)) {
        throw new InvalidArgumentException('Invalid date format. Use YYYY-MM-DD');
      }
      //auth
      $auth = new Auth();
      $user = $auth->authMiddleware();

      // Fetch raw data based on role
      if ($user['role'] == 'membre') {
        $rawData = $this->resarvation->ReservationsByDateMembre($start, $final, $user['sub']);
        $roleKey = 'membre';
        $roleMap = [
          'id' => 'id_m',
          'name' => 'nom_m',
          'city' => 'ville_m'
        ];
      } else {
        $rawData = $this->resarvation->ReservationsByDateClient($start, $final, $user['sub']);
        $roleKey = 'client';
        $roleMap = [
          'id' => 'id_c',
          'name' => 'nom_c',
          'city' => 'ville_c'
        ];
      }

      // Process data into formatted collection
      $collection = [];
      foreach ($rawData as $item) {
        // Transform reservation data
        $reservation = [
          'id' => (int) $item['id_r'],
          'StartDate' => $item['date_r_debut'],
          'FinalDate' => $item['date_r_fin'],
          'etat' => $item['etat_r'],
          'DateCreation' => $item['date_cr']
        ];

        // Transform role-specific data
        $roleData = [
          'id' => (int) $item[$roleMap['id']],
          'name' => $item[$roleMap['name']],
          'city' => $item[$roleMap['city']]
        ];

        // Transform annonce data with computed fields
        $annonce = [
          'id' => (int) $item['id_an'],
          'name' => $item['nom_an'],
          'city' => $item['ville_an'],
          'address' => $item['adresse_an'],
          'price' => (float) $item['tarif_an'] ?? 0.0,
          'image_full_path' => $item['file_path'] . $item['file_name'],
        ];

        $collection[] = [
          'reservation' => $reservation,
          $roleKey => $roleData,
          'annonce' => $annonce
        ];
      }

      return [
        'status' => 'success',
        'message' => 'Data retrieved successfully',
        'data' => $collection,
      ];
    } catch (Exception $e) {
      http_response_code(500);
      return [
        'status' => 'error',
        'message' => "No Data Found or Invalid Date Format",
      ];
    }
  }
}
