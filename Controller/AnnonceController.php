<?php
require_once 'Controller.php';
require_once 'Services/Collection.php';
require_once 'Services/Resource.php';
require_once 'Services/Validator.php';
require_once 'Services/UploadVideo.php';
require_once 'Services/Filter.php';
require_once 'Services/auth.php';

class AnnonceController  extends Controller
{
  // cache 
  //queue 
  //update image 
  //connection in create
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
      //
      $data = $query ? $this->annonce->whereannonce(Filter::Filterquery($query, 'annonce'))
        : $this->annonce->allannonce();
      //
      return [
        'status' => 'success',
        'message' => 'Data retrieved successfully',
        'data' => Collection::returnAnnounces($data)
      ];
    } catch (Exception $e) {
      //
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
      if (array_key_exists($data['categorie_an'], $this->allowedAction)) {
        $action = $this->allowedAction[$data['categorie_an']];
      } else {
        $action = ['res', 'cont'];
      }
      //
      http_response_code(200);
      return [
        'status' => 'success',
        'message' => 'Data retrieved successfully',
        'data' => Resource::ReturnAnnonce($data) + ['actions' => $action] + ['allowed' => false, 'liked' => false]
      ];
    } catch (Exception $e) {
      // 
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
      //authentication role 
      $auth = new Auth();
      $decode = $auth->checkRole(['membre']);
      //validation of data and files
      $valid = new Validator();
      $data = $valid->validateData($data, 'annonce');
      //
      $valid::ValideImage($data['image']);
      //
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
      //
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
      $ann = $this->annonce->find($id, 'id_an');
      //authorize
      if ($user['sub'] !== $ann['id_m']) {
        throw new Exception('this membre is not allowed to edite');
      }
      //
      $valide = new Validator();
      $data = $valide->validateData($data, 'updateannonce');
      // resource 
      $data = Resource::UpdateAnnonce($data);
      // Update operation
      if (empty($data)) {
        throw new Exception('empty data given');
      }
      $this->annonce->update($id, $data, 'id_an');
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
      $ann = $this->annonce->find($id, 'id_an');
      //authorize
      if ($user['sub'] !== $ann['id_m']) {
        throw new Exception('this membre is not allowed to edite');
      }
      //
      $this->annonce->delete($id, 'id_an');
      // Return success response
      return [
        'status' => 'success',
        'message' => 'Data deleted successfully'
      ];
    } catch (Exception  $e) {
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
          'name_ar' => $info['ar'], // Arabic name field
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

      return [
        'status' => 'success',
        'message' => 'Data retrieved successfully',
        'data' => array_values($categories),
      ];
    } catch (Exception $e) {
      // Handle exceptions
      http_response_code(500);
      return [
        'status' => 'error',
        'message' => 'An error occurred while fetching data',
      ];
    }
  }


  public function showvip($query = null)
  {
    if (isset($query['page'])) {
      $page = $query['page'];
      unset($query['page']);
    } else {
      $page = 1;
    }
    try {
      //
      $info = $query ? $this->annonce->whereVip(Filter::Filterquery($query, 'annonce'), $page)
        : $this->annonce->allvip($page);
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
      //
      http_response_code(500);
      return [
        'status' => 'error',
        'message' => "Can't fetch data",
      ];
    }
  }

  public function showgold($query = null)
  {
    if (isset($query['page'])) {
      $page = $query['page'];
      unset($query['page']);
    } else {
      $page = 1;
    }
    try {
      //
      $info = $query ? $this->annonce->whereGold(Filter::Filterquery($query, 'annonce'), $page)
        : $this->annonce->allboost($page);
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
      //
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
      // Find annonce
      $data = $this->annonce->findannonce($id);
      //
      session_start();
      $sessionKey = "visited_{$id}_{$user['sub']}";
      //
      if (!isset($_SESSION[$sessionKey])) {
        $this->annonce->updateVisite($id);
        $_SESSION[$sessionKey] = true;
      }
      // Authorization
      $allow = (bool) ($data['id_m'] == $user['sub']) && ($user['role'] == 'membre');
      //actions
      if (array_key_exists($data['categorie_an'], $this->allowedAction)) {
        $action = $this->allowedAction[$data['categorie_an']];
      } else {
        $action = ['res', 'cont'];
      }
      //liked 
      if ($user['role'] == 'client') {
        $favorisList = $this->favoris->findfavoris($user['sub'], 'id_c');
      } else {
        $favorisList = $this->favoris->findfavoris($user['sub'], 'id_m');
      }
      $favorisValues = array_column($favorisList, 'id_c');
      $liked = in_array($user['sub'], $favorisValues);
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
      //authnetication
      $auth = new Auth();
      $user = $auth->authMiddleware();
      // create new favoris  or delete 
      if ($user['role'] == 'client') {
        try {
          $this->favoris->delete($user['sub'], 'id_c');
        } catch (Exception) {
          $this->favoris->create([
            'id_c' => $user['sub'],
            'id_an' => $id
          ]);
        }
      } else {
        try {
          $this->favoris->delete($user['sub'], 'id_m');
        } catch (Exception) {
          $this->favoris->create([
            'id_m' => $user['sub'],
            'id_an' => $id
          ]);
        }
      }
      // update likes 
      try {
        $annonce = $this->favoris->findall($id, 'id_an');
        $this->annonce->update($id, [
          'jaime' => count($annonce),
        ], 'id_an');
      } catch (Exception) {
        $this->annonce->update($id, [
          'jaime' => 0,
        ], 'id_an');
      }
      //return 
      http_response_code(200);
      return [
        'status' => 'success',
        'message' => 'Like updated successfully',
      ];
    } catch (Exception $e) {
      http_response_code(500);
      return [
        'status' => 'error',
        'message' => "Can't Access This Resource"
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
    if (isset($query['page'])) {
      $page = $query['page'];
      unset($query['page']);
    } else {
      $page = 1;
    }
    try {
      //
      $info = $this->annonce->Searchannonce($word, $page);
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
