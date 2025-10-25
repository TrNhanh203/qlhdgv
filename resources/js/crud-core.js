window.CRUD = {
    csrf: document.querySelector('meta[name=csrf-token]')?.content,
    toast(msg, ok = true) { alert(msg); },
    async postJson(url, data) {
        const res = await fetch(url, {
            method: "POST",
            credentials: 'same-origin',
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": this.csrf
            },
            body: JSON.stringify(data)
        });
        return res.ok ? res.json() : Promise.reject(await res.text());
    },
    getSelectedIds() { return Array.from(document.querySelectorAll('.row-check:checked')).map(cb => cb.value); },
    toggleAll(main, cls) { document.querySelectorAll(cls).forEach(cb => cb.checked = main.checked); }
};
