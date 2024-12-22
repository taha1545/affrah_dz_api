<?php
  
class Resource {

    // CLIENT
    public static function GetClient($data){
        return [
            'nom_c' => $data['name'],
            'ville_c' => $data['wilaya'],
            'age_c' => $data['age'],
            'email_c' => $data['email'],
            'tel_c' => $data['phone'],
            'mdp_c' => password_hash($data['password'], PASSWORD_BCRYPT), 
            'etat_c' => $data['etat'],
            'signale' => $data['banned'],
            'id_a' => $data['idAdmin'],
            'id_mo' => $data['idModerateur'],
            'photo_c'=>$data['image'] ?? "kmsakakm"
        ];
    }
    public static function UpdateClient($data)
    {
        $newdata = [
            'nom_c' => $data['name'] ?? null,
            'ville_c' => $data['wilaya'] ?? null,
            'age_c' => $data['age'] ?? null,
            'email_c' => $data['email'] ?? null,
            'tel_c' => $data['phone'] ?? null,
            'mdp_c' => isset($data['password']) ? password_hash($data['password'], PASSWORD_BCRYPT) : null,
            'etat_c' => $data['etat'] ?? null,
            'signale' => $data['banned'] ?? null,
            'id_a' => $data['idAdmin'] ?? null,
            'id_mo' => $data['idModerateur'] ?? null,
            'photo_c' => $data['image'] ?? "kdksdsjk",
        ];
    
        // Filter out null values
        $newdata = array_filter($newdata, function ($value) {
            return $value !== null;
        });
       
        return $newdata;
    }

    public static function ReturnClient($data){
        $data = (array) $data;
        return [
            'id'=>(int)$data['id_c'],
            'name' => $data['nom_c'],
            'wilaya' => $data['ville_c'],
            'age' => (int)$data['age_c'],
            'email' => $data['email_c'],
            'phone' => $data['tel_c'],
            'password' => $data['mdp_c'],
            'etat' => $data['etat_c'],
            'banned' => $data['signale'],
            'idAdmin' => (int)$data['id_a'],
            'idModerateur' => (int)$data['id_mo']
        ];
    }

    // ADMIN
    public static function GetAdmin($data){
        return [
            'nom_a' => $data['name'],
            'email_a' => $data['email'],
            'tel_c' => $data['phone'],
            'mdp_a' =>  password_hash($data['password'], PASSWORD_BCRYPT),
            'photo_a'=>$data['image'] ?? "jhdskhsjls"
        ];
    }

    public static function ReturnAdmin($data){
        $data = (array) $data;
        return [
            'id'=>(int)$data['id_a'],
            'name' => $data['nom_a'],
            'email' => $data['email_a'],
            'phone' => $data['tel_a'],
            'password' => $data['mdp_a']
        ];
    }

    // ANNONCE
    public static function GetAnnonce($data){
        return [
            'nom_an' => $data['name'],
            'categorie_an' => $data['category'],
            'type_fete' => $data['eventType'],
            'ville_an' => $data['city'],
            'adresse_an' => $data['address'],
            'date_cr' => $data['creationDate'],
            'tel_an' => $data['phone'],
            'mobile_an' => $data['mobile'],
            'tarif_an' => $data['price'],
            'detail_an' => $data['details'],
            'etat_an' => $data['etat'],
            'id_a' => $data['idAdmin'],
            'id_mo' => $data['idModerateur'],
            'id_m' => $data['idMember'],
            'nature_tarif' => $data['pricingNature'],
            'visites' => $data['visits'],
            'jaime' => $data['likes']
        ];
    }

    public static function ReturnAnnonce($data){
        $data = (array) $data;
        return [
            'id'=>(int)$data['id_an'],
            'name' => $data['nom_an'],
            'category' => $data['categorie_an'],
            'eventType' => $data['type_fete'],
            'city' => $data['ville_an'],
            'address' => $data['adresse_an'],
            'creationDate' => $data['date_cr'],
            'phone' => $data['tel_an'],
            'mobile' => $data['mobile_an'],
            'price' =>(float) $data['tarif_an'],
            'details' => $data['detail_an'],
            'etat' => $data['etat_an'],
            'idAdmin' =>(int) $data['id_a'],
            'idModerateur' =>(int) $data['id_mo'],
            'idMember' => (int)$data['id_m'],
            'pricingNature' => $data['nature_tarif'],
            'visits' => (int)$data['visites'],
            'likes' =>(int) $data['jaime']
        ];
    }

    // BOOST
    public static function GetBoost($data){
        return [
            'duree_b' => $data['duration'],
            'tarif_b' => $data['price'],
            'etat_b' => $data['etat'],
            'id_m' => $data['idMember'],
            'id_an' => $data['idAnnonce'],
            'date_cr_b' => $data['creationDate'],
            'id_mo' => $data['idModerateur']
        ];
    }

