


// const CRUD = {
//     async postJson(url, data) {
//         const token = document
//             .querySelector('meta[name="csrf-token"]')
//             ?.getAttribute('content');

//         const res = await fetch(url, {
//             method: 'POST',
//             headers: {
//                 'Content-Type': 'application/json',
//                 'X-CSRF-TOKEN': token,
//             },
//             body: JSON.stringify(data),
//         });

//         return res.json();
//     },


//     toast(msg, ok = true) {
//         const symbol = ok ? '✅' : '❌';
//         alert(symbol + ' ' + msg);
//     },

//     getSelectedIds() {
//         return Array.from(document.querySelectorAll('.row-check:checked')).map(i => i.value);
//     },

//     toggleAll(master, selector) {
//         document.querySelectorAll(selector).forEach(i => (i.checked = master.checked));
//     },
// };

// window.CRUD = CRUD;
// console.log("✅ CRUD core loaded");


const CRUD = {
    async postJson(url, data) {
        const token = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute('content');

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
                body: JSON.stringify(data),
            });

            // Nếu không phải JSON (vd Laravel trả view lỗi), tránh crash
            const text = await res.text();
            try {
                return JSON.parse(text);
            } catch {
                console.error("⚠️ Response không phải JSON:", text);
                return { success: false, message: "Phản hồi không hợp lệ từ server", raw: text };
            }
        } catch (err) {
            console.error("🚨 Fetch error:", err);
            return { success: false, message: err.message || "Lỗi khi gửi request" };
        }
    },

    //Hiển thị alet đơn giản 
    // toast(msg, ok = true) {
    //     const symbol = ok ? "✅" : "❌";
    //     alert(symbol + " " + msg);
    // },

    //toast nâng cao sử dụng Bootstrap Toast (cần có container <div id="toastArea"> trong HTML )
    toast(msg, ok = true) {
        const container = document.getElementById('toastArea');
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white ${ok ? 'bg-success' : 'bg-danger'} border-0 show mb-2`;
        toast.role = 'alert';
        toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${ok ? '✅' : '❌'} ${msg}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>`;
        container.appendChild(toast);
        setTimeout(() => toast.remove(), 2500);
    },

    getSelectedIds() {
        return Array.from(document.querySelectorAll(".row-check:checked")).map(i => i.value);
    },

    toggleAll(master, selector) {
        document.querySelectorAll(selector).forEach(i => (i.checked = master.checked));
    },
};

window.CRUD = CRUD;
console.log("✅ CRUD core loaded");
