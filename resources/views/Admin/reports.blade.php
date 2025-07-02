@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Reports</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Reporter</th>
                <th>Reported User</th>
                <th>Reason</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $report)
            <tr>
                <td>{{ $report->id }}</td>



                <td>{{ $report->reporter ? $report->reporter->name : 'Unknown' }}</td>


                <td>{{ $report->reported ? $report->reported->name : 'Unknown' }}</td>

                <td>{{ $report->reason }}</td>
                <td>
                    <form action="{{ route('admin.reports.destroy', $report->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Report</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if ($reports instanceof \Illuminate\Pagination\LengthAwarePaginator)
    {{ $reports->links() }}  <!-- ✅ Only call links() when it's paginated -->
@endif
</div>
@endsection