    public static function ReturnBoost($data){
        $data = (array) $data;
        return [
            'id'=>(int)$data['id_b'],
            'duration' => $data['duree_b'],
            'price' => $data['tarif_b'],
            'etat' => $data['etat_b'],
            'idMember' =>(int) $data['id_m'],
            'idAnnonce' => (int)$data['id_an'],
            'creationDate' => $data['date_cr_b'],
            'idModerateur' =>(int) $data['id_mo']
        ];
    }

    // CONTACT
    public static function GetContact($data){
        return [
            'nom' => $data['name'],
            'email' => $data['email'],
            'msg' => $data['message'],
            'tel' => $data['phone'],
            'sujet' => $data['subject'],
            'genre' => $data['genre'],
            'id_m' => $data['idMember'],
            'id_c' => $data['idClient'],
            'id_mo' => $data['idModerateur']
        ];
    }

    public static function ReturnContact($data){
        $data = (array) $data;
        return [
            'id'=>(int)$data['id'],
            'name' => $data['nom'],
            'email' => $data['email'],
            'message' => $data['msg'],
            'phone' => $data['tel'],
            'subject' => $data['sujet'],
            'genre' => $data['genre'],
            'idMember' => (int)$data['id_m'],
            'idClient' => (int)$data['id_c'],
            'idModerateur' =>(int) $data['id_mo']
        ];
    }

    // FAVORITE
    public static function GetFavorite($data){
        return [
            'id_an' => $data['idAnnonce'],
            'id_c' => $data['idClient'],
            'id_m' => $data['idMember']
        ];
    }

    public static function ReturnFavorite($data){
        $data = (array) $data;
        return [
            'id'=>(int)$data['id_fav'],
            'idAnnonce' =>(int) $data['id_an'],
            'idClient' => (int)$data['id_c'],
            'idMember' =>(int) $data['id_m']
        ];
    }

        // MODERATEUR
        public static function GetModerateur($data){
            return [
                'nom_mo' => $data['name'],
                'prenom_mo' => $data['familyname'], 
                'email_mo' => $data['email'],
                'tel_mo' => $data['phone'],
                'mdp_mo' => password_hash($data['password'], PASSWORD_BCRYPT),
                'id_a' => $data['idAdmin'], 
                'photo_mo'=>$data['image'] ??"345676543"
            ];
        }
        
 
        public static function ReturnModerateur($data){
            $data = (array) $data;
            return [
                'id' => (int)$data['id_mo'],
                'name' => $data['nom_mo'],
                'familyname' => $data['prenom_mo'], 
                'email' => $data['email_mo'],
                'phone' => $data['tel_mo'],
                'password' => $data['mdp_mo'],
                'idAdmin' => (int)$data['id_a'], 
            ];
        }
        
 
     // RESERVATION
     public static function GetReservation($data){
         return [
             'date_res' => $data['reservationDate'],
             'nbr_invite' => $data['numberOfGuests'],
             'etat_res' => $data['etat'],
             'id_c' => $data['idClient'],
             'id_m' => $data['idMember'],
             'id_an' => $data['idAnnonce']
         ];
     }
 
     public static function ReturnReservation($data){
         $data = (array) $data;
         return [
            'id'=>(int)$data['id_r'],
             'reservationDate' => $data['date_res'],
             'numberOfGuests' => (int)$data['nbr_invite'],
             'etat' => $data['etat_res'],
             'idClient' =>(int) $data['id_c'],
             'idMember' =>(int) $data['id_m'],
             'idAnnonce' =>(int) $data['id_an']
         ];
     }
 
     // MEMBRE
     public static function GetMembre($data){
         return [
             'nom_m' => $data['name'],
             'email_m' => $data['email'],
             'ville_m'=>$data['wilaya'],
             'adresse_m'=>$data['location'],
             'tel_m' => $data['phone'],
             'mobil_m '=>$data['mobail'],
             'mdp_m' => password_hash($data['password'], PASSWORD_BCRYPT), 
             'etat_m' => $data['etat'],
             'id_a' => $data['idAdmin'],
             'signale' => $data['banned'],
             'id_mo'=>$data['idModerateur'],
             'photo_m'=>$data['image'] ?? "2345678987dfghjkjhgfd"
         ];
     }
 
     public static function ReturnMembre($data) {
        $data = (array) $data;
        return [
            'id' => (int) $data['id_m'],
            'name' => $data['nom_m'],
            'email' => $data['email_m'],
            'wilaya' => $data['ville_m'],
            'location' => $data['adresse_m'],
            'phone' => $data['tel_m'],
            'mobail' => $data['mobil_m'],
            'password' => $data['mdp_m'],
            'etat' => $data['etat_m'],
            'idAdmin' => (int) $data['id_a'],
            'banned' => $data['signale'],
            'idModerateur' => (int) $data['id_mo']
        ];
    }
    
   
     

