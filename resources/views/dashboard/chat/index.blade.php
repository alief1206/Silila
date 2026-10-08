@extends('dashboard.template')
@section('title', 'Live Chat Pengunjung')

@section('content')
<div class="main-content-container container-fluid px-4 py-3">
    <!-- Page Header -->
    <div class="page-header row no-gutters align-items-center justify-content-between py-3">
      <div class="col-12 col-md-6 mb-2 mb-md-0">
        <span class="page-subtitle"><i class="material-icons mr-1" style="font-size: 14px; vertical-align: middle;">headset_mic</i> Layanan Konsultasi SILILA</span>
        <h3 class="page-title">Pusat Pesan & Live Chat</h3>
      </div>
      <div class="col-12 col-md-6 text-md-right">
        <span class="badge px-3 py-2" style="background: rgba(16, 185, 129, 0.1); color: #059669; font-weight: 700; border-radius: 30px; font-size: 12px; border: 1px solid rgba(16, 185, 129, 0.2);">
          <i class="fas fa-circle mr-1" style="font-size: 8px; vertical-align: middle; color: #10b981;"></i> Kanal Interaktif Real-Time
        </span>
      </div>
    </div>
    <!-- End Page Header -->

    <div class="row">
        <!-- Sidebar Daftar Chat -->
        <div class="col-lg-4 col-md-5 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden; height: 680px; display: flex; flex-direction: column;">
                <div class="card-header border-bottom py-3 px-4" style="background: #ffffff;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle mr-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background: rgba(16, 185, 129, 0.1); color: #059669;">
                                <i class="material-icons" style="font-size: 18px;">forum</i>
                            </div>
                            <h6 class="m-0 font-weight-bold" style="color: #0f172a; font-size: 15px;">Daftar Chat Aktif</h6>
                        </div>
                        <span id="session-count-badge" class="badge badge-pill" style="background: var(--silila-emerald-50); color: var(--silila-emerald-700); font-weight: 700; border: 1px solid var(--silila-emerald-200); font-size: 11px;">0 sesi</span>
                    </div>
                </div>
                <div class="card-body p-0 overflow-auto flex-grow-1" id="session-list" style="background: #f8fafc;">
                    <!-- Sesi dimuat dengan AJAX -->
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-spinner fa-spin mr-1 text-emerald"></i>
                        <span style="font-size: 13px;">Memuat percakapan...</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Area Chat -->
        <div class="col-lg-8 col-md-7 mb-4">
            <div class="card border-0 shadow-sm d-none" id="chat-area" style="border-radius: 20px; overflow: hidden; height: 680px; display: flex; flex-direction: column;">
                <!-- Header Chat Aktif -->
                <div class="card-header border-bottom py-3 px-4" style="background: #ffffff;">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="d-flex align-items-center mb-1 mb-md-0">
                            <div class="rounded-circle mr-3 d-flex align-items-center justify-content-center text-white" style="width: 44px; height: 44px; background: linear-gradient(135deg, #059669, #10b981); font-weight: 700; font-size: 18px; box-shadow: 0 4px 12px rgba(16,185,129,0.3);">
                                <i class="material-icons" style="font-size: 22px;">person</i>
                            </div>
                            <div>
                                <h6 class="m-0 font-weight-bold" id="chat-nama" style="color: #0f172a; font-size: 16px;">Nama Pengunjung</h6>
                                <div class="d-flex align-items-center flex-wrap gap-2 mt-1">
                                    <span class="badge" id="chat-nik-badge" style="background: #f1f5f9; color: #475569; font-size: 11px; font-weight: 600;">NIK: -</span>
                                    <span class="badge" id="chat-koordinat-badge" style="background: rgba(245, 158, 11, 0.1); color: #d97706; font-size: 11px; font-weight: 600; margin-left: 6px;">
                                        <i class="material-icons mr-1" style="font-size: 11px; vertical-align: text-top;">place</i><span id="chat-koordinat-val">-</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <button class="btn btn-outline-danger btn-sm" id="btn-close-session" style="border-radius: 20px; font-weight: 600; font-size: 12px; padding: 5px 14px;">
                                <i class="material-icons mr-1" style="font-size: 14px; vertical-align: text-top;">cancel</i> Tutup Sesi
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Chat Messages Body -->
                <div class="card-body p-4 overflow-auto flex-grow-1" style="background-color: #f8fafc;" id="chat-messages">
                    <!-- Pesan dimuat dengan AJAX -->
                </div>

                <!-- Footer Input Bar -->
                <div class="card-footer p-3 border-top bg-white">
                    <form id="form-send-message" class="d-flex align-items-center">
                        <input type="hidden" id="current-session-id">
                        <div class="input-group">
                            <input type="text" id="input-message" class="form-control" placeholder="Tulis tanggapan atau informasi lahan untuk pengunjung..." style="border-radius: 30px 0 0 30px; border-color: #e2e8f0; font-size: 13.5px; padding-left: 20px;" required>
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-silila-emerald px-4" style="border-radius: 0 30px 30px 0; font-weight: 600;">
                                    <i class="material-icons mr-1" style="font-size: 16px; vertical-align: text-top;">send</i> Kirim
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Empty State / No Chat Selected -->
            <div class="card border-0 shadow-sm h-100 d-flex flex-column justify-content-center align-items-center text-center p-5" id="no-chat-selected" style="border-radius: 20px; height: 680px !important; background: #ffffff;">
                <div class="rounded-circle mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; background: rgba(16, 185, 129, 0.08); color: #059669;">
                    <i class="material-icons" style="font-size: 42px;">chat</i>
                </div>
                <h5 class="font-weight-bold" style="color: #0f172a; margin-bottom: 8px;">Pilih Percakapan untuk Memulai</h5>
                <p class="text-muted" style="max-width: 420px; font-size: 13.5px; line-height: 1.6;">
                    Pilih salah satu sesi konsultasi dari panel di sebelah kiri untuk melihat pesan masuk dan memberikan informasi peruntukan lahan kepada pengunjung.
                </p>
                <div class="d-flex align-items-center mt-2" style="font-size: 12px; color: #64748b;">
                    <i class="material-icons mr-1" style="font-size: 16px; color: #10b981;">sync</i> Sinkronisasi otomatis setiap beberapa detik
                </div>
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
    const sessionCountBadge = document.getElementById('session-count-badge');
    
    // Tarik daftar sesi tiap 5 detik
    function fetchSessions() {
        fetch('{{ route("dashboard.chat.sessions") }}')
            .then(res => res.json())
            .then(data => {
                sessionList.innerHTML = '';
                if(!data.sessions || data.sessions.length === 0) {
                    if (sessionCountBadge) sessionCountBadge.textContent = '0 sesi';
                    sessionList.innerHTML = `
                        <div class="text-center py-5 px-3 text-muted">
                            <i class="material-icons text-muted mb-2" style="font-size: 36px; opacity: 0.5;">mark_chat_read</i>
                            <p class="font-weight-bold mb-1" style="font-size: 14px; color: #64748b;">Tidak Ada Chat Aktif</p>
                            <small>Saat ini belum ada pengunjung yang menghubungi.</small>
                        </div>
                    `;
                    return;
                }

                if (sessionCountBadge) sessionCountBadge.textContent = `${data.sessions.length} sesi`;

                data.sessions.forEach(session => {
                    let lastMsg = session.messages && session.messages.length > 0 ? session.messages[0].message : 'Sesi baru dimulai';
                    let isActive = session.id === activeSessionId;
                    
                    let div = document.createElement('div');
                    div.className = `p-3 border-bottom transition-all session-item ${isActive ? 'bg-white shadow-sm' : ''}`;
                    div.style.cursor = 'pointer';
                    div.style.transition = 'all 0.2s ease';
                    div.style.borderLeft = isActive ? '4px solid #059669' : '4px solid transparent';
                    div.innerHTML = `
                        <div class="d-flex align-items-start justify-content-between mb-1">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle mr-2 d-flex align-items-center justify-content-center text-white" style="width: 32px; height: 32px; background: ${isActive ? 'linear-gradient(135deg, #059669, #10b981)' : '#94a3b8'}; font-size: 13px; font-weight: 700;">
                                    ${(session.nama || 'U').charAt(0).toUpperCase()}
                                </div>
                                <h6 class="font-weight-bold mb-0" style="font-size: 14px; color: #0f172a;">${session.nama}</h6>
                            </div>
                            <span class="badge" style="background: rgba(16, 185, 129, 0.1); color: #059669; font-size: 10px;">Aktif</span>
                        </div>
                        <p class="text-secondary mb-1 text-truncate" style="font-size: 12.5px; padding-left: 40px; color: #64748b;">${lastMsg}</p>
                        <div style="padding-left: 40px;">
                            <span class="text-muted" style="font-size: 11px;">
                                <i class="material-icons" style="font-size: 11px; vertical-align: middle;">place</i> ${session.koordinat || '-'}
                            </span>
                        </div>
                    `;
                    
                    div.addEventListener('mouseenter', () => {
                        if (!isActive) div.style.background = '#f1f5f9';
                    });
                    div.addEventListener('mouseleave', () => {
                        if (!isActive) div.style.background = 'transparent';
                    });

                    div.addEventListener('click', () => {
                        openSession(session);
                    });
                    
                    sessionList.appendChild(div);
                });
            })
            .catch(err => console.error('Error fetching sessions:', err));
    }

    function openSession(session) {
        activeSessionId = session.id;
        lastMessageId = 0; // Reset last id untuk sesi baru
        
        document.getElementById('chat-nama').textContent = session.nama;
        const nikBadge = document.getElementById('chat-nik-badge');
        if (nikBadge) nikBadge.textContent = `NIK: ${session.nik || '-'}`;
        const coordVal = document.getElementById('chat-koordinat-val');
        if (coordVal) coordVal.textContent = session.koordinat || '-';
        document.getElementById('current-session-id').value = session.id;
        
        chatMessages.innerHTML = '';
        noChatSelected.classList.add('d-none');
        chatArea.classList.remove('d-none');
        
        // Segera ambil pesan, lalu ulangi tiap 3 detik
        clearInterval(fetchMessagesInterval);
        fetchMessages();
        fetchMessagesInterval = setInterval(fetchMessages, 1000);
        
        // Refresh styling active list
        fetchSessions();
        
        // Tandai sudah dibaca
        fetch(`/chat/${activeSessionId}/read`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ reader_type: 'admin' })
        }).catch(err => console.error('Mark read err:', err));
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
                        let bubbleStyle = isMe 
                            ? 'background: linear-gradient(135deg, #059669 0%, #10b981 100%); color: #ffffff; border-radius: 18px 18px 4px 18px; box-shadow: 0 4px 14px rgba(16, 185, 129, 0.25);' 
                            : 'background: #ffffff; color: #0f172a; border-radius: 18px 18px 18px 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;';
                        
                        let tickHtml = '';
                        if (isMe) {
                            let tickColor = msg.is_read ? '#a7f3d0' : 'rgba(255,255,255,0.7)';
                            let tickIcon = msg.is_read ? 'fa-check-double' : 'fa-check';
                            tickHtml = `<div class="msg-tick text-right mt-1" data-read="${msg.is_read ? 'true' : 'false'}">
                                            <i class="fas ${tickIcon}" style="font-size:10px; color:${tickColor};"></i>
                                        </div>`;
                        } else {
                            needsMarkRead = true;
                        }
                        
                        let msgHtml = `
                            <div class="d-flex w-100 mb-3 ${align}" data-msg-id="${msg.id}">
                                <div class="p-3" style="max-width: 75%; ${bubbleStyle}">
                                    ${!isMe ? '<div class="d-flex align-items-center mb-1 text-muted" style="font-size: 11px; font-weight: 700;"><i class="material-icons mr-1" style="font-size: 12px; color: #10b981;">person</i> Pengunjung</div>' : ''}
                                    <p class="mb-0" style="font-size: 13.5px; line-height: 1.5;">${msg.message}</p>
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
                                tickDiv.innerHTML = '<i class="fas fa-check-double" style="font-size:10px; color:#a7f3d0;"></i>';
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
                    }).catch(err => console.error('Mark read err:', err));
                }
            })
            .catch(err => console.error('Fetch msgs err:', err));
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
        })
        .catch(err => console.error('Send message err:', err));
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
            })
            .catch(err => console.error('Close session err:', err));
        }
    });

    // Mulai polling sesi pertama kali
    fetchSessions();
    fetchSessionsInterval = setInterval(fetchSessions, 1000);
});
</script>
@endsection
