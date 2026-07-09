<?php
require_once __DIR__ . '/../includes/header.php';

if (isset($_POST['save'])) {
    $stmt = $pdo->prepare("INSERT INTO citizens (fullname, cin, phone, email, address, birth_date, gender) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['fullname'],
        $_POST['cin'],
        $_POST['phone'],
        $_POST['email'],
        $_POST['address'],
        $_POST['birth_date'],
        $_POST['gender']
    ]);
    flashMessage('success', 'Citizen added successfully!');
    redirect('index.php');
}
?>

<h2>Add Citizen</h2>

<div class="card">
    <div class="card-body">
        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Full Name *</label>
                    <input type="text" name="fullname" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>CIN</label>
                    <input type="text" name="cin" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Phone</label>
                    <input type="tel" name="phone" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Birth Date</label>
                    <input type="date" name="birth_date" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Gender</label>
                    <select name="gender" class="form-select">
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
                <div class="col-12 mb-3">
                    <label>Address</label>
                    <textarea name="address" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <button type="submit" name="save" class="btn btn-primary"><i class="bi bi-save"></i> Save</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>