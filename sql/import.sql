-- Adminer 5.4.1 MySQL 8.0.40 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `aukce`;
CREATE TABLE `aukce` (
                         `id` int NOT NULL AUTO_INCREMENT,
                         `nazev` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                         `popis` longtext COLLATE utf8mb4_unicode_ci,
                         `vychozi_cena` decimal(10,2) NOT NULL,
                         `aktualni_cena` decimal(10,2) DEFAULT NULL,
                         `cas_zacatku` datetime NOT NULL,
                         `cas_konce` datetime NOT NULL,
                         `stav` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                         `hlavni_foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                         `skryta` tinyint(1) NOT NULL,
                         `vyuctovana` tinyint(1) NOT NULL,
                         `verejne_id` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
                         `uzivatel_id` int NOT NULL,
                         `vitez_id` int DEFAULT NULL,
                         `typ_id` int DEFAULT NULL,
                         PRIMARY KEY (`id`),
                         UNIQUE KEY `UNIQ_91B703C234752EE` (`verejne_id`),
                         KEY `IDX_91B703C9B3651C6` (`uzivatel_id`),
                         KEY `IDX_91B703C4DD0A578` (`vitez_id`),
                         KEY `IDX_91B703C278CD074` (`typ_id`),
                         CONSTRAINT `FK_91B703C278CD074` FOREIGN KEY (`typ_id`) REFERENCES `kategorie` (`id`),
                         CONSTRAINT `FK_91B703C4DD0A578` FOREIGN KEY (`vitez_id`) REFERENCES `uzivatel` (`id`),
                         CONSTRAINT `FK_91B703C9B3651C6` FOREIGN KEY (`uzivatel_id`) REFERENCES `uzivatel` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `aukce` (`id`, `nazev`, `popis`, `vychozi_cena`, `aktualni_cena`, `cas_zacatku`, `cas_konce`, `stav`, `hlavni_foto`, `skryta`, `vyuctovana`, `verejne_id`, `uzivatel_id`, `vitez_id`, `typ_id`) VALUES
                                                                                                                                                                                                                (1,	'iPhone 13',	'Použitý iPhone',	5000.00,	5200.00,	'2026-04-09 00:23:54',	'2026-04-16 00:23:54',	'aktivni',	NULL,	0,	0,	'a1',	2,	NULL,	NULL),
                                                                                                                                                                                                                (2,	'Notebook Dell',	'Výkonný notebook',	8000.00,	8200.00,	'2026-04-09 00:23:54',	'2026-04-16 00:23:54',	'aktivni',	NULL,	0,	0,	'a2',	3,	NULL,	NULL),
                                                                                                                                                                                                                (3,	'Kožená bunda',	'Stylová bunda',	1500.00,	1700.00,	'2026-04-09 00:23:54',	'2026-04-16 00:23:54',	'aktivni',	NULL,	0,	0,	'a3',	4,	NULL,	NULL),
                                                                                                                                                                                                                (4,	'Auto Škoda',	'Starší auto',	30000.00,	31000.00,	'2026-04-09 00:23:54',	'2026-04-16 00:23:54',	'aktivni',	NULL,	0,	0,	'a4',	5,	NULL,	NULL),
                                                                                                                                                                                                                (5,	'Horské kolo',	'Kvalitní kolo',	4000.00,	4500.00,	'2026-04-09 00:23:54',	'2026-04-16 00:23:54',	'aktivni',	NULL,	0,	0,	'a5',	6,	NULL,	NULL),
                                                                                                                                                                                                                (6,	'Kniha HP',	'Fantasy kniha',	300.00,	350.00,	'2026-04-09 00:23:54',	'2026-04-16 00:23:54',	'aktivni',	NULL,	0,	0,	'a6',	7,	NULL,	NULL),
                                                                                                                                                                                                                (7,	'Kytara',	'Hudební nástroj',	2000.00,	2300.00,	'2026-04-09 00:23:54',	'2026-04-16 00:23:54',	'aktivni',	NULL,	0,	0,	'a7',	8,	NULL,	NULL),
                                                                                                                                                                                                                (8,	'Televize',	'Smart TV',	6000.00,	6200.00,	'2026-04-09 00:23:54',	'2026-04-16 00:23:54',	'aktivni',	NULL,	0,	0,	'a8',	9,	NULL,	NULL),
                                                                                                                                                                                                                (9,	'Boty Nike',	'Nové boty',	1200.00,	1400.00,	'2026-04-09 00:23:54',	'2026-04-16 00:23:54',	'aktivni',	NULL,	0,	0,	'a9',	10,	NULL,	NULL),
                                                                                                                                                                                                                (10,	'Stůl',	'Dřevěný stůl',	2500.00,	2700.00,	'2026-04-09 00:23:54',	'2026-04-16 00:23:54',	'aktivni',	NULL,	0,	0,	'a10',	11,	NULL,	NULL),
                                                                                                                                                                                                                (11,	'Hodinky',	'Luxusní hodinky',	5000.00,	5300.00,	'2026-04-09 00:23:54',	'2026-04-16 00:23:54',	'aktivni',	NULL,	0,	0,	'a11',	12,	NULL,	NULL),
                                                                                                                                                                                                                (12,	'Foťák',	'Digitální fotoaparát',	7000.00,	7200.00,	'2026-04-09 00:23:54',	'2026-04-16 00:23:54',	'aktivni',	NULL,	0,	0,	'a12',	13,	NULL,	NULL),
                                                                                                                                                                                                                (13,	'PS5',	'Herní konzole',	12000.00,	12500.00,	'2026-04-09 00:23:54',	'2026-04-16 00:23:54',	'aktivni',	NULL,	0,	0,	'a13',	14,	NULL,	NULL),
                                                                                                                                                                                                                (14,	'Mixér',	'Kuchyňský mixér',	800.00,	900.00,	'2026-04-09 00:23:54',	'2026-04-16 00:23:54',	'aktivni',	NULL,	0,	0,	'a14',	2,	NULL,	NULL),
                                                                                                                                                                                                                (15,	'Sběratelská mince',	'Vzácná mince',	3000.00,	3500.00,	'2026-04-09 00:23:54',	'2026-04-16 00:23:54',	'aktivni',	NULL,	0,	0,	'a15',	3,	NULL,	NULL);

DROP TABLE IF EXISTS `aukce_kategorie`;
CREATE TABLE `aukce_kategorie` (
                                   `id` int NOT NULL AUTO_INCREMENT,
                                   `aukce_id` int NOT NULL,
                                   `kategorie_id` int NOT NULL,
                                   PRIMARY KEY (`id`),
                                   KEY `IDX_891C1CFCEE6A69D7` (`aukce_id`),
                                   KEY `IDX_891C1CFCBAF991D3` (`kategorie_id`),
                                   CONSTRAINT `FK_891C1CFCBAF991D3` FOREIGN KEY (`kategorie_id`) REFERENCES `kategorie` (`id`),
                                   CONSTRAINT `FK_891C1CFCEE6A69D7` FOREIGN KEY (`aukce_id`) REFERENCES `aukce` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `aukce_kategorie` (`id`, `aukce_id`, `kategorie_id`) VALUES
                                                                     (1,	1,	1),
                                                                     (2,	2,	3),
                                                                     (3,	3,	5),
                                                                     (4,	4,	7),
                                                                     (5,	5,	9),
                                                                     (6,	6,	11),
                                                                     (7,	7,	14),
                                                                     (8,	8,	4),
                                                                     (9,	9,	6),
                                                                     (10,	10,	8),
                                                                     (11,	11,	19),
                                                                     (12,	12,	18),
                                                                     (13,	13,	17),
                                                                     (14,	14,	16),
                                                                     (15,	15,	23);

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE `doctrine_migration_versions` (
                                               `version` varchar(191) NOT NULL,
                                               `executed_at` datetime DEFAULT NULL,
                                               `execution_time` int DEFAULT NULL,
                                               PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
    ('DoctrineMigrations\\Version20260322154612',	'2026-03-22 15:46:15',	393);

DROP TABLE IF EXISTS `fotky_aukci`;
CREATE TABLE `fotky_aukci` (
                               `id` int NOT NULL AUTO_INCREMENT,
                               `cesta` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                               `vytvoreno` datetime NOT NULL,
                               `aukce_id` int NOT NULL,
                               PRIMARY KEY (`id`),
                               KEY `IDX_9163788EEE6A69D7` (`aukce_id`),
                               CONSTRAINT `FK_9163788EEE6A69D7` FOREIGN KEY (`aukce_id`) REFERENCES `aukce` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `kategorie`;
CREATE TABLE `kategorie` (
                             `id` int NOT NULL AUTO_INCREMENT,
                             `nazev` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
                             `popis` longtext COLLATE utf8mb4_unicode_ci,
                             PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `kategorie` (`id`, `nazev`, `popis`) VALUES
                                                     (1,	'Elektronika',	NULL),
                                                     (2,	'Mobily',	NULL),
                                                     (3,	'Notebooky',	NULL),
                                                     (4,	'TV',	NULL),
                                                     (5,	'Oblečení',	NULL),
                                                     (6,	'Boty',	NULL),
                                                     (7,	'Auto-moto',	NULL),
                                                     (8,	'Nábytek',	NULL),
                                                     (9,	'Sport',	NULL),
                                                     (10,	'Fitness',	NULL),
                                                     (11,	'Knihy',	NULL),
                                                     (12,	'Hračky',	NULL),
                                                     (13,	'Zahrada',	NULL),
                                                     (14,	'Hudba',	NULL),
                                                     (15,	'Domácnost',	NULL),
                                                     (16,	'Kuchyně',	NULL),
                                                     (17,	'PC komponenty',	NULL),
                                                     (18,	'Foto',	NULL),
                                                     (19,	'Hodinky',	NULL),
                                                     (20,	'Šperky',	NULL),
                                                     (21,	'Kosmetika',	NULL),
                                                     (22,	'Zvířata',	NULL),
                                                     (23,	'Sběratelství',	NULL),
                                                     (24,	'Starožitnosti',	NULL),
                                                     (25,	'Ostatní',	NULL);

DROP TABLE IF EXISTS `komentare`;
CREATE TABLE `komentare` (
                             `id` int NOT NULL AUTO_INCREMENT,
                             `text` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
                             `hodnoceni` smallint DEFAULT NULL,
                             `vytvoreno` datetime NOT NULL,
                             `skryty` tinyint(1) NOT NULL,
                             `verejne_id` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
                             `aukce_id` int NOT NULL,
                             `uzivatel_id` int NOT NULL,
                             PRIMARY KEY (`id`),
                             UNIQUE KEY `UNIQ_2837DB5F234752EE` (`verejne_id`),
                             KEY `IDX_2837DB5FEE6A69D7` (`aukce_id`),
                             KEY `IDX_2837DB5F9B3651C6` (`uzivatel_id`),
                             CONSTRAINT `FK_2837DB5F9B3651C6` FOREIGN KEY (`uzivatel_id`) REFERENCES `uzivatel` (`id`),
                             CONSTRAINT `FK_2837DB5FEE6A69D7` FOREIGN KEY (`aukce_id`) REFERENCES `aukce` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `komentare` (`id`, `text`, `hodnoceni`, `vytvoreno`, `skryty`, `verejne_id`, `aukce_id`, `uzivatel_id`) VALUES
                                                                                                                        (1,	'Super aukce!',	5,	'2026-04-09 00:25:37',	0,	'k1',	1,	3),
                                                                                                                        (2,	'Doporučuji',	4,	'2026-04-09 00:25:37',	0,	'k2',	2,	4),
                                                                                                                        (3,	'Rychlá domluva',	5,	'2026-04-09 00:25:37',	0,	'k3',	3,	5),
                                                                                                                        (4,	'Vše v pořádku',	5,	'2026-04-09 00:25:37',	0,	'k4',	4,	6),
                                                                                                                        (5,	'Perfektní komunikace',	5,	'2026-04-09 00:25:37',	0,	'k5',	5,	7),
                                                                                                                        (6,	'Spokojenost',	4,	'2026-04-09 00:25:37',	0,	'k6',	6,	8),
                                                                                                                        (7,	'Dobrá cena',	4,	'2026-04-09 00:25:37',	0,	'k7',	7,	9),
                                                                                                                        (8,	'Skvělé',	5,	'2026-04-09 00:25:37',	0,	'k8',	8,	10),
                                                                                                                        (9,	'Top aukce',	5,	'2026-04-09 00:25:37',	0,	'k9',	9,	11),
                                                                                                                        (10,	'Bez problémů',	5,	'2026-04-09 00:25:37',	0,	'k10',	10,	12),
                                                                                                                        (11,	'Vše ok',	4,	'2026-04-09 00:25:37',	0,	'k11',	11,	13),
                                                                                                                        (12,	'Super zkušenost',	5,	'2026-04-09 00:25:37',	0,	'k12',	12,	14),
                                                                                                                        (13,	'Doporučuji všem',	5,	'2026-04-09 00:25:37',	0,	'k13',	13,	2),
                                                                                                                        (14,	'Velká spokojenost',	5,	'2026-04-09 00:25:37',	0,	'k14',	14,	3),
                                                                                                                        (15,	'Výborné',	5,	'2026-04-09 00:25:37',	0,	'k15',	15,	4);

DROP TABLE IF EXISTS `notifikace`;
CREATE TABLE `notifikace` (
                              `id` int NOT NULL AUTO_INCREMENT,
                              `text` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
                              `typ` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                              `stav` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                              `vytvoreno` datetime NOT NULL,
                              `uzivatel_id` int NOT NULL,
                              PRIMARY KEY (`id`),
                              KEY `IDX_5FF01419B3651C6` (`uzivatel_id`),
                              CONSTRAINT `FK_5FF01419B3651C6` FOREIGN KEY (`uzivatel_id`) REFERENCES `uzivatel` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `notifikace` (`id`, `text`, `typ`, `stav`, `vytvoreno`, `uzivatel_id`) VALUES
                                                                                       (1,	'Bylo přihozeno na vaši aukci',	'prihod',	'nova',	'2026-04-09 00:25:54',	2),
                                                                                       (2,	'Nový komentář u aukce',	'komentar',	'nova',	'2026-04-09 00:25:54',	3),
                                                                                       (3,	'Vyhrál jste aukci',	'vyhra',	'nova',	'2026-04-09 00:25:54',	4),
                                                                                       (4,	'Byl jste přehozen',	'prihod',	'nova',	'2026-04-09 00:25:54',	5),
                                                                                       (5,	'Aukce skončila',	'vyhra',	'nova',	'2026-04-09 00:25:54',	6),
                                                                                       (6,	'Nová nabídka',	'prihod',	'nova',	'2026-04-09 00:25:54',	7),
                                                                                       (7,	'Komentář přidán',	'komentar',	'nova',	'2026-04-09 00:25:54',	8),
                                                                                       (8,	'Vyhrál jste aukci',	'vyhra',	'nova',	'2026-04-09 00:25:54',	9),
                                                                                       (9,	'Bylo přihozeno',	'prihod',	'nova',	'2026-04-09 00:25:54',	10),
                                                                                       (10,	'Nový komentář',	'komentar',	'nova',	'2026-04-09 00:25:54',	11),
                                                                                       (11,	'Výhra aukce',	'vyhra',	'nova',	'2026-04-09 00:25:54',	12),
                                                                                       (12,	'Přehození v aukci',	'prihod',	'nova',	'2026-04-09 00:25:54',	13),
                                                                                       (13,	'Komentář přidán',	'komentar',	'nova',	'2026-04-09 00:25:54',	14),
                                                                                       (14,	'Vyhrál jste aukci',	'vyhra',	'nova',	'2026-04-09 00:25:54',	2),
                                                                                       (15,	'Nová nabídka',	'prihod',	'nova',	'2026-04-09 00:25:54',	3);

DROP TABLE IF EXISTS `platby`;
CREATE TABLE `platby` (
                          `id` int NOT NULL AUTO_INCREMENT,
                          `castka` decimal(10,2) NOT NULL,
                          `typ` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                          `popis` longtext COLLATE utf8mb4_unicode_ci,
                          `stav` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                          `vytvoreno` datetime NOT NULL,
                          `uzivatel_id` int NOT NULL,
                          `aukce_id` int DEFAULT NULL,
                          PRIMARY KEY (`id`),
                          KEY `IDX_4852A6799B3651C6` (`uzivatel_id`),
                          KEY `IDX_4852A679EE6A69D7` (`aukce_id`),
                          CONSTRAINT `FK_4852A6799B3651C6` FOREIGN KEY (`uzivatel_id`) REFERENCES `uzivatel` (`id`),
                          CONSTRAINT `FK_4852A679EE6A69D7` FOREIGN KEY (`aukce_id`) REFERENCES `aukce` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `platby` (`id`, `castka`, `typ`, `popis`, `stav`, `vytvoreno`, `uzivatel_id`, `aukce_id`) VALUES
                                                                                                          (1,	5200.00,	'platba',	'Výhra aukce',	'dokonceno',	'2026-04-09 00:26:12',	3,	1),
                                                                                                          (2,	8200.00,	'platba',	'Výhra aukce',	'dokonceno',	'2026-04-09 00:26:12',	4,	2),
                                                                                                          (3,	1700.00,	'platba',	'Výhra aukce',	'dokonceno',	'2026-04-09 00:26:12',	5,	3),
                                                                                                          (4,	31000.00,	'platba',	'Výhra aukce',	'dokonceno',	'2026-04-09 00:26:12',	6,	4),
                                                                                                          (5,	4500.00,	'platba',	'Výhra aukce',	'dokonceno',	'2026-04-09 00:26:12',	7,	5),
                                                                                                          (6,	350.00,	'platba',	'Výhra aukce',	'dokonceno',	'2026-04-09 00:26:12',	8,	6),
                                                                                                          (7,	2300.00,	'platba',	'Výhra aukce',	'dokonceno',	'2026-04-09 00:26:12',	9,	7),
                                                                                                          (8,	6200.00,	'platba',	'Výhra aukce',	'dokonceno',	'2026-04-09 00:26:12',	10,	8),
                                                                                                          (9,	1400.00,	'platba',	'Výhra aukce',	'dokonceno',	'2026-04-09 00:26:12',	11,	9),
                                                                                                          (10,	2700.00,	'platba',	'Výhra aukce',	'dokonceno',	'2026-04-09 00:26:12',	12,	10),
                                                                                                          (11,	5300.00,	'platba',	'Výhra aukce',	'dokonceno',	'2026-04-09 00:26:12',	13,	11),
                                                                                                          (12,	7200.00,	'platba',	'Výhra aukce',	'dokonceno',	'2026-04-09 00:26:12',	14,	12),
                                                                                                          (13,	12500.00,	'platba',	'Výhra aukce',	'dokonceno',	'2026-04-09 00:26:12',	2,	13),
                                                                                                          (14,	900.00,	'platba',	'Výhra aukce',	'dokonceno',	'2026-04-09 00:26:12',	3,	14),
                                                                                                          (15,	3500.00,	'platba',	'Výhra aukce',	'dokonceno',	'2026-04-09 00:26:12',	4,	15);

DROP TABLE IF EXISTS `report_aukce`;
CREATE TABLE `report_aukce` (
                                `id` int NOT NULL AUTO_INCREMENT,
                                `duvod` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
                                `vytvoreno` datetime NOT NULL,
                                `aukce_id` int NOT NULL,
                                `nahlasujici_id` int NOT NULL,
                                `nahlaseny_id` int NOT NULL,
                                PRIMARY KEY (`id`),
                                KEY `IDX_23077745EE6A69D7` (`aukce_id`),
                                KEY `IDX_2307774523325519` (`nahlasujici_id`),
                                KEY `IDX_230777456346C8D7` (`nahlaseny_id`),
                                CONSTRAINT `FK_2307774523325519` FOREIGN KEY (`nahlasujici_id`) REFERENCES `uzivatel` (`id`),
                                CONSTRAINT `FK_230777456346C8D7` FOREIGN KEY (`nahlaseny_id`) REFERENCES `uzivatel` (`id`),
                                CONSTRAINT `FK_23077745EE6A69D7` FOREIGN KEY (`aukce_id`) REFERENCES `aukce` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `report_aukce` (`id`, `duvod`, `vytvoreno`, `aukce_id`, `nahlasujici_id`, `nahlaseny_id`) VALUES
                                                                                                          (1,	'Podezřelá aukce',	'2026-04-09 00:26:33',	1,	3,	2),
                                                                                                          (2,	'Spam',	'2026-04-09 00:26:33',	2,	4,	3),
                                                                                                          (3,	'Nevhodný obsah',	'2026-04-09 00:26:33',	3,	5,	4),
                                                                                                          (4,	'Podvod',	'2026-04-09 00:26:33',	4,	6,	5),
                                                                                                          (5,	'Falešný produkt',	'2026-04-09 00:26:33',	5,	7,	6),
                                                                                                          (6,	'Porušení pravidel',	'2026-04-09 00:26:33',	6,	8,	7),
                                                                                                          (7,	'Spam aukce',	'2026-04-09 00:26:33',	7,	9,	8),
                                                                                                          (8,	'Podvod',	'2026-04-09 00:26:33',	8,	10,	9),
                                                                                                          (9,	'Nevhodný obsah',	'2026-04-09 00:26:33',	9,	11,	10),
                                                                                                          (10,	'Podezření na scam',	'2026-04-09 00:26:33',	10,	12,	11),
                                                                                                          (11,	'Porušení podmínek',	'2026-04-09 00:26:33',	11,	13,	12),
                                                                                                          (12,	'Fake produkt',	'2026-04-09 00:26:33',	12,	14,	13),
                                                                                                          (13,	'Spam',	'2026-04-09 00:26:33',	13,	2,	14),
                                                                                                          (14,	'Nevhodný obsah',	'2026-04-09 00:26:33',	14,	3,	2),
                                                                                                          (15,	'Podvod',	'2026-04-09 00:26:33',	15,	4,	3);

DROP TABLE IF EXISTS `sazky`;
CREATE TABLE `sazky` (
                         `id` int NOT NULL AUTO_INCREMENT,
                         `castka` decimal(10,2) NOT NULL,
                         `vytvoreno` datetime NOT NULL,
                         `uzivatel_id` int DEFAULT NULL,
                         `aukce_id` int NOT NULL,
                         PRIMARY KEY (`id`),
                         KEY `IDX_D7C6B169B3651C6` (`uzivatel_id`),
                         KEY `IDX_D7C6B16EE6A69D7` (`aukce_id`),
                         CONSTRAINT `FK_D7C6B169B3651C6` FOREIGN KEY (`uzivatel_id`) REFERENCES `uzivatel` (`id`),
                         CONSTRAINT `FK_D7C6B16EE6A69D7` FOREIGN KEY (`aukce_id`) REFERENCES `aukce` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `sazky` (`id`, `castka`, `vytvoreno`, `uzivatel_id`, `aukce_id`) VALUES
                                                                                 (1,	5100.00,	'2026-04-09 00:24:37',	3,	1),
                                                                                 (2,	5200.00,	'2026-04-09 00:24:37',	4,	1),
                                                                                 (3,	8100.00,	'2026-04-09 00:24:37',	4,	2),
                                                                                 (4,	8200.00,	'2026-04-09 00:24:37',	5,	2),
                                                                                 (5,	1600.00,	'2026-04-09 00:24:37',	5,	3),
                                                                                 (6,	1700.00,	'2026-04-09 00:24:37',	6,	3),
                                                                                 (7,	30500.00,	'2026-04-09 00:24:37',	6,	4),
                                                                                 (8,	31000.00,	'2026-04-09 00:24:37',	7,	4),
                                                                                 (9,	4200.00,	'2026-04-09 00:24:37',	7,	5),
                                                                                 (10,	4500.00,	'2026-04-09 00:24:37',	8,	5),
                                                                                 (11,	320.00,	'2026-04-09 00:24:37',	8,	6),
                                                                                 (12,	350.00,	'2026-04-09 00:24:37',	9,	6),
                                                                                 (13,	2100.00,	'2026-04-09 00:24:37',	9,	7),
                                                                                 (14,	2300.00,	'2026-04-09 00:24:37',	10,	7),
                                                                                 (15,	6100.00,	'2026-04-09 00:24:37',	10,	8),
                                                                                 (16,	6200.00,	'2026-04-09 00:24:37',	11,	8),
                                                                                 (17,	1300.00,	'2026-04-09 00:24:37',	11,	9),
                                                                                 (18,	1400.00,	'2026-04-09 00:24:37',	12,	9),
                                                                                 (19,	2600.00,	'2026-04-09 00:24:37',	12,	10),
                                                                                 (20,	2700.00,	'2026-04-09 00:24:37',	13,	10),
                                                                                 (21,	5200.00,	'2026-04-09 00:24:37',	13,	11),
                                                                                 (22,	5300.00,	'2026-04-09 00:24:37',	14,	11),
                                                                                 (23,	7100.00,	'2026-04-09 00:24:37',	14,	12),
                                                                                 (24,	7200.00,	'2026-04-09 00:24:37',	2,	12),
                                                                                 (25,	12300.00,	'2026-04-09 00:24:37',	2,	13),
                                                                                 (26,	12500.00,	'2026-04-09 00:24:37',	3,	13),
                                                                                 (27,	850.00,	'2026-04-09 00:24:37',	3,	14),
                                                                                 (28,	900.00,	'2026-04-09 00:24:37',	4,	14),
                                                                                 (29,	3200.00,	'2026-04-09 00:24:37',	4,	15),
                                                                                 (30,	3500.00,	'2026-04-09 00:24:37',	5,	15);

DROP TABLE IF EXISTS `uzivatel`;
CREATE TABLE `uzivatel` (
                            `id` int NOT NULL AUTO_INCREMENT,
                            `uzivatelske_jmeno` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
                            `cele_jmeno` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                            `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
                            `heslo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                            `reset_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                            `reset_token_expires_at` datetime DEFAULT NULL,
                            `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                            `kredity` decimal(10,2) NOT NULL,
                            `profil_foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                            `email_overeno` tinyint(1) NOT NULL,
                            `blokovan` tinyint(1) NOT NULL,
                            `vytvoreno` datetime NOT NULL,
                            `email_token` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                            `verejne_id` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
                            PRIMARY KEY (`id`),
                            UNIQUE KEY `UNIQ_1C0F667EE7927C74` (`email`),
                            UNIQUE KEY `UNIQ_1C0F667E234752EE` (`verejne_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `uzivatel` (`id`, `uzivatelske_jmeno`, `cele_jmeno`, `email`, `heslo`, `reset_token`, `reset_token_expires_at`, `role`, `kredity`, `profil_foto`, `email_overeno`, `blokovan`, `vytvoreno`, `email_token`, `verejne_id`) VALUES
                                                                                                                                                                                                                                         (1,	'admin',	'Admin',	'admin@email.cz',	'$2y$13$UAWXGih.RRL7uj5jN4iiu.JH5nUGTO1zH/MWREXWX6emKznyRpbii',	NULL,	NULL,	'ROLE_ADMIN',	9999.00,	NULL,	1,	0,	'2026-04-09 00:22:16',	NULL,	'admin1'),
                                                                                                                                                                                                                                         (2,	'user1',	'Jan Novák',	'u1@email.cz',	'$2y$13$j2LHsup.kDu7XkjnI0baTOm6ze3FopGzNg4agxgDqNzRACASuMWPC',	NULL,	NULL,	'ROLE_USER',	1000.00,	NULL,	1,	0,	'2026-04-09 00:23:16',	NULL,	'u1'),
                                                                                                                                                                                                                                         (3,	'user2',	'Eva Svobodová',	'u2@email.cz',	'$2y$13$j2LHsup.kDu7XkjnI0baTOm6ze3FopGzNg4agxgDqNzRACASuMWPC',	NULL,	NULL,	'ROLE_USER',	1200.00,	NULL,	1,	0,	'2026-04-09 00:23:16',	NULL,	'u2'),
                                                                                                                                                                                                                                         (4,	'user3',	'Petr Dvořák',	'u3@email.cz',	'$2y$13$j2LHsup.kDu7XkjnI0baTOm6ze3FopGzNg4agxgDqNzRACASuMWPC',	NULL,	NULL,	'ROLE_USER',	900.00,	NULL,	1,	0,	'2026-04-09 00:23:16',	NULL,	'u3'),
                                                                                                                                                                                                                                         (5,	'user4',	'Lucie Nová',	'u4@email.cz',	'$2y$13$j2LHsup.kDu7XkjnI0baTOm6ze3FopGzNg4agxgDqNzRACASuMWPC',	NULL,	NULL,	'ROLE_USER',	1500.00,	NULL,	1,	0,	'2026-04-09 00:23:16',	NULL,	'u4'),
                                                                                                                                                                                                                                         (6,	'user5',	'Marek Hrubý',	'u5@email.cz',	'$2y$13$j2LHsup.kDu7XkjnI0baTOm6ze3FopGzNg4agxgDqNzRACASuMWPC',	NULL,	NULL,	'ROLE_USER',	800.00,	NULL,	1,	0,	'2026-04-09 00:23:16',	NULL,	'u5'),
                                                                                                                                                                                                                                         (7,	'user6',	'Karel Malý',	'u6@email.cz',	'$2y$13$j2LHsup.kDu7XkjnI0baTOm6ze3FopGzNg4agxgDqNzRACASuMWPC',	NULL,	NULL,	'ROLE_USER',	700.00,	NULL,	1,	0,	'2026-04-09 00:23:16',	NULL,	'u6'),
                                                                                                                                                                                                                                         (8,	'user7',	'Anna Černá',	'u7@email.cz',	'$2y$13$j2LHsup.kDu7XkjnI0baTOm6ze3FopGzNg4agxgDqNzRACASuMWPC',	NULL,	NULL,	'ROLE_USER',	950.00,	NULL,	1,	0,	'2026-04-09 00:23:16',	NULL,	'u7'),
                                                                                                                                                                                                                                         (9,	'user8',	'Tomáš Veselý',	'u8@email.cz',	'$2y$13$j2LHsup.kDu7XkjnI0baTOm6ze3FopGzNg4agxgDqNzRACASuMWPC',	NULL,	NULL,	'ROLE_USER',	1100.00,	NULL,	1,	0,	'2026-04-09 00:23:16',	NULL,	'u8'),
                                                                                                                                                                                                                                         (10,	'user9',	'Petra Králová',	'u9@email.cz',	'$2y$13$j2LHsup.kDu7XkjnI0baTOm6ze3FopGzNg4agxgDqNzRACASuMWPC',	NULL,	NULL,	'ROLE_USER',	1000.00,	NULL,	1,	0,	'2026-04-09 00:23:16',	NULL,	'u9'),
                                                                                                                                                                                                                                         (11,	'user10',	'David Procházka',	'u10@email.cz',	'$2y$13$j2LHsup.kDu7XkjnI0baTOm6ze3FopGzNg4agxgDqNzRACASuMWPC',	NULL,	NULL,	'ROLE_USER',	1300.00,	NULL,	1,	0,	'2026-04-09 00:23:16',	NULL,	'u10'),
                                                                                                                                                                                                                                         (12,	'user11',	'Veronika Urbanová',	'u11@email.cz',	'$2y$13$j2LHsup.kDu7XkjnI0baTOm6ze3FopGzNg4agxgDqNzRACASuMWPC',	NULL,	NULL,	'ROLE_USER',	850.00,	NULL,	1,	0,	'2026-04-09 00:23:16',	NULL,	'u11'),
                                                                                                                                                                                                                                         (13,	'user12',	'Michal Beneš',	'u12@email.cz',	'$2y$13$j2LHsup.kDu7XkjnI0baTOm6ze3FopGzNg4agxgDqNzRACASuMWPC',	NULL,	NULL,	'ROLE_USER',	900.00,	NULL,	1,	0,	'2026-04-09 00:23:16',	NULL,	'u12'),
                                                                                                                                                                                                                                         (14,	'user13',	'Tereza Kučerová',	'u13@email.cz',	'$2y$13$j2LHsup.kDu7XkjnI0baTOm6ze3FopGzNg4agxgDqNzRACASuMWPC',	NULL,	NULL,	'ROLE_USER',	1000.00,	NULL,	1,	0,	'2026-04-09 00:23:16',	NULL,	'u13'),
                                                                                                                                                                                                                                         (15,	'user14',	'Filip Horák',	'u14@email.cz',	'$2y$13$j2LHsup.kDu7XkjnI0baTOm6ze3FopGzNg4agxgDqNzRACASuMWPC',	NULL,	NULL,	'ROLE_USER',	950.00,	NULL,	1,	0,	'2026-04-09 00:23:16',	NULL,	'u14');

-- 2026-04-09 00:26:48 UTC