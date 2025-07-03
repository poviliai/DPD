# DPD Shipping Module - Developer Guide

This document describes the standard development workflow for working with the DPD Shipping Module for OpenCart 4 using GitHub and FTP deployment.

---

## 🛠️ Project Location

* Local project folder: `C:/Users/vpovi/DPD/`
* GitHub repository: [https://github.com/poviliai/DPD](https://github.com/poviliai/DPD)
* FTP server: `hosting2.tn-rechenzentrum1.de`

  * FTP user: `shipping-module-dpd.hosti`
  * FTP password: `********` *(stored securely in ftp-simple)*
  * Remote path: `/httpdocs/`

---

## 🔁 Daily Development Workflow

### 1. Edit code locally in VS Code

* Files located in `C:/Users/vpovi/DPD`
* Example: `admin/controller/extension/shipping/dpd.php`

### 2. Save changes

* Use `Ctrl + S` to save
* If `ftp-simple` is configured with `autosave: true`, the file is immediately uploaded to the server

### 3. Commit changes to Git

In terminal:

```bash
git add .
git commit -m "Your commit message"
```

### 4. Push to GitHub

```bash
git push origin main
```

---

## 🌐 FTP Configuration (ftp-simple)

Stored in `.vscode/ftp-simple.json`:

```json
{
  "name": "DPD Server",
  "host": "hosting2.tn-rechenzentrum1.de",
  "port": 21,
  "type": "ftp",
  "username": "shipping-module-dpd.hosti",
  "password": "********",
  "path": "/httpdocs",
  "autosave": true,
  "confirm": true,
  "localPath": "C:/Users/vpovi/DPD"
}
```

---

## 🧼 Recommended .gitignore

```gitignore
/storage/
/system/storage/
*.log
*.cache
*.zip
.vscode/
.ftp-simple.json
.DS_Store
node_modules/
```

---

## 📝 Commit Message Examples

```bash
git commit -m "Fix PDF label output for multiparcel shipment"
git commit -m "Add DPD webhook endpoint handler"
git commit -m "Update README.md and developer guide"
```

---

## ✅ Best Practices

* Keep FTP synced: save to upload
* Keep GitHub updated: commit and push regularly
* Avoid pushing config.php or secrets
* Add a README.md to explain changes

---

## 🔁 How to Revert to a Previous Version

### 1. View commit history

```bash
git log --oneline
```

Example output:

```
fe21c6f Fix label issue
9a2f4cb Add webhook support
273a8f1 Initial import from FTP
```

### 2. Revert files to a previous commit

```bash
git checkout <commit-hash> .
```

For example:

```bash
git checkout 9a2f4cb .
```

### 3. Save files to update on server

* Just press `Ctrl + S` in VS Code
* ftp-simple will automatically upload the reverted files to the server

### 4. (Optional) Commit the revert to Git

```bash
git add .
git commit -m "Revert to previous working version"
git push origin main
```

### Alternative: revert last commit entirely

```bash
git reset --hard HEAD~1
```

⚠️ This removes the last commit and changes. Use with caution.

---

Created by: **Viktoriia Poviliai**
