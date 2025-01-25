<?php
//
ob_start();
//
require 'vendor/autoload.php';

// difine Cors
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
//  import controolers
require_once 'Controller/ClientController.php';
require_once 'Controller/MembreController.php';
require_once 'Controller/ResarvationController.php';
require_once 'Controller/AnnonceController.php';
require_once 'Controller/FavorisController.php';
require_once 'Controller/BoostController.php';
require_once 'Controller/ContactController.php';
require_once 'Controller/ImageController.php';
//
$ClientController = new ClientController();
$membreController = new MembreController();
$resarvationController = new ResarvationController();
$contactController = new ContactController();
$annonceController = new AnnonceController();
$favorisController = new FavorisController();
$boostController = new BoostController();
$imagesController = new ImageController();
//
ob_end_flush();

// header type json  and methodes and url
header('Content-Type: application/json');
$url = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$method = $_SERVER['REQUEST_METHOD'];

// get query and data 
$query = [];
parse_str($_SERVER['QUERY_STRING'] ?? '', $query);
// 
$data = [];
if ($method === 'POST') {
  $data = $_POST;
  //
  if (!empty($_FILES)) {
    foreach ($_FILES as $key => $file) {
      if ($key === 'images' && is_array($file['name'])) {
        $filesArray = [];
        foreach ($file['name'] as $index => $fileName) {
          $filesArray[] = [
            'name' => $fileName,
            'type' => $file['type'][$index],
            'tmp_name' => $file['tmp_name'][$index],
            'error' => $file['error'][$index],
            'size' => $file['size'][$index],
          ];
        }
        $data[$key] = $filesArray;
      } else {
        $data[$key] = $file;
      }
    }
  }
} else {
  $rawInput = file_get_contents('php://input');
  $data = json_decode($rawInput, true) ?? [];
}

