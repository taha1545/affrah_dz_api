<?php

// import controolers
    require_once 'Controller/ClientController.php';
    require_once 'Controller/MembreController.php';
    require_once 'Controller/ModerateurController.php';
    require_once 'Controller/ResarvationController.php';
    require_once 'Controller/AnnonceController.php';
    require_once 'Controller/AdminController.php';
    require_once 'Controller/FavorisController.php';
    require_once 'Controller/BoostController.php';
    require_once 'Controller/ContactController.php';
    require_once 'Controller/ImageController.php';
    
  // controller classes 10
  $ClientController=new ClientController();
  $membreController=new MembreController();
  $moderateurController=new ModerateurController();
  $resarvationController=new ResarvationController();
  $annonceController= new AnnonceController();
  $adminController=new AdminController();
  $favorisController=new FavorisController();
  $boostController= new BoostController();
  $contactController=new ContactController();
  $imagesController= new ImageController();
 
// header type json and methode and url  and data sent with request and varible in url for fillter
    header('Content-Type: application/json'); 
    $url = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $method = $_SERVER['REQUEST_METHOD'];
   //
   $query = [];
   parse_str($_SERVER['QUERY_STRING'] ?? '', $query);
    //
    $data = [];
    if ($method === 'POST' || $method === 'PUT' || $method === 'DELETE') {
          $rawInput = file_get_contents('php://input');
          $data = json_decode($rawInput, true) ?? [];
    }
   
