/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50547
Source Host           : localhost:3306
Source Database       : mycart

Target Server Type    : MYSQL
Target Server Version : 50547
File Encoding         : 65001

Date: 2017-10-26 15:56:37
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `admin_nav`
-- ----------------------------
DROP TABLE IF EXISTS `admin_nav`;
CREATE TABLE `admin_nav` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '栏目Id',
  `title` varchar(50) DEFAULT NULL COMMENT '栏目标题',
  `pid` int(11) DEFAULT NULL COMMENT '栏目上级id',
  `src` varchar(255) DEFAULT NULL COMMENT '路径',
  `status` tinyint(4) DEFAULT '1' COMMENT '状态,1开启, 2关闭',
  `icon` text COMMENT '图标',
  `path` varchar(50) DEFAULT NULL COMMENT '路径',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin_nav
-- ----------------------------
INSERT INTO `admin_nav` VALUES ('1', '系统', '0', 'javascript:;', '1', 'home', '1');
INSERT INTO `admin_nav` VALUES ('2', '首页', '1', '/index/index', '1', 'home', '1_2');
INSERT INTO `admin_nav` VALUES ('3', '管理员管理', '1', 'javascript:;', '1', 'home', '1_3');
INSERT INTO `admin_nav` VALUES ('4', '管理员', '3', '/manage/index', '1', 'home', '1_3_4');
INSERT INTO `admin_nav` VALUES ('5', '菜单管理', '3', '/menu/index', '1', 'home', '1_3_5');
INSERT INTO `admin_nav` VALUES ('7', '邮件管理', '0', 'javascript:;', '1', 'fiber_new', '7');
INSERT INTO `admin_nav` VALUES ('8', '邮件模板', '7', '/mail/template', '1', '', '7_8');
INSERT INTO `admin_nav` VALUES ('9', '邮件应用', '7', '/mail/app', '1', '', '7_9');

