@extends('layouts.app')

@section('content')
@php
    $employeeCollection = method_exists($employees, 'getCollection') ? $employees->getCollection() : collect($employees);
    $currentPageInstructors = $employeeCollection->where('position', 'Instructor')->count();
    $currentPageFullTime = $employeeCollection->where('employment_status', 'Full time')->count();
@endphp

<div class="page-wrap faculty-index-page">

    <div class="page-head ascend-card">
        <div>
            <div class="page-eyebrow">ASCEND • Faculty Module</div>
            <h1 class="page-title">Faculty Directory</h1>
            <p class="page-sub">Manage faculty members, employee references, positions, and future instructor assignments for scheduling.</p>
        </div>

        <div class="page-head-actions">
            <a href="{{ route('faculty.create') }}" class="btn btn-primary">+ Add Faculty</a>
        </div>
    </div>

    <div class="faculty-overview-grid">
        <div class="overview-card ascend-card">
            <div class="overview-label">Total Faculty Records</div>
            <div class="overview-value">{{ method_exists($employees, 'total') ? $employees->total() : $employeeCollection->count() }}</div>
            <div class="overview-note">All saved faculty entries</div>
        </div>

        <div class="overview-card ascend-card">
            <div class="overview-label">Instructors</div>
            <div class="overview-value">{{ $currentPageInstructors }}</div>
            <div class="overview-note">Visible on current page</div>
        </div>

        <div class="overview-card ascend-card">
            <div class="overview-label">Full Time</div>
            <div class="overview-value">{{ $currentPageFullTime }}</div>
            <div class="overview-note">Visible on current page</div>
        </div>
    </div>

    <div class="faculty-list-card ascend-card">
        <div class="faculty-list-head">
            <div>
                <h2 class="section-title">Employee Grid</h2>
                <p class="section-sub">Faculty records listed in ASCEND theme with quick actions for viewing and editing.</p>
            </div>

            <form method="GET" action="{{ route('faculty.index') }}" class="faculty-toolbar">
                <div class="toolbar-search">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search last name, first name, contact..."
                    >
                </div>

                <button type="submit" class="btn btn-outline">Search</button>

                @if(request()->filled('search'))
                    <a href="{{ route('faculty.index') }}" class="btn btn-muted">Reset</a>
                @endif
            </form>
        </div>

        <div class="employee-grid-wrap">
            <table class="table employee-grid">
                <thead>
                    <tr>
                        <th>Employee Number</th>
                        <th>Last Name</th>
                        <th>First Name</th>
                        <th>Middle Name</th>
                        <th>Position</th>
                        <th>Status</th>
                        <th>Contact</th>
                        <th class="actions">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($employees as $emp)
                        <tr>
                            <td>
                                <span class="faculty-id">{{ $emp->employee_number ?: '—' }}</span>
                            </td>
                            <td>{{ $emp->FacultyLastName }}</td>
                            <td>{{ $emp->FacultyFirstName }}</td>
                            <td>{{ $emp->FacultyMiddleName ?? '—' }}</td>
                            <td>
                                <span class="status-chip type-chip">{{ $emp->position ?: '—' }}</span>
                            </td>
                            <td>
                                <span class="status-chip status-chip-secondary">{{ $emp->employment_status ?: '—' }}</span>
                            </td>
                            <td>{{ $emp->contact_number ?: '—' }}</td>
                            <td class="actions">
                                <div class="action-group">
                                    <a href="{{ route('faculty.show', $emp->IDemployees) }}" class="btn btn-muted btn-sm">View</a>
                                    <a href="{{ route('faculty.edit', $emp->IDemployees) }}" class="btn btn-outline btn-sm">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty-state-cell">
                                <div class="empty-state-box">
                                    <div class="empty-title">No faculty members found.</div>
                                    <div class="empty-sub">Start by adding the first faculty record to ASCEND.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrap">
            {{ $employees->appends(request()->query())->links() }}
        </div>
    </div>

</div>

<style>
.faculty-index-page {
    display: flex;
    flex-direction: column;
    gap: 18px;
}

.faculty-index-page .ascend-card {
    background: #ffffff;
    border: 1px solid #e9e4f5;
    border-radius: 18px;
    box-shadow: 0 12px 30px rgba(74, 31, 122, 0.08);
}

.faculty-index-page .page-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 22px 24px;
    background: linear-gradient(135deg, #4b1f7a 0%, #6d28d9 55%, #8b5cf6 100%);
    color: #ffffff;
    border: none;
}

.faculty-index-page .page-eyebrow {
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    opacity: 0.85;
    margin-bottom: 6px;
}

.faculty-index-page .page-title {
    margin: 0;
    font-size: 28px;
    font-weight: 800;
    line-height: 1.15;
}

.faculty-index-page .page-sub {
    margin: 8px 0 0;
    font-size: 14px;
    opacity: 0.92;
}

.faculty-index-page .page-head-actions {
    display: flex;
    align-items: center;
    gap: 10px;
}

