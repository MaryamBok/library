
document.addEventListener("DOMContentLoaded", () => {
    console.log("DOM fully loaded and parsed");

    // Check which page we are on to run specific functions
    const pagePath = window.location.pathname;

    if (pagePath.includes("Books.html")) {
        console.log("On Books page, loading books...");
        loadBooks();
    }

    if (pagePath.includes("LogIn.html")) {
        console.log("On Login page, setting up form...");
        setupLoginForm();
    }

    if (pagePath.includes("SignIn.html")) {
        console.log("On Sign In page, setting up form...");
        setupRegisterForm();
    }
    
    if (pagePath.includes("Cart.html")) {
        console.log("On Cart page, loading cart...");
        loadCart();
    }

    if (pagePath.includes("add_book_form.html")) {
        console.log("On Add Book page, setting up form...");
        setupAddBookForm();
        // Also check authorization immediately
        checkAddBookAuthorization(); 
    }

    // Setup logout functionality if logout button exists
    const logoutButton = document.getElementById("logoutButton"); 
    if (logoutButton) {
        logoutButton.addEventListener("click", handleLogout);
    }
    
    // Update UI based on login status (e.g., show/hide login/logout links)
    updateLoginStatusUI();
});

// --- Book Loading --- 
async function loadBooks() {
    const bookListContainer = document.getElementById("book-list"); 
    if (!bookListContainer) {
        console.error("Book list container not found.");
        return;
    }

    try {
        const response = await fetch("php/get_books.php"); 
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const books = await response.json();

        bookListContainer.innerHTML = ""; 

        if (books.length === 0) {
            bookListContainer.innerHTML = "<p>No books available currently.</p>";
            return;
        }

        books.forEach(book => {
            const bookElement = document.createElement("div");
            bookElement.className = "book-item"; 
            bookElement.innerHTML = `
                <h3>${book.title}</h3>
                <p>Author: ${book.author}</p>
                <p>Price: ${book.price} $</p>
                <p>Category: ${book.category || "Uncategorized"}</p>
                <p>${book.description || ""}</p>
                ${book.cover_image ? `<img src="${book.cover_image}" alt="${book.title}" style="max-width: 100px;">` : ""}
                <button onclick="addToCart(${book.id})">Add to Cart</button>
            `;
            bookListContainer.appendChild(bookElement);
        });

    } catch (error) {
        console.error("Error loading books:", error);
        bookListContainer.innerHTML = "<p>Error loading books. Please try again later.</p>";
    }
}

// --- Authentication --- 

function setupLoginForm() {
    const loginForm = document.getElementById("login-form"); 
    const messageDiv = document.getElementById("login-message"); 

    if (!loginForm || !messageDiv) {
        console.error("Login form or message div not found.");
        return;
    }

    loginForm.addEventListener("submit", async (event) => {
        event.preventDefault();
        messageDiv.textContent = ""; 

        const username = loginForm.username.value;
        const password = loginForm.password.value;

        if (!username || !password) {
            messageDiv.textContent = "Please enter your username and password.";
            messageDiv.style.color = "red";
            return;
        }

        try {
            const response = await fetch("php/login_handler.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ username, password }),
            });

            const result = await response.json();

            if (response.ok) {
                messageDiv.textContent = "Login successful! Redirecting...";
                messageDiv.style.color = "green";
                localStorage.setItem("isLoggedIn", "true");
                localStorage.setItem("userData", JSON.stringify(result.user)); 
                updateLoginStatusUI(); // Update UI immediately after login
                setTimeout(() => {
                    window.location.href = "Home.html"; 
                }, 1500);
            } else {
                messageDiv.textContent = result.message || "Login failed. Please check your credentials.";
                messageDiv.style.color = "red";
                localStorage.removeItem("isLoggedIn");
                localStorage.removeItem("userData");
                updateLoginStatusUI();
            }
        } catch (error) {
            console.error("Login error:", error);
            messageDiv.textContent = "Network or server error. Please try again.";
            messageDiv.style.color = "red";
            localStorage.removeItem("isLoggedIn");
            localStorage.removeItem("userData");
            updateLoginStatusUI();
        }
    });
}