-- ----------------------------
-- Table structure for `admin_template`
-- ----------------------------
DROP TABLE IF EXISTS `admin_template`;
CREATE TABLE `admin_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL COMMENT '权限模版名字',
  `description` varchar(255) DEFAULT NULL COMMENT '权限模版扩展字段',
  `navs` text COMMENT '导航id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin_template
-- ----------------------------
INSERT INTO `admin_template` VALUES ('2', 'test', 'test', 'test');
INSERT INTO `admin_template` VALUES ('3', 'test', 'test', 'test');

-- ----------------------------
-- Table structure for `admin_user`
-- ----------------------------
DROP TABLE IF EXISTS `admin_user`;
CREATE TABLE `admin_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) DEFAULT NULL,
  `pwd` varchar(100) DEFAULT NULL,
  `qq` varchar(30) DEFAULT NULL COMMENT '开心玩首页',
  `tel` varchar(30) DEFAULT NULL,
  `creat_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `type` tinyint(4) DEFAULT NULL COMMENT '管理员类型',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin_user
-- ----------------------------
INSERT INTO `admin_user` VALUES ('1', 'admin', 'e10adc3949ba59abbe56e057f20f883e', null, null, '2017-03-20 10:14:35', '1');
INSERT INTO `admin_user` VALUES ('2', 'gaozhen', '283c2a8c2e448c32791110262ddae526', '2567485861', '18862324237', '2017-06-30 13:27:18', '1');
INSERT INTO `admin_user` VALUES ('3', 'test', '098f6bcd4621d373cade4e832627b4f6', '1111111', '1111111', '2017-07-05 17:23:02', '1');

-- ----------------------------
-- Table structure for `app`
-- ----------------------------
DROP TABLE IF EXISTS `app`;
CREATE TABLE `app` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'APP应用ID',
  `pid` int(11) DEFAULT NULL COMMENT '栏目上层id',
  `path` varchar(100) DEFAULT NULL COMMENT '应用层级路径',
  `title` varchar(255) DEFAULT NULL COMMENT 'APP标题',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态:1》正常；2》关闭',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `authorizeKey` char(32) DEFAULT '' COMMENT 'key 32位字符串',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='APP应用表';

-- ----------------------------
-- Records of app
-- ----------------------------
INSERT INTO `app` VALUES ('4', '0', '4', 'kxwan父级应用', '1', '2017-03-31 19:20:27', '1111111');
INSERT INTO `app` VALUES ('5', '4', '4_5', 'kxwan1', '1', '2017-03-31 19:36:43', '');
INSERT INTO `app` VALUES ('6', '4', '4_6', 'kxwan2', '1', '2017-03-31 19:41:30', '');
INSERT INTO `app` VALUES ('7', '0', '7', '农家情迷', '1', '2017-07-06 11:21:07', '77777777');

-- ----------------------------
-- Table structure for `app_template`
-- ----------------------------
DROP TABLE IF EXISTS `app_template`;
CREATE TABLE `app_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appId` int(11) NOT NULL COMMENT 'APP应用ID',
  `template_id` int(11) NOT NULL COMMENT '模板ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COMMENT='App模板关联表';

-- ----------------------------
-- Records of app_template
-- ----------------------------
INSERT INTO `app_template` VALUES ('1', '4', '3');
INSERT INTO `app_template` VALUES ('2', '4', '3');
INSERT INTO `app_template` VALUES ('3', '5', '3');
INSERT INTO `app_template` VALUES ('4', '6', '3');
INSERT INTO `app_template` VALUES ('5', '6', '3');
INSERT INTO `app_template` VALUES ('6', '6', '3');
INSERT INTO `app_template` VALUES ('7', '5', '3');
INSERT INTO `app_template` VALUES ('8', '7', '3');
INSERT INTO `app_template` VALUES ('9', '5', '3');
INSERT INTO `app_template` VALUES ('10', '6', '3');
INSERT INTO `app_template` VALUES ('12', '7', '3');
INSERT INTO `app_template` VALUES ('13', '7', '14');
INSERT INTO `app_template` VALUES ('14', '7', '16');
INSERT INTO `app_template` VALUES ('15', '7', '17');

-- ----------------------------
-- Table structure for `kuaidi_companylist`
-- ----------------------------
DROP TABLE IF EXISTS `kuaidi_companylist`;
CREATE TABLE `kuaidi_companylist` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '快递名字',
  `enName` char(50) NOT NULL DEFAULT '' COMMENT '快递英文名字',
  `phone` varchar(11) DEFAULT '' COMMENT '物流公司联系方式',
  `num` char(50) DEFAULT '' COMMENT '快递编号',
  `status` tinyint(4) DEFAULT '1' COMMENT '1.正常 2.关闭',
  PRIMARY KEY (`id`),
  KEY `num` (`num`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='本地快递列表';

-- ----------------------------
-- Records of kuaidi_companylist
-- ----------------------------
INSERT INTO `kuaidi_companylist` VALUES ('4', '顺丰', 'shunfeng', '95338', '1010', '1');
INSERT INTO `kuaidi_companylist` VALUES ('7', '圆通', '', '110', '1011', '1');

-- ----------------------------
-- Table structure for `kuaidi_data`
-- ----------------------------
DROP TABLE IF EXISTS `kuaidi_data`;
CREATE TABLE `kuaidi_data` (
  `id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '快递单号',
  `data` text COMMENT '快递具体数据',
  `saveTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of kuaidi_data
-- ----------------------------
INSERT INTO `kuaidi_data` VALUES ('36', '[{\"time\":\"2017-08-02 09:06:07\",\"ftime\":\"2017-08-02 09:06:07\",\"context\":\"在官网\\\"运单资料&签收图\\\",可查看签收人信息\"},{\"time\":\"2017-08-02 09:05:24\",\"ftime\":\"2017-08-02 09:05:24\",\"context\":\"已签收,感谢使用顺丰,期待再次为您服务\"},{\"time\":\"2017-08-02 08:28:04\",\"ftime\":\"2017-08-02 08:28:04\",\"context\":\"快件交给高尽辉，正在派送途中（联系电话：13621563932）\"},{\"time\":\"2017-08-02 07:30:53\",\"ftime\":\"2017-08-02 07:30:53\",\"context\":\"正在派送途中,请您准备签收(派件人:高尽辉,电话:13621563932)\"},{\"time\":\"2017-08-02 07:13:32\",\"ftime\":\"2017-08-02 07:13:32\",\"context\":\"快件到达 【苏州市姑苏区学士街营业点】\"},{\"time\":\"2017-08-02 05:28:34\",\"ftime\":\"2017-08-02 05:28:34\",\"context\":\"快件在【苏州吴中集散中心】已装车，准备发往 【苏州市姑苏区学士街营业点】\"},{\"time\":\"2017-08-02 05:28:34\",\"ftime\":\"2017-08-02 05:28:34\",\"context\":\"快件到达 【苏州吴中集散中心】\"},{\"time\":\"2017-08-02 02:27:57\",\"ftime\":\"2017-08-02 02:27:57\",\"context\":\"快件在【苏州昆山集散中心】已装车，准备发往 【苏州吴中集散中心】\"},{\"time\":\"2017-08-02 00:49:35\",\"ftime\":\"2017-08-02 00:49:35\",\"context\":\"快件到达 【苏州昆山集散中心】\"},{\"time\":\"2017-08-01 15:18:46\",\"ftime\":\"2017-08-01 15:18:46\",\"context\":\"快件在【苏州市昆山市中航城营业点】已装车，准备发往下一站\"},{\"time\":\"2017-08-01 12:45:21\",\"ftime\":\"2017-08-01 12:45:21\",\"context\":\"顺丰速运 已收取快件\"}]', '2017-08-14 23:16:30');
INSERT INTO `kuaidi_data` VALUES ('37', '[{\"time\":\"2017-08-12 10:45:41\",\"ftime\":\"2017-08-12 10:45:41\",\"context\":\"在官网\\\"运单资料&签收图\\\",可查看签收人信息\"},{\"time\":\"2017-08-12 10:45:00\",\"ftime\":\"2017-08-12 10:45:00\",\"context\":\"已签收,感谢使用顺丰,期待再次为您服务\"},{\"time\":\"2017-08-12 08:36:02\",\"ftime\":\"2017-08-12 08:36:02\",\"context\":\"正在派送途中,请您准备签收(派件人:米永平,电话:13753918852)\"},{\"time\":\"2017-08-12 08:23:47\",\"ftime\":\"2017-08-12 08:23:47\",\"context\":\"快件交给米永平，正在派送途中（联系电话：13753918852）\"},{\"time\":\"2017-08-12 07:56:02\",\"ftime\":\"2017-08-12 07:56:02\",\"context\":\"快件到达 【运城市河津市龙门营业点】\"},{\"time\":\"2017-08-11 22:59:26\",\"ftime\":\"2017-08-11 22:59:26\",\"context\":\"快件在【运城空港集散中心】已装车，准备发往 【运城市河津市龙门营业点】\"},{\"time\":\"2017-08-11 21:35:14\",\"ftime\":\"2017-08-11 21:35:14\",\"context\":\"快件到达 【运城空港集散中心】\"},{\"time\":\"2017-08-11 13:59:52\",\"ftime\":\"2017-08-11 13:59:52\",\"context\":\"快件在【西安陆运中转场】已装车，准备发往 【运城空港集散中心】\"},{\"time\":\"2017-08-11 13:49:23\",\"ftime\":\"2017-08-11 13:49:23\",\"context\":\"快件到达 【西安陆运中转场】\"},{\"time\":\"2017-08-11 12:51:40\",\"ftime\":\"2017-08-11 12:51:40\",\"context\":\"快件在【西安总集散中心】已装车，准备发往 【西安陆运中转场】\"},{\"time\":\"2017-08-11 12:51:10\",\"ftime\":\"2017-08-11 12:51:10\",\"context\":\"快件到达 【西安总集散中心】\"},{\"time\":\"2017-08-11 02:25:25\",\"ftime\":\"2017-08-11 02:25:25\",\"context\":\"快件在【哈尔滨哈平集散中心】已装车，准备发往 【西安总集散中心】\"},{\"time\":\"2017-08-11 00:55:10\",\"ftime\":\"2017-08-11 00:55:10\",\"context\":\"快件到达 【哈尔滨哈平集散中心】\"},{\"time\":\"2017-08-10 17:35:42\",\"ftime\":\"2017-08-10 17:35:42\",\"context\":\"快件在【佳木斯凯旋集散中心】已装车，准备发往 【哈尔滨哈平集散中心】\"},{\"time\":\"2017-08-10 17:32:51\",\"ftime\":\"2017-08-10 17:32:51\",\"context\":\"快件到达 【佳木斯凯旋集散中心】\"},{\"time\":\"2017-08-10 14:20:46\",\"ftime\":\"2017-08-10 14:20:46\",\"context\":\"快件在【佳木斯市富锦市建三江管局中央大街营业点】已装车，准备发往 【佳木斯凯旋集散中心】\"},{\"time\":\"2017-08-10 09:40:56\",\"ftime\":\"2017-08-10 09:40:56\",\"context\":\"快件到达 【佳木斯市富锦市建三江管局中央大街营业点】\"},{\"time\":\"2017-08-09 19:37:36\",\"ftime\":\"2017-08-09 19:37:36\",\"context\":\"快件在【佳木斯市建三江前锋农场合作点】已装车，准备发往 【佳木斯市富锦市建三江管局中央大街营业点】\"},{\"time\":\"2017-08-09 19:12:20\",\"ftime\":\"2017-08-09 19:12:20\",\"context\":\"顺丰速运 已收取快件\"}]', '2017-08-14 23:22:55');

-- ----------------------------
-- Table structure for `kuaidi_otherapplist`
-- ----------------------------
DROP TABLE IF EXISTS `kuaidi_otherapplist`;
CREATE TABLE `kuaidi_otherapplist` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT '' COMMENT '三方接口名字',
  `en` varchar(50) DEFAULT '' COMMENT '三方接口英文名字',
  `appKey` char(50) DEFAULT '' COMMENT '密钥',
  `notifyUrl` varchar(255) DEFAULT '' COMMENT '回调地址',
  `salt` char(20) DEFAULT '' COMMENT '盐值',
  `type` tinyint(11) DEFAULT '1' COMMENT '1.快递100(暂时只支持快递100)',
  `status` tinyint(4) DEFAULT '1' COMMENT '1.开启, 2关闭',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='三方应用表';

-- ----------------------------
-- Records of kuaidi_otherapplist
-- ----------------------------
INSERT INTO `kuaidi_otherapplist` VALUES ('1', '快递100', 'kuaidi100', '123456789', null, '11', '1', '1');
INSERT INTO `kuaidi_otherapplist` VALUES ('2', '快递100', 'kuaidi100', 'dasdasd', '', '11', '1', '1');

-- ----------------------------
-- Table structure for `kuaidi_otherlist`
-- ----------------------------
DROP TABLE IF EXISTS `kuaidi_otherlist`;
CREATE TABLE `kuaidi_otherlist` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `otherAppId` int(11) DEFAULT '0' COMMENT '三方接口Id',
  `name` varchar(50) DEFAULT '' COMMENT '名字',
  `enName` char(20) DEFAULT '' COMMENT '英文名字',
  `num` varchar(50) DEFAULT '' COMMENT '第三方编号',
  `companyListId` int(11) DEFAULT '0' COMMENT 'kuaidi_companyList的Id',
  `status` tinyint(4) DEFAULT '1' COMMENT '1正常, 2锁定',
  PRIMARY KEY (`id`),
  KEY `listId` (`companyListId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='本地和三方快递关联表';

