SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `admin` (
  `id_a` int NOT NULL,
  `nom_a` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email_a` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `tel_a` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `mdp_a` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `photo_a` longblob
);

CREATE TABLE `annonce` (
  `id_an` int NOT NULL,
  `nom_an` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `categorie_an` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `type_fete` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `ville_an` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `adresse_an` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `file_name_video` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `file_path_video` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `file_size_video` int DEFAULT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `file_size` int NOT NULL,
  `date_cr` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tel_an` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mobile_an` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `tarif_an` float DEFAULT NULL,
  `detail_an` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `etat_an` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Attente',
  `id_a` int DEFAULT NULL,
  `id_mo` int DEFAULT NULL,
  `id_m` int NOT NULL,
  `nature_tarif` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `visites` int DEFAULT '0',
  `jaime` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `boost` (
  `id_b` int NOT NULL,
  `duree_b` int NOT NULL,
  `tarif_b` float NOT NULL,
  `recu_b` longblob NOT NULL,
  `etat_b` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Attente',
  `id_m` int NOT NULL,
  `id_an` int NOT NULL,
  `date_cr_b` datetime NOT NULL,
  `id_mo` int NOT NULL DEFAULT '2',
  `type_b` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `client` (
  `id_c` int NOT NULL,
  `nom_c` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `ville_c` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `age_c` int NOT NULL,
  `email_c` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `tel_c` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `mdp_c` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `photo_c` longblob,
  `etat_c` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'attente',
  `signale` varchar(10) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'non',
  `id_a` int NOT NULL,
  `id_mo` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `contact` (
  `id` int NOT NULL,
  `nom` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `msg` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `tel` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sujet` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `genre` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `id_m` int DEFAULT NULL,
  `id_c` int DEFAULT NULL,
  `id_mo` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `favoris` (
  `id_fav` int NOT NULL,
  `id_an` int NOT NULL,
  `id_c` int DEFAULT NULL,
  `id_m` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `images` (
  `id_img` int NOT NULL,
  `nom_img` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `taille_img` int NOT NULL,
  `type_img` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `chemin_img` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `date_cr` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_an` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `membre` (
  `id_m` int NOT NULL,
  `nom_m` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email_m` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `ville_m` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `adresse_m` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tel_m` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mobil_m` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `mdp_m` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `photo_m` longblob NOT NULL,
  `etat_m` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'attente',
  `signale` varchar(10) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'non',
  `id_a` int NOT NULL,
  `id_mo` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


--

CREATE TABLE `moderateur` (
  `id_mo` int NOT NULL,
  `nom_mo` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `prenom_mo` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email_mo` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `tel_mo` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `mdp_mo` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `photo_mo` longblob NOT NULL,
  `id_a` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `reservation` (
  `id_r` int NOT NULL,
  `date_r_debut` date NOT NULL,
  `date_r_fin` date NOT NULL,
  `nom_c_r` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email_c_r` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `tel_c_r` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `type_fete` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `etat_r` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Attente',
  `date_cr` datetime NOT NULL,
  `id_an` int NOT NULL,
  `id_m` int NOT NULL,
  `id_c` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE membre ADD COLUMN fcm_token VARCHAR(255) NULL;
ALTER TABLE client ADD COLUMN fcm_token VARCHAR(255) NULL;


ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_a`),
  ADD UNIQUE KEY `email_a` (`email_a`),
  ADD UNIQUE KEY `tel_a` (`tel_a`);

ALTER TABLE `annonce`
  ADD PRIMARY KEY (`id_an`),
  ADD UNIQUE KEY `nom_an` (`nom_an`),
  ADD KEY `id_a_fk` (`id_a`),
  ADD KEY `id_mo_fk` (`id_mo`),
  ADD KEY `id_m_fk` (`id_m`);


ALTER TABLE `boost`
  ADD PRIMARY KEY (`id_b`),
  ADD KEY `id_m_fk` (`id_m`),
  ADD KEY `id_an_fk` (`id_an`),
  ADD KEY `id_mo_fk` (`id_mo`);


ALTER TABLE `client`
  ADD PRIMARY KEY (`id_c`),
  ADD UNIQUE KEY `email_c` (`email_c`),
  ADD UNIQUE KEY `tel_c` (`tel_c`),
  ADD KEY `id_a_fk` (`id_a`),
  ADD KEY `id_mo_fk` (`id_mo`);


ALTER TABLE `contact`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_m_fk` (`id_m`),
  ADD KEY `id_c_fk` (`id_c`),
  ADD KEY `id_mo_fk` (`id_mo`);


ALTER TABLE `favoris`
  ADD PRIMARY KEY (`id_fav`),
  ADD KEY `FK_id_an` (`id_an`),
  ADD KEY `FK_id_c` (`id_c`),
  ADD KEY `FK_id_m` (`id_m`);


ALTER TABLE `images`
  ADD PRIMARY KEY (`id_img`),
  ADD KEY `id_an_fk` (`id_an`);


ALTER TABLE `membre`
  ADD PRIMARY KEY (`id_m`),
  ADD UNIQUE KEY `email_m` (`email_m`),
  ADD UNIQUE KEY `mobil_m` (`mobil_m`),
  ADD KEY `id_a_fk` (`id_a`),
  ADD KEY `id_mo_fk` (`id_mo`);


ALTER TABLE `moderateur`
  ADD PRIMARY KEY (`id_mo`),
  ADD UNIQUE KEY `email_mo` (`email_mo`),
  ADD UNIQUE KEY `tel_mo` (`tel_mo`),
  ADD KEY `id_a_fk` (`id_a`);


ALTER TABLE `reservation`
  ADD PRIMARY KEY (`id_r`),
  ADD KEY `id_an_fk` (`id_an`),
  ADD KEY `id_m_fk` (`id_m`),
  ADD KEY `id_c_fk` (`id_c`);


ALTER TABLE `admin`
  MODIFY `id_a` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;


ALTER TABLE `annonce`
  MODIFY `id_an` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127;


ALTER TABLE `boost`
  MODIFY `id_b` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;


ALTER TABLE `client`
  MODIFY `id_c` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;


ALTER TABLE `contact`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=165;


ALTER TABLE `favoris`
  MODIFY `id_fav` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;


ALTER TABLE `images`
  MODIFY `id_img` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;


ALTER TABLE `membre`
  MODIFY `id_m` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;


ALTER TABLE `moderateur`
  MODIFY `id_mo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;


ALTER TABLE `reservation`
  MODIFY `id_r` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;


ALTER TABLE `annonce`
  ADD CONSTRAINT `annonce_ibfk_1` FOREIGN KEY (`id_a`) REFERENCES `admin` (`id_a`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `annonce_ibfk_2` FOREIGN KEY (`id_m`) REFERENCES `membre` (`id_m`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `annonce_ibfk_3` FOREIGN KEY (`id_mo`) REFERENCES `moderateur` (`id_mo`) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE `boost`
  ADD CONSTRAINT `boost_ibfk_3` FOREIGN KEY (`id_m`) REFERENCES `membre` (`id_m`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `boost_ibfk_4` FOREIGN KEY (`id_an`) REFERENCES `annonce` (`id_an`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `boost_ibfk_5` FOREIGN KEY (`id_mo`) REFERENCES `moderateur` (`id_mo`) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE `client`
  ADD CONSTRAINT `client_ibfk_2` FOREIGN KEY (`id_mo`) REFERENCES `moderateur` (`id_mo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `client_ibfk_3` FOREIGN KEY (`id_a`) REFERENCES `admin` (`id_a`);


ALTER TABLE `contact`
  ADD CONSTRAINT `contact_ibfk_1` FOREIGN KEY (`id_c`) REFERENCES `client` (`id_c`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `contact_ibfk_2` FOREIGN KEY (`id_m`) REFERENCES `membre` (`id_m`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `contact_ibfk_3` FOREIGN KEY (`id_mo`) REFERENCES `moderateur` (`id_mo`);


ALTER TABLE `favoris`
  ADD CONSTRAINT `favoris_ibfk_1` FOREIGN KEY (`id_an`) REFERENCES `annonce` (`id_an`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `favoris_ibfk_2` FOREIGN KEY (`id_c`) REFERENCES `client` (`id_c`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `favoris_ibfk_3` FOREIGN KEY (`id_m`) REFERENCES `membre` (`id_m`) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE `images`
  ADD CONSTRAINT `images_ibfk_1` FOREIGN KEY (`id_an`) REFERENCES `annonce` (`id_an`) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE `membre`
  ADD CONSTRAINT `membre_ibfk_1` FOREIGN KEY (`id_a`) REFERENCES `admin` (`id_a`),
  ADD CONSTRAINT `membre_ibfk_2` FOREIGN KEY (`id_mo`) REFERENCES `moderateur` (`id_mo`) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE `moderateur`
  ADD CONSTRAINT `moderateur_ibfk_1` FOREIGN KEY (`id_a`) REFERENCES `admin` (`id_a`);


ALTER TABLE `reservation`
  ADD CONSTRAINT `reservation_ibfk_2` FOREIGN KEY (`id_an`) REFERENCES `annonce` (`id_an`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reservation_ibfk_3` FOREIGN KEY (`id_m`) REFERENCES `membre` (`id_m`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reservation_ibfk_4` FOREIGN KEY (`id_c`) REFERENCES `client` (`id_c`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;


