<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_admin();

// Handle status toggle / delete actions
if (isset($_GET['action'], $_GET['id'])) {
    $id = (int) $_GET['id'];
    if ($_GET['action'] === 'block') {
        $conn->query("UPDATE teachers SET status='blocked' WHERE teacher_id=$id");
        set_message("Teacher blocked.");
    } elseif ($_GET['action'] === 'unblock') {
        $conn->query("UPDATE teachers SET status='active' WHERE teacher_id=$id");
        set_message("Teacher unblocked.");
    } elseif ($_GET['action'] === 'delete') {
        $conn->query("DELETE FROM teachers WHERE teacher_id=$id");
        set_message("Teacher removed.");
    }
    header("Location: manage_teachers.php");
    exit();
}

$teachers = $conn->query("SELECT * FROM teachers ORDER BY created_at DESC");

$page_title = "Manage Teachers";
$asset_path = "../";
include '../includes/header.php';
?>

<h1>Manage Teachers</h1>
<?php show_message(); ?>
<a class="btn" href="add_teacher.php">+ Add Teacher</a>

<div class="card" style="margin-top:18px;">
<table>
<tr><th>Name</th><th>Email</th><th>Subject</th><th>Status</th><th>Joined</th><th>Actions</th></tr>
<?php while ($t = $teachers->fetch_assoc()): ?>
<tr>
    <td><?php echo htmlspecialchars($t['name']); ?></td>
    <td><?php echo htmlspecialchars($t['email']); ?></td>
    <td><?php echo htmlspecialchars($t['subject_expertise']); ?></td>
    <td><span class="badge badge-<?php echo $t['status']; ?>"><?php echo ucfirst($t['status']); ?></span></td>
    <td><?php echo date('d M Y', strtotime($t['created_at'])); ?></td>
    <td class="action-links">
        <?php if ($t['status'] === 'active'): ?>
            <a href="?action=block&id=<?php echo $t['teacher_id']; ?>">Block</a>
        <?php else: ?>
            <a href="?action=unblock&id=<?php echo $t['teacher_id']; ?>">Unblock</a>
        <?php endif; ?>
        <a class="confirm-action" data-confirm="Delete this teacher permanently?" href="?action=delete&id=<?php echo $t['teacher_id']; ?>">Delete</a>
    </td>
</tr>
<?php endwhile; ?>
</table>
</div>

<?php include '../includes/footer.php'; ?>