function setupRegisterForm() {
    const registerForm = document.getElementById("register-form"); 
    const messageDiv = document.getElementById("register-message"); 

    if (!registerForm || !messageDiv) {
        console.error("Register form or message div not found.");
        return;
    }

    registerForm.addEventListener("submit", async (event) => {
        event.preventDefault();
        messageDiv.textContent = "";

        const username = registerForm.username.value;
        const email = registerForm.email.value;
        const password = registerForm.password.value;
        const role = registerForm.role.value; 

        if (!username || !email || !password || !role) {
            messageDiv.textContent = "Please fill out all fields.";
            messageDiv.style.color = "red";
            return;
        }

        try {
            const response = await fetch("php/register_handler.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ username, email, password, role }),
            });

            const result = await response.json();

            if (response.status === 201) {
                messageDiv.textContent = "Registration successful! You can now log in.";
                messageDiv.style.color = "green";
                setTimeout(() => {
                    window.location.href = "LogIn.html";
                }, 2000);
            } else {
                messageDiv.textContent = result.message || "Registration failed. Try again.";
                messageDiv.style.color = "red";
            }
        } catch (error) {
            console.error("Registration error:", error);
            messageDiv.textContent = "Network or server error. Please try again.";
            messageDiv.style.color = "red";
        }
    });
}

async function handleLogout() {
    try {
        const response = await fetch("php/logout.php", { method: "POST" }); 
        if (response.ok) {
             localStorage.removeItem("isLoggedIn");
             localStorage.removeItem("userData");
             updateLoginStatusUI(); 
             window.location.href = "Home.html"; 
        } else {
             console.error("Logout failed on server.");
             localStorage.removeItem("isLoggedIn");
             localStorage.removeItem("userData");
             updateLoginStatusUI();
             alert("Logout failed from server, but session cleared.");
             window.location.href = "Home.html"; 
        }
    } catch (error) {
        console.error("Logout network error:", error);
        localStorage.removeItem("isLoggedIn");
        localStorage.removeItem("userData");
        updateLoginStatusUI();
        alert("Logout error occurred. Session cleared.");
        window.location.href = "Home.html"; 
    }
}

function updateLoginStatusUI() {
    const isLoggedIn = localStorage.getItem("isLoggedIn") === "true";
    const loginLink = document.getElementById("loginLink"); 
    const registerLink = document.getElementById("registerLink"); 
    const logoutButton = document.getElementById("logoutButton"); 
    const welcomeMessage = document.getElementById("welcomeMessage"); 
    const adminSellerElements = document.querySelectorAll(".admin-seller-only"); 

    const userData = JSON.parse(localStorage.getItem("userData"));
    const userRole = userData ? userData.role : null;

    if (isLoggedIn) {
        if (loginLink) loginLink.style.display = "none";
        if (registerLink) registerLink.style.display = "none";
        if (logoutButton) logoutButton.style.display = "inline-block"; 
        if (welcomeMessage && userData) {
            welcomeMessage.textContent = `Welcome, ${userData.username}! (${userData.role})`;
            welcomeMessage.style.display = "inline";
        }
        // Show admin/seller specific elements
        if (userRole === 'admin' || userRole === 'seller') {
             adminSellerElements.forEach(el => el.style.display = 'block'); // Or inline-block depending on element type
        } else {
             adminSellerElements.forEach(el => el.style.display = 'none'); // Hide if not admin/seller
        }

    } else {
        if (loginLink) loginLink.style.display = "inline-block";
        if (registerLink) registerLink.style.display = "inline-block";
        if (logoutButton) logoutButton.style.display = "none";
        if (welcomeMessage) welcomeMessage.style.display = "none";
        // Hide admin/seller specific elements
        adminSellerElements.forEach(el => el.style.display = 'none');
    }
}

