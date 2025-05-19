<?php

include_once './header.php';
include_once './config/dbconn.php';
header('Content-Type: application/json');

$connect = conn();  // Get the DB connection from dbconn.php

$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'fetch_products':
        fetchProducts();
        break;
    case 'insert_product':
        insertProduct();
        break;
    case 'update_product':
        updateProduct();
        break;
    case 'delete_product':
        deleteProduct();
        break;
    case 'low_stock':
        fetchLowStockProducts();
        break;
    case 'scan_barcode':
        scanBarcode();
        break;
    case 'fetch_suppliers':
        fetchSuppliers();
        break;
    case 'fetch_categories':
        fetchCategories();
        break;
     case 'fetch_products_by_category':
        fetchProductsByCategory();
        break;
    case 'fetch_warehouses_with_products':
         fetchWarehousesWithProducts();
           break;
    case 'fetch_suppliers_with_products':
    fetchSuppliersWithProducts();
    break;
    case 'reduce_stock':
    reduceStock();
    break;

        default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        break;
}

// --- Function definitions ---

function fetchProducts() {
$connect = conn();

    $stmt = $connect->prepare("SELECT * FROM products");
    $stmt->execute();
    $result = $stmt->get_result();
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
    exit;
}

function insertProduct() {
    $connect = conn();

    // Check 
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pname'])) {
        $pname = $_POST['pname'] ?? '';
        $pcategory = $_POST['pcategory'] ?? '';
        $stock_quantity = $_POST['stock_quantity'] ?? '';
        $barcode = $_POST['barcode'] ?? '';
        $warehouse = $_POST['warehouse'] ?? '';
        
        
        if (empty($pname) || empty($pcategory) || $stock_quantity === '' || empty($barcode) || empty($warehouse)) {
            echo json_encode(['type' => 'error', 'message' => 'Missing required fields']);
            exit;
        }

        $image_path = null;

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileTmpPath = $_FILES['image']['tmp_name'];
            $fileName = basename($_FILES['image']['name']);
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];

            if (!in_array($fileExtension, $allowedExt)) {
                echo json_encode(['type' => 'error', 'message' => 'Invalid image type']);
                exit;
            }

            $newFileName = uniqid('prod_', true) . '.' . $fileExtension;
            $destPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $image_path = $newFileName;
            } else {
                echo json_encode(['type' => 'error', 'message' => 'Error uploading image']);
                exit;
            }
        }

        $stmt = $connect->prepare("INSERT INTO products (pname, pcategory, stock_quantity, barcode, warehouse, image_path) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisss", $pname, $pcategory, $stock_quantity, $barcode, $warehouse, $image_path);

        if ($stmt->execute()) {
            echo json_encode(['type' => 'success', 'message' => 'Product inserted']);
        } else {
            echo json_encode(['type' => 'error', 'message' => 'Insert failed']);
        }
    } else {
        echo json_encode(['type' => 'error', 'message' => 'Invalid request']);
    }

    exit;
}
function updateProduct() {
    $connect = conn();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['type' => 'error', 'message' => 'Invalid request method']);
        exit;
    }

    $pid = $_POST['pid'] ?? null;
    $pname = $_POST['pname'] ?? null;
    $pcategory = $_POST['pcategory'] ?? null;
    $stock_quantity = $_POST['stock_quantity'] ?? null;
    $barcode = $_POST['barcode'] ?? null;
    $warehouse = $_POST['warehouse'] ?? null;

    if (!$pid || !$pname || !$pcategory || $stock_quantity === null || !$barcode || !$warehouse) {
        echo json_encode(['type' => 'error', 'message' => 'Missing required fields']);
        exit;
    }

    $image_path = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = basename($_FILES['image']['name']);
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($fileExtension, $allowedExt)) {
            echo json_encode(['type' => 'error', 'message' => 'Invalid image type']);
            exit;
        }

        $newFileName = uniqid('prod_', true) . '.' . $fileExtension;
        $destPath = $uploadDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $image_path = $newFileName;
        } else {
            echo json_encode(['type' => 'error', 'message' => 'Error uploading image']);
            exit;
        }
    }

    if ($image_path) {
        $stmt = $connect->prepare("UPDATE products SET pname=?, pcategory=?, stock_quantity=?, barcode=?, warehouse=?, image_path=? WHERE pid=?");
        $stmt->bind_param("ssisssi", $pname, $pcategory, $stock_quantity, $barcode, $warehouse, $image_path, $pid);
    } else {
        $stmt = $connect->prepare("UPDATE products SET pname=?, pcategory=?, stock_quantity=?, barcode=?, warehouse=? WHERE pid=?");
        $stmt->bind_param("ssissi", $pname, $pcategory, $stock_quantity, $barcode, $warehouse, $pid);
    }

    if ($stmt->execute()) {
        echo json_encode(['type' => 'success', 'message' => 'Product updated']);
    } else {
        echo json_encode(['type' => 'error', 'message' => 'Update failed']);
    }
    exit;
}


