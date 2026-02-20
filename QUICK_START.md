# Quick Start Guide - BISU R.I.T.E.S System

## 🚀 How to Use the Restructured System

### Access Points

| User Role | Start Page | Dashboard |
|-----------|-----------|-----------|
| **Public User** | http://localhost/bisu-rites/ | Login available |
| **Superadmin** | Login → admin/admin_dashboard.php | Full system control |
| **R&D Director** | Login → RandD/rd_dashboard.php | Research management |
| **ITSO Director** | Login → itso/itso_dashboard.php | Innovation & IP |
| **Extension Director** | Login → extension/extension_dashboard.php | Community programs |
| **Faculty** | Login → index.php | Public dashboard |

---

## 📁 Navigation System (Zero Duplication!)

### Before (❌ Bad Practice)
```
admin_dashboard.php     - Has full navigation HTML
admin_users.php         - Has full navigation HTML (copy-paste)
admin_colleges.php      - Has full navigation HTML (copy-paste)
rd_dashboard.php        - Has full navigation HTML
... (7+ files with duplicated code)
```
**Problem**: Update navigation = edit 7+ files!

### After (✅ Best Practice)
```
includes/navigation.php - Single source of truth
admin/admin_dashboard.php        - Only calls: <?php include "../includes/navigation.php"; ?>
admin/admin_users.php            - Only calls: <?php include "../includes/navigation.php"; ?>
admin/admin_colleges.php         - Only calls: <?php include "../includes/navigation.php"; ?>
RandD/rd_dashboard.php           - Only calls: <?php include "../includes/navigation.php"; ?>
... (all files use same include)
```
**Benefit**: Update navigation = edit 1 file!

---

## 🔧 Developer Tasks

### Add New Menu Item
**File**: `includes/navigation.php` (Line ~50)

```php
<?php elseif ($_SESSION["role_id"] == 1): // Superadmin Navigation ?>
    <!-- Existing items... -->
    <li class="sidebar-nav-item">
        <a href="<?php echo $base_link; ?>new_page.php" class="sidebar-nav-link <?php echo $current_file == 'new_page.php' ? 'active' : ''; ?>">
            <i class="fas fa-icon-name"></i>
            <span>New Menu Item</span>
        </a>
    </li>
```

### Create New Dashboard
**Steps**:
1. Create file: `role_folder/dashboard.php` (e.g., `RandD/rd_analytics.php`)
2. Start with template below:
```php
<?php
session_start();
require_once "../db_connect.php";

if(!isset($_SESSION["loggedin"]) || $_SESSION["role_id"] !== 2){ // Change role_id
    header("location: ../login.php");
    exit;
}

$page_title = "Dashboard Title";
include "../includes/header.php";
?>

<div style="display: flex;">
    <?php include "../includes/navigation.php"; ?>
    <div class="main-content">
        <!-- Your content here -->
    </div>
</div>

<?php include "../includes/footer.php"; ?>
```
3. Add menu item to `includes/navigation.php`

### Update Styling
**File**: `assets/shadcn.css`

- All buttons: `.btn`, `.btn-primary`, `.btn-destructive`
- Cards: `.card`, `.card-header`, `.card-body`
- Alerts: `.alert`, `.alert-primary`, `.alert-destructive`
- Grid: `.grid`, `.grid-cols-1`, `.grid-cols-2`, etc.
- Badges: `.badge`, `.badge-primary`, `.badge-success`

---

## 📊 Landing Page Statistics

The homepage automatically displays counts. To populate data:

### Database Setup
```sql
INSERT INTO research_projects (title, status) VALUES
('Project 1', 'active'),
('Project 2', 'completed');

INSERT INTO ip_projects (title, type) VALUES
('Patent 1', 'patent'),
('Copyright 1', 'copyright');

INSERT INTO extension_programs (title, status) VALUES
('Program 1', 'active'),
('Program 2', 'planning');
```

### Statistics Query (in index.php)
- Counts from `research_projects` table
- Counts from `ip_projects` table
- Counts from `extension_programs` table
- Displays with animated cards

---

## 🔐 Security Checklist

Each dashboard file has:
```php
<?php
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["role_id"] !== requiredRoleId){
    header("location: ../login.php");
    exit;
}
```

**Role IDs**:
- 1 = Superadmin
- 2 = R&D Director
- 3 = ITSO Director
- 4 = Extension Director
- 5 = Faculty

---

## 🐛 Common Issues & Fixes

### Issue: "File not found" errors
**Cause**: Using wrong paths in admin folder
**Fix**: Always use `../` for parent directory
```php
require_once "../db_connect.php"              // ✅ Correct
require_once "db_connect.php"                 // ❌ Wrong
header("location: ../admin_dashboard.php")   // ✅ Correct (if in admin folder)
header("location: admin_dashboard.php")      // ❌ Wrong
```

### Issue: Navigation not showing
**Cause**: `includes/navigation.php` path wrong
**Fix**: Check path from your file
```php
<?php include "../includes/navigation.php"; ?>           // // From admin folder
<?php include "../../includes/navigation.php"; ?>        // From subsubfolder
```

### Issue: Assets (CSS) not loading
**Cause**: Wrong path in `includes/header.php`
**Fix**: `includes/header.php` automatically calculates path. No changes needed.

### Issue: Active menu not highlighting
**Cause**: Filename doesn't match CSS class
**Example**: File is `admin_users.php` but CSS checks for `admin_management.php`
**Fix**: Ensure `$current_file == 'admin_users.php'` matches actual filename

---

## 📝 File Change Summary

### 🆕 New Files Created
- `index.php` - Landing page
- `includes/header.php` - HTML header component
- `includes/navigation.php` - Sidebar navigation
- `includes/footer.php` - HTML footer component
- `SYSTEM_STRUCTURE.md` - Documentation

### ✏️ Modified Files
- `login.php` - Updated routing to new folders
- `admin/admin_dashboard.php` - Now uses includes
- `admin/admin_colleges.php` - Now uses includes
- `admin/admin_users.php` - Now uses includes
- All admin/ CRUD files - Updated paths
- `RandD/rd_dashboard.php` - Modern design + includes
- `itso/itso_dashboard.php` - Modern design + includes
- `extension/extension_dashboard.php` - Modern design + includes

### ✅ No Breaking Changes
- All authentication logic preserved
- All database queries work as before
- All form handling unchanged
- All validation logic intact

---

## 📞 Need Help?

1. **Navigation Issues**: Check `includes/navigation.php`
2. **Path Issues**: Ensure `../` used for parent directory
3. **Styling**: Check `assets/shadcn.css` for class names
4. **Security**: Verify role_id checks in each file
5. **Database**: Check tables exist (research_projects, ip_projects, extension_programs)

---

## 🎯 Next Steps

1. ✅ Test all dashboards load without errors
2. ✅ Verify navigation highlights active page
3. ✅ Check landing page displays statistics
4. ✅ Test login redirects to correct dashboard by role
5. ✅ Verify logout works from all pages
6. ⏳ Add content to R&D, ITSO, Extension dashboards
7. ⏳ Create additional admin management pages
8. ⏳ Set up email notifications
9. ⏳ Add audit logging

---

**System Status**: ✅ Fully Restructured & Optimized
**Launch Command**: Visit http://localhost/bisu-rites/
**Support**: Check SYSTEM_STRUCTURE.md for detailed documentation
