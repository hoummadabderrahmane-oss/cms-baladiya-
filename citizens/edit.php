<?php
require_once __DIR__ . '/../includes/header.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM citizens WHERE id = ?");
$stmt->execute([$id]);
$citizen = $stmt->fetch();

if (!$citizen) redirect('index.php');

if (isset($_POST['update'])) {
    $stmt = $pdo->prepare("UPDATE citizens SET fullname=?, cin=?, phone=?, email=?, address=?, birth_date=?, gender=? WHERE id=?");
    $stmt->execute([
        $_POST['fullname'], $_POST['cin'], $_POST['phone'], 
        $_POST['email'], $_POST['address'], $_POST['birth_date'], 
        $_POST['gender'], $id
    ]);
    flashMessage('success', 'Citizen updated successfully!');
    redirect('index.php');
}
?>

<h2>Edit Citizen</h2>
<div class="card">
    <div class="card-body">
        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Full Name *</label>
                    <input type="text" name="fullname" class="form-control" value="<?php echo sanitize($citizen['fullname']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>CIN</label>
                    <input type="text" name="cin" class="form-control" value="<?php echo sanitize($citizen['cin']); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Phone</label>
                    <input type="tel" name="phone" class="form-control" value="<?php echo sanitize($citizen['phone']); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo sanitize($citizen['email']); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Birth Date</label>
                    <input type="date" name="birth_date" class="form-control" value="<?php echo $citizen['birth_date']; ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Gender</label>
                    <select name="gender" class="form-select">
                        <option value="male" <?php echo $citizen['gender'] == 'male' ? 'selected' : ''; ?>>Male</option>
                        <option value="female" <?php echo $citizen['gender'] == 'female' ? 'selected' : ''; ?>>Female</option>
                    </select>
                </div>
                <div class="col-12 mb-3">
                    <label>Address</label>
                    <textarea name="address" class="form-control" rows="3"><?php echo sanitize($citizen['address']); ?></textarea>
                </div>
            </div>
            <button type="submit" name="update" class="btn btn-primary"><i class="bi bi-save"></i> Update</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>