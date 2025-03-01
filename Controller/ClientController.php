<?php
require_once 'Controller.php';
require_once 'Services/Collection.php';
require_once 'Services/Resource.php';
require_once 'Services/Validator.php';
require_once 'Services/auth.php';
require_once 'Services/Mail.php';

// index = all user fetch
// show = one user fetch 
// create = for signup 
// update to update user data
// show image to get image for client
//login = login user 
// otp and forgetpassword = for forget password logic it send mail to client for otp check 
// updateimage = to update image for client  
// userbytoken = to get data from token (just for test in postman)

class ClientController extends Controller
{

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
      http_response_code(500);
      return [
        'status' => 'error',
        'message' => 'An error occurred while fetching clients',
      ];
    }
  }

  //show client
  public function show($id)
  {
    try {
      $client = $this->client->find($id, 'id_c');
      //
      return [
        'status' => 'success',
        'message' => 'Client retrieved successfully',
        'data' => Resource::ReturnClient($client)
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

  // signup
  public function create($data)
  {
    try {
      //validation
      $valid = new Validator();
      $data = $valid->validateData($data, 'client');
      //image validation
      if (isset($data['image'])) {
        $valid->ValideImage($data['image']);
      }
      //resource
      $data = Resource::GetClient($data);
      //create in db
      $id = $this->client->create($data);
      //generate token 
      $auth = new Auth();
      $token = $auth->generateToken($id, 'client');
      // return with 201
      http_response_code(201);
      return [
        'status' => 'success',
        'message' => 'data created success',
        'token' => $token,
      ];
      // catch errors 
    } catch (Exception $e) {
      // if empty message docode than it means its server error not 421 
      if (empty(json_decode($e->getMessage()))) {
        http_response_code(500);
      }
      //
      return [
        'status' => 'error',
        'message' => json_decode($e->getMessage()) ?? "Can't SignIn Try Later",
      ];
    }
  }


  public function update($id, $data)
  {
    try {
      //auth to get role and user id
      $auth = new Auth();
      $user = $auth->checkRole(['client']);
      // find client in db
      $client = $this->client->find($id, 'id_c');
      // authorazation
      if ($client['id_c'] !== $user['sub']) {
        throw new Exception('u cant update this resource');
      }
      // validation 
      $valide = new Validator();
      $data = $valide->validateData($data, 'updateclient');
      // resource 
      $data = Resource::UpdateClient($data);
      // Update operation
      if ($data !== []) {
        $this->client->update($id, $data, 'id_c');
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
      //auth to get data 
      $auth = new Auth();
      $user = $auth->checkRole(['client']);
      //find
      $client = $this->client->find($id, 'id_c');
      // authorazation 
      if ($client['id_c'] !== $user['sub']) {
        throw new Exception('u cant delete this resource');
      }
      // delete user
      $this->client->delete($id, 'id_c');
      // Return success response
      return [
        'status' => 'success',
        'message' => 'User Deleted successfully'
      ];
    } catch (Exception $e) {
      // Handle error
      http_response_code(500);
      return [
        'status' => 'error',
        'message' => 'Cant Delete This User'
      ];
    }
  }


  public function showImage($id)
  {
    $image = $this->client->findImage($id, 'id_c');

    if ($image && isset($image['photo_c'])) {
      $imageData = $image['photo_c'];

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
        $user = $this->client->find($data['phone'], 'tel_c');
      } else {
        $user = $this->client->find($data['email'], 'email_c');
      }
      //
      if (!$user) {
        http_response_code(404);
        return ['status' => 'error', 'message' => 'User not found'];
      }
      //
      if (!password_verify($data['password'], $user['mdp_c'])) {
        http_response_code(401);
        return ['status' => 'error', 'message' => 'Invalid credentials'];
      }
      //
      if (!empty($data['fcm']) && $user['fcm_token'] !== $data['fcm']) {
        $this->client->update($user['id_c'], ['fcm_token' => $data['fcm']], 'id_c');
      }
      //
      $auth = new Auth();
      $token = $auth->generateToken($user['id_c'], 'client');
      //
      http_response_code(200);
      return ['status' => 'success', 'token' => $token];
    } catch (Exception) {
      http_response_code(500);
      return ['status' => 'error', 'message' => 'An unexpected error occurred'];
    }
  }


  public function updatePassword(array $data)
  {
    if (empty($data['email'])) {
      http_response_code(400);
      return ['status' => 'error', 'message' => 'Email is required'];
    }
    //
    if (empty($data['password']) || strlen($data['password']) < 8) {
      http_response_code(400);
      return ['status' => 'error', 'message' => 'Password must be at least 8 characters long'];
    }
    //
    try {
      //
      $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
      //
      $user = $this->client->find($data['email'], 'email_c');
      //
      $this->client->updatepass($data['email'], 'email_c', ['mdp_c' => $hashedPassword]);
      //
      if (!$user) {
        http_response_code(404);
        return ['status' => 'error', 'message' => 'User not found'];
      }
      //
      $auth = new Auth();
      $token = $auth->generateToken($user['id_c'], 'client');
      //
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


  public function userbytoken($data)
  {
    try {
      //
      $token = $data['token'] ?? null;
      //
      if (!isset($token)) {
        http_response_code(401);
        return [
          'status' => 'error',
          'message' => 'token is required'
        ];
      }
      //
      $auth = new Auth();
      return [
        'status' => 'success',
        'data' => $auth->validateToken($token)
      ];
      //
    } catch (Exception) {
      http_response_code(403);
      return [
        'status' => 'error',
        'message' => 'token not valid',
      ];
    }
  }

  public function  OTP($data)
  {
    try {
      if (empty($data['email'])) {
        throw new Exception('Email is required');
      }
      // find user data
      $email = $data['email'];
      $user = $this->client->find($email, 'email_c');
      //random number to front
      $number = random_int(10000, 99999);
      // send  mail 
      $mail = new Mail();
      if (!$mail->sendmail($email, $number)) {
        throw new Exception('message not send to $email');
      }
      //
      return [
        'status' => 'success',
        'message' => 'Mail Sent successfully',
        'data' => $number
      ];
    } catch (Exception $e) {
      http_response_code(500);
      return [
        'status' => 'error',
        'message' => "Can't Send Mail"
      ];
    }
  }

  public function updateimage($id, $data)
  {
    try {
      //auth to get data of token
      $auth = new Auth();
      $user = $auth->checkRole(['client']);
      //find user
      $client = $this->client->find($id, 'id_c');
      //authorize
      if ($client['id_c'] !== $user['sub']) {
        throw new Exception('u cant update this resource');
      }
      //validate image
      $valid = new Validator();
      $valid::ValideImage($data['image']);
      //update in db
      $image = $data['image'];
      $this->client->update($id, ['photo_c' => file_get_contents($data['image']['tmp_name'])], 'id_c');
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
}
