<?php
require_once 'Controller.php';
require_once 'Services/Collection.php';
require_once 'Services/Resource.php';
require_once 'Services/Validator.php';
require_once 'Services/Notification/Notify.php';



class ResarvationController extends Controller
{

  public function index($query = null)
  {
    try {
      $data = $query
        ? $this->resarvation->where(Filter::Filterquery($query, 'resarvation'))
        : $this->resarvation->all();
      //
      return [
        'status' => 'success',
        'message' => 'data retrieved successfully',
        'data' => Collection::returnReservations($data)
      ];
    } catch (Exception) {
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
    } catch (Exception) {
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
      if ($data['reservationDate'] < date('Y-m-d H:i:s') || $data['finalreservationDate'] < $data['reservationDate']) {
        throw new Exception('date invalide ');
      }
      //resource
      $data = Resource::GetReservation($data);
      $data['id_c'] = $user['sub'];
      //create  
      $this->resarvation->create($data);
      //send notification to membre
      $this->membre->StoreFCM('New Resarvation Affrah', 'you have new resarvation from client ', $data['id_m'], 'membre');
      //return true 
      return [
        'status' => 'success',
        'message' => 'data created success',
      ];
    } catch (Exception $e) {
      http_response_code(500);
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
      //find resarvation
      $reserv = $this->resarvation->find($id, 'id_r');
      //authoraztion for client
      if ($user['role'] == 'client') {
        if ($user['sub'] !== $reserv['id_c']) {
          http_response_code(403);
          throw new Exception('this client is not allowed for edite');
        }
      }
      // authoration for membre
      if ($user['role'] == 'membre') {
        if ($user['sub'] !== $reserv['id_m']) {
          throw new Exception('this membre is not allowed for edite');
        }
      }
      // validation
      $valide = new Validator();
      $data = $valide->validateData($data, 'updatereservation');
      //resource 
      $data = Resource::UpdateReservation($data);
      // Update operation
      if (!empty($data)) {
        $this->resarvation->update($id, $data, 'id_r');
        //send notification to membre
        $this->membre->StoreFCM('Resarvation Status', 'check your resarvation status ', $reserv['id_c'], 'client');
      }
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
          throw new Exception('this client is not allowed for delete');
        }
        if ($reserv['etat_r'] == "active") {
          throw new Exception("this resarvation can't be deleted");
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
      // 
      $start = $data['StartDate'] ?? "2024-1-1 00:00:00";
      $final = $data['FinalDate'] ?? date("Y-m-d H:i:s");
      //
      if (!DateTime::createFromFormat('Y-m-d', $start) || !DateTime::createFromFormat('Y-m-d', $final)) {
        throw new InvalidArgumentException('Invalid date format. Use YYYY-MM-DD');
      }
      //auth
      $auth = new Auth();
      $user = $auth->authMiddleware();

      // 
      if ($user['role'] == 'membre') {
        $rawData = $this->resarvation->ReservationsByDateMembre($start, $final, $user['sub']);
        $roleKey = 'client';
        $roleMap = [
          'id' => 'id_c',
          'name' => 'nom_c',
          'city' => 'ville_c',
          'email' => 'email_c',
          'phone' => 'tel_c'
        ];
      } else {
        $rawData = $this->resarvation->ReservationsByDateClient($start, $final, $user['sub']);
        $roleKey = 'membre';
        $roleMap = [
          'id' => 'id_m',
          'name' => 'nom_m',
          'city' => 'ville_m',
          'email' => 'email_m',
          'phone' => 'tel_m'
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
          'city' => $item[$roleMap['city']],
          'email' => $item[$roleMap['email']],
          'phone' => $item[$roleMap['phone']]
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
