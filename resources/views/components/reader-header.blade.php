<div class="reader-header">
    <div class="reader-header-content">
        <div class="book-info">
            <div class="book-info-cover">
                <i class="fas fa-book"></i>
            </div>
            <div class="book-info-details">
                <h1 id="bookTitle">Eloquent JavaScript</h1>
                <p id="bookAuthor">by Marijn Haverbeke</p>
            </div>
        </div>

        <div class="reader-actions">
            <button class="reader-actions-button" data-bs-toggle="modal" data-bs-target="#searchModal">
                <i class="fas fa-search"></i>
                <span>Search</span>
            </button>
            <button class="reader-actions-button" onclick="toggleBookmark()" title="Toggle Bookmark">
                <i class="fas fa-bookmark" id="bookmarkIcon"></i>
                <span id="bookmarkText">Bookmark</span>
            </button>
            <button class="reader-actions-button" data-bs-toggle="modal" data-bs-target="#settingsModal">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </button>
        </div>
    </div>
</div>
