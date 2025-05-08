<?php include './inc/header.php'; ?>

<link rel="stylesheet" href="./css/customer.css">
<div class="content-container">
    <div class="content">
        <h2>Customer Information</h2>
        <form method="post" action="">

            <div class="form-group">
                <label for="fullName" class="fullName">Full Name: </label>
                <input type="text" id="fullName" name="fullName" required>
            </div>

            <?php if (!empty($exists)): ?>
                <p class="exists"><?php echo $exists; ?></p>
            <?php endif; ?>

            <div class="form-group">
                <label for="mobile" class="mobile">Phone(mobile): </label>
                <input type="number" id="mobile" name="mobile" required>
            </div>

            <div class="form-group">
                <label for="phone2" class="phone2">Phone 2: </label>
                <input type="number" id="phone2" name="phone2" required>
            </div>

            <div class="form-group">
                <label for="email" class="email">Email: </label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="address" class="address">Address: </label>
                <input type="text" id="address" name="address" required>
            </div>

            <div class="form-group">
                <label for="address2" class="address2">Address 2: </label>
                <input type="text" id="address2" name="address2" required>
            </div>

            <div class="form-group">
                <label for="city" class="city">City: </label>
                <input type="text" id="city" name="city" required>
            </div>

            <div class="form-group">
                <label for="districtSelect" class="districtSelect">District: </label>
                <select id="districtSelect" name="districtSelect" required>
                    <option value=""></option>
                    <option value="Ampara">Ampara</option>
                    <option value="Anuradhapura">Anuradhapura</option>
                    <option value="Badulla">Badulla</option>
                    <option value="Batticalao">Batticalao</option>
                    <option value="Colombo">Colombo</option>
                    <option value="Galle">Galle</option>
                    <option value="Gampaha">Gampaha</option>
                    <option value="Hambantota">Hambantota</option>
                    <option value="Jaffna">Jaffna</option>
                    <option value="Kalutara">Kalutara</option>
                    <option value="Kandy">Kandy</option>
                    <option value="Mannar">Mannar</option>
                    <option value="Puttalam">Puttalam</option>
                    <option value="Ratnapura">Ratnapura</option>
                </select>
            </div>

            <div class="form-group">
                <label for="statusSelect" class="statusSelect">Status: </label>
                <select id="statusSelect" name="statusSelect" required>
                    <option value=""></option>
                    <option value="Active">Active</option>
                    <option value="Disable">Disable</option>
                </select>
            </div>

            <div class="bot">
                <input type="submit" value="Submit" class="sub">
                <a href="./index.php" class="can">Cancel</a>
            </div>
        </form>
    </div>

    <div class="content">
        <h2>Customer List</h2>

        <div class="table-wrapper">
            <table id="salesTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer Name</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="display_product">
                    <!-- Dynamic rows inserted here -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        fetchCustomer();

        document.querySelector("form").addEventListener("submit", function(event) {
            event.preventDefault();
            addCustomer();
        });
    });

    function fetchCustomer() {
        fetch("http://127.0.0.1:5000/customer")
            .then(response => response.json())
            .then(data => displayCustomer(data))
            .catch(error => console.error("Error fetching customers:", error));
    }

    function displayCustomer(add_customer) {
        const tableBody = document.getElementById("display_product");
        tableBody.innerHTML = "";

        add_customer.forEach(product => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${product.customerID}</td>
                <td>${product.fullName}</td>
                <td>${product.status}</td>
            `;
            tableBody.appendChild(row);
        });
    }

    function addCustomer() {
        let fullName = document.getElementById("fullName").value;
        let email = document.getElementById("email").value;
        let mobile = document.getElementById("mobile").value;
        let phone2 = document.getElementById("phone2").value;
        let address = document.getElementById("address").value;
        let address2 = document.getElementById("address2").value;
        let city = document.getElementById("city").value;
        let district = document.getElementById("districtSelect").value;
        let status = document.getElementById("statusSelect").value;

        fetch("http://127.0.0.1:5000/add_customer", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    fullName,
                    email,
                    mobile,
                    phone2,
                    address,
                    address2,
                    city,
                    district,
                    status
                }),
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                document.querySelector("form").reset();
                fetchCustomer();
            })
            .catch(error => console.error("Error adding customer:", error));
    }
</script>

<?php include './inc/footer.php'; ?>