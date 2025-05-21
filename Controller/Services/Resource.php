<?php


class Resource
{

    public static function GetClient($data)
    {
        return [
            'nom_c' => $data['name'],
            'ville_c' => $data['wilaya'],
            'age_c' => $data['age'] ?? 22,
            'email_c' => $data['email'],
            'tel_c' => $data['phone'],
            'mdp_c' => password_hash($data['password'], PASSWORD_BCRYPT),
            'etat_c' => "valide",
            'signale' => "non",
            'id_a' => 1,
            'id_mo' => 1,
            'photo_c' => isset($data['image']['tmp_name']) && is_file($data['image']['tmp_name'])
                ? file_get_contents($data['image']['tmp_name'])
                : null,
            'fcm_token' => $data['fcm'] ?? null,
        ];
    }
    public static function UpdateClient($data)
    {
        $newdata = [
            'nom_c' => $data['name'] ?? null,
            'ville_c' => $data['wilaya'] ?? null,
            'age_c' => $data['age'] ?? null,
            'tel_c' => $data['phone'] ?? null,
            'mdp_c' => isset($data['password']) ? password_hash($data['password'], PASSWORD_BCRYPT) : null,
            'fcm_token' => $data['fcm'] ?? null
        ];

        // Filter out null values
        $newdata = array_filter($newdata, function ($value) {
            return $value !== null;
        });

        return $newdata;
    }

    public static function ReturnClient($data)
    {
        $data = (array) $data;
        return [
            'id' => (int)$data['id_c'],
            'name' => $data['nom_c'],
            'wilaya' => $data['ville_c'],
            'age' => (int)$data['age_c'],
            'email' => $data['email_c'],
            'phone' => $data['tel_c'],
        ];
    }

    public static function GetAnnonce($data)
    {
        return [
            'nom_an' => $data['name'],
            'categorie_an' => $data['category'],
            'type_fete' => $data['eventType'],
            'ville_an' => $data['city'],
            'adresse_an' => $data['address'],
            'date_cr' =>  date("Y-m-d H:i:s", time()),
            'tel_an' => $data['phone'] ?? null,
            'mobile_an' => $data['mobile'],
            'tarif_an' => $data['price'] ?? 0,
            'detail_an' => $data['details'] ?? null,
            'etat_an' => $data['etat'] ?? "attente",
            'id_a' => 1,
            'id_m' => $data['idMember'],
            'nature_tarif' => $data['pricingNature'] ?? null,
            'visites' =>  0,
            'jaime' =>  0,
            'file_name' => $data['image']['name'],
            'file_path' =>  $data['image']['path'] . $data['image']['name'],
            'file_size' => $data['image']['size'],
            'file_name_video' => $data['video']['name']  ?? null,
            'file_path_video' => ($data['video']['path'] ?? null) . ($data['video']['name'] ?? null),
            'file_size_video' => $data['video']['size'] ?? null
        ];
    }

    public static function ReturnAnnonce($data)
    {
        $data = (array) $data;
        return [
            'id' => (int)$data['id_an'],
            'name' => $data['nom_an'],
            'category' => $data['categorie_an'],
            'eventType' => $data['type_fete'],
            'city' => $data['ville_an'],
            'address' => $data['adresse_an'],
            'creationDate' => $data['date_cr'],
            'phone' => $data['tel_an'],
            'mobile' => $data['mobile_an'],
            'price' => (float) $data['tarif_an'],
            'pricenature' => $data['nature_tarif'] ?? null,
            'details' => $data['detail_an'],
            'visits' => (int)$data['visites'],
            'likes' => (int) $data['jaime'],
            'idmobmre' => (int) $data['id_m'],
            'image_full_path' => $data['file_path'],
            'video_full_path' => $data['file_path_video'] ?? null,
            'boost' => $data['boost'] ? Resource::ReturnBoost($data['boost']) : [],
            'images' => $data['images'] ? Collection::ReturnImages($data['images']) : [],
        ];
    }


    public static function GetBoost($data)
    {
        return [
            'duree_b' => $data['duration'],
            'tarif_b' => $data['price'],
            'etat_b' =>  "attente",
            'id_an' => $data['idAnnonce'],
            'date_cr_b' =>  date("Y-m-d H:i:s", time()),
            'id_mo' => 1,
            'recu_b' => file_get_contents($data['image']['tmp_name']),
            'type_b' => $data['type']
        ];
    }

    public static function ReturnBoost($data)
    {
        $data = (array) $data;
        return [
            'id' => (int)$data['id_b'],
            'duration' => $data['duree_b'],
            'etat' => $data['etat_b'],
            'idMember' => (int) $data['id_m'],
            'idAnnonce' => (int)$data['id_an'],
            'creationDate' => $data['date_cr_b'],
            'type' => $data['type_b']
        ];
    }


    public static function GetContact($data)
    {
        return [
            'nom' => $data['name'] ?? null,
            'email' => $data['email'] ?? null,
            'msg' => $data['message'],
            'tel' => $data['phone'] ?? null,
            'sujet' => $data['subject'],
            'genre' => $data['genre'],
        ];
    }

    public static function ReturnContact($data)
    {
        $data = (array) $data;
        return [
            'id' => (int)$data['id'],
            'name' => $data['nom'],
            'email' => $data['email'],
            'message' => $data['msg'],
            'phone' => $data['tel'],
            'subject' => $data['sujet'],
            'genre' => $data['genre'],
            'idMember' => (int)$data['id_m'],
            'idClient' => (int)$data['id_c'],
        ];
    }


    public static function GetFavorite($data)
    {
        return [
            'id_an' => $data['idAnnonce'],
            'id_c' => $data['idClient'],
            'id_m' => $data['idMember']
        ];
    }

