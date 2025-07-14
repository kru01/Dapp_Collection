<?php include('views/partials/header.php') ?>

<div class="container my-4">
    <h3>Add New Paper</h3>
    <form method="post" action="index.php?controller=paper&action=save" id="paperForm">
        <div class="mb-3">
            <label class="form-label" for='title'>Title</label>
            <input name="title" id='title' class="form-control" maxlength="255" required>
        </div>
        <div class="mb-3">
            <label class="form-label" for='abstract'>Abstract</label>
            <textarea name="abstract" id='abstract' rows="4" class="form-control" maxlength="1000" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label" for='topic'>Topic</label>
            <select name="topic" id='topic' class="form-select" required>
                <option value="">-- Select Topic --</option>
                <?php foreach ($topics as $t): ?>
                    <option value="<?= $t['topic_id'] ?>"><?= htmlspecialchars($t['topic_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label" for='confInput'>Conference
                <a data-bs-toggle="tooltip" data-bs-title="Search by conference name or abbreviation"
                    data-bs-placement="right" tabindex="0" class="link-primary">
                    <i class="bi bi-info-circle"></i>
                </a>
            </label>
            <input type="text" id="confInput" class="form-control" placeholder="Search conference..." autocomplete="off" required>
            <input type="hidden" name="confId" id="confId">
            <div id="confResults" class="list-group mt-1"></div>
        </div>

        <hr>
        <h5>
            <label class="form-label" for='authorSearch'>Add Authors
                <a data-bs-toggle="tooltip" data-bs-title="Search by username, full name, or email.<br>Prefix with `#` to search by ID."
                    data-bs-placement="right" data-bs-html='true' tabindex="0" class="link-primary">
                    <i class="bi bi-info-circle"></i>
                </a>
            </label>
        </h5>

        <div class="mb-3">
            <input type="text" id="authorSearch" class="form-control" placeholder="Search authors..." autocomplete="off">
            <div id="authorResults" class="list-group mt-1"></div>
        </div>

        <div id="authorPaginationBar"></div>
        <div id="selectedAuthors" class="mt-3"></div>

        <button class="btn btn-success mt-4">Submit Paper</button>
    </form>
</div>

<script src="<?= BASE_URL ?>public/js/pagination_helper.js"></script>
<script>
    /*      ---- LOCAL STORAGE ----
     */
    const handleLocalStorage = (() => {
        const FORM_STORAGE_KEY = 'paperFormDraft';

        function saveFormToStorage(selectedAuthors) {
            const formData = {
                title: document.getElementById('title')?.value || '',
                abstract: document.getElementById('abstract')?.value || '',
                topic: document.getElementById('topic')?.value || '',
                confId: document.getElementById('confId')?.value || '',
                confInput: document.getElementById('confInput')?.value || '',
                authors: selectedAuthors,
            };

            localStorage.setItem(FORM_STORAGE_KEY, JSON.stringify(formData));
        }

        function loadFormFromStorage(selectedAuthors) {
            const saved = localStorage.getItem(FORM_STORAGE_KEY);
            if (!saved) return;

            try {
                const data = JSON.parse(saved);
                if (data.title) document.getElementById('title').value = data.title;
                if (data.abstract) document.getElementById('abstract').value = data.abstract;
                if (data.topic) document.getElementById('topic').value = data.topic;
                if (data.confId) document.getElementById('confId').value = data.confId;
                if (data.confInput) document.getElementById('confInput').value = data.confInput;

                if (data.authors && Array.isArray(data.authors)) {
                    selectedAuthors.splice(0, selectedAuthors.length, ...data.authors);
                }
            } catch (err) {
                console.error('Error loading draft:', err);
            }
        }

        function clearFormStorage() {
            localStorage.removeItem(FORM_STORAGE_KEY);
        }

        return {
            saveFormToStorage,
            clearFormStorage,
            loadFormFromStorage
        };
    })();

    /*      ---- FORM ----
     */
    const handleForm = (() => {
        /*      ---- CONFERENCE ----
         */
        const confInput = document.getElementById('confInput');

        confInput.addEventListener('input', async (e) => {
            const query = e.target.value;
            if (query.length < 1) return;

            const res = await fetch(
                `index.php?controller=paper&action=ajax_search_conference&q=${encodeURIComponent(query)}`
            );

            const data = await res.json();

            const confResults = document.getElementById('confResults');
            confResults.innerHTML = '';

            data.forEach(conf => {
                const item = document.createElement('a');

                item.href = '#';
                item.className = 'list-group-item list-group-item-action'

                item.textContent =
                    `${conf.abbreviation} – ${conf.name} | ${conf.start_date}`;

                item.onclick = () => {
                    confInput.value = item.textContent;
                    document.getElementById('confId').value = conf.id;

                    confResults.innerHTML = '';
                    handleLocalStorage.saveFormToStorage(selectedAuthors);

                    return false;
                };

                confResults.appendChild(item);
            });
        });

        /*      ---- AUTHOR ----
         */
        let selectedAuthors = [];

        let currentPage = 1;
        const perPage = 5;

        const renderSelectedAuthors = () => {
            const container = document.getElementById('selectedAuthors');
            const authorPaginationBar = document.getElementById('authorPaginationBar');

            container.innerHTML = '';
            authorPaginationBar.innerHTML = '';

            // Pagination logic
            const totalPages = Math.max(1, Math.ceil(selectedAuthors.length / perPage));
            currentPage = Math.min(currentPage, totalPages);

            const start = (currentPage - 1) * perPage;
            const end = start + perPage;
            const authorsPage = selectedAuthors.slice(start, end);

            const listGroup = document.createElement('ul');
            listGroup.className = 'list-group';

            authorsPage.forEach((author, index) => {
                const actualIndex = start + index;

                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';

                li.innerHTML =
                    `<dl class="w-100 row mb-0">
                        <dt class="col-sm-1 text-end d-flex align-items-center">
                            #${author.id}
                        </dt>
                        <dd class="col-sm-11 mb-0 d-flex align-items-center gap-3">
                            <a class="d-flex align-items-center gap-2 link-offset-2 link-offset-3-hover me-3
                                link-underline link-underline-opacity-0 link-underline-opacity-75-hover w-50 text-break"
                                href="index.php?controller=author&action=profile&id=${author.id}"
                                target="_blank">
                                <img src="${author.image_path}" class="rounded-circle object-fit-contain"
                                    alt="Profile Image" style="width: 32px; height: 32px;">
                                <span>${author.full_name}</span>
                            </a>
                            <span class="link-underline link-underline-opacity-0 link-secondary fst-italic flex-grow-1">
                                – ${author.username}
                            </span>
                        </dd>
                    </dl>

                    <input type="hidden" name="authors[${actualIndex}][id]" value="${author.id}">
                    <input type="hidden" name="authors[${actualIndex}][full_name]" value="${author.full_name}">`;

                // Role input
                const roleInput = document.createElement('input');
                roleInput.name = `authors[${actualIndex}][role]`;
                roleInput.maxLength = 30;
                roleInput.required = true;
                roleInput.value = author.role;
                roleInput.className = "form-control w-auto me-3 role-select";

                roleInput.addEventListener('input', (e) => {
                    author.role = e.target.value;
                    handleLocalStorage.saveFormToStorage(selectedAuthors);
                });
                roleInput.addEventListener('blur', (e) => {
                    author.role = e.target.value;
                    handleLocalStorage.saveFormToStorage(selectedAuthors);
                });

                // Remove button
                const removeBtn = document.createElement('button');

                removeBtn.type = 'button';
                removeBtn.className = 'btn btn-sm btn-danger';
                removeBtn.textContent = 'Remove';

                removeBtn.onclick = () => {
                    selectedAuthors.splice(actualIndex, 1);

                    const newTotalPages = Math.max(1, Math.ceil(selectedAuthors.length / perPage));
                    if (currentPage > newTotalPages) currentPage = newTotalPages;

                    renderSelectedAuthors();
                    handleLocalStorage.saveFormToStorage(selectedAuthors);
                };

                li.appendChild(roleInput);
                li.appendChild(removeBtn);

                listGroup.appendChild(li);
            });

            container.appendChild(listGroup);

            if (totalPages > 1) {
                handlePagination.renderPaginationBar(
                    authorPaginationBar,
                    currentPage,
                    totalPages,
                    (newPage) => {
                        currentPage = newPage;
                        renderSelectedAuthors();
                    }
                );
            }
        };

        const authorSearch = document.getElementById('authorSearch');

        authorSearch.addEventListener('input', async (e) => {
            const query = e.target.value;
            if (query.length < 1) return;

            const res = await fetch(
                `index.php?controller=paper&action=ajax_search_author&q=${encodeURIComponent(query)}`
            );

            const data = await res.json();

            const authorResults = document.getElementById('authorResults');
            authorResults.innerHTML = '';

            data.forEach(author => {
                const item = document.createElement('a');

                item.href = '#';
                item.className = 'list-group-item list-group-item-action';

                const img = `<img src="${author.image_path}"
                            class="rounded-circle object-fit-contain" alt="Profile Image"
                            style="width: 32px; height: 32px;">`

                item.innerHTML = `#${author.user_id} ${img} ${author.username} – ${author.full_name}`;

                item.onclick = () => {
                    if (!selectedAuthors.find(x => x.id === author.user_id)) {
                        selectedAuthors.push({
                            id: author.user_id,
                            username: author.username,
                            full_name: author.full_name,
                            image_path: author.image_path,
                            role: 'member'
                        });

                        authorSearch.value = '';
                        renderSelectedAuthors();

                        handleLocalStorage.saveFormToStorage(selectedAuthors);
                    }

                    authorResults.innerHTML = '';
                    return false;
                };

                authorResults.appendChild(item);
            });
        });

        /*      ---- INIT ----
         */
        handleLocalStorage.loadFormFromStorage(selectedAuthors);
        renderSelectedAuthors();

        document.getElementById('paperForm').addEventListener('submit', (e) => {
            handleLocalStorage.clearFormStorage();

            const confId = document.getElementById('confId').value;

            if (!confId) {
                e.preventDefault();
                alert('Please select a conference from the list.');
                document.getElementById('confInput').focus();
            }
        });

        // Auto-save on blur
        ['title', 'abstract', 'topic', 'confInput'].forEach(id => {
            const el = document.getElementById(id);

            if (el) {
                el.addEventListener('blur', handleLocalStorage.saveFormToStorage);
                el.addEventListener('input', handleLocalStorage.saveFormToStorage);
            }
        });
    })();
</script>

<?php include('views/partials/footer.php') ?>