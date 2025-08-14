<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuizMaster - Kết Quả</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.css">
    <style>
        body {
            background: linear-gradient(135deg, #2c1f3b, #3a2b4f);
            color: #ffffff;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            max-width: 900px;
            width: 100%;
            margin: 0 auto;
            text-align: center;
            padding: 20px;
        }

        .result-card {
            background: #3a2b4f;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            padding: 30px;
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        h1 {
            font-size: 2.2em;
            margin-bottom: 10px;
            color: #e9ecef;
        }

        p {
            color: #b0b0b0;
            margin-bottom: 20px;
        }

        .result-box {
            background: #4a355f;
            padding: 20px;
            border-radius: 10px;
            width: 100%;
            box-sizing: border-box;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #5a406f;
            font-weight: 600;
        }

        tr:nth-child(even) {
            background-color: #4a355f;
        }

        .score-tag {
            text-align: center;
            padding: 5px 10px;
            border-radius: 5px;
            display: inline-block;
            font-weight: 500;
        }

        .green { background-color: #4caf50; }
        .orange { background-color: #ff9800; }
        .red { background-color: #f44336; }

        .back-btn {
            display: inline-block;
            margin-top: 25px;
            background: linear-gradient(45deg, #555, #777);
            color: white;
            padding: 12px 25px;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .back-btn:hover {
            background: linear-gradient(45deg, #666, #888);
            transform: translateY(-2px);
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            .result-card {
                padding: 20px;
            }

            table {
                font-size: 0.9em;
            }

            .back-btn {
                padding: 10px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="result-card">
            <h1>Kết Quả Quiz <i class="fas fa-trophy"></i></h1>
            <p>Xem lại điểm số các bài quiz đã hoàn thành</p>
            <div class="result-box">
                <h2>Danh sách kết quả</h2>
                <table>
                    <tr>
                        <th>Tên Quiz</th>
                        <th>Ngày hoàn thành</th>
                        <th>Thời gian làm bài</th>
                        <th>Điểm</th>
                        <th>Tỷ lệ</th>
                    </tr>
                    @forelse ($results as $result)
                        @php
                            // Nếu result không có total_questions, lấy từ quiz hoặc tính thủ công
                            $total = $result->total_questions 
                                ?? ($result->quiz->questions_count 
                                    ?? ($result->quiz->questions ? $result->quiz->questions->count() : 0));
                            $score = $result->score;
                            $percent = $total > 0 ? round(($score / $total) * 100) : 0;
                            $color = $percent >= 80 ? 'green' : ($percent >= 50 ? 'orange' : 'red');
                        @endphp
                        <tr>
                            <td>{{ $result->quiz->name }}</td>
                            <td>{{ \Carbon\Carbon::parse($result->completed_at)->format('d/m/Y') }}</td>
                            <td>{{ $result->time_taken }} giây</td>
                            <td>{{ $result->score }} / {{ $total }}</td>
                            <td><span class="score-tag {{ $color }}">{{ $percent }}%</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4">Chưa có kết quả nào.</td></tr>
                    @endforelse
                </table>
                <div style="text-align: center;">
                    <a href="{{ route('home') }}" class="back-btn">⬅ Quay về trang chủ</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>