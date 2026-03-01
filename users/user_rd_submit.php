<?php
session_start();
require_once "../db_connect.php";

if(!isset($_SESSION["loggedin"]) || in_array($_SESSION["role_id"], [1, 2, 3, 4])) {
    header("location: ../login.php"); exit;
}

$error = "";
$colleges_result = $conn->query("SELECT college_id, college_name FROM colleges ORDER BY college_name ASC");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["project_title"]);
    $abstract = trim($_POST["abstract"]);
    $budget = empty($_POST["budget"]) ? 0.00 : floatval($_POST["budget"]);
    $college_id = empty($_POST["college_id"]) ? NULL : intval($_POST["college_id"]);
    
    // CAPTURE THE NEW DATE FIELDS
    $start_date = empty($_POST["start_date"]) ? NULL : $_POST["start_date"];
    $end_date = empty($_POST["end_date"]) ? NULL : $_POST["end_date"];
    
    $status = 'Submitted'; 

    if(empty($title) || empty($college_id)){
        $error = "Title and College are required.";
    } elseif (!isset($_FILES['proposal_file']) || $_FILES['proposal_file']['error'] == UPLOAD_ERR_NO_FILE) {
        $error = "Please attach your proposal document.";
    } else {
        $conn->begin_transaction();

        try {
            // 1. UPDATED SQL: Insert into rd_projects including start_date and end_date
            $sql1 = "INSERT INTO rd_projects (project_title, abstract, status, budget, start_date, end_date, college_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt1 = $conn->prepare($sql1);
            $stmt1->bind_param("sssdssi", $title, $abstract, $status, $budget, $start_date, $end_date, $college_id);
            $stmt1->execute();
            $new_project_id = $stmt1->insert_id;

            // 2. Assign User as Main Author
            $sql2 = "INSERT INTO rd_proponents (rd_id, user_id, project_role) VALUES (?, ?, 'Main Author')";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("ii", $new_project_id, $_SESSION["id"]);
            $stmt2->execute();

            // 3. Handle File Upload
            $file = $_FILES['proposal_file'];
            $file_name = basename($file["name"]);
            
            $clean_file_name = preg_replace("/[^a-zA-Z0-9.-]/", "_", $file_name);
            $unique_file_name = time() . "_" . $new_project_id . "_" . $clean_file_name;
            $target_dir = "../uploads/rd/";
            $target_file = $target_dir . $unique_file_name;

            if (move_uploaded_file($file["tmp_name"], $target_file)) {
                // 4. Save file path in `documents` table
                $doc_category = "Initial Proposal";
                $module = "RD";
                $sql3 = "INSERT INTO documents (module_type, reference_id, doc_category, file_name, file_path, uploaded_by) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt3 = $conn->prepare($sql3);
                $stmt3->bind_param("sisssi", $module, $new_project_id, $doc_category, $file_name, $target_file, $_SESSION["id"]);
                $stmt3->execute();
            } else {
                throw new Exception("File upload failed. Please check folder permissions.");
            }

            $conn->commit();
            echo "<script>alert('Proposal successfully submitted!'); window.location.href='user_dashboard.php';</script>";
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Failed to submit proposal: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit Proposal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 min-h-screen">
    
    <nav class="bg-blue-800 text-white shadow-md p-4 flex justify-between">
        <div class="font-bold text-xl tracking-wider">BISU R.I.T.E.S</div>
        <a href="user_dashboard.php" class="text-blue-200 hover:text-white"><i class="fas fa-arrow-left"></i> Back to Portal</a>
    </nav>

    <div class="max-w-3xl mx-auto py-10 px-4">
        <h2 class="text-2xl font-bold text-slate-800 mb-6"><i class="fas fa-file-upload text-blue-600 mr-2"></i> Submit R&D Proposal</h2>
        
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <?php if($error) echo "<p class='text-red-600 mb-4 bg-red-50 p-3 rounded'>$error</p>"; ?>
            
            <form method="post" enctype="multipart/form-data" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Proposed Project Title *</label>
                    <input type="text" name="project_title" required class="w-full border border-slate-300 rounded p-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Abstract / Brief Rationale</label>
                    <textarea name="abstract" rows="5" class="w-full border border-slate-300 rounded p-2 focus:ring-blue-500"></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">My Department/College *</label>
                    <select name="college_id" required class="w-full border border-slate-300 rounded p-2 focus:ring-blue-500">
                        <option value="">-- Select --</option>
                        <?php 
                        if ($colleges_result->num_rows > 0) {
                            while($c = $colleges_result->fetch_assoc()) {
                                echo "<option value='{$c['college_id']}'>{$c['college_name']}</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Proposed Budget (₱)</label>
                        <input type="number" step="0.01" name="budget" placeholder="0.00" class="w-full border border-slate-300 rounded p-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Target Start Date</label>
                        <input type="date" name="start_date" class="w-full border border-slate-300 rounded p-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Target End Date</label>
                        <input type="date" name="end_date" class="w-full border border-slate-300 rounded p-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="mt-4 p-4 border border-dashed border-slate-300 rounded bg-slate-50">
                    <label class="block text-sm font-bold text-slate-700 mb-2"><i class="fas fa-paperclip mr-1"></i> Attach Proposal Document *</label>
                    <input type="file" name="proposal_file" required class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-xs text-slate-500 mt-2">Accepted formats: PDF, DOCX (Max size: 5MB)</p>
                </div>

                <div class="pt-4 mt-6 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded shadow transition font-medium">Submit to R&D Office</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>