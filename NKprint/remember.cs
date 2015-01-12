using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Management;
using System.IO;
using Newtonsoft.Json;
namespace NKprint
{
    class remember
    {
        //将List转换为TXT文件
        public static void WriteListToTextFile(List<string> list, string txtFile)
        {
            /*if (!File.Exists(txtFile))
            {
                File.Create(txtFile);
            }*/
            //创建一个文件流，用以写入或者创建一个StreamWriter 
            FileStream fs = new FileStream(txtFile, FileMode.OpenOrCreate, FileAccess.Write, FileShare.ReadWrite);
            StreamWriter sw = new StreamWriter(fs);
            sw.Flush();
            // 使用StreamWriter来往文件中写入内容 
            sw.BaseStream.Seek(0, SeekOrigin.Begin);
            for (int i = 0; i < list.Count; i++) sw.WriteLine(list[i]);
            //关闭此文件t 
            sw.Flush();
            sw.Close();
            fs.Close();
        }
        public static void WriteStringToTextFile(string list, string txtFile)
        {
            /*if (!File.Exists(txtFile))
            {
                File.Create(txtFile);
            }*/
            //创建一个文件流，用以写入或者创建一个StreamWriter 
            FileStream fs = new FileStream(txtFile, FileMode.OpenOrCreate, FileAccess.Write, FileShare.ReadWrite);
            StreamWriter sw = new StreamWriter(fs);
            sw.Flush();
            // 使用StreamWriter来往文件中写入内容 
            sw.BaseStream.Seek(0, SeekOrigin.Begin);
            sw.WriteLine(list);
            //关闭此文件t 
            sw.Flush();
            sw.Close();
            fs.Close();
        }
        public static void WriteJsonToTextFile(List<ToJsonMy> list, string txtFile)
        {
            /*if (!File.Exists(txtFile))
            {
                File.Create(txtFile);
            }*/
            //创建一个文件流，用以写入或者创建一个StreamWriter 
            FileStream fs = new FileStream(txtFile, FileMode.OpenOrCreate, FileAccess.Write, FileShare.ReadWrite);
            StreamWriter sw = new StreamWriter(fs);
            sw.Flush();
            //string output = JsonConvert.SerializeObject(product);//类转换成Json字符串
            // 使用StreamWriter来往文件中写入内容 
            sw.BaseStream.Seek(0, SeekOrigin.Begin);
            for (int i = 0; i < list.Count; i++)
            {
                string output = JsonConvert.SerializeObject(list[i]);//类转换成Json字符串
                //sw.Write(list[i].id); sw.Write(list[i].name);
                sw.WriteLine(output);
            }
            //关闭此文件t 
            sw.Flush();
            sw.Close();
            fs.Close();
        }
        //读取文本文件转换为List 
        public static List<string> ReadTextFileToList(string fileName)
        {
            if (!File.Exists(fileName))
            {
                File.Create(fileName);
            }
            FileStream fs = new FileStream(fileName, FileMode.Open, FileAccess.Read);
            List<string> list = new List<string>();
            StreamReader sr = new StreamReader(fs);
            //使用StreamReader类来读取文件 
            sr.BaseStream.Seek(0, SeekOrigin.Begin);
            // 从数据流中读取每一行，直到文件的最后一行
            string tmp = sr.ReadLine();
            while (tmp != null)
            {
                list.Add(tmp);
                tmp = sr.ReadLine();
            }
            //关闭此StreamReader对象 
            sr.Close();
            fs.Close();
            return list;
        }
        //读取文本文件转换为List 
        public static List<ToJsonMy> ReadJsonFileToList(string fileName)
        {
            if (!File.Exists(fileName))
            {
                File.Create(fileName);
            }
            FileStream fs = new FileStream(fileName, FileMode.Open, FileAccess.Read);
            List<ToJsonMy> list = new List<ToJsonMy>();
            ToJsonMy my = new ToJsonMy();
            StreamReader sr = new StreamReader(fs);
            //使用StreamReader类来读取文件 
            sr.BaseStream.Seek(0, SeekOrigin.Begin);
            // 从数据流中读取每一行，直到文件的最后一行
            string tmp = sr.ReadLine();
            while (tmp != null)
            {
                my = JsonConvert.DeserializeObject<ToJsonMy>(tmp);
                //if(my.status!="5")
                //{
                    list.Add(my);
                //}
                tmp = sr.ReadLine();
            }
            //关闭此StreamReader对象 
            sr.Close();
            fs.Close();
            return list;
        }
    }
}
