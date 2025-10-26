@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <h4 class="mb-3">Quản lý Chuẩn đầu ra chương trình (PLO & PI)</h4>

        <div id="ploContainer"></div>

        <button class="btn btn-primary mt-3" id="addPloBtn">+ Thêm PLO</button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // ======= DỮ LIỆU GIẢ BAN ĐẦU =======
            let plos = [{
                    id: 1,
                    code: 'PLO1',
                    description: 'Vận dụng kiến thức cơ bản về toán học, khoa học tự nhiên, xã hội và tư duy học thuật.',
                    pis: [{
                            id: 1,
                            code: 'PI1.1',
                            description: 'Áp dụng kiến thức toán học vào giải quyết vấn đề CNTT.'
                        },
                        {
                            id: 2,
                            code: 'PI1.2',
                            description: 'Vận dụng tư duy logic trong phân tích chuyên môn.'
                        }
                    ]
                },
                {
                    id: 2,
                    code: 'PLO2',
                    description: 'Vận dụng kiến thức cơ sở ngành để phát triển các giải pháp CNTT.',
                    pis: [{
                            id: 3,
                            code: 'PI2.1',
                            description: 'Áp dụng kiến thức cơ sở ngành để xây dựng hệ thống.'
                        },
                        {
                            id: 4,
                            code: 'PI2.2',
                            description: 'Sử dụng kỹ năng lập trình để phát triển giải pháp CNTT hiệu quả.'
                        }
                    ]
                }
            ];

            const container = document.getElementById('ploContainer');

            // ======= HÀM RENDER BẢNG =======
            function render() {
                container.innerHTML = '';
                plos.forEach((plo, i) => {
                    const div = document.createElement('div');
                    div.className = 'card mb-3 shadow-sm';
                    div.innerHTML = `
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div><strong>${plo.code}</strong> – ${plo.description}</div>
                    <div>
                        <button class="btn btn-sm btn-outline-secondary me-1" onclick="editPLO(${i})">Sửa</button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deletePLO(${i})">Xóa</button>
                    </div>
                </div>
                <div class="card-body">
                    <h6>Danh sách PI:</h6>
                    <ul class="list-group mb-2" id="pi-list-${i}">
                        ${plo.pis.map((pi, j) => `
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div><strong>${pi.code}</strong>: ${pi.description}</div>
                                    <div>
                                        <button class="btn btn-sm btn-outline-secondary me-1" onclick="editPI(${i}, ${j})">Sửa</button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deletePI(${i}, ${j})">Xóa</button>
                                    </div>
                                </li>`).join('')}
                    </ul>
                    <button class="btn btn-sm btn-outline-primary" onclick="addPI(${i})">+ Thêm PI</button>
                </div>
            `;
                    container.appendChild(div);
                });
            }

            // ======= CRUD HÀM XỬ LÝ =======
            window.addPLO = () => {
                const code = prompt('Mã PLO:');
                const desc = prompt('Mô tả PLO:');
                if (code && desc) plos.push({
                    id: Date.now(),
                    code,
                    description: desc,
                    pis: []
                });
                render();
            };
            window.editPLO = (i) => {
                const code = prompt('Sửa mã PLO:', plos[i].code);
                const desc = prompt('Sửa mô tả PLO:', plos[i].description);
                if (code) plos[i].code = code;
                if (desc) plos[i].description = desc;
                render();
            };
            window.deletePLO = (i) => {
                if (confirm('Xóa PLO này?')) plos.splice(i, 1);
                render();
            };

            window.addPI = (i) => {
                const code = prompt('Mã PI:');
                const desc = prompt('Mô tả PI:');
                if (code && desc) plos[i].pis.push({
                    id: Date.now(),
                    code,
                    description: desc
                });
                render();
            };
            window.editPI = (i, j) => {
                const code = prompt('Sửa mã PI:', plos[i].pis[j].code);
                const desc = prompt('Sửa mô tả PI:', plos[i].pis[j].description);
                if (code) plos[i].pis[j].code = code;
                if (desc) plos[i].pis[j].description = desc;
                render();
            };
            window.deletePI = (i, j) => {
                if (confirm('Xóa PI này?')) plos[i].pis.splice(j, 1);
                render();
            };

            document.getElementById('addPloBtn').addEventListener('click', addPLO);
            render();
        });
    </script>
@endsection
