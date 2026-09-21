using System;
using System.Diagnostics;
using System.Drawing;
using System.IO;
using System.Net;
using System.Net.Sockets;
using System.Threading;
using System.Windows.Forms;

namespace SIPCE
{
    static class Program
    {
        static Process phpProc = null;
        static Process syncProc = null;
        static NotifyIcon tray = null;
        static private int port = 8899;
        static private string baseUrl = "http://127.0.0.1:8899";
        static private string appDir = "";
        static private string phpExe = "";
        static private string logFile = "";
        static private string localDb = "";

        [STAThread]
        static void Main(string[] args)
        {
            bool noBrowser = false;
            foreach (string a in args) { if (a == "--nobrowser") noBrowser = true; }

            string here = Path.GetDirectoryName(Application.ExecutablePath);
            appDir = Path.Combine(here, "app");
            phpExe = Path.Combine(here, "php", "php.exe");
            logFile = Path.Combine(here, "app", "storage", "logs", "desktop.log");
            localDb = Path.Combine(appDir, "database", "database.sqlite");

            bool isFirst = false;
            using (Mutex m = new Mutex(true, "SIPCE_Desktop_Launcher", out isFirst))
            {
                if (!isFirst)
                {
                    // Ya hay una instancia: solo abrir el navegador
                    OpenBrowser();
                    return;
                }

                if (!File.Exists(phpExe))
                {
                    MessageBox.Show("No se encontro PHP en:\n" + phpExe + "\n\nLa carpeta de instalacion esta incompleta.", "SIPCE Desktop", MessageBoxButtons.OK, MessageBoxIcon.Error);
                    return;
                }

                Application.EnableVisualStyles();
                Application.SetCompatibleTextRenderingDefault(false);

                bool ok = RunInit() && StartServer();
                SetupTray();

                if (ok)
                {
                    StartSyncLoop();
                    if (!noBrowser) OpenBrowser();
                    Log("SIPCE listo en " + baseUrl);
                }
                else
                {
                    tray.Visible = true;
                    tray.BalloonTipTitle = "SIPCE Desktop";
                    tray.BalloonTipText = "No se pudo iniciar el servidor. Abre SIPCE.exe para reintentar o revisa el log.";
                    tray.ShowBalloonTip(6000);
                    Log("Fallo al iniciar el servidor");
                }

                Application.Run();
            }
        }

        static void SetupTray()
        {
            ContextMenuStrip menu = new ContextMenuStrip();
            menu.Items.Add("Abrir SIPCE", null, (s, e) => OpenBrowser());
            menu.Items.Add(new ToolStripSeparator());
            menu.Items.Add("Salir", null, (s, e) => { Shutdown(); Application.Exit(); });

            tray = new NotifyIcon();
            tray.Icon = SystemIcons.Application;
            tray.Text = "SIPCE - " + baseUrl;
            tray.Visible = true;
            tray.ContextMenuStrip = menu;
            tray.DoubleClick += (s, e) => OpenBrowser();
        }

        static bool RunInit()
        {
            if (!File.Exists(localDb))
            {
                Log("No existe la BD local, inicializando (artisan sipce:init-local)...");
                ProcessStartInfo psi = new ProcessStartInfo();
                psi.FileName = phpExe;
                psi.WorkingDirectory = appDir;
                psi.Arguments = "artisan sipce:init-local --no-interaction";
                psi.UseShellExecute = false;
                psi.CreateNoWindow = true;
                psi.RedirectStandardOutput = true;
                psi.RedirectStandardError = true;
                try
                {
                    Process p = Process.Start(psi);
                    string outp = p.StandardOutput.ReadToEnd();
                    string err = p.StandardError.ReadToEnd();
                    if (!p.WaitForExit(180000)) { p.Kill(); Log("init-local expiro (180s)"); return false; }
                    Log("init-local exit=" + p.ExitCode + (outp.Length > 0 ? " | " + outp.Trim() : ""));
                    if (p.ExitCode != 0) { Log("init-local stderr: " + err); return false; }
                    p.Dispose();
                    return File.Exists(localDb);
                }
                catch (Exception ex) { Log("init-local error: " + ex.Message); return false; }
            }
            Log("BD local ya existe: " + localDb);
            return true;
        }

