using System;
using System.IO;
namespace NKprint
{
    class openFile
    {
        //static string path=string.Empty;
        //打开已经下载文件所在的文件夹
        public static bool open()
        {
            String Date = (DateTime.Now.ToLongDateString());
            string  path = @"D:\云印南开\" + Date;
            if (!Directory.Exists(path))
            {
                Directory.CreateDirectory(path);
            }
            try
            {
                System.Diagnostics.Process.Start(path);
            }
            /*OpenFileDialog ofd = new OpenFileDialog();
            if (ofd.ShowDialog(path) == System.Windows.Forms.DialogResult.OK)
            {
                string file = ofd.FileName;
                return true;
            }*/
            catch
            {
                return false;
            }
            return false;
        }
        
    }
}
