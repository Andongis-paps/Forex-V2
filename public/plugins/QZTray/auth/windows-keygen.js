/**
 * @author Tres Finocchiaro
 *
 * Copyright (C) 2015 Tres Finocchiaro, QZ Industries
 */

/******************************************************************************
 *                        Windows KeyGen Utility                              *
 ******************************************************************************
 *  Description:                                                              *
 *    Utility to create a private key and install its respective public       *
 *    certificate to the system.  When run in "uninstall" mode, the public    *
 *    certificate is removed based on matched publisher/vendor information.   *
 *                                                                            *
 *  INSTALL:                                                                  *
 *    1. Creates a self-signed Java Keystore for jetty wss://localhost        *
 *    2. Exports public certificate from Java Keystore                        *
 *    3. Imports into Windows trusted cert store                              *
 *    4. Imports into Firefox web browser (if installed)                      *
 *                                                                            *
 *  UNINSTALL                                                                 *
 *    1. Deletes certificate from Windows trusted cert store                  *
 *    2. Deletes certificate from Firefox web browser (if installed)          *
 *                                                                            *
 *  Depends:                                                                  *
 *    keytool.exe (distributed with jre: http://java.com)                     *
 *                                                                            *
 *  Usage:                                                                    *
 *    cscript //NoLogo windows-keygen.js "C:\Program Files\QZ Tray" install   *
 *    cscript //NoLogo windows-keygen.js "C:\Program Files\QZ Tray" uninstall *
 *                                                                            *
 *****************************************************************************/

var shell = new ActiveXObject("WScript.shell");
var fso = new ActiveXObject("Scripting.FileSystemObject");
var newLine = "\r\n";

// Uses passed-in parameter as install location.  Will fallback to registry if not provided.
var qzInstall = getArg(0, getRegValue("HKLM\\Software\\QZ Tray\\"));
var installMode = getArg(1, "install");

if (installMode == "install") {
    var javaKey, jreHome, keyTool, keyStore, password, derCert, firefoxInstall;
    if (createJavaKeystore()) {
        try { installWindowsCertificate(); }
        catch (err) { installWindowsXPCertificate(); }
        if (hasFirefoxConflict()) {
            alert("WARNING: QZ Tray installation would conflict with an existing Firefox AutoConfig rule.\n\n" +
                "Please notify your administrator of this warning.\n\n" +
                "The installer will continue, but QZ Tray will not function with Firefox until this conflict is resolved.",
                "Firefox AutoConfig Warning");
        } else {
            if (firefoxInstall) {
                installFirefoxCertificate();
            }
        }
    }
} else {
    var firefoxInstall;
    try { deleteWindowsCertificate(); }
    catch (err) { deleteWindowsXPCertificate(); }
    deleteFirefoxCertificate();
}

WScript.Quit(0);

/**
 * Deletes a file
 */
function deleteFile(filePath) {
	if (fso.FileExists(filePath)) {
		try {
			fso.DeleteFile(filePath);
		} catch (err) {
			die("Unable to delete " + filePath);
		}
	}
}

/**
 * Generates a random string to be used as a password
 */
function pw() {
    var text = "";
    var chars = "abcdefghijklmnopqrstuvwxyz0123456789";
    for( var i=0; i < parseInt("10"); i++ ) {
        text += chars.charAt(Math.floor(Math.random() * chars.length));
	}
    return text;
}

/**
 * Reads a registry value, taking 32-bit/64-bit architecture into consideration
 */
function getRegValue(path) {	
	// If 64-bit OS, try 32-bit registry first
	var arch = "";
	if (shell.ExpandEnvironmentStrings("ProgramFiles(x86)")) {
		path = path.replace("\\Software\\", "\\Software\\Wow6432Node\\");
	}
	
	var regValue = "";
	try {
		regValue = shell.RegRead(path);
	} catch (err) {
		try {
            // Fall back to 64-bit registry
            path = path.replace("\\Software\\Wow6432Node\\", "\\Software\\");
            regValue = shell.RegRead(path);
		} catch (err) {}
	}
	return regValue;
}

/**
 * Displays a message regarding whether or not a file exists
 */
function verifyExists(path, msg) {
    debug(" - " + (fso.FileExists(path) ? "[success] " : "[failed] ") + msg);
}

/**
 * Displays a message regarding whether or not a command succeeded
 */
function verifyExec(cmd, msg) {
    debug(" - " + (shell.Run(cmd, 0, true) == 0 ? "[success] " : "[failed] ")  + msg);
}

/**
 * Replaces "!install" with proper location, usually "C:\Program Files\", fixes forward slashes
 */