    public static function ReturnFavorite($data)
    {
        $data = (array) $data;
        return [
            'id' => (int)$data['id_fav'],
            'idAnnonce' => (int) $data['id_an'],
            'idClient' => (int)$data['id_c'],
            'idMember' => (int) $data['id_m']
        ];
    }


    public static function GetReservation($data)
    {
        return [
            'date_r_debut' => $data['reservationDate'],
            'date_r_fin' => $data['finalreservationDate'],
            'nom_c_r' => $data['name'],
            'email_c_r' => $data['email'],
            'tel_c_r' => $data['phone'],
            'type_fete' => $data['type'],
            'etat_r' =>  "attente",
            'date_cr' =>  date("Y-m-d H:i:s", time()),
            'id_m' => $data['idMember'],
            'id_an' => $data['idAnnonce']
        ];
    }

    public static function ReturnReservation($data)
    {
        $data = (array) $data;
        return [
            'id' => (int)$data['id_r'],
            'reservationDate' => $data['date_r_debut'],
            'finalreservationDate' => $data['date_r_fin'],
            'name' => $data['nom_c_r'],
            'email' => $data['email_c_r'],
            'phone' => $data['tel_c_r'],
            'type' => $data['type_fete'],
            'etat' => $data['etat_r'],
            'date' => $data['date_cr'],
            'idClient' => (int)$data['id_c'],
            'idMember' => (int)$data['id_m'],
            'idAnnonce' => (int)$data['id_an']
        ];
    }


    public static function GetMembre($data)
    {
        return [
            'nom_m' => $data['name'],
            'email_m' => $data['email'],
            'ville_m' => $data['wilaya'],
            'adresse_m' => $data['location'] ?? null,
            'tel_m' => $data['phone'] ?? null,
            'mobil_m ' => $data['mobail'],
            'mdp_m' => password_hash($data['password'], PASSWORD_BCRYPT),
            'etat_m' =>  "attente",
            'id_a' =>  1,
            'signale' =>  "non",
            'id_mo' => 1,
            'fcm_token' => $data['fcm'] ?? null,
            'photo_m' => file_get_contents($data['image']['tmp_name'])  ?? null,
            'code' => substr(uniqid(), 0, 7)
        ];
    }

    public static function ReturnMembre($data)
    {
        $data = (array) $data;
        return [
            'id' => (int) $data['id_m'],
            'name' => $data['nom_m'],
            'email' => $data['email_m'],
            'wilaya' => $data['ville_m'],
            'location' => $data['adresse_m'],
            'phone' => $data['tel_m'],
            'mobail' => $data['mobil_m'],
            'code' => $data['code'] ?? null,
            'code_use' => $data['code_use'] ?? 0
        ];
    }

    public static function UpdateAnnonce($data)
    {
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
            'nature_tarif' => $data['pricingNature'] ?? null,
            'visites' => $data['visits'] ?? null,
            'jaime' => $data['likes'] ?? null,
        ];

        return array_filter($newdata, fn($value) => $value !== null);
    }


    public static function UpdateBoost($data)
    {
        $newdata = [
            'duree_b' => $data['duration'] ?? null,
        ];

        return array_filter($newdata, fn($value) => $value !== null);
    }


    public static function UpdateContact($data)
    {
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

    public static function UpdateFavorite($data)
    {
        $newdata = [
            'id_an' => $data['idAnnonce'] ?? null,
            'id_c' => $data['idClient'] ?? null,
            'id_m' => $data['idMember'] ?? null,
        ];

        return array_filter($newdata, fn($value) => $value !== null);
    }

    public static function UpdateReservation($data)
    {
        $newdata = [
            'date_r_debut' => $data['reservationDate'] ?? null,
            'date_r_fin' => $data['finalreservationDate'] ?? null,
            'nom_c_r' => $data['name'] ?? null,
            'email_c_r' => $data['email'] ?? null,
            'tel_c_r' => $data['phone'] ?? null,
            'type_fete' => $data['type'] ?? null,
            'etat_r' => $data['etat'] ?? null,
        ];

        return array_filter($newdata, fn($value) => $value !== null);
    }


    public static function UpdateMembre($data)
    {
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
            'photo_m' => $data['image'] ?? null,
            'fcm_token' => $data['fcm'] ?? null
        ];

        return array_filter($newdata, fn($value) => $value !== null);
    }


    public static function  GetImages($data)
    {
        return [
            'nom_img' => $data['image']['name'],
            'taille_img' => $data['image']['size'],
            'type_img' => $data['image']['type'],
            'chemin_img' => $data['image']['path'] . $data['image']['name'],
            'date_cr' => date('y-m-d  h:m:s'),
            'id_an' => $data['idAnnonce'],
        ];
    }
    public static function  UpdateImages($data)
    {
        $newdata = [
            'nom_img' => $data['image']['name'],
            'taille_img' => $data['image']['size'],
            'type_img' => $data['image']['type'],
            'chemin_img' => $data['image']['path'] . $data['image']['name'],
            'date_cr' => date('y-m-d h:m:s'),
            'id_an' => $data['idAnnonce'] ?? null,
        ];
        // Filter out null values
        $newdata = array_filter($newdata, function ($value) {
            return $value !== null;
        });
        return $newdata;
    }

    public static function ReturnImages($data)
    {
        return [
            'id' => $data['id_img'],
            'image_path' => $data['chemin_img'],
            'idAnnonce' => $data['id_an']
        ];
    }

    public static function  GetAlotImages($data, $id)
    {
        return [
            'nom_img' => $data['name'],
            'taille_img' => $data['size'],
            'type_img' => $data['type'],
            'chemin_img' => $data['path'] . $data['name'],
            'date_cr' => date('y-m-d  h:m:s'),
            'id_an' => $id,
        ];
    }
}
