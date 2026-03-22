<?php
session_start();
require_once "../db_connect.php";

// Check if user is logged in AND is Extension Director (Role ID 4)
if(!isset($_SESSION["loggedin"]) || !in_array($_SESSION["role_id"], [4, 7])){ header("location: ../login.php"); exit; }

// --- EXTENSION STATISTICS ---

// 1. Pending Proposals
$pending_count = 0;
$pending_query = $conn->query("SELECT COUNT(*) as count FROM ext_projects WHERE service_status IN ('Proposed', 'Under Review')");
if($pending_query) $pending_count = $pending_query->fetch_assoc()['count'];

// 2. Ongoing Programs
$ongoing_count = 0;
$ongoing_query = $conn->query("SELECT COUNT(*) as count FROM ext_projects WHERE service_status IN ('Approved', 'Ongoing')");
if($ongoing_query) $ongoing_count = $ongoing_query->fetch_assoc()['count'];

// 3. Completed Programs
$completed_count = 0;
$completed_query = $conn->query("SELECT COUNT(*) as count FROM ext_projects WHERE service_status = 'Completed'");
if($completed_query) $completed_count = $completed_query->fetch_assoc()['count'];

// --- DATA FOR CHARTS ---
$status_data = [];
$status_query = $conn->query("SELECT service_status, COUNT(*) as count FROM ext_projects GROUP BY service_status");
if($status_query) {
    while($row = $status_query->fetch_assoc()) {
        $status_data[$row['service_status']] = (int)$row['count'];
    }
}

// Funding distribution
$fund_data = [];
$fund_query = $conn->query("SELECT source_of_funds, COUNT(*) as count FROM ext_projects WHERE source_of_funds IS NOT NULL AND source_of_funds != '' GROUP BY source_of_funds");
if($fund_query) {
    while($row = $fund_query->fetch_assoc()) {
        $fund_data[$row['source_of_funds'] ?: 'Unspecified'] = (int)$row['count'];
    }
}

// --- PREVIEW DATA FOR HOVER POPUPS ---
$pending_preview = [];
$pending_preview_query = $conn->query("SELECT ext_id, project_title, service_status FROM ext_projects WHERE service_status IN ('Proposed', 'Under Review') ORDER BY ext_id DESC LIMIT 5");
if($pending_preview_query) {
    while($row = $pending_preview_query->fetch_assoc()) {
        $pending_preview[] = $row;
    }
}

$ongoing_preview = [];
$ongoing_preview_query = $conn->query("SELECT ext_id, project_title, service_status FROM ext_projects WHERE service_status IN ('Approved', 'Ongoing') ORDER BY ext_id DESC LIMIT 5");
if($ongoing_preview_query) {
    while($row = $ongoing_preview_query->fetch_assoc()) {
        $ongoing_preview[] = $row;
    }
}

$completed_preview = [];
$completed_preview_query = $conn->query("SELECT ext_id, project_title, service_status FROM ext_projects WHERE service_status = 'Completed' ORDER BY ext_id DESC LIMIT 5");
if($completed_preview_query) {
    while($row = $completed_preview_query->fetch_assoc()) {
        $completed_preview[] = $row;
    }
}

$page_title = "Extension Director Dashboard";
include "../includes/header.php";
?>

<style>
.stat-card {
    position: relative;
    isolation: isolate;
}

.preview-popup {
    position: absolute;
    left: 50%;
    bottom: 100%;
    top: auto;
    transform: translateX(-50%) scale(0.95);
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(0, 0, 0, 0.05);
    border: 1px solid #e5e7eb;
    min-width: 320px;
    max-width: 360px;
    margin-bottom: 12px;
    z-index: 50;
    pointer-events: none;
    white-space: normal;
}

.stat-card:hover .preview-popup,
.stat-card.show-preview .preview-popup {
    opacity: 1 !important;
    visibility: visible !important;
    transform: translateX(-50%) scale(1) !important;
    pointer-events: auto !important;
}

.preview-header {
    padding: 1rem;
    border-bottom: 1px solid #f3f4f6;
    font-weight: 600;
    font-size: 0.875rem;
    color: #1f2937;
}

.preview-list {
    max-height: 240px;
    overflow-y: auto;
}

.preview-item {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #f3f4f6;
    cursor: pointer;
    transition: background-color 0.2s;
}

.preview-item:last-child {
    border-bottom: none;
}

.preview-item:hover {
    background-color: #f9fafb;
}

.preview-item-title {
    font-size: 0.85rem;
    font-weight: 500;
    color: #1f2937;
    white-space: normal;
    word-wrap: break-word;
    margin-bottom: 0.25rem;
}

.preview-item-status {
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
}

.preview-footer {
    padding: 0.75rem 1rem;
    border-top: 1px solid #f3f4f6;
    text-align: center;
    font-size: 0.75rem;
    color: #6b7280;
}
</style>

