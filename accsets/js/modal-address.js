const modal = document.querySelector(".modal-address");
const editBtn = document.querySelector(".js-edit-address");

// Mở modal
if (editBtn) {
    editBtn.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        modal.style.display = "block";
    });
}

// Đóng modal khi click vào nút close
document.addEventListener("click", function (e) {
    if (e.target.closest('.close')) {
        modal.style.display = "none";
    }
});

// Đóng modal khi click ra ngoài modal-content
window.addEventListener("click", function (e) {
    if (e.target === modal) {
        modal.style.display = "none";
    }
});

// Ngăn sự kiện nổi bọt khi click bên trong nội dung modal
document.addEventListener("click", function (e) {
    if (e.target.closest('.modal-address-content')) {
        e.stopPropagation();
    }
});

// Xử lý submit form - sử dụng event delegation
document.addEventListener('submit', function (e) {
    // Kiểm tra xem form có nằm trong modal không
    if (e.target.closest('#modalAddress')) {
        e.preventDefault(); // Không reload trang
        const form = e.target;
        const formData = new FormData(form);

        fetch('update_address.php', {
            method: 'POST',
            body: formData
        })
            .then(res => res.text())
            .then(data => {
                if (data.trim() === "success") {
                    alert("Cập nhật thành công!");
                    modal.style.display = "none";
                    location.reload(); // Reload để hiển thị thông tin mới
                } else {
                    alert("Có lỗi xảy ra: " + data);
                }
            })
            .catch(error => {
                alert("Có lỗi xảy ra: " + error);
            });
    }
});