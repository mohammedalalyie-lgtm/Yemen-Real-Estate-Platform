/**
 * عقار اليمن – Yemen Real Estate Platform
 * Smart Real Estate Assistant (مساعد عقار اليمن الذكي)
 */

document.addEventListener('DOMContentLoaded', function () {
    const bubble = document.getElementById('chatbotBubble');
    const windowEl = document.getElementById('chatbotWindow');
    const closeBtn = document.getElementById('chatbotClose');
    const form = document.getElementById('chatbotForm');
    const input = document.getElementById('chatbotInput');
    const body = document.getElementById('chatbotBody');

    if (!bubble || !windowEl) return;

    // Toggle Chatbot
    bubble.addEventListener('click', function () {
        windowEl.classList.toggle('active');
        if (windowEl.classList.contains('active')) {
            input.focus();
        }
    });

    closeBtn.addEventListener('click', function () {
        windowEl.classList.remove('active');
    });

    // Handle Form Submit
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const text = input.value.trim();
        if (!text) return;

        sendMessage(text);
        input.value = '';
    });

    // Handle Quick Action Chips
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('chatbot-chip')) {
            const prompt = e.target.getAttribute('data-prompt') || e.target.innerText;
            sendMessage(prompt);
        }
    });

    function sendMessage(messageText) {
        // Append user bubble
        appendUserMessage(messageText);

        // Show typing indicator
        const typingId = showTypingIndicator();

        const baseUrl = document.querySelector('meta[name="base-url"]')?.content || '';
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

        fetch(`${baseUrl}/api/chatbot`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `message=${encodeURIComponent(messageText)}&csrf_token=${csrfToken}`
        })
        .then(res => res.json())
        .then(data => {
            removeTypingIndicator(typingId);
            appendBotResponse(data);
        })
        .catch(() => {
            removeTypingIndicator(typingId);
            appendBotResponse({
                message: 'عذراً، حدث خطأ أثناء معالجة الطلب. يرجى المحاولة مرة أخرى.',
                suggestions: ['أريد شراء عقار', 'أريد استئجار عقار', 'عقارات في صنعاء']
            });
        });
    }

    function appendUserMessage(text) {
        const div = document.createElement('div');
        div.className = 'chat-bubble-user';
        div.innerText = text;
        body.appendChild(div);
        scrollToBottom();
    }

    function appendBotResponse(data) {
        const div = document.createElement('div');
        div.className = 'chat-bubble-bot';

        let formattedMsg = formatBotText(data.message || '');
        let html = `<div>${formattedMsg}</div>`;

        // Render properties if any
        if (data.properties && data.properties.length > 0) {
            const baseUrl = document.querySelector('meta[name="base-url"]')?.content || '';
            html += `<div class="mt-3 d-flex flex-column gap-2">`;
            data.properties.forEach(p => {
                const img = p.primary_image ? `${baseUrl}/uploads/properties/${p.primary_image}` : `${baseUrl}/assets/images/placeholder.svg`;
                const priceFormatted = Number(p.price).toLocaleString();
                const curr = p.currency === 'USD' ? '$ دولار' : (p.currency === 'SAR' ? 'ر.س' : 'ريال يمني');
                
                html += `
                    <div class="card border border-primary-subtle shadow-sm p-2 text-end" style="background:#fff;">
                        <div class="row g-2 align-items-center">
                            <div class="col-4">
                                <img src="${img}" class="img-fluid rounded" style="height: 65px; width: 100%; object-fit: cover;" alt="${p.title}">
                            </div>
                            <div class="col-8">
                                <div class="fw-bold small text-truncate">${p.title}</div>
                                <div class="text-muted" style="font-size:0.75rem;"><i class="bi bi-geo-alt"></i> ${p.governorate_name} - ${p.district_name}</div>
                                <div class="text-primary fw-bold small">${priceFormatted} ${curr}</div>
                                <a href="${baseUrl}/property/${p.id}" class="btn btn-sm btn-outline-primary py-0 px-2 mt-1" style="font-size:0.75rem;" target="_blank">
                                    معاينة التفاصيل <i class="bi bi-arrow-left"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += `</div>`;
        }

        // Suggestions
        if (data.suggestions && data.suggestions.length > 0) {
            html += `<div class="chatbot-suggestions mt-2">`;
            data.suggestions.forEach(s => {
                html += `<span class="chatbot-chip" data-prompt="${s}">${s}</span>`;
            });
            html += `</div>`;
        }

        div.innerHTML = html;
        body.appendChild(div);
        scrollToBottom();
    }

    function showTypingIndicator() {
        const id = 'typing_' + Date.now();
        const div = document.createElement('div');
        div.id = id;
        div.className = 'chat-bubble-bot text-muted small fst-italic';
        div.innerHTML = '<span class="spinner-grow spinner-grow-sm me-1"></span> جاري البحث والتحليل...';
        body.appendChild(div);
        scrollToBottom();
        return id;
    }

    function removeTypingIndicator(id) {
        const el = document.getElementById(id);
        if (el) el.remove();
    }

    function formatBotText(str) {
        let safe = str.replace(/</g, "&lt;").replace(/>/g, "&gt;");
        // Markdown bold **text**
        safe = safe.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        // Newlines to br
        safe = safe.replace(/\n/g, '<br>');
        return safe;
    }

    function scrollToBottom() {
        body.scrollTop = body.scrollHeight;
    }
});
