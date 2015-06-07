/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     2015/6/6 19:00:00                           */
/*==============================================================*/


drop table if exists card;

drop table if exists cardlog;

drop table if exists code;

drop table if exists device;

drop table if exists feedback;

drop table if exists file;

drop table if exists notification;

drop table if exists printer;

drop table if exists school;

drop table if exists token;

drop table if exists user;

drop table if exists share;

drop table if exists hastag;

drop table if exists tag;

/*==============================================================*/
/* Table: card                                                  */
/*==============================================================*/
create table card
(
   id                   bigint not null,
   off                  tinyint default 0,
   blocked              bool default 0,
   primary key (id)
);

/*==============================================================*/
/* Table: cardlog                                               */
/*==============================================================*/
create table cardlog
(
   id                   bigint not null auto_increment,
   find_id              bigint not null,
   lost_id              bigint not null,
   time                 timestamp default CURRENT_TIMESTAMP,
   status               tinyint,
   primary key (id)
);

/*==============================================================*/
/* Table: code                                                  */
/*==============================================================*/
create table code
(
   id                   bigint not null auto_increment,
   use_id               bigint not null,
   code                 char(32),
   time                 timestamp not null default CURRENT_TIMESTAMP,
   type                 tinyint,
   content              varchar(64),
   primary key (id)
);

/*==============================================================*/
/* Table: device                                                */
/*==============================================================*/
create table device
(
   id                   bigint not null,
   code                 varchar(16),
   last_login           timestamp default CURRENT_TIMESTAMP,
   status               tinyint,
   type                 tinyint,
   primary key (id)
);

/*==============================================================*/
/* Table: feedback                                              */
/*==============================================================*/
create table feedback
(
   id                   bigint not null auto_increment,
   email                char(32),
   phone                char(16),
   message              text,
   time                 timestamp not null default CURRENT_TIMESTAMP,
   primary key (id)
);

/*==============================================================*/
/* Table: file                                                  */
/*==============================================================*/
create table file
(
   id                   bigint not null auto_increment,
   pri_id               bigint not null,
   use_id               bigint not null,
   name                 char(64),
   url                  char(64),
   time                 timestamp not null default CURRENT_TIMESTAMP,
   requirements         varchar(128),
   copies               int default 1,
   double_side          bool,
   status               tinyint,
   color                bool,
   ppt_layout           tinyint default 0,
   sended               tinyint default 0,
   primary key (id)
);

/*==============================================================*/
/* Table: notification                                          */
/*==============================================================*/
create table notification
(
   id                   bigint not null auto_increment,
   fil_id               bigint not null,
   content              text,
   to_id                bigint,
   type                 tinyint,
   primary key (id)
);

/*==============================================================*/
/* Table: printer                                               */
/*==============================================================*/
create table printer
(
   id                   bigint not null auto_increment,
   sch_id               bigint not null,
   name                 char(16) not null,
   account              char(16) not null,
   password             char(32) not null,
   address              char(32),
   email                char(64),
   phone                char(16),
   qq                   char(16),
   profile              text,
   image_url            char(64),
   open_time            char(32),
   status               tinyint default 1,
   rank                 int default 0,
   price                varchar(256),
   price_more           text,
   primary key (id),
   unique key AK_account_unique (account)
);

/*==============================================================*/
/* Table: school                                                */
/*==============================================================*/
create table school
(
   id                   bigint not null auto_increment,
   name                 varchar(32),
   address              varchar(128),
   primary key (id)
);


/*==============================================================*/
/* Table: token                                                 */
/*==============================================================*/
create table token
(
   to_id                bigint not null,
   type                 tinyint not null,
   time                 timestamp not null default CURRENT_TIMESTAMP,
   token                char(64),
   primary key (to_id, type),
   unique key AK_token_unique (token)
);

/*==============================================================*/
/* Table: user                                                  */
/*==============================================================*/
create table user
(
   id                   bigint not null auto_increment,
   sch_id               bigint not null,
   student_number       char(10),
   password             char(32),
   name                 char(8),
   gender               char(2),
   phone                char(16),
   email                char(64),
   status               tinyint default 1,
   last_login           timestamp default CURRENT_TIMESTAMP,
   primary key (id),
   unique key AK_student_number_unique (student_number)
);

/*==============================================================*/
/* Table: share                                                  */
/*==============================================================*/
create table share
(
	id 					bigint not null auto_increment,
	fil_id				bigint not null,
	time 				timestamp not null default CURRENT_TIMESTAMP,
	name 				char(32) not null,
	anomonity 			bool,
	primary key(id)
);

/*==============================================================*/
/* Table: tag                                                  */
/*==============================================================*/
create table tag
(
	id 					bigint not null auto_increment,
	use_id 			bigint not null,
	name 			char(16) not null,
	count 				bigint,
	time 				timestamp not null default CURRENT_TIMESTAMP,
	primary key(id)
);

/*==============================================================*/
/* Table: hastag                                            */
/*==============================================================*/
create table hastag
(
	share_id 			bigint not null,
	tag_id 				bigint not null,
	time 				timestamp not null default CURRENT_TIMESTAMP
);

alter table card add constraint FK_card_info_of_user foreign key (id)
      references user (id) on delete restrict on update restrict;

alter table cardlog add constraint FK_user_find_card foreign key (find_id)
      references user (id) on delete restrict on update restrict;

alter table cardlog add constraint FK_user_lost_card foreign key (lost_id)
      references user (id) on delete restrict on update restrict;

alter table code add constraint FK_code_of_user foreign key (use_id)
      references user (id) on delete restrict on update restrict;

alter table device add constraint FK_mobile_device_of_user foreign key (id)
      references user (id) on delete restrict on update restrict;

alter table file add constraint FK_file_of_printer foreign key (pri_id)
      references printer (id) on delete restrict on update restrict;

alter table file add constraint FK_file_of_user foreign key (use_id)
      references user (id) on delete restrict on update restrict;

alter table notification add constraint FK_notification_of_file foreign key (fil_id)
      references file (id) on delete restrict on update restrict;

alter table printer add constraint FK_printer_blong_to_school foreign key (sch_id)
      references school (id) on delete restrict on update restrict;

alter table user add constraint FK_user_blong_to_school foreign key (sch_id)
      references school (id) on delete restrict on update restrict;

alter table share add constraint FK_share_of_file foreign key (fil_id) 
	  references file (id) on delete restrict on update restrict;

alter table tag add constraint FK_tag_of_user foreign key (use_id) 
      references user (id) on delete restrict on update restrict;	 

alter table hastag add constraint FK_connection_of_share foreign key (share_id) 
	  references share (id) on delete restrict on update restrict;

alter table hastag add constraint FK_connetction_of_tag foreign key (tag_id) 
      references tag (id) on delete restrict on update restrict;	  

INSERT INTO `school` (`id`, `name`, `address`) VALUES
(1, '南开大学', '天津市 南开区 卫津路94号'),
(2, '天津大学', '天津市 南开区 卫津路92号'),
(0, '无学校', '');

INSERT INTO `user`(`id`,`sch_id`) VALUES ('0','0' );
INSERT INTO `card`(`id`) VALUES ('0');