function deleteProduct() {
    $connect = conn();

    $data = json_decode(file_get_contents('php://input'), true);

    $stmt = $connect->prepare("DELETE FROM products WHERE pid=?");
    $stmt->bind_param("i", $data['pid']);

    if ($stmt->execute()) {
        echo json_encode(['type' => 'success', 'message' => 'Product deleted']);
    } else {
        echo json_encode(['type' => 'error', 'message' => 'Delete failed']);
    }
    exit;
}
function fetchLowStockProducts() {
    global $connect;

    // Set the low stock threshold — change this value if needed
    $threshold = 10;

    $stmt = $connect->prepare("
        SELECT 
            p.pid, 
            p.pname, 
            p.stock_quantity, 
            p.image_path,
            w.name AS warehouse_name
        FROM products p
        JOIN warehouses w ON p.warehouse = w.id
        WHERE p.stock_quantity < ?
    ");

    $stmt->bind_param("i", $threshold);
    $stmt->execute();
    $result = $stmt->get_result();

    $lowStockProducts = [];
    while ($row = $result->fetch_assoc()) {
        // Include full image URL
        $row['image_url'] = 'uploads/' . ($row['image_path'] ?? 'default.png');
        $lowStockProducts[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($lowStockProducts);
    exit;
}


function scanBarcode() {
    $connect = conn();

    $code = isset($_GET['barcode']) ? $_GET['barcode'] : '';

    $stmt = $connect->prepare("SELECT * FROM products WHERE barcode = ?");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();

    $product = $result->fetch_assoc();

    if ($product) {
        echo json_encode(['type' => 'success', 'data' => $product]);
    } else {
        echo json_encode(['type' => 'error', 'message' => 'Product not found']);
    }
    exit;
}

function fetchSuppliers() {
    $connect = conn();

    $stmt = $connect->prepare("SELECT id, name, contact_name, contact_email, phone, address FROM suppliers");
    $stmt->execute();
    $result = $stmt->get_result();

    $suppliers = [];
    while ($row = $result->fetch_assoc()) {
        $suppliers[] = $row;
    }

    echo json_encode($suppliers);
    exit;
}
function fetchCategories() {
    $connect = conn();

    $stmt = $connect->prepare("SELECT id, name FROM categories");
    $stmt->execute();
    $result = $stmt->get_result();

    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }

    echo json_encode($categories);
    exit; // 🚨 IMPORTANT: Stop script execution after response
}

function fetchProductsByCategory() {
    $connect = conn();

    $categoryId = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

    $stmt = $connect->prepare("SELECT pid, pname, image_path FROM products WHERE pcategory = ?");
    $stmt->bind_param("i", $categoryId);
    $stmt->execute();
    $result = $stmt->get_result();

    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    echo json_encode($products);
    exit;
}
function fetchWarehousesWithProducts() {
    global $connect;

    $stmt = $connect->prepare("SELECT id, name, address FROM warehouses");
    $stmt->execute();
    $warehousesResult = $stmt->get_result();

    $warehouses = [];
    while ($warehouse = $warehousesResult->fetch_assoc()) {
        $stmt2 = $connect->prepare("SELECT pid, pname, stock_quantity, image_path FROM products WHERE warehouse = ?");
        $stmt2->bind_param("i", $warehouse['id']);
        $stmt2->execute();
        $productsResult = $stmt2->get_result();

        $products = [];
       while ($product = $productsResult->fetch_assoc()) {
    if (!empty($product['image_path']) && file_exists(__DIR__ . '/uploads/' . $product['image_path'])) {
        $product['image_url'] = 'uploads/' . $product['image_path'];
    } else {
        $product['image_url'] = 'uploads/default.png';
    }
    $products[] = $product;
}


        $warehouse['products'] = $products;
        $warehouses[] = $warehouse;
    }

    header('Content-Type: application/json');
    echo json_encode($warehouses);
    exit;
}
function fetchSuppliersWithProducts() {
    global $connect;

    $query = "
        SELECT 
            s.id AS supplier_id,
            s.name AS supplier_name,
            s.contact_email,
            s.phone AS phone_number,
            s.address,
            p.pid,
            p.pname,
            p.stock_quantity,
            p.image_path
        FROM suppliers s
        LEFT JOIN products p ON s.id = p.supplier_id
        ORDER BY s.id
    ";

    $result = mysqli_query($connect, $query);

    $suppliers = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $id = $row['supplier_id'];
        if (!isset($suppliers[$id])) {
            $suppliers[$id] = [
                'id' => $id,
                'name' => $row['supplier_name'],
                'contact_email' => $row['contact_email'],
                'phone_number' => $row['phone_number'],
                'address' => $row['address'],
                'products' => []
            ];
        }

        // Add product only if it exists (some suppliers may not have products yet)
        if (!empty($row['pid'])) {
            $row['image_url'] = file_exists(__DIR__ . '/uploads/' . $row['image_path'])
                ? 'uploads/' . $row['image_path']
                : 'uploads/default.png';

            $suppliers[$id]['products'][] = [
                'pid' => $row['pid'],
                'pname' => $row['pname'],
                'stock_quantity' => $row['stock_quantity'],
                'image_url' => $row['image_url']
            ];
        }
    }

    echo json_encode(array_values($suppliers));
    exit;
}
function reduceStock() {
    $connect = conn();

    // Get JSON POST input
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['pid']) || !isset($data['amount'])) {
        echo json_encode(['type' => 'error', 'message' => 'Missing product ID or amount']);
        exit;
    }

    $pid = intval($data['pid']);
    $amount = intval($data['amount']);
    if ($amount <= 0) {
        echo json_encode(['type' => 'error', 'message' => 'Amount must be positive']);
        exit;
    }

    // Get current stock
    $stmt = $connect->prepare("SELECT stock_quantity FROM products WHERE pid = ?");
    $stmt->bind_param("i", $pid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['type' => 'error', 'message' => 'Product not found']);
        exit;
    }

    $row = $result->fetch_assoc();
    $currentStock = intval($row['stock_quantity']);

    if ($currentStock < $amount) {
        echo json_encode(['type' => 'error', 'message' => 'Not enough stock to reduce']);
        exit;
    }

    $newStock = $currentStock - $amount;

    // Update stock
    $updateStmt = $connect->prepare("UPDATE products SET stock_quantity = ? WHERE pid = ?");
    $updateStmt->bind_param("ii", $newStock, $pid);

    if ($updateStmt->execute()) {
        echo json_encode(['type' => 'success', 'message' => 'Stock reduced', 'new_stock' => $newStock]);
    } else {
        echo json_encode(['type' => 'error', 'message' => 'Failed to update stock']);
    }
    exit;
}

?>