-- ----------------------------
-- Records of kuaidi_otherlist
-- ----------------------------
INSERT INTO `kuaidi_otherlist` VALUES ('1', '1', '顺风', 'shunfeng', 'shunfeng', '4', '1');

-- ----------------------------
-- Table structure for `kuaidi_waybill`
-- ----------------------------
DROP TABLE IF EXISTS `kuaidi_waybill`;
CREATE TABLE `kuaidi_waybill` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `companyListId` char(50) DEFAULT '' COMMENT '快递公司Id',
  `waybill` char(50) DEFAULT '' COMMENT '运单号',
  `origin` varchar(50) DEFAULT '' COMMENT '出发城市',
  `target` varchar(50) DEFAULT '' COMMENT '目标城市',
  `status` tinyint(4) DEFAULT '1' COMMENT '状态',
  `crearteTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `subscribeCount` tinyint(4) DEFAULT '0' COMMENT '订阅次数',
  `subscribeTime` datetime DEFAULT NULL COMMENT '最后订阅的时间',
  `subscribeResult` varchar(255) DEFAULT '' COMMENT '订阅结果',
  `subscribeStatus` tinyint(4) DEFAULT '4' COMMENT '2, 订阅失败, 1, 订阅成功, 3,订阅中, 4, 待订阅',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8 COMMENT='订阅日志表';

-- ----------------------------
-- Records of kuaidi_waybill
-- ----------------------------
INSERT INTO `kuaidi_waybill` VALUES ('46', '7', '546565765', '默认', '默认', '1', '2017-08-16 15:49:55', '0', '2017-08-16 15:50:01', '{\"result\":false,\"returnCode\":\"500\",\"message\":\"POLL:服务器错误\"}', '2');
INSERT INTO `kuaidi_waybill` VALUES ('47', '7', '543543543', '默认', '默认', '1', '2017-08-16 17:01:36', '0', '2017-08-16 17:01:42', '{\"result\":false,\"returnCode\":\"500\",\"message\":\"POLL:服务器错误\"}', '2');
INSERT INTO `kuaidi_waybill` VALUES ('48', '7', '54356436', '默认', '默认', '1', '2017-08-16 17:20:30', '0', '2017-08-16 17:20:36', '{\"result\":false,\"returnCode\":\"500\",\"message\":\"POLL:服务器错误\"}', '2');
INSERT INTO `kuaidi_waybill` VALUES ('49', '7', '5646546', '默认', '默认', '1', '2017-08-16 17:26:49', '0', '2017-08-16 17:26:55', '{\"result\":false,\"returnCode\":\"500\",\"message\":\"POLL:服务器错误\"}', '2');

-- ----------------------------
-- Table structure for `log_kuaidisubscribe`
-- ----------------------------
DROP TABLE IF EXISTS `log_kuaidisubscribe`;
CREATE TABLE `log_kuaidisubscribe` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `appId` int(11) DEFAULT '0' COMMENT 'app表的id',
  `companyListId` int(11) DEFAULT '0' COMMENT '快递公司id',
  `waybill` char(50) DEFAULT '' COMMENT '运单号',
  `createTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ip` varchar(40) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8 COMMENT='订阅日志表';

-- ----------------------------
-- Records of log_kuaidisubscribe
-- ----------------------------
INSERT INTO `log_kuaidisubscribe` VALUES ('67', '7', '7', '654657676', '2017-08-16 15:17:25', '112.81.119.20');
INSERT INTO `log_kuaidisubscribe` VALUES ('68', '7', '7', '5436543654', '2017-08-16 15:33:47', '112.81.119.20');
INSERT INTO `log_kuaidisubscribe` VALUES ('69', '7', '7', '54365465', '2017-08-16 15:39:32', '112.81.119.20');
INSERT INTO `log_kuaidisubscribe` VALUES ('70', '7', '7', '546565765', '2017-08-16 15:49:51', '112.81.119.20');
INSERT INTO `log_kuaidisubscribe` VALUES ('71', '7', '7', '543543543', '2017-08-16 17:01:32', '112.81.119.20');
INSERT INTO `log_kuaidisubscribe` VALUES ('72', '7', '7', '54356436', '2017-08-16 17:20:26', '112.81.119.20');
INSERT INTO `log_kuaidisubscribe` VALUES ('73', '7', '7', '5646546', '2017-08-16 17:26:44', '112.81.119.20');

-- ----------------------------
-- Table structure for `log_mail`
-- ----------------------------
DROP TABLE IF EXISTS `log_mail`;
CREATE TABLE `log_mail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pkId` varchar(50) DEFAULT '',
  `appId` int(11) DEFAULT '0' COMMENT '应用id',
  `templateId` int(11) DEFAULT '0' COMMENT '模版id',
  `target` varchar(50) DEFAULT '' COMMENT '发送目标',
  `status` tinyint(4) DEFAULT '3' COMMENT '发送状态 1.发送成功, 2.发送失败, 3发送中',
  `createTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `content` text COMMENT '邮件内容',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of log_mail
