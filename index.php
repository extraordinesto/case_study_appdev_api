<?php include './inc/header.php'; ?>
<link rel="stylesheet" href="./css/item.css">
<!-- Main Content -->
<div class="content-container">
  <div class="content">
    <div class="card-container" id="itemContainer">
      <!-- JS will populate items here -->
    </div>
  </div>
</div>

<script>
  function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('hidden');
  }

  const itemContainer = document.getElementById("itemContainer");

  fetch("http://localhost:5000/item")
    .then((response) => response.json())
    .then((data) => {
      data.forEach((item) => {
        const itemNumber = item.itemNumber || "0";
        const imageName = item.imageURL || "placeholder.jpg";
        const imageUrl = `http://192.168.100.15/Advance_IMS/data/item_images/${itemNumber}/${encodeURIComponent(imageName)}`;
        const itemName = item.itemName || "Item";
        const unitPrice = item.unitPrice || 0;
        const stock = item.stock || "0";

        const card = document.createElement("div");
        card.className = "card";
        card.innerHTML = `
          <img src="${imageUrl}" alt="Image" onerror="this.src='default.jpg'">
          <div class="card-title">${itemName}</div>
          <div class="card-price">₱${parseFloat(unitPrice).toFixed(2)}</div>
          <div class="card-stock">Stock: ${stock}</div>
        `;
        itemContainer.appendChild(card);
      });
    })
    .catch((error) => {
      itemContainer.innerHTML = "<p>Error loading items.</p>";
      console.error("Fetch error:", error);
    });
</script>

<?php include './inc/footer.php'; ?>