.faculty-index-page .faculty-overview-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
}

.faculty-index-page .overview-card {
    padding: 18px 20px;
}

.faculty-index-page .overview-label {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: #7c6f95;
    margin-bottom: 10px;
}

.faculty-index-page .overview-value {
    font-size: 30px;
    font-weight: 800;
    line-height: 1;
    color: #4b1f7a;
    margin-bottom: 8px;
}

.faculty-index-page .overview-note {
    font-size: 13px;
    color: #6b7280;
}

.faculty-index-page .faculty-list-card {
    overflow: hidden;
}

.faculty-index-page .faculty-list-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
    padding: 20px 22px 16px;
    border-bottom: 1px solid #eee7f8;
}

.faculty-index-page .section-title {
    margin: 0;
    font-size: 18px;
    font-weight: 800;
    color: #2f1c4d;
}

.faculty-index-page .section-sub {
    margin: 6px 0 0;
    font-size: 13px;
    color: #6b7280;
}

.faculty-index-page .faculty-toolbar {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.faculty-index-page .toolbar-search input {
    width: 300px;
    max-width: 100%;
    height: 42px;
    padding: 0 14px;
    border: 1px solid #d9cdee;
    border-radius: 12px;
    background: #ffffff;
    color: #2f1c4d;
    outline: none;
}

.faculty-index-page .toolbar-search input:focus {
    border-color: #7c3aed;
    box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.10);
}

.faculty-index-page .employee-grid-wrap {
    width: 100%;
    overflow-x: auto;
}

.faculty-index-page .employee-grid {
    width: 100%;
    border-collapse: collapse;
}

.faculty-index-page .employee-grid thead th {
    background: #f6f1fc;
    color: #5f4b80;
    font-size: 12px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    padding: 14px 16px;
    border-bottom: 1px solid #eadff8;
    white-space: nowrap;
}

.faculty-index-page .employee-grid tbody td {
    padding: 15px 16px;
    border-bottom: 1px solid #f1ebfa;
    color: #2f1c4d;
    vertical-align: middle;
}

.faculty-index-page .employee-grid tbody tr:hover {
    background: #fcfaff;
}

.faculty-index-page .faculty-id {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 54px;
    padding: 6px 10px;
    border-radius: 999px;
    background: #efe8fb;
    color: #4b1f7a;
    font-weight: 700;
}

.faculty-index-page .status-chip {
    display: inline-flex;
    align-items: center;
    padding: 6px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
    background: #eee7fb;
    color: #5b21b6;
}

.faculty-index-page .status-chip-secondary {
    background: #ede9fe;
    color: #6d28d9;
}

.faculty-index-page .actions {
    text-align: right;
    white-space: nowrap;
}

.faculty-index-page .action-group {
    display: inline-flex;
    gap: 8px;
}

.faculty-index-page .empty-state-cell {
    padding: 32px 20px !important;
}

.faculty-index-page .empty-state-box {
    text-align: center;
    padding: 24px;
    border: 1px dashed #d9cdee;
    border-radius: 16px;
    background: #fcfaff;
}

.faculty-index-page .empty-title {
    font-size: 16px;
    font-weight: 800;
    color: #4b1f7a;
}

.faculty-index-page .empty-sub {
    margin-top: 6px;
    font-size: 13px;
    color: #6b7280;
}

.faculty-index-page .pagination-wrap {
    padding: 14px 18px 18px;
}

.faculty-index-page .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    min-height: 42px;
    padding: 0 14px;
    border-radius: 12px;
    font-weight: 700;
    text-decoration: none;
    border: 1px solid transparent;
    transition: all 0.18s ease;
    cursor: pointer;
}

.faculty-index-page .btn-sm {
    min-height: 34px;
    padding: 0 12px;
    font-size: 13px;
}

.faculty-index-page .btn-primary {
    background: #ffffff;
    color: #4b1f7a;
}

.faculty-index-page .btn-primary:hover {
    background: #f3edff;
}

.faculty-index-page .btn-outline {
    background: #ffffff;
    color: #5b21b6;
    border-color: #d9cdee;
}

.faculty-index-page .btn-outline:hover {
    background: #f8f4ff;
    border-color: #c8b4ea;
}

.faculty-index-page .btn-muted {
    background: #f6f1fc;
    color: #4b1f7a;
    border-color: #eee7f8;
}

.faculty-index-page .btn-muted:hover {
    background: #efe8fb;
}

@media (max-width: 1100px) {
    .faculty-index-page .faculty-overview-grid {
        grid-template-columns: 1fr;
    }

    .faculty-index-page .faculty-list-head,
    .faculty-index-page .page-head {
        flex-direction: column;
        align-items: flex-start;
    }
}

@media (max-width: 768px) {
    .faculty-index-page .toolbar-search input {
        width: 100%;
    }

    .faculty-index-page .faculty-toolbar {
        width: 100%;
    }
}
</style>
@endsection
