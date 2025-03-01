<?php
require_once 'Controller.php';
require_once 'Services/Collection.php';
require_once 'Services/Resource.php';
require_once 'Services/Validator.php';
require_once 'Services/UploadVideo.php';
require_once 'Services/Filter.php';
require_once 'Services/auth.php';

// index = fetch all  annonces
// show = fetch annonce
//create and update and delete annonce
// showcategorie = show all categorie that existe 
//showvip = show annonce that have gold boost
// show gold = show annonces that have silver boost
//visite = like show but it update visisites (seen)
// like = like annonce and dislike 
//myannonce =  show all my annonce
// myfavoris = show all my favoris annonce
// search  =  search in annonce
// annoncebymembre =  show all annonce from this membre 
// allowed action is array of each categoire and allowed action for client to do (resarve or contact)


class AnnonceController  extends Controller
{


  private  $allowedAction = [
    'Costumes' => ['res', 'cont'],
    'Automobiles' => ['res'],
    'Dj' => ['res'],
    'Negafats' => ['res'],
    'Kaftan' => ['res', 'cont'],
    'Salle des fetes' => ['res'],
    'Groupes de motards' => ['res'],
    'Bonbons et gâteaux de mariage' => ['cont'],
    'Sévérité et robes' => ['res'],
    'Groupes de musique' => ['res'],
    'Chef professionnel' => ['res', 'cont'],
    'Appareil photo et photographie' => ['res'],
    'Costumes du marié' => ['res'],
    'Coiffure et beauté' => ['res'],
    'Décoration et fleurs' => ['res', 'cont'],
    'Hôtels et chambres pour la nuit de noces' => ['res'],
    'Burnous et chevaux' => ['res'],
    'Organisateur de fêtes' => ['res'],
    'Pas de groupe de photographie' => ['res'],
  ];

  public function index($query = null)
  {
    try {
      $data = $query ? $this->annonce->whereannonce(Filter::Filterquery($query, 'annonce'))
        : $this->annonce->allannonce();
      //
      return [
        'status' => 'success',
        'message' => 'Data retrieved successfully',
        'data' => Collection::returnAnnounces($data)
      ];
    } catch (Exception $e) {
      http_response_code(500);
      return [
        'status' => 'error',
        'message' => 'Empty Data',
      ];
    }
  }

  public function show($id)
  {
    try {
      // find annonce
      $data = $this->annonce->findannonce($id);
      //actions 
      $action = $this->allowedAction[$data['categorie_an']] ?? ['res', 'cont'];
      // return responce
      http_response_code(200);
      return [
        'status' => 'success',
        'message' => 'Data retrieved successfully',
        'data' => Resource::ReturnAnnonce($data) + ['actions' => $action, 'allowed' => false, 'liked' => false]
      ];
    } catch (Exception) {
      http_response_code(500);
      return [
        'status' => 'error',
        'message' => 'Data Not Found',
      ];
    }
  }

  public function create($data)
  {
    try {
      //auth to get data and check role membre
      $auth = new Auth();
      $decode = $auth->checkRole(['membre']);

      //validation 
      $valid = new Validator();
      $data = $valid->validateData($data, 'annonce');
      $valid::ValideImage($data['image']);
      foreach ($data['images'] as $file) {
        $valid::ValideImage($file);
      }
      if (isset($data['video'])) {
        $valid::ValideVideo(video: $data['video']);
      }

      //create image and video
      $data['image'] = UploadVideo::CreateImage($data['image']);
      if (isset($data['video'])) {
        $data['video'] = UploadVideo::CreateVideo($data['video']);
      }

      //resource
      $oldimages = $data['images'];
      $data['idMember'] = $decode['sub'];
      $data = Resource::GetAnnonce($data);

      //create annonce
      $id_an = $this->annonce->create($data);

      //create images 
      foreach ($oldimages as $file) {
        $images[] = Resource::GetAlotImages(UploadVideo::CreateImage($file), $id_an);
      }
      $this->images->bulkcreate($images);

      //return true 
      http_response_code(201);
      return [
        'status' => 'success',
        'message' => 'Data Created successfully',
      ];
    } catch (Exception $e) {
      $errorms = json_decode($e->getMessage()) ?? $e->getMessage();
      return [
        'status' => 'error',
        'message' => $errorms,
      ];
    }
  }

  public function update($id, $data)
  {
    try {
      //authentication role 
      $auth = new Auth();
      $user = $auth->checkRole(['membre']);
      // find annonce
      $ann = $this->annonce->find($id, 'id_an');
      //authorize
      if ($user['sub'] !== $ann['id_m']) {
        throw new Exception('this membre is not allowed to edite');
      }
      // validation
      $valide = new Validator();
      $data = $valide->validateData($data, 'updateannonce');
      // resource 
      $data = Resource::UpdateAnnonce($data);
      // Update operation
      if (isset($data)) {
        $this->annonce->update($id, $data, 'id_an');
      }
      // Return success response
      http_response_code(200);
      return [
        'status' => 'success',
        'message' => 'Data updated successfully'
      ];
    } catch (Exception) {
      return [
        'status' => 'error',
        'message' => "Can't Update Data"
      ];
    }
  }