// --- Add Book Form --- 
function setupAddBookForm() {
    const addBookForm = document.getElementById("add-book-form");
    const messageDiv = document.getElementById("add-book-message");

    if (!addBookForm || !messageDiv) {
        console.error("Add book form or message div not found.");
        return;
    }

    addBookForm.addEventListener("submit", async (event) => {
        event.preventDefault();
        messageDiv.textContent = "";

        // Double check authorization before submitting
        const userData = JSON.parse(localStorage.getItem("userData"));
        const userRole = userData ? userData.role : null;
        if (userRole !== 'admin' && userRole !== 'seller') {
             messageDiv.textContent = "You are not authorized to add books.";
             messageDiv.style.color = "red";
             return;
        }

        const title = addBookForm.title.value;
        const author = addBookForm.author.value;
        const price = addBookForm.price.value;
        const category = addBookForm.category.value;
        const description = addBookForm.description.value;

        if (!title || !author || !price) {
            messageDiv.textContent = "Please fill out the title, author, and price fields.";
            messageDiv.style.color = "red";
            return;
        }

        try {
            const response = await fetch("php/add_book.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ title, author, price, category, description }), 
            });

            const result = await response.json();

            if (response.status === 201) {
                messageDiv.textContent = `Book "${result.title}" added successfully!`;
                messageDiv.style.color = "green";
                addBookForm.reset(); 
            } else {
                 if (response.status === 403) { // Unauthorized
                     messageDiv.textContent = "You are not authorized to add books.";
                 } else {
                     messageDiv.textContent = result.message || "Failed to add book.";
                 }
                messageDiv.style.color = "red";
            }
        } catch (error) {
            console.error("Add book error:", error);
            messageDiv.textContent = "Network or server error while adding the book.";
            messageDiv.style.color = "red";
        }
    });
}

// Function to check authorization specifically for the add book page content
function checkAddBookAuthorization() {
     const userData = JSON.parse(localStorage.getItem("userData"));
     const userRole = userData ? userData.role : null;
     const formSection = document.querySelector(".form-section.admin-seller-only");
     const unauthorizedMessage = document.getElementById("unauthorized-message");

     if (userRole === 'admin' || userRole === 'seller') {
         if (formSection) formSection.style.display = 'block';
         if (unauthorizedMessage) unauthorizedMessage.style.display = 'none';
     } else {
         if (formSection) formSection.style.display = 'none';
         if (unauthorizedMessage) unauthorizedMessage.style.display = 'block';
     }
}

// --- Cart Functions --- 
async function addToCart(bookId, quantity = 1) {
    console.log(`Adding book ${bookId} to cart, quantity ${quantity}`);
    if (localStorage.getItem("isLoggedIn") !== "true") {
        alert("Please log in to add items to the cart.");
        window.location.href = "LogIn.html";
        return;
    }

    try {
        const response = await fetch("php/cart_handler.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ book_id: bookId, quantity: quantity }),
        });

        const result = await response.json();

        if (response.ok || response.status === 201) {
            alert(`Successfully added "${result.title || 'the book'}" to the cart!`);
        } else {
            alert(`Failed to add the book to the cart: ${result.message}`);
        }
    } catch (error) {
        console.error("Add to cart error:", error);
        alert("An error occurred while trying to add the book to the cart.");
    }
}

