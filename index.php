<?php
require 'vendor/autoload.php';

//buffer
ob_start();

//
header("Access-Control-Allow-Origin: * ");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// controllers 
spl_autoload_register(function ($class) {
  include 'Controller/' . $class . '.php';
});

// 
$clientController = new ClientController();
$membreController = new MembreController();
$resarvationController = new ResarvationController();
$contactController = new ContactController();
$annonceController = new AnnonceController();
$favorisController = new FavorisController();
$boostController = new BoostController();
$imagesController = new ImageController();
$worker = new Worker();

//  JSON
header('Content-Type: application/json');
//  URL + method
$url = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$method = $_SERVER['REQUEST_METHOD'];
//  query + data
$query = [];
parse_str($_SERVER['QUERY_STRING'] ?? '', $query);
//
$data = [];
if ($method === 'POST') {
  $data = $_POST;
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

// rotes
$router = [
  'GET' => [
    //
    'client' => fn() => $clientController->index(),
    'client/(\d+)' => fn($id) => $clientController->show($id),
    'client/image/(\d+)' => fn($id) => $clientController->showImage($id),
    //
    'membre' => fn() => $membreController->index(),
    'membre/(\d+)' => fn($id) => $membreController->show($id),
    'membre/image/(\d+)' => fn($id) => $membreController->showImage($id),
    //
    'resarvation' => fn() => $resarvationController->index($query),
    'myresarvation' => fn() => $resarvationController->myresarvation(),
    'resarvationPlan' => fn() => $resarvationController->myPlanning($_GET),
    'resarvation/(\d+)' => fn($id) => $resarvationController->show($id),
    //
    'annonce' => fn() => $annonceController->index($query),
    'myannonce' => fn() => $annonceController->myannonce(),
    'annoncebymembre/(\d+)' => fn($id) => $annonceController->annoncebymembre($id),
    'annoncefav' => fn() => $annonceController->myfavoris(),
    'annonce/categorie' => fn() => $annonceController->showcategorie(),
    'annonce/vip' => fn() => $annonceController->showvip($query),
    'annonce/gold' => fn() => $annonceController->showgold($query),
    'annonce/(\d+)' => fn($id) => $annonceController->show($id),
    'annonce/search/(.+)' => fn($word) => $annonceController->search(urldecode($word), $query),
    'annonce/visite/(\d+)' => fn($id) => $annonceController->visite($id),
    //
    'favoris' => fn() => $favorisController->index(),
    'favoris/(\d+)' => fn($id) => $favorisController->show($id),
    //
    'boost' => fn() => $boostController->index(),
    'boost/(\d+)' => fn($id) => $boostController->show($id),
    //
    'contact' => fn() => $contactController->index(),
    'contact/(\d+)' => fn($id) => $contactController->show($id),
    //
    'images' => fn() => $imagesController->index(),
    'images/(\d+)' => fn($id) => $imagesController->show($id),
    //
    'startworker' => fn() => $worker->processNotifications(),
  ],
  'POST' => [
    'client' => fn() => $clientController->create($data),
    'client/login' => fn() => $clientController->login($data),
    'getinfo' => fn() => $clientController->userbytoken($data),
    'client/forget' => fn() => $clientController->updatepassword($data),
    'client/OTP' => fn() => $clientController->OTP($data),
    'client/image/update/(\d+)' => fn($id) => $clientController->updateimage($id, $data),
    'membre' => fn() => $membreController->create($data),
    'membre/login' => fn() => $membreController->login($data),
    'membre/forget' => fn() => $membreController->updatepassword($data),
    'membre/OTP' => fn() => $membreController->OTP($data),
    'membre/image/update/(\d+)' => fn($id) => $membreController->updateimage($id, $data),
    'boostbypoint/(\d+)' => fn($id) => $membreController->FreeBoost($id),
    //
    'resarvation' => fn() => $resarvationController->create($data),
    'annonce' => fn() => $annonceController->create($data),
    'favoris' => fn() => $favorisController->create($data),
    'boost' => fn() => $boostController->create($data),
    'contact' => fn() => $contactController->create($data),
    'images' => fn() => $imagesController->create($data),
  ],
  'PUT' => [
    'client/(\d+)' => fn($id) => $clientController->update($id, $data),
    'membre/(\d+)' => fn($id) => $membreController->update($id, $data),
    'resarvation/(\d+)' => fn($id) => $resarvationController->update($id, $data),
    'annonce/(\d+)' => fn($id) => $annonceController->update($id, $data),
    'annonce/like/(\d+)' => fn($id) => $annonceController->like($id),
    'favoris/(\d+)' => fn($id) => $favorisController->update($id, $data),
    'boost/(\d+)' => fn($id) => $boostController->update($id, $data),
    'contact/(\d+)' => fn($id) => $contactController->update($id, $data),
    'images/(\d+)' => fn($id) => $imagesController->update($id, $data),
  ],
  'DELETE' => [
    'client/(\d+)' => fn($id) => $clientController->delete($id),
    'membre/(\d+)' => fn($id) => $membreController->delete($id),
    'resarvation/(\d+)' => fn($id) => $resarvationController->delete($id),
    'annonce/(\d+)' => fn($id) => $annonceController->delete($id),
    'favoris/(\d+)' => fn($id) => $favorisController->delete($id),
    'boost/(\d+)' => fn($id) => $boostController->delete($id),
    'contact/(\d+)' => fn($id) => $contactController->delete($id),
    'images/(\d+)' => fn($id) => $imagesController->delete($id),
  ],
];
// Match route 
$matched = false;
foreach ($router[$method] as $route => $action) {
  if (preg_match('#^' . $route . '$#', $url, $matches)) {
    array_shift($matches);
    $result = $action(...$matches);
    $matched = true;
    break;
  }
}
// not found
if (!$matched) {
  http_response_code(404);
  $result = [
    'status' => 'error',
    'message' => "Request endpoint not found",
  ];
}
// close connection +  result 
Database::closeConnection();
//
echo json_encode($result);
ob_end_flush();
