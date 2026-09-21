@extends('dashboard.template')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Sidebar Daftar Chat -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header pb-0 p-3">
                    <h6 class="mb-0">Daftar Chat Aktif</h6>
                </div>
                <div class="card-body p-3 overflow-auto" style="max-height: 500px;" id="session-list">
                    <!-- Sesi dimuat dengan AJAX -->
                    <p class="text-center text-sm text-muted">Memuat data...</p>
                </div>
            </div>
        </div>

        <!-- Area Chat -->
        <div class="col-md-8">
            <div class="card h-100 d-none" id="chat-area">
                <div class="card-header pb-0 p-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0" id="chat-nama">Nama Pengunjung</h6>
                            <small class="text-muted" id="chat-nik-koordinat">NIK: - | Koordinat: -</small>
                        </div>
                        <button class="btn btn-sm btn-danger mb-0" id="btn-close-session">Tutup Sesi</button>
                    </div>
                </div>
                <div class="card-body p-3 overflow-auto" style="height: 400px; background-color: #f8f9fa;" id="chat-messages">
                    <!-- Pesan dimuat dengan AJAX -->
                </div>
                <div class="card-footer p-3 border-top">
                    <form id="form-send-message" class="d-flex align-items-center">
                        <input type="hidden" id="current-session-id">
                        <input type="text" id="input-message" class="form-control me-2" placeholder="Ketik balasan Anda..." required>
                        <button type="submit" class="btn btn-primary mb-0">Kirim</button>
                    </form>
                </div>
            </div>
            
            <div class="card h-100 d-flex justify-content-center align-items-center text-muted" id="no-chat-selected">
                <p>Pilih salah satu percakapan di samping untuk mulai membalas.</p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let activeSessionId = null;
    let lastMessageId = 0;
    let fetchSessionsInterval;
    let fetchMessagesInterval;

    const sessionList = document.getElementById('session-list');
    const chatArea = document.getElementById('chat-area');
    const noChatSelected = document.getElementById('no-chat-selected');
    const chatMessages = document.getElementById('chat-messages');
    
    // Tarik daftar sesi tiap 5 detik
    function fetchSessions() {
        fetch('{{ route("dashboard.chat.sessions") }}')
            .then(res => res.json())
            .then(data => {
                sessionList.innerHTML = '';
                if(data.sessions.length === 0) {
                    sessionList.innerHTML = '<p class="text-center text-sm text-muted mt-3">Tidak ada chat aktif.</p>';
                    return;
                }

                data.sessions.forEach(session => {
                    let lastMsg = session.messages.length > 0 ? session.messages[0].message : 'Sesi baru dimulai';
                    let isActive = session.id === activeSessionId ? 'bg-light border-primary' : '';
                    
                    let div = document.createElement('div');
                    div.className = `p-2 border-bottom cursor-pointer ${isActive}`;
                    div.style.cursor = 'pointer';
                    div.innerHTML = `
                        <h6 class="text-sm mb-0">${session.nama}</h6>
                        <p class="text-xs text-secondary mb-0 text-truncate" style="max-width: 100%;">${lastMsg}</p>
                    `;
                    
                    div.addEventListener('click', () => {
                        openSession(session);
                    });
                    
                    sessionList.appendChild(div);
                });
            });
    }

    function openSession(session) {
        activeSessionId = session.id;
        lastMessageId = 0; // Reset last id untuk sesi baru
        
        document.getElementById('chat-nama').textContent = session.nama;
        document.getElementById('chat-nik-koordinat').textContent = `NIK: ${session.nik} | Koordinat: ${session.koordinat}`;
        document.getElementById('current-session-id').value = session.id;
        
        chatMessages.innerHTML = '';
        noChatSelected.classList.add('d-none');
        chatArea.classList.remove('d-none');
        
        // Segera ambil pesan, lalu ulangi tiap 3 detik
        clearInterval(fetchMessagesInterval);
        fetchMessages();
        fetchMessagesInterval = setInterval(fetchMessages, 3000);
        
        // Refresh styling active list
        fetchSessions();
        
        // Tandai sudah dibaca
        fetch(`/chat/${activeSessionId}/read`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ reader_type: 'admin' })
        });
    }

    function fetchMessages() {
        if(!activeSessionId) return;
        
        fetch(`/chat/${activeSessionId}/messages?last_id=${lastMessageId}`)
            .then(res => res.json())
            .then(data => {
                let needsMarkRead = false;
                
                if(data.messages && data.messages.length > 0) {
                    data.messages.forEach(msg => {
                        let isMe = msg.sender_type === 'admin';
                        let align = isMe ? 'justify-content-end' : 'justify-content-start';
                        let bg = isMe ? 'bg-primary text-white' : 'bg-white text-dark border';
                        
                        let tickHtml = '';
                        if (isMe) {
                            let tickColor = msg.is_read ? '#34b7f1' : '#ccc';
                            let tickIcon = msg.is_read ? 'fa-check-double' : 'fa-check';
                            tickHtml = `<div class="msg-tick text-end mt-1" data-read="${msg.is_read ? 'true' : 'false'}">
                                            <i class="fas ${tickIcon}" style="font-size:10px; color:${tickColor};"></i>
                                        </div>`;
                        } else {
                            needsMarkRead = true;
                        }
                        
                        let msgHtml = `
                            <div class="d-flex w-100 mb-2 ${align}" data-msg-id="${msg.id}">
                                <div class="p-2 rounded shadow-sm ${bg}" style="max-width: 75%;">
                                    <p class="mb-0 text-sm">${msg.message}</p>
                                    ${tickHtml}
                                </div>
                            </div>
                        `;
                        chatMessages.insertAdjacentHTML('beforeend', msgHtml);
                        lastMessageId = msg.id;
                    });
                    // Scroll ke bawah
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
                
                // Update ticks for previously sent messages
                if (data.user_last_read_id) {
                    document.querySelectorAll('.msg-tick').forEach(tickDiv => {
                        const msgDiv = tickDiv.closest('[data-msg-id]');
                        if (msgDiv) {
                            const msgId = parseInt(msgDiv.getAttribute('data-msg-id'));
                            if (msgId <= data.user_last_read_id && tickDiv.dataset.read !== 'true') {
                                tickDiv.innerHTML = '<i class="fas fa-check-double" style="font-size:10px; color:#34b7f1;"></i>';
                                tickDiv.dataset.read = 'true';
                            }
                        }
                    });
                }
                
                if (needsMarkRead) {
                    fetch(`/chat/${activeSessionId}/read`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ reader_type: 'admin' })
                    });
                }
            });
    }

    document.getElementById('form-send-message').addEventListener('submit', function(e) {
        e.preventDefault();
        let input = document.getElementById('input-message');
        let message = input.value.trim();
        if(!message || !activeSessionId) return;

        fetch('{{ route("chat.send") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                session_id: activeSessionId,
                sender_type: 'admin',
                message: message
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                input.value = '';
                fetchMessages(); // Langsung fetch pesan tanpa nunggu interval
                fetchSessions(); // Update last message di sidebar
            }
        });
    });

    document.getElementById('btn-close-session').addEventListener('click', function() {
        if(!activeSessionId) return;
        if(confirm("Apakah Anda yakin ingin menutup sesi ini?")) {
            fetch(`/dashboard/chat/sessions/${activeSessionId}/close`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    activeSessionId = null;
                    clearInterval(fetchMessagesInterval);
                    chatArea.classList.add('d-none');
                    noChatSelected.classList.remove('d-none');
                    fetchSessions();
                }
            });
        }
    });

    // Mulai polling sesi pertama kali
    fetchSessions();
    fetchSessionsInterval = setInterval(fetchSessions, 5000);
});
</script>
@endsection