-- ----------------------------
INSERT INTO `log_mail` VALUES ('33', '42', '7', '3', '844596330@qq.com', '2', '2017-07-17 12:19:50', '{\"code\":\"0453\"}');

-- ----------------------------
-- Table structure for `log_phone`
-- ----------------------------
DROP TABLE IF EXISTS `log_phone`;
CREATE TABLE `log_phone` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pkId` char(50) DEFAULT '' COMMENT '队列编号',
  `appId` int(11) DEFAULT '0' COMMENT '应用id',
  `templateId` int(11) DEFAULT '0' COMMENT '模版id',
  `target` varchar(50) DEFAULT '' COMMENT '发送目标',
  `status` tinyint(4) DEFAULT '3' COMMENT '发送状态 1.发送成功, 2.发送失败, 3发送中',
  `createTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `content` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=438 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of log_phone
-- ----------------------------
INSERT INTO `log_phone` VALUES ('435', '364', '7', '17', '13812623457', '1', '2017-08-25 17:31:06', '{\"code\":\"4920\",\"timeOut\":2}');
INSERT INTO `log_phone` VALUES ('436', '365', '7', '17', '18896785659', '1', '2017-09-01 14:23:36', '{\"code\":\"8154\",\"timeOut\":2}');
INSERT INTO `log_phone` VALUES ('437', '366', '7', '17', '18862324237', '1', '2017-09-05 16:29:18', '{\"code\":\"1094\",\"timeOut\":2}');

