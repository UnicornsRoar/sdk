-- phpMyAdmin SQL Dump
-- version 4.0.8
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2014-03-31 14:06:37
-- 服务器版本: 5.6.13
-- PHP 版本: 5.5.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `test`
--

-- --------------------------------------------------------

--
-- 表的结构 `sdk_accounts`
--

CREATE TABLE IF NOT EXISTS `sdk_accounts` (
  `account_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `user_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `full_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `college` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `class` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `club` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `department` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `job` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `mobile` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `reg_time` int(10) unsigned NOT NULL,
  `confirmation` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `verified` tinyint(1) unsigned NOT NULL,
  `is_admin` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`account_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- 转存表中的数据 `sdk_accounts`
--

INSERT INTO `sdk_accounts` (`account_id`, `email`, `user_name`, `full_name`, `password`, `college`, `class`, `club`, `department`, `job`, `mobile`, `reg_time`, `confirmation`, `verified`, `is_admin`) VALUES
(4, '460260@qq.com', 'harman', '', '4b0b23ba3f9a2a69c3a2f0adefe6cace6b39f916', '', '', '', '', '', '', 1396246664, '', 0, 0),
(5, '460260333@qq.com', 'luzhaodeng', '', '4b0b23ba3f9a2a69c3a2f0adefe6cace6b39f916', '', '', '', '', '', '', 1396246664, '', 1, 1);

-- --------------------------------------------------------

--
-- 表的结构 `sdk_attachments`
--

CREATE TABLE IF NOT EXISTS `sdk_attachments` (
  `attachment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(10) unsigned NOT NULL,
  `event_id` int(10) unsigned NOT NULL,
  `attachment_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `attachment_path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`attachment_id`),
  KEY `account_id` (`account_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `sdk_auth`
--

CREATE TABLE IF NOT EXISTS `sdk_auth` (
  `auth_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(10) unsigned NOT NULL,
  `is_verified` tinyint(1) unsigned NOT NULL,
  `full_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `college` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `class` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `club` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `department` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `job` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mobile` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`auth_id`),
  KEY `account_id` (`account_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `sdk_categorys`
--

CREATE TABLE IF NOT EXISTS `sdk_categorys` (
  `category_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;

--
-- 转存表中的数据 `sdk_categorys`
--

INSERT INTO `sdk_categorys` (`category_id`, `category_name`) VALUES
(1, '沙龙/讲座'),
(2, '比赛'),
(3, '演出'),
(4, '公益活动'),
(5, '社团招新换届'),
(7, '其它');

-- --------------------------------------------------------

--
-- 表的结构 `sdk_collections`
--

CREATE TABLE IF NOT EXISTS `sdk_collections` (
  `collection_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(10) unsigned NOT NULL,
  `c_event_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`collection_id`),
  KEY `account_id` (`account_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `sdk_comments`
--

CREATE TABLE IF NOT EXISTS `sdk_comments` (
  `comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  `account_id` int(10) unsigned NOT NULL,
  `comment` text COLLATE utf8_unicode_ci NOT NULL,
  `pre_comment_id` int(10) unsigned NOT NULL,
  `source_comment_id` int(10) unsigned NOT NULL,
  `post_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`comment_id`),
  KEY `account_id` (`account_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `sdk_events`
--

CREATE TABLE IF NOT EXISTS `sdk_events` (
  `event_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(10) unsigned NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  `poster_src` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `event_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `event_host` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `start_time` int(10) unsigned NOT NULL,
  `end_time` int(10) NOT NULL,
  `event_locale` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `event_details` text COLLATE utf8_unicode_ci NOT NULL,
  `has_attachments` tinyint(1) unsigned NOT NULL,
  `has_photos` tinyint(1) unsigned NOT NULL,
  `has_videos` tinyint(1) unsigned NOT NULL,
  `has_comments` tinyint(1) unsigned NOT NULL,
  `is_posted` tinyint(1) unsigned NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  `last_edit_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '最后修改时间',
  `is_audited` tinyint(1) NOT NULL COMMENT '是否通过审阅',
  `share_count` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`event_id`),
  KEY `account_id` (`account_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `sdk_field_record`
--

CREATE TABLE IF NOT EXISTS `sdk_field_record` (
  `record_id` int(11) NOT NULL AUTO_INCREMENT,
  `field_id` int(11) NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `account_id` int(11) NOT NULL COMMENT '报名用户的id',
  `event_id` int(11) NOT NULL,
  `edit_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`record_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `sdk_hots`
--

CREATE TABLE IF NOT EXISTS `sdk_hots` (
  `hot_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) NOT NULL,
  `begin_time` int(10) NOT NULL COMMENT '开始被标记为hot的时间',
  `off_time` int(10) NOT NULL DEFAULT '0' COMMENT '取消被标记hot的时间',
  `banner_img` text COLLATE utf8_unicode_ci NOT NULL COMMENT '放到banner上的图片',
  PRIMARY KEY (`hot_id`),
  KEY `hot_id` (`hot_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;

--
-- 转存表中的数据 `sdk_hots`
--

INSERT INTO `sdk_hots` (`hot_id`, `event_id`, `begin_time`, `off_time`, `banner_img`) VALUES
(1, 356, 1368948832, 0, '/App/Public/Images/banner/1.png'),
(7, 511, 1368948945, 0, '/App/Public/Images/banner/yiguangnian.jpg'),
(4, 393, 1368948834, 0, '/App/Public/Images/banner/5.jpg'),
(6, 443, 1368948850, 0, '/App/Public/Images/banner/voice.jpg');

-- --------------------------------------------------------

--
-- 表的结构 `sdk_photos`
--

CREATE TABLE IF NOT EXISTS `sdk_photos` (
  `photo_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(10) unsigned NOT NULL,
  `event_id` int(10) unsigned NOT NULL,
  `photo_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `photo_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `photo_src` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `thumb_src` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`photo_id`),
  KEY `account_id` (`account_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `sdk_table_field`
--

CREATE TABLE IF NOT EXISTS `sdk_table_field` (
  `field_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `field_name` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `is_long` tinyint(4) DEFAULT '0' COMMENT '备注',
  PRIMARY KEY (`field_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `sdk_tags`
--

CREATE TABLE IF NOT EXISTS `sdk_tags` (
  `event_id` int(10) NOT NULL COMMENT '活动ID',
  `theme_id` int(5) NOT NULL COMMENT '主题ID',
  KEY `event_id` (`event_id`,`theme_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='链接活动和主题的表';

--
-- 转存表中的数据 `sdk_tags`
--

INSERT INTO `sdk_tags` (`event_id`, `theme_id`) VALUES
(380, 1),
(382, 1),
(383, 1),
(392, 1),
(393, 1),
(394, 1);

-- --------------------------------------------------------

--
-- 表的结构 `sdk_themes`
--

CREATE TABLE IF NOT EXISTS `sdk_themes` (
  `theme_id` int(5) NOT NULL AUTO_INCREMENT,
  `theme_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `discription` text COLLATE utf8_unicode_ci NOT NULL,
  `crecate_time` int(10) NOT NULL COMMENT '主题创建时间',
  `theme_img` text COLLATE utf8_unicode_ci NOT NULL COMMENT '主题放在banner上的图片',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '主题的状态，1为上线，0为沉默',
  PRIMARY KEY (`theme_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `sdk_themes`
--

INSERT INTO `sdk_themes` (`theme_id`, `theme_name`, `discription`, `crecate_time`, `theme_img`, `status`) VALUES
(1, '2013年三下乡活动主题', '2013年“三下乡”活动开始啦', 1369639703, '/sae/sdk/3/App/Public/Images/banner/5.jpg', 1);

-- --------------------------------------------------------

--
-- 表的结构 `sdk_videos`
--

CREATE TABLE IF NOT EXISTS `sdk_videos` (
  `video_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(10) unsigned NOT NULL,
  `event_id` int(10) unsigned NOT NULL,
  `video_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `video_img` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `video_object` text COLLATE utf8_unicode_ci NOT NULL,
  `video_description` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`video_id`),
  KEY `account_id` (`account_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
