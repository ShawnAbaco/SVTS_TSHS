<!DOCTYPE html>
    <html>
<head>
    <title>Multiple Violation Anecdotal Record</title>
    <style>
        /* Print-specific styles */
        @media print {
            body {
                margin: 0;
                padding: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .container {
                padding: 0.5in;
                margin: 0;
                max-width: 100%;
            }
            .page-break {
                page-break-before: always;
            }
            .signature-line-custom {
                border-top: 1px solid #000 !important;
            }
        }

        @page {
            margin: 0.5in;
            size: letter;
        }

        body {
            font-family: 'Times New Roman', serif;
            margin: 0;
            padding: 0;
            line-height: 1.5;
            color: #000;
            background: white;
        }
        .container {
            max-width: 8.5in;
            margin: 0 auto;
            padding: 0.8in;
            background: white;
        }
        
        /* New styles for enhanced layout */
        .deped-header {
            text-align: center;
            line-height: 1.4;
            margin-bottom: 5px;
        }
        .deped-header div {
            margin-bottom: 2px;
        }
        .header-line {
            margin: 15px 0 20px 0;
            border-top: 1px solid #000;
        }
        .complaint-title {
            text-align: center;
            margin-bottom: 10px;
            font-size: 18px;
            font-weight: 500;
        }
        .incident-section, .recommendation-section, .solution-section {
            margin-bottom: 50px;
            font-size: 14px;
            text-align: justify;
        }
        .signature-container {
            margin-top: 40px;
        }
        .signature-block {
            margin-bottom: 35px;
            page-break-inside: avoid;
        }
        .signature-name {
            font-size: 15px;
            font-weight: bold;
        }
        .signature-line-custom {
            border-top: 1px solid #000;
            width: 170px;
            margin-top: 5px;
            margin-bottom: 5px;
        }
        .signature-label {
            font-size: 13px;
        }
        .signature-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- DEPED HEADER -->
        <div class="deped-header">
            <div>Republic of Philippines</div>
            <div>Department of Education</div>
            <div><b>REGION X - NORTHERN MINDANAO</b></div>
            <div><b>SCHOOLS DIVISION OF MISAMIS ORIENTAL</b></div>
            <div><b>TAGOLOAN SENIOR HIGH SCHOOL</b></div>
        </div>

        <!-- HEADER LINE -->
        <div class="header-line"></div>

        <!-- TITLE -->
        <div class="complaint-title">
            VIOLATION ANECDOTAL RECORD
        </div>

        <div style="text-align: center; font-size: 14px;">
            Prefect Of Discipline
        </div>

        <div style="text-align: center; margin-bottom: 30px; font-size: 14px;">
            Date: {{ $currentDate }}
        </div>

        <!-- INCIDENT -->
        <div class="incident-section">
            <strong>INCIDENT:</strong> 
            @foreach($violations as $violation)
                {{ $violation->violation_incident }}
                @if(!$loop->last); @endif
            @endforeach
        </div>

        <!-- RECOMMENDATION -->
        <div class="recommendation-section">
            <strong>RECOMMENDATION:</strong> {{ $recommendation }}
        </div>

        <!-- SOLUTION -->
        <div class="solution-section">
            <strong>SOLUTION:</strong> {{ $solution }}
        </div>

        <!-- SIGNATURES -->
        <div class="signature-container">
            <!-- Student Signature -->
            @if($violations->count() > 0)
            @php $firstViolation = $violations->first(); @endphp
            <div class="signature-block">
                <div class="signature-name">{{ $firstViolation->student->student_fname }} {{ $firstViolation->student->student_lname }}</div>
                <div class="signature-line-custom"></div>
                <div class="signature-label">Student's name and signature</div>
            </div>
            @endif

            <!-- Parent/Guardian Signature -->
            @if($violations->count() > 0)
            <div class="signature-block">
                <div class="signature-name">{{ $firstViolation->student->parent->parent_fname }} {{ $firstViolation->student->parent->parent_lname }}</div>
                <div class="signature-line-custom"></div>
                <div class="signature-label">Parent's name and signature</div>
            </div>
            @endif

            <!-- Adviser Signature -->
            @if($violations->count() > 0)
            <div class="asignature-block">
                <div class="signature-name">{{ $firstViolation->student->adviser->adviser_fname }} {{ $firstViolation->student->adviser->adviser_lname }}</div>
                <div class="signature-line-custom"></div>
                <div class="signature-label">Adviser's name and signature</div>
            </div>
            @endif

            <div style="width: 100%; display: flex; justify-content: flex-end;">
                  <div style="margin-bottom: 35px; text-align: right;">
                      <div style="font-size: 15px;">
                              {{ Auth::user()->prefect_fname }} {{ Auth::user()->prefect_lname }}
                      </div>
                      <div style="border-top: 1px solid #000; width: 170px; margin-left: auto; margin-top: 5px;"></div>
                      <div style="font-size: 13px; margin-top: 5px;">
                          Prefect of Discipline Incharge
            </div>
    </div>
</body>
</html>