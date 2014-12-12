/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     2014/12/11 13:32:03                          */
/*==============================================================*/


drop table if exists file;

drop table if exists printer;

drop table if exists user;

/*==============================================================*/
/* Table: file                                                  */
/*==============================================================*/
create table file
(
   id                   int not null,
   use_id               int not null,
   pri_id               int not null,
   name                 char(32),
   url             char(64),
   time                 datetime,
   ---printed              bool default 0,
   requirements         char(100),
   amount         int,
   ---
   is_double           char(10),
   status          char(10),
   primary key (id)
);

/*==============================================================*/
/* Table: printer                                               */
/*==============================================================*/
create table printer
(
   id                   int not null,
   name                 char(20),
   account              char(30),
   password             char(32),
   address              char(30),
  ---
   phone          char(20),
   QQ

   primary key (id)
);

/*==============================================================*/
/* Table: user                                                  */
/*==============================================================*/
create table user
(
   id                   int not null,
   student_number       char(10),
   password             char(32),
   name                 char(6),
---
   email                char(20),
   gender                  char(3),
   phone             char(20),
   primary key (id)
);

alter table file add constraint FK_file_of_printer foreign key (pri_id)
      references printer (id) on delete restrict on update restrict;

alter table file add constraint FK_file_of_user foreign key (use_id)
      references user (id) on delete restrict on update restrict;

