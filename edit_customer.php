<?php
include './inc/header.php';
include './inc/db.php'; // connection file

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM ims_customer WHERE customerID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $customer = $result->fetch_assoc();

    if (!$customer) {
        echo "Customer not found.";
        exit;
    }
} else {
    echo "No customer ID provided.";
    exit;
}
?>

<h2>Edit Customer</h2>
<form method="post" action="update_customer.php">
    <input type="hidden" name="customerID" value="<?php echo $customer['customerID']; ?>">

    <label>Full Name:</label>
    <input type="text" name="fullName" value="<?php echo $customer['fullName']; ?>" required><br>

    <label>Email:</label>
    <input type="email" name="email" value="<?php echo $customer['email']; ?>" required><br>

    <label>Mobile:</label>
    <input type="text" name="mobile" value="<?php echo $customer['mobile']; ?>" required><br>

    <label>Status:</label>
    <select name="status" required>
        <option value="Active" <?php if ($customer['status'] == 'Active') echo 'selected'; ?>>Active</option>
        <option value="Disable" <?php if ($customer['status'] == 'Disable') echo 'selected'; ?>>Disable</option>
    </select><br>

    <input type="submit" value="Update">
</form>

<?php include './inc/footer.php'; ?>