<div class="flex h-screen overflow-hidden bg-slate-50">
    <?php include "../includes/navigation.php"; ?>

    <div class="main-content flex-1 flex flex-col overflow-y-auto p-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Extension Services Office</h1>
                <p class="text-slate-500 text-sm mt-1">Manage community outreach, trainings, and technology transfer programs.</p>
            </div>
            <div class="flex items-center space-x-4 bg-white px-4 py-2 rounded-lg shadow-sm border border-slate-200">
                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center text-green-600 font-bold">
                    <i class="fas fa-handshake"></i>
                </div>
                <div class="text-sm">
                    <p class="text-slate-500 text-xs"><?php echo $_SESSION['role_id'] == 4 ? 'Director Account' : 'Secretary Account'; ?></p>
                    <p class="font-bold text-slate-800"><?php echo htmlspecialchars($_SESSION["username"]); ?></p>
                </div>
                <button class="btn btn-outline btn-sm" onclick="window.location.href='../logout.php'" style="margin-left: auto;">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            
            <a href="ext_projects.php" class="stat-card group relative bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex items-center justify-between border-l-4 border-l-amber-500 hover:shadow-md hover:border-amber-300 transition-all cursor-pointer">
                <div class="preview-popup">
                    <div class="preview-header">📋 Pending Proposals</div>
                    <div class="preview-list">
                        <?php if(!empty($pending_preview)): ?>
                            <?php foreach($pending_preview as $item): ?>
                            <div class="preview-item">
                                <div class="preview-item-title"><?php echo htmlspecialchars(substr($item['project_title'], 0, 45)); ?></div>
                                <span class="preview-item-status" style="background-color: #fef3c7; color: #b45309;"><?php echo $item['service_status']; ?></span>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                        <div style="padding: 1rem; text-align: center; color: #9ca3af; font-size: 0.875rem;">No pending proposals</div>
                        <?php endif; ?>
                    </div>
                    <div class="preview-footer">View all proposals →</div>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-500 mb-1">Pending Proposals</p>
                    <h3 class="text-3xl font-bold text-slate-800"><?php echo $pending_count; ?></h3>
                </div>
                <div class="w-12 h-12 bg-amber-50 rounded-lg flex items-center justify-center text-amber-500 text-xl group-hover:bg-amber-100 transition">
                    <i class="fas fa-inbox"></i>
                </div>
            </a>

            <a href="ext_projects.php" class="stat-card group relative bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex items-center justify-between border-l-4 border-l-blue-500 hover:shadow-md hover:border-blue-300 transition-all cursor-pointer">
                <div class="preview-popup">
                    <div class="preview-header">⏳ Ongoing Programs</div>
                    <div class="preview-list">
                        <?php if(!empty($ongoing_preview)): ?>
                            <?php foreach($ongoing_preview as $item): ?>
                            <div class="preview-item">
                                <div class="preview-item-title"><?php echo htmlspecialchars(substr($item['project_title'], 0, 45)); ?></div>
                                <span class="preview-item-status" style="background-color: #dbeafe; color: #1e40af;"><?php echo $item['service_status']; ?></span>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                        <div style="padding: 1rem; text-align: center; color: #9ca3af; font-size: 0.875rem;">No ongoing programs</div>
                        <?php endif; ?>
                    </div>
                    <div class="preview-footer">View all programs →</div>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-500 mb-1">Ongoing Programs</p>
                    <h3 class="text-3xl font-bold text-slate-800"><?php echo $ongoing_count; ?></h3>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600 text-xl group-hover:bg-blue-100 transition">
                    <i class="fas fa-spinner fa-spin-pulse"></i>
                </div>
            </a>

            <a href="ext_projects.php" class="stat-card group relative bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex items-center justify-between border-l-4 border-l-green-500 hover:shadow-md hover:border-green-300 transition-all cursor-pointer">
                <div class="preview-popup">
                    <div class="preview-header">✅ Completed Activities</div>
                    <div class="preview-list">
                        <?php if(!empty($completed_preview)): ?>
                            <?php foreach($completed_preview as $item): ?>
                            <div class="preview-item">
                                <div class="preview-item-title"><?php echo htmlspecialchars(substr($item['project_title'], 0, 45)); ?></div>
                                <span class="preview-item-status" style="background-color: #d1fae5; color: #047857;"><?php echo $item['service_status']; ?></span>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                        <div style="padding: 1rem; text-align: center; color: #9ca3af; font-size: 0.875rem;">No completed activities</div>
                        <?php endif; ?>
                    </div>
                    <div class="preview-footer">View all completed →</div>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-500 mb-1">Completed Activities</p>
                    <h3 class="text-3xl font-bold text-slate-800"><?php echo $completed_count; ?></h3>
                </div>
                <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center text-green-600 text-xl group-hover:bg-green-100 transition">
                    <i class="fas fa-check-circle"></i>
                </div>
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 border-t-4 border-t-green-500">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-slate-800">Action Required: New Proposals</h3>
                <a href="ext_projects.php" class="text-sm text-green-600 hover:text-green-800 font-medium">View All</a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="text-xs text-slate-500 uppercase bg-slate-50">
                        <tr>
                            <th class="p-3">Project Title</th>
                            <th class="p-3">Target Beneficiary</th>
                            <th class="p-3">Status</th>
                            <th class="p-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-100">
                        <?php
                        $recent = $conn->query("SELECT ext_id, project_title, beneficiary_name, service_status FROM ext_projects WHERE service_status IN ('Proposed', 'Under Review') ORDER BY ext_id DESC LIMIT 5");
                        
                        if($recent && $recent->num_rows > 0) {
                            while($r = $recent->fetch_assoc()) {
                                echo "<tr class='hover:bg-slate-50 transition'>";
                                echo "<td class='p-3 font-medium text-slate-800'>" . htmlspecialchars(substr($r['project_title'], 0, 50)) . "</td>";
                                echo "<td class='p-3 text-slate-600'>" . htmlspecialchars(substr($r['beneficiary_name'], 0, 40)) . "</td>";
                                echo "<td class='p-3'><span class='bg-amber-100 text-amber-800 px-2 py-1 rounded-full text-xs font-semibold'>" . $r['service_status'] . "</span></td>";
                                echo "<td class='p-3 text-right'><a href='ext_project_review.php?id=".$r['ext_id']."' class='text-green-600 hover:underline font-bold'>Review</a></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' class='p-6 text-center text-slate-500'>No new proposals waiting for review.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Program Status Distribution</h3>
                <div class="flex justify-center" style="max-height: 300px;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Funding Sources</h3>
                <div class="flex justify-center" style="max-height: 300px;">
                    <canvas id="fundChart"></canvas>
                </div>
            </div>
        </div>
        
    </div>
</div>

<?php include "../includes/footer.php"; ?>

<script>
const statusLabels = <?php echo json_encode(array_keys($status_data)); ?>;
const statusValues = <?php echo json_encode(array_values($status_data)); ?>;
const statusColors = {
    'Proposed': '#8b5cf6', 'Under Review': '#f97316', 'Approved': '#06b6d4',
    'Ongoing': '#3b82f6', 'Completed': '#10b981', 'Not Completed': '#ef4444', 'Needs Follow-up': '#f59e0b'
};
if(statusLabels.length > 0) {
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: { labels: statusLabels, datasets: [{ data: statusValues, backgroundColor: statusLabels.map(l => statusColors[l] || '#94a3b8'), borderWidth: 2, borderColor: '#fff' }] },
        options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'bottom', labels: { padding: 15, usePointStyle: true, pointStyle: 'circle' } } } }
    });
} else {
    document.getElementById('statusChart').parentElement.innerHTML += '<p class="text-center text-slate-400 mt-4">No program data yet.</p>';
}