        static void StartSyncLoop()
        {
            ProcessStartInfo psi = new ProcessStartInfo();
            psi.FileName = phpExe;
            psi.WorkingDirectory = appDir;
            psi.Arguments = "artisan sipce:sync --loop";
            psi.UseShellExecute = false;
            psi.CreateNoWindow = true;
            psi.RedirectStandardOutput = true;
            psi.RedirectStandardError = true;
            try
            {
                syncProc = Process.Start(psi);
                syncProc.OutputDataReceived += (s, e) => { };
                syncProc.ErrorDataReceived += (s, e) => { };
                syncProc.BeginOutputReadLine();
                syncProc.BeginErrorReadLine();
                Log("Sync loop iniciado PID=" + syncProc.Id);
            }
            catch (Exception ex) { Log("Sync loop error: " + ex.Message); }
        }

        static bool StartServer()
        {
            if (!Directory.Exists(appDir)) { Log("No existe appDir: " + appDir); return false; }
            // Verificar que el puerto este libre
            if (IsPortInUse(port))
            {
                // Ya hay algo corriendo: usar la app
                Log("Puerto " + port + " ocupado, intentando usar la instancia ya activa");
                return HttpAlive(baseUrl);
            }

            ProcessStartInfo psi = new ProcessStartInfo();
            psi.FileName = phpExe;
            psi.WorkingDirectory = Path.Combine(appDir, "public");
            psi.Arguments = "-S 127.0.0.1:" + port + " \"" +
                Path.Combine(appDir, "public", "server.php") + "\"";
            psi.UseShellExecute = false;
            psi.CreateNoWindow = true;
            // Redirigir a pipes consumidos de forma asincrona: da handles validos
            // a php (sin consola) y evita el bloqueo por llenado del pipe.
            psi.RedirectStandardOutput = true;
            psi.RedirectStandardError = true;

            try
            {
                phpProc = Process.Start(psi);
                phpProc.OutputDataReceived += (s, e) => { };
                phpProc.ErrorDataReceived += (s, e) => { };
                phpProc.BeginOutputReadLine();
                phpProc.BeginErrorReadLine();
                Log("Servidor iniciado PID=" + phpProc.Id + " en " + baseUrl);
            }
            catch (Exception ex)
            {
                Log("Error al iniciar PHP: " + ex.Message);
                return false;
            }

            // Esperar a que responda (hasta 60s)
            for (int i = 0; i < 60; i++)
            {
                Thread.Sleep(1000);
                if (HttpAlive(baseUrl)) return true;
                if (phpProc.HasExited)
                {
                    Log("PHP termino con codigo " + phpProc.ExitCode);
                    return false;
                }
            }
            return HttpAlive(baseUrl);
        }

        static bool HttpAlive(string url)
        {
            try
            {
                HttpWebRequest req = (HttpWebRequest)WebRequest.Create(url + "/up");
                req.Timeout = 2000;
                using (HttpWebResponse res = (HttpWebResponse)req.GetResponse())
                {
                    return (int)res.StatusCode < 500;
                }
            }
            catch { return false; }
        }

        static bool IsPortInUse(int p)
        {
            try
            {
                TcpListener tl = new TcpListener(IPAddress.Loopback, p);
                tl.Start();
                tl.Stop();
                return false;
            }
            catch { return true; }
        }

        static void OpenBrowser()
        {
            try { Process.Start(baseUrl + "/login"); }
            catch (Exception ex) { Log("No se pudo abrir el navegador: " + ex.Message); }
        }

        static void Shutdown()
        {
            if (syncProc != null && !syncProc.HasExited)
            {
                try { syncProc.Kill(); Log("Sync loop detenido"); } catch { }
                syncProc.Dispose();
                syncProc = null;
            }
            if (phpProc != null && !phpProc.HasExited)
            {
                try { phpProc.Kill(); Log("Servidor detenido"); } catch { }
                phpProc.Dispose();
                phpProc = null;
            }
        }

        static void Log(string msg)
        {
            try
            {
                string dir = Path.GetDirectoryName(logFile);
                if (!Directory.Exists(dir)) Directory.CreateDirectory(dir);
                File.AppendAllText(logFile, DateTime.Now.ToString("yyyy-MM-dd HH:mm:ss") + "  " + msg + "\r\n");
            }
            catch { }
        }
    }
}