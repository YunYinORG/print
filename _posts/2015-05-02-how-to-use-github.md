---
layout: post
title: 如何提交新的文档                    	 
date: 2015-05-02 22:30 						 
description: github使用					 	
category: 使用说明 						 	
tags: github markdown 技术文档					 
---

1.	在https://windows.github.com/ 下载安装github for windows版，注册账户
2.	转至https://github.com/登陆
3.	转至https://github.com/nkumstc/print 点击fork
 ![fork](http://www.yunyin.org/assets/image/2015-05-02/gh_fork.jpg)
4.	点击clone in desktop，选择路径，clone到Windows版
![clone](http://www.yunyin.org/assets/image/2015-05-02/gh_clone.jpg)
 

###纯文本文档写法

1.	选择你要修改的branch（这里以gh-pages/posts为例，大部分文档都要在这里写），点击 + 
 ![add](http://www.yunyin.org/assets/image/2015-05-02/gh_add.jpg)
 
2.	编辑标题和内容（使用markdown语言）
 ![edit](http://www.yunyin.org/assets/image/2015-05-02/gh_edit.jpg)
 
3.	点击propose new file
 ![propose](http://www.yunyin.org/assets/image/2015-05-02/gh_propose.jpg)
 

###以上就是你要做的了，下面就等future合并生效了。

###有图片的文档写法（以gh-pages/posts为例）

1.	找到clone的路径，打开_posts，把要上传的文件拷贝到此文件夹下
 ![](http://www.yunyin.org/assets/image/2015-05-02/gh_posts.jpg)

 

2.	把要上传的图片文件拷贝到…\GitHub\print\assets\image（新建一个文件夹，把图片放在一起）
 ![image](http://www.yunyin.org/assets/image/2015-05-02/gh_image.jpg)
 
 
3.	同步，点击sync
 ![sync](http://www.yunyin.org/assets/image/2015-05-02/gh_sync.jpg)

4.	点击view on github
 ![view](http://www.yunyin.org/assets/image/2015-05-02/gh_view on gh.jpg)

5.	new pull request
 ![p r q](http://www.yunyin.org/assets/image/2015-05-02/gh_new pull request.jpg)
	选择分支
 ![prq1](http://www.yunyin.org/assets/image/2015-05-02/gh_npr1.jpg)

 
##文档写作格式
1.markdown语法
 ![md](http://www.yunyin.org/assets/image/2015-05-02/markdown.jpg)
 
2.文章标题
日期+标题.md（如：2015-03-20-how-to-use.md）
3.文章开头
每篇文章的最前面添加，在冒号后填空，括号内容为注释

>layout: post
>
>title:（标题）
>
>date: （发布日期）
>
>description: （描述摘要）	
>
>category: （分类）	
>
>tags:（标签）	
>
