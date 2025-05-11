<?php include './inc/header.php'; ?>

<?php
$customerID = $_GET['id'] ?? '';
?>

<link rel="stylesheet" href="./css/customer.css">
<div class="content-container">
    <div class="content">
        <h2>Edit Customer</h2>
        <form id="editCustomerForm">
            <input type="hidden" id="customerID" value="<?php echo $customerID; ?>">

            <div class="form-group">
                <label>Full Name:</label>
                <input type="text" id="fullName" required>
            </div>
            <div class="form-group">
                <label>Mobile:</label>
                <input type="number" id="mobile" required>
            </div>
            <div class="form-group">
                <label>Phone 2:</label>
                <input type="number" id="phone2" required>
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" id="email" required>
            </div>
            <div class="form-group">
                <label>Address:</label>
                <input type="text" id="address" required>
            </div>
            <div class="form-group">
                <label>Address 2:</label>
                <input type="text" id="address2" required>
            </div>
            <div class="form-group">
                <label>City:</label>
                <input type="text" id="city" required>
            </div>
            <div class="form-group">
                <label>District:</label>
                <input type="text" id="district" required>
            </div>
            <div class="form-group">
                <label>Status:</label>
                <select id="status" required>
                    <option value="Active">Active</option>
                    <option value="Disable">Disable</option>
                </select>
            </div>

            <div class="bot">
                <button type="submit" class="sub">Update</button>
                <a href="customer.php" class="can">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
    const id = document.getElementById("customerID").value;

    // Fetch customer data for prefill
    fetch(`http://127.0.0.1:5000/customer/${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById("fullName").value = data.fullName;
            document.getElementById("mobile").value = data.mobile;
            document.getElementById("phone2").value = data.phone2;
            document.getElementById("email").value = data.email;
            document.getElementById("address").value = data.address;
            document.getElementById("address2").value = data.address2;
            document.getElementById("city").value = data.city;
            document.getElementById("district").value = data.district;
            document.getElementById("status").value = data.status;
        });

    // Update on submit
    document.getElementById("editCustomerForm").addEventListener("submit", function(e) {
        e.preventDefault();

        const updatedData = {
            fullName: document.getElementById("fullName").value,
            email: document.getElementById("email").value,
            mobile: document.getElementById("mobile").value,
            phone2: document.getElementById("phone2").value,
            address: document.getElementById("address").value,
            address2: document.getElementById("address2").value,
            city: document.getElementById("city").value,
            district: document.getElementById("district").value,
            status: document.getElementById("status").value,
        };

        fetch(`http://127.0.0.1:5000/edit_customer/${id}`, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(updatedData)
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            window.location.href = "customer.php";
        })
        .catch(error => {
            console.error("Error updating customer:", error);
        });
    });
</script>

<?php include './inc/footer.php'; ?>
