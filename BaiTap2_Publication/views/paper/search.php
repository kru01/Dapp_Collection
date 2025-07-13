<?php include("views/partials/header.php"); ?>

<div class="container my-4">
    <h3>Search Papers</h3>

    <form id="searchForm" class="mb-3">
        <div class="row g-3">
            <div class="col-md-6">
                <label for="keyword" class="form-label">Paper Title</label>
                <input type="text" id="keyword" name="keyword" placeholder="Paper title" class="form-control">
            </div>
            <div class="col-md-6">
                <label for="author" class="form-label">
                    Author(s)
                    <a data-bs-toggle="tooltip" data-bs-title='Enter author names separated by ", "<br>E.g., John Smith, jaNE doe'
                        data-bs-placement="right" data-bs-html='true' tabindex="0" class="link-primary">
                        <i class="bi bi-info-circle"></i>
                    </a>
                </label>
                <input type="text" id="author" name="author" placeholder="Author name(s)" class="form-control">
            </div>

            <div class="col-md-6">
                <label for="conference" class="form-label">Conference Name</label>
                <input type="text" id="conference" name="conference" placeholder="Conference name or abbreviation" class="form-control">
            </div>
            <div class="col-md-6">
                <label for="topic" class="form-label">Topic</label>
                <select id="topic" name="topic" class="form-select">
                    <option value="">-- All --</option>
                    <?php foreach ($topics as $t): ?>
                        <option value="<?= $t['topic_id'] ?>"><?= htmlspecialchars($t['topic_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" id="start_date" name="start_date" class="form-control" placeholder="Start date">
            </div>
            <div class="col-md-6">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" id="end_date" name="end_date" class="form-control" placeholder="End date">
            </div>

            <div class="col-12 text-center">
                <button id="submitBtn" class="btn btn-primary px-5">Search</button>
            </div>
        </div>
    </form>

    <div id="resultContainer"></div>
</div>

<script>
    const handleForm = (() => {
        const loadPage = async (page) => {
            const form = document.getElementById('searchForm');
            const formData = new FormData(form);

            let query = new URLSearchParams(formData);

            query.append('controller', 'paper');
            query.append('action', 'ajax_search');
            query.append('page', page);

            try {
                const res = await fetch('index.php?' + query.toString());

                query.set('action', 'search');
                window.history.pushState({}, '', 'index.php?' + query.toString());

                const html = await res.text();
                document.getElementById('resultContainer').innerHTML = html;
            } catch (err) {
                console.log('Error fetching page: ', err);
            }
        }

        document.getElementById('searchForm').addEventListener('submit', function(e) {
            e.preventDefault();
            loadPage(1);
        });

        const initForm = () => {
            const params = new URLSearchParams(window.location.search);

            fields = ['keyword', 'author', 'conference', 'topic', 'start_date', 'end_date'];

            fields.forEach((field) => {
                const val = params.get(field);
                if (val) document.getElementById(field).value = val;
            })
        }

        initForm();
        document.getElementById('submitBtn').click();

        return {
            loadPage
        };
    })();
</script>

<?php include("views/partials/footer.php"); ?>