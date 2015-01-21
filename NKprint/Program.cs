using System;
using System.Windows.Forms;
using System.Linq;
namespace NKprint
{
    static class Program
    {
        //将服务器地址改为外部可以配置
        public static string downloadUrl = @"http://newfuture-uploads.stor.sinaapp.com/";
        public static string serverUrl = @"https://newfuture.sinaapp.com/api.php";
        /// <summary>
        /// 应用程序的主入口点。
        /// </summary>
        [STAThread]
        static void Main()
        {
            /*string[] words = { "aPPLE", "BlUeBeRrY", "cHeRry" };

            // If a query produces a sequence of anonymous types, 
            // then use var in the foreach statement to access the properties.
            var upperLowerWords =
                 from w in words
                 select new { Upper = w.ToUpper(), Lower = w.ToLower() };

            // Execute the query
            foreach (var ul in upperLowerWords)
            {
                Console.WriteLine("Uppercase: {0}, Lowercase: {1}", ul.Upper, ul.Lower);
            }*/
            Application.EnableVisualStyles();
            Application.SetCompatibleTextRenderingDefault(false);
            Application.Run(new NKprint_login());
        }
    }
}
