<?php
session_start();
require_once "../db_connect.php";

if(!isset($_SESSION["loggedin"]) || in_array($_SESSION["role_id"], [1, 2, 3, 4])) {
    header("location: ../login.php"); exit;
}

$page_title = "Download Forms & Templates - BISU RITES";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $page_title; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 min-h-screen flex flex-col">

    <nav class="bg-blue-800 text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex-shrink-0 font-bold text-xl tracking-wider">
                    BISU R.I.T.E.S <span class="text-blue-300 text-sm font-normal">| Researcher Portal</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="user_dashboard.php" class="text-blue-200 hover:text-white text-sm font-medium transition"><i class="fas fa-home mr-1"></i> Dashboard</a>
                    <a href="user_settings.php" class="text-blue-200 hover:text-white transition" title="Account Settings"><i class="fas fa-cog text-lg"></i></a>
                    <span class="text-blue-400">|</span>
                    <a href="../logout.php" class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded text-sm transition font-medium shadow-sm">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full">
        
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-800"><i class="fas fa-file-download text-blue-600 mr-2"></i> Downloadable Forms</h1>
            <p class="text-slate-500 mt-2 text-lg">Official BISU R.I.T.E.S templates required for project proposals and IP disclosures.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden border-t-4 border-t-blue-500 flex flex-col">
                <div class="p-5 border-b border-slate-100 bg-blue-50/50 flex items-center">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-xl mr-3"><i class="fas fa-flask"></i></div>
                    <h3 class="font-bold text-slate-800 text-lg">Research & Development</h3>
                </div>
                <div class="p-5 flex-grow">
                    <ul class="space-y-3">
                        <li class="flex justify-between items-start group">
                            <div>
                                <p class="text-sm font-bold text-slate-700 group-hover:text-blue-600 transition">F-RIE-RES-001</p>
                                <p class="text-xs text-slate-500">Institutionally Funded Research Form</p>
                            </div>
                            <a href="../downloads/rd/F-RIE-RES-001-Institutionally-Funded-Research-Form.docx" download class="text-slate-400 hover:text-blue-600 transition"><i class="fas fa-download"></i></a>
                        </li>
                        <li class="flex justify-between items-start group">
                            <div>
                                <p class="text-sm font-bold text-slate-700 group-hover:text-blue-600 transition">F-RIE-RES-002</p>
                                <p class="text-xs text-slate-500">Research Endorsement Form</p>
                            </div>
                            <a href="../downloads/rd/F-RIE-RES-002-Research-Endorsement-Form.docx" download class="text-slate-400 hover:text-blue-600 transition"><i class="fas fa-download"></i></a>
                        </li>
                        <li class="flex justify-between items-start group">
                            <div>
                                <p class="text-sm font-bold text-slate-700 group-hover:text-blue-600 transition">F-RIE-RES-003</p>
                                <p class="text-xs text-slate-500">Research Compliance Form</p>
                            </div>
                            <a href="../downloads/rd/F-RIE-RES-003-Research-Compliance-Form.docx" download class="text-slate-400 hover:text-blue-600 transition"><i class="fas fa-download"></i></a>
                        </li>
                    </ul>
                </div>
                <div class="p-4 border-t border-slate-100 bg-slate-50">
                    <a href="user_rd_submit.php" class="block w-full text-center bg-blue-100 hover:bg-blue-200 text-blue-700 font-bold py-2 rounded transition text-sm">Submit R&D Proposal</a>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden border-t-4 border-t-teal-500 flex flex-col">
                <div class="p-5 border-b border-slate-100 bg-teal-50/50 flex items-center">
                    <div class="w-10 h-10 bg-teal-100 rounded-full flex items-center justify-center text-teal-600 text-xl mr-3"><i class="fas fa-lightbulb"></i></div>
                    <h3 class="font-bold text-slate-800 text-lg">Innovation (ITSO)</h3>
                </div>
                <div class="p-5 flex-grow">
                    <ul class="space-y-3">
                        <li class="flex justify-between items-start group">
                            <div>
                                <p class="text-sm font-bold text-slate-700 group-hover:text-teal-600 transition">F-RIE-ITS-001</p>
                                <p class="text-xs text-slate-500">Certification of Contribution</p>
                            </div>
                            <a href="../downloads/itso/F-RIE-ITS-001.docx" download class="text-slate-400 hover:text-teal-600 transition"><i class="fas fa-download"></i></a>
                        </li>
                        <li class="flex justify-between items-start group">
                            <div>
                                <p class="text-sm font-bold text-slate-700 group-hover:text-teal-600 transition">F-RIE-ITS-002</p>
                                <p class="text-xs text-slate-500">Patent Application Request</p>
                            </div>
                            <a href="../downloads/itso/F-RIE-ITS-002-Revision-3.docx" download class="text-slate-400 hover:text-teal-600 transition"><i class="fas fa-download"></i></a>
                        </li>
                        <li class="flex justify-between items-start group">
                            <div>
                                <p class="text-sm font-bold text-slate-700 group-hover:text-teal-600 transition">F-RIE-ITS-003</p>
                                <p class="text-xs text-slate-500">Inquiry / Commercialization Request</p>
                            </div>
                            <a href="../downloads/itso/F-RIE-ITS-003.docx" download class="text-slate-400 hover:text-teal-600 transition"><i class="fas fa-download"></i></a>
                        </li>
                        <li class="flex justify-between items-start group">
                            <div>
                                <p class="text-sm font-bold text-slate-700 group-hover:text-teal-600 transition">F-RIE-ITS-004 & 005</p>
                                <p class="text-xs text-slate-500">Utility Model / Industrial Design Request</p>
                            </div>
                            <div class="flex space-x-2">
                                <a href="../downloads/itso/F-RIE-ITS-004.docx" download class="text-slate-400 hover:text-teal-600 transition" title="Utility Model"><i class="fas fa-file-word"></i></a>
                                <a href="../downloads/itso/F-RIE-ITS-005.docx" download class="text-slate-400 hover:text-teal-600 transition" title="Industrial Design"><i class="fas fa-file-word"></i></a>
                            </div>
                        </li>
                        <li class="flex justify-between items-start group">
                            <div>
                                <p class="text-sm font-bold text-slate-700 group-hover:text-teal-600 transition">F-RIE-ITS-006 & 007</p>
                                <p class="text-xs text-slate-500">Copyright / Trademark Request</p>
                            </div>
                            <div class="flex space-x-2">
                                <a href="../downloads/itso/F-RIE-ITS-006.docx" download class="text-slate-400 hover:text-teal-600 transition" title="Copyright"><i class="fas fa-file-word"></i></a>
                                <a href="../downloads/itso/F-RIE-ITS-007.docx" download class="text-slate-400 hover:text-teal-600 transition" title="Trademark"><i class="fas fa-file-word"></i></a>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="p-4 border-t border-slate-100 bg-slate-50">
                    <a href="user_itso_submit.php" class="block w-full text-center bg-teal-100 hover:bg-teal-200 text-teal-800 font-bold py-2 rounded transition text-sm">Submit IP Disclosure</a>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden border-t-4 border-t-green-500 flex flex-col">
                <div class="p-5 border-b border-slate-100 bg-green-50/50 flex items-center">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center text-green-600 text-xl mr-3"><i class="fas fa-handshake"></i></div>
                    <h3 class="font-bold text-slate-800 text-lg">Extension Services</h3>
                </div>
                <div class="p-5 flex-grow">
                    <ul class="space-y-3">
                        <li class="flex justify-between items-start group">
                            <div>
                                <p class="text-sm font-bold text-slate-700 group-hover:text-green-600 transition">F-RIE-EXT-001</p>
                                <p class="text-xs text-slate-500">Needs Assessment Form</p>
                            </div>
                            <a href="../downloads/extension/F-RIE-EXT-001-1.docx" download class="text-slate-400 hover:text-green-600 transition"><i class="fas fa-download"></i></a>
                        </li>
                        <li class="flex justify-between items-start group">
                            <div>
                                <p class="text-sm font-bold text-slate-700 group-hover:text-green-600 transition">F-RIE-EXT-002 & 003</p>
                                <p class="text-xs text-slate-500">Program / Activity Proposal</p>
                            </div>
                            <div class="flex space-x-2">
                                <a href="../downloads/extension/F-RIE-EXT-002.docx" download class="text-slate-400 hover:text-green-600 transition" title="Program"><i class="fas fa-file-word"></i></a>
                                <a href="../downloads/extension/F-RIE-EXT-003-1.docx" download class="text-slate-400 hover:text-green-600 transition" title="Activity"><i class="fas fa-file-word"></i></a>
                            </div>
                        </li>
                        <li class="flex justify-between items-start group">
                            <div>
                                <p class="text-sm font-bold text-slate-700 group-hover:text-green-600 transition">F-RIE-EXT-004</p>
                                <p class="text-xs text-slate-500">Activity Evaluation Form</p>
                            </div>
                            <a href="../downloads/extension/F-RIE-EXT-004-_-Extension-Activity-Evaluation-Form-2-2.docx" download class="text-slate-400 hover:text-green-600 transition"><i class="fas fa-download"></i></a>
                        </li>
                        <li class="flex justify-between items-start group">
                            <div>
                                <p class="text-sm font-bold text-slate-700 group-hover:text-green-600 transition">F-RIE-EXT-005 & 006</p>
                                <p class="text-xs text-slate-500">Monitoring & Impact Assessment</p>
                            </div>
                            <div class="flex space-x-2">
                                <a href="../downloads/extension/F-RIE-EXT-005.docx" download class="text-slate-400 hover:text-green-600 transition" title="Monitoring"><i class="fas fa-file-word"></i></a>
                                <a href="../downloads/extension/F-RIE-EXT-006.docx" download class="text-slate-400 hover:text-green-600 transition" title="Impact"><i class="fas fa-file-word"></i></a>
                            </div>
                        </li>
                        <li class="flex justify-between items-start group">
                            <div>
                                <p class="text-sm font-bold text-slate-700 group-hover:text-green-600 transition">F-RIE-EXT-007</p>
                                <p class="text-xs text-slate-500">Terminal Report Form</p>
                            </div>
                            <a href="../downloads/extension/F-RIE-EXT-007.docx" download class="text-slate-400 hover:text-green-600 transition"><i class="fas fa-download"></i></a>
                        </li>
                    </ul>
                </div>
                <div class="p-4 border-t border-slate-100 bg-slate-50">
                    <a href="user_ext_submit.php" class="block w-full text-center bg-green-100 hover:bg-green-200 text-green-800 font-bold py-2 rounded transition text-sm">Propose Extension</a>
                </div>
            </div>

        </div>
    </main>
</body>
</html>