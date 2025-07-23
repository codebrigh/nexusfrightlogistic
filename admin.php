<?php
session_start();
// --- CONFIGURATION ---
$db_host = 'localhost';
$db_user = 'root'; // Change if needed
$db_pass = '';
$db_name = 'nexus_fright';
$admin_password = 'yourStrongAdminPassword'; // CHANGE THIS!
// --- DB CONNECTION ---
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) die('Database connection failed: ' . $conn->connect_error);
// --- LOGIN LOGIC ---
if (isset($_POST['admin_password'])) {
    if ($_POST['admin_password'] === $admin_password) {
        $_SESSION['admin'] = true;
    } else {
        $error = 'Invalid password';
    }
}
// --- LOGOUT ---
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}
// --- ADD/EDIT TRACKING ---
if (isset($_POST['save']) && isset($_SESSION['admin'])) {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $tracking_number = $conn->real_escape_string($_POST['tracking_number']);
    $status = $conn->real_escape_string($_POST['status']);
    $last_update = $conn->real_escape_string($_POST['last_update']);
    $estimated_delivery = $conn->real_escape_string($_POST['estimated_delivery']);
    $email = $conn->real_escape_string($_POST['email']);
    if ($id > 0) {
        $conn->query("UPDATE tracking SET tracking_number='$tracking_number', status='$status', last_update='$last_update', estimated_delivery='$estimated_delivery', email='$email' WHERE id=$id");
        $msg = "Tracking updated!";
    } else {
        $conn->query("INSERT INTO tracking (tracking_number, status, last_update, estimated_delivery, email) VALUES ('$tracking_number', '$status', '$last_update', '$estimated_delivery', '$email')");
        $msg = "Tracking added!";
    }
}
// --- DELETE TRACKING ---
if (isset($_GET['delete']) && isset($_SESSION['admin'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM tracking WHERE id=$id");
    $msg = "Tracking deleted!";
}
// --- FETCH FOR EDIT ---
$edit = null;
if (isset($_GET['edit']) && isset($_SESSION['admin'])) {
    $id = intval($_GET['edit']);
    $res = $conn->query("SELECT * FROM tracking WHERE id=$id");
    $edit = $res->fetch_assoc();
}
// --- FETCH ALL TRACKINGS ---
$trackings = [];
if (isset($_SESSION['admin'])) {
    $res = $conn->query("SELECT * FROM tracking ORDER BY id DESC");
    while ($row = $res->fetch_assoc()) $trackings[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel - Nexus Fright</title>
    <style>
        body { background: #f4f6fa; font-family: Arial, sans-serif; }
        .admin-container { max-width: 700px; margin: 40px auto; background: #fff; border-radius: 10px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); padding: 2rem; }
        .admin-title { text-align: center; font-size: 2rem; font-weight: 700; margin-bottom: 2rem; }
        .section-divider { border-bottom: 1.5px solid #e0e0e0; margin: 2rem 0 1.5rem 0; }
        .admin-card { background: #f8fafd; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); padding: 1.5rem 1.2rem; margin-bottom: 2rem; }
        .admin-form { display: flex; flex-direction: column; gap: 1.1rem; margin-bottom: 0; }
        .admin-form label { font-weight: 500; color: #333; margin-bottom: 0.2rem; }
        .admin-form input { padding: 0.7rem; border: 1.5px solid #b0b8c9; border-radius: 7px; font-size: 1rem; background: #f7fafc; transition: border 0.2s; }
        .admin-form input:focus { border: 1.5px solid #007bff; outline: none; }
        .admin-form button { background: #007bff; color: #fff; border: none; border-radius: 7px; padding: 0.7rem 1.2rem; font-size: 1rem; font-weight: 600; cursor: pointer; transition: background 0.2s; margin-top: 0.5rem; }
        .admin-form button:hover { background: #0056b3; }
        .tracking-table { width: 100%; border-collapse: collapse; margin-top: 1.5rem; font-size: 0.98rem; }
        .tracking-table th, .tracking-table td { border: 1px solid #e0e0e0; padding: 0.7rem; text-align: left; }
        .tracking-table th { background: #f4f8ff; }
        .tracking-table tr:nth-child(even) { background: #f8fafd; }
        .tracking-table tr:hover { background: #e3f2fd; }
        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.3em;
            border: none;
            border-radius: 5px;
            padding: 0.35em 0.9em;
            font-size: 0.97rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s, color 0.2s, box-shadow 0.2s;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            margin-right: 0.3em;
            outline: none;
            position: relative;
        }
        .action-btn.edit {
            background: #eaf4ff;
            color: #1565c0;
            border: 1px solid #90caf9;
        }
        .action-btn.edit:hover {
            background: #1565c0;
            color: #fff;
        }
        .action-btn.delete {
            background: #fff0f0;
            color: #c62828;
            border: 1px solid #ef9a9a;
        }
        .action-btn.delete:hover {
            background: #c62828;
            color: #fff;
        }
        .action-btn svg {
            width: 1em;
            height: 1em;
            vertical-align: middle;
        }
        .action-btn[title]:hover:after {
            content: attr(title);
            position: absolute;
            left: 50%;
            top: 110%;
            transform: translateX(-50%);
            background: #222;
            color: #fff;
            padding: 0.2em 0.7em;
            border-radius: 4px;
            font-size: 0.85em;
            white-space: nowrap;
            z-index: 10;
            opacity: 0.95;
        }
        .logout-btn { float: right; background: #dc3545; color: #fff; border: none; border-radius: 4px; padding: 0.4rem 1rem; cursor: pointer; margin-bottom: 1rem; }
        .error-msg { color: #dc3545; font-weight: 500; margin-bottom: 1rem; }
        .success-msg { color: #28a745; font-weight: 500; margin-bottom: 1rem; }
        .modal-bg { display: none; position: fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(10,24,48,0.25); z-index:1000; justify-content:center; align-items:center; }
        .modal-confirm { background:#fff; border-radius:10px; box-shadow:0 4px 24px rgba(0,0,0,0.12); padding:2rem 2.5rem; text-align:center; }
        .modal-confirm button { margin:0 0.7em; }
    </style>
</head>
<body>
<div class="admin-container">
    <?php if (!isset($_SESSION['admin'])): ?>
        <div class="admin-title">Admin Login</div>
        <form class="admin-login" method="post">
            <input type="password" name="admin_password" placeholder="Enter admin password" required>
            <button type="submit">Login</button>
        </form>
        <?php if (isset($error)) echo '<div class="error-msg">'.$error.'</div>'; ?>
    <?php else: ?>
        <form method="get" style="text-align:right;">
            <button class="logout-btn" name="logout" value="1" type="submit">Logout</button>
        </form>
        <div class="admin-title">Tracking Management</div>
        <?php if (isset($msg)) echo '<div class="success-msg">'.$msg.'</div>'; ?>
        <div class="section-divider"></div>
        <div class="admin-card">
            <div style="font-size:1.2rem;font-weight:600;margin-bottom:1.2rem;color:#1565c0;">Add or Edit Tracking Entry</div>
            <form class="admin-form" method="post">
                <input type="hidden" name="id" value="<?php echo $edit ? $edit['id'] : ''; ?>">
                <label for="tracking_number">Tracking Number</label>
                <input type="text" id="tracking_number" name="tracking_number" placeholder="Tracking Number" required value="<?php echo $edit ? htmlspecialchars($edit['tracking_number']) : ''; ?>">
                <label for="status">Status</label>
                <input type="text" id="status" name="status" placeholder="Status" required value="<?php echo $edit ? htmlspecialchars($edit['status']) : ''; ?>">
                <label for="last_update">Last Update</label>
                <input type="text" id="last_update" name="last_update" placeholder="Last Update" required value="<?php echo $edit ? htmlspecialchars($edit['last_update']) : ''; ?>">
                <label for="estimated_delivery">Estimated Delivery</label>
                <input type="text" id="estimated_delivery" name="estimated_delivery" placeholder="Estimated Delivery" required value="<?php echo $edit ? htmlspecialchars($edit['estimated_delivery']) : ''; ?>">
                <label for="email">User Email</label>
                <input type="email" id="email" name="email" placeholder="User Email" required value="<?php echo $edit ? htmlspecialchars($edit['email']) : ''; ?>">
                <button type="submit" name="save"><?php echo $edit ? 'Update Tracking' : 'Add Tracking'; ?></button>
            </form>
        </div>
        <div style="font-size:1.1rem;font-weight:600;margin-bottom:0.7rem;color:#222;">All Tracking Entries</div>
        <table class="tracking-table">
            <thead>
                <tr>
                    <th>Tracking Number</th>
                    <th>Status</th>
                    <th>Last Update</th>
                    <th>Estimated Delivery</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($trackings)): ?>
                <tr><td colspan="5" style="text-align:center;">No tracking entries found.</td></tr>
            <?php else: foreach ($trackings as $tr): ?>
                <tr>
                    <td><?php echo htmlspecialchars($tr['tracking_number']); ?></td>
                    <td><?php echo htmlspecialchars($tr['status']); ?></td>
                    <td><?php echo htmlspecialchars($tr['last_update']); ?></td>
                    <td><?php echo htmlspecialchars($tr['estimated_delivery']); ?></td>
                    <td><?php echo htmlspecialchars($tr['email']); ?></td>
                    <td>
                        <a class="action-btn edit" href="?edit=<?php echo $tr['id']; ?>" title="Edit">
                            <svg viewBox="0 0 20 20" fill="currentColor"><path d="M17.414 2.586a2 2 0 0 0-2.828 0l-9.9 9.9A2 2 0 0 0 4 15v1a1 1 0 0 0 1 1h1a2 2 0 0 0 1.414-.586l9.9-9.9a2 2 0 0 0 0-2.828zM5 16v-1l9.9-9.9 1 1L6 16H5z"/></svg>
                            Edit
                        </a>
                        <button class="action-btn delete" title="Delete" onclick="showDeleteModal(<?php echo $tr['id']; ?>, '<?php echo htmlspecialchars($tr['tracking_number']); ?>');return false;">
                            <svg viewBox="0 0 20 20" fill="currentColor"><path d="M6 8a1 1 0 0 1 1 1v6a1 1 0 0 1-2 0V9a1 1 0 0 1 1-1zm4 0a1 1 0 0 1 1 1v6a1 1 0 0 1-2 0V9a1 1 0 0 1 1-1zm4-3a1 1 0 0 1 1 1v1H5V6a1 1 0 0 1 1-1h2.586l.707-.707A1 1 0 0 1 10.414 4h1.172a1 1 0 0 1 .707.293l.707.707H15zM4 7v10a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7H4z"/></svg>
                            Delete
                        </button>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
        <div class="modal-bg" id="deleteModal">
            <div class="modal-confirm">
                <div id="deleteModalMsg" style="margin-bottom:1.2em;font-size:1.1em;"></div>
                <form method="get" style="display:inline;">
                    <input type="hidden" name="delete" id="deleteModalId" value="">
                    <button type="submit" class="action-btn delete" style="margin:0 0.7em;">Yes, Delete</button>
                    <button type="button" class="action-btn edit" onclick="hideDeleteModal()">Cancel</button>
                </form>
            </div>
        </div>
        <script>
        function showDeleteModal(id, trackingNumber) {
            document.getElementById('deleteModalId').value = id;
            document.getElementById('deleteModalMsg').textContent = 'Are you sure you want to delete tracking #' + trackingNumber + '?';
            document.getElementById('deleteModal').style.display = 'flex';
        }
        function hideDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }
        // Hide modal on background click
        document.getElementById('deleteModal').onclick = function(e) {
            if (e.target === this) hideDeleteModal();
        };
        </script>
    <?php endif; ?>
</div>
</body>
</html> 