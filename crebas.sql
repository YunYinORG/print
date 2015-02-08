/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     2015/2/8 17:02:24                            */
/*==============================================================*/


drop table if exists cardlog;

drop table if exists code;

drop table if exists feedback;

drop table if exists file;

drop table if exists notification;

drop table if exists printer;

drop table if exists token;

drop table if exists user;

/*==============================================================*/
/* Table: cardlog                                               */
/*==============================================================*/
create table cardlog
(
   id                   bigint not null auto_increment,
   find_id              bigint not null,
   lost_id              bigint not null,
   time                 datetime default CURRENT_TIMESTAMP,
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
   name                 char(16),
   account              char(16),
   password             char(32),
   address              char(32),
   phone                char(16),
   qq                   char(16),
   primary key (id),
   unique key AK_account_unique (account)
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
   student_number       char(10),
   password             char(32),
   name                 char(8),
   gender               char(2),
   phone                char(16),
   email                char(64),
   status               tinyint default 1,
   device_code          varchar(16),
   last_login           datetime default CURRENT_TIMESTAMP,
   card_on              bool default 1,
   primary key (id),
   unique key AK_student_number_unique (student_number)
);

alter table cardlog add constraint FK_user_find_card foreign key (find_id)
      references user (id) on delete restrict on update restrict;

alter table cardlog add constraint FK_user_lost_card foreign key (lost_id)
      references user (id) on delete restrict on update restrict;

alter table code add constraint FK_code_of_user foreign key (use_id)
      references user (id) on delete restrict on update restrict;

alter table file add constraint FK_file_of_printer foreign key (pri_id)
      references printer (id) on delete restrict on update restrict;

alter table file add constraint FK_file_of_user foreign key (use_id)
      references user (id) on delete restrict on update restrict;

alter table notification add constraint FK_notification_of_file foreign key (fil_id)
      references file (id) on delete restrict on update restrict;