-- ----------------------------
-- Table structure for `log_push`
-- ----------------------------
DROP TABLE IF EXISTS `log_push`;
CREATE TABLE `log_push` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `appId` int(11) DEFAULT '0' COMMENT '应用id',
  `templateId` int(11) DEFAULT '0' COMMENT '模版id',
  `content` varchar(255) DEFAULT NULL COMMENT '内容',
  `target` varchar(255) DEFAULT '' COMMENT '目标, uid或者all',
  `ip` varchar(255) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of log_push
-- ----------------------------
INSERT INTO `log_push` VALUES ('51', '7', '12', '{\"alter\":333,\"text2\":444}', '4', 'Unknown', '2');
INSERT INTO `log_push` VALUES ('52', '7', '12', '{\"alter\":1111,\"text2\":2222}', 'all', 'Unknown', '2');
INSERT INTO `log_push` VALUES ('53', '7', '12', '{\"alter\":333,\"text2\":444}', '4', 'Unknown', '2');
INSERT INTO `log_push` VALUES ('54', '7', '12', '{\"alter\":1111,\"text2\":2222}', 'all', 'Unknown', '2');

-- ----------------------------
-- Table structure for `log_wxtemplate`
-- ----------------------------
DROP TABLE IF EXISTS `log_wxtemplate`;
CREATE TABLE `log_wxtemplate` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `openId` varchar(255) DEFAULT NULL,
  `appId` int(11) DEFAULT '0' COMMENT '应用id',
  `templateId` int(11) DEFAULT NULL COMMENT '模版id',
  `content` text COMMENT '内容',
  `ext` varchar(255) DEFAULT '' COMMENT '扩展字段 url,小程序跳转等',
  `ip` varchar(50) DEFAULT NULL,
  `type` tinyint(4) DEFAULT '1' COMMENT '1.个人消息, 2.群发消息',
  `status` tinyint(4) DEFAULT '1' COMMENT '1.成功, 2失败, 3进行中',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=99 DEFAULT CHARSET=utf8 COMMENT='微信模版消息日志';

