<!DOCTYPE html>
<html>
<head>
    <title>Multiple Complaint Anecdotal Record</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            margin: 0;
            padding: 0;
            line-height: 1.4;
            color: #000;
        }
        .container {
            max-width: 8.5in;
            margin: 0 auto;
            padding: 0.5in;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin: 5px 0;
            text-transform: uppercase;
        }
        .header p {
            font-size: 12px;
            margin: 2px 0;
        }
        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 25px 0;
            text-decoration: underline;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 8px;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
        }
        .incident-content {
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 12px;
            line-height: 1.6;
        }
        .complaint-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 12px;
        }
        .complaint-table th, .complaint-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .complaint-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .signature-section {
            margin-top: 60px;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 40px;
            padding-top: 5px;
            font-size: 12px;
        }
        .signature-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 40px;
            margin-top: 20px;
        }
        .signature-item {
            text-align: center;
        }
        .date-section {
            margin-top: 30px;
            font-size: 12px;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Republic of the Philippines</h1>
            <h1>Department of Education</h1>
            <p>RECORD'S DIVISION OF MIRANDA ORIGINAL</p>
            <h1>TABORQAM SENIOR HIGH SCHOOL</h1>
            <h1>COMPLAINT ANECDOTAL RECORD</h1>
            <p>Prefect of Discipline</p>
            <div class="date-section">
                Date: {{ $currentDate }}
            </div>
        </div>

        <div class="section">
            <div class="section-title">COMPLAINT SUMMARY</div>
            <div class="incident-content">
                <table class="complaint-table">
                    <thead>
                        <tr>
                            <th>Complainant</th>
                            <th>Respondent</th>
                            <th>Incident</th>
                            <th>Offense Type</th>
                            <th>Sanction</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($complaints as $complaint)
                        <tr>
                            <td>{{ $complaint->complainant->student_fname ?? 'N/A' }} {{ $complaint->complainant->student_lname ?? 'N/A' }}</td>
                            <td>{{ $complaint->respondent->student_fname ?? 'N/A' }} {{ $complaint->respondent->student_lname ?? 'N/A' }}</td>
                            <td>{{ $complaint->complaints_incident ?? 'N/A' }}</td>
                            <td>{{ $complaint->offense->offense_type ?? 'N/A' }}</td>
                            <td>{{ $complaint->sanction->sanction_consequences ?? 'N/A' }}</td>
                            <td>{{ $complaint->complaints_date ? \Carbon\Carbon::parse($complaint->complaints_date)->format('M d, Y') : 'N/A' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <p><strong>Total Complaints: {{ $complaints->count() }}</strong></p>
            </div>
        </div>

        <div class="section">
            <div class="section-title">SOLUTION IMPLEMENTED</div>
            <div class="incident-content">
                {{ $solution }}
            </div>
        </div>

        <div class="section">
            <div class="section-title">RECOMMENDATION</div>
            <div class="incident-content">
                {{ $recommendation }}
            </div>
        </div>

        <div class="signature-section">
            <div class="signature-grid">
                @foreach($complaints as $complaint)
                <div class="signature-item">
                    <div class="signature-line"></div>
                    <div>Complainant's Signature</div>
                    <div style="margin-top: 5px; font-weight: bold;">
                        {{ $complaint->complainant->student_fname ?? 'N/A' }} {{ $complaint->complainant->student_lname ?? 'N/A' }}
                    </div>
                </div>
                <div class="signature-item">
                    <div class="signature-line"></div>
                    <div>Respondent's Signature</div>
                    <div style="margin-top: 5px; font-weight: bold;">
                        {{ $complaint->respondent->student_fname ?? 'N/A' }} {{ $complaint->respondent->student_lname ?? 'N/A' }}
                    </div>
                </div>
                @endforeach

                <div class="signature-item">
                    <div class="signature-line"></div>
                    <div>Parent/Guardian's Signature</div>
                    <div style="margin-top: 5px; font-weight: bold;">Parent/Guardian</div>
                </div>

                <div class="signature-item">
                    <div class="signature-line"></div>
                    <div>Prefect of Discipline In-charge</div>
                    <div style="margin-top: 5px; font-weight: bold;">Prefect of Discipline</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
