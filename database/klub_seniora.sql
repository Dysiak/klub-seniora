-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 12, 2025 at 12:44 PM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `klub_seniora`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `instruktor_details`
--

CREATE TABLE `instruktor_details` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `specjalizacje` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `instruktor_details`
--

INSERT INTO `instruktor_details` (`id`, `user_id`, `specjalizacje`) VALUES
(6, 4, 'Joga, Pilates, Stretching'),
(7, 5, 'Taniec, Muzyka'),
(8, 6, 'Gimnastyka, Aerobik');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `logi`
--

CREATE TABLE `logi` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `akcja` varchar(100) NOT NULL,
  `opis` text DEFAULT NULL,
  `data_operacji` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `rezerwacje`
--

CREATE TABLE `rezerwacje` (
  `id` int(11) NOT NULL,
  `id_seniora` int(11) NOT NULL,
  `id_zajec` int(11) NOT NULL,
  `data_rezerwacji` datetime DEFAULT current_timestamp(),
  `status` enum('aktywna','anulowana','zakonczona') DEFAULT 'aktywna',
  `data_anulowania` datetime DEFAULT NULL,
  `potwierdzenie` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rezerwacje`
--

INSERT INTO `rezerwacje` (`id`, `id_seniora`, `id_zajec`, `data_rezerwacji`, `status`, `data_anulowania`, `potwierdzenie`) VALUES
(1, 7, 8, '2025-12-07 14:30:00', 'zakonczona', NULL, 1),
(2, 8, 8, '2025-12-07 15:00:00', 'zakonczona', NULL, 1),
(3, 9, 8, '2025-12-07 16:20:00', 'zakonczona', NULL, 1),
(4, 7, 9, '2025-12-08 10:00:00', 'zakonczona', NULL, 1),
(5, 7, 4, '2025-12-11 09:00:00', 'aktywna', NULL, 1),
(6, 8, 4, '2025-12-11 10:30:00', 'aktywna', NULL, 1),
(7, 9, 4, '2025-12-11 11:15:00', 'aktywna', NULL, 1),
(8, 7, 5, '2025-12-11 12:00:00', 'aktywna', NULL, 1),
(9, 10, 5, '2025-12-11 13:45:00', 'aktywna', NULL, 1),
(10, 7, 6, '2025-12-11 14:20:00', 'aktywna', NULL, 1),
(11, 8, 6, '2025-12-11 14:25:00', 'aktywna', NULL, 1),
(12, 9, 6, '2025-12-11 14:30:00', 'aktywna', NULL, 1),
(13, 10, 6, '2025-12-11 15:00:00', 'aktywna', NULL, 1),
(14, 11, 6, '2025-12-11 15:10:00', 'aktywna', NULL, 1),
(15, 8, 7, '2025-12-11 16:00:00', 'aktywna', NULL, 1),
(16, 9, 7, '2025-12-11 16:05:00', 'aktywna', NULL, 1),
(17, 10, 7, '2025-12-11 16:10:00', 'aktywna', NULL, 1),
(18, 11, 7, '2025-12-11 16:15:00', 'aktywna', NULL, 1),
(19, 7, 7, '2025-12-11 16:20:00', 'aktywna', NULL, 1),
(20, 11, 4, '2025-12-11 08:00:00', 'anulowana', '2025-12-11 17:30:00', 0),
(21, 10, 4, '2025-12-11 23:48:31', 'anulowana', '2025-12-11 23:48:55', 1),
(22, 10, 10, '2025-12-12 00:14:59', 'aktywna', NULL, 1),
(23, 12, 3, '2025-12-12 00:23:10', 'aktywna', NULL, 1),
(26, 10, 3, '2025-12-12 12:13:46', 'aktywna', NULL, 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `sale`
--

CREATE TABLE `sale` (
  `id` int(11) NOT NULL,
  `nazwa` varchar(100) NOT NULL,
  `pojemnosc` int(11) NOT NULL,
  `opis` text DEFAULT NULL,
  `wyposazenie` text DEFAULT NULL,
  `czy_dostepna` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sale`
--

INSERT INTO `sale` (`id`, `nazwa`, `pojemnosc`, `opis`, `wyposazenie`, `czy_dostepna`) VALUES
(1, 'Sala Jogi', 15, 'Przestronna sala z lustrem i drewnianą podłogą', 'Maty do jogi,Poduszki,Klocki,Pasy', 1),
(2, 'Sala Taneczna', 20, 'Duża sala z parkietem i systemem nagłośnienia', 'Lustro,Nagłośnienie,Baletnica', 1),
(3, 'Sala Gimnastyczna', 25, 'Sala wyposażona w sprzęt do ćwiczeń', 'Hantle,Step,Piłki gimnastyczne,Maty', 1),
(4, 'Sala Konferencyjna', 30, 'Sala ze stolami i krzesłami, rzutnik', 'Projektor,Ekran,Tablica,Krzesła', 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `senior_details`
--

CREATE TABLE `senior_details` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `preferencje` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `senior_details`
--

INSERT INTO `senior_details` (`id`, `user_id`, `preferencje`) VALUES
(8, 7, 'Zainteresowana jogą i tańcem'),
(9, 8, 'Lubi aktywność fizyczną'),
(10, 9, 'Preferuje spokojne zajęcia'),
(11, 10, 'Chce rozwijać kondycję'),
(12, 11, 'Zainteresowana różnorodnymi zajęciami');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `imie` varchar(100) NOT NULL,
  `nazwisko` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `login` varchar(50) NOT NULL,
  `haslo` varchar(255) NOT NULL,
  `telefon` varchar(15) DEFAULT NULL,
  `rola` enum('senior','instruktor','koordynator','administrator') NOT NULL,
  `data_rejestracji` datetime DEFAULT current_timestamp(),
  `czy_aktywny` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `imie`, `nazwisko`, `email`, `login`, `haslo`, `telefon`, `rola`, `data_rejestracji`, `czy_aktywny`) VALUES
(1, 'Jan', 'Kowalski', 'admin@klub.pl', 'admin', '$2y$10$w6JK4uq6sY3VMygKt/BAY.V5HubuKEBUkS.Xr.I838MBFt1TyXmT2', '123456789', 'administrator', '2025-12-11 22:14:02', 1),
(2, 'Anna', 'Nowak', 'anna.nowak@klub.pl', 'koordynator1', '$2y$10$w6JK4uq6sY3VMygKt/BAY.V5HubuKEBUkS.Xr.I838MBFt1TyXmT2', '234567890', 'koordynator', '2025-12-11 22:14:02', 1),
(3, 'Piotr', 'Wiśniewski', 'piotr.wisniewski@klub.pl', 'koordynator2', '$2y$10$w6JK4uq6sY3VMygKt/BAY.V5HubuKEBUkS.Xr.I838MBFt1TyXmT2', '345678901', 'koordynator', '2025-12-11 22:14:02', 1),
(4, 'Maria', 'Kowalczyk', 'maria.kowalczyk@klub.pl', 'instruktor1', '$2y$10$w6JK4uq6sY3VMygKt/BAY.V5HubuKEBUkS.Xr.I838MBFt1TyXmT2', '456789012', 'instruktor', '2025-12-11 22:14:02', 1),
(5, 'Tomasz', 'Lewandowski', 'tomasz.lewandowski@klub.pl', 'instruktor2', '$2y$10$w6JK4uq6sY3VMygKt/BAY.V5HubuKEBUkS.Xr.I838MBFt1TyXmT2', '567890123', 'instruktor', '2025-12-11 22:14:02', 1),
(6, 'Ewa', 'Wójcik', 'ewa.wojcik@klub.pl', 'instruktor3', '$2y$10$w6JK4uq6sY3VMygKt/BAY.V5HubuKEBUkS.Xr.I838MBFt1TyXmT2', '678901234', 'instruktor', '2025-12-11 22:14:02', 1),
(7, 'Zofia', 'Mazur', 'zofia.mazur@email.pl', 'senior1', '$2y$10$w6JK4uq6sY3VMygKt/BAY.V5HubuKEBUkS.Xr.I838MBFt1TyXmT2', '789012345', 'senior', '2025-12-11 22:14:02', 1),
(8, 'Stanisław', 'Krawczyk', 'stanislaw.krawczyk@email.pl', 'senior2', '$2y$10$w6JK4uq6sY3VMygKt/BAY.V5HubuKEBUkS.Xr.I838MBFt1TyXmT2', '890123456', 'senior', '2025-12-11 22:14:02', 1),
(9, 'Helena', 'Zając', 'helena.zajac@email.pl', 'senior3', '$2y$10$w6JK4uq6sY3VMygKt/BAY.V5HubuKEBUkS.Xr.I838MBFt1TyXmT2', '901234567', 'senior', '2025-12-11 22:14:02', 1),
(10, 'Władysław', 'Pawlak', 'wladyslaw.pawlak@email.pl', 'senior4', '$2y$10$w6JK4uq6sY3VMygKt/BAY.V5HubuKEBUkS.Xr.I838MBFt1TyXmT2', '012345678', 'senior', '2025-12-11 22:14:02', 1),
(11, 'Janina', 'Król', 'janina.krol@email.pl', 'senior5', '$2y$10$w6JK4uq6sY3VMygKt/BAY.V5HubuKEBUkS.Xr.I838MBFt1TyXmT2', '123450987', 'senior', '2025-12-11 22:14:02', 1),
(12, 'Mordechaj', 'Jojko', 'mordechaj@klub.com', 'senior6', '$2y$10$A/FiD71XigXWR.G6ztq9Re2F5gRxDvVz/gN5AGvA425rFVyzPgjBS', '123211451', 'senior', '2025-12-12 00:20:40', 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `zajecia`
--

CREATE TABLE `zajecia` (
  `id` int(11) NOT NULL,
  `nazwa` varchar(150) NOT NULL,
  `opis` text DEFAULT NULL,
  `data` date NOT NULL,
  `godzina_od` time NOT NULL,
  `godzina_do` time NOT NULL,
  `limit_miejsc` int(11) NOT NULL,
  `wolne_miejsca` int(11) NOT NULL,
  `id_instruktora` int(11) NOT NULL,
  `id_sali` int(11) NOT NULL,
  `status` enum('planowane','odbyte','odwolane') DEFAULT 'planowane',
  `data_utworzenia` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `zajecia`
--

INSERT INTO `zajecia` (`id`, `nazwa`, `opis`, `data`, `godzina_od`, `godzina_do`, `limit_miejsc`, `wolne_miejsca`, `id_instruktora`, `id_sali`, `status`, `data_utworzenia`) VALUES
(1, 'Joga dla początkujących', 'Spokojne zajęcia jogi dla osób rozpoczynających przygodę z jogą', '2025-12-16', '10:00:00', '11:30:00', 15, 15, 4, 1, 'odwolane', '2025-12-11 22:14:02'),
(2, 'Taniec towarzyski', 'Podstawowe kroki tańca towarzyskiego - walc, tango', '2025-12-16', '14:00:00', '15:30:00', 20, 20, 5, 2, 'planowane', '2025-12-11 22:14:02'),
(3, 'Gimnastyka poranna', 'Energetyczna gimnastyka na dobry początek dnia', '2025-12-17', '09:00:00', '10:00:00', 25, 23, 6, 3, 'planowane', '2025-12-11 22:14:02'),
(4, 'Joga Hatha', 'Zajęcia z jogi Hatha - wzmacnianie ciała i umysłu', '2025-12-18', '11:00:00', '12:30:00', 15, 12, 4, 1, 'planowane', '2025-12-11 22:14:02'),
(5, 'Taniec latino', 'Nauka kroków salsy i bachaty', '2025-12-19', '16:00:00', '17:30:00', 20, 18, 5, 2, 'planowane', '2025-12-11 22:14:02'),
(6, 'Pilates', 'Zajęcia wzmacniające mięśnie głębokie', '2025-12-21', '11:00:00', '12:00:00', 15, 10, 4, 1, 'planowane', '2025-12-11 22:14:02'),
(7, 'Aerobik dla seniorów', 'Lekki aerobik dostosowany do możliwości seniorów', '2025-12-20', '14:00:00', '15:00:00', 25, 20, 6, 3, 'planowane', '2025-12-11 22:14:02'),
(8, 'Joga dla seniorów', 'Zajęcia z jogi dla doświadczonych', '2025-12-09', '10:00:00', '11:30:00', 15, 0, 4, 1, 'odbyte', '2025-12-11 22:14:02'),
(9, 'Nordic Walking', 'Spacer z kijkami nordic walking po parku - zbiórka w sali konferencyjnej', '2025-12-10', '09:00:00', '10:30:00', 20, 5, 6, 4, 'odbyte', '2025-12-11 22:14:02'),
(10, 'Garncarstwo', '...', '2025-12-19', '09:00:00', '10:00:00', 25, 24, 4, 3, 'planowane', '2025-12-12 00:07:23'),
(11, 'test', '...', '2025-12-21', '09:08:00', '12:08:00', 25, 25, 5, 3, 'planowane', '2025-12-12 00:08:33');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `instruktor_details`
--
ALTER TABLE `instruktor_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeksy dla tabeli `logi`
--
ALTER TABLE `logi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_data` (`data_operacji`),
  ADD KEY `idx_user` (`user_id`);

--
-- Indeksy dla tabeli `rezerwacje`
--
ALTER TABLE `rezerwacje`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_rezerwacja` (`id_seniora`,`id_zajec`),
  ADD KEY `idx_senior` (`id_seniora`),
  ADD KEY `idx_zajecia` (`id_zajec`),
  ADD KEY `idx_status` (`status`);

--
-- Indeksy dla tabeli `sale`
--
ALTER TABLE `sale`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `senior_details`
--
ALTER TABLE `senior_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `login` (`login`),
  ADD KEY `idx_rola` (`rola`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_login` (`login`);

--
-- Indeksy dla tabeli `zajecia`
--
ALTER TABLE `zajecia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_data` (`data`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_instruktor` (`id_instruktora`),
  ADD KEY `idx_sala` (`id_sali`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `instruktor_details`
--
ALTER TABLE `instruktor_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `logi`
--
ALTER TABLE `logi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rezerwacje`
--
ALTER TABLE `rezerwacje`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `sale`
--
ALTER TABLE `sale`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `senior_details`
--
ALTER TABLE `senior_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `zajecia`
--
ALTER TABLE `zajecia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `instruktor_details`
--
ALTER TABLE `instruktor_details`
  ADD CONSTRAINT `instruktor_details_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `logi`
--
ALTER TABLE `logi`
  ADD CONSTRAINT `logi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `rezerwacje`
--
ALTER TABLE `rezerwacje`
  ADD CONSTRAINT `rezerwacje_ibfk_1` FOREIGN KEY (`id_seniora`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rezerwacje_ibfk_2` FOREIGN KEY (`id_zajec`) REFERENCES `zajecia` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `senior_details`
--
ALTER TABLE `senior_details`
  ADD CONSTRAINT `senior_details_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `zajecia`
--
ALTER TABLE `zajecia`
  ADD CONSTRAINT `zajecia_ibfk_1` FOREIGN KEY (`id_instruktora`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `zajecia_ibfk_2` FOREIGN KEY (`id_sali`) REFERENCES `sale` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
