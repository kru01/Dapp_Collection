const handlePagination = (() => {
	/**
	 * Get pagination range with ellipses (JS version of PHP helper)
	 * Always includes:
	 * - First 2 pages
	 * - Last 2 pages
	 * - Current page ± radius
	 * - Ellipses when there’s a gap
	 */
	const getPaginationRange = (current, total, radius = 2, edge = 2) => {
		if (total <= 1) return [1];
		if (total <= edge * 2)
			return Array.from({ length: total }, (_, i) => i + 1);

		const pages = [];

		const minMiddle = edge + 1;
		const maxMiddle = total - edge;

		// Edge left
		for (let i = 1; i <= edge; i++) pages.push(i);

		let start = Math.max(minMiddle, current - radius);
		let end = Math.min(maxMiddle, current + radius);

		// Shift center block if near start
		if (current <= edge + radius) {
			start = edge + 1;
			end = Math.min(total - edge, start + radius * 2);
		}

		// Shift center block if near end
		if (current >= total - edge - radius + 1) {
			end = total - edge;
			start = Math.max(edge + 1, end - radius * 2);
		}

		if (start > edge + 1) pages.push("...");
		for (let i = start; i <= end; i++) {
			if (i > edge && i < total - edge + 1) pages.push(i);
		}
		if (end < total - edge) pages.push("...");

		for (let i = total - edge + 1; i <= total; i++) pages.push(i);

		return pages;
	};

	const createPaginationButton = ({
		label,
		page,
		disabled = false,
		isInput = false,
		currentPage,
		totalPages,
		onPageChange,
	}) => {
		const li = document.createElement("li");
		li.className = "page-item" + (disabled ? " disabled" : "");

		if (isInput) {
			const input = document.createElement("input");
			input.type = "number";

			input.min = 1;
			input.max = totalPages;
			input.value = currentPage;

			input.className =
				"form-control page-link text-center rounded-0 active";

			input.onchange = (e) => {
				const val = parseInt(e.target.value);

				if (!isNaN(val) && val >= 1 && val <= totalPages)
					onPageChange(val);
			};

			li.appendChild(input);
		} else if (label === "...") {
			li.classList.add("disabled");
			li.innerHTML = `<span class="page-link">…</span>`;
		} else {
			const a = document.createElement("a");
			a.href = "#";
			a.className = "page-link";
			a.textContent = label;

			a.onclick = (e) => {
				e.preventDefault();
				if (!disabled) onPageChange(page);
			};

			li.appendChild(a);
		}

		return li;
	};

	/**
	 * Render pagination bar into a container.
	 * - container: HTMLElement
	 * - currentPage, totalPages: integers
	 * - onPageChange: function(pageNumber) => void
	 */
	const renderPaginationBar = (
		container,
		currentPage,
		totalPages,
		onPageChange
	) => {
		if (totalPages <= 1) return;

		container.innerHTML = "";

		const pagination = document.createElement("ul");
		pagination.className = "pagination mt-2 flex-wrap";

		const commonProps = { currentPage, totalPages, onPageChange };

		// Prev
		pagination.appendChild(
			createPaginationButton({
				label: "« Prev",
				page: currentPage - 1,
				disabled: currentPage === 1,
				...commonProps,
			})
		);

		// Page numbers
		const range = getPaginationRange(currentPage, totalPages);
		range.forEach((p) => {
			if (p === "...") {
				pagination.appendChild(
					createPaginationButton({
						label: "...",
						page: 0,
						disabled: true,
						...commonProps,
					})
				);
			} else if (p === currentPage) {
				pagination.appendChild(
					createPaginationButton({
						label: "",
						page: p,
						isInput: true,
						...commonProps,
					})
				);
			} else {
				pagination.appendChild(
					createPaginationButton({
						label: p,
						page: p,
						...commonProps,
					})
				);
			}
		});

		// Next
		pagination.appendChild(
			createPaginationButton({
				label: "Next »",
				page: currentPage + 1,
				disabled: currentPage === totalPages,
				...commonProps,
			})
		);

		container.appendChild(pagination);
	};

	return {
		renderPaginationBar,
	};
})();
