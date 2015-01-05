using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Windows.Forms;
using System.Net;
using System.IO;
using System.Web;
using System.Runtime.Serialization;
namespace NKprint
{
    class API
    {
        private static string server_url = @"http://newfuture.sinaapp.com/api.php";
       
        // REST @GET 方法，根据泛型自动转换成实体，支持List<T>
        public static string doGetMethodToObj(string metodUrl)
        {
            string down = server_url + @"/File/?token=" + metodUrl;
            HttpWebRequest request = (HttpWebRequest)WebRequest.Create(down);
            request.Method = "get";
            request.ContentType = "application/json;charset=UTF-8";
            HttpWebResponse response = null;
            try
            {
                response = (HttpWebResponse)request.GetResponse();
            }
            catch (WebException e)
            {
                response = (HttpWebResponse)e.Response;
                MessageBox.Show(e.Message + " - " + getRestErrorMessage(response));
                return default(string);
            }
            string json = getResponseString(response);
            return json;
        }
        
        //post方法来从服务器访问数据
        public static string PostWebRequest(string postUrl, string paramData, Encoding dataEncode)
        {
            postUrl = server_url + postUrl;
            string ret = string.Empty;
            try
            {
                byte[] byteArray = dataEncode.GetBytes(paramData); //转化
                HttpWebRequest webReq = (HttpWebRequest)WebRequest.Create(new Uri(postUrl));
                webReq.Method = "POST";
                webReq.ContentType = "application/x-www-form-urlencoded";

                webReq.ContentLength = byteArray.Length;
                Stream newStream = webReq.GetRequestStream();
                newStream.Write(byteArray, 0, byteArray.Length);//写入参数
                newStream.Close();
                HttpWebResponse response = (HttpWebResponse)webReq.GetResponse();
                StreamReader sr = new StreamReader(response.GetResponseStream(), Encoding.Default);
                ret = sr.ReadToEnd();
                sr.Close();
                response.Close();
                newStream.Close();
            }
            catch (Exception ex)
            {
                MessageBox.Show(ex.Message);
            }
            return ret;
        }
        private static string getResponseString(HttpWebResponse response)
        {
            string json = null;
            using (StreamReader reader = new StreamReader(response.GetResponseStream(), System.Text.Encoding.GetEncoding("UTF-8")))
            {
                json = reader.ReadToEnd();
            }
            return json;
        }
        // 获取异常信息
        private static string getRestErrorMessage(HttpWebResponse errorResponse)
        {
            string errorhtml = getResponseString(errorResponse);
            string errorkey = "spi.UnhandledException:";
            errorhtml = errorhtml.Substring(errorhtml.IndexOf(errorkey) + errorkey.Length);
            errorhtml = errorhtml.Substring(0, errorhtml.IndexOf("\n"));
            return errorhtml;
        }
    }
}
