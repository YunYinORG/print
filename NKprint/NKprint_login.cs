using System;
using System.Text;
using System.Windows.Forms;
using Newtonsoft.Json.Linq;//Jobject
using System.Collections.Generic;
using System.Threading;
namespace NKprint
{
    public partial class NKprint_login : Form
    {
        //定义一些要用到的通用的字符串
        List<string> myLogin = new List<string>();
        public NKprint_login()
        {
            InitializeComponent();
        }

        private void NKprint_login_Load(object sender, EventArgs e)
        {
            System.Windows.Forms.Control.CheckForIllegalCrossThreadCalls = false;
            myLogin = remember.ReadTextFileToList(@"pwd.sjc");
            if (myLogin.Count == 2)
            {
                printerAccount.Text = myLogin[0];
                printerPassword.Text = myLogin[1];
            }     
        }
        //登录服务器事件
        private void buttonLogin_Click(object sender, EventArgs e)
        {
            //执行登陆函数
            Thread loginThread = new Thread(loginFrom);
            loginThread.Start();
        }
        //登陆到服务器，post方式验证，用到API类，和remember类
        public void loginFrom()
        {
            string type = "2";//打印店默认的type是2;
            //定义List的myRem记录登陆的用户名和密码
            List<string> myRem = new List<string>();

            string account = printerAccount.Text;
            string strPassword = printerPassword.Text;

            if (account.Length == 0 || strPassword.Length == 0)
            {
                labelError.Text = "请输入！";
                labelError.Visible = true;
            }
            else
            {
                labelWait.Visible = true;
                string js = "type=" + type + "&account=" + account +
                "&pwd=" + strPassword;
                //POST得到要数据//登陆得到token
                string r = API.PostWebRequest("", js, new UTF8Encoding());
                //从post得到的数据中得到token 
                JObject toke = JObject.Parse(r);
                ToMyToken my = new ToMyToken();
                bool loginOk = r.Contains("token");
                if (loginOk == true)
                {
                    my.token = (string)toke["token"];//也能够得到token
                    my.name = (string)toke["name"];
                    my.id = (string)toke["id"];
                    //判断是否保存用户名
                    if (checkRemember.Checked)
                    {
                        myRem.Add(printerAccount.Text);
                        myRem.Add(printerPassword.Text);
                        
                    }
                    
                    remember.WriteListToTextFile(myRem, @"pwd.sjc");
#if DEBUG
                    Console.WriteLine(my.token);
                    Console.WriteLine(my.name);
                    Console.WriteLine(my.id);
#endif
                    showDownloadForm(my);
                    
                }
                //如果登陆不成功
                else
                {
                    /*foreach (Control c in this.Controls)
                        deleteText(c);*/
                    clearText();
                    //显示登陆失败的label
                    labelError.Text = "登陆失败，请重新登录！";
                    labelError.Visible = true;
                    labelWait.Visible = false;
                }
            }
        }
        //如果登陆成功转到NKprint_download.cs
        //并且在NKprint_download中退出后返回此窗体
        private void showDownloadForm(ToMyToken my)
        {
            this.Hide();
            NKprint_download nForm = new NKprint_download();
            nForm.downloadToken = my.token;
            nForm.printerName = my.name;
            nForm.printerId = my.id;
            //窗体之间传值完成 ，显示下载窗体！
            nForm.ShowDialog();
            /*if (nForm.DialogResult == DialogResult.OK)
            {
                this.Show();
            }*/
        }
        // public object ToObject(Type objectType);//ToObject转换函数。。！！！！！！
        //清除textBox中的字符
        private void deleteText(Control c)
        {
            if (c is Panel)
            {
                foreach (Control cc in (c as Panel).Controls)
                    deleteText(cc);
            }
            else
            {
                if (c is TextBox)
                    (c as TextBox).Clear();
            }
        }
        //重置用户的输入：
        private void buttonRest_Click(object sender, EventArgs e)
        {
            labelError.Visible = false;
            /*foreach (Control c in this.Controls)
            {
                deleteText(c);
            }*/
            clearText();
        }
        private void clearText()
        {
            printerAccount.Text = "";
            printerPassword.Text = "";
        }
    }
}
