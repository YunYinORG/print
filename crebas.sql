/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     2014/12/10 2:11:07                           */
/*==============================================================*/


drop table if exists File;

drop table if exists Printer;

drop table if exists User;

/*==============================================================*/
/* Table: File                                                  */
/*==============================================================*/
create table File
(
   id                   int not null auto_increment,
   Use_id               int not null,
   Pri_id               int not null,
   name                 char(32),
   location             char(64),
   time                 datetime,
   printed              bool default 0,
   requirements         char(100),
   primary key (id)
);

/*==============================================================*/
/* Table: Printer                                               */
/*==============================================================*/
create table Printer
(
   id                   int not null auto_increment,
   name                 char(20),
   account              char(30),
   password             char(32),
   address              char(30),
   primary key (id)
);

/*==============================================================*/
/* Table: User                                                  */
/*==============================================================*/
create table User
(
   id                   int not null auto_increment,
   student_number       char(10),
   password             char(32),
   name                 char(6),
   major                char(20),
   sex                  char(3),
   primary key (id)
);

alter table File add constraint FK_File_of_Printer foreign key (Pri_id)
      references Printer (id) on delete restrict on update restrict;

alter table File add constraint FK_File_of_User foreign key (Use_id)
      references User (id) on delete restrict on update restrict;

