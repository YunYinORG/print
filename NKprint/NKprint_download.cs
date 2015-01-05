using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Windows.Forms;
using System.Collections;
using Newtonsoft.Json.Linq;
using System.Threading;
using System.Net;
using System.IO;
using System.Web;
using System.Runtime.Serialization;
namespace NKprint
{
    public partial class NKprint_download : Form
    {
        //下载文件的url
        private static string download_url = @"http://newfuture-uploads.stor.sinaapp.com/";
        //定义用来存储已操作的文件 
        static ArrayList myDown = new ArrayList();
        static ArrayList myPrinting = new ArrayList();
        static ArrayList myPrinted = new ArrayList();
        static ArrayList myPay = new ArrayList();
        //定义接收从NKprint_login窗体传值的参数
        public string downloadToken;
        public string printerName;
        public string printerId;

        //窗体类的构造函数
        public NKprint_download()
        {
            /*this.Hide();
            NKprint_login nForm = new NKprint_login();
            if (nForm.ShowDialog() != DialogResult.OK)    //判断登录窗体返回值
            {
                Environment.Exit(Environment.ExitCode);    //退出程序
                return;
            }
            nForm.Owner = this;
            nForm.Show();
            if (nForm.ShowDialog() != DialogResult.OK)    //判断登录窗体返回值
            {
                Environment.Exit(Environment.ExitCode);    //退出程序
                this.Show();
            }*/
            InitializeComponent();
            // 在现在线程和UI线程出问题的时候false一下。。。。。。
            //System.Windows.Forms.Control.CheckForIllegalCrossThreadCalls = false;
        }
        private void NKprint_download_Load(object sender, EventArgs e)
        {
            labelWelcom.Text = "你好："+printerName+"\n欢迎登陆!";
            //登陆窗体后刷新下载列表
            myRefresh();
        }
        private void labelWelcom_Click(object sender, EventArgs e)
        {
        }
        