-- ----------------------------
-- Records of log_wxtemplate
-- ----------------------------
INSERT INTO `log_wxtemplate` VALUES ('98', '44', '7', '14', '{\"first\":{\"value\":\"订单生成通知\",\"color\":\"#173177\"},\"keyword1\":{\"value\":\"2017-08-17 16:30:22\",\"color\":\"#173177\"},\"keyword2\":{\"value\":\"现货澄大阳澄湖六月黄大闸蟹鲜活螃蟹 礼盒装包邮\",\"color\":\"#173177\"},\"keyword3\":{\"value\":\"A817586223455782\",\"color\":\"#173177\"},\"remark\":{\"value\":\"点击去支付\",\"color\":\"#173177\"}}', '{\"url\":\"http://web.com/order/oid-233\",\"appid\":\"\",\"pagepath\":\"\"}', '127.0.0.1', '1', '1');

-- ----------------------------
-- Table structure for `pay_order`
-- ----------------------------
DROP TABLE IF EXISTS `pay_order`;
CREATE TABLE `pay_order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `appId` int(11) DEFAULT '0' COMMENT 'app表id',
  `otherListId` int(11) DEFAULT '0' COMMENT 'otherList表Id',
  `otherSn` varchar(50) DEFAULT '' COMMENT '三方支付订单: 支付宝的支付订单',
  `sn` varchar(50) DEFAULT '' COMMENT '服务生成的订单',
  `aoumnt` int(11) DEFAULT '0' COMMENT '金额(分)',
  `productName` varchar(255) DEFAULT '' COMMENT '商品名字',
  `productSn` varchar(255) DEFAULT '' COMMENT '商品订单多个订单 1D2D3',
  `productDesc` varchar(255) DEFAULT '' COMMENT '商品描述',
  `timeOut` int(11) DEFAULT '30' COMMENT '有效期30分钟',
  `num` int(11) DEFAULT '1' COMMENT '数量',
  `createTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间',
  `ip` varchar(50) DEFAULT '' COMMENT 'ip',
  `status` tinyint(4) DEFAULT '3' COMMENT '1.支付, 2失效, 3待支付, 4交易关闭',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sn` (`sn`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pay_order
-- ----------------------------
INSERT INTO `pay_order` VALUES ('1', '7', '1', '', 'S7S1S1S20170802152553S47', '10000', '农家情迷', '104', '农家情迷', '30', '1', '2017-08-02 15:25:53', '127.0.0.1', '3');
INSERT INTO `pay_order` VALUES ('4', '7', '1', '', '7S1S4S20170613192235S66', '1000', '快送大礼包', '20171208121212', '礼包1: 1, 2, 3', '30', '1', '2017-06-13 19:22:35', 'Unknown', '0');
INSERT INTO `pay_order` VALUES ('5', '7', '1', '', '7S1S5S20170613193017S91', '1000', '快送大礼包', '20171208121212', '礼包1: 1, 2, 3', '30', '1', '2017-06-13 19:30:17', 'Unknown', '3');
INSERT INTO `pay_order` VALUES ('6', '7', '1', '', 'S7S1S6S20170613193126S71', '1000', '快送大礼包', '20171208121212', '礼包1: 1, 2, 3', '30', '1', '2017-06-13 19:31:26', 'Unknown', '3');

-- ----------------------------
-- Table structure for `pay_otherlist`
-- ----------------------------
DROP TABLE IF EXISTS `pay_otherlist`;
CREATE TABLE `pay_otherlist` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT '' COMMENT '名字,例如支付宝',
  `title` varchar(50) DEFAULT NULL COMMENT '默认商品标题如: 传奇天下-游戏充值',
  `partner` varchar(50) DEFAULT NULL COMMENT '商户分配的商户id',
  `partnerName` varchar(50) DEFAULT NULL COMMENT '商户名字',
  `partnerKey` varchar(50) DEFAULT NULL COMMENT '商户分配的key',
  `partnerSecret` varchar(50) DEFAULT NULL COMMENT '商户分配的Secret',
  `partnerAppId` varchar(50) DEFAULT '' COMMENT '商户分配的appId,没有则不填',
  `notifyUrl` varchar(255) DEFAULT NULL,
  `returnUrl` varchar(255) DEFAULT NULL,
  `publicRSA2` text COMMENT '公钥(一般是合作方比如支付宝提供的)',
  `privateRSA2` text COMMENT '私钥(自己生成的)',
  `driveType` tinyint(4) DEFAULT '1' COMMENT '驱动类型1.支付宝sdk, 2支付宝移动端, 3.支付宝网页, 4,微信app, 5,微信jsdk, 6微信网页, 7,汇付宝网银sdk, 8汇付宝网银网页',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pay_otherlist
-- ----------------------------
INSERT INTO `pay_otherlist` VALUES ('1', '支付宝web', '订单', '44', '11', '312321', '123456', '32131', '', null, '321321', '123', '3');
INSERT INTO `pay_otherlist` VALUES ('2', '支付宝wap', '订单', '55', '22', '3213213', '123456', '321321', null, null, '11321', '12536', '2');
INSERT INTO `pay_otherlist` VALUES ('3', '支付宝app', '订单', '66', '33', '312321', '123456', '999', null, null, '123', '456', '1');
INSERT INTO `pay_otherlist` VALUES ('4', '微信扫码', '订单', null, null, null, null, '', null, null, null, null, '6');
INSERT INTO `pay_otherlist` VALUES ('5', '微信app', '订单', '66', '游戏充值', '321321', '321321', '321321', null, null, null, null, '4');
INSERT INTO `pay_otherlist` VALUES ('6', '微信jsdk', '订单', null, null, null, null, '', null, null, null, null, '5');
INSERT INTO `pay_otherlist` VALUES ('7', '汇付宝网银', '订单', '7788', null, '111', null, '', null, null, null, null, '8');
INSERT INTO `pay_otherlist` VALUES ('8', '汇付宝app', '订单', '77', null, '222', null, '', null, null, null, null, '7');

-- ----------------------------
-- Table structure for `pay_refound`
-- ----------------------------
DROP TABLE IF EXISTS `pay_refound`;
CREATE TABLE `pay_refound` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `appId` int(11) DEFAULT '0' COMMENT '应用id',
  `orderId` int(11) DEFAULT '0' COMMENT '支付订单id',
  `otherListId` int(11) DEFAULT '0' COMMENT '三方接口id',
  `refoundSn` varchar(50) DEFAULT '' COMMENT '退款订单',
  `refoundStatus` tinyint(4) DEFAULT '3' COMMENT '1.成功, 2.失败, 3.退款中,4,退款关闭',
  `refoundAmount` int(11) DEFAULT '0' COMMENT '退款金额',
  `refoundTotalAmount` int(11) DEFAULT '0' COMMENT '退款总金额',
  PRIMARY KEY (`id`),
  UNIQUE KEY `refoundSn` (`refoundSn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pay_refound
