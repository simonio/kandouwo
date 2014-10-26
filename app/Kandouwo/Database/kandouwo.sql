SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema kandouwo
-- -----------------------------------------------------
-- CREATE SCHEMA IF NOT EXISTS `kandouwo` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
-- USE `kandouwo` ;

-- -----------------------------------------------------
-- Table `kandouwo`.`users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kandouwo`.`users` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `uuid` VARCHAR(60) NULL,
  `nickname` VARCHAR(60) NULL COMMENT '昵称(不唯一，可以任意设置)',
  `password` VARCHAR(60) NULL COMMENT '密码',
  `authorize` INT NULL DEFAULT 100 COMMENT '权限',
  `email` VARCHAR(45) NULL COMMENT '邮箱',
  `sex` VARCHAR(2) NULL DEFAULT '男' COMMENT '性别',
  `signature` VARCHAR(255) NULL COMMENT '签名',
  `kdou` VARCHAR(45) NULL COMMENT 'k豆',
  `thumbnail` BLOB NULL COMMENT '头像',
  `thumbnail_big` MEDIUMBLOB NULL COMMENT '大头像',
  `attend_date` DATE NULL COMMENT '签到',
  `lastlogin_place` VARCHAR(45) NULL COMMENT '最后一次登录地点',
  `readed_book_num` INT NULL COMMENT '阅读的书本数量',
  `downloaded_book_num` INT NULL COMMENT '下载的书本数量',
  `comment_num` INT NULL COMMENT '评论数',
  `deleted` TINYINT NULL COMMENT '是否删除',
  `status` TINYINT NULL DEFAULT 0 COMMENT '状态信息',
  `updated_at` DATETIME NULL,
  `created_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC))
ENGINE = InnoDB
COMMENT = '用户的基本信息';


-- -----------------------------------------------------
-- Table `kandouwo`.`friends`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kandouwo`.`friends` (
  `id` BIGINT NOT NULL COMMENT '用户id',
  `friend_id` BIGINT NOT NULL COMMENT '好友id',
  PRIMARY KEY (`id`, `friend_id`),
  CONSTRAINT `friend_id`
    FOREIGN KEY (`id`)
    REFERENCES `kandouwo`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `friend_friend_id`
    FOREIGN KEY (`id`)
    REFERENCES `kandouwo`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = '好友';


-- -----------------------------------------------------
-- Table `kandouwo`.`book_type`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kandouwo`.`book_type` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NULL COMMENT '类型名称',
  PRIMARY KEY (`id`))
ENGINE = InnoDB
COMMENT = '书的类型';


-- -----------------------------------------------------
-- Table `kandouwo`.`book`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kandouwo`.`book` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `douban_id` BIGINT NULL COMMENT '豆瓣上的id',
  `isbn10` VARCHAR(10) NULL COMMENT 'isbn:10位',
  `isbn13` VARCHAR(13) NULL COMMENT 'isbn:13位',
  `type` INT NULL COMMENT '书的类型',
  `title` VARCHAR(45) NULL COMMENT '书名',
  `origin_title` VARCHAR(45) NULL COMMENT '原始书名',
  `alt_title` VARCHAR(45) NULL COMMENT '别名',
  `sub_title` VARCHAR(45) NULL COMMENT '子书名',
  `alt` VARCHAR(2083) NULL COMMENT '未知，貌似是书在豆瓣上的网址',
  `author` VARCHAR(255) NULL COMMENT '作者（多个作者之间用“,”分割）',
  `translator` VARCHAR(255) NULL COMMENT '译者（多个译者之间用“,”分割）',
  `publisher` VARCHAR(45) NULL COMMENT '出版社',
  `pubdate` DATE NULL COMMENT '出版日',
  `binding` TINYINT NULL COMMENT '装帧',
  `tags` VARCHAR(550) NULL COMMENT '标签(限定每个标签的长度为10个字符，最多有50个标签，标签用“,”分割)',
  `rate` INT NULL COMMENT '评分',
  `rate_num` INT NULL COMMENT '评分人数',
  `price` FLOAT NULL COMMENT '价格',
  `pages` INT NULL COMMENT '页数',
  `words` INT NULL COMMENT '字数',
  `click_num` INT NULL COMMENT '点击数',
  `download_num` INT NULL COMMENT '下载数',
  `downloadable` TINYINT(1) NULL COMMENT '是否可下载',
  `listenable` TINYINT(1) NULL COMMENT '是否可听书',
  `image_normal` VARCHAR(2083) NULL COMMENT '封面图: 正常',
  `image_small` VARCHAR(2083) NULL COMMENT '封面图: 小',
  `image_big` VARCHAR(2083) NULL COMMENT '封面图: 大',
  `author_inro` VARCHAR(2048) NULL COMMENT '作者简介',
  `summary` VARCHAR(2048) NULL COMMENT '书简介',
  `catalog` VARCHAR(2048) NULL COMMENT '目录',
  PRIMARY KEY (`id`),
  INDEX `type_idx` (`type` ASC),
  CONSTRAINT `book_type`
    FOREIGN KEY (`type`)
    REFERENCES `kandouwo`.`book_type` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = '书的基本信息';


-- -----------------------------------------------------
-- Table `kandouwo`.`collection`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kandouwo`.`collection` (
  `user_id` BIGINT NOT NULL COMMENT '用户id',
  `book_id` BIGINT NULL COMMENT '书本id',
  PRIMARY KEY (`user_id`),
  INDEX `book_id_idx` (`book_id` ASC),
  CONSTRAINT `collections_id`
    FOREIGN KEY (`user_id`)
    REFERENCES `kandouwo`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `collections_book_id`
    FOREIGN KEY (`book_id`)
    REFERENCES `kandouwo`.`book` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = '收藏';


-- -----------------------------------------------------
-- Table `kandouwo`.`game`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kandouwo`.`game` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `parent_id` BIGINT NULL COMMENT '父类游戏id',
  `name` VARCHAR(45) NULL COMMENT '游戏名称',
  `desc` VARCHAR(255) NULL COMMENT '游戏的描述',
  `interest_id` BIGINT NULL COMMENT '喜爱的游戏（模式），该字段仅在parent_id为空时有效',
  PRIMARY KEY (`id`))
