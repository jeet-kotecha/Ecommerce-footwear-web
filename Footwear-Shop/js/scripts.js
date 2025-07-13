// Confirm delete action (for admin & cart item removal)
function confirmDelete() {
    return confirm("Are you sure you want to delete this item?");
}

// Add to cart notification
function addToCart(productName) {
    alert(productName + " added to cart!");
}

// Remove from cart notification
function removeFromCart(productName) {
    if (confirm("Remove " + productName + " from cart?")) {
        alert(productName + " removed from cart.");
    }
}

// Wishlist toggle button
function toggleWishlist(button) {
    if (button.classList.contains("added")) {
        button.classList.remove("added");
        button.innerHTML = "Add to Wishlist ❤️";
    } else {
        button.classList.add("added");
        button.innerHTML = "In Wishlist 💖";
    }
}

// Mobile menu toggle (if applicable)
function toggleMenu() {
    var menu = document.getElementById("mobileMenu");
    if (menu.style.display === "block") {
        menu.style.display = "none";
    } else {
        menu.style.display = "block";
    }
}

// Simulated product search suggestion (very basic)
function showSuggestions() {
    var input = document.getElementById("searchBox").value.toLowerCase();
    var suggestionBox = document.getElementById("suggestions");

    if (input.length === 0) {
        suggestionBox.innerHTML = "";
        return;
    }

    var suggestions = ["Nike Sneakers", "Adidas Running Shoes", "Casual Sandals", "Leather Boots", "Sports Shoes"];
    var matches = suggestions.filter(function (product) {
        return product.toLowerCase().includes(input);
    });

    var html = "<ul>";
    matches.forEach(function (item) {
        html += "<li onclick='selectSuggestion(\"" + item + "\")'>" + item + "</li>";
    });
    html += "</ul>";

    suggestionBox.innerHTML = html;
}

function selectSuggestion(value) {
    document.getElementById("searchBox").value = value;
    document.getElementById("suggestions").innerHTML = "";
}