// routing  
switch (true) {

    //client
  case ($url === 'client' && $method === 'GET'):
    $result = $ClientController->index();
    break;

  case (preg_match('/^client\/(\d+)$/', $url, $matches) && $method === 'GET'):
    $id = $matches[1];
    $result = $ClientController->show($id);
    break;

  case ($url === 'client' && $method === 'POST'):
    $result = $ClientController->create($data);
    break;

  case ($url === 'client/login' && $method === 'POST'):
    $result = $ClientController->login($data);
    break;

  case ($url == 'getinfo' && $method == 'POST'):
    $result = $ClientController->userbytoken($data);
    break;

  case ($url == 'client/forget' && $method == 'POST'):
    $result = $ClientController->updatepassword($data);
    break;

  case ($url == 'client/OTP' && $method == 'POST'):
    $result = $ClientController->OTP($data);
    break;

  case (preg_match('/^client\/(\d+)$/', $url, $matches) && $method === 'PUT'):
    $id = $matches[1];
    $result = $ClientController->update($id, $data);
    break;

  case (preg_match('/^client\/(\d+)$/', $url, $matches) && $method === 'DELETE'):
    $id = $matches[1];
    $result = $ClientController->delete($id);
    break;

  case (preg_match('/^client\/image\/(\d+)$/', $url, $matches) && $method === 'GET'):
    $id = $matches[1];
    $ClientController->ShowImage($id);
    break;


    //membre

  case ($url === 'membre' && $method === 'GET'):
    $result = $membreController->index();
    break;

  case (preg_match('/^membre\/(\d+)$/', $url, $matches) && $method === 'GET'):
    $id = $matches[1];
    $result = $membreController->show($id);
    break;

  case ($url === 'membre' && $method === 'POST'):
    $result = $membreController->create($data);
    break;

  case ($url === 'membre/login' && $method === 'POST'):
    $result = $membreController->login($data);
    break;

  case ($url == 'membre/forget' && $method == 'POST'):
    $result = $membreController->updatepassword($data);
    break;

  case ($url == 'membre/OTP' && $method == 'POST'):
    $result = $membreController->OTP($data);
    break;

  case (preg_match('/^membre\/(\d+)$/', $url, $matches) && $method === 'PUT'):
    $id = $matches[1];
    $result = $membreController->update($id, $data);
    break;

  case (preg_match('/^membre\/(\d+)$/', $url, $matches) && $method === 'DELETE'):
    $id = $matches[1];
    $result = $membreController->delete($id);
    break;

  case (preg_match('/^membre\/image\/(\d+)$/', $url, $matches) && $method === 'GET'):
    $id = $matches[1];
    $membreController->ShowImage($id);
    break;


    //resarvation

  case ($url === 'resarvation' && $method === 'GET'):
    $result = $resarvationController->index($query);
    break;

  case ($url === 'myresarvation' && $method === 'GET'):
    $result = $resarvationController->myresarvation();
    break;

  case (preg_match('/^resarvation\/(\d+)$/', $url, $matches) && $method === 'GET'):
    // 
    $id = $matches[1];
    $result = $resarvationController->show($id);
    break;

  case ($url === 'resarvation' && $method === 'POST'):
    $result = $resarvationController->create($data);
    break;

  case (preg_match('/^resarvation\/(\d+)$/', $url, $matches) && $method === 'PUT'):
    $id = $matches[1];
    $result = $resarvationController->update($id, $data);
    break;

  case (preg_match('/^resarvation\/(\d+)$/', $url, $matches) && $method === 'DELETE'):
    $id = $matches[1];
    $result = $resarvationController->delete($id);
    break;



    //annonce

  case ($url === 'annonce' && $method === 'GET'):
    $result = $annonceController->index($query);
    break;
  case ($url === 'myannonce' && $method === 'GET'):
    $result = $annonceController->myannonce();
    break;

  case ($url === 'annoncefav' && $method === 'GET'):
    $result = $annonceController->myfavoris();
    break;

  case ($url === 'annonce/categorie' && $method === 'GET'):
    $result = $annonceController->showcategorie();
    break;

  case ($url === 'annonce/vip' && $method === 'GET'):
    $result = $annonceController->showvip();
    break;

  case ($url === 'annonce/gold' && $method === 'GET'):
    $result = $annonceController->showgold();
    break;


  case (preg_match('/^annonce\/(\d+)$/', $url, $matches) && $method === 'GET'):
    // 
    $id = $matches[1];
    $result = $annonceController->show($id);
    break;

  case (preg_match('/^annonce\/search\/(.+)$/', $url, $matches) && $method === 'GET'):
    // 
    $word = urldecode($matches[1]);
    $result = $annonceController->search($word);
    break;

  case (preg_match('/^annonce\/visite\/(\d+)$/', $url, $matches) && $method === 'GET'):
    // 
    $id = $matches[1];
    $result = $annonceController->visite($id);
    break;
  case (preg_match('/^annonce\/like\/(\d+)$/', $url, $matches) && $method === 'PUT'):
    // 
    $id = $matches[1];
    $result = $annonceController->like($id);
    break;

  case ($url === 'annonce' && $method === 'POST'):
    $result = $annonceController->create($data);
    break;

  case (preg_match('/^annonce\/(\d+)$/', $url, $matches) && $method === 'PUT'):
    $id = $matches[1];
    $result = $annonceController->update($id, $data);
    break;

  case (preg_match('/^annonce\/(\d+)$/', $url, $matches) && $method === 'DELETE'):
    $id = $matches[1];
    $result = $annonceController->delete($id);
    break;



    // favoris

  case ($url === 'favoris' && $method === 'GET'):
    $result = $favorisController->index();
    break;

  case (preg_match('/^favoris\/(\d+)$/', $url, $matches) && $method === 'GET'):
    $id = $matches[1];
    $result = $favorisController->show($id);
    break;

  case ($url === 'favoris' && $method === 'POST'):
    $result = $favorisController->create($data);
    break;

  case (preg_match('/^favoris\/(\d+)$/', $url, $matches) && $method === 'PUT'):
    $id = $matches[1];
    $result = $favorisController->update($id, $data);
    break;

  case (preg_match('/^favoris\/(\d+)$/', $url, $matches) && $method === 'DELETE'):
    $id = $matches[1];
    $result = $favorisController->delete($id);
    break;



    //boost

  case ($url === 'boost' && $method === 'GET'):
    $result = $boostController->index();
    break;

  case (preg_match('/^boost\/(\d+)$/', $url, $matches) && $method === 'GET'):
    // 
    $id = $matches[1];
    $result = $boostController->show($id);
    break;

  case ($url === 'boost' && $method === 'POST'):
    $result = $boostController->create($data);
    break;

  case (preg_match('/^boost\/(\d+)$/', $url, $matches) && $method === 'PUT'):
    $id = $matches[1];
    $result = $boostController->update($id, $data);
    break;

  case (preg_match('/^boost\/(\d+)$/', $url, $matches) && $method === 'DELETE'):
    $id = $matches[1];
    $result = $boostController->delete($id);
    break;

  case (preg_match('/^boost\/image\/(\d+)$/', $url, $matches) && $method === 'GET'):
    $id = $matches[1];
    $boostController->ShowImage($id);
    break;


    //contact

  case ($url === 'contact' && $method === 'GET'):
    $result = $contactController->index();
    break;

  case (preg_match('/^contact\/(\d+)$/', $url, $matches) && $method === 'GET'):
    // 
    $id = $matches[1];
    $result = $contactController->show($id);
    break;

  case ($url === 'contact' && $method === 'POST'):
    $result = $contactController->create($data);
    break;

  case (preg_match('/^contact\/(\d+)$/', $url, $matches) && $method === 'PUT'):
    $id = $matches[1];
    $result = $contactController->update($id, $data);
    break;

  case (preg_match('/^contact\/(\d+)$/', $url, $matches) && $method === 'DELETE'):
    $id = $matches[1];
    $result = $contactController->delete($id);
    break;


    //images   

  case ($url === 'images' && $method === 'GET'):
    $result = $imagesController->index();
    break;

  case (preg_match('/^images\/(\d+)$/', $url, $matches) && $method === 'GET'):
    // 
    $id = $matches[1];
    $result = $imagesController->show($id);
    break;

  case ($url === 'images' && $method === 'POST'):
    $result = $imagesController->create($data);
    break;

  case (preg_match('/^images\/(\d+)$/', $url, $matches) && $method === 'PUT'):
    $id = $matches[1];
    $result = $imagesController->update($id, $data);
    break;

  case (preg_match('/^images\/(\d+)$/', $url, $matches) && $method === 'DELETE'):
    $id = $matches[1];
    $result = $imagesController->delete($id);
    break;

  default:
    http_response_code(404);
    $result = [
      'status' => 'error',
      'message' => "request endpoint not found",
    ];
    break;
}


//  final result 
echo json_encode($result);
