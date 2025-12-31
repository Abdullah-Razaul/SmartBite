<?php
session_start();
if(!isset($_SESSION['owner_id'])){
    header("Location: login.html");
    exit();
}
include "../config/db.php";
 // connect to database
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Dashboard</title>

    <style>
        body {
            margin: 0;
            font-family: Arial;
            background: #f3f4f6;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }


        .sidebar {
            width: 200px;
            background: #FC9300;
            color: white;
            padding: 20px;
        }

        @media (max-width: 768px) {
            .layout {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                padding: 10px;
            }
            .sidebar h2 {
                font-size: 1.2em;
            }
            .nav a {
                display: inline-block;
                margin-right: 15px;
            }
            .card-box {
                flex-direction: column;
            }
            .main {
                padding: 10px;
            }
        }

        .sidebar h2 {
            margin-top: 0;
        }

        .nav a {
            display: block;
            padding: 8px 0;
            color: black;
            text-decoration: none;
        }

        .nav a:hover {
            color: white;
        }


        .main {
            flex: 1;
            padding: 20px;
        }

        .card-box {
            display: flex;
            gap: 10px;
        }

        .card {
            flex: 1;
            background: white;
            padding: 15px;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border-bottom: 1px solid #eee;
            padding: 8px;
            font-size: 14px;
        }

        .btn {
            padding: 5px 10px;
            border: 0;
            background: #FC9300;
            color: white;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-small {
            background: #6b7280;
        }

        .menu-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
    </style>
</head>

<body>

    <div class="layout">

        <!-- Sidebar -->
        <div class="sidebar">
            <h2>My Restaurant</h2>
            <div class="nav">
                <a href="#">Dashboard</a>
                <a href="#">Orders</a>
                <a href="#">Profile</a>
                <a href="#" onclick="logout()">Logout</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main">
            <h1>Dashboard</h1>
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['owner_name']); ?>!</p>

            <!-- upper card box -->
            <div class="card-box">
                <div class="card">
                    <h3>Today's Orders</h3>
                    <p id="todayOrders"><b>0</b></p>
                </div>
                <div class="card">
                    <h3>Pending</h3>
                    <p id="pending"><b>0</b></p>
                </div>
                <div class="card">
                    <h3>Preparing</h3>
                    <p id="preparing"><b>0</b></p>
                </div>
                <div class="card">
                    <h3>Ready</h3>
                    <p id="ready"><b>0</b></p>
                </div>
            </div>

            <br>

            <!-- Live Orders Table -->
            <div class="card">
                <h3>Live Orders</h3>
                <table id="orderTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Items</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <br>

            <!-- Menu Manager -->
            <div class="card">
                <h3>Menu Manager</h3>

                <div id="menuList"></div>

                <hr>

                <h4>Add New Item</h4>
                <input type="text" id="newItem" placeholder="Item name"> <br><br>
                <input type="number" id="newPrice" placeholder="Price"> <br><br>
                <button class="btn" onclick="addMenuItem()">Add Item</button>
            </div>

        </div>
    </div>

    <script>
let menu = [];

// Fetch menu from database
function fetchMenu() {
    fetch('get_menu.php')
    .then(res => res.json())
    .then(data => {
        menu = data;
        showMenu();
    })
    .catch(err => {
        console.log('Error fetching menu:', err);
        alert('Failed to load menu. Please refresh the page.');
    });
}

// Render menu
function showMenu() {
    let box = document.getElementById("menuList");
    box.innerHTML = "";
    menu.forEach(m=>{
        box.innerHTML += `
            <div class="menu-item">
                <span>${m.name} - ৳${m.price}</span>
                <button class="btn-small" onclick="deleteItem(${m.id})">Delete</button>
            </div>
        `;
    });
}

// Add menu item
function addMenuItem() {
    let name = document.getElementById("newItem").value.trim();
    let price = document.getElementById("newPrice").value.trim();
    if(!name || !price) return alert("Please fill all fields");

    fetch('add_menu.php',{
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:`name=${encodeURIComponent(name)}&price=${encodeURIComponent(price)}`
    }).then(res=>res.json())
      .then(data=>{
          if(data.success){
              fetchMenu(); // refresh
              document.getElementById("newItem").value="";
              document.getElementById("newPrice").value="";
          } else {
              alert(data.message || 'Failed to add item');
          }
      })
      .catch(err => {
          console.log('Error adding item:', err);
          alert('Failed to add item. Please try again.');
      });
}

// Delete menu item
function deleteItem(id){
    if (!confirm('Are you sure you want to delete this item?')) return;

    fetch('delete_menu.php',{
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:`id=${id}`
    }).then(res=>res.json())
      .then(data=>{
          if(data.success) {
              fetchMenu();
          } else {
              alert(data.message || 'Failed to delete item');
          }
      })
      .catch(err => {
          console.log('Error deleting item:', err);
          alert('Failed to delete item. Please try again.');
      });
}

// Initialize dashboard
fetchMenu();

// Logout function
function logout() {
    if (confirm('Are you sure you want to logout?')) {
        window.location.href = 'logout.php';
    }
}

    </script>

</body>

</html>