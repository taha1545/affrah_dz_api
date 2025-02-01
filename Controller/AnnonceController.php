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
  //pagination
  // cache 
  // more performance
  // rating
  //queue
  //update image 
  //limit image 
  //connection 

  public function index($query = null)
  {
    try {
      //
      $data = $query ? $this->annonce->whereannonce(Filter::Filterquery($query, 'annonce'))
        : $this->annonce->allannonce();

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
      $data = $this->annonce->find($id, 'id_an');
      //get boost
      try {
        $data['boost'] = $this->boost->find($id, "id_an");
      } catch (Exception) {
        $data['boost'] = [];
      }
      // get all images 
      try {
        $data['images'] = $this->images->findall($id, "id_an");
      } catch (Exception) {
        $data['images'] = [];
      }
      //return 
      return [
        'status' => 'success',
        'message' => 'Data retrieved successfully',
        'data' => Resource::ReturnAnnonce($data)
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
      // Fetch all categories
      $data = $this->annonce->allcategorie();
      // Extract only the values of 'categorie_an' into a single array
      $categories = array_map(function ($item) {
        $number = random_int(2, 4);
        return [
          'name' => $item['categorie_an'],
          'number' => (int) $item['count'],
          'image' => "/catg/$number.png",
        ];
      }, $data);
      //
      return [
        'status' => 'success',
        'message' => 'data retrieved successfully',
        'data' => $categories
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
    try {
      //
      $data = $query ? $this->annonce->whereVip(Filter::Filterquery($query, 'annonce'))
        : $this->annonce->allvip();
      //
      return [
        'status' => 'success',
        'message' => 'data retrieved successfully',
        'data' => Collection::returnAnnounces($data)
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
    try {
      //
      $data = $query ? $this->annonce->whereGold(Filter::Filterquery($query, 'annonce'))
        : $this->annonce->allboost();
      //
      return [
        'status' => 'success',
        'message' => 'data retrieved successfully',
        'data' => Collection::returnAnnounces($data)
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
      //authnetication
      $auth = new Auth();
      $user = $auth->authMiddleware();
      // find annonce
      $data = $this->annonce->find($id, 'id_an');
      $data['visites'] = $data['visites'] + 1;
      $viste = $data['visites'];
      //get boost
      try {
        $data['boost'] = $this->boost->find($id, "id_an");
      } catch (Exception) {
        $data['boost'] = [];
      }
      // get all images 
      try {
        $data['images'] = $this->images->findall($id, "id_an");
      } catch (Exception) {
        $data['images'] = [];
      }
      //update visite
      $this->annonce->update($id, [
        'visites' => $viste
      ], 'id_an');
      //return 
      return [
        'status' => 'success',
        'message' => 'data retrieved successfully',
        'data' => Resource::ReturnAnnonce($data)
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
      $annonce = $this->annonce->whereannonce([['annonce.id_m', '=', $user['sub']]]);
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

  public function search($word)
  {
    try {
      //
      $data = $this->annonce->Searchannonce($word);
      //
      return [
        'status' => 'success',
        'message' => 'data found successfully',
        'data' => Collection::returnAnnounces($data)
      ];
    } catch (Exception) {
      http_response_code(500);
      return [
        'status' => 'error',
        'message' => "No Data Found"
      ];
    }
  }
}
