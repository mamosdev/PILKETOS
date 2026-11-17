// Alpine.js Countdown Component
document.addEventListener('alpine:init', () => {
    window.Alpine.data('countdown', (target) => ({
        left: '--:--:--',
        init() {
            this.tick();
            setInterval(() => this.tick(), 1000);
        },
        tick() {
            const s = Math.max(0, Math.floor((new Date(target) - Date.now()) / 1000));
            const h = String(Math.floor(s / 3600)).padStart(2, '0');
            const m = String(Math.floor((s % 3600) / 60)).padStart(2, '0');
            const sec = String(s % 60).padStart(2, '0');
            this.left = `${h}:${m}:${sec}`;
        },
    }));
});