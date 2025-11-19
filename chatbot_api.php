<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'env_loader.php';

// Replace with your actual MegaLLM API Key
define('MEGALLM_API_KEY', getenv('MEGALLM_API_KEY'));
// Base URL is https://ai.megallm.io/v1, so we append /chat/completions
define('MEGALLM_API_URL', 'https://ai.megallm.io/v1/chat/completions');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $userMessage = $input['message'] ?? '';

    if (empty($userMessage)) {
        echo json_encode(['error' => 'Message is required']);
        exit;
    }

    // 0. Personalization
    $userName = isset($_SESSION['name']) ? $_SESSION['name'] : 'Khách';
    $userContext = "User Name: $userName";

    // 1. Fetch Product Data & Stock from Database
    $host = getenv('DB_HOST');
    $dbname = getenv('DB_NAME');
    $username = getenv('DB_USER');
    $password = getenv('DB_PASS');

    $conn = new mysqli($host, $username, $password, $dbname);
    $conn->set_charset("utf8");

    $systemContext = "";

    if (!$conn->connect_error) {
        // --- A. Product & Stock Context ---
        $productContext = "Danh sách sản phẩm và tồn kho tại BT Shop:\n";
        // Added s.masp and s.url to query
        $sql = "SELECT s.masp, s.tensp, s.gia, s.danhmuc, s.kieu, s.url, sz.size, sz.soluong 
                FROM sanpham s 
                LEFT JOIN size_sanpham sz ON s.masp = sz.masp 
                ORDER BY s.tensp, sz.size";
        $result = $conn->query($sql);

        $products = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $name = $row['tensp'];
                if (!isset($products[$name])) {
                    $products[$name] = [
                        'id' => $row['masp'],
                        'info' => "{$row['danhmuc']} - {$row['kieu']} - " . number_format($row['gia'], 0, ',', '.') . " VNĐ",
                        'img' => $row['url'],
                        'price_raw' => $row['gia'],
                        'stock' => []
                    ];
                }
                if ($row['size']) {
                    $products[$name]['stock'][] = "{$row['size']}(SL:{$row['soluong']})";
                }
            }
            foreach ($products as $name => $data) {
                $stockStr = implode(", ", $data['stock']);
                // Format: - Name [ID: ...] [Img: ...] (Info) [Stock: ...]
                $productContext .= "- $name [ID: {$data['id']}] [Img: {$data['img']}] ({$data['info']}) [Size: $stockStr]\n";
            }
        } else {
            $productContext .= "Hiện không có sản phẩm nào.\n";
        }
        $systemContext .= $productContext . "\n";

        // --- B. Order Tracking Context ---
        // Check if user message contains an Order ID (e.g., HD001)
        if (preg_match('/(HD\d+)/i', $userMessage, $matches)) {
            $orderId = $matches[1];
            $stmt = $conn->prepare("SELECT trangthai, trigia, ngayHD FROM hoadon WHERE soHD = ?");
            $stmt->bind_param("s", $orderId);
            $stmt->execute();
            $resultOrder = $stmt->get_result();
            
            if ($rowOrder = $resultOrder->fetch_assoc()) {
                $date = date("d/m/Y", strtotime($rowOrder['ngayHD']));
                $price = number_format($rowOrder['trigia'], 0, ',', '.');
                $systemContext .= "THÔNG TIN ĐƠN HÀNG {$orderId}:\n";
                $systemContext .= "- Trạng thái: {$rowOrder['trangthai']}\n";
                $systemContext .= "- Ngày đặt: {$date}\n";
                $systemContext .= "- Tổng tiền: {$price} VNĐ\n";
                $systemContext .= "Hãy thông báo trạng thái này cho khách hàng.\n\n";
            } else {
                $systemContext .= "THÔNG TIN ĐƠN HÀNG {$orderId}: Không tìm thấy đơn hàng này trong hệ thống.\n\n";
            }
            $stmt->close();
        }

        $conn->close();
    } else {
        $systemContext .= "Lỗi kết nối cơ sở dữ liệu.\n";
    }

    // --- C. Policy & Size Guide Context ---
    // Read policy from file (simple strip tags)
    $policyContent = "";
    if (file_exists('dieukhoandichvu.php')) {
        $rawPolicy = file_get_contents('dieukhoandichvu.php');
        // Extract content between <body> tags to avoid head metadata
        if (preg_match('/<body>(.*?)<\/body>/s', $rawPolicy, $matches)) {
            $policyContent = strip_tags($matches[1]);
            // Clean up excess whitespace
            $policyContent = preg_replace('/\s+/', ' ', $policyContent);
            $policyContent = substr($policyContent, 0, 1000); // Limit length to save tokens
        }
    }
    
    $sizeGuide = "HƯỚNG DẪN CHỌN SIZE:\n";
    $sizeGuide .= "- Size S: Dưới 55kg, Cao dưới 1m65\n";
    $sizeGuide .= "- Size M: 55-65kg, Cao 1m65-1m70\n";
    $sizeGuide .= "- Size L: 65-75kg, Cao 1m70-1m75\n";
    $sizeGuide .= "- Size XL: Trên 75kg, Cao trên 1m75\n";

    // 2. Construct System Prompt
    $systemPrompt = "You are a helpful shopping assistant for BT Shop.
    
    USER INFO:
    $userContext
    
    CONTEXT DATA:
    $systemContext
    
    SIZE GUIDE:
    $sizeGuide
    
    STORE POLICY SUMMARY:
    $policyContent
    
    INSTRUCTIONS:
    - Greet the user by name if known.
    - Use the provided Product List to answer price/stock questions.
    - Use the Size Guide to advise on sizes.
    - Use the Policy Summary to answer return/warranty questions.
    - If Order Information is provided above, report it to the user.
    
    RICH UI INSTRUCTIONS (IMPORTANT):
    - When you recommend a specific product that is available, you MUST output a JSON block to display a product card.
    - Format: ###JSON_START### {\"type\": \"product\", \"data\": {\"masp\": \"...\", \"tensp\": \"...\", \"gia\": \"...\", \"img\": \"...\", \"sizes\": [\"S\", \"M\"]}} ###JSON_END###
    - Only use the JSON format for products that exist in the Context Data.
    - Do NOT output JSON if the product is out of stock.
    
    Be polite, concise, and helpful.
    ";

    $data = [
        'model' => 'llama3.3-70b-instruct',
        'messages' => [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userMessage]
        ],
        // 'temperature' => 0.7 // Optional
    ];

    $ch = curl_init(MEGALLM_API_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . MEGALLM_API_KEY
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        echo json_encode(['error' => 'Curl error: ' . curl_error($ch)]);
    } else {
        if ($httpCode === 200) {
            $decodedResponse = json_decode($response, true);
            // Adapt this based on the actual MegaLLM response structure (usually OpenAI compatible)
            $botReply = $decodedResponse['choices'][0]['message']['content'] ?? 'Sorry, I could not understand the response.';
            echo json_encode(['reply' => $botReply]);
        } else {
            echo json_encode(['error' => 'API Error: ' . $response]);
        }
    }

    curl_close($ch);
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
