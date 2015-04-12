using System;
using System.Windows.Forms;
using System.Linq;
namespace NKprint
{
    static class Program
    {
        //将服务器地址改为外部可以配置
        public static string downloadUrl = @"http://nkuprint-uploads.stor.sinaapp.com/";
        public static string serverUrl = @"http://nkuprint.sinaapp.com/api.php";
        /// <summary>
        /// 应用程序的主入口点。
        /// </summary>
        [STAThread]
        static void Main()
        {
            Application.EnableVisualStyles();
            Application.SetCompatibleTextRenderingDefault(false);
            Application.Run(new NKprint_login());
        }
    }
}
