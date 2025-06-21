-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 21 Jun 2025 pada 15.12
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `socialize_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `followers`
--

CREATE TABLE `followers` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_follower` int(11) NOT NULL,
  `followed_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `followers`
--

INSERT INTO `followers` (`id`, `id_user`, `id_follower`, `followed_at`) VALUES
(1, 11, 1, '2025-06-21 19:59:55');

-- --------------------------------------------------------

--
-- Struktur dari tabel `messages`
--

CREATE TABLE `messages` (
  `id_message` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `messages`
--

INSERT INTO `messages` (`id_message`, `sender_id`, `receiver_id`, `message`, `created_at`) VALUES
(1, 1, 11, 'sikattt', '2025-06-21 14:29:51'),
(2, 11, 1, 'yahh sikatt', '2025-06-21 14:32:58'),
(3, 12, 1, 'sikatttt', '2025-06-21 14:37:42'),
(4, 1, 12, 'sikat tang', '2025-06-21 14:38:30'),
(5, 11, 12, 'binn', '2025-06-21 14:39:40'),
(6, 12, 11, 'sikkk', '2025-06-21 14:40:20'),
(7, 1, 11, 'sikatt', '2025-06-21 20:03:36');

-- --------------------------------------------------------

--
-- Struktur dari tabel `post`
--

CREATE TABLE `post` (
  `id_post` int(11) NOT NULL,
  `id_user` int(26) NOT NULL,
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `post`
--

INSERT INTO `post` (`id_post`, `id_user`, `content`, `image`, `created_at`) VALUES
(10, 11, 'Perjuangan University', 'uploads/683c6e988ff46_gerbang unper.jpg', '2025-06-01 22:15:36'),
(11, 1, 'Test week!!', 'uploads/683c6ecd515c0_WhatsApp Image 2024-11-04 at 18.14.55_118bde7f.jpg', '2025-06-01 22:16:29'),
(12, 1, '60%\r\n40% to go', NULL, '2025-06-01 22:19:19'),
(13, 1, 'more feature to add', NULL, '2025-06-01 22:20:15'),
(14, 1, 'new feature on going', NULL, '2025-06-21 13:59:23'),
(15, 12, 'new to this platform!!', NULL, '2025-06-21 14:39:12');

-- --------------------------------------------------------

--
-- Struktur dari tabel `post_likes`
--

CREATE TABLE `post_likes` (
  `id_like` int(11) NOT NULL,
  `id_post` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `post_likes`
--

INSERT INTO `post_likes` (`id_like`, `id_post`, `id_user`, `created_at`) VALUES
(22, 11, 1, '2025-06-01 22:16:37'),
(23, 13, 1, '2025-06-01 22:20:17'),
(24, 12, 1, '2025-06-01 22:20:20'),
(25, 13, 11, '2025-06-01 22:20:33'),
(26, 12, 11, '2025-06-01 22:20:34'),
(27, 11, 11, '2025-06-01 22:20:37'),
(28, 10, 11, '2025-06-01 22:20:39'),
(31, 15, 11, '2025-06-21 14:39:26');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_user` int(26) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `email` text NOT NULL,
  `description` text DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `banner_pic` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `email`, `description`, `profile_pic`, `banner_pic`) VALUES
(1, 'regaalgifary', '03dffbec7d0e3678b8d5a8496d3003aa', '2203010074@unper.ac.id', 'coding maniac', 'uploads/profile_1_1750492582.jpg', 'uploads/banner_1_1750492582.jpeg'),
(11, 'arif', 'd53d757c0f838ea49fb46e09cbcc3cb1', '2203010059@unper.ac.id', 'hallo saya arif', 'uploads/profile_11_1750491867.jpg', 'uploads/banner_11_1750491867.jpg'),
(12, 'bintang', '1d0c1f8d2fec766925cef5ed72aa6135', '2203010075@unper.ac.id', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_follows`
--

CREATE TABLE `user_follows` (
  `id` int(11) NOT NULL,
  `follower_id` int(11) NOT NULL,
  `following_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `followers`
--
ALTER TABLE `followers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_follow` (`id_user`,`id_follower`),
  ADD KEY `id_follower` (`id_follower`);

--
-- Indeks untuk tabel `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id_message`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indeks untuk tabel `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id_post`),
  ADD KEY `fk_user_post` (`id_user`);

--
-- Indeks untuk tabel `post_likes`
--
ALTER TABLE `post_likes`
  ADD PRIMARY KEY (`id_like`),
  ADD UNIQUE KEY `unique_like` (`id_post`,`id_user`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`(255)),
  ADD UNIQUE KEY `email` (`email`(255));

--
-- Indeks untuk tabel `user_follows`
--
ALTER TABLE `user_follows`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `follower_id` (`follower_id`,`following_id`),
  ADD KEY `following_id` (`following_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `followers`
--
ALTER TABLE `followers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `messages`
--
ALTER TABLE `messages`
  MODIFY `id_message` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `post`
--
ALTER TABLE `post`
  MODIFY `id_post` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `post_likes`
--
ALTER TABLE `post_likes`
  MODIFY `id_like` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(26) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `user_follows`
--
ALTER TABLE `user_follows`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `followers`
--
ALTER TABLE `followers`
  ADD CONSTRAINT `followers_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `followers_ibfk_2` FOREIGN KEY (`id_follower`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `fk_user_post` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `post_likes`
--
ALTER TABLE `post_likes`
  ADD CONSTRAINT `post_likes_ibfk_1` FOREIGN KEY (`id_post`) REFERENCES `post` (`id_post`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_likes_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `user_follows`
--
ALTER TABLE `user_follows`
  ADD CONSTRAINT `user_follows_ibfk_1` FOREIGN KEY (`follower_id`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_follows_ibfk_2` FOREIGN KEY (`following_id`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