        //手动刷新下载列表刷新下载列表
        private void buttonRefresh_Click(object sender, EventArgs e)
        {
            myRefresh();
        }
        //刷新下载列表函数
        public void  myRefresh()
        {
            string myJsFile = API.doGetMethodToObj(downloadToken);
#if DEBUG
            Console.WriteLine(myJsFile);
#endif
            //ArrayList objList = new ArrayList();
            JObject jo = JObject.Parse(myJsFile);

            List<ToJsonMy> jsonList = new List<ToJsonMy>();  //附近位置的列表
            JArray ja = jo["files"] as JArray;
            //将JArray类型的ja转化为ToMyJohn对象数组
            int i = 0;
            for (i = 0; i < ja.Count; i++)//遍历ja数组
            {
                ToJsonMy myJs = new ToJsonMy();
                myJs.id = ja[i]["id"].ToString();
                myJs.name = ja[i]["name"].ToString();
                myJs.use_id = ja[i]["use_id"].ToString();
                myJs.pri_id = ja[i]["pri_id"].ToString();
                myJs.url = ja[i]["url"].ToString();
                myJs.time = ja[i]["time"].ToString();
                myJs.name = ja[i]["name"].ToString();
                myJs.status = ja[i]["status"].ToString();
                myJs.copies = ja[i]["copies"].ToString();
                myJs.use_name = ja[i]["use_name"].ToString();
                myJs.double_side = ja[i]["double_side"].ToString();
                myJs.student_number = ja[i]["student_number"].ToString();
                //如果已经付款则没有意义，不添加到jsonList 中
                if (myJs.status != "5")
                {
                    jsonList.Add(myJs);
                }
            }
            //已经将得到的文件列表保存到类型（list<yoJsonMy>）jsonList中
            //显示所得到的的文件列表，而且要显示的是状态
            //定义文件的初始状态
            bool down = false, printing = false, printed = false, pay = false;
            string id1, userName1, fileName1, copies1, doubleSides1, statues1 = null;
            for (i = 0; i < jsonList.Count; i++)
            {
                down = myDown.Contains(jsonList[i].id);
                printing = myPrinting.Contains(jsonList[i].id);
                printed = myPrinted.Contains(jsonList[i].id);
                pay = myPay.Contains(jsonList[i].id);
                if (!down && !printing && !printed && !pay)
                {

                    //判断列表中的值来显示文件信息
                    id1 = jsonList[i].id;
                    userName1 = jsonList[i].use_name;
                    fileName1 = jsonList[i].name;
                    copies1 = jsonList[i].copies + "份";
                    if (jsonList[i].double_side == "0")
                    {
                        doubleSides1 = "单面";
                    }
                    else
                    {
                        doubleSides1 = "双面";
                    }
                    switch (jsonList[i].status)
                    {
                        case "1":
                            statues1 = "未下载";
                            break;
                        case "2":
                            statues1 = "已下载";
                            break;
                        case "3":
                            statues1 = "正打印";
                            break;
                        case "4":
                            statues1 = "已打印";
                            break;
                        case "5":
                            statues1 = "已付款";
                            break;
                    }
                    this.myData.Rows.Add(id1, userName1, fileName1, copies1, doubleSides1, statues1);
                }

            }

            this.myData.Rows[0].ErrorText = "wrong";
            //download d = new download();
            string downurl = null;
            string filename = null;
            //定义线程执行下载程序
            Thread piThread1 = new Thread(delegate()
            {
                //在list中使用jsonList，遍历下载
                for (i = 0; i < jsonList.Count; i++)
                {
                    //down = myDown.Contains(jsonList[i].id);
                    //printing = myPrinting.Contains(jsonList[i].id);
                    //printed = myPrinted.Contains(jsonList[i].id);
                    //pay = myPay.Contains(jsonList[i].id);
                    //if (!down && !printing && !printed && !pay)
                    //{
                    //bool isdown = myDown.Contains(((ToJsonMy)(objList[i])).id);
                    //if ((isdown == false) && (int.Parse(((ToJsonMy)(objList[i])).status) == 1))
                    //{
                    //d.lv = listView;
                    downurl = jsonList[i].url;
                    filename = jsonList[i].name;
                    fileDownload(downurl, filename, i);
                    // }
                    //}
                }
            });
            piThread1.Start();
        }
        //Button事件退出当前登陆，同事返回到登陆界面
        private void buttonExit_Click(object sender, EventArgs e)
        {
            myDown.Clear();
            myPrinting.Clear();
            myPrinted.Clear();
            myPay.Clear();
            this.Close();
            this.DialogResult = DialogResult.OK;
            
        }
        //窗体关闭事件退出当前登陆，退出应用程序
        private void NKprint_download_FormClosed(object sender, FormClosedEventArgs e)
        {
            this.Close();
            this.DialogResult = DialogResult.OK;
            //Application.Exit();
        }
        //点击状态列，修改当前的文件状态,并post 到服务器
        private void myData_CellContentClick(object sender, DataGridViewCellEventArgs e)
        {
            if ((e.ColumnIndex + 1)==6)
            {
                //用switch
                switch (myData.Rows[e.RowIndex].Cells[e.ColumnIndex].Value.ToString())
                {
                    case "已下载":
                        myData.Rows[e.RowIndex].Cells[e.ColumnIndex].Value = "正打印";
                        myPrinting.Add(myData.Rows[e.RowIndex].Cells[0].Value);
                        changeStatusById(e.RowIndex, "printing");
                        break;
                    case "正打印":
                        myData.Rows[e.RowIndex].Cells[e.ColumnIndex].Value = "已打印";
                        myPrinted.Add(myData.Rows[e.RowIndex].Cells[0].Value);
                        changeStatusById(e.RowIndex, "printed");
                        break;
                    case "已打印":
                        myData.Rows[e.RowIndex].Cells[e.ColumnIndex].Value = "已支付";
                        myPay.Add(myData.Rows[e.RowIndex].Cells[0].Value);
                        changeStatusById(e.RowIndex, "payed");
                        break;
                    default:
                        break;

                }
            }            
            //MessageBox.Show("您单击的是第" + (e.RowIndex + 1) + "行第" + (e.ColumnIndex + 1) + "列！");
            //MessageBox.Show("单元格的内容是：" + myData.Rows[e.RowIndex].Cells[e.ColumnIndex].Value.ToString());
        }
        //下载从服务器得到的json数据中的用户打印文件
        public bool fileDownload(string url, string fileName, int i)
        {

            //下载文件地址等于服务器地址加上文件地址
            url = download_url + url;
            DateTime start = DateTime.Now;
            DateTime lastTime = DateTime.Now;
            String Date = (DateTime.Now.ToShortDateString());
            String path = @"D:\" + Date;
            //使用Directory要用到System.IO
            if (!Directory.Exists(path))
            {
                Directory.CreateDirectory(path);
            }
            WebClient webClient = new WebClient();
            String pathDoc = path + "/" + fileName;
            //添加下载完成后的事件
            webClient.DownloadFileCompleted += new AsyncCompletedEventHandler(webClient_DownloadFileCompleted);
            try
            {
                webClient.DownloadFileAsync(new Uri(url), pathDoc, i);
            }
            catch
            {
                return false;
                //判断出错
            }
            return true;
        }
        //webClient下载完成后相应的事件，下载完成后，调用改变状态函数
        void webClient_DownloadFileCompleted(object sender, AsyncCompletedEventArgs e)
        {
            int i = int.Parse(e.UserState.ToString());
            myDown.Add(myData.Rows[i].Cells[0].Value);
            changeStatusByi(i, "download");
        }
        public void changeStatusByi(int i, string currentStatus)
        {
            bool down, printing, printed, pay;
            down = myData.Rows[i].Cells[5].Value.Equals( "已下载");
            printing = myData.Rows[i].Cells[5].Value.Equals( "正打印");
            printed = myData.Rows[i].Cells[5].Value.Equals( "已打印");
            pay = myData.Rows[i].Cells[5].Value.Equals("已支付");
            //bool flag=!down&&!printed!printing&&!pay;
            if (!down&&!printing&&!printed&&!pay)
            {
                if (currentStatus == "download")
                    myData.Rows[i].Cells[5].Value = "已下载";
                //put到服务器状态;/api.php/File/1234?token=xxxxxxxxxxxx 
                //将下载完成的文件id添加到下载完成myDown （ArrayList）中
           
                //参数： status=>文件状态'uploud','download','printing','printed','payed', 返回操作结果
                string putUrl = @"/File/" + myData.Rows[i].Cells[0].Value + "?token=" + downloadToken;
                string putPara = "status=" + currentStatus;
                string out1 = API.PostWebRequest(putUrl, putPara, new UTF8Encoding());
                Console.WriteLine(out1);
            }
        }
        //
        public void changeStatusById(int i, string currentStatus)
        {
            //put到服务器状态;/api.php/File/1234?token=xxxxxxxxxxxx 
            //将下载完成的文件id添加到下载完成myDown （ArrayList）中

            //参数： status=>文件状态'uploud','download','printing','printed','payed', 返回操作结果
            string putUrl = @"/File/" + myData.Rows[i].Cells[0].Value + "?token=" + downloadToken;
            string putPara = "status=" + currentStatus;
            string out1 = API.PostWebRequest(putUrl, putPara, new UTF8Encoding());
            Console.WriteLine(out1);
        }
        //打开下载文件所在的文件夹
        private void button1_Click(object sender, EventArgs e)
        {
            openFile.open();
        }

    }
}
