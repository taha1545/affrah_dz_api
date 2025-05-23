<?php

require_once 'Resource.php';

// group of resources 

class Collection
{

    // CLIENTS
    public static function returnClients($data)
    {
        $clients = [];
        foreach ($data as $clientData) {
            $clients[] = Resource::ReturnClient($clientData);
        }
        return $clients;
    }

    public static function returnImages($data)
    {
        $images = [];
        foreach ($data as $image) {
            $images[] = Resource::ReturnImages($image);
        }
        return $images;
    }

    // ANNOUNCES
    public static function returnAnnounces($data)
    {
        $announces = [];
        foreach ($data as $announceData) {
            $boostType = $announceData['type_b'] ?? null;
            $visites = (int) ($announceData['visites'] ?? 0);
            $jaime = (int) ($announceData['jaime'] ?? 0);

            // 
            if ($boostType === 'gold') {
                $rating = 4 + ($jaime * 0.05) + ($visites * 0.002);
            } elseif ($boostType === 'silver') {
                $rating = 3 + ($jaime * 0.05) + ($visites * 0.002);
            } else {
                $rating = 0 + ($jaime * 0.02) + ($visites * 0.001);
            }

            //
            $rating = round(min($rating, 5), 2);

            $announces[] = [
                'id' => (int) $announceData['id_an'],
                'name' => $announceData['nom_an'],
                'category' => $announceData['categorie_an'],
                'eventType' => $announceData['type_fete'],
                'city' => $announceData['ville_an'],
                'address' => $announceData['adresse_an'],
                'price' => (float) $announceData['tarif_an'],
                'image_full_path' => $announceData['file_path'],
                'type' => $boostType,
                'date' => $announceData['date_cr'],
                'rating' => $rating,
            ];
        }

        return $announces;
    }



    // BOOSTS
    public static function returnBoosts($data)
    {
        $boosts = [];
        foreach ($data as $boostData) {
            $boosts[] = Resource::ReturnBoost($boostData);
        }
        return $boosts;
    }

    // CONTACTS
    public static function returnContacts($data)
    {
        $contacts = [];
        foreach ($data as $contactData) {
            $contacts[] = Resource::ReturnContact($contactData);
        }
        return $contacts;
    }

    // FAVORITES
    public static function returnFavorites($data)
    {
        $favorites = [];
        foreach ($data as $favoriteData) {
            $favorites[] = Resource::ReturnFavorite($favoriteData);
        }
        return $favorites;
    }

    // RESERVATIONS
    public static function returnReservations($data)
    {
        $reservations = [];
        foreach ($data as $reservationData) {
            $reservations[] = Resource::ReturnReservation($reservationData);
        }
        return $reservations;
    }

    // MEMBERS
    public static function returnMembers($data)
    {
        $members = [];
        foreach ($data as $memberData) {
            $members[] = Resource::ReturnMembre($memberData);
        }
        return $members;
    }
}