-- ----------------------------

-- ----------------------------
-- Table structure for `push_list`
-- ----------------------------
DROP TABLE IF EXISTS `push_list`;
CREATE TABLE `push_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT '' COMMENT '推送应用名字',
  `appKey` varchar(255) DEFAULT NULL COMMENT '推送分配的key',
  `appSecret` varchar(255) DEFAULT NULL COMMENT '推送分配的secret',
  `status` tinyint(4) DEFAULT NULL COMMENT '1.正常 2.禁用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of push_list
-- ----------------------------
INSERT INTO `push_list` VALUES ('1', '111', '111', '222', '1');

-- ----------------------------
-- Table structure for `template`
-- ----------------------------
DROP TABLE IF EXISTS `template`;
CREATE TABLE `template` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '模板ID',
  `templateNum` char(50) DEFAULT '0' COMMENT '对应第三方模版编号, 如果没有可不填',
  `title` varchar(50) DEFAULT NULL COMMENT '模板标题',
  `content` text COMMENT '模板内容',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否正常启用：1》正常；2》关闭',
  `type` tinyint(4) DEFAULT '1' COMMENT '1.邮件 2.手机 4.微信 8.推送 5.平台',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COMMENT='邮件模板表';

-- ----------------------------
-- Records of template
-- ----------------------------
INSERT INTO `template` VALUES ('3', '129486', 'kxwan', '<p><span style=\"color: rgb(255, 0, 0); text-align: center; background-color: rgb(0, 176, 240);\">{{code}}</span>{{text}}</p>', '1', '1', '2017-03-31 17:11:44');
INSERT INTO `template` VALUES ('11', '1111', '微信测试', '{{first.DATA}}会员卡号：{{keyword1.DATA}}会员姓名：{{keyword2.DATA}}变更内容：{{keyword3.DATA}}时间：{{keyword4.DATA}}{{remark.DATA}}\r\n', '1', '4', '2017-07-13 09:29:07');
INSERT INTO `template` VALUES ('12', '0', 'app', '{\"alter\":\"dasdasd\"}', '1', '8', '2017-07-17 13:49:55');
INSERT INTO `template` VALUES ('13', '1', '推送测试模板', '{\"alert\":\"hello u8fd9u662fu4e00u6761u63a8u9001u6d4bu8bd5uff0cu8bf7u5ffdu7565\",\"platform\":\"u5f00u5fc3u73a9\",\"title\":\"u63a8u9001u6d4bu8bd5u6a21u677f\",\"sendno\":\"u5f00u5fc3u73a9\",\"timeTolive\":\"600\",\"overrideMsgId\":\"1\",\"apnsProduction\":\"pc\",\"apnsCollapseId\":\"1\",\"bigPushDuration\":\"5\",\"androidCategory\":\"1\",\"androidBuilderId\":\"1\",\"androidPriority\":\"1\",\"androidStyle\":\"1\",\"androidBigText\":\"test\",\"androidInbox\":\"1\",\"androidBigPicPath\":\"1\",\"iosSound\":\"1\",\"iosBadge\":\"1\",\"iosContentAvailable\":\"test\",\"iosMutableContent\":\"1\",\"iosCategory\":\"1\",\"webPhoneOpenPage\":\"1\"}', '1', '8', '2017-07-18 10:57:29');
INSERT INTO `template` VALUES ('14', '微信的模版id', '农家情迷', '{{first.DATA}}\r\n时间：{{keyword1.DATA}}\r\n商品名称：{{keyword2.DATA}}\r\n订单号：{{keyword3.DATA}}\r\n{{remark.DATA}}', '1', '4', '2017-08-09 09:38:52');
INSERT INTO `template` VALUES ('15', '微信的模版id', '农家情迷退款', '\r\n{{first.DATA}}\r\n\r\n退款原因：{{reason.DATA}}\r\n退款金额：{{refund.DATA}}\r\n{{remark.DATA}}', '1', '4', '2017-08-09 09:43:45');
INSERT INTO `template` VALUES ('16', '200292', '农家情迷通知', '<p>【天生我茶】亲爱的天生我茶茶友，您关注的商品{{1}}已降价，快去看看吧！谢谢您的关注~退订回N</p>', '1', '2', '2017-08-15 13:53:39');
INSERT INTO `template` VALUES ('17', '200284', '农家情迷验证码', '<p>【天生我茶】天生我茶通知您,您的验证码是{{code}},请于{{timeOut}}分钟内使用,如果不是您触发该信息,请予以忽略。打死也不告诉别人验证码是多少</p>', '1', '2', '2017-08-15 14:47:15');

-- ----------------------------
-- Table structure for `wx_applist`
-- ----------------------------
DROP TABLE IF EXISTS `wx_applist`;
CREATE TABLE `wx_applist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appId` int(11) DEFAULT NULL,
  `title` varchar(50) DEFAULT '' COMMENT '标题',
  `wxAppId` varchar(50) DEFAULT '' COMMENT '微信应用appid',
  `appSecret` varchar(255) DEFAULT '' COMMENT '微信提供第三方用户唯一凭证密钥，即appsecret',
  `token` varchar(255) DEFAULT '' COMMENT '微信公众号设置的token',
  `encodingAESKey` varchar(255) DEFAULT '' COMMENT 'aes加密key',
  `encodingOldAESKey` varchar(255) DEFAULT '' COMMENT '旧的aes加密key',
  `status` tinyint(3) DEFAULT '1' COMMENT '1.正常, 2.禁用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wx_applist
-- ----------------------------
INSERT INTO `wx_applist` VALUES ('1', '7', '平台测试', '111', '222', '333', '4444', 'E8GsR5ebLp0bj6vrULL6eBzz0y0dobJSlQx4aG5SA7F', '1');
