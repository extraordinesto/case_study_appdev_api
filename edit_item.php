<?php include './inc/header.php'; ?>

<?php
$productID = $_GET['id'] ?? '';
?>

<link rel="stylesheet" href="./css/customer.css">
<div class="content-container">
    <div class="content">
        <h2>Edit Customer</h2>
        <form id="editItemForm">
            <input type="hidden" id="productID" value="<?php echo $productID; ?>">

            <div class="form-group">
                <label for="itemNumber">Item Number:</label>
                <input type="text" id="itemNumber" name="itemNumber" required />
            </div>

            <div class="form-group">
                <label for="itemName">Item Name:</label>
                <input type="text" id="itemName" name="itemName" placeholder="Detected Product Name" readonly />
            </div>

            <div class="form-group">
                <label for="status">Status:</label>
                <select id="status" name="status" required>
                    <option value="">Select Status</option>
                    <option value="Active">Active</option>
                    <option value="Disable">Disable</option>
                </select>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <input type="text" id="description" name="description" />
            </div>

            <div class="form-group">
                <label for="discount">Discount %:</label>
                <input type="number" id="discount" name="discount" required />
            </div>

            <div class="form-group">
                <label for="stock">Quantity:</label>
                <input type="number" id="stock" name="stock" required />
            </div>

            <div class="form-group">
                <label for="unitPrice">Unit Price:</label>
                <input type="number" id="unitPrice" name="unitPrice" required />
            </div>

            <div class="form-group">
                <label for="imageURL">Image URL:</label>
                <input type="text" id="imageURL" name="imageURL" />
                <!-- <img id="imagePreview" src="#" alt="Preview" style="max-width: 100px; display: none; margin-top: 10px;" /> -->
            </div>

            <div class="bot">
                <button type="submit" class="sub">Update</button>
                <a href="customer.php" class="can">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
    const id = document.getElementById("productID").value;

    fetch(`http://127.0.0.1:5000/item/${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById("itemNumber").value = data.itemNumber;
            document.getElementById("itemName").value = data.itemName;
            document.getElementById("discount").value = data.discount;
            document.getElementById("stock").value = data.stock;
            document.getElementById("unitPrice").value = data.unitPrice;
            document.getElementById("imageURL").value = data.imageURL;
            document.getElementById("status").value = data.status;
            document.getElementById("description").value = data.description;
        });

    // Update on submit
    document.getElementById("editItemForm").addEventListener("submit", function(e) {
        e.preventDefault();

        const updatedData = {
            itemNumber: document.getElementById("itemNumber").value,
            itemName: document.getElementById("itemName").value,
            discount: document.getElementById("discount").value,
            stock: document.getElementById("stock").value,
            unitPrice: document.getElementById("unitPrice").value,
            imageURL: document.getElementById("imageURL").value,
            status: document.getElementById("status").value,
            description: document.getElementById("description").value,
        };

        fetch(`http://127.0.0.1:5000/edit_item/${id}`, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(updatedData)
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                window.location.href = "index.php";
            })
            .catch(error => {
                console.error("Error updating item:", error);
            });
    });
</script>

<?php include './inc/footer.php'; ?>