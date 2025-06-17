@extends('layouts.index')

@section('heading')
<div class="d-flex align-items-center" style="gap: 12px;">
    <span style="font-size: 1.5rem; font-weight: bold;">Gợi ý sửa chữa:</span>
    <button id="robotBtn" class="btn btn-light p-0" type="button" style="opacity:0.5; border:none; background:none;" disabled>
        <i class="fas fa-robot" style="font-size:2.2rem; color:#007bff;"></i>
    </button>
</div>
@endsection

@section('content')
<div class="container mt-1 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-body text-center">

                    {{-- Dropdown chọn dãy trọ --}}
                    <div class="form-group mb-3">
                        <label for="daytroSelect"><strong>Chọn dãy trọ:</strong></label>
                        <select class="form-control" id="daytroSelect">
                            <option value="">-- Chọn dãy trọ --</option>
                            @foreach($daytros as $daytro)
                                <option value="{{ $daytro->id }}"
                                        data-tinh="{{ $daytro->tinh }}"
                                        data-huyen="{{ $daytro->huyen }}"
                                        data-xa="{{ $daytro->xa }}">
                                    {{ $daytro->tendaytro }} - {{ $daytro->tinh }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Khu vực hiển thị các phòng trọ bị lỗi --}}
                    <div id="phongTroLoiContainer" class="mt-4 text-left">
                        <h5 class="mb-3">Các phòng trọ có sự cố trong dãy:</h5>
                        <div id="phongTroLoiList">
                            <p class="text-muted text-center">Vui lòng chọn một dãy trọ để xem các phòng bị lỗi.</p>
                        </div>
                    </div>

                    <hr class="my-4"> {{-- Đường phân cách --}}

                    {{-- Dropdown loại sự cố --}}
                    <div id="loaiSuCoWrapper" class="form-group mb-3" style="display: none;">
                        <label for="loaiSuCoSelect"><strong>Chọn loại sự cố để tìm dịch vụ:</strong></label>
                        <select class="form-control" id="loaiSuCoSelect">
                            <option value="">-- Chọn sự cố --</option>
                        </select>
                    </div>

                    {{-- Nút Gợi ý dịch vụ --}}
                    <button id="goiYButton" class="btn btn-primary mt-2 mb-3"
                            style="opacity:0.5; pointer-events:none; min-width:180px;">
                        Gợi ý dịch vụ sửa chữa
                    </button>

                    {{-- Kết quả gợi ý dịch vụ --}}
                    <div id="ketquaGoiY" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="geminiModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document"> {{-- Thêm modal-lg để rộng hơn --}}
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-robot"></i> Phân tích tổng thể sự cố từ AI</h5>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body" id="geminiContent">
                <div class="text-center text-muted">Nhấn "Phân tích" để nhận hướng dẫn chi tiết.</div>
            </div>
            <div class="modal-footer">
                <button id="geminiAnalyzeBtn" class="btn btn-primary">Phân tích</button>
                <button class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script>
    // --- khái báo các biến để chuận bị xử lí  ---
    const suCoData = @json($sucophongtros); // Dữ liệu sự cố từ Controller
    const daytroSelect = document.getElementById('daytroSelect');
    const phongTroLoiList = document.getElementById('phongTroLoiList');
    const loaiSuCoSelect = document.getElementById('loaiSuCoSelect');
    const loaiSuCoWrapper = document.getElementById('loaiSuCoWrapper');
    const goiYButton = document.getElementById('goiYButton');
    const ketquaGoiY = document.getElementById('ketquaGoiY');
    const robotBtn = document.getElementById('robotBtn');
    const geminiContent = document.getElementById('geminiContent');
    const geminiAnalyzeBtn = document.getElementById('geminiAnalyzeBtn');

    // --- HIện thị khi user chọn sự cố phòng ---
    function displaySuCoPhongTro(selectedDayTroId) {
        phongTroLoiList.innerHTML = ''; // Xóa nội dung cũ

        if (!selectedDayTroId) {
            phongTroLoiList.innerHTML = '<p class="text-muted text-center">Vui lòng chọn một dãy trọ để xem các phòng bị lỗi.</p>';
            return;
        }

        const filteredSuCo = suCoData.filter(suCo => {
            return suCo.phongtro && suCo.phongtro.daytro && suCo.phongtro.daytro.id == selectedDayTroId;
        });

        if (filteredSuCo.length > 0) {
            // Nhóm các sự cố theo phòng để hiển thị gọn gàng hơn
            const suCoByPhong = {};
            filteredSuCo.forEach(suCo => {
                const phongId = suCo.phongtro.id;
                if (!suCoByPhong[phongId]) {
                    suCoByPhong[phongId] = {
                        tenPhong: suCo.phongtro.tenphong,
                        soPhong: suCo.phongtro.sophong,
                        suCoList: []
                    };
                }
                suCoByPhong[phongId].suCoList.push(suCo);
            });

            let htmlContent = '<div class="accordion" id="suCoAccordion">';
            Object.values(suCoByPhong).sort((a, b) => a.soPhong - b.soPhong).forEach((phongData, index) => {
                const collapseId = `collapsePhong${phongData.soPhong}`;
                const headingId = `headingPhong${phongData.soPhong}`;
                htmlContent += `
                    <div class="card mb-2">
                        <div class="card-header" id="${headingId}">
                            <h2 class="mb-0">
                                <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#${collapseId}" aria-expanded="false" aria-controls="${collapseId}">
                                    <i class="fas fa-exclamation-triangle text-danger mr-2"></i> Phòng ${phongData.tenPhong || phongData.soPhong} (${phongData.suCoList.length} sự cố)
                                </button>
                            </h2>
                        </div>
                        <div id="${collapseId}" class="collapse" aria-labelledby="${headingId}" data-parent="#suCoAccordion">
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                `;
                phongData.suCoList.forEach(suCo => {
                    htmlContent += `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${suCo.loai_su_co ? suCo.loai_su_co.charAt(0).toUpperCase() + suCo.loai_su_co.slice(1) : 'Sự cố khác'}:</strong> ${suCo.mo_ta}
                                <br><small class="text-muted">Báo cáo: ${new Date(suCo.created_at).toLocaleDateString('vi-VN')}</small>
                            </div>
                        </li>
                    `;
                });
                htmlContent += `
                                </ul>
                            </div>
                        </div>
                    </div>
                `;
            });
            htmlContent += '</div>';
            phongTroLoiList.innerHTML = htmlContent;
        } else {
            phongTroLoiList.innerHTML = '<p class="text-muted text-center">Không có phòng trọ nào có sự cố trong dãy trọ này.</p>';
        }
    }

    // --- Hàm lấy sự cố cho AI phân tích ---
    function tongHopSuCoForAI(daytroId) {
        const filtered = suCoData.filter(item =>
            item.phongtro && item.phongtro.daytro && item.phongtro.daytro.id == daytroId
        );
        if (filtered.length === 0) return '';
        const phongMap = {};
        filtered.forEach(item => {
            const sophong = item.phongtro ? item.phongtro.sophong : '???';
            if (!phongMap[sophong]) phongMap[sophong] = [];
            let suCoDesc = '';
            if (item.loai_su_co && item.mota_sucophongtro) {
                suCoDesc = `${item.loai_su_co.charAt(0).toUpperCase() + item.loai_su_co.slice(1)}: ${item.mota_sucophongtro}`;
            } else if (item.mota_sucophongtro) {
                suCoDesc = item.mota_sucophongtro;
            } else if (item.loai_su_co) {
                 suCoDesc = item.loai_su_co.charAt(0).toUpperCase() + item.loai_su_co.slice(1);
            } else {
                suCoDesc = 'Sự cố không rõ mô tả';
            }
            phongMap[sophong].push(suCoDesc);
        });
        let ketQua = '';
        Object.keys(phongMap).sort((a, b) => parseInt(a) - parseInt(b)).forEach(phong => { // Sắp xếp phòng theo số
            ketQua += `Phòng ${phong}: ${phongMap[phong].join(' | ')}\n`;
        });
        return ketQua;
    }

    // --- Hàm kêu AI phản hồi cho user---
    function formatGeminiResponse(text) {
        const lines = text.split('\n');
        let html = '';
        let inList = false;

        const sanitizeAndBold = str => {
            return str
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.*?)\*/g, '<em>$1</em>');
        };

        lines.forEach(line => {
            line = line.trim();
            if (line === '') {
                if (inList) {
                    html += '</ul>';
                    inList = false;
                }
                return;
            }

            // Headers
            if (line.startsWith('📊')) {
                if (inList) html += '</ul>';
                html += '<h4 class="mt-3 text-primary"><i class="fas fa-chart-bar"></i> ' + sanitizeAndBold(line.substring(2).trim()) + '</h4><ul class="list-unstyled">';
                inList = true;
            } else if (line.startsWith('🔍')) {
                if (inList) html += '</ul>';
                html += '<h4 class="mt-3 text-info"><i class="fas fa-search"></i> ' + sanitizeAndBold(line.substring(2).trim()) + '</h4><ul class="list-unstyled">';
                inList = true;
            } else if (line.startsWith('💡')) {
                if (inList) html += '</ul>';
                html += '<h4 class="mt-3 text-warning"><i class="fas fa-lightbulb"></i> ' + sanitizeAndBold(line.substring(2).trim()) + '</h4><ul class="list-unstyled">';
                inList = true;
            } else if (line.startsWith('📋')) {
                if (inList) html += '</ul>';
                html += '<h4 class="mt-3 text-success"><i class="fas fa-clipboard-list"></i> ' + sanitizeAndBold(line.substring(2).trim()) + '</h4><ul class="list-unstyled">';
                inList = true;
            }
            // List items
            else if (line.startsWith('• ') || line.startsWith('* ') || line.startsWith('- ')) {
                if (!inList) {
                    html += '<ul class="list-unstyled">';
                    inList = true;
                }
                html += `<li><i class="fas fa-angle-right mr-2 text-muted"></i>${sanitizeAndBold(line.substring(1).trim())}</li>`;
            }
            // Paragraphs (if not part of a list)
            else {
                if (inList) {
                    html += '</ul>';
                    inList = false;
                }
                html += `<p>${sanitizeAndBold(line)}</p>`;
            }
        });

        if (inList) html += '</ul>'; // Close last ul if still open

        // Special handling for "không rõ ràng" message
        if (text.includes("viết lại cho rõ ràng nếu muốn AI hướng dẫn")) {
             return '<div class="alert alert-warning text-center mt-3"><i class="fas fa-exclamation-circle mr-2"></i>Mô tả sự cố chưa rõ ràng. Vui lòng cung cấp thông tin chi tiết hơn để AI có thể phân tích chính xác.</div>';
        }

        return html;
    }

    // --- Xử lí khi chọn dãy trọ ---
    daytroSelect.addEventListener('change', function () {
        const selectedDaytroId = this.value;

        // Cập nhật trạng thái nút AI Robot
        if (selectedDaytroId) {
            robotBtn.style.opacity = '1';
            robotBtn.disabled = false;
        } else {
            robotBtn.style.opacity = '0.5';
            robotBtn.disabled = true;
        }

        // Hiển thị các phòng trọ bị lỗi trong dãy đã chọn
        displaySuCoPhongTro(selectedDaytroId);

        // Cập nhật dropdown "Chọn loại sự cố" cho việc gợi ý dịch vụ
        loaiSuCoSelect.innerHTML = '<option value="">-- Chọn sự cố --</option>'; // Reset options
        ketquaGoiY.innerHTML = ''; // Xóa kết quả gợi ý dịch vụ cũ
        goiYButton.style.opacity = '0.5'; // Disable nút gợi ý
        goiYButton.style.pointerEvents = 'none';

        if (!selectedDaytroId) {
            loaiSuCoWrapper.style.display = 'none'; // Ẩn dropdown loại sự cố
            return;
        }

        const filtered = suCoData.filter(item =>
            item.phongtro && item.phongtro.daytro && item.phongtro.daytro.id === parseInt(selectedDaytroId)
        );

        // Lấy các loại sự cố duy nhất và thêm vào dropdown
        const uniqueLoaiSuCo = [...new Set(filtered.map(item => item.loai_su_co))].filter(Boolean); // Lọc bỏ giá trị null/undefined
        if (uniqueLoaiSuCo.length > 0) {
            uniqueLoaiSuCo.sort().forEach(loai => { // Sắp xếp theo alphabet
                const opt = document.createElement('option');
                opt.value = loai;
                // Hiển thị tên loại sự cố đẹp hơn
                if (loai === 'dien') opt.textContent = 'Điện';
                else if (loai === 'nuoc') opt.textContent = 'Nước';
                else if (loai === 'nha') opt.textContent = 'Xây dựng';
                else opt.textContent = loai.charAt(0).toUpperCase() + loai.slice(1); // Chữ cái đầu viết hoa
                loaiSuCoSelect.appendChild(opt);
            });
            loaiSuCoWrapper.style.display = 'block'; // Hiển thị dropdown loại sự cố
        } else {
            loaiSuCoWrapper.style.display = 'none';
        }
    });

    // --- Xử lí khi user chọn loại sự cố---
    loaiSuCoSelect.addEventListener('change', function() {
        if (this.value) {
            goiYButton.style.opacity = '1';
            goiYButton.style.pointerEvents = 'auto';
        } else {
            goiYButton.style.opacity = '0.5';
            goiYButton.style.pointerEvents = 'none';
        }
        ketquaGoiY.innerHTML = ''; // Xóa kết quả gợi ý dịch vụ cũ khi thay đổi loại sự cố
    });


    // --- Xử lí xử kiện trong AI ---
    robotBtn.addEventListener('click', function () {
        $('#geminiModal').modal('show');
        geminiContent.innerHTML = '<div class="text-center text-muted">Nhấn "Phân tích" để nhận hướng dẫn từ AI.</div>';
    });

    // Prompt để cho AI phân tích ra dc bản sự cố => Đưa vào word luôn (khoa có thể hỏi)
    geminiAnalyzeBtn.addEventListener('click', function () {
        const daytroId = daytroSelect.value;
        if (!daytroId) {
            geminiContent.innerHTML = '<div class="text-danger">Vui lòng chọn một dãy trọ trước khi phân tích AI.</div>';
            return;
        }

        const daytroText = daytroSelect.options[daytroSelect.selectedIndex].text;
        const tongKet = tongHopSuCoForAI(daytroId);

        // Sử dụng prompt để format lại cho AI 
        let prompt = `Dãy trọ "${daytroText}" có các sự cố như sau:\n${tongKet}\n\nHãy phân tích và đưa ra hướng dẫn/giải pháp cho chủ trọ theo format sau (nhớ viết trong 300 từ là hết):\n\n📊 TỔNG QUAN SỰ CỐ:\n• Liệt kê các sự cố theo từng phòng (phòng nào nhỏ xếp trước vd:001, 002, ...)\n• Đánh giá mức độ nghiêm trọng của từng sự cố (cao, trung bình, thấp)\n\n🔍 PHÂN TÍCH NGUYÊN NHÂN:\n• Nguyên nhân có thể của từng loại sự cố\n• Các yếu tố ảnh hưởng\n\n💡 GIẢI PHÁP KHẨN CẤP:\n• Biện pháp khắc phục nhanh (ví dụ: tắt cầu dao, khóa vòi nước)\n• Các bước xử lý tạm thời (cách dùng đồ đạc hay các dụng cụ có sẵn)\n\n📋 GIẢI PHÁP LÂU DÀI:\n• Kế hoạch bảo trì, sửa chữa\n• Biện pháp phòng ngừa\n\nVui lòng trình bày ngắn gọn, rõ ràng và có format dễ đọc.\nLưu ý: nếu người dùng viết các sự cố không rõ ràng, hay nhảm nhí (như: adsadada, asdasdasd, ...), hãy bỏ qua và không phân tích. Và kèm theo lời nhắc nhở yêu cầu người dùng viết rõ ràng hơn. viết lại cho rõ ràng nếu muốn AI hướng dẫn một cách ngắn gọn. Để dành thời gian cho các sự cố được viết rõ ràng hơn khác.`;
    

        geminiContent.innerHTML = '<div class="text-center text-info"><i class="fas fa-spinner fa-spin mr-2"></i>Đang phân tích, vui lòng chờ...</div>';

        // Dùng API của google ở đây 
        fetch('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=AIzaSyCNt-bKNCVX1eCjDsCkGO8gJ4gfZL0roIc', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                contents: [{ parts: [{ text: prompt }] }]
            })
        })
        .then(res => res.json())
        .then(data => {
            const rawText = data.candidates?.[0]?.content?.parts?.[0]?.text || 'Không nhận được hướng dẫn từ AI. Vui lòng thử lại sau.';
            const formatted = formatGeminiResponse(rawText); // Dùng hàm này để in ra phản hồi 
            geminiContent.innerHTML = formatted;
        })
        .catch(() => {
            geminiContent.innerHTML = '<div class="alert alert-danger text-center mt-3"><i class="fas fa-exclamation-circle mr-2"></i>Có lỗi khi gọi AI. Vui lòng thử lại.</div>';
        });
    });

    // --- Gợi ý dịch vụ (đưa vô word vì khoa có thể hỏi) ---
    goiYButton.addEventListener('click', function () {
        const selectedOption = daytroSelect.options[daytroSelect.selectedIndex];
        const tinh = selectedOption.getAttribute('data-tinh').toLowerCase().replace('tỉnh ', '');
        const huyen = selectedOption.getAttribute('data-huyen');
        const xa = selectedOption.getAttribute('data-xa');
        const loaiSuCo = loaiSuCoSelect.value;

        if (!loaiSuCo) {
            alert('Vui lòng chọn loại sự cố!');
            return;
        }

        let keywords = [];
        let icon = '';
        let type = ''; // Map4D types

        if (loaiSuCo === 'dien') {
            keywords = ['sửa điện', 'thợ điện', 'điện lạnh', 'điện gia dụng'];
            type = 'store'; // "store" thường phù hợp cho các cửa hàng, dịch vụ
            icon = '<i class="fas fa-bolt text-warning"></i>';
        } else if (loaiSuCo === 'nuoc') {
            keywords = ['sửa nước', 'thợ nước', 'ống nước', 'điện nước', 'plumber'];
            type = 'store';
            icon = '<i class="fas fa-tint text-primary"></i>';
        } else if (loaiSuCo === 'nha') {
            keywords = ['sửa nhà', 'xây dựng', 'thợ xây', 'thợ hồ', 'cải tạo nhà', 'thầu xây dựng'];
            type = ''; // Để trống để tìm kiếm rộng hơn hoặc dùng loại cụ thể khác
            icon = '<i class="fas fa-tools text-secondary"></i>';
        } else { // Trường hợp khác
            keywords = ['sửa chữa', 'dịch vụ sửa chữa', 'bảo trì', 'thợ sửa'];
            type = '';
            icon = '<i class="fas fa-wrench text-success"></i>';
        }

        ketquaGoiY.innerHTML = '<div class="text-center text-info mt-3"><i class="fas fa-spinner fa-spin mr-2"></i>Đang tìm kiếm dịch vụ...</div>';

        const nominatimUrl = `https://nominatim.openstreetmap.org/search?country=Vietnam&state=${encodeURIComponent(tinh)}&county=${encodeURIComponent(huyen)}&village=${encodeURIComponent(xa)}&format=json`;

        fetch(nominatimUrl)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok for Nominatim.');
                return response.json();
            })
            .then(data => {
                if (data.length > 0) {
                    const lat = data[0].lat;
                    const lon = data[0].lon;
                    const map4dKey = 'a7e16c35d6d991013a86600b26a4b99b'; // Map4D API Key của bạn
                    const radius = 20000; // 20 km

                    return Promise.all(keywords.map(kw => {
                        const map4dUrl = `https://api.map4d.vn/sdk/place/nearby-search?key=${map4dKey}` +
                                         `&location=${lat},${lon}` +
                                         `&radius=${radius}` +
                                         `&text=${encodeURIComponent(kw)}` +
                                         `&types=${type}`; // Sử dụng loại type đã định nghĩa
                        return fetch(map4dUrl).then(res => res.json());
                    }));
                } else {
                    throw new Error('Không tìm thấy tọa độ cho khu vực này. Vui lòng thử lại với thông tin địa chỉ rõ ràng hơn.');
                }
            })
            .then(results => {
                let allItems = [];
                results.forEach(r => {
                    if (r && r.result) allItems = allItems.concat(r.result);
                });

                const unique = [];
                const seen = new Set();
                allItems.forEach(item => {
                    const key = (item.name || '') + (item.address || '') + (item.map4dId || ''); // Sử dụng map4dId để đảm bảo unique
                    if (!seen.has(key)) {
                        unique.push(item);
                        seen.add(key);
                    }
                });

                // Lọc thêm theo tên/địa chỉ để tăng độ chính xác (ví dụ nếu Map4D trả về quá nhiều)
                let filtered = unique.filter(item => {
                    const addr = (item.address || '').toLowerCase();
                    // Đảm bảo chỉ lấy dịch vụ trong tỉnh đã chọn
                    return addr.includes(tinh);
                });

                // Lọc bổ sung để loại bỏ kết quả không liên quan rõ ràng (ví dụ: "nước uống" khi tìm "sửa nước")
                if (loaiSuCo === 'nuoc') {
                    filtered = filtered.filter(item => {
                        const name = (item.name || '').toLowerCase();
                        // Các từ khóa không liên quan đến sửa chữa nước
                        const excludeWords = ['nước uống', 'nước giải khát', 'nước đá', 'nước chấm', 'nước đóng chai', 'nước tinh khiết', 'nước suối', 
                                    'nước mắm', 'nước tương', 'nước hoa', 'nước giặt', 'nước rửa chén', 'nước rửa tay','nước ngoài','tinh khiết',
                                    'đóng chai','gửi hàng','giao hàng','kho bạc','bảo vệ','sản xuất'];
                        const isExcluded = excludeWords.some(word => name.includes(word));
                        return !isExcluded;
                    });
                } else if (loaiSuCo === 'dien') {
                    filtered = filtered.filter(item => {
                        const name = (item.name || '').toLowerCase();
                        const excludeWords = ['điện thoại', 'điện tử', 'điện máy xanh', 'thế giới di động', 'viettel', 'fpt','công nghiệp','dân dụng'];
                        const isExcluded = excludeWords.some(word => name.includes(word));
                        return !isExcluded;
                    });
                } else if (loaiSuCo === 'nha') {
                    filtered = filtered.filter(item => {
                        const name = (item.name || '').toLowerCase();
                        const excludeWords = ['nhà hàng', 'nhà nghỉ', 'nhà sách', 'nhà trẻ', 'nhà thờ', 'nhà thuốc', 'nhà ở xã hội', 'nhà đất', 
                                            'nhà cho thuê','nhà thờ','trà sữa','đồng hồ','may','shop','cafe','quán','bán','tiệm','nhà hàng','nhà làm'];
                        
                        const isExcluded = excludeWords.some(word => name.includes(word));
                        return !isExcluded;
                    });
                }

                let html = '';
                if (filtered.length > 0) {
                    html += `<h5 class="mt-4 mb-3 text-left">Dịch vụ ${loaiSuCoSelect.options[loaiSuCoSelect.selectedIndex].textContent}:</h5>`;
                    html += `<div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width: 5%;"></th>
                                            <th style="width: 35%;">Tên cửa hàng/Dịch vụ</th>
                                            <th style="width: 60%;">Địa chỉ</th>
                                        </tr>
                                    </thead>
                                    <tbody>`;
                    filtered.forEach(item => {
                        const googleMapLink = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(`${item.name || ''}, ${item.address || ''}`)}`;
                        html += `<tr>
                                    <td class="text-center align-middle">${icon}</td>
                                    <td class="align-middle"><strong>${item.name || 'Không rõ tên'}</strong></td>
                                    <td class="align-middle">
                                        ${item.address || 'Không rõ địa chỉ'}
                                        <br>
                                        <a href="${googleMapLink}" target="_blank" class="btn btn-sm btn-outline-info mt-1">
                                            <i class="fas fa-map-marker-alt"></i> Xem trên Google Map
                                        </a>
                                    </td>
                                </tr>`;
                    });
                    html += `       </tbody>
                                </table>
                            </div>`;
                } else {
                    html = '<div class="alert alert-warning mt-3"><i class="fas fa-info-circle mr-2"></i>Không tìm thấy dịch vụ phù hợp trong bán kính 20km quanh khu vực này. Vui lòng thử lại với loại sự cố khác hoặc mở rộng phạm vi tìm kiếm.</div>';
                }
                ketquaGoiY.innerHTML = html;
            })
            .catch(error => {
                console.error("Lỗi khi tìm kiếm dịch vụ:", error);
                ketquaGoiY.innerHTML = `<div class="alert alert-danger mt-3"><i class="fas fa-exclamation-circle mr-2"></i>Đã xảy ra lỗi: ${error.message}. Vui lòng thử lại sau.</div>`;
            });
    });

</script>
@endsection