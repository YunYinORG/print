using System;
using System.Text;
using System.Windows.Forms;
using System.Net;
using System.IO;
namespace NKprint
{
    class API
    {
        public static string token = "";
        public static int myPage=1;
        private static string server_url = Program.serverUrl;
        public static string PutMethod(string metodUrl, string para, Encoding dataEncode)
        {
            string down = server_url + metodUrl;
            HttpWebRequest request = (HttpWebRequest)WebRequest.Create(down);
            if (token != "")
            {
                request.Headers.Add("Token", token);
            }
            request.Method = "PUT";
            string s = "1";
            //HttpWebResponse response = (HttpWebResponse)request.GetResponse();

            request.Accept = "Accept: application/json";
            try
            {
                //byte[] byteArray = dataEncode.GetBytes(para); //转化
                //request.ContentType = "application/x-www-form-urlencoded";
                //request.Accept = "Accept: application/json";
                //request.ContentLength = byteArray.Length;
                //Stream newStream = request.GetRequestStream();
                //newStream.Write(byteArray, 0, byteArray.Length);//写入参数
                //newStream.Close();

                //Console.WriteLine("\nThe HttpHeaders are \n\n\tName\t\tValue\n{0}", request.Headers);
                //HttpWebResponse response = (HttpWebResponse)request.GetResponse();
                //StreamReader sr = new StreamReader(response.GetResponseStream(), Encoding.Default);
                //s = sr.ReadToEnd();
                //sr.Close();
                //response.Close();
                //newStream.Close();
                using (StreamWriter writer = new StreamWriter(request.GetRequestStream()))
                {
                    writer.Write(para);
                }
                HttpWebResponse response = (HttpWebResponse)request.GetResponse();
                using (StreamReader reader = new StreamReader(response.GetResponseStream()))
                {
                    //while (reader.Peek() != -1)
                    //{
                    //    Console.WriteLine(reader.ReadLine());
                    //}
                    s = reader.ReadToEnd();
                    reader.Close();
                }
            }
            catch (Exception ex)
            {
                MessageBox.Show(ex.Message);
            }
            return s;
            
        }

        public static string DeleteMethod(string metodUrl)
        {
            string s = "1";

            string url = server_url + metodUrl;
            HttpWebRequest request = (HttpWebRequest)WebRequest.Create(url);
            request.Accept = "Accept: Application/json";
            request.Method = "delete";
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
            return s;
        }

        //GetMEthod用来完成Accept: application/json
        public static string GetMethod(string metodUrl)
        {
            string down = server_url  + metodUrl;
            HttpWebRequest request = (HttpWebRequest)WebRequest.Create(down);
            if (token!="")
            {
                request.Headers.Add("Token", token);//修改Headers,添加
            }
            request.Method = "get";
            request.Accept = "Accept: application/json";
            //request.ContentType = "application/json;charset=UTF-8";
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
            //
            //Console.WriteLine("\nThe HttpHeaders are \n\n\tName\t\tValue\n{0}", request.Headers);
            string json = getResponseString(response);
            return json;
        }

        // REST @GET 方法，根据泛型自动转换成实体，支持List<T>
        public static string doGetMethodToObj(string metodUrl)
        {
            //get获取。/api.php/File/?page=2&token=....
            string down = server_url + metodUrl;
            //string down = server_url + @"/File/?token=" + metodUrl;
            HttpWebRequest request = (HttpWebRequest)WebRequest.Create(down);
            request.Method = "get";
            request.Accept = "Accept:application/json";
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
        public static string PostMethod(string postUrl, string paramData, Encoding dataEncode)
        {
            postUrl = server_url + postUrl;
            string ret = string.Empty;
            try
            {
                byte[] byteArray = dataEncode.GetBytes(paramData); //转化
                HttpWebRequest webReq = (HttpWebRequest)WebRequest.Create(new Uri(postUrl));
                webReq.Method = "POST";
                webReq.ContentType = "application/x-www-form-urlencoded";
                webReq.Accept = "Accept: application/json";
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
