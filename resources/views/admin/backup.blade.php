@extends('layouts.admin')

@section('content')
    <h1>Backup and export</h1>
    <div class="m">Download your data as spreadsheets. The files open in Excel, Numbers and Google Sheets. Passwords and bank account numbers are never included.</div>

    <div class="card" style="margin-top: 20px;">
        <h3>Download everything</h3>
        <p class="m" style="margin-bottom: 12px;">One ZIP file with a spreadsheet for each list below. Good for a regular backup, for example once a week.</p>
        <a href="{{ route('admin.backup.all') }}" class="btn" style="text-decoration:none;">Download full backup (ZIP)</a>
    </div>

    <div class="card" style="margin-top: 20px;">
        <h3>Download one list</h3>
        <div style="overflow-x:auto;">
        <table class="tb">
            <tr><th>List</th><th>What it holds</th><th>Rows</th><th></th></tr>
            @foreach($sets as $set)
                <tr>
                    <td><b>{{ $set['label'] }}</b></td>
                    <td class="m sm">{{ $set['about'] }}</td>
                    <td>{{ number_format($set['count']) }}</td>
                    <td><a href="{{ route('admin.backup.download', $set['key']) }}" class="btn g" style="text-decoration:none; margin:0; padding:5px 14px; font-size:13px;">Download</a></td>
                </tr>
            @endforeach
        </table>
        </div>
    </div>

    <div class="card" style="margin-top: 20px;">
        <h3>Good to know</h3>
        <ul>
            <li>Uploaded files (ID documents, photos, vehicle pictures) are not in the spreadsheets. They stay in the storage bucket.</li>
            <li>This is a copy for your records. It does not replace a database backup from Laravel Cloud, which can restore the whole system.</li>
            <li>The files contain personal information about your customers. Keep them private and delete old copies you no longer need.</li>
        </ul>
    </div>
@endsection
