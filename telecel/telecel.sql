/*
Navicat MySQL Data Transfer

Source Server         : MYSQL 3306
Source Server Version : 50709
Source Host           : localhost:3306
Source Database       : telecel

Target Server Type    : MYSQL
Target Server Version : 50709
File Encoding         : 65001

Date: 2016-11-26 06:28:30
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for genset
-- ----------------------------
DROP TABLE IF EXISTS `genset`;
CREATE TABLE `genset` (
  `name` varchar(111) DEFAULT NULL,
  `sitedate` datetime DEFAULT NULL,
  `sid` int(11) NOT NULL AUTO_INCREMENT,
  `units` decimal(10,0) DEFAULT NULL,
  `rate` decimal(10,0) DEFAULT NULL,
  PRIMARY KEY (`sid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of genset
-- ----------------------------
INSERT INTO `genset` VALUES ('932hdf230', '2016-11-26 05:58:46', '3', '34', '1');
INSERT INTO `genset` VALUES ('asfad222', '2016-11-25 20:30:52', '2', '64', '1');

-- ----------------------------
-- Table structure for site
-- ----------------------------
DROP TABLE IF EXISTS `site`;
CREATE TABLE `site` (
  `name` varchar(111) DEFAULT NULL,
  `sitedate` datetime DEFAULT NULL,
  `sid` int(11) NOT NULL AUTO_INCREMENT,
  `units` decimal(10,0) DEFAULT NULL,
  `rate` decimal(10,0) DEFAULT NULL,
  PRIMARY KEY (`sid`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of site
-- ----------------------------
INSERT INTO `site` VALUES ('Manila', '2016-11-26 05:56:18', '4', '16', '2');
INSERT INTO `site` VALUES ('Gweru', '2016-11-25 20:24:36', '2', '100', '2');
INSERT INTO `site` VALUES ('Kwekwe', '2016-11-26 06:05:04', '5', '1000', '1');

-- ----------------------------
-- Table structure for topup
-- ----------------------------
DROP TABLE IF EXISTS `topup`;
CREATE TABLE `topup` (
  `oldlevel` decimal(10,0) DEFAULT NULL,
  `topuplevel` decimal(10,0) DEFAULT NULL,
  `topupdate` datetime DEFAULT NULL,
  `item` varchar(255) DEFAULT NULL,
  `tid` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`tid`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of topup
-- ----------------------------
INSERT INTO `topup` VALUES ('2', '2', '2016-11-25 07:38:31', '2', '1', 'fuel');
INSERT INTO `topup` VALUES ('100', '32', '2016-11-25 20:56:32', '2', '2', 'units');
INSERT INTO `topup` VALUES ('100', '32', '2016-11-25 20:57:45', '2', '3', 'units');
INSERT INTO `topup` VALUES ('32', '32', '2016-11-25 20:58:51', '2', '4', 'units');
INSERT INTO `topup` VALUES ('20', '4', '2016-11-26 06:14:15', '4', '5', 'units');
INSERT INTO `topup` VALUES ('16', '4', '2016-11-26 06:15:13', '4', '6', 'units');
INSERT INTO `topup` VALUES ('33', '3', '2016-11-26 06:15:26', '3', '7', 'units');
INSERT INTO `topup` VALUES ('12', '4', '2016-11-26 06:15:39', '4', '8', 'units');
INSERT INTO `topup` VALUES ('30', '4', '2016-11-26 06:15:51', '3', '9', 'units');

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) DEFAULT NULL,
  `surname` varchar(40) DEFAULT NULL,
  `sex` varchar(40) DEFAULT NULL,
  `email` varchar(40) DEFAULT NULL,
  `account` varchar(40) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `idnumber` varchar(100) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  `date` varchar(100) DEFAULT NULL,
  `access` varchar(30) DEFAULT NULL,
  `suspend` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES ('1', 'Telecel', 'telecel', 'male', 'emuroiwa@gmail.com', null, '23 Harare road', null, 'admin', 'password', '29-20018023-E-82', '1', '10/01/2013', '1', '1');
INSERT INTO `users` VALUES ('6', 'muroiwa', 'asdf', 'male', 'emuroiwa@ecbinternational.biz', null, null, null, 'manager', 'password', '263774002797', null, '11/24/2016', '3', null);
INSERT INTO `users` VALUES ('7', 'muroiwa', 'asdf', 'male', 'emuroiwa@ecbinternational.biz', '', '', '', 'user', 'password', '263774002797', '', '11/24/2016', ' 2', '');
