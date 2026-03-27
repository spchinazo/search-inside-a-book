import '../sass/app.scss';

let form;
let input;
let statusEl;
let resultsListEl;
let resultsGridEl;
let resultsBlock;
let resultsHeading;
let favoritesBlock;
let favoritesTop;
let viewer;
let viewerContent;
let viewerTitle;
let viewerMeta;
let closeViewer;
let viewButtons;
let currentView = 'grid';
let hasSearched = false;
let suggestionsBox;
let suggestionsList;
let suggestTimer;

const seenKey = 'seenPages';
const favKey = 'favoritePages';
let seenPages = [];
let favoritePages = [];

let lastResults = [];
let lastTerm = '';
let currentPage = 1;
let totalPages = 1;
const perPage = 20;

function escapeHtml(value) {
	return value
		.replace(/&/g, '&amp;')
		.replace(/</g, '&lt;')
		.replace(/>/g, '&gt;')
		.replace(/"/g, '&quot;')
		.replace(/'/g, '&#039;');
}

function highlightSnippet(snippet, term) {
	if (!term) return escapeHtml(snippet);
	const escaped = escapeHtml(snippet);
	try {
		const re = new RegExp(`(${term.replace(/[-/\\^$*+?.()|[\]{}]/g, '\\$&')})`, 'gi');
		return escaped.replace(re, '<mark>$1</mark>');
	} catch (e) {
		return escaped;
	}
}

document.addEventListener('DOMContentLoaded', () => {
	form = document.getElementById('search-form');
	input = document.getElementById('search-term');
	statusEl = document.getElementById('status');
	resultsListEl = document.getElementById('results-list');
	resultsGridEl = document.getElementById('results-grid');
	resultsBlock = document.getElementById('results-block');
	resultsHeading = document.getElementById('results-heading');
	favoritesBlock = document.getElementById('favorites-block');
	favoritesTop = document.getElementById('fav-badges-top');
	suggestionsBox = document.getElementById('suggestions');
	suggestionsList = document.getElementById('suggestions-list');
	viewer = document.getElementById('page-viewer');
	viewerContent = document.getElementById('viewer-content');
	viewerTitle = document.getElementById('viewer-title');
	viewerMeta = document.getElementById('viewer-meta');
	closeViewer = document.getElementById('close-viewer');

	if (form && input && statusEl) {
		form.addEventListener('submit', (event) => {
			event.preventDefault();
			event.stopPropagation();
			const term = input.value.trim();
			if (term.length < 2) {
				statusEl.textContent = 'Ingresa al menos 2 caracteres.';
				return;
			}
			currentPage = 1;
			search(term, currentPage);
			renderSuggestions([]);
		});

		input.addEventListener('input', () => {
			const term = input.value.trim();
			if (!term) {
				renderSuggestions([]);
				fetchSuggestions('');
				return;
			}
			clearTimeout(suggestTimer);
			suggestTimer = setTimeout(() => fetchSuggestions(term), 260);
		});

		input.addEventListener('focus', () => {
			const term = input.value.trim();
			fetchSuggestions(term);
		});
	}

	if (closeViewer && viewer) {
		closeViewer.addEventListener('click', () => {
			viewer.hidden = true;
		});
	}
	viewButtons = document.querySelectorAll('.view-controls .icon-toggle');

	initViewMode();
	seenPages = loadSeen();
	favoritePages = loadFavorites();
	if (resultsListEl) resultsListEl.hidden = true;
	if (resultsGridEl) resultsGridEl.hidden = true;
	if (resultsBlock) resultsBlock.hidden = true;
	renderFavoritesTop();
});

async function fetchSuggestions(term) {
	if (!term) {
		try {
			const resp = await fetch('/api/search/suggest?limit=8');
			if (!resp.ok) throw new Error('fail');
			const data = await resp.json();
			renderSuggestions(data.data ?? [], term);
		} catch (e) {
			renderSuggestions([]);
		}
		return;
	}
	try {
		const resp = await fetch(`/api/search/suggest?q=${encodeURIComponent(term)}&limit=8`);
		if (!resp.ok) {
			throw new Error('fail');
		}
		const data = await resp.json();
		renderSuggestions(data.data ?? [], term);
	} catch (e) {
		renderSuggestions([]);
	}
}

function initViewMode() {
	const saved = loadViewMode();
	setView(saved || 'grid');
	viewButtons?.forEach((btn) => {
		btn.addEventListener('click', () => {
			const mode = btn.dataset.view;
			setView(mode);
			saveViewMode(mode);
		});
	});
}

function loadViewMode() {
	try { return localStorage.getItem('resultsViewMode'); } catch (e) { return null; }
}

function saveViewMode(mode) {
	try { localStorage.setItem('resultsViewMode', mode); } catch (e) { /* ignore */ }
}

function setView(mode) {
	currentView = mode === 'list' ? 'list' : 'grid';
	if (resultsListEl && resultsGridEl) {
		if (!hasSearched) {
			resultsListEl.hidden = true;
			resultsGridEl.hidden = true;
		} else {
			resultsListEl.hidden = currentView !== 'list';
			resultsGridEl.hidden = currentView !== 'grid';
		}
	}
	viewButtons?.forEach((btn) => btn.classList.toggle('active', btn.dataset.view === currentView));
}

function loadSeen() {
	try { return JSON.parse(localStorage.getItem(seenKey) || '[]'); } catch (e) { return []; }
}

function saveSeen(list) {
	const unique = Array.from(new Set(list.filter(Number.isFinite)));
	try { localStorage.setItem(seenKey, JSON.stringify(unique)); } catch (e) { /* ignore */ }
	return unique;
}

function loadFavorites() {
	try { return JSON.parse(localStorage.getItem(favKey) || '[]'); } catch (e) { return []; }
}

function saveFavorites(list) {
	const unique = Array.from(new Set(list.filter(Number.isFinite)));
	try { localStorage.setItem(favKey, JSON.stringify(unique)); } catch (e) { /* ignore */ }
	return unique;
}
async function search(term, page = 1) {
	const url = `/api/search?q=${encodeURIComponent(term)}&page=${page}&per_page=${perPage}`;
	statusEl.textContent = 'Buscando...';

	try {
		const resp = await fetch(url);
		if (!resp.ok) {
			throw new Error(`Error ${resp.status}`);
		}

		const data = await resp.json();
		const results = data.data ?? [];
		hasSearched = true;
		lastResults = results;
		lastTerm = term;
		totalPages = Math.max(1, data.meta?.total_pages ?? 1);
		const incomingPage = parseInt(data.meta?.page, 10) || 1;
		currentPage = Math.min(Math.max(1, incomingPage), totalPages);

		if (results.length === 0) {
			statusEl.textContent = 'Sin resultados para este término.';
			renderList([]);
			renderGrid([]);
			setView(currentView);
			if (resultsBlock) resultsBlock.hidden = false;
			if (resultsHeading) {
				resultsHeading.hidden = false;
				resultsHeading.textContent = `Resultados para "${term}" — 0 encontrados`;
			}
			return;
		}

		const total = data.meta?.total ?? results.length;
		statusEl.textContent = `${results.length} resultado(s) de ${total}`;
		if (resultsHeading) {
			resultsHeading.hidden = false;
			resultsHeading.textContent = `Resultados para "${term}" — ${total} encontrados`;
		}
		seenPages = loadSeen();
		favoritePages = loadFavorites();
		renderList(results);
		renderGrid(results);
		setView(currentView);
		renderBars();
		renderFavoritesTop();
		renderSuggestions([]);
		renderPager();
		if (resultsBlock) resultsBlock.hidden = false;
	} catch (err) {
		hasSearched = false;
		statusEl.textContent = 'No se pudo completar la búsqueda.';
		console.error(err);
	}
}

function renderList(results) {
	if (!resultsListEl) return;
	resultsListEl.innerHTML = '';

	results.forEach((item) => {
		const li = document.createElement('li');
		li.className = 'result-item';
		li.dataset.pageId = item.page;

		const header = document.createElement('div');
		header.className = 'result-header';
		header.textContent = `Página ${item.page}`;

		const snippet = document.createElement('p');
		snippet.className = 'snippet';
		snippet.innerHTML = highlightSnippet(item.snippet, lastTerm);

		const link = document.createElement('a');
		link.href = `/pages/${item.page_id}${lastTerm ? `?q=${encodeURIComponent(lastTerm)}` : ''}`;
		link.className = 'open-link';
		link.textContent = 'Ver página';
		link.addEventListener('click', () => {
			seenPages = saveSeen([...seenPages, item.page]);
		});

		const favBtn = document.createElement('button');
		favBtn.type = 'button';
	favBtn.className = 'fav-btn';
	favBtn.textContent = '☆';
	favBtn.addEventListener('click', (e) => {
		e.preventDefault();
		favoritePages = toggleFavorite(item.page);
		updateFavButton(favBtn, item.page);
		decorateResultItem(li, item.page);
		renderBars();
		renderFavoritesTop();
	});

		const actions = document.createElement('div');
		actions.className = 'action-row';
		actions.appendChild(link);
		actions.appendChild(favBtn);

		li.appendChild(header);
		li.appendChild(snippet);
		li.appendChild(actions);

		decorateResultItem(li, item.page);
		resultsListEl.appendChild(li);
	});
}

function renderGrid(results) {
	if (!resultsGridEl) return;
	resultsGridEl.innerHTML = '';

	results.forEach((item) => {
		const li = document.createElement('li');
		li.className = 'result-item';
		li.dataset.pageId = item.page;

		const header = document.createElement('div');
		header.className = 'result-header';
		header.textContent = `Página ${item.page}`;

		const snippet = document.createElement('p');
		snippet.className = 'snippet';
		snippet.innerHTML = highlightSnippet(item.snippet, lastTerm);

		const link = document.createElement('a');
		link.href = `/pages/${item.page_id}${lastTerm ? `?q=${encodeURIComponent(lastTerm)}` : ''}`;
		link.className = 'open-link';
		link.textContent = 'Ver página';
		link.addEventListener('click', () => {
			seenPages = saveSeen([...seenPages, item.page]);
		});

		const favBtn = document.createElement('button');
		favBtn.type = 'button';
	favBtn.className = 'fav-btn';
	favBtn.textContent = '☆';
	favBtn.addEventListener('click', (e) => {
		e.preventDefault();
		favoritePages = toggleFavorite(item.page);
		updateFavButton(favBtn, item.page);
		decorateResultItem(li, item.page);
		renderBars();
		renderFavoritesTop();
	});

		const actions = document.createElement('div');
		actions.className = 'action-row';
		actions.appendChild(link);
		actions.appendChild(favBtn);

		li.appendChild(header);
		li.appendChild(snippet);
		li.appendChild(actions);

		decorateResultItem(li, item.page);
		resultsGridEl.appendChild(li);
	});
}

function decorateResultItem(node, pageId) {
	if (!node) return;
	if (seenPages.includes(pageId)) {
		node.classList.add('viewed');
	}
	if (favoritePages.includes(pageId)) {
		node.classList.add('favorite');
	} else {
		node.classList.remove('favorite');
	}
	const favBtn = node.querySelector('.fav-btn');
	if (favBtn) {
		updateFavButton(favBtn, pageId);
	}
	node.addEventListener('pointerenter', () => node.classList.add('current'));
	node.addEventListener('pointerleave', () => node.classList.remove('current'));
}

function toggleFavorite(pageId) {
	if (favoritePages.includes(pageId)) {
		return saveFavorites(favoritePages.filter((p) => p !== pageId));
	}
	return saveFavorites([...favoritePages, pageId]);
}

function updateFavButton(btn, pageId) {
	if (!btn) return;
	const isFav = favoritePages.includes(pageId);
	btn.classList.toggle('is-fav', isFav);
	btn.textContent = isFav ? '★' : '☆';
}

function renderBars() {
	const seenBar = document.getElementById('seen-badges');
	const favBar = document.getElementById('fav-badges');
	if (seenBar) {
		seenBar.innerHTML = '';
		seenPages.forEach((p) => {
			const a = document.createElement('a');
			a.className = 'badge viewed';
			a.href = `/pages/${p}${lastTerm ? `?q=${encodeURIComponent(lastTerm)}` : ''}`;
			a.textContent = p;
			seenBar.appendChild(a);
		});
	}
	if (favBar) {
		favBar.innerHTML = '';
		favoritePages.forEach((p) => {
			favBar.appendChild(buildFavoriteBadge(p));
		});
	}
}

function renderFavoritesTop() {
	if (!favoritesTop || !favoritesBlock) return;
	favoritesTop.innerHTML = '';
	if (favoritePages.length === 0) {
		favoritesBlock.hidden = true;
		return;
	}
	favoritePages.forEach((p) => {
		favoritesTop.appendChild(buildFavoriteBadge(p));
	});
	favoritesBlock.hidden = false;
}

function buildFavoriteBadge(pageId) {
	const isCurrent = false;
	const link = document.createElement('a');
	link.className = 'badge favorite closable';
	link.href = `/pages/${pageId}${lastTerm ? `?q=${encodeURIComponent(lastTerm)}` : ''}`;
	link.textContent = pageId;
	link.dataset.pageId = pageId;

	const close = document.createElement('button');
	close.type = 'button';
	close.className = 'badge-close';
	close.textContent = '×';
	close.title = 'Quitar de favoritos';
	close.addEventListener('click', (e) => {
		e.preventDefault();
		e.stopPropagation();
		favoritePages = saveFavorites(favoritePages.filter((p) => p !== pageId));
		renderBars();
		renderFavoritesTop();
		refreshResultFavorites();
	});

	link.appendChild(close);
	return link;
}

function refreshResultFavorites() {
	const nodes = document.querySelectorAll('[data-page-id]');
	nodes.forEach((node) => {
		const pageId = parseInt(node.dataset.pageId ?? node.dataset.pageid, 10);
		decorateResultItem(node, pageId);
	});
}

function renderSuggestions(items, typed = '') {
	if (!suggestionsBox || !suggestionsList) return;
	suggestionsList.innerHTML = '';
	if (!items || items.length === 0) {
		suggestionsBox.hidden = true;
		return;
	}
	items.forEach((item) => {
		const btn = document.createElement('button');
		btn.type = 'button';
		btn.className = 'suggestion-item';
		btn.innerHTML = `<span class="suggestion-term">${escapeHtml(item.term ?? '')}</span>` +
			`<span class="suggestion-meta">${item.times ?? 0} búsquedas</span>`;
		btn.addEventListener('click', () => {
			if (input) {
				input.value = item.term ?? '';
			}
			renderSuggestions([]);
			if (item.term) {
				currentPage = 1;
				search(item.term, currentPage);
			}
		});
		suggestionsList.appendChild(btn);
	});
	suggestionsBox.hidden = false;
}

async function loadPage(pageId) {
	// Navigation happens via link; keep function unused.
}

function renderPager() {
	const pager = document.getElementById('pager');
	const pagerInfo = document.getElementById('pager-info');
	const prevBtn = document.getElementById('pager-prev');
	const nextBtn = document.getElementById('pager-next');

	if (!pager || !pagerInfo || !prevBtn || !nextBtn) return;

	if (!hasSearched || !lastTerm || totalPages <= 1) {
		pager.hidden = true;
		return;
	}

	pager.hidden = false;
	pagerInfo.textContent = `Page ${currentPage} of ${totalPages}`;
	prevBtn.disabled = currentPage <= 1;
	nextBtn.disabled = currentPage >= totalPages;

	prevBtn.onclick = () => {
		if (currentPage > 1) {
			const target = Math.max(1, currentPage - 1);
			search(lastTerm, target);
		}
	};

	nextBtn.onclick = () => {
		if (currentPage < totalPages) {
			const target = Math.min(totalPages, currentPage + 1);
			search(lastTerm, target);
		}
	};
}
