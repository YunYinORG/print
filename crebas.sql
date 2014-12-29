/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     2014/12/29 16:36:34                          */
/*==============================================================*/


drop table if exists code;

drop table if exists feedback;

drop table if exists file;

drop table if exists notification;

drop table if exists printer;

drop table if exists token;

drop table if exists user;

/*==============================================================*/
/* Table: code                                                  */
/*==============================================================*/
create table code
(
   id                   int not null auto_increment,
   use_id               int not null,
   code                 char(32),
   time                 timestamp not null default CURRENT_TIMESTAMP,
   type                 char(8),
   primary key (id)
);

/*==============================================================*/
/* Table: feedback                                              */
/*==============================================================*/
create table feedback
(
   id                   int not null auto_increment,
   email                char(32),
   phone                char(20),
   message              text,
   time                 timestamp not null default CURRENT_TIMESTAMP,
   primary key (id)
);

/*==============================================================*/
/* Table: file                                                  */
/*==============================================================*/
create table file
(
   id                   int not null auto_increment,
   pri_id               int not null,
   use_id               int not null,
   name                 char(32),
   url                  char(64),
   time                 timestamp not null default CURRENT_TIMESTAMP,
   requirements         char(100),
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
   id                   int not null auto_increment,
   fil_id               int not null,
   content              text,
   to_id                int,
   type                 tinyint,
   primary key (id)
);

/*==============================================================*/
/* Table: printer                                               */
/*==============================================================*/
create table printer
(
   id                   int not null auto_increment,
   name                 char(20),
   account              char(30),
   password             char(32),
   address              char(30),
   phone                char(20),
   qq                   char(15),
   primary key (id),
   unique key AK_account_unique (account)
);

/*==============================================================*/
/* Table: token                                                 */
/*==============================================================*/
create table token
(
   to_id                int not null,
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
   id                   int not null auto_increment,
   student_number       char(10),
   password             char(32),
   name                 char(6),
   gender               char(3),
   phone                char(20),
   email                char(32),
   primary key (id),
   unique key AK_student_number_unique (student_number)
);

alter table code add constraint FK_code_of_user foreign key (use_id)
      references user (id) on delete restrict on update restrict;

alter table file add constraint FK_file_of_printer foreign key (pri_id)
      references printer (id) on delete restrict on update restrict;

alter table file add constraint FK_file_of_user foreign key (use_id)
      references user (id) on delete restrict on update restrict;

alter table notification add constraint FK_notification_of_file foreign key (fil_id)
      references file (id) on delete restrict on update restrict;

