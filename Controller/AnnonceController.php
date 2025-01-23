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
  //search
  //update prblm 
  // more auth 


  public function index($query = null)
  {
    try {
      if ($query == null) {
        //
        $data = $this->annonce->allannonce();
        return [
          'status' => 'success',
          'message' => 'data retrieved successfully',
          'data' => Collection::returnAnnounces($data)
        ];
      } else {
        $condition = Filter::Filterquery($query, 'annonce');
        $data = $this->annonce->whereannonce($condition);
        return [
          'status' => 'success',
          'message' => 'data retrieved successfully',
          'data' => Collection::returnAnnounces($data)
        ];
      }
    } catch (Exception $e) {
      //
      http_response_code(404);
      return [
        'status' => 'error',
        'message' => 'An error occurred while fetching data',
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
        'message' => 'data retrieved successfully',
        'data' => Resource::ReturnAnnonce($data)
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
      return [
        'status' => 'success',
        'message' => 'data created success',
      ];
      //

    } catch (Exception $e) {
      http_response_code(404);
      $errorms = json_decode($e->getMessage()) ?? $e->getMessage();
      return [
        'status' => 'error',
        'message' => $errorms
      ];
    }
  }


  public function update($id, $data)
  {
    try {
      //authentication role 
      $auth = new Auth();
      $user = $auth->checkRole(['membre']);
      //
      $valide = new Validator();
      $data = $valide->validateData($data, 'updateannonce');
      // resource 
      $data = Resource::UpdateAnnonce($data);
      // Update operation
      if ($data !== []) {
        $this->annonce->update($id, $data, 'id_an');
      }
      // Return success response
      return [
        'status' => 'success',
        'message' => 'Data updated successfully'
      ];
    } catch (Exception $e) {
      http_response_code(404);
      $errorms = json_decode($e->getMessage()) ?? $e->getMessage();
      return [
        'status' => 'error',
        'message' => $errorms
      ];
    }
  }


  public function delete($id)
  {
    try {
      //authentication role 
      $auth = new Auth();
      $user = $auth->checkRole(['membre']);
      //
      $this->annonce->delete($id, 'id_an');
      // Return success response
      return [
        'status' => 'success',
        'message' => 'Data deleted successfully'
      ];
    } catch (Exception  $e) {
      // Handle error
      http_response_code(404);
      return [
        'status' => 'error',
        'message' => $e->getMessage()
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
        return [
          'name' => $item['categorie_an'],
          'number' => (int) $item['count'],
          'image' => "api/upload/catg.png",
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
      http_response_code(404);
      return [
        'status' => 'error',
        'message' => 'An error occurred while fetching data',
      ];
    }
  }

  public function showvip()
  {
    try {
      //
      $data = $this->annonce->allvip();
      return [
        'status' => 'success',
        'message' => 'data retrieved successfully',
        'data' => Collection::returnAnnounces($data)
      ];
    } catch (Exception $e) {
      //
      http_response_code(404);
      return [
        'status' => 'error',
        'message' => 'An error occurred while fetching data',
      ];
    }
  }

  public function showgold()
  {
    try {
      //
      $data = $this->annonce->allboost();
      return [
        'status' => 'success',
        'message' => 'data retrieved successfully',
        'data' => Collection::returnAnnounces($data)
      ];
    } catch (Exception $e) {
      //
      http_response_code(404);
      return [
        'status' => 'error',
        'message' => 'An error occurred while fetching data',
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
      http_response_code(404);
      return [
        'status' => 'error',
        'message' => $e->getMessage()
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
      return [
        'status' => 'success',
        'message' => 'like updated successfully',
      ];
    } catch (Exception $e) {
      http_response_code(404);
      return [
        'status' => 'error',
        'message' => $e->getMessage()
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
      http_response_code(404);
      return [
        'status' => 'error',
        'message' => 'error while trynig to get annonces',
      ];
    }
  }

  public function myfavoris() {
    try {
      //authnetication
      $auth = new Auth();
      $user = $auth->checkRole(['membre','client']);
      // get 
       if( $user['role'] == 'client'){
            $data=$this->annonce->allannoncefavoris($user['sub'],'id_c');
       }else{
        $data=$this->annonce->allannoncefavoris($user['sub'],'id_m');
       }
      // return
      return [
        'status' => 'success',
        'message' => 'data retirved seccsefly',
        'data' =>  Collection::returnAnnounces($data)
      ];
    } catch (Exception $e) {
      http_response_code(404);
      return [
        'status' => 'error',
        'message' => $e->getMessage(),
      ];
    }
  }


}
