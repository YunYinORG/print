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
            System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(NKprint_download));
            this.panelControl = new System.Windows.Forms.Panel();
            this.buttonRefresh = new System.Windows.Forms.Button();
            this.buttonExit = new System.Windows.Forms.Button();
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
            this.button1 = new System.Windows.Forms.Button();
            this.panelControl.SuspendLayout();
            ((System.ComponentModel.ISupportInitialize)(this.pictureBox1)).BeginInit();
            this.panelShow.SuspendLayout();
            ((System.ComponentModel.ISupportInitialize)(this.myData)).BeginInit();
            this.SuspendLayout();
            // 
            // panelControl
            // 
            this.panelControl.BackColor = System.Drawing.SystemColors.ControlDark;
            this.panelControl.Controls.Add(this.button1);
            this.panelControl.Controls.Add(this.buttonRefresh);
            this.panelControl.Controls.Add(this.buttonExit);
            this.panelControl.Controls.Add(this.pictureBox1);
            this.panelControl.Controls.Add(this.labelWelcom);
            this.panelControl.Location = new System.Drawing.Point(3, 1);
            this.panelControl.Name = "panelControl";
            this.panelControl.Size = new System.Drawing.Size(193, 327);
            this.panelControl.TabIndex = 1;
            // 
            // buttonRefresh
            // 
            this.buttonRefresh.Font = new System.Drawing.Font("宋体", 10.5F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(134)));
            this.buttonRefresh.ForeColor = System.Drawing.SystemColors.ActiveCaption;
            this.buttonRefresh.Location = new System.Drawing.Point(29, 170);
            this.buttonRefresh.Name = "buttonRefresh";
            this.buttonRefresh.Size = new System.Drawing.Size(125, 46);
            this.buttonRefresh.TabIndex = 3;
            this.buttonRefresh.Text = "刷新下载列表";
            this.buttonRefresh.UseVisualStyleBackColor = true;
            this.buttonRefresh.Click += new System.EventHandler(this.buttonRefresh_Click);
            // 
            // buttonExit
            // 
            this.buttonExit.Font = new System.Drawing.Font("宋体", 10.5F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(134)));
            this.buttonExit.ForeColor = System.Drawing.SystemColors.ActiveCaption;
            this.buttonExit.Location = new System.Drawing.Point(29, 100);
            this.buttonExit.Name = "buttonExit";
            this.buttonExit.Size = new System.Drawing.Size(125, 46);
            this.buttonExit.TabIndex = 2;
            this.buttonExit.Text = "退出当前登陆";
            this.buttonExit.UseVisualStyleBackColor = true;
            this.buttonExit.Click += new System.EventHandler(this.buttonExit_Click);
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
            this.labelWelcom.Location = new System.Drawing.Point(91, 11);
            this.labelWelcom.Name = "labelWelcom";
            this.labelWelcom.Size = new System.Drawing.Size(94, 67);
            this.labelWelcom.TabIndex = 0;
            this.labelWelcom.Text = "你好，欢迎登陆";
            this.labelWelcom.Click += new System.EventHandler(this.labelWelcom_Click);
            // 
            // panelShow
            // 
            this.panelShow.Controls.Add(this.myData);
            this.panelShow.Location = new System.Drawing.Point(202, 1);
            this.panelShow.Name = "panelShow";
            this.panelShow.Size = new System.Drawing.Size(653, 328);
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
            this.myData.RowTemplate.Height = 23;
            this.myData.Size = new System.Drawing.Size(653, 328);
            this.myData.TabIndex = 0;
            this.myData.CellContentClick += new System.Windows.Forms.DataGridViewCellEventHandler(this.myData_CellContentClick);
            // 
            // id
            // 
            this.id.HeaderText = "id";
            this.id.Name = "id";
            // 
            // suserName
            // 
            this.suserName.HeaderText = "用户名";
            this.suserName.Name = "suserName";
            // 
            // fileName
            // 
            this.fileName.HeaderText = "文件名";
            this.fileName.Name = "fileName";
            // 
            // copies
            // 
            this.copies.HeaderText = "份数";
            this.copies.Name = "copies";
            // 
            // doubleSides
            // 
            this.doubleSides.HeaderText = "单双面";
            this.doubleSides.Name = "doubleSides";
            // 
            // status
            // 
            this.status.HeaderText = "状态";
            this.status.Name = "status";
            // 
            // button1
            // 
            this.button1.Font = new System.Drawing.Font("宋体", 10.5F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(134)));
            this.button1.ForeColor = System.Drawing.SystemColors.ActiveCaption;
            this.button1.Location = new System.Drawing.Point(29, 239);
            this.button1.Name = "button1";
            this.button1.Size = new System.Drawing.Size(125, 46);
            this.button1.TabIndex = 4;
            this.button1.Text = "打开下载文件";
            this.button1.UseVisualStyleBackColor = true;
            this.button1.Click += new System.EventHandler(this.button1_Click);
            // 
            // NKprint_download
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 12F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(857, 330);
            this.Controls.Add(this.panelShow);
            this.Controls.Add(this.panelControl);
            this.Icon = ((System.Drawing.Icon)(resources.GetObject("$this.Icon")));
            this.IsMdiContainer = true;
            this.Name = "NKprint_download";
            this.Text = "云印南开打印管理";
            this.FormClosed += new System.Windows.Forms.FormClosedEventHandler(this.NKprint_download_FormClosed);
            this.Load += new System.EventHandler(this.NKprint_download_Load);
            this.panelControl.ResumeLayout(false);
            ((System.ComponentModel.ISupportInitialize)(this.pictureBox1)).EndInit();
            this.panelShow.ResumeLayout(false);
            ((System.ComponentModel.ISupportInitialize)(this.myData)).EndInit();
            this.ResumeLayout(false);

        }

        #endregion

        private System.Windows.Forms.Panel panelControl;
        private System.Windows.Forms.Panel panelShow;
        private System.Windows.Forms.Label labelWelcom;
        private System.Windows.Forms.PictureBox pictureBox1;
        private System.Windows.Forms.Button buttonExit;
        private System.Windows.Forms.Button buttonRefresh;
        private System.Windows.Forms.DataGridView myData;
        private System.Windows.Forms.DataGridViewTextBoxColumn id;
        private System.Windows.Forms.DataGridViewTextBoxColumn suserName;
        private System.Windows.Forms.DataGridViewTextBoxColumn fileName;
        private System.Windows.Forms.DataGridViewTextBoxColumn copies;
        private System.Windows.Forms.DataGridViewTextBoxColumn doubleSides;
        private System.Windows.Forms.DataGridViewLinkColumn status;
        private System.Windows.Forms.Button button1;
    }
}