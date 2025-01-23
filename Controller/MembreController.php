<?php
require_once 'Controller.php';
require_once 'Services/Collection.php';
require_once 'Services/Resource.php';
require_once 'Services/Validator.php';

class MembreController  extends Controller
{

  // otp send email

  public function index(){
    try {
      $members = $this->membre->all();
      return [
        'status' => 'success',
        'message' => 'membres retrieved successfully',
        'data' => Collection::returnMembers($members)
      ];
    } catch (Exception) {
      //
      http_response_code(404);
      return [
        'status' => 'error',
        'message' => 'An error get while fetching membres',
      ];
    }
  }


  public function show($id){
    try {
      $membre = $this->membre->find($id, 'id_m');
      return [
        'status' => 'success',
        'message' => 'membre retrieved successfully',
        'data' => Resource::ReturnMembre($membre)
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


  public function create($data){
    try {
      //validation
      $valid = new Validator();
      $data = $valid->validateData($data, 'member');
      //image validation
      $valid->ValideImage($data['image']);
      //resource
      $data = Resource::GetMembre($data);
      //create  
      $id = $this->membre->create($data);
      //generate token 
      $auth = new Auth();
      $token = $auth->generateToken($id, 'membre');
      //
      return [
        'status' => 'success',
        'message' => 'data created success',
        'token' => $token,
      ];
    } catch (Exception $e) {
      // error message
      http_response_code(404);
      return [
        'status' => 'error',
        'message' => json_decode($e->getMessage()),
      ];
    }
  }


  public function update($id, $data)
  {
    try {
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
      http_response_code(404);
      return [
        'status' => 'error',
        'message' => json_decode($e->getMessage())
      ];
    }
  }


  public function delete($id)
  {
    try {
      $this->membre->delete($id, 'id_m');
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
    //
    if ($image && isset($image['photo_m'])) {
      // Retrieve the image data
      $imageData = $image['photo_m'];
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
        echo "Invalid image format.";
      }
    } else {
      // Image not found
      http_response_code(404);
      echo "Image not found.";
    }
  }

    public function login($data)
  {
    // validation
    if (empty($data["email"]) || empty($data["password"])) {
      return [
        'status' => 'error',
        'message' => 'no email or password provided'
      ];
    }
    // generate token and send it
    try {
      //
      $user = $this->membre->find($data['email'], 'email_m');
      //
      if (password_verify($data['password'], $user['mdp_m'])) {
        //
        $auth = new Auth();
        return [
          'status' => 'success',
          'token' => $auth->generateToken($user['id_m'], 'membre')
        ];
      } else {
        http_response_code(404);
        return [
          'status' => 'error',
          'message' => 'password does not match'
        ];
      }
      //
    } catch (Exception) {
      http_response_code(404);
      return [
        'status' => 'error',
        'message' => 'membre not found'
      ];
    }
  }


    public function updatepassword($data){
    try {
      // get data 
      $data = [
        'email_m' => $data['email'] ?? null,
        'mdp_m' => $data['password'] ?? null
      ];
      // test email
      if (isset($data['email_m'])) {
        // password validation 
        if (isset($data['mdp_m']) && (strlen($data['mdp_m']) >= 8)) {
          //find user 
          $data['mdp_m'] = password_hash($data['mdp_m'], PASSWORD_BCRYPT);
          $this->membre->updatepass($data['email_m'], 'email_m', $data);
          $userid = $this->membre->find($data['email_m'], 'email_m');
          // generate token 
          $auth = new Auth();
          $token = $auth->generateToken($userid['id_m'], 'membre');
          return [
            'status' => 'success',
            'message' => 'resouce created seccessfly',
            'token' => $token
          ];
        } else {
          http_response_code(404);
          return [
            'status' => 'error',
            'message' => 'password is required '
          ];
        }
      } else {
        http_response_code(404);
        return [
          'status' => 'error',
          'message' => 'email is required '
        ];
      }
    } catch (Exception $e) {
      // Handle error
      http_response_code(404);
      return [
        'status' => 'error',
        'message' => $e->getMessage()
      ];
    }
  }
}
