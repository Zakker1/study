document.addEventListener('DOMContentLoaded', function() {
    // Получаем все кнопки меню
    const navLinks = document.querySelectorAll('.nav-link');
    
    // Получаем все секции
    const sections = {
        navDeposits: document.getElementById('depositsSection'),
        navAdd: document.getElementById('addSection'),
        navReport: document.getElementById('reportSection'),
        navSettings: document.getElementById('settingsSection')
    };

    // Функция для скрытия всех секций
    function hideAllSections() {
        Object.values(sections).forEach(section => {
            if (section) {
                section.classList.add('hidden');
            }
        });
    }

    // Функция для удаления активного класса у всех кнопок
    function removeActiveClass() {
        navLinks.forEach(link => {
            link.classList.remove('active');
        });
    }

    // Обработчик клика по кнопкам меню
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Удаляем активный класс у всех кнопок
            removeActiveClass();
            
            // Добавляем активный класс текущей кнопке
            this.classList.add('active');
            
            // Скрываем все секции
            hideAllSections();
            
            // Показываем нужную секцию
            const sectionToShow = sections[this.id];
            if (sectionToShow) {
                sectionToShow.classList.remove('hidden');
                
                // Если открыта секция с таблицей месторождений, обновляем данные
                if (this.id === 'navDeposits' && window.depositsTable) {
                    window.depositsTable.ajax.reload();
                }
            }
        });
    });

    // По умолчанию показываем секцию месторождений
    document.getElementById('navDeposits').click();
});