  public function delete($id)
  {
    try {
      //authentication role 
      $auth = new Auth();
      $user = $auth->checkRole(['membre']);
      // find annonce
      $ann = $this->annonce->find($id, 'id_an');
      //authorize
      if ($user['sub'] !== $ann['id_m']) {
        throw new Exception('this membre is not allowed to edite');
      }
      // delete annonce
      $this->annonce->delete($id, 'id_an');
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
        'message' => "Can't Delete This !"
      ];
    }
  }


  public function showcategorie()
  {
    try {
      // Define allowed categories with their corresponding images and Arabic names
      $catg = [
        'Salle des fetes' => ['image' => '/catg/3.png', 'ar' => 'قاعة الحفـلات'],
        'Organisateur de fêtes' => ['image' => '/catg/Organisateurdefêtes.jpg', 'ar' => 'منظم الحفلات'],
        'Dj' => ['image' => '/catg/Dj.jpg', 'ar' => 'دي جـي'],
        'Costumes' => ['image' => '/catg/Costumes.jpg', 'ar' => 'بدلات العريـس'],
        'Hôtels et chambres pour la nuit de noces' => ['image' => '/catg/Hôtelsetchambrespourlanuitdenoces.jpg', 'ar' => 'فنـادق و غرف ليلة الزفـاف'],
        'Groupes de musique' => ['image' => '/catg/Groupesdemusique.jpg', 'ar' => 'المجموعات الموسيـقـيـة'],
        'Chef professionnel' => ['image' => '/catg/Chefprofessionnel.jpg', 'ar' => 'طبـاخ محتـرف'],
        'Automobiles' => ['image' => '/catg/Automobiles.jpg', 'ar' => 'كـراء السيـارات'],
        'Negafats' => ['image' => '/catg/Negafats.jpg', 'ar' => 'نقافات'],
        'Kaftan' => ['image' => '/catg/Kaftan.jpg', 'ar' => 'الشـدة و الفساتيـن'],
        'Groupes de motards' => ['image' => '/catg/Groupesdemotards.jpg', 'ar' => 'مجموعات الدراجات النارية'],
        'Bonbons et gâteaux de mariage' => ['image' => '/catg/Bonbonsetgâteauxdemariage.jpg', 'ar' => 'حلويـات و كعك الأعراس'],
        'Sévérité et robes' => ['image' => '/catg/Sévéritéetrobes.jpg', 'ar' => 'الشـدة و الفساتيـن'],
        'Appareil photo et photographie' => ['image' => '/catg/Appareilphotoetphotographie.jpg', 'ar' => 'الكاميـرا و الفوتوغرافيـا'],
        'Costumes du marié' => ['image' => '/catg/Costumesdumarié.jpg', 'ar' => 'بدلات العريـس'],
        'Coiffure et beauté' => ['image' => '/catg/Coiffureetbeauté.jpg', 'ar' => 'حلاقـة و تجميـل'],
        'Décoration et fleurs' => ['image' => '/catg/Décorationetfleurs.jpg', 'ar' => 'ديكـور و ورود'],
        'Burnous et chevaux' => ['image' => '/catg/Burnousetchevaux.jpg', 'ar' => 'برنـوس و أحصـنة'],
        'Pas de groupe de photographie' => ['image' => '/catg/Pasdegroupedephotographie.jpg', 'ar' => 'فرقـة ممنـوع التصـويـر'],
      ];
      // Fetch all categories from the database
      $data = $this->annonce->allcategorie();
      // Initialize categories with zero count
      $categories = [];
      foreach ($catg as $name => $info) {
        $categories[$name] = [
          'name' => $name,
          'name_ar' => $info['ar'],
          'number' => 0,
          'image' => $info['image'],
        ];
      }
      // Update counts for categories found in the database
      foreach ($data as $item) {
        if (isset($categories[$item['categorie_an']])) {
          $categories[$item['categorie_an']]['number'] = (int) $item['count'];
        }
      }
      //
      return [
        'status' => 'success',
        'message' => 'Data retrieved successfully',
        'data' => array_values($categories),
      ];
      //
    } catch (Exception $e) {
      http_response_code(500);
      return [
        'status' => 'error',
        'message' => 'An error occurred while fetching data',
      ];
    }
  }

  public function showvip($query = null)
  {
    $page = $query['page'] ?? 1;
    unset($query['page']);
    //
    try {
      // get gold
      $info = $query ? $this->annonce->whereVip(Filter::Filterquery($query, 'annonce'), $page)
        : $this->annonce->allvip($page);
      // resource
      $data = $info['data'];
      $paginate = $info['paginate'];
      //
      return [
        'status' => 'success',
        'message' => 'data retrieved successfully',
        'data' => Collection::returnAnnounces($data),
        'info' => $paginate
      ];
    } catch (Exception $e) {
      http_response_code(500);
      return [
        'status' => 'error',
        'message' => "Can't fetch data",
      ];
    }
  }

  public function showgold($query = null)
  {
    $page = $query['page'] ?? 1;
    unset($query['page']);
    //
    try {
      // get silver annonce
      $info = $query ? $this->annonce->whereGold(Filter::Filterquery($query, 'annonce'), $page)
        : $this->annonce->allboost($page);
      //
      $data = $info['data'];
      $paginate = $info['paginate'];
      //
      return [
        'status' => 'success',
        'message' => 'data retrieved successfully',
        'data' => Collection::returnAnnounces($data),
        'info' => $paginate
      ];
    } catch (Exception $e) {
      http_response_code(500);
      return [
        'status' => 'error',
        'message' => "Can't fetch data",
      ];
    }
  }

  public function visite($id)
  {
    try {
      // Authentication
      $auth = new Auth();
      $user = $auth->authMiddleware();
      $userId = $user['sub'];
      $userRole = $user['role'];
      // Find annonce
      $data = $this->annonce->findannonce($id);
      // start session   
      if (session_status() === PHP_SESSION_NONE) {
        session_start();
      }
      $sessionKey = "visited_{$id}_{$user['sub']}";
      // test session
      if (!isset($_SESSION[$sessionKey])) {
        $this->annonce->updateVisite($id);
        $_SESSION[$sessionKey] = true;
      }
      // Authorization
      $allow = (bool) ($data['id_m'] == $user['sub']) && ($user['role'] == 'membre');
      //actions
      $action = $this->allowedAction[$data['categorie_an']] ?? ['res', 'cont'];
      //liked 
      $favorisKey = $userRole === 'client' ? 'id_c' : 'id_m';
      $favorisList = $this->favoris->findfavoris($userId, $favorisKey);
      $favorisValues = array_column($favorisList, 'id_an');
      $liked = in_array($id, $favorisValues);
      // Return response
      http_response_code(200);
      return [
        'status' => 'success',
        'message' => 'Data retrieved successfully',
        'data' => Resource::ReturnAnnonce($data) + ['allowed' => $allow] + ['actions' => $action, 'liked' => $liked]
      ];
    } catch (Exception $e) {
      http_response_code(500);
      return [
        'status' => 'error',
        'message' => "Can't Access This Resource"
      ];
    }
  }


  public function like($id)
  {
    try {
      // Authentication
      $auth = new Auth();
      $user = $auth->authMiddleware();
      $userId = $user['sub'];
      $column = ($user['role'] == 'client') ? 'id_c' : 'id_m';
      // Check if the user already liked the annonce
      if ($this->favoris->exists_like($userId, $column, $id)) {
        $this->favoris->delete_like($userId, $column, $id);
      } else {
        $this->favoris->create([
          $column => $userId,
          'id_an' => $id
        ]);
      }
      // Update like count
      $this->favoris->updateLikeCount($id);
      // Return response
      http_response_code(200);
      return [
        'status' => 'success',
        'message' => 'Like updated successfully',
      ];
    } catch (Exception $e) {
      http_response_code(500);
      return [
        'status' => 'error',
        'message' => "An error occurred while updating like."
      ];
    }
  }


  public function myannonce()
  {
    try {
      //authnetication
      $auth = new Auth();
      $user = $auth->checkRole(['membre']);
      // get
      $annonce = $this->annonce->findallannonce($user['sub']);
      // return
      return [
        'status' => 'success',
        'message' => 'data retirved seccsefly',
        'data' =>  Collection::returnAnnounces($annonce)
      ];
    } catch (Exception) {
      http_response_code(500);
      return [
        'status' => 'error',
        'message' => 'No Data Found',
      ];
    }
  }

  public function myfavoris()
  {
    try {
      //authnetication
      $auth = new Auth();
      $user = $auth->checkRole(['membre', 'client']);
      // get 
      if ($user['role'] == 'client') {
        $data = $this->annonce->allannoncefavoris($user['sub'], 'id_c');
      } else {
        $data = $this->annonce->allannoncefavoris($user['sub'], 'id_m');
      }
      // return
      return [
        'status' => 'success',
        'message' => 'data retirved seccsefly',
        'data' =>  Collection::returnAnnounces($data)
      ];
    } catch (Exception) {
      http_response_code(500);
      return [
        'status' => 'error',
        'message' => "No Data Found",
      ];
    }
  }

  public function search($word, $query = null)
  {
    $page = $query['page'] ?? 1;
    unset($query['page']);
    //
    try {
      $info = $this->annonce->Searchannonce($word, $page);
      //
      $data = $info['data'];
      $paginate = $info['paginate'];
      //
      return [
        'status' => 'success',
        'message' => 'data found successfully',
        'data' => Collection::returnAnnounces($data),
        'info' => $paginate
      ];
    } catch (Exception) {
      http_response_code(500);
      return [
        'status' => 'error',
        'message' => "No Data Found"
      ];
    }
  }

  public function annoncebymembre($id)
  {
    try {
      // get
      $annonce = $this->annonce->findallannonce($id);
      // return
      return [
        'status' => 'success',
        'message' => 'data retirved seccsefly',
        'data' =>  Collection::returnAnnounces($annonce)
      ];
    } catch (Exception) {
      http_response_code(500);
      return [
        'status' => 'error',
        'message' => 'No Data Found',
      ];
    }
  }
}
