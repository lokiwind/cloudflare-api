using OpenQA.Selenium;
using OpenQA.Selenium.Chrome;
using OpenQA.Selenium.Support.UI;
using System;
using System.Windows.Forms;
using System.Threading;
using System.Linq;

namespace seleniumautomation
{
    public partial class Form1 : Form
    {
        private IWebDriver driver;
        public Form1()
        {
            InitializeComponent();
            var button = new Button();
            button.Text = "Open ";
            button.Click += Button_Click;
            Controls.Add(button);

            var driverService = ChromeDriverService.CreateDefaultService(@"C:\selenium\chromedriver.exe");
            driver = new ChromeDriver(driverService);
        }

        private void Button_Click(object sender, EventArgs e)
        {
            driver.Navigate().GoToUrl("https://www.just-eat.ch/");
            Thread.Sleep(10000); 
            IWebElement buttonElement = driver.FindElement(By.ClassName("_908LZ"));
            buttonElement.Click(); 
        }
        private void Form1_FormClosing(object sender, FormClosingEventArgs e)
        {
            driver.Quit(); 
        }
    }
}
