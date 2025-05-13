<?php include './inc/header.php'; ?>
<link rel="stylesheet" href="./css/item.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
  .icon-btn-delete {
    background-color: #e55353;
    color: white;
    border: none;
    padding: 6px 10px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.2s ease;
    margin-top: 5px;
  }

  .icon-btn-delete:hover {
    background-color: #c94242;
  }

  .icon-btn-edit {
    background-color:rgb(83, 229, 139);
    color: white;
    border: none;
    padding: 6px 10px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.2s ease;
    margin-top: 5px;
  }

  .icon-btn-edit:hover {
    background-color:rgb(66, 201, 118);
  }
</style>
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
        const itemName = item.itemName || "Item";
        const itemNumber = item.itemNumber || "0";
        const unitPrice = item.unitPrice || 0;
        const stock = item.stock || "0";
        const imageURL = item.imageURL || "placeholder.jpg";

        // Detect if imageURL is internet URL
        const imageUrl = imageURL.startsWith('http') ?
          imageURL :
          `http://192.168.217.160/Advance_IMS/data/item_images/${itemNumber}/${encodeURIComponent(imageURL)}`;

        const card = document.createElement("div");
        card.className = "card";
        card.innerHTML = `
          <img src="${imageUrl}" alt="Image" onerror="this.src='default.jpg'">
          <div class="card-title">${itemName}</div>
          <div class="card-price">₱${parseFloat(unitPrice).toFixed(2)}</div>
          <div class="card-stock">Stock: ${stock}</div>
          <div class="card-action">
            <a href="edit_item.php?id=${item.productID}" class="icon-btn-edit"><i class="fas fa-pen"></i></a>
            <button class="icon-btn-delete" onclick="deleteItem('${item.itemNumber}')">
              <i class="fas fa-trash"></i>
            </button>
          </div>
        `;
        itemContainer.appendChild(card);
      });
    })
    .catch((error) => {
      itemContainer.innerHTML = "<p>Error loading items.</p>";
      console.error("Fetch error:", error);
    });

  function deleteItem(itemNumber) {
    if (confirm("Are you sure you want to delete this item?")) {
      fetch(`http://localhost:5000/delete_item/${itemNumber}`, {
          method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
          alert(data.message);
          location.reload(); // reload to reflect deletion
        })
        .catch(error => console.error("Error deleting item:", error));
    }
  }
</script>

<?php include './inc/footer.php'; ?>