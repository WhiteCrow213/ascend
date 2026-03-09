@extends('layouts.app')

@section('content')
<div class="page-wrap">

    <div class="page-head">
        <div>
            <h1 class="page-title">Faculty</h1>
            <p class="page-sub">Manage faculty members and instructors</p>
        </div>

        <div>
            <a href="{{ route('faculty.create') }}" class="btn btn-primary">+ Add Faculty</a>
        </div>
    </div>

    <div class="table-card">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Employment Type</th>
                    <th>Status</th>
                    <th>Contact</th>
                    <th class="actions">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($employees as $emp)
                <tr>
                    <td>{{ $emp->IDemployees }}</td>
                    <td>{{ $emp->FacultyLastName }}</td>
                    <td>
                        {{ $emp->FacultyFirstName }}
                        {{ $emp->FacultyMiddleName ?? '' }}
                    </td>
                    <td>{{ $emp->employment_type }}</td>
                    <td>{{ $emp->employment_status }}</td>
                    <td>{{ $emp->contact_number }}</td>

                    <td class="actions">
                        <a href="{{ route('faculty.show', $emp->IDemployees) }}" class="btn btn-muted">View</a>
                        <a href="{{ route('faculty.edit', $emp->IDemployees) }}" class="btn btn-outline">Edit</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="empty">No faculty members found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div style="padding:12px 14px;border-top:1px solid #eef2f7;">
            {{ $employees->links() }}
        </div>

    </div>

</div>
@endsection
