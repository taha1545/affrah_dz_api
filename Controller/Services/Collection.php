<?php
  
  require_once 'Resource.php';
  
class Collection {

    // CLIENTS
    public static function returnClients($data) {
        $clients = [];
        foreach ($data as $clientData) {
            $clients[] = Resource::ReturnClient($clientData);
        }
        return $clients;
    }

    // ADMINS
    public static function returnAdmins($data) {
        $admins = [];
        foreach ($data as $adminData) {
            $admins[] = Resource::ReturnAdmin($adminData);
        }
        return $admins;
    }

    // ANNOUNCES
    public static function returnAnnounces($data) {
        $announces = [];
        foreach ($data as $announceData) {
            $announces[] = Resource::ReturnAnnonce($announceData);
        }
        return $announces;
    }

    // BOOSTS
    public static function returnBoosts($data) {
        $boosts = [];
        foreach ($data as $boostData) {
            $boosts[] = Resource::ReturnBoost($boostData);
        }
        return $boosts;
    }

    // CONTACTS
    public static function returnContacts($data) {
        $contacts = [];
        foreach ($data as $contactData) {
            $contacts[] = Resource::ReturnContact($contactData);
        }
        return $contacts;
    }

    // FAVORITES
    public static function returnFavorites($data) {
        $favorites = [];
        foreach ($data as $favoriteData) {
            $favorites[] = Resource::ReturnFavorite($favoriteData);
        }
        return $favorites;
    }

    // MODERATORS
    public static function returnModerators($data) {
        $moderators = [];
        foreach ($data as $moderatorData) {
            $moderators[] = Resource::ReturnModerateur($moderatorData);
        }
        return $moderators;
    }

    // RESERVATIONS
    public static function returnReservations($data) {
        $reservations = [];
        foreach ($data as $reservationData) {
            $reservations[] = Resource::ReturnReservation($reservationData);
        }
        return $reservations;
    }

    // MEMBERS
    public static function returnMembers($data) {
        $members = [];
        foreach ($data as $memberData) {
            $members[] = Resource::ReturnMembre($memberData);
        }
        return $members;
    }
}
