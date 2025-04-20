<?php
// This template is used for AJAX responses
// It contains only the parts of the page that need to be updated dynamically
?>

<!-- Table Body Content -->
<tbody id="usersTableBody">
    <?php if (empty($users)): ?>
        <tr>
            <td colspan="7" class="text-center py-4">No users found</td>
        </tr>
    <?php else: ?>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td>
                    <span class="badge <?= ($user['role_name'] === 'admin') ? 'bg-danger' : 'bg-primary' ?>">
                        <?= htmlspecialchars($user['role_name']) ?>
                    </span>
                </td>
                <td>
                    <span class="badge <?= ($user['is_active']) ? 'bg-success' : 'bg-secondary' ?>">
                        <?= ($user['is_active']) ? 'Active' : 'Inactive' ?>
                    </span>
                </td>
                <td><?= $user['last_login'] ? date('M d, Y H:i', strtotime($user['last_login'])) : 'Never' ?></td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <a href="#" class="btn btn-outline-secondary">
                            <i class="bi bi-eye"></i> View
                        </a>
                        <a href="#" class="btn btn-outline-primary">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <a href="#" class="btn btn-outline-danger">
                            <i class="bi bi-trash"></i> Delete
                        </a>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</tbody>

<!-- Pagination Info -->
<div id="paginationInfo">
    Showing <?= $pagination['from'] ?> to <?= $pagination['to'] ?> of <?= $pagination['total'] ?> entries
</div>

<!-- Total Count -->
<span id="totalCount" class="badge bg-secondary">
    <?= $pagination['total'] ?> Total Users
</span>

<!-- Pagination Links -->
<?php if ($pagination['total'] > 0): ?>
<ul class="pagination mb-0" id="paginationLinks">
    <?php
    // Build the query string for pagination links
    $queryParams = [];
    if (!empty($search)) $queryParams['search'] = $search;
    if (!empty($role)) $queryParams['role'] = $role;
    if (!empty($status)) $queryParams['status'] = $status;
    if (isset($pagination['per_page'])) $queryParams['per_page'] = $pagination['per_page'];
    
    // Function to build URL with query parameters
    function buildPaginationUrl($page, $params) {
        $params['page'] = $page;
        return '?' . http_build_query($params);
    }
    ?>
    
    <!-- First Page -->
    <li class="page-item <?= ($pagination['current_page'] == 1) ? 'disabled' : '' ?>">
        <a class="page-link pagination-link" href="<?= buildPaginationUrl(1, $queryParams) ?>" data-page="1">First</a>
    </li>
    
    <!-- Previous Page -->
    <li class="page-item <?= ($pagination['current_page'] == 1) ? 'disabled' : '' ?>">
        <a class="page-link pagination-link" href="<?= buildPaginationUrl($pagination['current_page'] - 1, $queryParams) ?>" data-page="<?= $pagination['current_page'] - 1 ?>">Previous</a>
    </li>
    
    <!-- Current Page Indicator -->
    <?php
    $startPage = max(1, $pagination['current_page'] - 2);
    $endPage = min($pagination['last_page'], $pagination['current_page'] + 2);
    
    for ($i = $startPage; $i <= $endPage; $i++):
    ?>
        <li class="page-item <?= ($i == $pagination['current_page']) ? 'active' : '' ?>">
            <a class="page-link pagination-link" href="<?= buildPaginationUrl($i, $queryParams) ?>" data-page="<?= $i ?>"><?= $i ?></a>
        </li>
    <?php endfor; ?>
    
    <!-- Next Page -->
    <li class="page-item <?= ($pagination['current_page'] == $pagination['last_page']) ? 'disabled' : '' ?>">
        <a class="page-link pagination-link" href="<?= buildPaginationUrl($pagination['current_page'] + 1, $queryParams) ?>" data-page="<?= $pagination['current_page'] + 1 ?>">Next</a>
    </li>
    
    <!-- Last Page -->
    <li class="page-item <?= ($pagination['current_page'] == $pagination['last_page']) ? 'disabled' : '' ?>">
        <a class="page-link pagination-link" href="<?= buildPaginationUrl($pagination['last_page'], $queryParams) ?>" data-page="<?= $pagination['last_page'] ?>">Last</a>
    </li>
</ul>
<?php endif; ?>