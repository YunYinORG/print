using System;
using System.Text;
using System.Windows.Forms;
using System.Collections;//ArrayList
using Newtonsoft.Json.Linq;//Jobject
using System.Web.Extensions;
namespace NKprint
{
    public partial class NKprint_login : Form
    {
        public NKprint_login()
        {
            InitializeComponent();
        }

        private void NKprint_login_Load(object sender, EventArgs e)
        {

        }
        //定义一些要用到的通用的字符串
        string printerName;//获取的打印店的名字和Id
        string printerId;
        private void buttonLogin_Click(object sender, EventArgs e)
        {
            printerAccount.Text = "songxu";
            printerPassword.Text = "5261014";
            string myDownloadToken;
            string type = "2";//打印店默认的type是2;
            string account = printerAccount.Text;
            string strPassword = printerPassword.Text;
            if (account.Length == 0 || strPassword.Length == 0)
            {
                labelError.Text = "请输入！";
                //labelError.ForeColor = System.Drawing.Color.Red;
                labelError.Visible = true;
            }
            else
            {
                string js = "type=" + type + "&account=" + account +
                "&pwd=" + strPassword;
                //POST得到要数据
                //登陆得到token
                string r = API.PostWebRequest("", js, new UTF8Encoding());
                //从post得到的数据中得到token
                ArrayList to = new ArrayList();
                JObject toke = JObject.Parse(r);
               // object user = toke.ToObject(typeof(ToMyToken));
                object printer = JObject.Parse(r);
                ToMyToken my = new ToMyToken() ;
                
                bool loginOk = r.Contains("token");
                Console.WriteLine(loginOk);
                if (loginOk == true)
                {
                    my.token = (string)toke["token"];//也能够得到token
                    my.name=(string)toke["name"];
                    my.id=(string)toke["id"];
                    myDownloadToken = my.token;
                    printerName = my.name;
                    printerId = my.id;
#if DEBUG
                    Console.WriteLine(myDownloadToken);
                    Console.WriteLine(printerName);
                    Console.WriteLine(printerId);
#endif
                    //如果登陆成功转到NKprint_download.cs
                    //并且在NKprint_download中退出后返回此窗体
                    this.Hide(); 
                    NKprint_download nForm = new NKprint_download();
                    nForm.downloadToken = myDownloadToken;
                    nForm.printerName = printerName;
                    nForm.printerId = printerId;
                    //窗体之间传值完成 ，显示下载窗体！
                    nForm.ShowDialog();
                    //
                    if(nForm.DialogResult==DialogResult.OK)
                    {
                        this.Show();
                    }
                    //this.Owner=nForm;
                    
                    
                }
                //如果登陆不成功
                else
                {
                    foreach (Control c in this.Controls)
                        deleteText(c);
                    //显示登陆失败的label
                    labelError.Text = "登陆失败，请重新登录！";
                    labelError.Visible = true;
                }
            }
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
            foreach (Control c in this.Controls)
            {
                deleteText(c);
            }
        }
    }
}
