<?php
session_start();
require_once "../db_connect.php";

if(!isset($_SESSION["loggedin"]) || in_array($_SESSION["role_id"], [1, 2, 3, 4])) {
    header("location: ../login.php"); exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $ip_type = $_POST["ip_type"];
    
    // Convert checkbox inputs to boolean (1 or 0)
    $is_funded = isset($_POST["is_externally_funded"]) ? 1 : 0;
    $funding_agency = $is_funded ? trim($_POST["funding_agency"]) : NULL;
    
    // ITSO Database Default Status
    $status = 'Disclosure Submitted'; 

    if(empty($title) || empty($ip_type)){
        $error = "Technology Title and IP Type are required.";
    } elseif (!isset($_FILES['ip_file']) || $_FILES['ip_file']['error'] == UPLOAD_ERR_NO_FILE) {
        $error = "Please attach your Draft Document (Description/Claims/Abstract) or Copy of Work.";
    } else {
        $conn->begin_transaction();

        try {
            // 1. Insert into ip_assets table
            $sql1 = "INSERT INTO ip_assets (title, ip_type, status, is_externally_funded, funding_agency, created_by_user_id) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt1 = $conn->prepare($sql1);
            $stmt1->bind_param("sssiis", $title, $ip_type, $status, $is_funded, $funding_agency, $_SESSION["id"]);
            $stmt1->execute();
            $new_ip_id = $stmt1->insert_id;

            // 2. Assign User as Main Inventor/Maker with 100% initial contribution
            // Forms F-RIE-ITS-001 requires tracking contribution percentage.
            $sql2 = "INSERT INTO ip_inventors (ip_id, user_id, contribution_percentage, task_assignment) VALUES (?, ?, 100.00, 'Main Developer')";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("ii", $new_ip_id, $_SESSION["id"]);
            $stmt2->execute();

            // 3. Handle File Upload
            $file = $_FILES['ip_file'];
            $file_name = basename($file["name"]);
            $clean_file_name = preg_replace("/[^a-zA-Z0-9.-]/", "_", $file_name);
            $unique_file_name = time() . "_IP_" . $new_ip_id . "_" . $clean_file_name;
            $target_dir = "../uploads/itso/";
            $target_file = $target_dir . $unique_file_name;

            if (move_uploaded_file($file["tmp_name"], $target_file)) {
                // 4. Save file path in `documents` table linked to ITSO module
                $doc_category = "Draft Patent / Copy of Work";
                $module = "ITSO";
                $sql3 = "INSERT INTO documents (module_type, reference_id, doc_category, file_name, file_path, uploaded_by) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt3 = $conn->prepare($sql3);
                $stmt3->bind_param("sisssi", $module, $new_ip_id, $doc_category, $file_name, $target_file, $_SESSION["id"]);
                $stmt3->execute();
            } else {
                throw new Exception("File upload failed. Ensure the 'uploads/itso/' folder exists.");
            }

            $conn->commit();
            echo "<script>alert('Intellectual Property successfully disclosed to the ITSO Office!'); window.location.href='user_dashboard.php';</script>";
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Failed to submit disclosure: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit IP Disclosure</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        // Toggle funding agency input box based on checkbox
        function toggleFunding() {
            var checkbox = document.getElementById('funding_checkbox');
            var inputDiv = document.getElementById('funding_input_div');
            inputDiv.style.display = checkbox.checked ? 'block' : 'none';
        }
    </script>
</head>
<body class="bg-slate-50 min-h-screen">
    
    <nav class="bg-blue-800 text-white shadow-md p-4 flex justify-between">
        <div class="font-bold text-xl tracking-wider">BISU R.I.T.E.S</div>
        <a href="user_dashboard.php" class="text-blue-200 hover:text-white"><i class="fas fa-arrow-left"></i> Back to Portal</a>
    </nav>

    <div class="max-w-3xl mx-auto py-10 px-4">
        <h2 class="text-2xl font-bold text-slate-800 mb-6"><i class="fas fa-lightbulb text-teal-600 mr-2"></i> Submit IP Disclosure</h2>
        
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 border-t-4 border-t-teal-500">
            <?php if($error) echo "<p class='text-red-600 mb-4 bg-red-50 p-3 rounded'>$error</p>"; ?>
            
            <form method="post" enctype="multipart/form-data" class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Title of the Technology / Work *</label>
                    <input type="text" name="title" required class="w-full border border-slate-300 rounded p-2 focus:ring-teal-500 focus:border-teal-500">
                    <p class="text-xs text-slate-500 mt-1">E.g., "AI-Based Agricultural Sensor", "BISU University Hymn"</p>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Type of Intellectual Property *</label>
                    <select name="ip_type" required class="w-full border border-slate-300 rounded p-2 focus:ring-teal-500 focus:border-teal-500">
                        <option value="">-- Select IP Type --</option>
                        <option value="Patent">Patent (Invention)</option>
                        <option value="Utility Model">Utility Model</option>
                        <option value="Industrial Design">Industrial Design</option>
                        <option value="Copyright">Copyright (Literary, Artistic, Software)</option>
                        <option value="Trademark">Trademark / Logo</option>
                    </select>
                </div>

                <div class="bg-slate-50 p-4 rounded border border-slate-200">
                    <h3 class="font-bold text-sm text-slate-700 mb-3">Funding Information (Based on Form F-RIE-ITS-003)</h3>
                    <label class="flex items-center space-x-2 text-sm text-slate-700 cursor-pointer">
                        <input type="checkbox" name="is_externally_funded" id="funding_checkbox" onchange="toggleFunding()" class="rounded text-teal-600 focus:ring-teal-500">
                        <span>Is your technology funded by an external agency?</span>
                    </label>
                    
                    <div id="funding_input_div" style="display:none;" class="mt-3">
                        <label class="block text-xs font-bold text-slate-600 mb-1">Name of Funding Agency:</label>
                        <input type="text" name="funding_agency" class="w-full border border-slate-300 rounded p-2 text-sm focus:ring-teal-500">
                    </div>
                </div>

                <div class="p-4 border border-dashed border-teal-300 rounded bg-teal-50">
                    <label class="block text-sm font-bold text-slate-800 mb-2"><i class="fas fa-file-upload mr-1"></i> Attach Draft Document / Copy of Work *</label>
                    <input type="file" name="ip_file" required class="w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-teal-600 file:text-white hover:file:bg-teal-700 cursor-pointer">
                    <p class="text-xs text-teal-700 mt-2">Required by ITSO forms: Upload the draft description, claims, abstract, or softcopy of the literary work.</p>
                </div>

                <div class="pt-4 border-t border-slate-200 flex justify-end">
                    <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2 rounded shadow transition font-bold">
                        <i class="fas fa-paper-plane mr-1"></i> Submit Disclosure to ITSO
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>