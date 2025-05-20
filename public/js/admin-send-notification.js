document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("sendNotificationForm");
  form.addEventListener("submit", async function (event) {
    event.preventDefault();
    SwalHoldModal("Please hold on for a moment while send notification.");
    const formData = new FormData(form);
    formData.append("action", "asd_send_notification");
    formData.append("_wpnonce", asd_ajax.ajax_nonce);
    try {
      const authResponse = await fetch(asd_ajax.ajax_url, {
        method: "POST",
        body: formData,
      });
      const authData = await authResponse.json();
      if (!authData.success) {
        errorModal(authData.data.message || "Failed to send notification.");
        return;
      }
      successModal(authData.data.message || "Notification sent successfully.");
    } catch (error) {
      console.log(error);
      errorModal("An error occurred during the process.");
      return;
    }
  });
  getProducts();
});
const getProducts = async () => {
  const productList = document.getElementById("productList");
  const notificationUrlInput = document.getElementById("notificationUrl");
  const searchInput = document.getElementById("searchInput");
  const paginationControls = document.getElementById("paginationControls");

  const itemsPerPage = 10; // Jumlah produk per halaman
  let currentPage = 1;
  let filteredProducts = []; // Produk yang difilter untuk pencarian

  try {
    const response = await fetch(asd_ajax.ajax_url, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: new URLSearchParams({
        action: "asd_woocommerce_products",
        _wpnonce: asd_ajax.ajax_nonce_product,
      }),
    });

    const results = await response.json();
    if (!results.success) throw new Error("Failed to fetch products");

    const allProducts = results.data || [];
    const renderProducts = (products, page = 1) => {
      productList.innerHTML = ""; // Bersihkan list lama
      const start = (page - 1) * itemsPerPage;
      const end = start + itemsPerPage;
      const paginatedProducts = products.slice(start, end);

      paginatedProducts.forEach((product) => {
        const row = document.createElement("tr");
        const nameCell = document.createElement("td");
        nameCell.textContent = product.name;

        const priceCell = document.createElement("td");
        priceCell.style.width = "50px";
        priceCell.innerHTML = product.price || "N/A";
        const categoryCell = document.createElement("td");
        categoryCell.textContent =
          product.categories && product.categories.length ? product.categories.join(", ") : "N/A";

        const tagsCell = document.createElement("td");
        tagsCell.textContent = product.tags && product.tags.length ? product.tags.join(", ") : "N/A";

        const actionCell = document.createElement("td");
        actionCell.style.width = "100px";
        actionCell.style.textAlign = "center";
        actionCell.style.whiteSpace = "nowrap";
        const selectButton = document.createElement("button");
        selectButton.textContent = "Select";
        selectButton.className = "button button-primary";
        selectButton.addEventListener("click", () => {
          notificationUrlInput.value = product.url; // Set URL ke input
          const modal = bootstrap.Modal.getInstance(document.getElementById("productModal"));
          modal.hide();
        });
        actionCell.appendChild(selectButton);

        // Append semua kolom ke row
        row.appendChild(nameCell);
        row.appendChild(priceCell);
        row.appendChild(categoryCell);
        row.appendChild(tagsCell);
        row.appendChild(actionCell);

        productList.appendChild(row);
      });

      renderPaginationControls(products.length, page);
    };

    const renderPaginationControls = (totalItems, page) => {
      paginationControls.innerHTML = "";
      const totalPages = Math.ceil(totalItems / itemsPerPage);

      for (let i = 1; i <= totalPages; i++) {
        const pageButton = document.createElement("button");
        pageButton.textContent = i;
        pageButton.className = `button ${i === page ? "button-primary" : "button-secondary"} mx-1`;
        pageButton.addEventListener("click", () => {
          currentPage = i;
          renderProducts(filteredProducts.length ? filteredProducts : allProducts, i);
        });
        paginationControls.appendChild(pageButton);
      }
    };

    filteredProducts = allProducts;
    renderProducts(allProducts);

    searchInput.addEventListener("input", () => {
      const searchTerm = searchInput.value.toLowerCase();
      filteredProducts = allProducts.filter((product) => product.name.toLowerCase().includes(searchTerm));
      currentPage = 1;
      renderProducts(filteredProducts);
    });
  } catch (error) {
    console.error("Error fetching products:", error);
    productList.innerHTML = `<tr><td colspan="3" class="text-center text-danger">Failed to load products</td></tr>`;
  }
};
