<?php
include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" />
    <title>Chatbot</title>
    <style>
        /* CSS for chat container */
        .chat-container {
            padding-top: 60px; /* Điều chỉnh cho phù hợp với chiều cao của .select-action-container */
            max-width: 800px;
            margin: 0 auto;
            margin-top: 70px;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 10px;
            background-color: #f7f7f8;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        

        /* CSS for chat box */
        .chat-box {
            max-height: 600px; /* Thay đổi kích thước khung chat */
            overflow-y: auto;
            padding: 10px;
            background-color: #ffffff; /* White background for chat messages */
            border-radius: 10px;
        }

        /* CSS for user input */
        .user-input {
            width: calc(100% - 80px);
            padding: 10px;
            margin-top: 10px;
            border-radius: 20px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        /* CSS for send button */
        #send-button {
            width: 70px;
            margin-top: 10px;
            padding: 10px;
            border: none;
            border-radius: 20px;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            font-size: 16px;
            margin-left: 10px;
        }

        /* CSS for chat messages */
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 10px;
            max-width: 75%;
        }

        /* CSS for user messages */
        .user-message {
            background-color: #dcf8c6; /* Light green for user messages */
            text-align: right;
            margin-left: auto;
            font-size: 18px;
        }
        .user-text {
            margin: 0;
            padding: 0;
        }

        /* CSS for bot messages */
        .bot-message {
            background-color: #f1f0f0; /* Light gray for bot messages */
            text-align: left;
            margin-right: auto;
            font-size: 18px;
        }
        .bot-text {
            margin: 0;
            padding: 0;
        }

        /* CSS for chatbot image */
        .chatbot-image {
            max-width: 100%;
            height: auto;
            margin-top: 5px;
            border-radius: 5px;
        }

        /* CSS for overall layout */
        .input-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px;
            background-color: #ffffff; /* White background for input area */
            /*border-top: 1px solid #ccc;*/
            border-radius: 0 0 10px 10px;
        }

        .chat-box::-webkit-scrollbar {
            width: 8px;
        }

        .chat-box::-webkit-scrollbar-thumb {
            background-color: #ccc;
            border-radius: 10px;
        }

        /* CSS for the dropdown */
        .select-action-container {
            position: fixed; /* Cố định vị trí */
            top: 0; /* Ở đầu trang */
            left: 61%;
            transform: translateX(-50%);
            width: 100%;
            border-radius: 10px;
            max-width: 1070px;
            background-color: #ffffff; /* Màu nền trắng */
            padding: 20px;
            z-index: 1000; /* Đảm bảo nó luôn ở trên cùng */
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); /* Shadow for better appearance */
        }
        
        .select-action {
            width: calc(100% - 40px); /* Giảm chiều rộng để mũi tên không sát bên phải */
            padding: 10px;
            border-radius: 20px;
            border: 1px solid #ccc;
            font-size: 16px;
            margin-left: 20px; /* Thêm khoảng cách từ trái */
        }
    </style>
</head>
<body>

<?php include 'components/user_header.php'; ?>

    <div class="select-action-container">
        <select id="action" class="select-action">
            <option value="search">Tìm kiếm nội dung bài giảng hoặc tóm tắt</option>
            <option value="answer">Trả lời câu hỏi trong bài giảng</option>
        </select>
    </div>

    <div class="chat-container">
        <div class="chat-box" id="chat-box"></div>
        <div class="input-container">
            <input type="text" id="user_message" name="user_message" class="user-input" placeholder="Nhập nội dung...">
            <button id="send-button" onclick="sendMessage()">Send</button>
        </div>
    </div>

    <script>
        function sendMessage() {
            var userMessage = document.getElementById("user_message").value;
            var action = document.getElementById("action").value;

            if (userMessage.trim() === "") return; // Prevent sending empty messages

            var chatBox = document.getElementById("chat-box");
            chatBox.innerHTML += "<div class='message user-message'><p class='user-text'>" + userMessage + "</p></div>";
            document.getElementById("user_message").value = "";
            chatBox.scrollTop = chatBox.scrollHeight;

            var apiUrl = action === "search" ? 'http://127.0.0.1:5000/get_response' : 'http://127.0.0.1:5000/answer';

            fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ user_message: userMessage, mode: action })
            })
            .then(response => response.json())
            .then(data => {
                var responseHTML = "<div class='message bot-message'><p class='bot-text'>" + data.text + "</p></div>";
                if (data.image) {
                    responseHTML += "<img src='" + "/output/Baigiangcosodulieu01Gioithieumonhoc/image_at_00_10-00_20.jpg" + "' alt='Chatbot Image' class='chatbot-image'>";
                }
                chatBox.innerHTML += responseHTML;
                chatBox.scrollTop = chatBox.scrollHeight;
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        document.getElementById("user_message").addEventListener("keydown", function(event) {
            if (event.key === "Enter") {
                sendMessage();
            }
        });
    </script>
</body>
</html>