// routing  
        switch (true) {

//client
        case ($url === 'client' && $method === 'GET'):
         $result=$ClientController->index(); 
        break;
   
        case (preg_match('/^client\/(\d+)$/', $url, $matches) && $method === 'GET'):
            // 
            $id = $matches[1];
            $result = $ClientController->show($id);
            break;
        
        case ($url === 'client' && $method === 'POST'):
          $result=$ClientController->create($data);
         break;

        case (preg_match('/^client\/(\d+)$/', $url, $matches) && $method === 'PUT'):
          $id = $matches[1];
          $result=$ClientController->update($id,$data);
           break;

        case (preg_match('/^client\/(\d+)$/', $url, $matches) && $method === 'DELETE'):
            $id = $matches[1];
            $result=$ClientController->delete($id);
            break;

            case (preg_match('/^client\/image\/(\d+)$/', $url, $matches) && $method === 'GET'):
              $id = $matches[1]; 
              $result=$ClientController->ShowImage($id);
            break;
          


//membre

       case ($url === 'membre' && $method === 'GET'):
        $result=$membreController->index(); 
       break;
  
       case (preg_match('/^membre\/(\d+)$/', $url, $matches) && $method === 'GET'):
           // 
           $id = $matches[1];
           $result = $membreController->show($id);
           break;
       
       case ($url === 'membre' && $method === 'POST'):
         $result=$membreController->create($data);
        break;

       case (preg_match('/^membre\/(\d+)$/', $url, $matches) && $method === 'PUT'):
         $id = $matches[1];
         $result=$membreController->update($id,$data);
          break;

       case (preg_match('/^membre\/(\d+)$/', $url, $matches) && $method === 'DELETE'):
           $id = $matches[1];
           $result=$membreController->delete($id);
           break;

           case (preg_match('/^membre\/image\/(\d+)$/', $url, $matches) && $method === 'GET'):
            $id = $matches[1]; 
            $result=$membreController->ShowImage($id);
          break;     
     
// moderateur

    case ($url === 'moderateur' && $method === 'GET'):
        $result=$moderateurController->index(); 
       break;
  
       case (preg_match('/^moderateur\/(\d+)$/', $url, $matches) && $method === 'GET'):
           // 
           $id = $matches[1];
           $result = $moderateurController->show($id);
           break;
       
       case ($url === 'moderateur' && $method === 'POST'):
         $result=$moderateurController->create($data);
        break;

       case (preg_match('/^moderateur\/(\d+)$/', $url, $matches) && $method === 'PUT'):
         $id = $matches[1];
         $result=$moderateurController->update($id,$data);
          break;

       case (preg_match('/^moderateur\/(\d+)$/', $url, $matches) && $method === 'DELETE'):
           $id = $matches[1];
           $result=$moderateurController->delete($id);
           break;

           case (preg_match('/^moderateur\/image\/(\d+)$/', $url, $matches) && $method === 'GET'):
            $id = $matches[1]; 
            $result=$moderateurController->ShowImage($id);
          break;     
    
//resarvation

    case ($url === 'resarvation' && $method === 'GET'):
        $result=$resarvationController->index(); 
       break;
  
       case (preg_match('/^resarvation\/(\d+)$/', $url, $matches) && $method === 'GET'):
           // 
           $id = $matches[1];
           $result = $resarvationController->show($id);
           break;
       
       case ($url === 'resarvation' && $method === 'POST'):
         $result=$resarvationController->create($data);
        break;

       case (preg_match('/^resarvation\/(\d+)$/', $url, $matches) && $method === 'PUT'):
         $id = $matches[1];
         $result=$resarvationController->update($id,$data);
          break;

       case (preg_match('/^resarvation\/(\d+)$/', $url, $matches) && $method === 'DELETE'):
           $id = $matches[1];
           $result=$resarvationController->delete($id);
           break;

//annonce

      case ($url === 'annonce' && $method === 'GET'):
        $result=$annonceController->index(); 
       break;
  
       case (preg_match('/^annonce\/(\d+)$/', $url, $matches) && $method === 'GET'):
           // 
           $id = $matches[1];
           $result = $annonceController->show($id);
           break;
       
       case ($url === 'annonce' && $method === 'POST'):
         $result=$annonceController->create($data);
        break;

       case (preg_match('/^annonce\/(\d+)$/', $url, $matches) && $method === 'PUT'):
         $id = $matches[1];
         $result=$annonceController->update($id,$data);
          break;

       case (preg_match('/^annonce\/(\d+)$/', $url, $matches) && $method === 'DELETE'):
           $id = $matches[1];
           $result=$annonceController->delete($id);
            break;


//admin 

      case ($url === 'admin' && $method === 'GET'):
        $result=$adminController->index(); 
       break;
  
       case (preg_match('/^admin\/(\d+)$/', $url, $matches) && $method === 'GET'):
           // 
           $id = $matches[1];
           $result = $adminController->show($id);
           break;
       
       case ($url === 'admin' && $method === 'POST'):
         $result=$adminController->create($data);
        break;

       case (preg_match('/^admin\/(\d+)$/', $url, $matches) && $method === 'PUT'):
         $id = $matches[1];
         $result=$adminController->update($id,$data);
          break;

       case (preg_match('/^admin\/(\d+)$/', $url, $matches) && $method === 'DELETE'):
           $id = $matches[1];
           $result=$adminController->delete($id);
           break;
           case (preg_match('/^admin\/image\/(\d+)$/', $url, $matches) && $method === 'GET'):
            $id = $matches[1]; 
            $result=$adminController->ShowImage($id);
          break;     

// favoris
    
    case ($url === 'favoris' && $method === 'GET'):
        $result=$favorisController->index(); 
       break;
  
       case (preg_match('/^favoris\/(\d+)$/', $url, $matches) && $method === 'GET'):
           // 
           $id = $matches[1];
           $result = $favorisController->show($id);
           break;
       
       case ($url === 'favoris' && $method === 'POST'):
         $result=$favorisController->create($data);
        break;

       case (preg_match('/^favoris\/(\d+)$/', $url, $matches) && $method === 'PUT'):
         $id = $matches[1];
         $result=$favorisController->update($id,$data);
          break;

       case (preg_match('/^favoris\/(\d+)$/', $url, $matches) && $method === 'DELETE'):
           $id = $matches[1];
           $result=$favorisController->delete($id);
           break;
      
     

//boost

    case ($url === 'boost' && $method === 'GET'):
        $result=$boostController->index(); 
       break;
  
       case (preg_match('/^boost\/(\d+)$/', $url, $matches) && $method === 'GET'):
           // 
           $id = $matches[1];
           $result = $boostController->show($id);
           break;
       
       case ($url === 'boost' && $method === 'POST'):
         $result=$boostController->create($data);
        break;

       case (preg_match('/^boost\/(\d+)$/', $url, $matches) && $method === 'PUT'):
         $id = $matches[1];
         $result=$boostController->update($id,$data);
          break;

       case (preg_match('/^boost\/(\d+)$/', $url, $matches) && $method === 'DELETE'):
           $id = $matches[1];
           $result=$boostController->delete($id);
           break;

           case (preg_match('/^boost\/image\/(\d+)$/', $url, $matches) && $method === 'GET'):
            $id = $matches[1]; 
            $result=$boostController->ShowImage($id);
          break;


//contact
     
    case ($url === 'contact' && $method === 'GET'):
        $result=$contactController->index(); 
       break;
  
       case (preg_match('/^contact\/(\d+)$/', $url, $matches) && $method === 'GET'):
           // 
           $id = $matches[1];
           $result = $contactController->show($id);
           break;
       
       case ($url === 'contact' && $method === 'POST'):
         $result=$contactController->create($data);
        break;

       case (preg_match('/^contact\/(\d+)$/', $url, $matches) && $method === 'PUT'):
         $id = $matches[1];
         $result=$contactController->update($id,$data);
          break;

       case (preg_match('/^contact\/(\d+)$/', $url, $matches) && $method === 'DELETE'):
           $id = $matches[1];
           $result=$contactController->delete($id);
           break;


//images   

    case ($url === 'images' && $method === 'GET'):
        $result=$imagesController->index(); 
       break;
  
       case (preg_match('/^images\/(\d+)$/', $url, $matches) && $method === 'GET'):
           // 
           $id = $matches[1];
           $result = $imagesController->show($id);
           break;
       
       case ($url === 'images' && $method === 'POST'):
         $result=$imagesController->create($data);
        break;

       case (preg_match('/^images\/(\d+)$/', $url, $matches) && $method === 'PUT'):
         $id = $matches[1];
         $result=$imagesController->update($id,$data);
          break;

       case (preg_match('/^images\/(\d+)$/', $url, $matches) && $method === 'DELETE'):
           $id = $matches[1];
           $result=$imagesController->delete($id);
           break;

     
        

    default:
        http_response_code(404);
        $result =['error'=>'page not found '];
        break;
}


// 
echo json_encode($result);
