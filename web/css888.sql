-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 07, 2024 at 04:15 PM
-- Server version: 5.7.24
-- PHP Version: 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `css888`
--

-- --------------------------------------------------------

--
-- Table structure for table `bet`
--

CREATE TABLE `bet` (
  `bet_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bet_amount` decimal(10,2) NOT NULL,
  `bet_outcome` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `bet`
--

INSERT INTO `bet` (`bet_id`, `user_id`, `bet_amount`, `bet_outcome`) VALUES
(1, 1, '2.00', '0.00'),
(3, 1, '1.00', '0.00'),
(4, 1, '1.00', '0.00'),
(7, 1, '48.00', '0.00'),
(8, 1, '17.00', '0.00'),
(9, 1, '17.00', '0.00'),
(10, 1, '50.00', '0.00'),
(11, 1, '50.00', '0.00'),
(150, 4, '1.00', '0.00'),
(152, 4, '1.00', '0.00'),
(153, 4, '1.00', '0.00'),
(154, 4, '1.00', '0.00'),
(181, 4, '0.00', '0.00');

--
-- Triggers `bet`
--
DELIMITER $$
CREATE TRIGGER `update_user_balance_after_bet_outcome_update` AFTER UPDATE ON `bet` FOR EACH ROW BEGIN
    -- Check if bet_outcome changed from NULL to a decimal value
    IF OLD.bet_outcome IS NULL AND NEW.bet_outcome IS NOT NULL THEN
        -- Update the user's balance by adding the new bet_outcome value
        UPDATE user
        SET balance = balance + NEW.bet_outcome
        WHERE userID = NEW.user_id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `game`
--

CREATE TABLE `game` (
  `game_id` int(11) NOT NULL,
  `game_type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `game`
--

INSERT INTO `game` (`game_id`, `game_type`) VALUES
(1, 'Football'),
(2, 'Basketball'),
(3, 'Valorant');

-- --------------------------------------------------------

--
-- Table structure for table `matches`
--

CREATE TABLE `matches` (
  `match_id` int(11) NOT NULL,
  `team_id_1` int(11) NOT NULL,
  `team_id_2` int(11) NOT NULL,
  `status` varchar(15) DEFAULT NULL,
  `start_time` datetime NOT NULL,
  `score_team_1` int(11) NOT NULL DEFAULT '0',
  `score_team_2` int(11) NOT NULL DEFAULT '0',
  `end_time` datetime NOT NULL,
  `game_id` int(11) NOT NULL,
  `odd_team1` decimal(4,2) DEFAULT NULL,
  `odd_draw` decimal(4,2) NOT NULL,
  `odd_team2` decimal(4,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `matches`
--

INSERT INTO `matches` (`match_id`, `team_id_1`, `team_id_2`, `status`, `start_time`, `score_team_1`, `score_team_2`, `end_time`, `game_id`, `odd_team1`, `odd_draw`, `odd_team2`) VALUES
(49, 28, 29, 'notstart', '2024-11-30 17:38:00', 0, 0, '2024-11-30 21:38:00', 1, '1.34', '26.21', '23.47'),
(50, 28, 29, 'ended', '2024-11-21 19:21:00', 3, 0, '2024-11-21 22:21:00', 1, '1.27', '2.79', '2.78'),
(51, 30, 33, 'notstart', '2024-12-01 22:29:00', 0, 0, '2024-12-01 13:29:00', 2, '2.37', '5.48', '2.78'),
(52, 33, 38, 'notstart', '2024-12-02 23:30:00', 0, 0, '2024-12-02 14:30:00', 2, '2.78', '5.75', '5.78'),
(53, 39, 31, 'notstart', '2024-12-05 20:31:00', 0, 0, '2024-12-05 23:31:00', 2, '10.45', '4.78', '10.53'),
(54, 32, 33, 'notstart', '2024-12-04 20:31:00', 0, 0, '2024-12-04 22:31:00', 2, '2.78', '5.44', '5.79'),
(55, 38, 35, 'notstart', '2024-12-06 20:32:00', 0, 0, '2024-11-27 22:32:00', 2, '2.37', '9.64', '7.89'),
(56, 34, 36, 'notstart', '2024-12-01 20:32:00', 0, 0, '2024-11-27 22:32:00', 2, '3.33', '1.57', '2.22'),
(57, 34, 32, 'notstart', '2024-12-04 20:32:00', 0, 0, '2024-12-04 23:32:00', 2, '5.78', '5.37', '1.27'),
(58, 47, 46, 'notstart', '2024-12-02 21:03:00', 0, 0, '2024-12-02 12:04:00', 1, '2.13', '2.87', '2.78'),
(59, 42, 46, 'notstart', '2024-12-03 21:04:00', 0, 0, '2024-11-27 12:04:00', 1, '2.37', '2.78', '2.78'),
(60, 29, 41, 'notstart', '2024-12-03 23:04:00', 0, 0, '2024-12-03 12:04:00', 1, '8.97', '5.78', '5.78'),
(61, 46, 44, 'notstart', '2024-12-04 21:05:00', 0, 0, '2024-12-04 12:05:00', 1, '3.33', '1.78', '2.22'),
(62, 45, 40, 'notstart', '2024-12-01 21:05:00', 0, 0, '2024-12-01 12:05:00', 1, '6.78', '3.45', '5.78'),
(63, 43, 29, 'notstart', '2024-12-03 12:05:00', 0, 0, '2024-12-03 14:05:00', 1, '6.78', '5.78', '5.14'),
(64, 42, 43, 'notstart', '2024-12-04 21:06:00', 0, 0, '2024-12-04 12:06:00', 1, '6.66', '4.44', '5.55'),
(65, 40, 47, 'notstart', '2024-12-06 22:06:00', 0, 0, '2024-12-06 12:06:00', 1, '5.67', '6.74', '2.78'),
(66, 44, 29, 'notstart', '2024-12-01 23:09:00', 0, 0, '2024-12-01 14:09:00', 1, '9.64', '5.11', '5.78'),
(67, 48, 49, 'notstart', '2024-12-04 21:18:00', 0, 0, '2024-12-04 12:18:00', 3, '3.47', '2.11', '2.78'),
(68, 50, 53, 'notstart', '2024-12-05 23:18:00', 0, 0, '2024-12-05 13:18:00', 3, '2.78', '2.11', '2.78'),
(69, 50, 49, 'notstart', '2024-12-04 21:18:00', 0, 0, '2024-12-04 12:18:00', 3, '2.78', '7.98', '2.11'),
(70, 51, 55, 'notstart', '2024-12-01 22:19:00', 0, 0, '2024-12-01 12:19:00', 3, '5.65', '77.80', '5.31'),
(71, 52, 54, 'notstart', '2024-12-01 23:19:00', 0, 0, '2024-12-01 14:23:00', 3, '6.89', '2.36', '4.56'),
(72, 50, 56, 'notstart', '2024-12-02 23:19:00', 0, 0, '2024-12-02 13:19:00', 3, '5.41', '2.67', '2.78'),
(73, 55, 56, 'notstart', '2024-12-04 21:20:00', 0, 0, '2024-12-04 12:20:00', 3, '3.64', '5.64', '2.36'),
(74, 51, 57, 'notstart', '2024-12-03 22:20:00', 0, 0, '2024-12-03 12:20:00', 3, '6.37', '5.33', '5.78'),
(75, 53, 52, 'notstart', '2024-12-02 21:20:00', 0, 0, '2024-12-02 12:20:00', 3, '2.78', '1.11', '5.78'),
(76, 50, 49, 'notstart', '2024-12-01 23:21:00', 0, 0, '2024-12-01 13:21:00', 3, '5.78', '9.47', '2.78'),
(77, 28, 42, 'ended', '2024-11-13 21:22:00', 3, 0, '2024-11-13 12:22:00', 1, '2.36', '5.14', '2.78'),
(78, 43, 45, 'ended', '2024-11-14 21:22:00', 0, 3, '2024-11-14 12:22:00', 1, '3.17', '2.13', '2.67'),
(79, 42, 47, 'ended', '2024-11-12 21:22:00', 2, 2, '2024-11-13 00:23:00', 1, '3.14', '2.36', '5.78'),
(80, 45, 46, 'ended', '2024-11-10 21:23:00', 1, 0, '2024-11-10 12:23:00', 1, '3.26', '2.17', '2.17'),
(81, 47, 29, 'ended', '2024-11-08 21:23:00', 4, 3, '2024-11-08 13:00:00', 1, '3.24', '5.55', '5.27'),
(82, 44, 41, 'ended', '2024-11-07 16:23:00', 2, 3, '2024-11-07 12:00:00', 1, '3.65', '2.78', '5.47'),
(83, 45, 42, 'ended', '2024-11-07 21:24:00', 2, 3, '2024-11-27 12:00:00', 1, '2.34', '5.78', '5.78'),
(84, 29, 47, 'ended', '2024-11-08 23:24:00', 1, 1, '2024-11-08 12:24:00', 1, '3.65', '5.78', '5.78'),
(85, 46, 47, 'ended', '2024-11-01 21:24:00', 3, 3, '2024-11-01 12:40:00', 1, '2.36', '3.78', '5.78'),
(86, 28, 43, 'ended', '2024-11-01 23:25:00', 5, 2, '2024-11-01 12:30:00', 1, '2.67', '3.14', '5.78'),
(87, 28, 42, 'ended', '2024-11-12 21:25:00', 4, 2, '2024-11-12 12:28:00', 1, '2.37', '2.78', '2.78'),
(88, 45, 29, 'ended', '2024-10-31 21:25:00', 1, 3, '2024-10-31 12:26:00', 1, '2.37', '67.47', '2.78'),
(89, 42, 44, 'ended', '2024-11-07 21:26:00', 6, 3, '2024-11-07 13:26:00', 1, '2.78', '31.17', '2.78'),
(90, 44, 40, 'ended', '2024-11-03 21:26:00', 1, 0, '2024-11-03 13:26:00', 1, '3.17', '4.65', '2.78'),
(91, 42, 28, 'ended', '2024-11-07 21:26:00', 0, 0, '2024-11-07 13:26:00', 1, '3.17', '1.23', '5.71'),
(92, 34, 35, 'ended', '2024-11-07 21:27:00', 120, 108, '2024-11-07 12:27:00', 2, '1.27', '2.78', '3.69'),
(93, 36, 39, 'ended', '2024-11-07 21:27:00', 64, 74, '2024-11-07 12:27:00', 2, '53.56', '2.78', '1.23'),
(94, 37, 35, 'ended', '2024-11-14 21:27:00', 49, 52, '2024-11-14 13:27:00', 2, '3.69', '2.77', '2.34'),
(95, 32, 39, 'ended', '2024-11-08 21:28:00', 56, 54, '2024-11-08 12:28:00', 2, '2.36', '3.77', '4.57'),
(96, 34, 33, 'ended', '2024-11-01 21:28:00', 112, 100, '2024-11-01 12:03:00', 2, '3.64', '10.96', '5.27'),
(97, 36, 30, 'ended', '2024-11-12 23:28:00', 123, 40, '2024-11-12 14:28:00', 2, '2.56', '2.78', '52.65'),
(98, 33, 31, 'ended', '2024-11-03 21:29:00', 95, 82, '2024-11-03 15:32:00', 2, '3.67', '2.78', '5.78'),
(99, 31, 30, 'ended', '2024-11-06 21:29:00', 114, 112, '2024-11-06 13:00:00', 2, '3.65', '5.55', '2.17'),
(100, 34, 38, 'ended', '2024-11-06 21:29:00', 102, 102, '2024-11-06 13:33:00', 2, '2.36', '5.67', '4.57'),
(101, 37, 36, 'ended', '2024-11-09 21:30:00', 100, 100, '2024-11-09 12:30:00', 2, '3.78', '3.78', '2.78'),
(102, 37, 39, 'ended', '2024-11-18 21:30:00', 82, 97, '2024-11-22 13:30:00', 2, '3.65', '2.78', '5.78'),
(103, 35, 30, 'ended', '2024-11-24 15:30:00', 67, 75, '2024-11-24 12:30:00', 2, '3.67', '2.78', '22.78'),
(104, 32, 30, 'ended', '2024-11-03 21:30:00', 74, 78, '2024-11-03 13:31:00', 2, '2.37', '75.11', '75.54'),
(105, 36, 34, 'ended', '2024-11-14 13:31:00', 92, 90, '2024-11-14 14:31:00', 2, '2.78', '9.64', '2.21'),
(106, 30, 39, 'ended', '2024-11-13 21:31:00', 73, 91, '2024-11-13 16:31:00', 2, '5.78', '36.65', '2.78'),
(107, 31, 32, 'ended', '2024-11-08 23:31:00', 105, 128, '2024-11-08 12:31:00', 2, '3.21', '2.31', '2.17'),
(108, 48, 49, 'ended', '2024-11-14 21:33:00', 13, 8, '2024-11-14 12:33:00', 3, '3.24', '2.23', '6.75'),
(109, 49, 57, 'ended', '2024-11-06 21:34:00', 13, 4, '2024-11-06 12:34:00', 3, '3.24', '5.78', '1.23'),
(110, 54, 48, 'ended', '2024-11-10 21:35:00', 8, 13, '2024-11-10 00:34:00', 3, '2.37', '9.63', '2.11'),
(111, 54, 56, 'ended', '2024-11-07 17:34:00', 5, 13, '2024-11-07 18:34:00', 3, '3.36', '2.78', '1.78'),
(112, 53, 54, 'ended', '2024-11-14 21:35:00', 12, 13, '2024-11-14 22:35:00', 3, '3.67', '2.11', '5.78'),
(113, 51, 55, 'ended', '2024-11-03 21:35:00', 13, 0, '2024-11-03 23:35:00', 3, '2.78', '9.78', '2.11'),
(114, 52, 54, 'ended', '2024-11-01 21:35:00', 13, 1, '2024-11-01 23:35:00', 3, '3.75', '9.62', '3.78'),
(115, 49, 56, 'ended', '2024-11-08 21:36:00', 2, 13, '2024-11-08 23:36:00', 3, '3.21', '59.64', '27.65'),
(116, 57, 51, 'ended', '2024-11-06 11:36:00', 5, 13, '2024-11-06 13:36:00', 3, '3.64', '2.37', '5.78'),
(117, 50, 55, 'ended', '2024-11-06 21:36:00', 13, 0, '2024-11-06 12:36:00', 3, '3.64', '5.78', '2.78'),
(118, 52, 48, 'ended', '2024-11-12 12:37:00', 9, 13, '2024-11-12 13:41:00', 3, '3.65', '9.99', '2.78'),
(119, 50, 49, 'ended', '2024-11-01 21:37:00', 9, 13, '2024-11-01 23:37:00', 3, '2.36', '12.78', '9.45'),
(120, 50, 57, 'ended', '2024-11-03 21:37:00', 8, 13, '2024-11-03 00:37:00', 3, '3.65', '2.78', '4.78'),
(121, 53, 54, 'ended', '2024-11-21 21:38:00', 13, 2, '2024-11-21 23:38:00', 3, '2.34', '2.78', '3.78'),
(122, 55, 49, 'ended', '2024-11-22 21:38:00', 6, 13, '2024-11-22 23:38:00', 3, '3.45', '3.78', '5.78'),
(123, 56, 50, 'ended', '2024-11-04 21:38:00', 13, 7, '2024-11-04 23:17:00', 3, '2.22', '4.44', '3.33'),
(124, 52, 51, 'ended', '2024-11-07 21:38:00', 13, 10, '2024-11-07 23:38:00', 3, '6.66', '8.88', '7.77'),
(125, 42, 43, 'ended', '2024-11-27 22:37:00', 0, 0, '2024-11-27 12:37:00', 1, '2.34', '2.78', '2.78'),
(126, 34, 36, 'ended', '2024-11-28 22:37:00', 0, 0, '2024-11-27 22:37:00', 2, '2.78', '2.47', '5.78'),
(127, 52, 51, 'ended', '2024-11-27 22:41:00', 1, 0, '2024-11-27 22:43:00', 3, '2.78', '2.71', '5.78'),
(128, 48, 52, 'ended', '2024-11-27 22:43:00', 2, 1, '2024-11-27 22:50:00', 3, '2.74', '1.78', '2.78'),
(129, 28, 29, 'ended', '2024-11-28 21:28:00', 0, 0, '2024-11-28 21:30:00', 1, '3.50', '4.50', '2.50'),
(130, 30, 35, 'playing', '2024-11-28 21:00:00', 0, 0, '2024-11-28 23:30:00', 2, '1.20', '21.04', '1.78'),
(131, 41, 46, 'ended', '2024-11-28 21:33:00', 0, 0, '2024-11-28 21:43:00', 1, '2.13', '1.23', '2.71'),
(132, 28, 47, 'playing', '2024-11-28 15:05:00', 0, 0, '2024-11-29 01:05:00', 1, '2.31', '2.87', '2.78'),
(133, 32, 36, NULL, '2024-12-01 10:52:00', 0, 0, '2024-12-01 13:52:00', 2, '2.37', '2.78', '5.78');

--
-- Triggers `matches`
--
DELIMITER $$
CREATE TRIGGER `update_match_bet_result` AFTER UPDATE ON `matches` FOR EACH ROW BEGIN
    DECLARE winning_team_id INT DEFAULT NULL;

    -- Check if the match status has changed to 'ended'
    IF NEW.status = 'ended' THEN
        -- Determine the outcome of the match based on the scores
        IF NEW.score_team_1 > NEW.score_team_2 THEN
            SET winning_team_id = NEW.team_id_1;  -- Team 1 wins
        ELSEIF NEW.score_team_1 < NEW.score_team_2 THEN
            SET winning_team_id = NEW.team_id_2;  -- Team 2 wins
        END IF;

        -- Update results in the match_bet table
        UPDATE match_bet
        SET 
            result = CASE
                WHEN winning_team_id IS NULL AND match_bet.teamid_bet_on IS NULL THEN 1  -- Correct bet on a draw
                WHEN match_bet.teamid_bet_on = winning_team_id THEN 1  -- Correct bet on the winning team
                ELSE 0  -- Incorrect bet
            END
        WHERE match_bet.match_id = NEW.match_id;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `valid_status_before_insert` BEFORE INSERT ON `matches` FOR EACH ROW BEGIN
    -- Ensure the status is valid before inserting (either 'notstart', 'playing', 'ended', or NULL)
    IF NOT (NEW.status IN ('notstart', 'playing', 'ended') OR NEW.status IS NULL) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid match status';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `valid_status_before_update` BEFORE UPDATE ON `matches` FOR EACH ROW BEGIN
    -- Ensure the status is valid before updating (either 'notstart', 'playing', 'ended', or NULL)
    IF NOT (NEW.status IN ('notstart', 'playing', 'ended') OR NEW.status IS NULL) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid match status';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `match_bet`
--

CREATE TABLE `match_bet` (
  `matchbet_id` int(11) NOT NULL,
  `bet_id` int(11) NOT NULL,
  `teamid_bet_on` int(11) DEFAULT NULL,
  `odd` decimal(4,2) NOT NULL,
  `match_id` int(11) NOT NULL,
  `result` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Triggers `match_bet`
--
DELIMITER $$
CREATE TRIGGER `enforce_valid_result_before_insert` BEFORE INSERT ON `match_bet` FOR EACH ROW BEGIN
    -- Ensure the result is valid before inserting (either 1, 0, or NULL)
    IF NOT (NEW.result IN (1, 0) OR NEW.result IS NULL) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid result value';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `enforce_valid_result_before_update` BEFORE UPDATE ON `match_bet` FOR EACH ROW BEGIN
    -- Ensure the result is valid before updating (either 1, 0, or NULL)
    IF NOT (NEW.result IN (1, 0) OR NEW.result IS NULL) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid result value';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_bet_outcome` AFTER UPDATE ON `match_bet` FOR EACH ROW BEGIN
    DECLARE total_odd DECIMAL(10,2);
    DECLARE has_lost BOOLEAN;
    DECLARE all_results_present BOOLEAN;

    -- Check if any match_bet record for the bet_id has result 0
    SET has_lost = (SELECT COUNT(*) > 0
                    FROM match_bet
                    WHERE bet_id = NEW.bet_id AND result = 0);

    -- Check if any match_bet record for the bet_id has result IS NULL
    SET all_results_present = (SELECT COUNT(*) = 0
                               FROM match_bet
                               WHERE bet_id = NEW.bet_id AND result IS NULL);

    -- Proceed only if all results are present
    IF all_results_present THEN
        -- If any bet is lost, set the bet_outcome to 0
        IF has_lost THEN
            UPDATE bet
            SET bet_outcome = 0
            WHERE bet_id = NEW.bet_id;
        ELSE
            -- Calculate the total odd by multiplying all odds if all bets are won
            SET total_odd = (SELECT EXP(SUM(LOG(odd)))
                             FROM match_bet
                             WHERE bet_id = NEW.bet_id);

            -- Update the bet_outcome in the bet table
            UPDATE bet
            SET bet_outcome = bet_amount * total_odd
            WHERE bet_id = NEW.bet_id;
        END IF;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `team`
--

CREATE TABLE `team` (
  `team_id` int(11) NOT NULL,
  `team_name` varchar(50) NOT NULL,
  `game_id` int(11) NOT NULL,
  `team_logo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `team`
--

INSERT INTO `team` (`team_id`, `team_name`, `game_id`, `team_logo`) VALUES
(28, 'Liverpool', 1, 'team_6746f5d352a6a6.59473055.png'),
(29, 'Manchester United', 1, 'team_6746f62a7fcd73.13470065.png'),
(30, 'Atlanta Hawks', 2, 'team_67471bfb97e464.05329665.png'),
(31, 'Boston Celtics', 2, 'team_67471c5671ba57.18383953.png'),
(32, 'Brooklyn Nets', 2, 'team_67471ca0158db7.65478573.png'),
(33, 'Charlotte Hornets', 2, 'team_67471cc962b060.13500756.png'),
(34, 'Chicago Bulls', 2, 'team_67471ce31154b7.52684585.png'),
(35, 'Cleveland Cavaliers', 2, 'team_67471cff62e272.65413171.png'),
(36, 'Dallas Mavericks', 2, 'team_67471d22570529.44533379.png'),
(37, 'Denver Nuggets', 2, 'team_67471d42a8b917.12087525.png'),
(38, 'Detroit Pistons', 2, 'team_67471d63d567c9.19568493.png'),
(39, 'Golden State', 2, 'team_67471d7d13d524.85587959.png'),
(40, 'Arsenal', 1, 'team_67471dd28dc2c1.04502298.png'),
(41, 'Aston Villa', 1, 'team_67471e044f1cb0.63455775.png'),
(42, 'Chelsea', 1, 'team_67471e29c6f097.56471317.png'),
(43, 'Everton', 1, 'team_67471e3ed53359.36869897.png'),
(44, 'Manchester City', 1, 'team_67471e5bf3b782.85303575.png'),
(45, 'Newcastle United', 1, 'team_67471e74e630f5.69418832.png'),
(46, 'Nottingham Forest', 1, 'team_67471e846769e3.46270641.png'),
(47, 'Crystal Palace', 1, 'team_67471eb379f1a9.08696145.png'),
(48, 'Paper Rex', 3, 'team_674728c1402e91.80164769.png'),
(49, 'Liquid', 3, 'team_674728e014fe36.17530793.png'),
(50, 'Fnatic', 3, 'team_674728f4f17063.98995640.png'),
(51, 'Sentinels', 3, 'team_674729613f2983.09001513.png'),
(52, 'TSM', 3, 'team_6747297f700c58.63986989.png'),
(53, 'Turtle Troop', 3, 'team_6747299ca2b958.43788698.png'),
(54, 'Cloud9', 3, 'team_674729ad7a0815.93401762.png'),
(55, 'MOUZ', 3, 'team_674729d95378f7.74282234.png'),
(56, '100 Thieves', 3, 'team_674729f616d3d4.84963051.png'),
(57, 'Full Sense', 3, 'team_67472a14962579.06034162.png');

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `transaction` (
  `transactionID` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `transaction_datetime` varbinary(255) NOT NULL,
  `transaction_type` varchar(20) NOT NULL,
  `transaction_status` varchar(15) NOT NULL,
  `method` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Triggers `transaction`
--
DELIMITER $$
CREATE TRIGGER `add_balance_on_withdraw_cancel` AFTER UPDATE ON `transaction` FOR EACH ROW BEGIN
    -- Check if the transaction type is 'withdraw' and the status is updated to 'cancel'
    IF NEW.transaction_type = 'withdraw' AND NEW.transaction_status = 'cancel' THEN
        -- Update the user's balance by adding the transaction amount back
        UPDATE user
        SET balance = balance + NEW.amount
        WHERE userID = NEW.user_id;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `enforce_valid_status_before_insert` BEFORE INSERT ON `transaction` FOR EACH ROW BEGIN
    -- Ensure the transaction_status is valid before inserting
    IF NOT (NEW.transaction_status IN ('complete', 'cancel', 'pending')) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid transaction status';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `enforce_valid_status_before_update` BEFORE UPDATE ON `transaction` FOR EACH ROW BEGIN
    -- Ensure the transaction_status is valid before updating
    IF NOT (NEW.transaction_status IN ('complete', 'cancel', 'pending')) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid transaction status';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `enforce_valid_transaction_type_before_insert` BEFORE INSERT ON `transaction` FOR EACH ROW BEGIN
    -- Ensure the transaction_type is valid before inserting
    IF NOT (NEW.transaction_type IN ('deposit', 'withdraw')) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid transaction type';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `enforce_valid_transaction_type_before_update` BEFORE UPDATE ON `transaction` FOR EACH ROW BEGIN
    -- Ensure the transaction_type is valid before updating
    IF NOT (NEW.transaction_type IN ('deposit', 'withdraw')) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid transaction type';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_balance_after_deposit_or_withdraw` AFTER UPDATE ON `transaction` FOR EACH ROW BEGIN
    -- Check if the transaction status has changed to 'complete' 
    -- and that the status was previously 'pending'
    IF NEW.transaction_status = 'complete' AND OLD.transaction_status = 'pending' THEN
        -- If the transaction type is 'deposit', add the amount to the user's balance
        IF NEW.transaction_type = 'deposit' THEN
            UPDATE user
            SET balance = balance + NEW.amount
            WHERE userID = NEW.user_id;
        END IF;

        -- If the transaction type is 'withdraw', subtract the amount from the user's balance
        
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `userID` int(11) NOT NULL,
  `balance` decimal(10,2) DEFAULT '0.00',
  `username` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `phonenumber` varchar(10) NOT NULL,
  `hashpassword` varchar(128) NOT NULL,
  `fname` varchar(50) DEFAULT NULL,
  `lname` varchar(50) DEFAULT NULL,
  `doB` date DEFAULT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`userID`, `balance`, `username`, `email`, `phonenumber`, `hashpassword`, `fname`, `lname`, `doB`, `role`) VALUES
(1, '0.00', '123', '12@gmail.com', '3123133211', '$2y$10$mMaAp.fY1C0Tt9Z.FR.6FOrMVhUrezkDQhBGOyoxdRq2fFUN3W04m', NULL, NULL, NULL, 'user'),
(2, '10.00', 'ABC', '99@gmail.com', '1231235431', '$2y$10$HT10zIi0NYRZe5dBkAPi7.47Ei0DTH8ZE0NSpvkH/Io6VzeY4A/6e', NULL, NULL, NULL, 'user'),
(3, '0.00', 'AA', '123@gmail.com', '1231233211', '$2y$10$pjYweb3HiKkHLyrUWKGq0OJNZG1uV9SERoz5NlL41SHdOnLOMUcVW', NULL, NULL, NULL, 'user'),
(4, '0.00', 'Admin', 'Admin@gmail.com', '1234321431', '$2y$10$nd0yjKeZYRZFdBA5InIa9.b06warsC7Xn5UQ2LM9K0oyd6BAeZISm', 'captain', 'eiei', '2024-11-09', 'admin'),
(5, '1253.00', 'Test', 'Test@gmail.com', '1234123123', '$2y$10$RUh8bIQb2/I7mn7KEq1.yuGI/HuaKKmABkEwoQgtt/sLbJsvpBk7S', NULL, NULL, NULL, 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bet`
--
ALTER TABLE `bet`
  ADD PRIMARY KEY (`bet_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `game`
--
ALTER TABLE `game`
  ADD PRIMARY KEY (`game_id`);

--
-- Indexes for table `matches`
--
ALTER TABLE `matches`
  ADD PRIMARY KEY (`match_id`),
  ADD KEY `game_id` (`game_id`);

--
-- Indexes for table `match_bet`
--
ALTER TABLE `match_bet`
  ADD PRIMARY KEY (`matchbet_id`),
  ADD KEY `match_id` (`match_id`),
  ADD KEY `match_bet_ibfk_3` (`teamid_bet_on`),
  ADD KEY `match_bet_ibfk_1` (`bet_id`);

--
-- Indexes for table `team`
--
ALTER TABLE `team`
  ADD PRIMARY KEY (`team_id`),
  ADD KEY `game_id` (`game_id`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`transactionID`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`userID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bet`
--
ALTER TABLE `bet`
  MODIFY `bet_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=348;

--
-- AUTO_INCREMENT for table `game`
--
ALTER TABLE `game`
  MODIFY `game_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `matches`
--
ALTER TABLE `matches`
  MODIFY `match_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT for table `match_bet`
--
ALTER TABLE `match_bet`
  MODIFY `matchbet_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=164;

--
-- AUTO_INCREMENT for table `team`
--
ALTER TABLE `team`
  MODIFY `team_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `transaction`
--
ALTER TABLE `transaction`
  MODIFY `transactionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bet`
--
ALTER TABLE `bet`
  ADD CONSTRAINT `bet_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`userID`) ON DELETE CASCADE;

--
-- Constraints for table `matches`
--
ALTER TABLE `matches`
  ADD CONSTRAINT `matches_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `game` (`game_id`) ON DELETE CASCADE;

--
-- Constraints for table `match_bet`
--
ALTER TABLE `match_bet`
  ADD CONSTRAINT `match_bet_ibfk_1` FOREIGN KEY (`bet_id`) REFERENCES `bet` (`bet_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `match_bet_ibfk_2` FOREIGN KEY (`match_id`) REFERENCES `matches` (`match_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `match_bet_ibfk_3` FOREIGN KEY (`teamid_bet_on`) REFERENCES `team` (`team_id`) ON DELETE CASCADE;

--
-- Constraints for table `team`
--
ALTER TABLE `team`
  ADD CONSTRAINT `team_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `game` (`game_id`) ON DELETE CASCADE;

--
-- Constraints for table `transaction`
--
ALTER TABLE `transaction`
  ADD CONSTRAINT `transaction_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE;

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `update_match_status` ON SCHEDULE EVERY 1 SECOND STARTS '2024-11-10 00:10:48' ON COMPLETION NOT PRESERVE ENABLE DO UPDATE matches
SET status = CASE
    WHEN NOW() < start_time THEN 'notstart'
    WHEN NOW() >= start_time AND NOW() <= end_time THEN 'playing'
    WHEN NOW() > end_time THEN 'ended'
END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
