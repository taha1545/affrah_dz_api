<?php
require_once 'Controller.php';
require_once 'Services/Collection.php';
require_once 'Services/Resource.php';
require_once 'Services/Validator.php';


class MembreController  extends Controller
{

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
      http_response_code(500);
      return [
        'status' => 'error',
        'message' => 'No Data Found',
      ];
    }
  }

  public function show($id)
  {
    try {
      $membre = $this->membre->find($id, 'id_m');
      return [
        'status' => 'success',
        'message' => 'membre retrieved successfully',
        'data' => Resource::ReturnMembre($membre)
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
      //
      $code = $data['code'] ?? null;
      //validation
      $valid = new Validator();
      $data = $valid->validateData($data, 'member');
      //image validation
      $valid->ValideImage($data['image']);
      //resource
      $data = Resource::GetMembre($data);
      //create  membre 
      $id = $this->membre->create($data);
      //
      $CodeUse = false;
      if (isset($code)) {
        $CodeUse = $this->membre->AddPointCoins($code);
      }
      // return secces for signup 
      return [
        'status' => 'success',
        'message' => 'successfully created. wait for your request to be submitted',
        'codeuse' => $CodeUse
      ];
    } catch (Exception $e) {
      // if empty message docode than it means its server error not 421 
      if (empty(json_decode($e->getMessage()))) {
        http_response_code(500);
      }
      return [
        'status' => 'error',
        'message' => json_decode($e->getMessage()) ?? $e->getMessage(),
      ];
    }
  }

  public function update($id, $data)
  {
    try {
      //auth
      $auth = new Auth();
      $user = $auth->checkRole(['membre']);
      // find membre in db
      $membre = $this->membre->find($id, 'id_m');
      // authorazation
      if ($membre['id_m'] !== $user['sub']) {
        throw new Exception('u cant update this resource');
      }
      // validation 
      $valide = new Validator();
      $data = $valide->validateData($data, 'updatemember');
      // resource 
      $data = Resource::UpdateMembre($data);
      // Update operation
      if ($data !== []) {
        $this->membre->update($id, $data, 'id_m');
      }
      // Return success response
      return [
        'status' => 'success',
        'message' => 'Data updated successfully'
      ];
    } catch (Exception $e) {
      // Handle error
      http_response_code(500);
      return [
        'status' => 'error',
        'message' => $e->getMessage()
      ];
    }
  }


  public function delete($id)
  {
    try {
      //auth
      $auth = new Auth();
      $user = $auth->checkRole(['membre']);
      // find
      $membre = $this->membre->find($id, 'id_m');
      //authorize
      if ($membre['id_m'] !== $user['sub']) {
        throw new Exception('u cant delete this resource');
      }
      // delete user
      $this->membre->update($id, ['signale' => 'oui'], 'id_m');
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


  public function showImage($id)
  {
    $image = $this->membre->findImage($id, 'id_m');

    if ($image && isset($image['photo_m'])) {
      $imageData = $image['photo_m'];

      $finfo = finfo_open(FILEINFO_MIME_TYPE);
      $mimeType = finfo_buffer($finfo, $imageData);
      finfo_close($finfo);

      $allowedTypes = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/bmp',
        'image/tiff',
        'image/svg+xml'
      ];

      if (in_array($mimeType, $allowedTypes)) {
        header('Content-Type: ' . $mimeType);
        echo $imageData;
        return;
      }
    }

    $defaultImagePath = './catg/profile.png';
    header('Content-Type: image/png');
    readfile($defaultImagePath);
  }

  public function login(array $data)
  {
    if ((empty($data["email"]) && empty($data["phone"])) || empty($data["password"])) {
      http_response_code(400);
      return ['status' => 'error', 'message' => 'Email/Phone or password is missing'];
    }
    //
    try {
      if (isset($data['phone'])) {
        $user = $this->membre->find($data['phone'], 'tel_m');
      } else {
        $user = $this->membre->find($data['email'], 'email_m');
      }
      //
      if (!$user) {
        http_response_code(404);
        return ['status' => 'error', 'message' => 'User not found'];
      }
      // test for etat 
      if ($user['etat_m'] !== 'valide') {
        throw new Exception('Your request is not yet activated. Please try again later.');
      }
      //
      if (!password_verify($data['password'], $user['mdp_m'])) {
        http_response_code(401);
        return ['status' => 'error', 'message' => 'Invalid credentials'];
      }
      //
      if (!empty($data['fcm']) && $user['fcm_token'] !== $data['fcm']) {
        $this->membre->update($user['id_m'], ['fcm_token' => $data['fcm']], 'id_m');
      }
      //
      $auth = new Auth();
      $token = $auth->generateToken($user['id_m'], 'membre');
      //
      http_response_code(200);
      return ['status' => 'success', 'token' => $token];
    } catch (Exception $e) {
      http_response_code(500);
      return [
        'status' => 'error',
        'message' =>  $e->getMessage()
      ];
    }
  }

  public function updatePassword(array $data)
  {
    if (empty($data['email'])) {
      http_response_code(400);
      return ['status' => 'error', 'message' => 'Email is required'];
    }

    if (empty($data['password']) || strlen($data['password']) < 8) {
      http_response_code(400);
      return ['status' => 'error', 'message' => 'Password must be at least 8 characters long'];
    }

    try {
      $user = $this->membre->find($data['email'], 'email_m');
      if (!$user) {
        http_response_code(404);
        return ['status' => 'error', 'message' => 'User not found'];
      }

      $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
      $this->membre->updatepass($data['email'], 'email_m', ['mdp_m' => $hashedPassword]);

      $auth = new Auth();
      $token = $auth->generateToken($user['id_m'], 'membre');

      return [
        'status' => 'success',
        'message' => 'Password updated successfully',
        'token' => $token
      ];
    } catch (Exception $e) {
      http_response_code(500);
      return ['status' => 'error', 'message' => "Can't update password"];
    }
  }

  public function  OTP($data)
  {
    try {
      //
      if (empty($data['email'])) {
        throw new Exception('email is required');
      }
      //  get membre data 
      $email = $data['email'];
      $user = $this->membre->find($email, 'email_m');
      //random number
      $number = rand(10000, 99999);
      // send  mail to membre 
      $mail = new Mail();
      if (!$mail->sendmail($email, $number)) {
        throw new Exception('message not send to $email');
      }
      // return number to front
      return [
        'status' => 'success',
        'message' => 'mail sent seccefly',
        'data' => $number
      ];
    } catch (Exception $e) {
      http_response_code(500);
      return [
        'status' => 'error',
        'message' => $e->getMessage()
      ];
    }
  }

  public function updateimage($id, $data)
  {
    try {
      //auth to get token data
      $auth = new Auth();
      $user = $auth->checkRole(['membre']);
      // find membre
      $membre = $this->membre->find($id, 'id_m');
      //authorize
      if ($membre['id_m'] !== $user['sub']) {
        throw new Exception('u cant update this resource');
      }
      //validate image
      $valid = new Validator();
      $valid::ValideImage($data['image']);
      // update image in db
      $image = $data['image'];
      $this->membre->update($id, ['photo_m' => file_get_contents($data['image']['tmp_name'])], 'id_m');
      //return true
      http_response_code(200);
      return [
        'status' => 'success',
        'message' => 'image updated succesfuly'
      ];
      //error
    } catch (Exception) {
      http_response_code(500);
      return [
        'status' => 'error',
        'message' => 'cant update this image'
      ];
    }
  }

  public function FreeBoost($id_an)
  {
    try {
      //auth
      $auth = new Auth();
      $user = $auth->checkRole(['membre']);
      //
      $this->membre->createBoostForMembre($user['sub'], $id_an);
      //
      return [
        'status' => true,
        'message' => "boost created succesfly"
      ];
      //
    } catch (Exception $e) {
      // Handle error
      http_response_code(500);
      return [
        'status' => 'error',
        'message' => $e->getMessage()
      ];
    }
  }
}
