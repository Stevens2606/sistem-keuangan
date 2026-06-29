@if(in_array(Auth::user()->role, ['admin', 'bendahara']))
<div class="relative" id="notifikasi-bell-wrapper">
    <button type="button" onclick="toggleNotifikasiDropdown()"
        class="relative p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        <span id="notifikasi-badge"
            class="absolute -top-1 -right-1 hidden min-w-[18px] h-[18px] px-1 text-[10px] font-bold text-white bg-red-500 rounded-full flex items-center justify-center">
            0
        </span>
    </button>

    <div id="notifikasi-dropdown"
        class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-100 z-50 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
            <span class="text-sm font-semibold text-gray-700">Notifikasi Anggaran</span>
            <span id="notifikasi-live-dot" class="hidden items-center gap-1 text-[10px] text-green-600">
                <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Live
            </span>
        </div>
        <div id="notifikasi-list" class="max-h-80 overflow-y-auto divide-y divide-gray-100">
            <p class="text-sm text-gray-400 text-center py-8">Memuat notifikasi...</p>
        </div>
    </div>
</div>

<script>
    function toggleNotifikasiDropdown() {
        document.getElementById('notifikasi-dropdown').classList.toggle('hidden');
    }

    // Tutup dropdown saat klik di luar
    document.addEventListener('click', function (e) {
        const wrapper = document.getElementById('notifikasi-bell-wrapper');
        if (wrapper && !wrapper.contains(e.target)) {
            document.getElementById('notifikasi-dropdown').classList.add('hidden');
        }
    });

    function renderNotifikasiItem(n) {
        const warna = n.melebihi ? 'red' : 'yellow';
        const label = n.melebihi ? 'MELEBIHI ANGGARAN' : 'Mendekati Batas';
        return `
            <div class="px-4 py-3 hover:bg-gray-50">
                <p class="text-sm font-semibold text-${warna}-700">${n.kategori} — ${label}</p>
                <p class="text-xs text-gray-500 mt-0.5">
                    Realisasi Rp ${Number(n.realisasi).toLocaleString('id-ID')}
                    dari Rp ${Number(n.anggaran).toLocaleString('id-ID')} (${n.persentase}%)
                </p>
            </div>
        `;
    }

    function updateBadge(count) {
        const badge = document.getElementById('notifikasi-badge');
        if (count > 0) {
            badge.textContent = count > 9 ? '9+' : count;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }

    // 1. Muat notifikasi yang sudah aktif saat halaman pertama dibuka
    fetch('{{ route('notifikasi.anggaran-aktif') }}')
        .then(res => res.json())
        .then(json => {
            const list = document.getElementById('notifikasi-list');
            updateBadge(json.count);

            if (json.count === 0) {
                list.innerHTML = '<p class="text-sm text-gray-400 text-center py-8">Tidak ada notifikasi</p>';
                return;
            }
            list.innerHTML = json.data.map(renderNotifikasiItem).join('');
        })
        .catch(() => {
            document.getElementById('notifikasi-list').innerHTML =
                '<p class="text-sm text-red-400 text-center py-8">Gagal memuat notifikasi</p>';
        });

    // 2. Dengarkan notifikasi baru secara real-time via Reverb (WebSocket)
    if (window.Echo) {
        window.Echo.private('notifikasi.keuangan')
            .listen('.anggaran.notifikasi', (n) => {
                const list = document.getElementById('notifikasi-list');
                const liveDot = document.getElementById('notifikasi-live-dot');

                liveDot.classList.remove('hidden');
                liveDot.classList.add('flex');

                // Hilangkan pesan "tidak ada notifikasi" kalau ada
                if (list.children.length === 1 && list.children[0].tagName === 'P') {
                    list.innerHTML = '';
                }

                list.insertAdjacentHTML('afterbegin', renderNotifikasiItem(n));

                const badge = document.getElementById('notifikasi-badge');
                const current = badge.classList.contains('hidden') ? 0 : parseInt(badge.textContent) || 0;
                updateBadge(current + 1);
            });
    } else {
        console.warn('Laravel Echo belum dimuat — cek resources/js/app.js sudah import echo.js');
    }
</script>
@endif