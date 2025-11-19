document.addEventListener("DOMContentLoaded", () => {
    const chatbotToggler = document.querySelector(".chatbot-toggler");
    const closeBtn = document.querySelector(".close-btn");
    const chatbox = document.querySelector(".chatbox");
    const chatInput = document.querySelector(".chat-input textarea");
    const sendChatBtn = document.querySelector(".chat-input span");

    if (!chatbotToggler) {
        console.error("Chatbot toggler not found!");
        return;
    }

    let userMessage = null; // Variable to store user's message
    const inputInitHeight = chatInput.scrollHeight;

    const createChatLi = (message, className) => {
        // Create a chat <li> element with passed message and className
        const chatLi = document.createElement("li");
        chatLi.classList.add("chat", className);
        let chatContent = className === "outgoing" ? `<p></p>` : `<span class="material-symbols-outlined">smart_toy</span><div class="chat-details"><p></p></div>`;
        chatLi.innerHTML = chatContent;
        chatLi.querySelector("p").textContent = message;
        return chatLi; // return chat <li> element
    }

    const generateResponse = async (chatElement) => {
        const API_URL = "chatbot_api.php"; // Point to our PHP proxy
        const messageElement = chatElement.querySelector("p");

        // Define the properties and message for the API request
        const requestOptions = {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                message: userMessage
            })
        }

        // Send POST request to API, get response and set the reponse as paragraph text
        try {
            const response = await fetch(API_URL, requestOptions);
            const data = await response.json();
            if (!response.ok) throw new Error(data.error || response.statusText);

            let botMessage = data.reply.trim();

            // Parse JSON for Rich UI
            const jsonRegex = /###JSON_START###([\s\S]*?)###JSON_END###/;
            const match = botMessage.match(jsonRegex);

            let productCardHtml = '';
            if (match) {
                try {
                    const jsonStr = match[1];
                    const jsonData = JSON.parse(jsonStr);
                    botMessage = botMessage.replace(jsonRegex, '').trim(); // Remove JSON from text

                    if (jsonData.type === 'product') {
                        productCardHtml = renderProductCard(jsonData.data);
                    }
                } catch (e) {
                    console.error("Error parsing JSON:", e);
                }
            }

            messageElement.textContent = botMessage;
            if (productCardHtml) {
                const cardDiv = document.createElement('div');
                cardDiv.innerHTML = productCardHtml;
                chatElement.querySelector(".chat-details").appendChild(cardDiv);
            }

        } catch (error) {
            messageElement.classList.add("error");
            messageElement.textContent = error.message;
        } finally {
            chatbox.scrollTo(0, chatbox.scrollHeight);
        }
    }

    const renderProductCard = (product) => {
        // Create size options if available
        let sizeOptions = '';
        if (product.sizes && product.sizes.length > 0) {
            sizeOptions = `<select class="card-size-select" id="size-${product.masp}">
                ${product.sizes.map(s => `<option value="${s}">${s}</option>`).join('')}
            </select>`;
        } else {
            sizeOptions = '<input type="hidden" value="" id="size-' + product.masp + '">';
        }

        return `
            <div class="product-card">
                <img src="${product.img}" alt="${product.tensp}">
                <div class="card-info">
                    <h4>${product.tensp}</h4>
                    <p class="price">${product.gia} VNĐ</p>
                    ${sizeOptions}
                    <button onclick="addToCart('${product.masp}')">Thêm vào giỏ</button>
                </div>
            </div>
        `;
    }

    // Expose addToCart to global scope
    window.addToCart = (masp) => {
        const sizeSelect = document.getElementById(`size-${masp}`);
        const size = sizeSelect ? sizeSelect.value : '';

        if (!size && document.querySelector(`#size-${masp}`).type !== 'hidden') {
            alert("Vui lòng chọn size!");
            return;
        }

        const btn = document.querySelector(`button[onclick="addToCart('${masp}')"]`);
        const originalText = btn.textContent;
        btn.textContent = "Đang thêm...";
        btn.disabled = true;

        fetch(`add_giohang.php?masp=${masp}&size=${size}&soluong=1`)
            .then(response => {
                if (response.redirected && response.url.includes('shopping-cart.php')) {
                    // Success (redirected to cart)
                    btn.textContent = "Đã thêm ✔";
                    btn.style.backgroundColor = "#2ecc71";
                } else {
                    // Check if response text indicates login required
                    return response.text().then(text => {
                        if (text.includes("Bạn cần đăng nhập")) {
                            alert("Bạn cần đăng nhập để mua hàng!");
                            btn.textContent = originalText;
                            btn.disabled = false;
                        } else {
                            // Assume success if no error found
                            btn.textContent = "Đã thêm ✔";
                            btn.style.backgroundColor = "#2ecc71";
                        }
                    });
                }
            })
            .catch(err => {
                console.error(err);
                alert("Có lỗi xảy ra!");
                btn.textContent = originalText;
                btn.disabled = false;
            });
    };

    const handleChat = () => {
        userMessage = chatInput.value.trim(); // Get user entered message and remove extra whitespace
        if (!userMessage) return;

        // Clear the input textarea and set its height to default
        chatInput.value = "";
        chatInput.style.height = `${inputInitHeight}px`;

        // Append the user's message to the chatbox
        chatbox.appendChild(createChatLi(userMessage, "outgoing"));
        chatbox.scrollTo(0, chatbox.scrollHeight);

        setTimeout(() => {
            // Display "Thinking..." message while waiting for the response
            const incomingChatLi = createChatLi("Thinking...", "incoming");
            chatbox.appendChild(incomingChatLi);
            generateResponse(incomingChatLi);
        }, 600);
    }

    chatInput.addEventListener("input", () => {
        // Adjust the height of the input textarea based on its content
        chatInput.style.height = `${inputInitHeight}px`;
        chatInput.style.height = `${chatInput.scrollHeight}px`;
    });

    chatInput.addEventListener("keydown", (e) => {
        // If Enter key is pressed without Shift key and the window 
        // width is greater than 800px, handle the chat
        if (e.key === "Enter" && !e.shiftKey && window.innerWidth > 800) {
            e.preventDefault();
            handleChat();
        }
    });

    sendChatBtn.addEventListener("click", handleChat);
    closeBtn.addEventListener("click", () => document.body.classList.remove("show-chatbot"));
    chatbotToggler.addEventListener("click", () => document.body.classList.toggle("show-chatbot"));
});
