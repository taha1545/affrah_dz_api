<?php
require_once 'Controller.php';
require_once 'Services/Collection.php';
require_once 'Services/Resource.php';
require_once 'Services/Validator.php';
require_once 'Services/auth.php';
require_once 'Services/Mail.php';

class ClientController extends Controller
{

  //queue
  //update image 

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

  public function show($id)
  {
    try {
      $client = $this->client->find($id, 'id_c');
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


  public function create($data)
  {
    try {
      //validation
      $valid = new Validator();
      $data = $valid->validateData($data, 'client');
      //image validation
      $valid->ValideImage($data['image']);
      //resource
      $data = Resource::GetClient($data);
      //create  
      $id = $this->client->create($data);
      //generate token 
      $auth = new Auth();
      $token = $auth->generateToken($id, 'client');
      //
      http_response_code(201);
      return [
        'status' => 'success',
        'message' => 'data created success',
        'token' => $token,
      ];
    } catch (Exception $e) {
      if (empty(json_decode($e->getMessage()))) {
        http_response_code(500);
      }
      return [
        'status' => 'error',
        'message' => json_decode($e->getMessage()) ?? "Can't SignIn Try Later",
      ];
    }
  }


  public function update($id, $data)
  {
    try {
      //auth
      $auth = new Auth();
      $user = $auth->checkRole(['client']);
      //
      $client = $this->client->find($id, 'id_c');
      //
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
      } else {
        throw new Exception('no data to update');
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
      $user = $auth->checkRole(['client']);
      //
      $client = $this->client->find($id, 'id_c');
      //
      if ($client['id_c'] !== $user['sub']) {
        throw new Exception('u cant delete this resource');
      }
      // block user
      $this->client->update($id, ['signale' => 'oui'], 'id_c');
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
    // Retrieve the image using the client service or model
    $image = $this->client->findImage($id, 'id_c');

    if ($image && isset($image['photo_c'])) {
      // Retrieve the image data
      $imageData = $image['photo_c'];

      // Validate the MIME type of the image
      $finfo = finfo_open(FILEINFO_MIME_TYPE);
      $mimeType = finfo_buffer($finfo, $imageData);
      finfo_close($finfo);

      // Check if the MIME type is valid (JPEG or PNG)
      if (in_array($mimeType, ['image/jpeg', 'image/jpg', 'image/png'])) {
        // Set the appropriate content type for the image
        header('Content-Type: ' . $mimeType);

        // Output the image data
        echo $imageData;
      } else {
        // Invalid image format
        http_response_code(415);
        echo "Invalid image format";
      }
    } else {
      // Image not found
      http_response_code(404);
      echo "Image not found.";
    }
  }

  public function login($data)
  {
    // Validate the input
    if (empty($data["email"]) || empty($data["password"])) {
      http_response_code(400);
      return [
        'status' => 'error',
        'message' => 'Email or password is missing',
      ];
    }
    try {
      // Find the user by email
      $user = $this->client->find($data['email'], 'email_c');

      if (!$user) {
        // User not found
        http_response_code(404); // Not Found
        return [
          'status' => 'error',
          'message' => 'User not found',
        ];
      }

      // Verify the password
      if (password_verify($data['password'], $user['mdp_c'])) {
        // Generate and return the token
        $auth = new Auth();
        http_response_code(200); // OK
        return [
          'status' => 'success',
          'token' => $auth->generateToken($user['id_c'], 'client'),
        ];
      } else {
        // Password mismatch
        http_response_code(401); // Unauthorized
        return [
          'status' => 'error',
          'message' => 'Invalid credentials',
        ];
      }
    } catch (Exception $e) {
      // Handle unexpected errors
      http_response_code(500); // Internal Server Error
      return [
        'status' => 'error',
        'message' => 'user not found',
      ];
    }
  }

  public function updatepassword($data)
  {
    try {
      // get data 
      $data = [
        'email_c' => $data['email'] ?? null,
        'mdp_c' => $data['password'] ?? null
      ];
      // test email
      if (isset($data['email_c'])) {
        // password validation 
        if (isset($data['mdp_c']) && (strlen($data['mdp_c']) >= 8)) {
          //find user 
          $data['mdp_c'] = password_hash($data['mdp_c'], PASSWORD_BCRYPT);
          $this->client->updatepass($data['email_c'], 'email_c', $data);
          $userid = $this->client->find($data['email_c'], 'email_c');
          // generate token 
          $auth = new Auth();
          $token = $auth->generateToken($userid['id_c'], 'client');
          return [
            'status' => 'success',
            'message' => 'resouce created seccessfly',
            'token' => $token
          ];
        } else {
          http_response_code(400);
          return [
            'status' => 'error',
            'message' => 'password is required '
          ];
        }
      } else {
        http_response_code(400);
        return [
          'status' => 'error',
          'message' => 'email is required '
        ];
      }
    } catch (Exception $e) {
      // Handle error
      http_response_code(500);
      return [
        'status' => 'error',
        'message' => "Can't Update Password"
      ];
    }
  }

  public function userbytoken($data)
  {
    try {
      $token = $data['token'] ?? null;
      if (isset($token)) {
        $auth = new Auth();
        return [
          'status' => 'success',
          'data' => $auth->validateToken($token)
        ];
      } else {
        http_response_code(401);
        return [
          'status' => 'error',
          'message' => 'token is required'
        ];
      }
      //
    } catch (Exception) {
      http_response_code(403);
      return [
        'status' => 'error',
        'message' => 'token not valid'
      ];
    }
  }

  public function  OTP($data)
  {
    try {
      //
      if (empty($data['email'])) {
        throw new Exception('Email is required');
      }
      // 
      $email = $data['email'];
      $user = $this->client->find($email, 'email_c');
      //random number
      $number = random_int(10000, 99999);
      // send 
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
      //auth
      $auth = new Auth();
      $user = $auth->checkRole(['client']);
      $client = $this->client->find($id, 'id_c');
      //authorize
        if($client['id_c'] !== $user['sub']){
          throw new Exception('u cant update this resource');
        }
      //validate image
        $valid=new Validator();
        $valid::ValideImage($data['image']);
        //
       $image=$data['image'];
        $this->client->update($id,['photo_c'=>file_get_contents($data['image']['tmp_name'])],'id_c');

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
