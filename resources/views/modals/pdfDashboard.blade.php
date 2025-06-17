<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; line-height: 1.5; }
        h2, h3 { text-align: center; margin-bottom: 20px; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            font-size: 14px;
        }
        th, td {
            border: 1px solid #333;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f0f0f0;
        }
        .section { margin-top: 30px; }
    </style>
</head>
<body>
    <h2 style="text-align: center;">BÁO CÁO TÀI CHÍNH</h2>
<p style="text-align: center;">Tháng {{ now()->format('m') }} năm {{ now()->format('Y') }}</p>


    <table>
        <thead>
            <tr>
                <th>Hạng mục</th>
                <th>Giá trị</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Dãy trọ</td>
                <td>{{ $daytroCount }}</td>
            </tr>
            <tr>
                <td>Phòng trọ</td>
                <td>{{ $phongtroCount }}</td>
            </tr>
            <tr>
                <td>Khách thuê</td>
                <td>{{ $khachthueCount }}</td>
            </tr>
            <tr>
                <td>Hợp đồng</td>
                <td>{{ $hopdongCount }}</td>
            </tr>
            <tr>
                <td>Hóa đơn</td>
                <td>{{ $hoadonCount }}</td>
            </tr>
            <tr>
                <td><strong>Tổng thu nhập</strong></td>
                <td><strong>{{ number_format($hoadonSum, 0, ',', '.') }} VNĐ</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="section">
        <h3>TÌNH TRẠNG HÓA ĐƠN</h3>
        <table>
            <thead>
                <tr>
                    <th>Trạng thái</th>
                    <th>Số lượng</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Đã thanh toán</td>
                    <td>{{ $paidCount }} hóa đơn</td>
                </tr>
                <tr>
                    <td>Chưa thanh toán</td>
                    <td>{{ $unpaidCount }} hóa đơn</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div class="section">
        <h3>Thu nhập theo từng tháng trong năm {{ date('Y') }}</h3>
        <table style="width: 100%; border-collapse: collapse;" border="1" cellpadding="8">
            <thead>
                <tr>
                    <th>Tháng</th>
                    <th>Thu nhập (VNĐ)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($months as $index => $month)
                    <tr>
                        <td style="text-align: center;">{{ $month }}</td>
                        <td style="text-align: right;">{{ number_format($incomeDataByMonth[$index], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="margin-top: 50px; text-align: right;">
        <p>Ngày {{ now()->format('d') }} tháng {{ now()->format('m') }} năm {{ now()->format('Y') }}</p>
        <p><strong>Người lập báo cáo</strong></p>
    </div>
        
</body>
</html>