    // ADMIN
public static function UpdateAdmin($data) {
    $newdata = [
        'nom_a' => $data['name'] ?? null,
        'email_a' => $data['email'] ?? null,
        'tel_c' => $data['phone'] ?? null,
        'mdp_a' => isset($data['password']) ? password_hash($data['password'], PASSWORD_BCRYPT) : null,
        'photo_a'=>$data['image'] ??  null 
    ];

    return array_filter($newdata, fn($value) => $value !== null);
}

// ANNONCE
public static function UpdateAnnonce($data) {
    $newdata = [
        'nom_an' => $data['name'] ?? null,
        'categorie_an' => $data['category'] ?? null,
        'type_fete' => $data['eventType'] ?? null,
        'ville_an' => $data['city'] ?? null,
        'adresse_an' => $data['address'] ?? null,
        'date_cr' => $data['creationDate'] ?? null,
        'tel_an' => $data['phone'] ?? null,
        'mobile_an' => $data['mobile'] ?? null,
        'tarif_an' => $data['price'] ?? null,
        'detail_an' => $data['details'] ?? null,
        'etat_an' => $data['etat'] ?? null,
        'id_a' => $data['idAdmin'] ?? null,
        'id_mo' => $data['idModerateur'] ?? null,
        'id_m' => $data['idMember'] ?? null,
        'nature_tarif' => $data['pricingNature'] ?? null,
        'visites' => $data['visits'] ?? null,
        'jaime' => $data['likes'] ?? null,
    ];

    return array_filter($newdata, fn($value) => $value !== null);
}

// BOOST
public static function UpdateBoost($data) {
    $newdata = [
        'duree_b' => $data['duration'] ?? null,
        'tarif_b' => $data['price'] ?? null,
        'etat_b' => $data['etat'] ?? null,
        'id_m' => $data['idMember'] ?? null,
        'id_an' => $data['idAnnonce'] ?? null,
        'date_cr_b' => $data['creationDate'] ?? null,
        'id_mo' => $data['idModerateur'] ?? null,
    ];

    return array_filter($newdata, fn($value) => $value !== null);
}

// CONTACT
public static function UpdateContact($data) {
    $newdata = [
        'nom' => $data['name'] ?? null,
        'email' => $data['email'] ?? null,
        'msg' => $data['message'] ?? null,
        'tel' => $data['phone'] ?? null,
        'sujet' => $data['subject'] ?? null,
        'genre' => $data['genre'] ?? null,
        'id_m' => $data['idMember'] ?? null,
        'id_c' => $data['idClient'] ?? null,
        'id_mo' => $data['idModerateur'] ?? null,
    ];

    return array_filter($newdata, fn($value) => $value !== null);
}

// FAVORITE
public static function UpdateFavorite($data) {
    $newdata = [
        'id_an' => $data['idAnnonce'] ?? null,
        'id_c' => $data['idClient'] ?? null,
        'id_m' => $data['idMember'] ?? null,
    ];

    return array_filter($newdata, fn($value) => $value !== null);
}

// MODERATEUR
public static function UpdateModerateur($data) {
    $newdata = [
        'nom_mo' => $data['name'],
        'prenom_mo' => $data['familyname'], 
        'email_mo' => $data['email'],
        'tel_mo' => $data['phone'],
        'mdp_mo' => password_hash($data['password'], PASSWORD_BCRYPT),
        'id_a' => $data['idAdmin'], 
        'photo_mo'=>$data['image'] ??"345676543"
    ];

    return array_filter($newdata, fn($value) => $value !== null);
}
// RESERVATION
public static function UpdateReservation($data) {
    $newdata = [
        'date_res' => $data['reservationDate'] ?? null,
        'nbr_invite' => $data['numberOfGuests'] ?? null,
        'etat_res' => $data['etat'] ?? null,
        'id_c' => $data['idClient'] ?? null,
        'id_m' => $data['idMember'] ?? null,
        'id_an' => $data['idAnnonce'] ?? null,
    ];

    return array_filter($newdata, fn($value) => $value !== null);
}

// MEMBRE
public static function UpdateMembre($data) {
    $newdata = [
        'nom_m' => $data['name'] ?? null,
        'email_m' => $data['email'] ?? null,
        'ville_m' => $data['wilaya'] ?? null,
        'adresse_m' => $data['location'] ?? null,
        'tel_m' => $data['phone'] ?? null,
        'mobil_m' => $data['mobail'] ?? null,
        'mdp_m' => isset($data['password']) ? password_hash($data['password'], PASSWORD_BCRYPT) : null,
        'etat_m' => $data['etat'] ?? null,
        'id_a' => $data['idAdmin'] ?? null,
        'signale' => $data['banned'] ?? null,
        'id_mo' => $data['idModerateur'] ?? null,
        'photo_mo'=>$data['image'] ?? null
    ];

    return array_filter($newdata, fn($value) => $value !== null);
}


}

 


