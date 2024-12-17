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
            'id_mo' => $data['idModerateur']
        ];
    }

    public static function ReturnClient($data){
        $data = (array) $data;
        return [
            'name' => $data['nom_c'],
            'wilaya' => $data['ville_c'],
            'age' => $data['age_c'],
            'email' => $data['email_c'],
            'phone' => $data['tel_c'],
            'password' => $data['mdp_c'],
            'etat' => $data['etat_c'],
            'banned' => $data['signale'],
            'idAdmin' => $data['id_a'],
            'idModerateur' => $data['id_mo']
        ];
    }

    // ADMIN
    public static function GetAdmin($data){
        return [
            'nom_a' => $data['name'],
            'email_a' => $data['email'],
            'tel_c' => $data['phone'],
            'mdp_a' =>  password_hash($data['password'], PASSWORD_BCRYPT)
        ];
    }

    public static function ReturnAdmin($data){
        $data = (array) $data;
        return [
            'name' => $data['nom_a'],
            'email' => $data['email_a'],
            'phone' => $data['tel_c'],
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
            'name' => $data['nom_an'],
            'category' => $data['categorie_an'],
            'eventType' => $data['type_fete'],
            'city' => $data['ville_an'],
            'address' => $data['adresse_an'],
            'creationDate' => $data['date_cr'],
            'phone' => $data['tel_an'],
            'mobile' => $data['mobile_an'],
            'price' => $data['tarif_an'],
            'details' => $data['detail_an'],
            'etat' => $data['etat_an'],
            'idAdmin' => $data['id_a'],
            'idModerateur' => $data['id_mo'],
            'idMember' => $data['id_m'],
            'pricingNature' => $data['nature_tarif'],
            'visits' => $data['visites'],
            'likes' => $data['jaime']
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
            'duration' => $data['duree_b'],
            'price' => $data['tarif_b'],
            'etat' => $data['etat_b'],
            'idMember' => $data['id_m'],
            'idAnnonce' => $data['id_an'],
            'creationDate' => $data['date_cr_b'],
            'idModerateur' => $data['id_mo']
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
            'name' => $data['nom'],
            'email' => $data['email'],
            'message' => $data['msg'],
            'phone' => $data['tel'],
            'subject' => $data['sujet'],
            'genre' => $data['genre'],
            'idMember' => $data['id_m'],
            'idClient' => $data['id_c'],
            'idModerateur' => $data['id_mo']
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
            'idAnnonce' => $data['id_an'],
            'idClient' => $data['id_c'],
            'idMember' => $data['id_m']
        ];
    }

        // MODERATEUR
        public static function GetModerateur($data){
         return [
             'nom_mo' => $data['name'],
             'email_mo' => $data['email'],
             'tel_mo' => $data['phone'],
             'mdp_mo' => password_hash($data['password'], PASSWORD_BCRYPT)
         ];
     }
 
     public static function ReturnModerateur($data){
         $data = (array) $data;
         return [
             'name' => $data['nom_mo'],
             'email' => $data['email_mo'],
             'phone' => $data['tel_mo'],
             'password' => $data['mdp_mo']
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
             'reservationDate' => $data['date_res'],
             'numberOfGuests' => $data['nbr_invite'],
             'etat' => $data['etat_res'],
             'idClient' => $data['id_c'],
             'idMember' => $data['id_m'],
             'idAnnonce' => $data['id_an']
         ];
     }
 
     // MEMBRE
     public static function GetMembre($data){
         return [
             'nom_m' => $data['name'],
             'email_m' => $data['email'],
             'tel_m' => $data['phone'],
             'mdp_m' => password_hash($data['password'], PASSWORD_BCRYPT), 
             'etat_m' => $data['etat'],
             'id_a' => $data['idAdmin'],
             'signale' => $data['banned']
         ];
     }
 
     public static function ReturnMembre($data){
         $data = (array) $data;
         return [
             'name' => $data['nom_m'],
             'email' => $data['email_m'],
             'phone' => $data['tel_m'],
             'password' => $data['mdp_m'],
             'etat' => $data['etat_m'],
             'idAdmin' => $data['id_a'],
             'banned' => $data['signale']
         ];
     }
 

 

}
