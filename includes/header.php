<?php
// Determine the base path based on current directory
$current_dir = dirname($_SERVER['PHP_SELF']);
$base_path = '/bisu-rites';

// Calculate relative path to assets
if (strpos($current_dir, '/admin') !== false) {
    $assets_path = '../assets';
} elseif (strpos($current_dir, '/RandD') !== false) {
    $assets_path = '../assets';
} elseif (strpos($current_dir, '/itso') !== false) {
    $assets_path = '../assets';
} elseif (strpos($current_dir, '/extension') !== false) {
    $assets_path = '../assets';
} else {
    $assets_path = './assets';
}
?>

<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - BISU R.I.T.E.S' : 'BISU R.I.T.E.S'; ?></title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?php echo $assets_path; ?>/shadcn.css?v=<?php echo filemtime(__DIR__ . '/../assets/shadcn.css'); ?>">
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: { colors: { darkbg: '#0f172a', darkcard: '#1e293b', darkborder: '#334155' } }
            }
        }
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <style>
        :root {
            --primary: #3b82f6; --secondary: #8b5cf6; --destructive: #ef4444;
            --success: #10b981; --warning: #f59e0b; --muted: #6b7280;
            --border: #e5e7eb; --radius: 0.5rem;
        }

        /* --- MAGIC DARK MODE OVERRIDES FOR ADMIN PAGES --- */
        html.dark body, html.dark .bg-gray-50, html.dark .bg-slate-50, html.dark .page-container { 
            background-color: #0f172a !important; color: #f1f5f9 !important; 
        }
        html.dark .bg-white, html.dark .card, html.dark .sidebar, html.dark table th { 
            background-color: #1e293b !important; border-color: #334155 !important; color: #f1f5f9 !important; 
        }
        html.dark .text-slate-800, html.dark .text-gray-800, html.dark h1, html.dark h2, html.dark h3 { color: #f8fafc !important; }
        html.dark .text-slate-500, html.dark .text-slate-600 { color: #cbd5e1 !important; }
        html.dark .border-slate-200, html.dark .border-slate-100, html.dark td, html.dark border-b { border-color: #334155 !important; }
        html.dark .sidebar-nav-link:hover { background-color: #334155 !important; }
        /* ------------------------------------------------- */

        /* Layout Fixes */
        body { display: block !important; margin: 0 !important; padding: 0 !important; width: 100% !important; overflow-x: hidden !important; }
        .page-container { display: block !important; width: 100% !important; min-height: 100vh !important; }
        .sidebar { width: 14rem !important; position: fixed !important; top: 0 !important; left: 0 !important; bottom: 0 !important; z-index: 50 !important; }
        .main-content { position: relative !important; margin-left: 14rem !important; width: calc(100% - 14rem) !important; min-height: 100vh !important; display: block !important; box-sizing: border-box !important; }
        .header { width: 100% !important; box-sizing: border-box !important; margin: 0 !important; display: flex !important; }
        .content-wrapper { width: 100% !important; box-sizing: border-box !important; }
    </style>
</head>
<body class="bg-slate-50 transition-colors duration-300">