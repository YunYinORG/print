namespace NKprint
{
    partial class NKprint_login
    {
        /// <summary>
        /// 必需的设计器变量。
        /// </summary>
        private System.ComponentModel.IContainer components = null;

        /// <summary>
        /// 清理所有正在使用的资源。
        /// </summary>
        /// <param name="disposing">如果应释放托管资源，为 true；否则为 false。</param>
        protected override void Dispose(bool disposing)
        {
            if (disposing && (components != null))
            {
                components.Dispose();
            }
            base.Dispose(disposing);
        }

        #region Windows 窗体设计器生成的代码

        /// <summary>
        /// 设计器支持所需的方法 - 不要
        /// 使用代码编辑器修改此方法的内容。
        /// </summary>
        private void InitializeComponent()
        {
            System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(NKprint_login));
            this.panelLogin = new System.Windows.Forms.Panel();
            this.labelWait = new System.Windows.Forms.Label();
            this.checkRemember = new System.Windows.Forms.CheckBox();
            this.labelError = new System.Windows.Forms.Label();
            this.labelLogin = new System.Windows.Forms.Label();
            this.pictureBox1 = new System.Windows.Forms.PictureBox();
            this.buttonRest = new System.Windows.Forms.Button();
            this.printerPassword = new System.Windows.Forms.TextBox();
            this.printerAccount = new System.Windows.Forms.TextBox();
            this.labelPassword = new System.Windows.Forms.Label();
            this.labelAccount = new System.Windows.Forms.Label();
            this.buttonLogin = new System.Windows.Forms.Button();
            this.panelLogin.SuspendLayout();
            ((System.ComponentModel.ISupportInitialize)(this.pictureBox1)).BeginInit();
            this.SuspendLayout();
            // 
            // panelLogin
            // 
            this.panelLogin.BackColor = System.Drawing.SystemColors.InactiveCaption;
            this.panelLogin.Controls.Add(this.labelWait);
            this.panelLogin.Controls.Add(this.checkRemember);
            this.panelLogin.Controls.Add(this.labelError);
            this.panelLogin.Controls.Add(this.labelLogin);
            this.panelLogin.Controls.Add(this.pictureBox1);
            this.panelLogin.Controls.Add(this.buttonRest);
            this.panelLogin.Controls.Add(this.printerPassword);
            this.panelLogin.Controls.Add(this.printerAccount);
            this.panelLogin.Controls.Add(this.labelPassword);
            this.panelLogin.Controls.Add(this.labelAccount);
            this.panelLogin.Controls.Add(this.buttonLogin);
            this.panelLogin.Location = new System.Drawing.Point(0, -3);
            this.panelLogin.Name = "panelLogin";
            this.panelLogin.Size = new System.Drawing.Size(541, 292);
            this.panelLogin.TabIndex = 1;
            // 
            // labelWait
            // 
            this.labelWait.Font = new System.Drawing.Font("宋体", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(134)));
            this.labelWait.ForeColor = System.Drawing.Color.Red;
            this.labelWait.Location = new System.Drawing.Point(303, 250);
            this.labelWait.Name = "labelWait";
            this.labelWait.Size = new System.Drawing.Size(187, 23);
            this.labelWait.TabIndex = 11;
            this.labelWait.Text = "正在登陆请等待";
            this.labelWait.TextAlign = System.Drawing.ContentAlignment.MiddleCenter;
            this.labelWait.Visible = false;
            // 
            // checkRemember
            // 
            this.checkRemember.AutoSize = true;
            this.checkRemember.Checked = true;
            this.checkRemember.CheckState = System.Windows.Forms.CheckState.Checked;
            this.checkRemember.Font = new System.Drawing.Font("宋体", 7.5F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(134)));
            this.checkRemember.Location = new System.Drawing.Point(477, 163);
            this.checkRemember.Name = "checkRemember";
            this.checkRemember.Size = new System.Drawing.Size(64, 14);
            this.checkRemember.TabIndex = 10;
            this.checkRemember.Text = "记住密码";
            this.checkRemember.UseVisualStyleBackColor = true;
            // 
            // labelError
            // 
            this.labelError.Font = new System.Drawing.Font("宋体", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(134)));
            this.labelError.ForeColor = System.Drawing.Color.Red;
            this.labelError.Location = new System.Drawing.Point(313, 189);
            this.labelError.Name = "labelError";
            this.labelError.Size = new System.Drawing.Size(187, 23);
            this.labelError.TabIndex = 9;
            this.labelError.Text = "请输入登录信息！";
            this.labelError.TextAlign = System.Drawing.ContentAlignment.MiddleCenter;
            this.labelError.Visible = false;
            // 
            // labelLogin
            // 
            this.labelLogin.AccessibleRole = System.Windows.Forms.AccessibleRole.TitleBar;
            this.labelLogin.BackColor = System.Drawing.SystemColors.InactiveCaptionText;
            this.labelLogin.Font = new System.Drawing.Font("宋体", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(134)));
            this.labelLogin.ForeColor = System.Drawing.SystemColors.Highlight;
            this.labelLogin.Location = new System.Drawing.Point(282, 29);
            this.labelLogin.Name = "labelLogin";
            this.labelLogin.Size = new System.Drawing.Size(232, 65);
            this.labelLogin.TabIndex = 8;
            this.labelLogin.Text = "南开大学打印店客户端";
            this.labelLogin.TextAlign = System.Drawing.ContentAlignment.MiddleCenter;
            // 
            // pictureBox1
            // 
            this.pictureBox1.Image = ((System.Drawing.Image)(resources.GetObject("pictureBox1.Image")));
            this.pictureBox1.Location = new System.Drawing.Point(12, 29);
            this.pictureBox1.Name = "pictureBox1";
            this.pictureBox1.Size = new System.Drawing.Size(261, 234);
            this.pictureBox1.TabIndex = 7;
            this.pictureBox1.TabStop = false;
            // 
            // buttonRest
            // 
            this.buttonRest.BackColor = System.Drawing.SystemColors.ControlDark;
            this.buttonRest.ForeColor = System.Drawing.SystemColors.ActiveCaption;
            this.buttonRest.Location = new System.Drawing.Point(409, 224);
            this.buttonRest.Name = "buttonRest";
            this.buttonRest.Size = new System.Drawing.Size(75, 23);
            this.buttonRest.TabIndex = 6;
            this.buttonRest.Text = "重置";
            this.buttonRest.UseVisualStyleBackColor = false;
            this.buttonRest.Click += new System.EventHandler(this.buttonRest_Click);
            // 
            // printerPassword
            // 
            this.printerPassword.Location = new System.Drawing.Point(358, 156);
            this.printerPassword.Name = "printerPassword";
            this.printerPassword.PasswordChar = '*';
            this.printerPassword.Size = new System.Drawing.Size(120, 21);
            this.printerPassword.TabIndex = 5;
            // 
            // printerAccount
            // 
            this.printerAccount.Location = new System.Drawing.Point(358, 107);
            this.printerAccount.Name = "printerAccount";
            this.printerAccount.Size = new System.Drawing.Size(120, 21);
            this.printerAccount.TabIndex = 4;
            // 
            // labelPassword
            // 
            this.labelPassword.Font = new System.Drawing.Font("宋体", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(134)));
            this.labelPassword.ForeColor = System.Drawing.SystemColors.Highlight;
            this.labelPassword.Location = new System.Drawing.Point(296, 155);
            this.labelPassword.Name = "labelPassword";
            this.labelPassword.Size = new System.Drawing.Size(61, 23);
            this.labelPassword.TabIndex = 3;
            this.labelPassword.Text = "密码：";
            // 
            // labelAccount
            // 
            this.labelAccount.Font = new System.Drawing.Font("宋体", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(134)));
            this.labelAccount.ForeColor = System.Drawing.SystemColors.Highlight;
            this.labelAccount.Location = new System.Drawing.Point(296, 111);
            this.labelAccount.Name = "labelAccount";
            this.labelAccount.Size = new System.Drawing.Size(61, 23);
            this.labelAccount.TabIndex = 2;
            this.labelAccount.Text = "账号：";
            // 
            // buttonLogin
            // 
            this.buttonLogin.BackColor = System.Drawing.SystemColors.ControlDark;
            this.buttonLogin.ForeColor = System.Drawing.SystemColors.ActiveCaption;
            this.buttonLogin.Location = new System.Drawing.Point(300, 224);
            this.buttonLogin.Name = "buttonLogin";
            this.buttonLogin.Size = new System.Drawing.Size(75, 23);
            this.buttonLogin.TabIndex = 0;
            this.buttonLogin.Tag = "登陆";
            this.buttonLogin.Text = "登录";
            this.buttonLogin.UseVisualStyleBackColor = false;
            this.buttonLogin.Click += new System.EventHandler(this.buttonLogin_Click);
            // 
            // NKprint_login
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 12F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(540, 289);
            this.Controls.Add(this.panelLogin);
            this.FormBorderStyle = System.Windows.Forms.FormBorderStyle.FixedSingle;
            this.Icon = ((System.Drawing.Icon)(resources.GetObject("$this.Icon")));
            this.Name = "NKprint_login";
            this.Text = "云印南开登录窗";
            this.Load += new System.EventHandler(this.NKprint_login_Load);
            this.panelLogin.ResumeLayout(false);
            this.panelLogin.PerformLayout();
            ((System.ComponentModel.ISupportInitialize)(this.pictureBox1)).EndInit();
            this.ResumeLayout(false);

        }

        #endregion

        private System.Windows.Forms.Panel panelLogin;
        private System.Windows.Forms.Button buttonRest;
        private System.Windows.Forms.TextBox printerPassword;
        private System.Windows.Forms.TextBox printerAccount;
        private System.Windows.Forms.Label labelPassword;
        private System.Windows.Forms.Label labelAccount;
        private System.Windows.Forms.Button buttonLogin;
        private System.Windows.Forms.Label labelLogin;
        private System.Windows.Forms.PictureBox pictureBox1;
        private System.Windows.Forms.Label labelError;
        private System.Windows.Forms.CheckBox checkRemember;
        private System.Windows.Forms.Label labelWait;
    }
}

