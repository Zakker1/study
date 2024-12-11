document.addEventListener('DOMContentLoaded', function() {
    // Загрузка списков для выпадающих меню
    function loadSelects() {
        // Загрузка регионов
        fetch('/api/regions')
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById('depositRegion');
                data.forEach(region => {
                    const option = new Option(region.name, region.id);
                    select.add(option);
                });
            });

        // Загрузка типов отходов
        fetch('/api/waste-types')
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById('depositWasteType');
                data.forEach(type => {
                    const option = new Option(type.name, type.id);
                    select.add(option);
                });
            });

        // Загрузка статусов
        fetch('/api/statuses')
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById('depositStatus');
                data.forEach(status => {
                    const option = new Option(status.name, status.id);
                    select.add(option);
                });
            });
    }

    // Обработка отправки формы
    const form = document.getElementById('addDepositForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        fetch('/api/deposits', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert('Месторождение успешно добавлено');
                form.reset();
                // Обновляем таблицу в разделе просмотра
                if (window.depositsTable) {
                    window.depositsTable.ajax.reload();
                }
            } else {
                alert('Ошибка при добавлении месторождения: ' + result.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Произошла ошибка при добавлении месторождения');
        });
    });

    // Загружаем списки при инициализации
    loadSelects();
});
