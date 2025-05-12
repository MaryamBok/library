-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 05, 2025 at 03:19 PM
-- Server version: 10.4.18-MariaDB
-- PHP Version: 7.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_library`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `added_by_user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `price`, `category`, `description`, `cover_image`, `added_by_user_id`, `created_at`) VALUES
(1, 'The Great Gatsby', 'F. Scott Fitzgerald', '12.99', 'Fiction', 'A novel about the American dream.', 'C:\\xampp\\htdocs\\my_library\\image\\pythin.png', 1, '2025-05-03 23:53:34'),
(2, 'To Kill a Mockingbird', 'Harper Lee', '15.50', 'Fiction', 'Story of racial injustice in the Deep South.', 'https://images.pexels.com/photos/1005324/pexels-photo-1005324.jpeg', 1, '2025-05-03 23:53:34'),
(3, '1984', 'George Orwell', '14.00', 'Dystopian', 'Dystopian social science fiction novel.', 'https://images.pexels.com/photos/263165/pexels-photo-263165.jpeg', 1, '2025-05-03 23:53:34'),
(4, 'Pride and Prejudice', 'Jane Austen', '11.25', 'Romance', 'Romantic novel of manners.', 'https://images.pexels.com/photos/258293/pexels-photo-258293.jpeg', 1, '2025-05-03 23:53:34'),
(5, 'The Catcher in the Rye', 'J.D. Salinger', '13.75', 'Fiction', 'Novel about teenage rebellion.', 'https://images.pexels.com/photos/1053687/pexels-photo-1053687.jpeg', 1, '2025-05-03 23:53:34'),
(6, 'The Hobbit', 'J.R.R. Tolkien', '17.99', 'Fantasy', 'Fantasy adventure novel.', 'https://images.pexels.com/photos/1666041/pexels-photo-1666041.jpeg', 1, '2025-05-03 23:53:34'),
(7, 'Fahrenheit 451', 'Ray Bradbury', '10.85', 'Dystopian', 'Dystopian novel about censorship.', 'https://images.pexels.com/photos/1854402/pexels-photo-1854402.jpeg', 1, '2025-05-03 23:53:34'),
(8, 'Moby-Dick', 'Herman Melville', '14.30', 'Adventure', 'Epic sea adventure.', 'https://images.pexels.com/photos/46274/pexels-photo-46274.jpeg', 1, '2025-05-03 23:53:34'),
(9, 'War and Peace', 'Leo Tolstoy', '19.99', 'Historical', 'Classic Russian historical novel.', 'https://images.pexels.com/photos/461064/pexels-photo-461064.jpeg', 1, '2025-05-03 23:53:34'),
(10, 'Crime and Punishment', 'Fyodor Dostoevsky', '12.95', 'Philosophical', 'Philosophical novel about morality.', 'https://images.pexels.com/photos/261909/pexels-photo-261909.jpeg', 1, '2025-05-03 23:53:34'),
(11, 'The Lord of the Rings', 'J.R.R. Tolkien', '25.00', 'Fantasy', 'Epic fantasy trilogy.', 'https://images.pexels.com/photos/691529/pexels-photo-691529.jpeg', 1, '2025-05-03 23:53:34'),
(12, 'The Alchemist', 'Paulo Coelho', '13.60', 'Fiction', 'Parable about following your dream.', 'https://images.pexels.com/photos/46274/pexels-photo-46274.jpeg', 1, '2025-05-03 23:53:34'),
(13, 'Brave New World', 'Aldous Huxley', '14.75', 'Dystopian', 'Dystopian science fiction.', 'https://images.pexels.com/photos/694740/pexels-photo-694740.jpeg', 1, '2025-05-03 23:53:34'),
(14, 'Jane Eyre', 'Charlotte Bronte', '11.95', 'Romance', 'Gothic romance novel.', 'https://images.pexels.com/photos/62169/pexels-photo-62169.jpeg', 1, '2025-05-03 23:53:34'),
(15, 'Animal Farm', 'George Orwell', '9.99', 'Political', 'Political satire.', 'https://images.pexels.com/photos/1103717/pexels-photo-1103717.jpeg', 1, '2025-05-03 23:53:34'),
(16, 'Wuthering Heights', 'Emily Bronte', '12.00', 'Romance', 'Tragic romance novel.', 'https://images.pexels.com/photos/1394881/pexels-photo-1394881.jpeg', 1, '2025-05-03 23:53:34'),
(17, 'The Odyssey', 'Homer', '16.50', 'Epic', 'Ancient Greek epic poem.', 'https://images.pexels.com/photos/371633/pexels-photo-371633.jpeg', 1, '2025-05-03 23:53:34'),
(18, 'The Divine Comedy', 'Dante Alighieri', '17.00', 'Epic', 'Epic poem of the afterlife.', 'https://images.pexels.com/photos/46274/pexels-photo-46274.jpeg', 1, '2025-05-03 23:53:34'),
(19, 'Les Misérables', 'Victor Hugo', '18.90', 'Historical', 'French historical novel.', 'https://images.pexels.com/photos/46274/pexels-photo-46274.jpeg', 1, '2025-05-03 23:53:34'),
(20, 'Don Quixote', 'Miguel de Cervantes', '15.80', 'Classic', 'Classic Spanish novel.', 'https://images.pexels.com/photos/46274/pexels-photo-46274.jpeg', 1, '2025-05-03 23:53:34'),
(21, 'The Brothers Karamazov', 'Fyodor Dostoevsky', '14.20', 'Philosophical', 'Philosophical novel.', 'https://images.pexels.com/photos/46274/pexels-photo-46274.jpeg', 1, '2025-05-03 23:53:34'),
(22, 'Frankenstein', 'Mary Shelley', '13.40', 'Horror', 'Gothic science fiction novel.', 'https://images.pexels.com/photos/46274/pexels-photo-46274.jpeg', 1, '2025-05-03 23:53:34'),
(23, 'java', 'Bram Stoker', '12.20', 'Fantasy', 'Classic horror novel.', 'C:\\xampp\\htdocs\\my_library\\image\\java.png', 1, '2025-05-03 23:53:34'),
(24, 'Great Expectations', 'Charles Dickens', '13.35', 'Fiction', 'Bildungsroman novel.', 'https://images.pexels.com/photos/46274/pexels-photo-46274.jpeg', 1, '2025-05-03 23:53:34'),
(25, 'The Grapes of Wrath', 'John Steinbeck', '14.50', 'Historical', 'Novel about the Great Depression.', 'https://images.pexels.com/photos/46274/pexels-photo-46274.jpeg', 1, '2025-05-03 23:53:34'),
(26, 'Catch-22', 'Joseph Heller', '15.25', 'Satire', 'Satirical war novel.', 'https://images.pexels.com/photos/46274/pexels-photo-46274.jpeg', 1, '2025-05-03 23:53:34'),
(27, 'The Kite Runner', 'Khaled Hosseini', '14.00', 'Fiction', 'Novel of friendship and redemption.', 'https://images.pexels.com/photos/46274/pexels-photo-46274.jpeg', 1, '2025-05-03 23:53:34'),
(28, 'The Book Thief', 'Markus Zusak', '13.90', 'Historical', 'Historical novel during WWII.', 'https://images.pexels.com/photos/46274/pexels-photo-46274.jpeg', 1, '2025-05-03 23:53:34'),
(29, 'The Fault in Our Stars', 'John Green', '12.75', 'Young Adult', 'Young adult romance.', 'https://images.pexels.com/photos/46274/pexels-photo-46274.jpeg', 1, '2025-05-03 23:53:34'),
(30, 'Gone with the Wind', 'Margaret Mitchell', '16.00', 'Historical', 'Historical romance.', 'https://images.pexels.com/photos/46274/pexels-photo-46274.jpeg', 1, '2025-05-03 23:53:34'),
(90, 'f', '11', '12.00', 's', 'sdsd', NULL, 5, '2025-05-04 20:15:01');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `book_id`, `quantity`, `added_at`) VALUES
(3, 5, 24, 1, '2025-05-04 18:22:07'),
(4, 5, 4, 1, '2025-05-04 18:22:13');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `created_at`) VALUES
(7, 'ش', 'x@n.m', 'kkk', 'شس', '2025-05-05 12:57:14');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('user','admin','seller') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`, `created_at`) VALUES
(1, 'admin', 'admin@example.com', '$2y$10$abcdefghijklmnopqrstuv0123456789abcdefghiJKLmnopqrstuvwx', 'admin', '2025-05-03 23:52:19'),
(4, 'x', 'x@g.c', '123', 'admin', '2025-05-04 15:35:25'),
(5, 'z', 'z@g.c', '$2y$10$/bhiLIwwuyPfoqOypOLgJO4YSohei8wbX4YKoeHnmC0DbM2nmmp7a', 'admin', '2025-05-04 17:30:35'),
(9, 'm', 'm@m.m', '$2y$10$Epuwwdj6ztbARXRQTxiZTOpY68bkd8Xo4G1sGDsHYp0X8zDRuqequ', 'seller', '2025-05-04 20:10:58'),
(10, 'zx', 'm@m.n', '$2y$10$yyoKcm2R4abZ.bSvkN8k9Oiyt.Qy0TLPiOpaR9HprDBue4sLkxR8i', 'user', '2025-05-04 20:12:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `added_by_user_id` (`added_by_user_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_book_unique` (`user_id`,`book_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`added_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
