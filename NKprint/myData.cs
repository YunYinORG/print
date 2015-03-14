//我们定义的JSON数据结构 
//属性的名字，必须与json格式字符串中的"key"值一样。
public class ToJsonMy
{
    //public string stat;
    public string id { get; set; }//文件编号
    public string use_id { get; set; }//
    public string use_name { get; set; }//
    public string student_number { get; set; }//
    public string pri_id { get; set; }
    public string name { get; set; }
    public string url { get; set; }
    public string time { get; set; }
    public string requirements { get; set; }
    public string copies { get; set; }
    public string double_side { get; set; }
    public string status { get; set; }
    /*{
        set { stat = value; }
        get
        {
            switch (stat)
            {
                case"1":
                    return "1";
                case "download":
                case"2":
                    return "2";

                case "printing":
                case "3":
                    return "3";

                case "printed":
                case "4":
                    return "4";

                case "payed":
                case "5":
                    return "5";

                default:
                    return "0";

            }
        }*/
    
}
//定义获取api的token
public struct ToMyToken
{
    public string token { get; set; }
    public string name { get; set; }
    public string id { get; set; }
    public float version { get; set; }
}

