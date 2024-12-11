document.addEventListener('DOMContentLoaded', function() {
    // Загрузка списка регионов для фильтра отчетов
    fetch('/api/regions')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('reportRegion');
            data.forEach(region => {
                const option = new Option(region.name, region.id);
                select.add(option);
            });
        });

    // Обработка генерации PDF
    document.getElementById('generatePdfReport').addEventListener('click', function() {
        const filters = {
            region_id: document.getElementById('reportRegion').value,
            date_start: document.getElementById('reportDateStart').value,
            date_end: document.getElementById('reportDateEnd').value,
            report_type: document.getElementById('reportType').value
        };

        // Запрос на генерацию PDF
        fetch('/api/reports/generate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(filters)
        })
        .then(response => response.blob())
        .then(blob => {
            // Создаем ссылку для скачивания PDF
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `Отчет_${new Date().toLocaleDateString()}.pdf`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ошибка при генерации отчета');
        });
    });
});
