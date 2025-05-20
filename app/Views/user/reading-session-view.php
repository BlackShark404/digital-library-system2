<?php
include $headerPath;
?>

<style>
    /* Make iframe content scrollable */
    #epub-viewer iframe {
        overflow-y: auto !important;
    }
    #epub-viewer {
        background-color: #f8f9fa;
    }
</style>

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">
                    <a href="/user/reading-sessions" class="text-decoration-none">
                        <i class="bi bi-arrow-left me-2"></i>
                    </a>
                    Reading: <?= htmlspecialchars($session['b_title']) ?>
                </h1>
                <div>
                    <?php if ($session['status'] === 'active' && !$session['is_purchased']): ?>
                        <span class="badge bg-warning text-dark me-2">
                            <i class="bi bi-clock"></i> Expires in <?= ceil((strtotime($session['rs_expires_at']) - time()) / 86400) ?> days
                        </span>
                    <?php elseif ($session['is_purchased']): ?>
                        <span class="badge bg-success me-2">
                            <i class="bi bi-check-circle"></i> Owned
                        </span>
                    <?php endif; ?>
                    <button id="fullScreenBtn" class="btn btn-sm btn-secondary me-2" title="Toggle Fullscreen">
                        <i class="bi bi-fullscreen"></i>
                    </button>
                    <button id="settingsBtn" class="btn btn-sm btn-secondary" title="Settings" data-bs-toggle="modal" data-bs-target="#settingsModal">
                        <i class="bi bi-gear"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <!-- EPUB Reader Container -->
                    <div id="epub-reader" class="epub-container">
                        <div id="reader-loading" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-3">Loading book...</p>
                        </div>
                        <div id="epub-viewer" style="height: 80vh; overflow-y: auto;"></div>
                        
                        <div id="reader-controls" class="p-2 bg-light border-top d-none">
                            <div class="container-fluid">
                                <div class="row align-items-center">
                                    <div class="col-4">
                                        <button id="prev" class="btn btn-sm btn-outline-primary me-2">
                                            <i class="bi bi-chevron-left"></i> Previous
                                        </button>
                                        <button id="next" class="btn btn-sm btn-outline-primary">
                                            Next <i class="bi bi-chevron-right"></i>
                                        </button>
                                    </div>
                                    <div class="col-4 text-center">
                                        <span id="book-progress">Page <span id="current-page">0</span> of <span id="total-pages">0</span></span>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="btn-group" role="group" aria-label="Navigation">
                                            <button id="toc-toggle" class="btn btn-sm btn-outline-secondary" title="Table of Contents">
                                                <i class="bi bi-list-ul"></i>
                                            </button>
                                            <button id="bookmark-btn" class="btn btn-sm btn-outline-secondary" title="Bookmark">
                                                <i class="bi bi-bookmark"></i>
                                            </button>
                                            <button id="search-btn" class="btn btn-sm btn-outline-secondary" title="Search" data-bs-toggle="modal" data-bs-target="#searchModal">
                                                <i class="bi bi-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Table of Contents Sidebar -->
    <div id="toc-sidebar" class="offcanvas offcanvas-start" tabindex="-1">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Table of Contents</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div id="toc-content" class="list-group list-group-flush">
                <!-- TOC will be populated here -->
            </div>
        </div>
    </div>
    
    <!-- Settings Modal -->
    <div class="modal fade" id="settingsModal" tabindex="-1" aria-labelledby="settingsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="settingsModalLabel">Reader Settings</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="font-size-range" class="form-label">Font Size</label>
                        <input type="range" class="form-range" min="80" max="150" step="10" value="100" id="font-size-range">
                    </div>
                    <div class="mb-3">
                        <label for="theme-select" class="form-label">Theme</label>
                        <select class="form-select" id="theme-select">
                            <option value="white">Light Mode</option>
                            <option value="sepia">Sepia Mode</option>
                            <option value="night">Dark Mode</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="line-height-range" class="form-label">Line Height</label>
                        <input type="range" class="form-range" min="100" max="200" step="10" value="140" id="line-height-range">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="save-settings">Save Settings</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Search Modal -->
    <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="searchModalLabel">Search</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="search-input" placeholder="Search text...">
                        <button class="btn btn-primary" type="button" id="perform-search">Search</button>
                    </div>
                    <div id="search-results" class="list-group">
                        <!-- Search results will be shown here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include required libraries -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/epubjs/dist/epub.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let book, rendition;
    const sessionId = <?= $session['rs_id'] ?>;
    let currentLocation = '';
    const bookPath = "<?= $filePath ?>";
    
    // Book Elements
    const viewer = document.getElementById('epub-viewer');
    const prevButton = document.getElementById('prev');
    const nextButton = document.getElementById('next');
    const tocToggle = document.getElementById('toc-toggle');
    const tocSidebar = document.getElementById('toc-sidebar');
    const tocContent = document.getElementById('toc-content');
    const currentPageEl = document.getElementById('current-page');
    const totalPagesEl = document.getElementById('total-pages');
    const loadingEl = document.getElementById('reader-loading');
    const controlsEl = document.getElementById('reader-controls');
    const readerContainer = document.getElementById('epub-reader');
    const fullScreenBtn = document.getElementById('fullScreenBtn');
    
    // Initialize EPUB.js reader
    initializeReader();
    
    function initializeReader() {
        // Create a new Book
        try {
            console.log("Loading book from path:", bookPath);
            book = ePub(bookPath);
            
            // Generate rendition
            rendition = book.renderTo(viewer, {
                width: '100%',
                height: '100%',
                flow: 'scrolled-doc',
                spread: 'none'
            });
            
            // Initial location (use saved location or start at beginning)
            const savedPage = <?= $session['current_page'] ?? 1 ?>;
            
            // Set up error handling on book loading
            book.ready.then(() => {
                // Hide loading spinner and show controls
                loadingEl.classList.add('d-none');
                controlsEl.classList.remove('d-none');
                
                console.log("Book loaded successfully");
                
                // Set initial location or chapter based on saved progress
                if (savedPage > 1) {
                    book.locations.generate().then(() => {
                        const location = book.locations.cfiFromPercentage((savedPage - 1) / book.locations.total);
                        rendition.display(location);
                    });
                } else {
                    rendition.display();
                }
                
                // Update total pages
                book.locations.generate(1024).then(() => {
                    totalPagesEl.textContent = book.locations.total || '?';
                    updatePageCountDisplay();
                });
                
                // Generate and display the table of contents
                book.loaded.navigation.then(toc => {
                    generateTOC(toc.toc);
                });
            }).catch(error => {
                console.error("Error loading book:", error);
                loadingEl.innerHTML = `<div class="alert alert-danger">
                    <h4>Error Loading Book</h4>
                    <p>${error.message || 'Could not load the book file.'}</p>
                    <p>Please try a different book or contact support.</p>
                </div>`;
            });
            
            // Event listeners for page change
            rendition.on('locationChanged', function(location) {
                currentLocation = location.start.cfi;
                updatePageCountDisplay();
                
                // Save progress
                saveReadingProgress();
            });
            
            // Add error handling for rendition
            rendition.on('rendered', (section) => {
                console.log('Rendered section:', section.href);
            });
            
            rendition.on('relocated', (location) => {
                console.log('Relocated to:', location);
            });
            
            // Set up event listeners
            setUpEventListeners();
            
            // Apply any saved reader settings
            applyReaderSettings();
        } catch (error) {
            console.error("Error initializing book:", error);
            loadingEl.innerHTML = `<div class="alert alert-danger">
                <h4>Error Initializing Book</h4>
                <p>${error.message || 'Could not initialize the book reader.'}</p>
                <p>Please try a different book or contact support.</p>
            </div>`;
        }
    }
    
    function updatePageCountDisplay() {
        const currentPage = book.locations.percentageFromCfi(currentLocation);
        const pageNum = Math.ceil(currentPage * book.locations.total);
        currentPageEl.textContent = pageNum || 1;
    }
    
    function generateTOC(toc) {
        tocContent.innerHTML = '';
        toc.forEach(chapter => {
            const item = document.createElement('a');
            item.className = 'list-group-item list-group-item-action';
            item.textContent = chapter.label;
            
            if (chapter.href) {
                item.addEventListener('click', () => {
                    rendition.display(chapter.href);
                    const offcanvas = bootstrap.Offcanvas.getInstance(tocSidebar);
                    offcanvas.hide();
                });
                tocContent.appendChild(item);
            }
            
            if (chapter.subitems && chapter.subitems.length > 0) {
                chapter.subitems.forEach(subitem => {
                    const subChapter = document.createElement('a');
                    subChapter.className = 'list-group-item list-group-item-action ps-4';
                    subChapter.textContent = subitem.label;
                    
                    if (subitem.href) {
                        subChapter.addEventListener('click', () => {
                            rendition.display(subitem.href);
                            const offcanvas = bootstrap.Offcanvas.getInstance(tocSidebar);
                            offcanvas.hide();
                        });
                        tocContent.appendChild(subChapter);
                    }
                });
            }
        });
    }
    
    function setUpEventListeners() {
        // Navigation buttons
        prevButton.addEventListener('click', () => {
            rendition.prev();
        });
        
        nextButton.addEventListener('click', () => {
            rendition.next();
        });
        
        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                rendition.prev();
            } else if (e.key === 'ArrowRight') {
                rendition.next();
            }
        });
        
        // Toggle table of contents sidebar
        tocToggle.addEventListener('click', () => {
            // Exit fullscreen first if we're in fullscreen mode
            if (document.fullscreenElement) {
                document.exitFullscreen().then(() => {
                    setTimeout(() => {
                        const offcanvas = new bootstrap.Offcanvas(tocSidebar);
                        offcanvas.show();
                    }, 300); // Short delay to allow fullscreen transition
                }).catch(err => {
                    console.error('Error exiting fullscreen:', err);
                    // Try to show offcanvas anyway
                    const offcanvas = new bootstrap.Offcanvas(tocSidebar);
                    offcanvas.show();
                });
            } else {
                const offcanvas = new bootstrap.Offcanvas(tocSidebar);
                offcanvas.show();
            }
        });
        
        // Fullscreen toggle
        fullScreenBtn.addEventListener('click', () => {
            if (!document.fullscreenElement) {
                readerContainer.requestFullscreen().catch(err => {
                    console.error(`Error attempting to enable fullscreen: ${err.message}`);
                });
            } else {
                document.exitFullscreen();
            }
        });
        
        // Handle fullscreen changes
        document.addEventListener('fullscreenchange', () => {
            if (document.fullscreenElement) {
                // We're in fullscreen mode
                viewer.style.height = '90vh';
            } else {
                // Exited fullscreen mode
                viewer.style.height = '80vh';
            }
            if (rendition) {
                rendition.resize();
            }
        });
        
        // Save settings button
        document.getElementById('save-settings').addEventListener('click', () => {
            saveAndApplySettings();
            const modal = bootstrap.Modal.getInstance(document.getElementById('settingsModal'));
            modal.hide();
        });
        
        // Search functionality
        document.getElementById('perform-search').addEventListener('click', performSearch);
    }
    
    function saveReadingProgress() {
        if (!currentLocation) return;
        
        const currentPage = book.locations.percentageFromCfi(currentLocation);
        const pageNum = Math.ceil(currentPage * book.locations.total) || 1;
        
        // Save reading progress via AJAX
        fetch('/user/reading-sessions/update-progress', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                session_id: sessionId,
                current_page: pageNum,
                is_completed: pageNum >= book.locations.total
            })
        })
        .then(response => response.json())
        .catch(error => {
            console.error('Error saving reading progress:', error);
        });
    }
    
    function saveAndApplySettings() {
        const fontSize = document.getElementById('font-size-range').value;
        const theme = document.getElementById('theme-select').value;
        const lineHeight = document.getElementById('line-height-range').value;
        
        // Save settings to localStorage
        localStorage.setItem('epub-reader-settings', JSON.stringify({
            fontSize,
            theme,
            lineHeight
        }));
        
        // Apply settings
        applyReaderSettings();
    }
    
    function applyReaderSettings() {
        // Get settings from localStorage or use defaults
        const settings = JSON.parse(localStorage.getItem('epub-reader-settings')) || {
            fontSize: 100,
            theme: 'white',
            lineHeight: 140
        };
        
        // Update UI controls
        document.getElementById('font-size-range').value = settings.fontSize;
        document.getElementById('theme-select').value = settings.theme;
        document.getElementById('line-height-range').value = settings.lineHeight;
        
        // Apply to rendition
        if (rendition) {
            // Apply font size
            rendition.themes.fontSize(`${settings.fontSize}%`);
            
            // Apply theme
            if (settings.theme === 'night') {
                rendition.themes.register('night', {
                    body: {
                        color: '#d7d7d7',
                        background: '#121212'
                    }
                });
                rendition.themes.select('night');
                viewer.style.backgroundColor = '#121212';
            } else if (settings.theme === 'sepia') {
                rendition.themes.register('sepia', {
                    body: {
                        color: '#5b4636',
                        background: '#f4ecd8'
                    }
                });
                rendition.themes.select('sepia');
                viewer.style.backgroundColor = '#f4ecd8';
            } else {
                rendition.themes.register('default', {
                    body: {
                        color: '#000',
                        background: '#fff'
                    }
                });
                rendition.themes.select('default');
                viewer.style.backgroundColor = '#ffffff';
            }
            
            // Apply line height
            rendition.themes.override('line-height', `${settings.lineHeight}%`);
        }
    }
    
    function performSearch() {
        const searchTerm = document.getElementById('search-input').value.trim();
        const resultsContainer = document.getElementById('search-results');
        
        if (!searchTerm) return;
        
        resultsContainer.innerHTML = '<div class="text-center p-3"><div class="spinner-border spinner-border-sm" role="status"></div> Searching...</div>';
        
        book.search(searchTerm).then(results => {
            resultsContainer.innerHTML = '';
            
            if (results.length === 0) {
                resultsContainer.innerHTML = '<div class="text-center p-3">No results found</div>';
                return;
            }
            
            results.forEach(result => {
                const resultItem = document.createElement('button');
                resultItem.className = 'list-group-item list-group-item-action text-start';
                resultItem.innerHTML = `<small class="text-muted">${result.cfi.split('!')[0].replace(/[\[\]\/]/g, '')}</small><br>${result.excerpt}`;
                
                resultItem.addEventListener('click', () => {
                    rendition.display(result.cfi);
                    const modal = bootstrap.Modal.getInstance(document.getElementById('searchModal'));
                    modal.hide();
                });
                
                resultsContainer.appendChild(resultItem);
            });
        });
    }
});
</script>

<?php
include $footerPath;
?> 