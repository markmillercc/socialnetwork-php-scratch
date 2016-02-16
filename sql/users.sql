
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `socialnetwork`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fb_id` bigint(20) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `picture` int(11) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` varchar(1) DEFAULT NULL,
  `hometown` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `quote` varchar(255) DEFAULT NULL,
  `quote_src` varchar(255) DEFAULT NULL,
  `education` varchar(255) DEFAULT NULL,
  `work` varchar(255) DEFAULT NULL,
  `relationship` varchar(255) DEFAULT NULL,
  `about` varchar(5000) DEFAULT NULL,
  `reg_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `fb_id` (`fb_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `users`
--

-- Insert dev user
-- username: dev@debug.site
-- passowrd: d3mo!

INSERT INTO `users` (`id`, `fb_id`, `email`, `password`, `first_name`, `last_name`, `picture`, `dob`, `gender`, `hometown`, `location`, `quote`, `quote_src`, `education`, `work`, `relationship`, `about`, `reg_date`) VALUES
(1, NULL, 'dev@debug.site', '$2y$10$OQ0aLwMADOfQJIdArkdE3upEqTD4O1ffidmwES53ZO5yz1lQKvXOC', 'Dev', 'Debug', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2014-04-09 00:00:00');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
