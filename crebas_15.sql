/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     2014/12/14 0:17:45                           */
/*==============================================================*/


drop table if exists code;

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
   id                   int not null auto_increment comment 'id',
   use_id               int not null comment '用户_id',
   code                 char(32) comment '码',
   start_time           datetime comment '产生的时间',
   type                 char(8) comment '类型',
   primary key (id)
);

alter table code comment '验证码';

/*==============================================================*/
/* Table: file                                                  */
/*==============================================================*/
create table file
(
   id                   int not null auto_increment comment 'id',
   use_id               int not null comment '用户_id',
   pri_id               int not null comment '打印店_id',
   name                 char(32) comment '文件名',
   url                  char(64) comment '文件存放位置',
   time                 datetime comment '文件上传的时间',
   requirements         char(100) comment '打印要求（备注）',
   copies               int comment '打印数量',
   ---copies
   double_side           boolean comment '单双面信息',
---名字和类型
   status               tinyint comment '文件状态',
    --------tinyint
   primary key (id)
);

alter table file comment '文件';

/*==============================================================*/
/* Table: notification                                          */
/*==============================================================*/
create table notification
(
   id                   int not null auto_increment comment 'id',
   fil_id               int not null comment '文件_id',
   content              text comment '内容',
   to_id                int comment '通知对象id',
   type              tinyint comment '通知对象类型',
   ----type ，tinyint
   primary key (id)
);

alter table notification comment '通知消息';

/*==============================================================*/
/* Table: printer                                               */
/*==============================================================*/
create table printer
(
   id                   int not null auto_increment comment 'id',
   name                 char(20) comment '打印店的名字',
   account              char(30) comment '账号',
   password             char(32) comment '密码',
   address              char(30) comment '地址',
   phone                char(20) comment '电话',
   qq                   char(15) comment 'QQ',
   primary key (id)
);

alter table printer comment '打印店';

/*==============================================================*/
/* Table: token                                                 */
/*==============================================================*/
create table token
(
   id                   int not null comment 'id',
   token                char(64) comment 'token',
---长度64
   type                 tinyint comment 'type',
--------tinyint
   to_id                int comment 'token对象id',
---删除   to_type              tinyint comment 'token对象类型',

   primary key (id),
   unique key AK_token_unique (token)
);

alter table token comment 'token';

/*==============================================================*/
/* Table: user                                                  */
/*==============================================================*/
create table user
(
   id                   int not null auto_increment comment 'id',
   student_number       char(10) comment '学号',
   password             char(32) comment '密码',
   name                 char(6) comment '姓名',
   gender               char(3) comment '性别',
   phone                char(20) comment '电话',
   email                char(32) comment 'email',
   primary key (id)
);

alter table user comment '用户';

alter table code add constraint FK_code_of_user foreign key (use_id)
      references user (id) on delete restrict on update restrict;

alter table file add constraint FK_file_of_printer foreign key (pri_id)
      references printer (id) on delete restrict on update restrict;

alter table file add constraint FK_file_of_user foreign key (use_id)
      references user (id) on delete restrict on update restrict;

alter table notification add constraint FK_notification_of_file foreign key (fil_id)
      references file (id) on delete restrict on update restrict;

