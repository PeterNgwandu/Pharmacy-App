<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
         {{ $sale->invoice_number }}
    </title>

    <style>

        :root {
            --accent: #0f766e;
            --accent-dark: #0b5c56;
            --ink: #1f2937;
            --muted: #6b7280;
            --line: #e5e7eb;
            --soft: #f8fafc;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Segoe UI", Arial, Helvetica, sans-serif;
            background: #eef1f5;
            padding: 30px 20px;
            color: #1f2937;
            -webkit-font-smoothing: antialiased;
            font-size: 14px;
            line-height: 1.5;
        }

        /* ---------- Print button ---------- */

        /* ---------- Action buttons (Print + Download) ---------- */

        .print-button {
            max-width: 210mm;
            margin: 0 auto 22px;
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        /* Neutralize Bootstrap's row/col so they don't fight the layout */
        .print-button .row {
            display: contents;
        }

        .print-button .col,
        .print-button .col-md-3 {
            width: auto;
            max-width: none;
            flex: 0 0 auto;
            padding: 0;
            margin: 0 !important;
        }

        /* Style BOTH the <button> and the <a class="btn"> the same way */
        .print-button button,
        .print-button .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 26px;
            border: none;
            background: #0f766e;
            color: #fff;
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.03em;
            line-height: 1;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(15, 118, 110, 0.28);
            transition: background 0.18s ease, transform 0.18s ease, box-shadow 0.18s ease;
        }

        .print-button button:hover,
        .print-button .btn:hover {
            background: #0b5c56;
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(15, 118, 110, 0.34);
        }

        .print-button button:active,
        .print-button .btn:active {
            transform: translateY(0);
        }

        /* Give the Download button a slightly different accent so they're distinguishable */
        .print-button .btn-success {
            background: #1f2937;
            box-shadow: 0 4px 12px rgba(31, 41, 55, 0.28);
        }

        .print-button .btn-success:hover {
            background: #111827;
            box-shadow: 0 6px 16px rgba(31, 41, 55, 0.34);
        }

        /* Icon spacing */
        .print-button .bi {
            font-size: 15px;
        }

        /* ---------- Sheet ---------- */

        .invoice {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #fff;
            padding: 20mm 18mm;
            border-radius: 2px;
            box-shadow: 0 10px 40px rgba(15, 23, 42, 0.12);
        }

        /* ---------- Header ---------- */

        .header {
            position: relative;
            text-align: center;
            padding-bottom: 22px;
            margin-bottom: 30px;
            border-bottom: 3px solid #0f766e;
        }

        .header::after {
            content: "";
            position: absolute;
            left: 50%;
            bottom: -3px;
            transform: translateX(-50%);
            width: 90px;
            height: 3px;
            background: #0b5c56;
        }

        .header h1 {
            margin: 0 0 8px;
            font-size: 34px;
            font-weight: 800;
            letter-spacing: 0.28em;
            text-indent: 0.28em;
            text-transform: uppercase;
            color: #0f766e;
        }

        .header p {
            margin: 0;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: #6b7280;
        }

        /* ---------- Invoice info ---------- */

        .invoice-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 30px;
        }

        .invoice-info > div {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-left: 4px solid #0f766e;
            border-radius: 2px;
            padding: 14px 16px;
        }

        .invoice-info p {
            margin: 0 0 9px;
            font-size: 13.5px;
            color: #1f2937;
        }

        .invoice-info p:last-child {
            margin-bottom: 0;
        }

        .invoice-info strong {
            display: inline-block;
            min-width: 92px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.07em;
            text-transform: uppercase;
            color: #6b7280;
            vertical-align: middle;
        }

        /* ---------- Items table ---------- */

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 4px;
            font-size: 13.5px;
        }

        thead th {
            background: #0f766e;
            color: #fff;
            padding: 12px 12px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.09em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        thead th:first-child {
            border-radius: 2px 0 0 2px;
        }

        thead th:last-child {
            border-radius: 0 2px 2px 0;
        }

        tbody td {
            padding: 13px 12px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
            color: #1f2937;
        }

        tbody tr:nth-child(even) td {
            background: #fafbfc;
        }

        tbody tr:last-child td {
            border-bottom: 1px solid #d1d5db;
        }

        /* column sizing / alignment */

        th:nth-child(1),
        td:nth-child(1) {
            width: 46px;
            text-align: center;
            color: #9ca3af;
            font-weight: 600;
        }

        th:nth-child(2),
        td:nth-child(2) {
            font-weight: 600;
        }

        th:nth-child(3),
        td:nth-child(3) {
            width: 100px;
            text-align: center;
            white-space: nowrap;
        }

        th:nth-child(4),
        td:nth-child(4) {
            width: 140px;
            text-align: right;
            white-space: nowrap;
        }

        th:nth-child(5),
        td:nth-child(5) {
            width: 150px;
            text-align: right;
            white-space: nowrap;
            font-weight: 600;
        }

        .text-end {
            text-align: right;
        }

        /* ---------- Totals ---------- */

        .total-section {
            width: 330px;
            margin-left: auto;
            margin-top: 26px;
            padding-top: 12px;
            border-top: 2px solid #1f2937;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 16px;
            padding: 8px 0;
            font-size: 13.5px;
        }

        .total-section .total-row {
            font-size: 17px;
            font-weight: 700;
            color: #1f2937;
        }

        .total-section .total-row span {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.09em;
            text-transform: uppercase;
            color: #6b7280;
        }

        /* ---------- Payment ---------- */

        .payment-section {
            width: 330px;
            margin-left: auto;
            margin-top: 14px;
            padding: 6px 18px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 2px;
        }

        .payment-section .total-row {
            font-size: 13.5px;
            color: #4b5563;
        }

        .payment-section .total-row strong {
            color: #1f2937;
            font-weight: 700;
        }

        .payment-section .total-row:last-child {
            border-top: 1px dashed #d1d5db;
            margin-top: 2px;
            padding-top: 12px;
        }

        .payment-section .total-row:last-child strong {
            color: #0f766e;
        }

        /* ---------- Footer ---------- */

        .footer {
            margin-top: 55px;
            padding-top: 22px;
            border-top: 1px dashed #d1d5db;
            text-align: center;
            color: #6b7280;
            font-size: 12.5px;
        }

        .footer p {
            margin: 4px 0;
        }

        /* =====================================================
        PRINT STYLES
        ===================================================== */

        @media print {

            @page {
                size: A4;
                margin: 0;
            }

            html,
            body {
                background: #fff;
                padding: 0;
                margin: 0;
            }

            /* Force background colors and borders to print */
            * {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .invoice {
                width: 100%;
                min-height: auto;
                margin: 0;
                padding: 15mm;
                border-radius: 0;
                box-shadow: none;
            }

            .print-button {
                display: none;
            }

            /* Keep rows and blocks from splitting across pages */
            tr {
                page-break-inside: avoid;
            }

            .invoice-info,
            .total-section,
            .payment-section,
            .footer {
                page-break-inside: avoid;
            }

            /* Slightly tighten spacing so it always fits one page */
            .invoice-info {
                margin-bottom: 22px;
            }

            .header {
                margin-bottom: 24px;
            }

            .footer {
                margin-top: 40px;
            }

        }

    </style>
</head>

<body>

    <div class="print-button">
        <div class="row">
            <div class="col col-md-3 mb-3">
                <button onclick="window.print()">
                    Print Invoice
                </button>
            </div>

            <div class="mt-3 col col-md-3">
               <a class="btn btn-success" href="{{ route('sales.downloadPdf', $sale->id) }}">
                    <i class="bi bi-file-earmark-pdf"></i>
                     Download PDF
               </a>
            </div>
        </div>
        
    </div>

    <div class="invoice">

        {{-- Header --}}
        <div class="header">

            <h1>INVOICE</h1>

            <p>PharmaGo Sales Invoice</p>

        </div>


        {{-- Invoice Information --}}
        <div class="invoice-info">

            <div>

                <p>
                    <strong>Invoice No:</strong>
                    {{ $sale->invoice_number }}
                </p>

                <p>
                    <strong>Date:</strong>
                    {{ $sale->created_at->format('Y-m-d H:i') }}
                </p>

            </div>


            <div>

                <p>
                    <strong>Sold By:</strong>
                    {{ $sale->creator->name }}
                </p>

                <p>
                    <strong>Payment:</strong>
                    {{ $sale->payment->payment_method }}
                </p>

            </div>

        </div>


        {{-- Items --}}
        <table>

            <thead>

                <tr>

                    <th>#</th>

                    <th>Medicine</th>

                    <th>Qty</th>

                    <th>Unit Price</th>

                    <th class="text-end">
                        Subtotal
                    </th>

                </tr>

            </thead>

            <tbody>

                @foreach ($sale->items as $index => $item)

                    <tr>

                        <td>
                            {{ $index + 1 }}
                        </td>

                        <td>
                            {{ $item->medicine->name }}
                        </td>

                        <td>
                            {{ $item->quantity }}
                            {{ $item->medicine->pack_unit }}
                        </td>

                        <td>
                            TZS {{ number_format($item->unit_price, 2) }}
                        </td>

                        <td class="text-end">
                            TZS {{ number_format($item->subtotal, 2) }}
                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>


        {{-- Totals --}}
        <div class="total-section">

            <div class="total-row">

                <span>
                    Total
                </span>

                <strong>
                    TZS {{ number_format($sale->total_amount, 2) }}
                </strong>

            </div>

        </div>


        {{-- Payment --}}
        <div class="payment-section">

            <div class="total-row">

                <span>
                    Amount Paid
                </span>

                <strong>
                    TZS {{ number_format($sale->payment->amount, 2) }}
                </strong>

            </div>

            <div class="total-row">

                <span>
                    Change
                </span>

                <strong>
                    TZS
                    {{ number_format(
                        $sale->payment->amount - $sale->total_amount,
                        2
                    ) }}
                </strong>

            </div>

        </div>


        {{-- Footer --}}
        <div class="footer">

            <p>
                Thank you for your purchase.
            </p>

            <p>
                Please keep this invoice for your records.
            </p>

        </div>

    </div>

</body>

</html>