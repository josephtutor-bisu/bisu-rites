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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - BISU R.I.T.E.S' : 'BISU R.I.T.E.S'; ?></title>
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Shadcn Components -->
    <link rel="stylesheet" href="<?php echo $assets_path; ?>/shadcn.css">
    
    <style>
        :root {
            --primary: #3b82f6;
            --secondary: #8b5cf6;
            --destructive: #ef4444;
            --success: #10b981;
            --warning: #f59e0b;
            --muted: #6b7280;
            --border: #e5e7eb;
            --radius: 0.5rem;
        }
    </style>
</head>
<body>
