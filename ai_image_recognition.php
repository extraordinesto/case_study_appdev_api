<?php include './inc/header.php'; ?>

<link rel="stylesheet" href="./css/customer.css">
<link rel="stylesheet" href="./css/ai.css">

<div class="content-container">
  <div class="content">
    <h2>Upload Product Image</h2>
    <form id="uploadForm">
      <input type="file" name="image" accept="image/*" required onchange="previewImage(event)" />
      <button type="submit" class="detect-btn">Detect</button>
      <img id="preview" src="#" alt="Image Preview" style="max-width: 100%; display: none; margin-bottom: 10px;" />
    </form>
  </div>

  <div class="content">
    <h2>Detected Product Name</h2>
    <form id="productForm">
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
        <input type="text" id="imageURL" name="imageURL"/>
        <!-- <img id="imagePreview" src="#" alt="Preview" style="max-width: 100px; display: none; margin-top: 10px;" /> -->
      </div>

      <div class="bot">
        <input type="submit" value="Submit" class="sub" />
        <a href="./index.php" class="can">Cancel</a>
      </div>
    </form>
  </div>
</div>

<script>
  let uploadedImageURL = "";

  function previewImage(event) {
    const image = document.getElementById('preview');
    image.src = URL.createObjectURL(event.target.files[0]);
    image.style.display = 'block';
  }

//   function updateImagePreview() {
//     const url = document.getElementById('imageURL').value;
//     const preview = document.getElementById('imagePreview');
//     if (url.startsWith('http')) {
//       preview.src = url;
//       preview.style.display = 'block';
//     } else {
//       preview.style.display = 'none';
//     }
//   }

  document.getElementById('uploadForm').onsubmit = async function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    try {
      const res = await fetch('http://127.0.0.1:5000/detect-image', {
        method: 'POST',
        body: formData
      });

      if (!res.ok) throw new Error("Server returned an error.");
      const data = await res.json();

      uploadedImageURL = data.image_url || "";
      document.getElementById('itemName').value = (data.product_name || 'Unknown').replace(/\b\w/g, c => c.toUpperCase());
    } catch (err) {
      alert("Detection failed: " + err.message);
    }
  };

  document.getElementById('productForm').onsubmit = async function (e) {
    e.preventDefault();

    const payload = {
      itemNumber: document.getElementById('itemNumber').value,
      itemName: document.getElementById('itemName').value,
      discount: parseFloat(document.getElementById('discount').value),
      stock: parseInt(document.getElementById('stock').value),
      unitPrice: parseFloat(document.getElementById('unitPrice').value),
      imageURL: document.getElementById('imageURL').value,
      status: document.getElementById('status').value,
      description: document.getElementById('description').value
    };

    try {
      const res = await fetch("http://127.0.0.1:5000/add_item", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
      });

      const result = await res.json();
      if (res.status === 409) {
        alert("Item already exists: " + result.details);
        return;
      }
      if (!res.ok) throw new Error(result.error || "Error adding item.");

      alert("Product added successfully!");
      location.reload();
    } catch (err) {
      alert("Add item failed: " + err.message);
    }
  };
</script>

<?php include './inc/footer.php'; ?>
