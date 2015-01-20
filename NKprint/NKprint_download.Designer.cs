namespace NKprint
{
    partial class NKprint_download
    {
        /// <summary>
        /// Required designer variable.
        /// </summary>
        private System.ComponentModel.IContainer components = null;

        /// <summary>
        /// Clean up any resources being used.
        /// </summary>
        /// <param name="disposing">true if managed resources should be disposed; otherwise, false.</param>
        protected override void Dispose(bool disposing)
        {
            if (disposing && (components != null))
            {
                components.Dispose();
            }
            base.Dispose(disposing);
        }

        #region Windows Form Designer generated code

        /// <summary>
        /// Required method for Designer support - do not modify
        /// the contents of this method with the code editor.
        /// </summary>
        private void InitializeComponent()
        {
            this.components = new System.ComponentModel.Container();
            System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(NKprint_download));
            this.panelControl = new System.Windows.Forms.Panel();
            this.textDownload = new System.Windows.Forms.TextBox();
            this.label2 = new System.Windows.Forms.Label();
            this.textStudent = new System.Windows.Forms.TextBox();
            this.label1 = new System.Windows.Forms.Label();
            this.buttonRefresh = new System.Windows.Forms.Button();
            this.buttonSearch = new System.Windows.Forms.Button();
            this.pictureBox1 = new System.Windows.Forms.PictureBox();
            this.labelWelcom = new System.Windows.Forms.Label();
            this.panelShow = new System.Windows.Forms.Panel();
            this.myData = new System.Windows.Forms.DataGridView();
            this.id = new System.Windows.Forms.DataGridViewTextBoxColumn();
            this.suserName = new System.Windows.Forms.DataGridViewTextBoxColumn();
            this.fileName = new System.Windows.Forms.DataGridViewTextBoxColumn();
            this.copies = new System.Windows.Forms.DataGridViewTextBoxColumn();
            this.doubleSides = new System.Windows.Forms.DataGridViewTextBoxColumn();
            this.status = new System.Windows.Forms.DataGridViewLinkColumn();
            this.menuStrip1 = new System.Windows.Forms.MenuStrip();
            this.文件ToolStripMenuItem = new System.Windows.Forms.ToolStripMenuItem();
            this.刷新下载ToolStripMenuItem = new System.Windows.Forms.ToolStripMenuItem();
            this.打开下载ToolStripMenuItem = new System.Windows.Forms.ToolStripMenuItem();
            this.退出ToolStripMenuItem = new System.Windows.Forms.ToolStripMenuItem();
            this.测试jsonLINQToolStripMenuItem = new System.Windows.Forms.ToolStripMenuItem();
            this.帮助ToolStripMenuItem = new System.Windows.Forms.ToolStripMenuItem();
            this.版本ToolStripMenuItem = new System.Windows.Forms.ToolStripMenuItem();
            this.toolTip1 = new System.Windows.Forms.ToolTip(this.components);
            this.panelControl.SuspendLayout();
            ((System.ComponentModel.ISupportInitialize)(this.pictureBox1)).BeginInit();
            this.panelShow.SuspendLayout();
            ((System.ComponentModel.ISupportInitialize)(this.myData)).BeginInit();
            this.menuStrip1.SuspendLayout();
            this.SuspendLayout();
            // 
            // panelControl
            // 
            this.panelControl.BackColor = System.Drawing.SystemColors.ControlDark;
            this.panelControl.Controls.Add(this.textDownload);
            this.panelControl.Controls.Add(this.label2);
            this.panelControl.Controls.Add(this.textStudent);
            this.panelControl.Controls.Add(this.label1);
            this.panelControl.Controls.Add(this.buttonRefresh);
            this.panelControl.Controls.Add(this.buttonSearch);
            this.panelControl.Controls.Add(this.pictureBox1);
            this.panelControl.Controls.Add(this.labelWelcom);
            this.panelControl.Location = new System.Drawing.Point(3, 29);
            this.panelControl.Name = "panelControl";
            this.panelControl.Size = new System.Drawing.Size(193, 299);
            this.panelControl.TabIndex = 1;
            // 
            // textDownload
            // 
            this.textDownload.Location = new System.Drawing.Point(84, 183);
            this.textDownload.Name = "textDownload";
            this.textDownload.Size = new System.Drawing.Size(101, 21);
            this.textDownload.TabIndex = 7;
            // 
            // label2
            // 
            this.label2.Font = new System.Drawing.Font("宋体", 10.5F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(134)));
            this.label2.ForeColor = System.Drawing.Color.MidnightBlue;
            this.label2.Location = new System.Drawing.Point(3, 186);
            this.label2.Name = "label2";
            this.label2.Size = new System.Drawing.Size(104, 26);
            this.label2.TabIndex = 6;
            this.label2.Text = "请输入id:";
            // 
            // textStudent
            // 
            this.textStudent.Location = new System.Drawing.Point(84, 105);
            this.textStudent.Name = "textStudent";
            this.textStudent.Size = new System.Drawing.Size(101, 21);
            this.textStudent.TabIndex = 5;
            // 
            // label1
            // 
            this.label1.Font = new System.Drawing.Font("宋体", 10.5F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(134)));
            this.label1.ForeColor = System.Drawing.Color.MidnightBlue;
            this.label1.Location = new System.Drawing.Point(3, 106);
            this.label1.Name = "label1";
            this.label1.Size = new System.Drawing.Size(104, 26);
            this.label1.TabIndex = 4;
            this.label1.Text = "请输入学号:";
            // 
            // buttonRefresh
            // 
            this.buttonRefresh.Font = new System.Drawing.Font("宋体", 10.5F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(134)));
            this.buttonRefresh.ForeColor = System.Drawing.SystemColors.ActiveCaption;
            this.buttonRefresh.Location = new System.Drawing.Point(0, 213);
            this.buttonRefresh.Name = "buttonRefresh";
            this.buttonRefresh.Size = new System.Drawing.Size(190, 46);
            this.buttonRefresh.TabIndex = 3;
            this.buttonRefresh.Tag = "";
            this.buttonRefresh.Text = "定位下载文件";
            this.toolTip1.SetToolTip(this.buttonRefresh, "重新下载输入id对应的文件");
            this.buttonRefresh.UseVisualStyleBackColor = true;
            this.buttonRefresh.Click += new System.EventHandler(this.buttonRefresh_Click);
            // 
            // buttonSearch
            // 
            this.buttonSearch.Font = new System.Drawing.Font("宋体", 10.5F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(134)));
            this.buttonSearch.ForeColor = System.Drawing.SystemColors.ActiveCaption;
            this.buttonSearch.Location = new System.Drawing.Point(0, 132);
            this.buttonSearch.Name = "buttonSearch";
            this.buttonSearch.Size = new System.Drawing.Size(190, 46);
            this.buttonSearch.TabIndex = 2;
            this.buttonSearch.Text = "查找下载文件";
            this.toolTip1.SetToolTip(this.buttonSearch, "更具输入学号高亮所在行");
            this.buttonSearch.UseVisualStyleBackColor = true;
            this.buttonSearch.Click += new System.EventHandler(this.buttonSearch_Click);
            // 
            // pictureBox1
            // 
            this.pictureBox1.Image = ((System.Drawing.Image)(resources.GetObject("pictureBox1.Image")));
            this.pictureBox1.Location = new System.Drawing.Point(11, 13);
            this.pictureBox1.Name = "pictureBox1";
            this.pictureBox1.Size = new System.Drawing.Size(74, 67);
            this.pictureBox1.TabIndex = 1;
            this.pictureBox1.TabStop = false;
            // 
            // labelWelcom
            // 
            this.labelWelcom.Font = new System.Drawing.Font("宋体", 10.5F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(134)));
            this.labelWelcom.ForeColor = System.Drawing.Color.FromArgb(((int)(((byte)(0)))), ((int)(((byte)(0)))), ((int)(((byte)(192)))));
            this.labelWelcom.Location = new System.Drawing.Point(91, 26);
            this.labelWelcom.Name = "labelWelcom";
            this.labelWelcom.Size = new System.Drawing.Size(94, 67);
            this.labelWelcom.TabIndex = 0;
            this.labelWelcom.Text = "你好，欢迎登陆";
            this.labelWelcom.Click += new System.EventHandler(this.labelWelcom_Click);
            // 
            // panelShow
            // 
            this.panelShow.Controls.Add(this.myData);
            this.panelShow.Location = new System.Drawing.Point(202, 29);
            this.panelShow.Name = "panelShow";
            this.panelShow.Size = new System.Drawing.Size(653, 300);
            this.panelShow.TabIndex = 2;
            // 
            // myData
            // 
            this.myData.AllowUserToAddRows = false;
            this.myData.ColumnHeadersHeightSizeMode = System.Windows.Forms.DataGridViewColumnHeadersHeightSizeMode.AutoSize;
            this.myData.Columns.AddRange(new System.Windows.Forms.DataGridViewColumn[] {
            this.id,
            this.suserName,
            this.fileName,
            this.copies,
            this.doubleSides,
            this.status});
            this.myData.Dock = System.Windows.Forms.DockStyle.Fill;
            this.myData.Location = new System.Drawing.Point(0, 0);
            this.myData.Name = "myData";
            this.myData.ReadOnly = true;
            this.myData.RowTemplate.Height = 23;
            this.myData.Size = new System.Drawing.Size(653, 300);
            this.myData.TabIndex = 0;
            this.myData.CellContentClick += new System.Windows.Forms.DataGridViewCellEventHandler(this.myData_CellContentClick);
            this.myData.CellContentDoubleClick += new System.Windows.Forms.DataGridViewCellEventHandler(this.myData_CellContentDoubleClick);
            // 
            // id
            // 
            this.id.HeaderText = "id";
            this.id.Name = "id";
            this.id.ReadOnly = true;
            // 
            // suserName
            // 
            this.suserName.HeaderText = "用户名";
            this.suserName.Name = "suserName";
            this.suserName.ReadOnly = true;
            // 
            // fileName
            // 
            this.fileName.HeaderText = "文件名";
            this.fileName.Name = "fileName";
            this.fileName.ReadOnly = true;
            // 
            // copies
            // 
            this.copies.HeaderText = "份数";
            this.copies.Name = "copies";
            this.copies.ReadOnly = true;
            // 
            // doubleSides
            // 
            this.doubleSides.HeaderText = "单双面";
            this.doubleSides.Name = "doubleSides";
            this.doubleSides.ReadOnly = true;
            // 
            // status
            // 
            this.status.HeaderText = "状态";
            this.status.Name = "status";
            this.status.ReadOnly = true;
            // 
            // menuStrip1
            // 
            this.menuStrip1.BackColor = System.Drawing.SystemColors.InactiveCaption;
            this.menuStrip1.Items.AddRange(new System.Windows.Forms.ToolStripItem[] {
            this.文件ToolStripMenuItem,
            this.帮助ToolStripMenuItem});
            this.menuStrip1.Location = new System.Drawing.Point(0, 0);
            this.menuStrip1.Name = "menuStrip1";
            this.menuStrip1.Size = new System.Drawing.Size(857, 25);
            this.menuStrip1.TabIndex = 4;
            this.menuStrip1.Text = "menuStrip1";
            // 
            // 文件ToolStripMenuItem
            // 
            this.文件ToolStripMenuItem.DropDownItems.AddRange(new System.Windows.Forms.ToolStripItem[] {
            this.刷新下载ToolStripMenuItem,
            this.打开下载ToolStripMenuItem,
            this.退出ToolStripMenuItem,
            this.测试jsonLINQToolStripMenuItem});
            this.文件ToolStripMenuItem.Name = "文件ToolStripMenuItem";
            this.文件ToolStripMenuItem.Size = new System.Drawing.Size(44, 21);
            this.文件ToolStripMenuItem.Text = "文件";
            // 
            // 刷新下载ToolStripMenuItem
            // 
            this.刷新下载ToolStripMenuItem.Name = "刷新下载ToolStripMenuItem";
            this.刷新下载ToolStripMenuItem.Size = new System.Drawing.Size(154, 22);
            this.刷新下载ToolStripMenuItem.Text = "刷新下载";
            this.刷新下载ToolStripMenuItem.Click += new System.EventHandler(this.刷新下载ToolStripMenuItem_Click);
            // 
            // 打开下载ToolStripMenuItem
            // 
            this.打开下载ToolStripMenuItem.Name = "打开下载ToolStripMenuItem";
            this.打开下载ToolStripMenuItem.Size = new System.Drawing.Size(154, 22);
            this.打开下载ToolStripMenuItem.Text = "打开下载";
            this.打开下载ToolStripMenuItem.Click += new System.EventHandler(this.打开下载ToolStripMenuItem_Click);
            // 
            // 退出ToolStripMenuItem
            // 
            this.退出ToolStripMenuItem.Name = "退出ToolStripMenuItem";
            this.退出ToolStripMenuItem.Size = new System.Drawing.Size(154, 22);
            this.退出ToolStripMenuItem.Text = "退出程序";
            this.退出ToolStripMenuItem.Click += new System.EventHandler(this.退出ToolStripMenuItem_Click);
            // 
            // 测试jsonLINQToolStripMenuItem
            // 
            this.测试jsonLINQToolStripMenuItem.Name = "测试jsonLINQToolStripMenuItem";
            this.测试jsonLINQToolStripMenuItem.Size = new System.Drawing.Size(154, 22);
            this.测试jsonLINQToolStripMenuItem.Text = "测试jsonLINQ";
            this.测试jsonLINQToolStripMenuItem.Click += new System.EventHandler(this.测试jsonLINQToolStripMenuItem_Click);
            // 
            // 帮助ToolStripMenuItem
            // 
            this.帮助ToolStripMenuItem.DropDownItems.AddRange(new System.Windows.Forms.ToolStripItem[] {
            this.版本ToolStripMenuItem});
            this.帮助ToolStripMenuItem.Name = "帮助ToolStripMenuItem";
            this.帮助ToolStripMenuItem.Size = new System.Drawing.Size(44, 21);
            this.帮助ToolStripMenuItem.Text = "帮助";
            // 
            // 版本ToolStripMenuItem
            // 
            this.版本ToolStripMenuItem.Name = "版本ToolStripMenuItem";
            this.版本ToolStripMenuItem.Size = new System.Drawing.Size(124, 22);
            this.版本ToolStripMenuItem.Text = "版本信息";
            this.版本ToolStripMenuItem.Click += new System.EventHandler(this.版本ToolStripMenuItem_Click);
            // 
            // toolTip1
            // 
            this.toolTip1.Popup += new System.Windows.Forms.PopupEventHandler(this.toolTip1_Popup);
            // 
            // NKprint_download
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 12F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(857, 330);
            this.Controls.Add(this.panelShow);
            this.Controls.Add(this.panelControl);
            this.Controls.Add(this.menuStrip1);
            this.FormBorderStyle = System.Windows.Forms.FormBorderStyle.FixedSingle;
            this.Icon = ((System.Drawing.Icon)(resources.GetObject("$this.Icon")));
            this.IsMdiContainer = true;
            this.Name = "NKprint_download";
            this.Text = "云印南开打印管理";
            this.FormClosed += new System.Windows.Forms.FormClosedEventHandler(this.NKprint_download_FormClosed);
            this.Load += new System.EventHandler(this.NKprint_download_Load);
            this.panelControl.ResumeLayout(false);
            this.panelControl.PerformLayout();
            ((System.ComponentModel.ISupportInitialize)(this.pictureBox1)).EndInit();
            this.panelShow.ResumeLayout(false);
            ((System.ComponentModel.ISupportInitialize)(this.myData)).EndInit();
            this.menuStrip1.ResumeLayout(false);
            this.menuStrip1.PerformLayout();
            this.ResumeLayout(false);
            this.PerformLayout();

        }

        #endregion

        private System.Windows.Forms.Panel panelControl;
        private System.Windows.Forms.Panel panelShow;
        private System.Windows.Forms.Label labelWelcom;
        private System.Windows.Forms.PictureBox pictureBox1;
        private System.Windows.Forms.Button buttonSearch;
        private System.Windows.Forms.DataGridView myData;
        private System.Windows.Forms.DataGridViewTextBoxColumn id;
        private System.Windows.Forms.DataGridViewTextBoxColumn suserName;
        private System.Windows.Forms.DataGridViewTextBoxColumn fileName;
        private System.Windows.Forms.DataGridViewTextBoxColumn copies;
        private System.Windows.Forms.DataGridViewTextBoxColumn doubleSides;
        private System.Windows.Forms.DataGridViewLinkColumn status;
        private System.Windows.Forms.MenuStrip menuStrip1;
        private System.Windows.Forms.ToolStripMenuItem 文件ToolStripMenuItem;
        private System.Windows.Forms.ToolStripMenuItem 打开下载ToolStripMenuItem;
        private System.Windows.Forms.ToolStripMenuItem 退出ToolStripMenuItem;
        private System.Windows.Forms.ToolStripMenuItem 帮助ToolStripMenuItem;
        private System.Windows.Forms.ToolStripMenuItem 版本ToolStripMenuItem;
        private System.Windows.Forms.TextBox textStudent;
        private System.Windows.Forms.Label label1;
        private System.Windows.Forms.ToolStripMenuItem 刷新下载ToolStripMenuItem;
        private System.Windows.Forms.Button buttonRefresh;
        private System.Windows.Forms.TextBox textDownload;
        private System.Windows.Forms.Label label2;
        private System.Windows.Forms.ToolTip toolTip1;
        private System.Windows.Forms.ToolStripMenuItem 测试jsonLINQToolStripMenuItem;
    }
}