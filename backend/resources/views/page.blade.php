<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book page {{ $page }}</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 32px auto; padding: 0 16px; line-height: 1.6; background: #f6f7fb; }
        header { margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center; }
        pre { white-space: pre-wrap; word-break: break-word; background: #f8f9fa; padding: 16px; border-radius: 8px; border: 1px solid #e5e7eb; }
        a { color: #0d6efd; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .meta { color: #6b7280; font-size: 13px; margin: 0 0 8px 0; }
        .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; padding: 18px; box-shadow: 0 10px 30px rgba(15,23,42,0.06); }
        .section { margin-top: 20px; }
        .grid { list-style: none; padding: 0; margin: 12px 0 0 0; display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 12px; }
        .result-item { border: 1px solid #e5e7eb; border-radius: 12px; padding: 12px; background: #fdfefe; display: flex; flex-direction: column; gap: 10px; word-break: break-word; overflow-wrap: anywhere; transition: transform 120ms ease, box-shadow 120ms ease; }
        .result-item:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(15,23,42,0.06); }
        .result-header { font-weight: 700; margin: 0; color: #0f172a; }
        .snippet { margin: 0; color: #374151; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; word-break: break-word; overflow-wrap: anywhere; }
        .open-link { font-weight: 700; background: linear-gradient(90deg, #0d6efd, #2563eb); color: #fff; border: 1px solid #0d6efd; border-radius: 10px; padding: 9px 14px; cursor: pointer; align-self: flex-start; box-shadow: 0 8px 18px rgba(13,110,253,0.25); text-decoration: none; }
        .open-link:hover { filter: brightness(1.05); }
        mark { background: #fef08a; padding: 0 2px; border-radius: 3px; }
        .pager { display: flex; align-items: center; gap: 12px; margin-top: 12px; }
        .ghost { background: #fff; border: 1px solid #d1d5db; border-radius: 10px; padding: 8px 12px; cursor: pointer; font-weight: 700; color: #0f172a; transition: background 120ms ease, transform 120ms ease; }
        .ghost:hover { background: #f3f4f6; transform: translateY(-1px); }
        .badge-list { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin: 12px 0 0 0; }
        .badge { display: inline-flex; align-items: center; justify-content: center; min-width: 36px; padding: 7px 12px; border-radius: 999px; border: 1px solid #d1d5db; background: #fff; color: #111827; font-weight: 700; text-decoration: none; box-shadow: 0 4px 10px rgba(15,23,42,0.05); position: relative; }
        .badge:hover { background: #eef2ff; border-color: #c7d2fe; }
        .badge.active { background: #0d6efd; color: #fff; border-color: #0d6efd; box-shadow: 0 8px 18px rgba(13,110,253,0.25); }
        .badge.viewed { opacity: 0.55; }
        .badge.favorite { border-color: #f59e0b; background: #fff7ed; color: #92400e; }
        .badge.current { opacity: 0.65; font-weight: 800; cursor: default; }
        .badge.closable:hover .badge-close { opacity: 1; pointer-events: auto; }
        .badge-close { position: absolute; top: -8px; right: -8px; border: 1px solid #d1d5db; background: #fff; color: #111827; border-radius: 999px; width: 18px; height: 18px; font-size: 12px; line-height: 1; cursor: pointer; opacity: 0; transition: opacity 120ms ease, transform 120ms ease; box-shadow: 0 4px 10px rgba(15,23,42,0.12); pointer-events: none; }
        .badge-close:hover { transform: scale(1.08); }
        .badge-label { font-weight: 700; color: #111827; margin-right: 4px; }
        .chips { display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap; margin: 10px 0; }
        .chip-group { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .favorites-top { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin: 12px 0; }
        .view-controls { display: flex; align-items: center; gap: 6px; }
        .icon-toggle { border: 1px solid #d1d5db; background: #fff; border-radius: 10px; padding: 8px 10px; cursor: pointer; font-weight: 800; color: #111827; box-shadow: 0 4px 10px rgba(15,23,42,0.05); display: inline-flex; align-items: center; justify-content: center; gap: 6px; }
        .icon-toggle.active { background: linear-gradient(90deg, #0d6efd, #2563eb); border-color: #0d6efd; color: #fff; box-shadow: 0 8px 18px rgba(13,110,253,0.25); }
        .grid[data-view="list"] { grid-template-columns: 1fr; }
        .result-item.viewed { opacity: 0.6; }
        .result-item.current { border-color: #0d6efd; box-shadow: 0 0 0 2px rgba(13,110,253,0.15); }
        .fav-btn { border: 1px solid #d1d5db; background: #fff; border-radius: 10px; padding: 7px 10px; cursor: pointer; font-weight: 700; align-self: flex-start; display: inline-flex; align-items: center; gap: 6px; }
        .fav-btn.is-fav { border-color: #f59e0b; background: #fff7ed; color: #92400e; box-shadow: 0 6px 14px rgba(245,158,11,0.2); }
        .tip { margin: 6px 0 12px 0; color: #4b5563; font-size: 14px; font-weight: 600; }
        .action-row { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; opacity: 0; pointer-events: none; transition: opacity 120ms ease; }
        .result-item:hover .action-row { opacity: 1; pointer-events: auto; }
        .selection-search { position: absolute; z-index: 50; border: 1px solid #0d6efd; background: #fff; color: #0d6efd; border-radius: 8px; padding: 6px 10px; cursor: pointer; box-shadow: 0 6px 20px rgba(13,110,253,0.18); font-weight: 700; }
        .selection-search.hidden { display: none; }
        .pages-row { display: flex; justify-content: space-between; gap: 12px; align-items: center; flex-wrap: wrap; margin: 12px 0; }
        .pager.mini { gap: 10px; }
        .page-select { padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 10px; min-width: 200px; box-shadow: 0 4px 10px rgba(15,23,42,0.05); font-weight: 700; }
    </style>
</head>
<body>
<header>
    <div>
        <p class="meta">Total pages: {{ $totalPages }}</p>
        <strong>Page {{ $page }}</strong>
    </div>
    <div>
        <a href="/">Back</a>
    </div>
</header>

<div id="favorites-top-wrap" class="favorites-top" style="display:none;">
    <span class="badge-label">Favoritos:</span>
    <div id="fav-badges-top" class="badge-list"></div>
</div>

<div class="card">
    <pre>{{ $content }}</pre>
</div>

@if(!empty($term))
<section class="section">
    <h2>Results for "{{ $term }}" — {{ $gridMeta['total'] ?? 0 }} found</h2>
    <p class="tip">Tip: selecciona cualquier palabra del texto y usa el botón flotante para lanzar una búsqueda rápida.</p>
    <div class="chips">
        <div class="chip-group">
            <span class="badge-label">Vistas:</span>
            <div id="seen-badges" class="badge-list"></div>
        </div>
        <div class="chip-group">
            <span class="badge-label">Favoritos:</span>
            <div id="fav-badges" class="badge-list"></div>
        </div>
    </div>
    @if(!empty($pagesWithTerm))
        <div class="pages-row">
            <label class="badge-label" for="page-select">Páginas:</label>
            <select id="page-select" class="page-select">
                @foreach($pagesWithTerm as $pageNumber)
                    <option value="{{ $pageNumber }}" {{ $pageNumber === $page ? 'selected' : '' }}>Página {{ $pageNumber }}</option>
                @endforeach
            </select>
            <div class="pager-inline">
                <div class="view-controls">
                    <button type="button" class="icon-toggle" data-view="grid" title="Ver en mosaico">⧉</button>
                    <button type="button" class="icon-toggle" data-view="list" title="Ver en lista">☰</button>
                </div>
                @php
                    $current = $gridMeta['current_page'] ?? 1;
                    $per = $gridMeta['per_page'] ?? 12;
                    $prev = $current > 1 ? $current - 1 : null;
                    $next = $current < ($gridMeta['total_pages'] ?? 1) ? $current + 1 : null;
                    $baseParams = ['q' => $term, 'per_page' => $per];
                @endphp
                <div class="pager mini">
                    @if($prev)
                        <a class="ghost" href="/pages/{{ $page }}?{{ http_build_query(array_merge($baseParams, ['p' => $prev])) }}">Anterior</a>
                    @else
                        <span class="ghost" style="opacity:0.5;pointer-events:none;">Anterior</span>
                    @endif
                    <span>Page {{ $current }} of {{ $gridMeta['total_pages'] ?? 1 }}</span>
                    @if($next)
                        <a class="ghost" href="/pages/{{ $page }}?{{ http_build_query(array_merge($baseParams, ['p' => $next])) }}">Siguiente</a>
                    @else
                        <span class="ghost" style="opacity:0.5;pointer-events:none;">Siguiente</span>
                    @endif
                </div>
            </div>
        </div>
    @endif
    <ul class="grid">
        @forelse($gridResults as $item)
            <li class="result-item" data-page-item="{{ $item['page'] }}">
                <div class="result-header">Page {{ $item['page'] }}</div>
                @php
                    $pattern = '/' . preg_quote($term, '/') . '/i';
                    $snippet = preg_replace($pattern, '<mark>$0</mark>', e($item['snippet']));
                @endphp
                <p class="snippet">{!! $snippet !!}</p>
                <div class="action-row">
                    <a class="open-link" href="/pages/{{ $item['page_id'] }}?q={{ urlencode($term) }}">Ver página</a>
                    <button type="button" class="fav-btn" data-fav-page="{{ $item['page'] }}"><span class="fav-icon">☆</span><span>Favorito</span></button>
                </div>
            </li>
        @empty
            <li>No results found.</li>
        @endforelse
    </ul>
</section>
@endif

<script>
(function() {
    const seenKey = 'seenPages';
    const favKey = 'favoritePages';
    const currentPage = {{ (int) $page }};
    const qParam = '{{ urlencode($term) }}';
    const qString = qParam ? `?q=${qParam}` : '';

    const favTopWrap = document.getElementById('favorites-top-wrap');
    const favTop = document.getElementById('fav-badges-top');

    const searchBtn = document.createElement('button');
    searchBtn.className = 'selection-search hidden';
    document.body.appendChild(searchBtn);

    function loadSeen() {
        try { return JSON.parse(localStorage.getItem(seenKey) || '[]'); } catch (e) { return []; }
    }

    function saveSeen(list) {
        const unique = Array.from(new Set(list.filter(Number.isFinite)));
        localStorage.setItem(seenKey, JSON.stringify(unique));
        return unique;
    }

    function loadFav() {
        try { return JSON.parse(localStorage.getItem(favKey) || '[]'); } catch (e) { return []; }
    }

    function saveFav(list) {
        const unique = Array.from(new Set(list.filter(Number.isFinite)));
        localStorage.setItem(favKey, JSON.stringify(unique));
        return unique;
    }

    let seen = loadSeen();
    let fav = loadFav();
    if (!seen.includes(currentPage)) {
        seen.push(currentPage);
        seen = saveSeen(seen);
    }

    function renderBars() {
        const seenBar = document.getElementById('seen-badges');
        const favBar = document.getElementById('fav-badges');
        if (seenBar) {
            seenBar.innerHTML = '';
            seen.forEach((p) => {
                const el = document.createElement(p === currentPage ? 'span' : 'a');
                el.className = 'badge viewed' + (p === currentPage ? ' current' : '');
                if (p !== currentPage) el.href = `/pages/${p}${qString}`;
                el.textContent = p;
                seenBar.appendChild(el);
            });
        }
        if (favBar) {
            favBar.innerHTML = '';
            fav.forEach((p) => {
                favBar.appendChild(buildFavBadge(p));
            });
        }
        renderFavoritesTop();
    }

    const badgeNodes = document.querySelectorAll('[data-page-badge]');
    badgeNodes.forEach((node) => {
        const pageNum = parseInt(node.dataset.pageBadge, 10);
        if (seen.includes(pageNum)) {
            node.classList.add('viewed');
        }
    });

    const itemNodes = document.querySelectorAll('[data-page-item]');
    itemNodes.forEach((node) => {
        const pageNum = parseInt(node.dataset.pageItem, 10);
        if (seen.includes(pageNum)) {
            node.classList.add('viewed');
        }
        if (pageNum === currentPage) {
            node.classList.add('current');
        }
        const favBtn = node.querySelector('[data-fav-page]');
        if (favBtn) {
            updateFavBtn(favBtn, pageNum);
            favBtn.addEventListener('click', (e) => {
                e.preventDefault();
                if (fav.includes(pageNum)) {
                    fav = saveFav(fav.filter((p) => p !== pageNum));
                } else {
                    fav = saveFav([...fav, pageNum]);
                }
                updateFavBtn(favBtn, pageNum);
                renderBars();
            });
        }
    });

    const grid = document.querySelector('.grid');
    const btnGrid = document.querySelector('[data-view="grid"]');
    const btnList = document.querySelector('[data-view="list"]');

    function setView(mode) {
        if (!grid) return;
        grid.dataset.view = mode;
        if (btnGrid && btnList) {
            btnGrid.classList.toggle('active', mode === 'grid');
            btnList.classList.toggle('active', mode === 'list');
        }
    }

    btnGrid?.addEventListener('click', () => setView('grid'));
    btnList?.addEventListener('click', () => setView('list'));

    const pageSelect = document.getElementById('page-select');
    if (pageSelect) {
        pageSelect.addEventListener('change', (e) => {
            const target = e.target;
            const value = parseInt(target.value, 10);
            if (Number.isFinite(value)) {
                window.location.href = `/pages/${value}${qString}`;
            }
        });
    }

    setView('grid');
    renderBars();
    renderFavoritesTop();

    document.addEventListener('mouseup', () => {
        const sel = window.getSelection();
        if (!sel || sel.rangeCount === 0) { hideSearch(); return; }
        const text = sel.toString().trim();
        if (!text || text.length < 2 || text.length > 60) { hideSearch(); return; }
        const range = sel.getRangeAt(0).cloneRange();
        const rect = range.getBoundingClientRect();
        const top = rect.bottom + window.scrollY + 6;
        const left = rect.left + window.scrollX;
        showSearch(text, { top, left });
    });

    function showSearch(term, pos) {
        searchBtn.textContent = `Buscar "${term.slice(0, 24)}${term.length > 24 ? '…' : ''}"`;
        searchBtn.style.top = `${pos.top}px`;
        searchBtn.style.left = `${pos.left}px`;
        searchBtn.classList.remove('hidden');
        searchBtn.onclick = () => {
            window.location.href = `/pages/${currentPage}?q=${encodeURIComponent(term)}`;
        };
    }

    function hideSearch() {
        searchBtn.classList.add('hidden');
    }

    function updateFavBtn(btn, pageNum) {
        const isFav = fav.includes(pageNum);
        const isCurrentFav = isFav && pageNum === currentPage;
        btn.classList.toggle('is-fav', isFav);
        btn.textContent = isFav ? '★' : '☆';
        btn.disabled = isCurrentFav;
        btn.title = isCurrentFav ? 'Ya estás en esta página favorita' : 'Marcar como favorito';
    }

    function renderFavoritesTop() {
        if (!favTop || !favTopWrap) return;
        favTop.innerHTML = '';
        if (!fav.length) {
            favTopWrap.style.display = 'none';
            return;
        }
        favTopWrap.style.display = 'flex';
        fav.forEach((p) => {
            favTop.appendChild(buildFavBadge(p));
        });
    }

    function buildFavBadge(p) {
        const isCurrent = p === currentPage;
        const el = document.createElement(isCurrent ? 'span' : 'a');
        el.className = 'badge favorite closable' + (isCurrent ? ' current' : '');
        if (!isCurrent) el.href = `/pages/${p}${qString}`;
        el.textContent = p;

        const close = document.createElement('button');
        close.type = 'button';
        close.className = 'badge-close';
        close.textContent = '×';
        close.title = 'Quitar de favoritos';
        close.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            fav = saveFav(fav.filter((v) => v !== p));
            renderBars();
            updateFavButtons();
        });

        el.appendChild(close);
        return el;
    }

    function updateFavButtons() {
        const itemNodes = document.querySelectorAll('[data-page-item]');
        itemNodes.forEach((node) => {
            const pageNum = parseInt(node.dataset.pageItem, 10);
            const favBtn = node.querySelector('[data-fav-page]');
            if (favBtn) {
                updateFavBtn(favBtn, pageNum);
            }
        });
    }
})();
</script>

</body>
</html>
