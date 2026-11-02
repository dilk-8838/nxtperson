@php
    $authUser = \Illuminate\Support\Facades\Auth::user();
    $currentRole = $authUser ? $authUser->role : session('demo_role', 'farmer');
@endphp

<!-- Floating Live Real-Time Multi-Role Notification Toast -->
<div id="realtime-toast" 
     class="fixed bottom-5 right-5 z-50 max-w-md w-[calc(100vw-2.5rem)] sm:w-full bg-white rounded-2xl shadow-2xl border-2 border-[#00875a] p-4 transform translate-y-32 opacity-0 pointer-events-none transition-all duration-300 ease-out"
     role="alert" 
     aria-live="assertive">
    
    <div class="flex items-start gap-3">
        <!-- Thumbnail / Icon Container -->
        <div id="toast-icon-wrapper" class="relative shrink-0 w-14 h-14 sm:w-16 sm:h-16 rounded-xl overflow-hidden bg-stone-100 border border-stone-200 flex items-center justify-center text-2xl">
            <img id="toast-produce-img" src="" alt="Produce Photo" class="hidden w-full h-full object-cover">
            <span id="toast-fallback-icon" class="text-2xl">🔔</span>
            <span id="toast-grade-badge" class="hidden absolute bottom-0 inset-x-0 text-center text-[9px] font-bold py-0.5 text-white bg-emerald-600 uppercase tracking-wider">
                Good
            </span>
        </div>

        <!-- Content Area -->
        <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between gap-1 mb-1">
                <span id="toast-tag" class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                    <span id="toast-tag-text">New Live Alert</span>
                </span>
                <button type="button" id="toast-close-btn" class="text-stone-400 hover:text-stone-700 text-sm p-1 rounded-lg transition-colors cursor-pointer" aria-label="Dismiss Notification">
                    ✕
                </button>
            </div>

            <h4 id="toast-title" class="text-sm font-bold text-stone-900 truncate">
                Notification Title
            </h4>
            
            <p id="toast-desc" class="text-xs text-stone-600 line-clamp-2 mt-0.5 leading-relaxed">
                Notification description details...
            </p>

            <!-- Pricing & Quantity Badges (shown for produce stock / reservations) -->
            <div id="toast-meta-badges" class="hidden flex-wrap items-center gap-2 mt-2 text-xs">
                <div id="toast-quantity-badge" class="bg-stone-100 px-2 py-0.5 rounded-md font-semibold text-stone-800 text-[11px]">
                    📦 <span id="toast-quantity">0 kg</span>
                </div>
                <div id="toast-price-badge" class="bg-emerald-50 text-emerald-900 px-2 py-0.5 rounded-md font-bold border border-emerald-200 text-[11px]">
                    💰 ₹<span id="toast-price">0</span>/kg
                </div>
                <div id="toast-district-badge" class="text-[11px] text-stone-500 font-medium">
                    📍 <span id="toast-district">Gujarat</span>
                </div>
            </div>

            <!-- Action Button -->
            <div class="mt-3 flex items-center justify-between gap-2 pt-2 border-t border-stone-100">
                <span id="toast-action-subtext" class="text-[10px] text-stone-400 font-medium">Click to review update</span>
                <a id="toast-view-link" href="#" class="inline-flex items-center gap-1 px-3 py-1.5 bg-[#0c365b] hover:bg-[#071f37] text-white rounded-lg text-xs font-bold shadow-xs transition-colors">
                    <span id="toast-view-text">View Details</span>
                    <span>→</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Real-Time Role-Based Notification & Polling Engine -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toast = document.getElementById('realtime-toast');
        const toastCloseBtn = document.getElementById('toast-close-btn');
        const toastImg = document.getElementById('toast-produce-img');
        const toastFallbackIcon = document.getElementById('toast-fallback-icon');
        const toastGrade = document.getElementById('toast-grade-badge');
        const toastTagText = document.getElementById('toast-tag-text');
        const toastTitle = document.getElementById('toast-title');
        const toastDesc = document.getElementById('toast-desc');
        const toastMetaBadges = document.getElementById('toast-meta-badges');
        const toastQuantity = document.getElementById('toast-quantity');
        const toastPrice = document.getElementById('toast-price');
        const toastDistrict = document.getElementById('toast-district');
        const toastViewLink = document.getElementById('toast-view-link');
        const toastViewText = document.getElementById('toast-view-text');

        // Header Bell & Dropdown elements
        const bellBtn = document.getElementById('notification-bell-btn');
        const dropdownPanel = document.getElementById('notification-dropdown-panel');
        const dropdownList = document.getElementById('dropdown-items-list');
        const dropdownEmpty = document.getElementById('dropdown-empty');
        const unreadBadge = document.getElementById('notification-unread-badge');
        const buyerBadge = document.getElementById('buyer-unread-badge');
        const dropdownUnreadPill = document.getElementById('dropdown-unread-pill');
        const dropdownMarkAllBtn = document.getElementById('dropdown-mark-all-btn');

        const csrfToken = '{{ csrf_token() }}';
        let lastPolledTimestamp = new Date(Date.now() - 60000).toISOString();
        let seenNotificationIds = new Set();
        let hideTimeout = null;

        // Toggle dropdown panel
        if (bellBtn && dropdownPanel) {
            bellBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                const isHidden = dropdownPanel.classList.contains('hidden');
                if (isHidden) {
                    dropdownPanel.classList.remove('hidden');
                    bellBtn.setAttribute('aria-expanded', 'true');
                    pollNotifications(); // Fetch latest on open
                } else {
                    dropdownPanel.classList.add('hidden');
                    bellBtn.setAttribute('aria-expanded', 'false');
                }
            });

            // Close when clicking outside
            document.addEventListener('click', function (e) {
                if (!dropdownPanel.contains(e.target) && !bellBtn.contains(e.target)) {
                    dropdownPanel.classList.add('hidden');
                    bellBtn.setAttribute('aria-expanded', 'false');
                }
            });

            // Close on Escape key
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && !dropdownPanel.classList.contains('hidden')) {
                    dropdownPanel.classList.add('hidden');
                    bellBtn.setAttribute('aria-expanded', 'false');
                    bellBtn.focus();
                }
            });
        }

        // Mark all as read button inside dropdown
        if (dropdownMarkAllBtn) {
            dropdownMarkAllBtn.addEventListener('click', function (e) {
                e.preventDefault();
                fetch('{{ route("notifications.mark-all-read") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    updateUnreadBadges(0);
                    // Update read visual status in dropdown
                    const dots = dropdownList.querySelectorAll('.unread-dot');
                    dots.forEach(d => d.remove());
                    const markBtns = dropdownList.querySelectorAll('.item-mark-read-btn');
                    markBtns.forEach(b => b.remove());
                })
                .catch(() => {});
            });
        }

        function updateUnreadBadges(count) {
            const hasUnread = count > 0;
            const text = count > 99 ? '99+' : count;

            if (unreadBadge) {
                unreadBadge.textContent = text;
                if (hasUnread) {
                    unreadBadge.classList.remove('hidden');
                    unreadBadge.classList.add('flex');
                } else {
                    unreadBadge.classList.remove('flex');
                    unreadBadge.classList.add('hidden');
                }
            }

            if (buyerBadge) {
                buyerBadge.textContent = text;
                if (hasUnread) {
                    buyerBadge.classList.remove('hidden');
                    buyerBadge.classList.add('flex');
                } else {
                    buyerBadge.classList.remove('flex');
                    buyerBadge.classList.add('hidden');
                }
            }

            if (dropdownUnreadPill) {
                if (hasUnread) {
                    dropdownUnreadPill.textContent = text + ' Unread';
                    dropdownUnreadPill.classList.remove('hidden');
                } else {
                    dropdownUnreadPill.classList.add('hidden');
                }
            }
        }

        function renderDropdownList(items) {
            if (!dropdownList) return;

            // Clear previous items except empty state
            const previousItems = dropdownList.querySelectorAll('.dropdown-item-entry');
            previousItems.forEach(el => el.remove());

            if (!items || items.length === 0) {
                if (dropdownEmpty) dropdownEmpty.classList.remove('hidden');
                return;
            }

            if (dropdownEmpty) dropdownEmpty.classList.add('hidden');

            items.forEach(notif => {
                const itemDiv = document.createElement('div');
                itemDiv.className = 'dropdown-item-entry p-3 sm:p-3.5 hover:bg-slate-50 transition-colors flex items-start gap-2.5 text-xs ' + (notif.is_read ? 'opacity-80' : 'bg-emerald-50/30');

                const icon = notif.type === 'produce_stock_available' ? '🌾' :
                             (notif.type === 'farmer_reservation_received' ? '🛒' :
                             (notif.type === 'farmer_interest' ? '💬' :
                             (notif.type === 'admin_new_lot_submitted' ? '🛡️' : '🔔')));

                let unreadIndicator = notif.is_read ? '' : '<span class="unread-dot w-2 h-2 rounded-full bg-[#00875a] shrink-0 mt-1"></span>';

                let markReadButton = notif.is_read ? '' : `
                    <button type="button" class="item-mark-read-btn text-[10px] text-slate-400 hover:text-slate-700 p-0.5 rounded cursor-pointer" title="Mark read" data-id="${notif.id}">
                        ✓
                    </button>
                `;

                itemDiv.innerHTML = `
                    <span class="text-base shrink-0 mt-0.5">${icon}</span>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-1">
                            <a href="${notif.view_url || '{{ route("notifications.index") }}'}" class="font-bold text-slate-900 hover:text-[#0c365b] truncate block">
                                ${escapeHtml(notif.title)}
                            </a>
                            <div class="flex items-center gap-1 shrink-0">
                                ${unreadIndicator}
                                ${markReadButton}
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-600 line-clamp-2 mt-0.5 leading-snug">
                            ${escapeHtml(notif.message)}
                        </p>
                        <span class="text-[10px] text-slate-400 mt-1 block font-mono">
                            ${notif.time_ago || ''}
                        </span>
                    </div>
                `;

                // Add single mark as read handler
                const btn = itemDiv.querySelector('.item-mark-read-btn');
                if (btn) {
                    btn.addEventListener('click', function (e) {
                        e.stopPropagation();
                        const notifId = this.getAttribute('data-id');
                        fetch('/notifications/' + encodeURIComponent(notifId) + '/read', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                updateUnreadBadges(data.unread_count);
                                const dot = itemDiv.querySelector('.unread-dot');
                                if (dot) dot.remove();
                                btn.remove();
                                itemDiv.classList.add('opacity-80');
                                itemDiv.classList.remove('bg-emerald-50/30');
                            }
                        })
                        .catch(() => {});
                    });
                }

                dropdownList.appendChild(itemDiv);
            });
        }

        function showToast(notif) {
            if (!toast) return;

            // Configure icon / image
            if (notif.image_url) {
                toastImg.src = notif.image_url;
                toastImg.classList.remove('hidden');
                toastFallbackIcon.classList.add('hidden');
            } else {
                toastImg.classList.add('hidden');
                toastFallbackIcon.classList.remove('hidden');
                toastFallbackIcon.textContent = notif.type === 'farmer_reservation_received' ? '🛒' :
                                               (notif.type === 'farmer_interest' ? '💬' :
                                               (notif.type === 'admin_new_lot_submitted' ? '🛡️' : '🔔'));
            }

            // Grade badge if present
            if (notif.grade) {
                toastGrade.textContent = notif.grade;
                toastGrade.classList.remove('hidden');
                toastGrade.className = 'absolute bottom-0 inset-x-0 text-center text-[9px] font-bold py-0.5 text-white uppercase tracking-wider ' +
                    (notif.grade === 'Good' ? 'bg-emerald-600' : (notif.grade === 'Intermediate' ? 'bg-amber-600' : 'bg-red-600'));
            } else {
                toastGrade.classList.add('hidden');
            }

            // Tag label
            if (notif.type === 'produce_stock_available') {
                toastTagText.textContent = 'Certified Stock Available';
            } else if (notif.type === 'farmer_reservation_received') {
                toastTagText.textContent = 'New Purchase Request';
            } else if (notif.type === 'farmer_interest') {
                toastTagText.textContent = 'Buyer Inquiry Received';
            } else if (notif.type === 'admin_new_lot_submitted') {
                toastTagText.textContent = 'Lot Verification Required';
            } else {
                toastTagText.textContent = 'Agritech Update';
            }

            toastTitle.textContent = notif.title || 'Notification';
            toastDesc.textContent = notif.message || '';

            // Quantity & Pricing metadata
            if (notif.available_quantity_kg > 0 || notif.price_per_kg > 0) {
                toastMetaBadges.classList.remove('hidden');
                toastMetaBadges.classList.add('flex');
                if (toastQuantity) toastQuantity.textContent = Number(notif.available_quantity_kg).toLocaleString() + ' kg';
                if (toastPrice) toastPrice.textContent = Number(notif.price_per_kg).toFixed(2);
                if (toastDistrict) toastDistrict.textContent = notif.district || 'Gujarat';
            } else {
                toastMetaBadges.classList.remove('flex');
                toastMetaBadges.classList.add('hidden');
            }

            toastViewLink.href = notif.view_url || '{{ route("notifications.index") }}';
            toastViewText.textContent = notif.action_label || 'View Details';

            // Animate slide-in
            toast.classList.remove('translate-y-32', 'opacity-0', 'pointer-events-none');
            toast.classList.add('translate-y-0', 'opacity-100', 'pointer-events-auto');

            if (hideTimeout) clearTimeout(hideTimeout);
            hideTimeout = setTimeout(hideToast, 10000); // Auto-hide after 10s
        }

        function hideToast() {
            if (!toast) return;
            toast.classList.remove('translate-y-0', 'opacity-100', 'pointer-events-auto');
            toast.classList.add('translate-y-32', 'opacity-0', 'pointer-events-none');
        }

        if (toastCloseBtn) {
            toastCloseBtn.addEventListener('click', hideToast);
        }

        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        // Authenticated multi-role polling engine
        function pollNotifications() {
            fetch('{{ route("notifications.poll") }}?since=' + encodeURIComponent(lastPolledTimestamp), {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) return null;
                return response.json();
            })
            .then(data => {
                if (!data) return;

                updateUnreadBadges(data.unread_count);

                if (data.recent) {
                    renderDropdownList(data.recent);
                }

                if (data.notifications && data.notifications.length > 0) {
                    data.notifications.forEach(notif => {
                        if (!seenNotificationIds.has(notif.id)) {
                            seenNotificationIds.add(notif.id);
                            showToast(notif);
                        }
                    });
                }

                if (data.polled_at) {
                    lastPolledTimestamp = data.polled_at;
                }
            })
            .catch(() => {});
        }

        // Initial check and recurring interval (4s)
        pollNotifications();
        setInterval(pollNotifications, 4000);

        // WebSockets channel listener (if Laravel Echo is active)
        if (typeof window.Echo !== 'undefined') {
            window.Echo.channel('produce.available')
                .listen('.ProduceInventoryPublished', (e) => {
                    pollNotifications();
                });
        }
    });
</script>