async function loadCart() {
    const cartItemsContainer = document.getElementById("cart-items"); 
    const cartTotalElement = document.getElementById("cart-total"); 

    if (!cartItemsContainer || !cartTotalElement) {
        console.error("Cart container or total element not found.");
        return;
    }
    
    if (localStorage.getItem("isLoggedIn") !== "true") {
        cartItemsContainer.innerHTML = "<p>Please <a href='LogIn.html'>log in</a> to view your cart.</p>";
        cartTotalElement.textContent = "0.00 $";
        return;
    }

    try {
        const response = await fetch("php/cart_handler.php", { method: "GET" });
        if (!response.ok) {
             if (response.status === 401) { 
                 localStorage.removeItem("isLoggedIn");
                 localStorage.removeItem("userData");
                 updateLoginStatusUI();
                 cartItemsContainer.innerHTML = "<p>Your session has expired. Please <a href='LogIn.html'>log in</a> again.</p>";
                 cartTotalElement.textContent = "0.00 $";
                 return;
             }
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const cartItems = await response.json();

        cartItemsContainer.innerHTML = ""; 
        let total = 0;

        if (cartItems.length === 0) {
            cartItemsContainer.innerHTML = "<p>Your cart is empty.</p>";
        } else {
            cartItems.forEach(item => {
                const itemElement = document.createElement("div");
                itemElement.className = "cart-item";
                const itemTotal = parseFloat(item.price) * item.quantity;
                total += itemTotal;
                itemElement.innerHTML = `
                    <h4>${item.title}</h4>
                    <p>Quantity: <input type="number" value="${item.quantity}" min="1" onchange="updateCartItem(${item.id}, this.value)"></p>
                    <p>Price: ${item.price} $</p>
                    <p>Subtotal: ${itemTotal.toFixed(2)} $</p>
                    <button onclick="removeFromCart(${item.id})">Remove</button>
                `;
                cartItemsContainer.appendChild(itemElement);
            });
        }

        cartTotalElement.textContent = `${total.toFixed(2)} $`;

    } catch (error) {
        console.error("Error loading cart:", error);
        cartItemsContainer.innerHTML = "<p>An error occurred while loading the cart.</p>";
        cartTotalElement.textContent = "0.00 $";
    }
}

async function updateCartItem(cartItemId, newQuantity) {
    const quantity = parseInt(newQuantity);
    if (isNaN(quantity) || quantity <= 0) {
        alert("Quantity must be a positive number.");
        loadCart(); 
        return;
    }

    console.log(`Updating cart item ${cartItemId} to quantity ${quantity}`);

    try {
        const response = await fetch(`php/cart_handler.php?id=${cartItemId}`, { 
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ quantity: quantity }),
        });

        const result = await response.json();

        if (response.ok) {
            console.log("Cart item updated successfully");
            loadCart(); 
        } else {
             if (response.status === 401) { 
                 localStorage.removeItem("isLoggedIn");
                 localStorage.removeItem("userData");
                 updateLoginStatusUI();
                 alert("Your session has expired. Please log in again.");
                 window.location.href = "LogIn.html";
                 return;
             }
            alert(`Failed to update the cart: ${result.message}`);
            loadCart(); 
        }
    } catch (error) {
        console.error("Update cart item error:", error);
        alert("An error occurred while trying to update the cart item.");
        loadCart(); 
    }
}

async function removeFromCart(cartItemId) {
    if (!confirm("Are you sure you want to remove this item from the cart?")) {
        return;
    }

    console.log(`Removing cart item ${cartItemId}`);

    try {
        const response = await fetch(`php/cart_handler.php?id=${cartItemId}`, { 
            method: "DELETE",
        });

        const contentType = response.headers.get("content-type");
        let result = {};
        if (contentType && contentType.indexOf("application/json") !== -1) {
            result = await response.json();
        } else {
            if (!response.ok) {
                 result.message = `Server error: ${response.statusText}`;
            }
        }

        if (response.ok) {
            console.log("Cart item removed successfully");
            loadCart(); 
        } else {
             if (response.status === 401) { 
                 localStorage.removeItem("isLoggedIn");
                 localStorage.removeItem("userData");
                 updateLoginStatusUI();
                 alert("Your session has expired. Please log in again.");
                 window.location.href = "LogIn.html";
                 return;
             }
            alert(`Failed to remove item from cart: ${result.message || 'Unknown error'}`);
        }
    } catch (error) {
        console.error("Remove from cart error:", error);
        alert("An error occurred while trying to remove the item from the cart.");
    }
}
