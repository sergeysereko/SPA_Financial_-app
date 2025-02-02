function loadOperations() {
    $.ajax({
        url: '/backend/api/get_data.php', // Путь к файлу
        method: 'GET',
        success: function(response) {
            console.log('Данные операций:', response);

            const operationsTable = $('#operationsTable tbody');
            operationsTable.empty();

            response.forEach(function(operation) {
                operationsTable.append(`
                    <tr>
                        <td>${operation.amount}</td>
                        <td>${operation.type === 'expense' ? 'Расход' : 'Приход'}</td>
                        <td>${operation.comment}</td>
                        <td>${operation.created_at}</td>
                        <td><button class="delete-btn" data-id="${operation.id}">Удалить</button></td>
                    </tr>
                `);
            });
        },
        error: function(xhr) {
            console.error('Ошибка при загрузке операций:', xhr.responseText);
            //alert('Не удалось загрузить операции.');
        }
    });
}

// $(document).ready(function() {
//     loadOperations();
// });

$(document).ready(function () {
    loadOperations();
    updateTotals();

    $('#addOperationForm').on('submit', function (e) {
        e.preventDefault();

        const formData = {
            amount: $('input[name="amount"]').val(),
            type: $('select[name="type"]').val(),
            comment: $('input[name="comment"]').val()
        };

        $.ajax({
            url: '/backend/api/add_operation.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            success: function () {

                updateTotals();
                loadOperations();
                //alert('Операция успешно добавлена!');

                // Очищаем поля формы
                $('input[name="amount"]').val('');
                $('input[name="comment"]').val('');
                $('select[name="type"]').prop('selectedIndex', 0);

                // Перезагружаем данные
                loadOperations();
            },
            error: function (xhr) {
                console.error('Ошибка запроса:', xhr.responseText);
                alert('Ошибка при добавлении операции.');
            }
        });
    });
});


$(document).on('click', '.delete-btn', function() {
    const operationId = $(this).data('id'); // Получаем ID операции

    if (confirm('Вы уверены, что хотите удалить эту операцию?')) {
        $.ajax({
            url: '/backend/api/delete_operation.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ id: operationId }),
            success: function(response) {
                //alert(response.success || 'Операция удалена.');
                updateTotals();
                loadOperations(); // Обновляем таблицу операций
            },
            error: function(xhr) {
                console.error('Ошибка при удалении операции:', xhr.responseText);
                alert(xhr.responseJSON?.error || 'Ошибка на сервере.');
            }
        });
    }
});


function updateTotals() {
    $.ajax({
        url: '/backend/api/get_totals.php',
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            if (response.error) {
                console.error(response.error);
                return;
            }

            const totalExpenses = response.totalExpenses || 0;
            const totalIncome = response.totalIncome || 0;
            const totalBalance = totalIncome - totalExpenses;

            // Обновление сумм на странице
            $('#totalExpenses').text(totalExpenses);
            $('#totalIncome').text(totalIncome);
            $('#totalBalance').text(totalBalance);
        },
        error: function (xhr, status, error) {
            console.error('Ошибка при обновлении сумм:', error);
        }
    });
}

// Обновление каждые 5 секунд
setInterval(updateTotals, 5000);