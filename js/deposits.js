$(document).ready(function() {
    // Инициализация DataTables
    var table = $('#depositsDataTable').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: 'api/deposits.php',
            type: 'POST',
            data: function(d) {
                d.region = $('#regionFilter').val();
                d.status = $('#statusFilter').val();
            },
            error: function (xhr, error, thrown) {
                console.error('DataTables error:', error, thrown);
                alert('Произошла ошибка при загрузке данных. Пожалуйста, обновите страницу.');
            }
        },
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'region' },
            { data: 'waste_type' },
            { data: 'status' },
            { data: 'area' },
            { data: 'created_at' },
            {
                data: null,
                render: function(data, type, row) {
                    return `
                        <button class="btn-edit" data-id="${row.id}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-delete" data-id="${row.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                        <button class="btn-view" data-id="${row.id}">
                            <i class="fas fa-eye"></i>
                        </button>
                    `;
                }
            }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/ru.json'
        },
        dom: 'Bfrtip',
        buttons: [
            'copy', 'excel', 'pdf'
        ]
    });

    // Загрузка списка регионов
    $.ajax({
        url: 'api/regions.php',
        method: 'GET',
        success: function(data) {
            var select = $('#regionFilter');
            data.forEach(function(region) {
                select.append($('<option>', {
                    value: region.id,
                    text: region.name
                }));
            });
        },
        error: function(xhr, status, error) {
            console.error('Error loading regions:', error);
            alert('Ошибка при загрузке списка регионов');
        }
    });

    // Загрузка списка статусов
    $.ajax({
        url: 'api/statuses.php',
        method: 'GET',
        success: function(data) {
            var select = $('#statusFilter');
            data.forEach(function(status) {
                select.append($('<option>', {
                    value: status.id,
                    text: status.name
                }));
            });
        },
        error: function(xhr, status, error) {
            console.error('Error loading statuses:', error);
            alert('Ошибка при загрузке списка статусов');
        }
    });

    // Обработчики изменения фильтров
    $('#regionFilter, #statusFilter').change(function() {
        table.ajax.reload();
    });

    // Обработчики кнопок действий
    $('#depositsDataTable').on('click', '.btn-edit', function() {
        var id = $(this).data('id');
        // Логика редактирования
    });

    $('#depositsDataTable').on('click', '.btn-delete', function() {
        var id = $(this).data('id');
        if (confirm('Вы уверены, что хотите удалить это месторождение?')) {
            $.ajax({
                url: 'api/deposits.php?id=' + id,
                method: 'DELETE',
                success: function() {
                    table.ajax.reload();
                },
                error: function(xhr, status, error) {
                    console.error('Error deleting deposit:', error);
                    alert('Ошибка при удалении месторождения');
                }
            });
        }
    });

    $('#depositsDataTable').on('click', '.btn-view', function() {
        var id = $(this).data('id');
        // Логика просмотра
    });
});