function fixPath(append) {
    return append.replace("!install", qzInstall).replace(/\//g, "\\");
}

/**
 * Displays an error message and exits the script
 * @param msg
 */
function die(msg, status) {
    WScript.Echo("ERROR: " + msg);
    status = status ? status : -1;
    WScript.Quit(status);
}

/**
 * Displays a status message
 * @param msg
 */
function debug(msg) {
    WScript.Echo(msg);
}

/*
 * Reads in a text file, expands the specified named variable replacements and writes it back out.
 */
function writeParsedConfig(inPath, outPath, replacements) {
    var inFile = fso.OpenTextFile(inPath, 1, true);     // 1 = ForReading
    var outFile = fso.OpenTextFile(outPath, 2, true);   // 2 = ForWriting

    while(!inFile.AtEndOfStream) {
        line = inFile.ReadLine()

        // Process all variable replacements
        for (var key in replacements) {
            // Escape leading "$" prior to building regex
            var varName = (key.indexOf("$") == 0 ? "\\" + key : key);
            var re = new RegExp(varName, 'g')
            line = line.replace(re, replacements[key]);
        }
        outFile.WriteLine(line);
    }
    inFile.close();
    outFile.close();
}

/*
 * Reads in a X509 certificate, stripping BEGIN, END and NEWLINE string
 */
function readPlainCert(certPath) {
    var certFile = fso.OpenTextFile(certPath, 1, true);
    var certData = "";
    while (!certFile.AtEndOfStream) { certData += strip(certFile.ReadLine()); }
    certFile.close();
    return certData;
}

/*
 * Strips non-base64 data (i.e RFC X509 --START, --END) from a string
 */
function strip(line) {
    var X509 = ["-----BEGIN CERTIFICATE-----", "-----END CERTIFICATE-----", "\r", "\n"];
    for (var i in X509) { line = line.replace(new RegExp(X509[i], 'g'), ''); }
    return line;
}

/*
 * Creates the Java Keystore
 */
function createJavaKeystore() {
    javaKey = "HKLM\\Software\\JavaSoft\\Java Runtime Environment\\";
    jreHome = getRegValue(javaKey + getRegValue(javaKey + "CurrentVersion") + "\\JavaHome");
    keyTool = jreHome + "\\bin\\keytool.exe";
    derCert = fixPath("!install/auth/qz-tray.crt");

    if (jreHome == "") {
        die("Can't find JavaHome.  Secure websockets will not work.", "2");
    }

    if (qzInstall == "") {
        die("Can't find QZ Tray installation path. Secure websockets will not work.", "4");
    }


    keyStore = fixPath("!install/auth/qz-tray.jks");
    password = pw();    // random password hash

    var makeKeyStore = "\"keytool\" -genkey -noprompt -alias qz-tray -keyalg RSA -keysize 2048 -dname \"CN=localhost, EMAILADDRESS=support@qz.io, OU=QZ Industries\\, LLC, O=QZ Industries\\, LLC, L=Canastota, S=NY, C=US\" -validity 7305 -keystore \"!install/auth/qz-tray.jks\" -storepass !storepass -keypass !keypass"
        .replace("keytool", keyTool)
        .replace("!install/auth/qz-tray.jks", keyStore)
        .replace("!storepass", password)
        .replace("!keypass", password);

    deleteFile(keyStore);   // remove old, if exists
    debug("Creating keystore for secure websockets (this could take a minute)...");
    shell.Run(makeKeyStore, 0, true);
    verifyExists(keyStore, "Check keystore exists");

    var file = fso.OpenTextFile(fixPath("!install/qz-tray.properties"), 2, true);
    file.WriteLine("wss.alias=" + "qz-tray");
    file.WriteLine("wss.keystore=" + keyStore.replace(/\\/g, "\\\\"));
    file.WriteLine("wss.keypass=" + password);
    file.WriteLine("wss.storepass=" + password);
    file.Close();

    return true;
}

/*
 * Exports certificate to native format
 */
function installWindowsCertificate() {
    var makeDerCert = "\"keytool\" -exportcert -alias qz-tray -keystore \"!install/auth/qz-tray.jks\" -storepass !storepass -keypass !keypass -file \"!install/auth/qz-tray.crt\" -rfc"
        .replace("keytool", keyTool)
        .replace("!install/auth/qz-tray.jks", keyStore)
        .replace("!storepass", password)
        .replace("!keypass", password)
        .replace("!install/auth/qz-tray.crt", derCert);

    deleteFile(derCert);    // remove old, if exists
    debug("Converting keystore to native certificate...");
    shell.Run(makeDerCert, 0, true);
    verifyExists(derCert, "Check certificate exists");

    debug("Installing native certificate for secure websockets...");
    var installDerCert = 'certutil.exe -addstore -f "Root" "' + derCert + '"';

    // Windows Vista or higher
    shell.Run(installDerCert, 0, true);
    debug(" - " + (findWindowsMatches("") ? "[success] " : "[failed] ")  + "Check certificate installed");
}

function installWindowsXPCertificate() {
    shell.Popup("Automatic certificate installation is not available for this platform.\n" +
        "For secure websockets to function properly:\n\n" +
        "     1.  Navigate to \"" + derCert + "\"\n" +
        "     2.  Click \"Install Certificate...\"\n" +
        "     3.  Click \"Place all certificates in the following store\"\n" +
        "     4.  Browse to \"Trusted Root Certificate Authorities\"\n" +
        "     5.  Click \"Finish\"\n" +
        "     6.  Click \"Yes\" on thumbprint Security Warning\n\n" +
        "Click OK to automatically launch the certificate import wizard now.\n", 0, "Warning - QZ Tray", 48);

    // Do not wrap quotes around derCert, or this next line will fail
    shell.Run("rundll32.exe cryptext.dll,CryptExtAddCER " + derCert, 1, true);
}

/*
 * Gets the Firefox installation path, stores it a global variable "firefoxInstall"
 */
function getFirefoxInstall() {
    //  Determine if Firefox is installed
    var firefoxKey = "HKLM\\Software\\Mozilla\\Mozilla Firefox";
    var firefoxVer = getRegValue(firefoxKey + "\\");
    if (!firefoxVer) {
        debug(" - [skipped] Firefox was not detected");
        return false;
    } else {
        debug(" - [success] Found Firefox " + firefoxVer);
    }

    // Determine full path to firefox.exe, i.e. "C:\Program Files (x86)\Mozilla Firefox\firefox.exe"
    firefoxInstall = getRegValue(firefoxKey + " " + firefoxVer + "\\bin\\PathToExe");

    return firefoxInstall;
}

/*
 * Iterates over the installed preferences file looking for a non-QZ Tray AutoConfig rule
 */
function hasFirefoxConflict() {
    if (!getFirefoxInstall()) { return false; }

    debug("Searching for Firefox AutoConfig conflicts...");
    // AutoConfig rule conflicts to search for
    var conflicts = ["general.config.filename"];

    // White-listed preference files, used for QZ Tray deployment
    var exceptions = ["firefox-prefs.js"];
    var folder = fso.GetFolder(firefoxInstall + "\\..\\defaults\\pref");
    var o = new Enumerator(folder.Files);
    for ( ; !o.atEnd(); o.moveNext()) {
        var whitelist = false;
        for (var i in exceptions) {
            if (exceptions[i] == o.item().Name) {
                debug(" - [skipping] QZ Tray config file: " + exceptions[i]);
                whitelist = true;
            }
        }
        if (!whitelist && parseFirefoxPref(o.item(), conflicts)) {
            return true;
        }
    }
    debug(" - [success] No conflicts found");
    return false;
}

/*
 * Reads a Firefox preference file for already existing AutoConfig rule conflicts
 * Conflicts suggest an enterprise-type deployment environment.
 * Returns true if a conflict exists.
 */
function parseFirefoxPref(file, conflicts) {
    var inFile = fso.OpenTextFile(file.Path, 1, true);     // 1 = ForReading
    var counter = 0;
    while(!inFile.AtEndOfStream) {
        var line = inFile.ReadLine()
        counter++;
        for (var i in conflicts) {
            // Check for both quote styles, 'foo.bar.name' and "foo.bar.name"
            if (line.indexOf("'" + conflicts[i] + "'") >= 0 ||
                line.indexOf('"' + conflicts[i] + '"') >= 0) {
                debug(" - [error] Conflict found in " + file.Name +
                    "\n\t Conflict on line " + counter + ": \"" + line + "\"");
                inFile.close();
                return true;
            }
        }
    }
    inFile.close();
    return false;
}


/*
 * Delete certificate for Mozilla Firefox browser, which utilizes its own cert database
 */
function deleteFirefoxCertificate() {
    if (!getFirefoxInstall()) { return; }

    debug("Removing from Firefox...");
    var firefoxCfg = firefoxInstall + "\\..\\firefox-config.cfg";

    // Variable replacements for Firefox config file
    var replacements = {
        "${certData}" : "",
        "${uninstall}" : "true"
    };

    // 1. readPlainCert() reads in certificate, stripping non-base64 content
    // 2. writeParsedConfig(...) reads, parses and writes config file in same folder as firefox.exe
    writeParsedConfig(fixPath("!install/auth/firefox/firefox-config.cfg"), firefoxCfg, replacements);
    verifyExists(firefoxCfg, "Check Firefox config exists");
}

/*
 * Install certificate for Mozilla Firefox browser, which utilizes its own cert database
 */
function installFirefoxCertificate() {
    debug("Registering with Firefox...");
    var firefoxCfg = firefoxInstall + "\\..\\firefox-config.cfg";

    // Variable replacements for Firefox config file
    var replacements = {
        "${certData}" : readPlainCert(derCert),
        "${uninstall}" : "false",
        "${timestamp}" : new Date().getTime()
    };

    // 1. readPlainCert() reads in certificate, stripping non-base64 content
    // 2. writeParsedConfig(...) reads, parses and writes config file in same folder as firefox.exe
    writeParsedConfig(fixPath("!install/auth/firefox/firefox-config.cfg"), firefoxCfg, replacements);
    verifyExists(firefoxCfg, "Check Firefox config exists");

    // Install the preference file tells Firefox to launches firefox-config.cfg each time it starts
    var firefoxPrefs = firefoxInstall + "\\..\\defaults\\pref\\firefox-prefs.js";
    fso.CopyFile(fixPath("!install/auth/firefox/firefox-prefs.js"), firefoxPrefs);
}

/*
 * Deletes windows certificates based on specific CN and OU values
 */
function deleteWindowsCertificate() {
    debug("Deleting old certificates...");
    var serialDelim = "||";
    var matches = findWindowsMatches(serialDelim);

    // If matches are found, delete them
    if (matches) {
        matches = matches.split(serialDelim);
        for (var i in matches) {
            if (matches[i]) {
                shell.Run('certutil.exe -delstore "Root" "' + matches[i] + '"', 1, true);
            }
        }

        // Verify removal
        matches = findWindowsMatches();
        if (matches) {
            debug(" - [failed] Some certificates not deleted");
            return false;
        } else {
            debug(" - [success] Certificate(s) removed");
        }
    } else {
        debug(" - [skipped] No matches found");
    }
    return true;
}

/*
 * Certutil isn't available on Windows XP, show manual instructions instead
 */
function deleteWindowsXPCertificate() {
    shell.Popup("Automatic certificate deletion is not available for this platform.\n" +
        "To completely remove unused certificates:\n\n" +
        "     1.  Manage computer certificates\n" +
        "     2.  Click \"Trusted Root Certificate Authorities...\"\n" +
        "     3.  Click \"Certificates\"\n" +
        "     4.  Browse to \"localhost, QZ Industries, LLC\"\n" +
        "     5.  Right Click, \"Delete\"\n" +
        "Click OK to automatically launch the certificate manager.\n", 0, "Warning - QZ Tray", 48);

    // Do not wrap quotes around derCert, or this next line will fail
    shell.Run("mmc.exe certmgr.msc", 1, true);
}


/*
 * Returns matching serial numbers delimited by two pipes, i.e "9876fedc||1234abcd"
 */
function findWindowsMatches(serialDelim) {
    var matches = "";
    var proc = shell.Exec('certutil.exe -store "Root"');
    var certData = "";
    while (!proc.StdOut.AtEndOfStream) {
        var line = proc.StdOut.ReadLine()
        if (trim(line) != "") {
            certData += line + newLine;
        } else {
            var serial = parseCertificateSerial(certData);
            if (serial && isVendorMatch(certData)) {
                matches += serial + serialDelim;
            }
            certData = "";
        }
    }
    return matches;
}

/*
 * Parses the supplied data for serialTag
 * If found, returns the serial number of the certificate, i.e. "89e301a9"
 */
function parseCertificateSerial(certData) {
    var serialTag = "Serial Number:";
    if (certData.indexOf(newLine) != -1) {
        var lines = certData.split(newLine);
        for (var i in lines) {
            var line = trim(lines[i]);
            if (line.indexOf(serialTag) == 0) {
                return trim(line.split(serialTag)[1]);
            }
        }
    }
    return false;
}

/*
 * Parses the supplied data for issuerTag
 * If found, parses the matched line for specific CN and OU values.
 * Returns true if found
 */
function isVendorMatch(certData) {
    var issuerTag = "Issuer:";
    if (certData.indexOf(newLine) != -1) {
        var lines = certData.split(newLine);
        for (var i in lines) {
            var line = trim(lines[i]);
            if (line.indexOf(issuerTag) == 0) {
                if (line.indexOf("OU=QZ Industries, LLC") != -1 && line.indexOf("CN=localhost") != -1) {
                    return true;
                }
            }
        }
    }
    return false;
}

/*
 * Functional equivalent of foo.trim()
 */
function trim(val) {
    return val.replace(/^\s+/,'').replace(/\s+$/,'');
}

/*
 * Gets then nth argument passed into this script
 * Returns defaultVal if argument wasn't found
 */
function getArg(index, defaultVal) {
    if (index >= WScript.Arguments.length || trim(WScript.Arguments(index)) == "") {
        return defaultVal;
    }
    return WScript.Arguments(index);
}

/*
 * Mimic an alert dialog, used only for OK_ONLY + WARNING (0 + 48)
 */
function alert(message, title) {
    new ActiveXObject("WScript.Shell").Popup(message, 0, title == null ? "Warning" : title, 48);
}