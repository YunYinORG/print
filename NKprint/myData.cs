//我们定义的JSON数据结构 
//属性的名字，必须与json格式字符串中的"key"值一样。
public class ToJsonMy
{
    private string m_double_side;
    private string m_copies;
    private string m_status;

    public string id { get; set; }//文件编号
    public string use_id { get; set; }//
    public string use_name { get; set; }//
    public string student_number { get; set; }//
    public string pri_id { get; set; }
    public string name { get; set; }
    public string url { get; set; }
    public string time { get; set; }
   
    public string copies { 
        get
        {
            if (m_copies == "0")
            {
                return "现场打印";
            }
            else
                return m_copies + "份";
        }
        set { m_copies=value;}

    }

    public string double_side {
        get
        {
            if (m_double_side == "0")
            {
                return  "单面";
            }
            else          
                return  "双面";
        }
        set { m_double_side=value;}
    }
    //根据属性的特点，将存储的和得到的status建立起映射关系；
    public string status
    {
        get
        {
            switch (m_status)
            {
                case "1":
                    return  "未下载";
                case "2":
                case "download":
                    return  "已下载";
                case "3":
                case "printing":
                    return  "正打印";
                case "4":
                case "printed":
                    return  "已打印";
                case "5":
                case "payed":
                    return  "已付款";
            }
            return "0";
        }
        set
        {
            m_status = value;
        }
    }
    public string color { get; set; }
    public string ppt_layout { get; set; }
    public string requirements { get; set; }
}
//定义获取api的token
public struct ToMyToken
{
    public string token { get; set; }
    public string name { get; set; }
    public string id { get; set; }
    public float version { get; set; }
}
//声明用户的隐私信息
//{
//    "id": "用户id",
//    "name": "姓名",
//    "student_number": "学号",
//    "gender": "性别",
//    "phone": "真实手机号",
//    "email": "真实邮箱",
//    "status": "用户状态标识码"
// }
public class userInfo
{
    public string id { get; set; }
    public string name { get; set; }
    public string student_number { get; set; }
    public string gender { get; set; }
    public string phone { get; set; }
    public string email { get; set; }
    public string status { get; set; }
}