ENGINE = InnoDB
COMMENT = '游戏';


-- -----------------------------------------------------
-- Table `kandouwo`.`game_guess_book`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kandouwo`.`game_guess_book` (
  `game_id` BIGINT NOT NULL COMMENT '游戏id',
  `life_time` INT NULL COMMENT '猜书的最长时间',
  PRIMARY KEY (`game_id`),
  CONSTRAINT `game_book_game_id`
    FOREIGN KEY (`game_id`)
    REFERENCES `kandouwo`.`game` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kandouwo`.`game_guess_book_detail`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kandouwo`.`game_guess_book_detail` (
  `id` BIGINT NOT NULL AUTO_INCREMENT COMMENT '书的id（可能需要去掉自增标记）',
  `game_id` BIGINT NULL COMMENT '游戏id',
  `image` MEDIUMBLOB NULL COMMENT '图像',
  `book_name` VARCHAR(45) NULL COMMENT '书名',
  `book_type` INT NULL,
  `guess_right` INT NULL COMMENT '概述猜对次数',
  `guess_count` INT NULL COMMENT '该书被猜次数',
  PRIMARY KEY (`id`),
  INDEX `guess_bool_detail_idx` (`game_id` ASC),
  CONSTRAINT `guess_bool_detail`
    FOREIGN KEY (`game_id`)
    REFERENCES `kandouwo`.`game_guess_book` (`game_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kandouwo`.`game_guess_book_data`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kandouwo`.`game_guess_book_data` (
  `id` BIGINT NOT NULL COMMENT '用户id',
  `guess_book_id` BIGINT NULL COMMENT '所猜书的id',
  `result` TINYINT(1) NULL COMMENT '猜书的结果(对,错)',
  `start_time` DATETIME NULL COMMENT '开始游戏的时间',
  `end_time` DATETIME NULL COMMENT '结束游戏的时间',
  PRIMARY KEY (`id`),
  INDEX `guess_book_id_idx` (`guess_book_id` ASC),
  CONSTRAINT `guess_user_id`
    FOREIGN KEY (`id`)
    REFERENCES `kandouwo`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `guess_book_id`
    FOREIGN KEY (`guess_book_id`)
    REFERENCES `kandouwo`.`game_guess_book_detail` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = '猜书记录';


-- -----------------------------------------------------
-- Table `kandouwo`.`kindle_service`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kandouwo`.`kindle_service` (
  `type` BIGINT NOT NULL AUTO_INCREMENT COMMENT '加油站分类',
  `name` VARCHAR(45) NULL,
  `parent_id` BIGINT NULL,
  PRIMARY KEY (`type`))
ENGINE = InnoDB
COMMENT = 'kindle加油站';


-- -----------------------------------------------------
-- Table `kandouwo`.`review`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kandouwo`.`review` (
  `user_id` BIGINT NOT NULL COMMENT '用户id',
  `book_id` BIGINT NOT NULL COMMENT '书id',
  `content` VARCHAR(1024) NULL COMMENT '评论内容',
  `rate` INT NULL COMMENT '评论级别(精品、普通等)，可以是评论被称赞的数目',
  `rate_num` INT NULL,
  `published` DATETIME NULL COMMENT '创建日期时间',
  `updated` DATETIME NULL COMMENT '最后创建日期时间',
  `summary` VARCHAR(140) NULL,
  `votes` INT NULL COMMENT '赞成数目',
  `useless` INT NULL,
  PRIMARY KEY (`user_id`, `book_id`),
  INDEX `book_id_idx` (`book_id` ASC),
  CONSTRAINT `review_id`
    FOREIGN KEY (`user_id`)
    REFERENCES `kandouwo`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `review_book_id`
    FOREIGN KEY (`book_id`)
    REFERENCES `kandouwo`.`book` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = '书评';


-- -----------------------------------------------------
-- Table `kandouwo`.`book_download_url`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kandouwo`.`book_download_url` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `book_id` BIGINT NULL COMMENT '书id',
  `user_id` BIGINT NULL COMMENT '当链接是用户提供时，该字段保存用户的id',
  `url_type` TINYINT NULL COMMENT '下载链接类型(许多书,用户提供,...)',
  `name` VARCHAR(45) NULL COMMENT '下载地址名称',
  `url` VARCHAR(2083) NULL COMMENT '下载地址',
  `source` VARCHAR(45) NULL COMMENT '来源',
  `price` FLOAT NULL COMMENT '价格',
  `price_type` TINYINT NULL COMMENT '货币类型(k豆,...)',
  PRIMARY KEY (`id`),
  INDEX `book_id_idx` (`book_id` ASC),
  CONSTRAINT `book_download_url_book_id`
    FOREIGN KEY (`book_id`)
    REFERENCES `kandouwo`.`book` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = '书的下载链接';


-- -----------------------------------------------------
-- Table `kandouwo`.`listen_book_url`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kandouwo`.`listen_book_url` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `book_id` BIGINT NULL COMMENT '书id',
  `user_id` BIGINT NULL,
  `url_type` INT NULL COMMENT '链接类型',
  `name` VARCHAR(45) NULL,
  `url` VARCHAR(45) NULL COMMENT '听书链接',
  `source` VARCHAR(45) NULL COMMENT '来源',
  PRIMARY KEY (`id`),
  INDEX `book_id_idx` (`book_id` ASC),
  CONSTRAINT `listen_book_url_book_id`
    FOREIGN KEY (`book_id`)
    REFERENCES `kandouwo`.`book` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = '听书链接';


-- -----------------------------------------------------
-- Table `kandouwo`.`book_relative_book`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kandouwo`.`book_relative_book` (
  `book_id` BIGINT NULL COMMENT '书id',
  `relative_book_id` BIGINT NULL COMMENT '关联书id',
  `relative_book_name` VARCHAR(45) NULL COMMENT '关联书名(关联书id不存在时,取该值)',
  INDEX `book_id_idx` (`book_id` ASC),
  CONSTRAINT `relative_book_id`
    FOREIGN KEY (`book_id`)
    REFERENCES `kandouwo`.`book` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = '相关书籍';


-- -----------------------------------------------------
-- Table `kandouwo`.`book_relative_film`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kandouwo`.`book_relative_film` (
  `book_id` BIGINT NOT NULL AUTO_INCREMENT COMMENT '书id',
  `film_name` VARCHAR(45) NULL COMMENT '中文电影名',
  `film_name_en` VARCHAR(45) NULL COMMENT '英文电影名',
  `actor` VARCHAR(255) NULL COMMENT '演员列表',
  `published` DATE NULL COMMENT '出品日',
  PRIMARY KEY (`book_id`),
  CONSTRAINT `relative_film_id`
    FOREIGN KEY (`book_id`)
    REFERENCES `kandouwo`.`book` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = '相关电影';


-- -----------------------------------------------------
-- Table `kandouwo`.`hot_book`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kandouwo`.`hot_book` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `book_id` BIGINT NULL COMMENT '书id',
  PRIMARY KEY (`id`),
  INDEX `hot_book_id_idx` (`book_id` ASC),
  CONSTRAINT `hot_book_id`
    FOREIGN KEY (`book_id`)
    REFERENCES `kandouwo`.`book` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = '热门书籍';


-- -----------------------------------------------------
-- Table `kandouwo`.`articles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kandouwo`.`articles` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `type` BIGINT NULL COMMENT '文章类型',
  `title` VARCHAR(45) NULL COMMENT '文章标题',
  `author` VARCHAR(255) NULL COMMENT '文章作者',
  `content` TEXT NULL COMMENT '文章内容(图片文件存在空间，链接放在文章里，和文章内容一起存在数据库)',
  `published` DATETIME NULL COMMENT '创建日期时间',
  `updated` DATETIME NULL COMMENT '最后编辑日期时间',
  `read_num` INT NULL COMMENT '阅读数目',
  `source` INT NULL COMMENT '来源',
  `url` VARCHAR(2083) NULL COMMENT '转载的url',
  `prise_num` INT NULL COMMENT '被称赞的数目',
  PRIMARY KEY (`id`),
  INDEX `articles_type_idx` (`type` ASC),
  CONSTRAINT `articles_type`
    FOREIGN KEY (`type`)
    REFERENCES `kandouwo`.`kindle_service` (`type`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = '文章';


-- -----------------------------------------------------
-- Table `kandouwo`.`book_type_tree`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kandouwo`.`book_type_tree` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `book_type_id` INT NULL COMMENT '书类型id',
  `book_type_parent_id` INT NULL COMMENT '父类书类型id',
  PRIMARY KEY (`id`),
  INDEX `book_type_id_idx` (`book_type_id` ASC),
  INDEX `book_type_parent_id_idx` (`book_type_parent_id` ASC),
  CONSTRAINT `book_type_id`
    FOREIGN KEY (`book_type_id`)
    REFERENCES `kandouwo`.`book_type` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `book_type_parent_id`
    FOREIGN KEY (`book_type_parent_id`)
    REFERENCES `kandouwo`.`book_type` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = '书的类型树';


-- -----------------------------------------------------
-- Table `kandouwo`.`download_record`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kandouwo`.`download_record` (
  `user_id` BIGINT NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `book_id` BIGINT NULL COMMENT '书id',
  PRIMARY KEY (`user_id`),
  INDEX `book_id_idx` (`book_id` ASC),
  CONSTRAINT `download_record_id`
    FOREIGN KEY (`user_id`)
    REFERENCES `kandouwo`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `download_record_book_id`
    FOREIGN KEY (`book_id`)
    REFERENCES `kandouwo`.`book` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = '下载记录';


-- -----------------------------------------------------
-- Table `kandouwo`.`book_rate_range`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kandouwo`.`book_rate_range` (
  `min` INT NOT NULL COMMENT '最低分',
  `max` INT NULL COMMENT '最高分',
  PRIMARY KEY (`min`))
ENGINE = InnoDB
COMMENT = '评分范围';


-- -----------------------------------------------------
-- Table `kandouwo`.`review_rate_range`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kandouwo`.`review_rate_range` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `min` INT NULL COMMENT '最低分',
  `max` INT NULL COMMENT '最高分',
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