const fundLabels = <?php echo json_encode(array_keys($fund_data)); ?>;
const fundValues = <?php echo json_encode(array_values($fund_data)); ?>;
const fundColors = ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#f97316','#ec4899'];
if(fundLabels.length > 0) {
    new Chart(document.getElementById('fundChart'), {
        type: 'pie',
        data: { labels: fundLabels, datasets: [{ data: fundValues, backgroundColor: fundColors.slice(0, fundLabels.length), borderWidth: 2, borderColor: '#fff' }] },
        options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'bottom', labels: { padding: 15, usePointStyle: true, pointStyle: 'circle' } } } }
    });
} else {
    document.getElementById('fundChart').parentElement.innerHTML += '<p class="text-center text-slate-400 mt-4">No funding data yet.</p>';
}

// Initialize popup hover behavior for stat cards
document.querySelectorAll('.stat-card').forEach(card => {
    const popup = card.querySelector('.preview-popup');
    if (!popup) return;
    
    card.addEventListener('mouseenter', function() {
        const rect = card.getBoundingClientRect();
        const popupRect = popup.getBoundingClientRect();
        const spaceAbove = rect.top;
        const spaceBelow = window.innerHeight - rect.bottom;
        
        // Reset popup position and check space
        popup.style.bottom = 'auto';
        popup.style.top = 'auto';
        
        // If not enough space above, position below instead
        if (spaceAbove < 300 && spaceBelow > 300) {
            popup.style.top = '100%';
            popup.style.bottom = 'auto';
            popup.style.marginBottom = '0';
            popup.style.marginTop = '12px';
        } else {
            popup.style.bottom = '100%';
            popup.style.top = 'auto';
            popup.style.marginTop = '0';
            popup.style.marginBottom = '12px';
        }
    });
});
</script>