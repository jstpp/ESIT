SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+01:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `ALG_TEST_LIST`
--

CREATE TABLE `ALG_TEST_LIST` (
  `TEST_ID` int(11) NOT NULL,
  `test_author_id` int(11) NOT NULL,
  `problem_id` int(11) NOT NULL,
  `max_time` double NOT NULL,
  `max_memory` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `ARTICLES`
--

CREATE TABLE `ARTICLES` (
  `id` int(11) NOT NULL,
  `title` text NOT NULL,
  `author` text NOT NULL,
  `time` datetime NOT NULL DEFAULT current_timestamp(),
  `content` text NOT NULL,
  `image_path` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `NOTIFICATIONS`
--

CREATE TABLE `NOTIFICATIONS` (
  `NOTIFICATION_ID` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `time` datetime NOT NULL DEFAULT current_timestamp(),
  `content` text NOT NULL,
  `type` text NOT NULL DEFAULT ('info'),
  `is_delivered` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `PORTAL_RESOURCES`
--

CREATE TABLE `PORTAL_RESOURCES` (
  `RESOURCE_ID` int(11) NOT NULL,
  `resource_type` text NOT NULL DEFAULT ('documents'),
  `resource_name` text NOT NULL,
  `resource_path` text NOT NULL,
  `resource_comment` text NOT NULL DEFAULT ('#'),
  `is_actual` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `MISC`
--

CREATE TABLE `MISC` (
  `MISC_ID` int(11) NOT NULL,
  `misc_name` text NOT NULL,
  `misc_value` text NOT NULL DEFAULT ('-')
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `PROBLEMS`
--

CREATE TABLE `PROBLEMS` (
  `PROBLEM_ID` int(11) NOT NULL,
  `title` text NOT NULL,
  `author_id` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `maxattempts` int(11) NOT NULL,
  `maxpoints` int(11) NOT NULL,
  `problemset` int(11) NOT NULL,
  `publish_time` datetime NOT NULL DEFAULT current_timestamp(),
  `result_publish_time` datetime NOT NULL DEFAULT current_timestamp(),
  `isarchived` int(11) NOT NULL,
  `comment` text NOT NULL DEFAULT ('-')
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `PROBLEMSETS`
--

CREATE TABLE `PROBLEMSETS` (
  `SET_ID` int(11) NOT NULL,
  `title` text NOT NULL,
  `author_id` int(11) NOT NULL,
  `description` text NOT NULL DEFAULT ('Zbiór zadań'),
  `publish_time` datetime NOT NULL DEFAULT current_timestamp(),
  `isarchived` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `RESULTS`
--

CREATE TABLE `RESULTS` (
  `RESULT_ID` int(11) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `time` text NOT NULL DEFAULT ('0'),
  `memory` text DEFAULT NULL,
  `comment` text NOT NULL,
  `anws_correct` int(11) NOT NULL DEFAULT 0,
  `anws_wrong` int(11) NOT NULL DEFAULT 0,
  `anws_resource` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `SUBMISSIONS`
--

CREATE TABLE `SUBMISSIONS` (
  `SUBMISSION_ID` int(11) NOT NULL,
  `problem_id` int(11) NOT NULL,
  `problemset_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `submission_time` datetime NOT NULL DEFAULT current_timestamp(),
  `verification_time` datetime NOT NULL,
  `score` int(11) NOT NULL,
  `score_percentage` int(11) NOT NULL,
  `submission_lang` text NOT NULL DEFAULT ('n/a'),
  `mode` int(11) NOT NULL DEFAULT 1,
  `content` text NOT NULL DEFAULT ('-'),
  `comment` text NOT NULL DEFAULT ('-')
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `TERMS`
--

CREATE TABLE `TERMS` (
  `TERM_ID` int(11) NOT NULL,
  `term_name` text NOT NULL,
  `term_begin` datetime NOT NULL DEFAULT current_timestamp(),
  `term_end` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `TEST_QUESTIONS`
--

CREATE TABLE `TEST_QUESTIONS` (
  `QUESTION_ID` int(11) NOT NULL,
  `problem_id` int(11) NOT NULL,
  `question` text NOT NULL DEFAULT ('-'),
  `anwsers` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `USERS`
--

CREATE TABLE `USERS` (
  `USER_ID` int(11) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `role` int(11) NOT NULL,
  `mail` text NOT NULL,
  `joined` datetime NOT NULL DEFAULT current_timestamp(),
  `lastlogin` datetime NOT NULL DEFAULT current_timestamp(),
  `name` text NOT NULL,
  `surname` text NOT NULL,
  `organization` text NOT NULL,
  `settings` text NOT NULL DEFAULT ('{"code_editor_theme":"dracula.css","dark_mode":"1"}')
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `ALG_TEST_LIST`
--
ALTER TABLE `ALG_TEST_LIST`
  ADD PRIMARY KEY (`TEST_ID`),
  ADD KEY `tests_problem_id` (`problem_id`),
  ADD KEY `tests_author_id` (`test_author_id`);

--
-- Indeksy dla tabeli `ARTICLES`
--
ALTER TABLE `ARTICLES`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `NOTIFICATIONS`
--
ALTER TABLE `NOTIFICATIONS`
  ADD PRIMARY KEY (`NOTIFICATION_ID`),
  ADD KEY `notifications_user_id` (`user_id`);

--
-- Indeksy dla tabeli `PORTAL_RESOURCES`
--
ALTER TABLE `PORTAL_RESOURCES`
  ADD PRIMARY KEY (`RESOURCE_ID`);

--
-- Indeksy dla tabeli `MISC`
--
ALTER TABLE `MISC`
  ADD PRIMARY KEY (`MISC_ID`);

--
-- Indeksy dla tabeli `PROBLEMS`
--
ALTER TABLE `PROBLEMS`
  ADD PRIMARY KEY (`PROBLEM_ID`),
  ADD KEY `problemset_id` (`problemset`),
  ADD KEY `author_id` (`author_id`);

--
-- Indeksy dla tabeli `PROBLEMSETS`
--
ALTER TABLE `PROBLEMSETS`
  ADD PRIMARY KEY (`SET_ID`),
  ADD KEY `author_2_id` (`author_id`);

--
-- Indeksy dla tabeli `RESULTS`
--
ALTER TABLE `RESULTS`
  ADD PRIMARY KEY (`RESULT_ID`),
  ADD KEY `result_submission_id` (`submission_id`);

--
-- Indeksy dla tabeli `SUBMISSIONS`
--
ALTER TABLE `SUBMISSIONS`
  ADD PRIMARY KEY (`SUBMISSION_ID`),
  ADD KEY `submission_problem_id` (`problem_id`),
  ADD KEY `submission_user_id` (`user_id`),
  ADD KEY `submission_problemset_id` (`problemset_id`);

--
-- Indeksy dla tabeli `TERMS`
--
ALTER TABLE `TERMS`
  ADD PRIMARY KEY (`TERM_ID`);

--
-- Indeksy dla tabeli `TEST_QUESTIONS`
--
ALTER TABLE `TEST_QUESTIONS`
  ADD PRIMARY KEY (`QUESTION_ID`),
  ADD KEY `question_problem_id` (`problem_id`);

--
-- Indeksy dla tabeli `USERS`
--
ALTER TABLE `USERS`
  ADD PRIMARY KEY (`USER_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ALG_TEST_LIST`
--
ALTER TABLE `ALG_TEST_LIST`
  MODIFY `TEST_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ARTICLES`
--
ALTER TABLE `ARTICLES`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `NOTIFICATIONS`
--
ALTER TABLE `NOTIFICATIONS`
  MODIFY `NOTIFICATION_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `PORTAL_RESOURCES`
--
ALTER TABLE `PORTAL_RESOURCES`
  MODIFY `RESOURCE_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `MISC`
--
ALTER TABLE `MISC`
  MODIFY `MISC_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `PROBLEMS`
--
ALTER TABLE `PROBLEMS`
  MODIFY `PROBLEM_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `PROBLEMSETS`
--
ALTER TABLE `PROBLEMSETS`
  MODIFY `SET_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `RESULTS`
--
ALTER TABLE `RESULTS`
  MODIFY `RESULT_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `SUBMISSIONS`
--
ALTER TABLE `SUBMISSIONS`
  MODIFY `SUBMISSION_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `TERMS`
--
ALTER TABLE `TERMS`
  MODIFY `TERM_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `TEST_QUESTIONS`
--
ALTER TABLE `TEST_QUESTIONS`
  MODIFY `QUESTION_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `USERS`
--
ALTER TABLE `USERS`
  MODIFY `USER_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ALG_TEST_LIST`
--
ALTER TABLE `ALG_TEST_LIST`
  ADD CONSTRAINT `tests_author_id` FOREIGN KEY (`test_author_id`) REFERENCES `USERS` (`USER_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tests_problem_id` FOREIGN KEY (`problem_id`) REFERENCES `PROBLEMS` (`PROBLEM_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `NOTIFICATIONS`
--
ALTER TABLE `NOTIFICATIONS`
  ADD CONSTRAINT `notifications_user_id` FOREIGN KEY (`user_id`) REFERENCES `USERS` (`USER_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `PROBLEMS`
--
ALTER TABLE `PROBLEMS`
  ADD CONSTRAINT `author_id` FOREIGN KEY (`author_id`) REFERENCES `USERS` (`USER_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `problemset_id` FOREIGN KEY (`problemset`) REFERENCES `PROBLEMSETS` (`SET_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `PROBLEMSETS`
--
ALTER TABLE `PROBLEMSETS`
  ADD CONSTRAINT `author_2_id` FOREIGN KEY (`author_id`) REFERENCES `USERS` (`USER_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `RESULTS`
--
ALTER TABLE `RESULTS`
  ADD CONSTRAINT `result_submission_id` FOREIGN KEY (`submission_id`) REFERENCES `SUBMISSIONS` (`SUBMISSION_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `SUBMISSIONS`
--
ALTER TABLE `SUBMISSIONS`
  ADD CONSTRAINT `submission_problem_id` FOREIGN KEY (`problem_id`) REFERENCES `PROBLEMS` (`PROBLEM_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `submission_problemset_id` FOREIGN KEY (`problemset_id`) REFERENCES `PROBLEMSETS` (`SET_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `submission_user_id` FOREIGN KEY (`user_id`) REFERENCES `USERS` (`USER_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `TEST_QUESTIONS`
--
ALTER TABLE `TEST_QUESTIONS`
  ADD CONSTRAINT `question_problem_id` FOREIGN KEY (`problem_id`) REFERENCES `PROBLEMS` (`PROBLEM_ID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
